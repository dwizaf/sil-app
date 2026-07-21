import time
import json
import re
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={'width': 1280, 'height': 2000})
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Scroll down to bottom repeatedly
    for i in range(50):
        page.keyboard.press("PageDown")
        time.sleep(0.2)

    html = page.content()
    browser.close()

# Save raw HTML to file so we can analyze all folder data directly
with open('gdrive_raw_dom.html', 'w', encoding='utf-8') as f:
    f.write(html)

print("Saved raw DOM HTML. Searching for RS / RSUD / numbers in HTML...")

# Find all matches of format: [ "Name", "ID" ] or similar
all_folders = re.findall(r'\[\[null,"(1[a-zA-Z0-9_-]{25,38})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"', html, re.DOTALL)

print(f"Total folder matches found: {len(all_folders)}")
for fid, name in all_folders:
    if "RS" in name or "Rumah Sakit" in name or "Soetomo" in name or "Saiful" in name or "Haji" in name or "Menur" in name or any(name.startswith(f"{i}.") for i in range(51, 65)):
        print(f"FOUND RSUD: {name} -> https://drive.google.com/drive/folders/{fid}")
