import urllib.request
import re
import json

url = "https://drive.google.com/drive/folders/1-ONJ6xdVBPjYIJ9shFMySZ24cQQUgyCM"
req = urllib.request.Request(url, headers={
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
})

with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')

# Regex to extract folder ID and folder Name from Google Drive payload:
# [[null,"FOLDER_ID"],null,null,null,"application/vnd.google-apps.folder",...,[[["FOLDER_NAME",null,1]]]
# Or pattern: [[null,"(1[a-zA-Z0-9_-]{28,35})"],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"

pattern = r'\[\[null,"(1[a-zA-Z0-9_-]{25,35})"\],null,null,null,"application/vnd\.google-apps\.folder".*?\[\[\["([^"]+)"'
matches = re.findall(pattern, html, re.DOTALL)

# Alternative regex if order is reversed or slightly different
if not matches:
    pattern = r'\[\[\["([^"]+)",null,1\]\]\].*?\[\[null,"(1[a-zA-Z0-9_-]{25,35})"'
    raw_matches = re.findall(pattern, html, re.DOTALL)
    matches = [(fid, name) for name, fid in raw_matches]

print(f"Total subfolder OPD ditemukan: {len(matches)}\n")

folders_dict = {}
for fid, name in matches:
    # Clean up name if needed
    clean_name = name.strip()
    if clean_name and clean_name != "OPD":
        folders_dict[clean_name] = f"https://drive.google.com/drive/folders/{fid}"

print(f"Total unik OPD: {len(folders_dict)}\n")

output_list = []
for idx, (name, link) in enumerate(sorted(folders_dict.items()), 1):
    print(f"{idx:2d}. {name:<55} -> {link}")
    output_list.append({'opd_name': name, 'gdrive_link': link})

# Save to JSON
with open('opd_gdrive_folders.json', 'w', encoding='utf-8') as f:
    json.dump(output_list, f, indent=2, ensure_ascii=False)

print(f"\n✅ Berhasil menyimpan {len(output_list)} OPD ke opd_gdrive_folders.json")
