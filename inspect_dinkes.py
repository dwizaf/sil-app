import urllib.request
import re

dinkes_url = "https://drive.google.com/drive/folders/1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0"
req = urllib.request.Request(dinkes_url, headers={
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
})

with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')

matches = [m.start() for m in re.finditer(r'application/vnd\.google-apps\.folder', html)]
print(f"Total occurrences in Dinkes folder: {len(matches)}")

for i, pos in enumerate(matches):
    chunk = html[max(0, pos-150):min(len(html), pos+150)]
    print(f"\n--- Chunk {i+1} ---")
    print(chunk)
