import urllib.request
import re

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"
req = urllib.request.Request(url, headers={
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
})

with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')

# Find all occurrences of "application/vnd.google-apps.folder"
matches = [m.start() for m in re.finditer(r'application/vnd\.google-apps\.folder', html)]
print(f"Total occurrences of folder mime-type: {len(matches)}")

for i, pos in enumerate(matches):
    chunk = html[max(0, pos-150):min(len(html), pos+150)]
    print(f"\n--- Chunk {i+1} ---")
    print(chunk)
