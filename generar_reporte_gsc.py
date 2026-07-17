import os
from datetime import datetime
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from fpdf import FPDF

SCOPES = ['https://www.googleapis.com/auth/webmasters']
SITE_DOMAIN = 'sc-domain:suplementospanama.net'
base = os.path.dirname(os.path.abspath(__file__))
TOKEN_FILE = os.path.join(base, 'token.json')

creds = Credentials.from_authorized_user_file(TOKEN_FILE, SCOPES)
service = build('searchconsole', 'v1', credentials=creds)

def query_gsc(start, end, dim, filters=None, limit=15):
    body = {'startDate': start, 'endDate': end, 'dimensions': [dim], 'rowLimit': limit}
    if filters:
        body['dimensionFilterGroups'] = filters
    resp = service.searchanalytics().query(siteUrl=SITE_DOMAIN, body=body).execute()
    return resp.get('rows', [])

print("Consultando GSC...", flush=True)
top_queries = query_gsc('2026-04-16', '2026-07-14', 'query')
top_pages = query_gsc('2026-04-16', '2026-07-14', 'page')

cats = ['creatina', 'proteina', 'whey', 'suplementos', 'vitaminas', 'quemador', 'pre entreno']
cat_data = {}
for cat in cats:
    rows = query_gsc('2026-04-16', '2026-07-14', 'query', filters=[{
        'groupType': 'and',
        'filters': [{'dimension': 'query', 'operator': 'contains', 'expression': cat}]
    }], limit=50)
    cat_data[cat] = {
        'clics': sum(r['clicks'] for r in rows),
        'impr': sum(r['impressions'] for r in rows)
    }

months = []
for m in ['2026-05', '2026-06', '2026-07']:
    sd = f"{m}-01"
    ed = f"{m}-28" if m == '2026-07' else f"{m}-30"
    rows = query_gsc(sd, ed, 'query', limit=1000)
    months.append({'mes': m, 'clics': sum(r['clicks'] for r in rows), 'impr': sum(r['impressions'] for r in rows)})

print("Generando PDF...", flush=True)

class PDF(FPDF):
    def header(self):
        self.set_font('Helvetica', 'B', 18)
        self.set_text_color(192, 57, 43)
        self.cell(0, 10, 'Suplementos Panama', new_x='LMARGIN', new_y='NEXT')
        self.set_font('Helvetica', '', 9)
        self.set_text_color(100, 100, 100)
        self.cell(0, 5, 'Reporte Google Search Console | sc-domain:suplementospanama.net', new_x='LMARGIN', new_y='NEXT')
        self.cell(0, 5, 'Periodo: 16 Abr - 14 Jul 2026', new_x='LMARGIN', new_y='NEXT')
        self.line(10, self.get_y(), 200, self.get_y())
        self.ln(5)

    def footer(self):
        self.set_y(-15)
        self.set_font('Helvetica', '', 8)
        self.set_text_color(150, 150, 150)
        self.cell(0, 10, f'Generado {datetime.now().strftime("%d/%m/%Y %H:%M")} | Pag. {self.page_no()}', align='C')

    def section_title(self, title):
        self.set_font('Helvetica', 'B', 13)
        self.set_text_color(44, 62, 80)
        self.cell(0, 8, title, new_x='LMARGIN', new_y='NEXT')
        self.ln(2)

    def table(self, headers, rows, col_widths=None):
        if col_widths is None:
            col_widths = [190 / len(headers)] * len(headers)
        # Header
        self.set_font('Helvetica', 'B', 8)
        self.set_fill_color(192, 57, 43)
        self.set_text_color(255, 255, 255)
        for i, h in enumerate(headers):
            self.cell(col_widths[i], 6, h, border=1, fill=True, align='C' if i > 0 else 'L')
        self.ln()
        # Rows
        self.set_font('Helvetica', '', 8)
        self.set_text_color(51, 51, 51)
        fill = False
        for row in rows:
            if fill:
                self.set_fill_color(245, 245, 245)
            else:
                self.set_fill_color(255, 255, 255)
            for i, val in enumerate(row):
                align = 'R' if i > 0 else 'L'
                self.cell(col_widths[i], 6, str(val), border=1, fill=True, align=align)
            self.ln()
            fill = not fill
        self.ln(3)

pdf = PDF('P', 'mm', 'A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.add_page()

# Resumen
total_clics = sum(r['clicks'] for r in top_queries)
total_impr = sum(r['impressions'] for r in top_queries)
pdf.section_title('Resumen')
pdf.set_font('Helvetica', '', 20)
pdf.set_text_color(192, 57, 43)
x_start = pdf.get_x()
pdf.cell(63, 12, str(total_clics), align='C')
pdf.cell(63, 12, str(total_impr), align='C')
pdf.cell(63, 12, f'{total_clics/total_impr*100:.1f}%', align='C')
pdf.ln()
pdf.set_font('Helvetica', '', 8)
pdf.set_text_color(100, 100, 100)
pdf.cell(63, 5, 'Clics totales', align='C')
pdf.cell(63, 5, 'Impresiones', align='C')
pdf.cell(63, 5, 'CTR promedio', align='C')
pdf.ln(8)

# Top Queries
pdf.section_title('Top 15 Queries')
q_rows = []
for i, r in enumerate(top_queries[:15]):
    q_rows.append([str(i+1), r['keys'][0], str(r['clicks']), str(r['impressions']), f"{r.get('ctr',0)*100:.1f}%"])
pdf.table(['#', 'Query', 'Clics', 'Impr.', 'CTR'], q_rows, [8, 82, 20, 20, 20])

# Top Pages
pdf.section_title('Top 10 Paginas')
p_rows = []
for i, r in enumerate(top_pages[:10]):
    u = r['keys'][0].replace('https://suplementospanama.net/', '/')
    if len(u) > 60:
        u = u[:57] + '...'
    p_rows.append([str(i+1), u, str(r['clicks']), str(r['impressions'])])
pdf.table(['#', 'URL', 'Clics', 'Impr.'], p_rows, [8, 122, 20, 20])

# Categorias
pdf.section_title('Rendimiento por Categoria')
c_rows = []
for cat in cats:
    d = cat_data[cat]
    c_rows.append([cat, str(d['clics']), str(d['impr'])])
pdf.table(['Categoria', 'Clics', 'Impresiones'], c_rows, [60, 30, 30])

# Trend
pdf.section_title('Trend Mensual')
m_rows = []
for m in months:
    m_rows.append([m['mes'], str(m['clics']), str(m['impr'])])
pdf.table(['Mes', 'Clics', 'Impresiones'], m_rows, [40, 30, 30])

# Guardar
out_dir = os.path.join(base, 'suplementos')
os.makedirs(out_dir, exist_ok=True)
pdf_path = os.path.join(out_dir, 'reporte_gsc.pdf')
pdf.output(pdf_path)
print(f"PDF generado: {pdf_path}")
