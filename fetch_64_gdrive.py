import time
import json
import re
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"

print("Fetching all OPD subfolders using Playwright scroll interaction...")

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context(viewport={'width': 1280, 'height': 1000})
    page = context.new_page()
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Focus on list and press Page Down / End repeatedly
    for _ in range(25):
        page.keyboard.press("PageDown")
        time.sleep(0.5)
        page.keyboard.press("End")
        time.sleep(0.5)

    # Scroll all elements that have scrollHeight > clientHeight
    page.evaluate("""
        const elements = document.querySelectorAll('*');
        elements.forEach(el => {
            if (el.scrollHeight > el.clientHeight) {
                el.scrollTop = el.scrollHeight;
            }
        });
    """)
    time.sleep(3)

    html = page.content()
    browser.close()

# Extract patterns
pattern = r'\[\[null,"(1[a-zA-Z0-9_-]{25,35})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"'
matches = re.findall(pattern, html, re.DOTALL)

if not matches:
    pattern = r'\[\[\["([^"]+)",null,1\]\]\].*?\[\[null,"(1[a-zA-Z0-9_-]{25,35})"'
    raw_matches = re.findall(pattern, html, re.DOTALL)
    matches = [(fid, name) for name, fid in raw_matches]

folders_dict = {}
for item in matches:
    if len(item) == 2:
        fid, name = item[0], item[1]
        if fid.startswith('1') and len(fid) > 25:
            clean_name = name.strip()
            if clean_name and clean_name != "OPD":
                folders_dict[clean_name] = f"https://drive.google.com/drive/folders/{fid}"

print(f"Total OPD unik ditemukan: {len(folders_dict)}\n")

output_list = []
for idx, (name, link) in enumerate(sorted(folders_dict.items()), 1):
    print(f"{idx:2d}. {name:<60} -> {link}")
    output_list.append({'opd_name': name, 'gdrive_link': link})

with open('opd_gdrive_folders_64.json', 'w', encoding='utf-8') as f:
    json.dump(output_list, f, indent=2, ensure_ascii=False)

print(f"\nDisimpan {len(output_list)} OPD ke opd_gdrive_folders_64.json")
