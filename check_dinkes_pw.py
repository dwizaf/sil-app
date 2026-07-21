import time
from playwright.sync_api import sync_playwright

url = "https://drive.google.com/drive/folders/1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.goto(url, wait_until="networkidle")
    time.sleep(3)

    # Print page title and text nodes
    print("Page Title:", page.title())

    # Get text of all items
    elements = page.query_selector_all("[data-id], [aria-label]")
    for el in elements:
        label = el.get_attribute("aria-label") or el.get_attribute("data-id")
        print("Element:", label)

    browser.close()
