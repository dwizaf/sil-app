import time
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={'width': 1280, 'height': 1200})
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Scroll down multiple times
    for _ in range(30):
        page.keyboard.press("PageDown")
        time.sleep(0.3)

    # Get all text content of elements
    texts = page.evaluate("""
        Array.from(document.querySelectorAll('*'))
            .map(el => el.textContent.trim())
            .filter(t => t.length > 0 && t.length < 100)
    """)

    # Find any numbers like "51.", "52.", "64."
    found_nums = set()
    for t in set(texts):
        if any(t.startswith(f"{i:02d}.") or t.startswith(f"{i}.") for i in range(51, 65)):
            found_nums.add(t)

    print("Found items 51-64 in DOM:", sorted(list(found_nums)))
    browser.close()
