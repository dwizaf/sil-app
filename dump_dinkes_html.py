import time
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={'width': 1280, 'height': 1200})
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Dump full HTML
    html = page.content()
    with open('dinkes_page.html', 'w', encoding='utf-8') as f:
        f.write(html)
    print("Saved html, size:", len(html))

    browser.close()
