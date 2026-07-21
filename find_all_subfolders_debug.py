import time
import json
import re
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"

print("Scraping all items in master Google Drive folder...")

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={'width': 1280, 'height': 2000})
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Scroll down multiple times to force GDrive to render all items
    for _ in range(40):
        page.keyboard.press("PageDown")
        time.sleep(0.3)

    html = page.content()
    browser.close()

# Extract all pairs of folder ID and folder Name
# Pattern for Google Drive items:
# [[null,"FOLDER_ID"],null,"FOLDER_NAME",...
# Or pattern: ["FOLDER_ID",null,"FOLDER_NAME","application/vnd.google-apps.folder"
matches = re.findall(r'\["([a-zA-Z0-9_-]{25,38})",\s*null,\s*"([^"]+)",\s*"application/vnd\.google-apps\.folder"', html)

if not matches:
    matches = re.findall(r'\[\[null,"([a-zA-Z0-9_-]{25,38})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"', html, re.DOTALL)

folders = {}
for fid, name in matches:
    if fid != "1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM":
        folders[name.strip()] = f"https://drive.google.com/drive/folders/{fid}"

print(f"\nTotal subfolder ditemukan: {len(folders)}")

for idx, (name, link) in enumerate(sorted(folders.items()), 1):
    print(f"{idx:2d}. {name:<60} -> {link}")

with open('all_64_scraped_opds.json', 'w', encoding='utf-8') as f:
    json.dump([{'opd_name': k, 'gdrive_link': v} for k, v in sorted(folders.items())], f, indent=2, ensure_ascii=False)

print(f"\nDisimpan ke all_64_scraped_opds.json")
