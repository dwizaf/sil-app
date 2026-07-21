import time
import json
import re
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.goto(url, wait_until="networkidle")
    
    # Scroll down multiple times to load all items
    for i in range(10):
        page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(1)
        # also scroll any scrollable div inside drive
        page.evaluate("""
            const scrollables = document.querySelectorAll('div');
            scrollables.forEach(el => {
                if (el.scrollHeight > el.clientHeight) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        """)
        time.sleep(1)

    html = page.content()
    browser.close()

# Pattern 1: HTML elements or JSON data in DOM
matches = re.findall(r'\[\[null,"(1[a-zA-Z0-9_-]{25,35})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"', html, re.DOTALL)
if not matches:
    matches = re.findall(r'\[\[\["([^"]+)",null,1\]\]\].*?\[\[null,"(1[a-zA-Z0-9_-]{25,35})"', html, re.DOTALL)
    matches = [(fid, name) for name, fid in matches]

folders_dict = {}
for item in matches:
    if len(item) == 2:
        fid, name = item[0], item[1]
        if fid.startswith('1') and len(fid) > 25:
            clean_name = name.strip()
            if clean_name and clean_name != "OPD":
                folders_dict[clean_name] = f"https://drive.google.com/drive/folders/{fid}"

# Also parse DOM data-id / aria-label if present
# e.g., elements with data-id or data-target-id
print(f"Total OPD unik ditemukan: {len(folders_dict)}")

output_list = []
for idx, (name, link) in enumerate(sorted(folders_dict.items()), 1):
    print(f"{idx:2d}. {name:<60} -> {link}")
    output_list.append({'opd_name': name, 'gdrive_link': link})

# Save to JSON
with open('opd_gdrive_folders_all.json', 'w', encoding='utf-8') as f:
    json.dump(output_list, f, indent=2, ensure_ascii=False)

print(f"\nDisimpan {len(output_list)} OPD ke opd_gdrive_folders_all.json")
