import urllib.request
import re
import json

# URL of 13. Dinas Kesehatan folder:
dinkes_url = "https://drive.google.com/drive/folders/1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0"
req = urllib.request.Request(dinkes_url, headers={
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
})

with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')

# Find all subfolder matches inside Dinas Kesehatan:
pattern = r'\[\[null,"(1[a-zA-Z0-9_-]{25,35})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"'
matches = re.findall(pattern, html, re.DOTALL)

if not matches:
    pattern = r'\[\[\["([^"]+)",null,1\]\]\].*?\[\[null,"(1[a-zA-Z0-9_-]{25,35})"'
    raw_matches = re.findall(pattern, html, re.DOTALL)
    matches = [(fid, name) for name, fid in raw_matches]

print(f"Total subfolder RSUD di dalam Dinas Kesehatan: {len(matches)}\n")

rsud_dict = {}
for item in matches:
    if len(item) == 2:
        fid, name = item[0], item[1]
        if fid != "1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0" and len(fid) > 25:
            clean_name = name.strip()
            rsud_dict[clean_name] = f"https://drive.google.com/drive/folders/{fid}"

for idx, (name, link) in enumerate(sorted(rsud_dict.items()), 1):
    print(f"{idx:2d}. {name:<60} -> {link}")

with open('rsud_subfolders.json', 'w', encoding='utf-8') as f:
    json.dump(rsud_dict, f, indent=2, ensure_ascii=False)
