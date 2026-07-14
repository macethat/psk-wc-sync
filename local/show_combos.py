"""Muestra datos de combos desde temp_combos.json"""
import json

with open('temp_combos.json', encoding='utf-8-sig') as f:
    data = json.load(f)

lines = []
for c in data:
    cur_cats = ", ".join(c['current_category_names'])
    lines.append(f"ID {c['id']}: {c['name']}")
    lines.append(f"  Slug: {c['slug']}")
    lines.append(f"  Categorias actuales: {cur_cats}")
    lines.append(f"  Hijos: {len(c['children'])}")

    child_cats = set()
    for ch in c['children']:
        for cat in ch['category_names']:
            child_cats.add(cat)

    sorted_cats = ", ".join(sorted(child_cats))
    lines.append(f"  Categorias de hijos: {sorted_cats}")

    new_cats = child_cats - set(c['current_category_names'])
    if new_cats:
        lines.append(f"  >> CATEGORIAS A AGREGAR: {', '.join(sorted(new_cats))}")
    else:
        lines.append(f"  >> No hay categorias nuevas que agregar")
    lines.append("")

with open('combos_report.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(lines))

print(f"Reporte guardado en combos_report.txt ({len(lines)} lineas)")
