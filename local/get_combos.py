"""Lee temp_export.json y muestra los productos grouped."""
import json

with open('temp_export.json', encoding='utf-8') as f:
    data = json.load(f)

groups = [p for p in data if p.get('type') == 'grouped']
for p in groups:
    slug = p.get('slug', '')
    name = p.get('name', '')
    pid = p.get('id', '')
    print(f"{slug} - {name} (ID {pid})")

print(f"\nTotal grouped: {len(groups)}")
