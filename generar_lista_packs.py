from fpdf import FPDF
from datetime import datetime

pdf = FPDF('P', 'mm', 'A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.add_page()

pdf.set_font('Helvetica', 'B', 18)
pdf.set_text_color(192, 57, 43)
pdf.cell(0, 10, 'Suplementos Panama', new_x='LMARGIN', new_y='NEXT')
pdf.set_font('Helvetica', '', 10)
pdf.set_text_color(100, 100, 100)
pdf.cell(0, 6, 'Productos con precio excluido de actualizacion automatica (Packs)', new_x='LMARGIN', new_y='NEXT')
pdf.cell(0, 5, f'Generado: {datetime.now().strftime("%d/%m/%Y %H:%M")}', new_x='LMARGIN', new_y='NEXT')
pdf.line(10, pdf.get_y()+2, 200, pdf.get_y()+2)
pdf.ln(8)

products = [
    ("RTD Carnivor 500ml", [
        "Proteina RTD Carnivor 500 ml - Proteina Lista para Beber Recuperacion y Crecimiento Muscular",
    ]),
    ("Batido Proteinas 325ml 12 Pack", [
        "Batido de Proteinas - 325 ml - 12 Pack - Recuperacion Muscular",
    ]),
    ("ON Amino Energy Pack 12", [
        "Bebida Energetica - Optimum Nutrition - Pack de 12 - Energia y Recuperacion",
    ]),
    ("C4 Ultimate Pack 12", [
        "Bebida Energetica C4 Ultimate Energy - 16 oz - Pack de 12 - Energia, Enfoque y Rendimiento Fisico",
    ]),
    ("C4 Performance Pack 12", [
        "Bebida Energetica C4 Performance Energy - 16 oz - Pack de 12 - Energia, Enfoque y Rendimiento Diario",
    ]),
]

for group_name, items in products:
    pdf.set_font('Helvetica', 'B', 12)
    pdf.set_text_color(44, 62, 80)
    pdf.set_fill_color(245, 245, 245)
    pdf.cell(0, 8, group_name, new_x='LMARGIN', new_y='NEXT', fill=True)
    pdf.ln(2)
    for item in items:
        pdf.set_font('Helvetica', '', 10)
        pdf.set_text_color(51, 51, 51)
        x = pdf.get_x()
        pdf.cell(8, 6, '-', align='C')
        pdf.multi_cell(0, 6, item)
        pdf.ln(1)
    pdf.ln(3)

pdf.ln(5)
pdf.set_font('Helvetica', 'I', 8)
pdf.set_text_color(150, 150, 150)
pdf.multi_cell(0, 5, 'Nota: Estos productos tienen un precio de pack diferente al precio unitario en PSK Cloud. '
              'La actualizacion automatica de precios via --update-prices esta desactivada para estos SKU mediante PRICE_EXCLUDE_SKUS en daily_stock_update.py.')

pdf.output('C:/suplementos/stock-suplementos/suplementos/productos_pack_excluidos.pdf')
print("PDF generado: productos_pack_excluidos.pdf")
