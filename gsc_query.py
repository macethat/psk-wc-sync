import os
import sys
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
import datetime

SCOPES = ['https://www.googleapis.com/auth/webmasters']
SITE_URL = 'https://suplementospanama.net'
SITE_DOMAIN = 'sc-domain:suplementospanama.net'
CREDENTIALS_FILE = os.path.join(os.path.dirname(__file__), 'credentials.json')
TOKEN_FILE = os.path.join(os.path.dirname(__file__), 'token.json')


def get_service():
    creds = None
    if os.path.exists(TOKEN_FILE):
        creds = Credentials.from_authorized_user_file(TOKEN_FILE, SCOPES)
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(CREDENTIALS_FILE, SCOPES)
            creds = flow.run_local_server(port=0)
        with open(TOKEN_FILE, 'w') as token:
            token.write(creds.to_json())
        print("Autorización completada. Token guardado.")
    return build('searchconsole', 'v1', credentials=creds)


def sitemap_status(service):
    response = service.sitemaps().list(siteUrl=SITE_URL).execute()
    sitemaps = response.get('sitemap', [])

    if not sitemaps:
        print("No hay sitemaps registrados.")
        return

    print(f"{'Sitemap':<50} {'Tipo':<12} {'Enviadas':<10} {'Indexadas':<10} {'Errores':<8}")
    print("-" * 90)
    total_submitted = 0
    total_indexed = 0
    total_errors = 0
    for s in sitemaps:
        path = s['path'][:48]
        stype = s.get('type', '')
        errors = int(s.get('errors', 0))
        warnings = int(s.get('warnings', 0))
        for c in s.get('contents', []):
            submitted = int(c.get('submitted', 0))
            indexed = int(c.get('indexed', 0))
            total_submitted += submitted
            total_indexed += indexed
            total_errors += errors
            status = "Pendiente" if s.get('isPending') else "OK"
            print(f"{path:<50} {stype:<12} {submitted:<10} {indexed:<10} {errors:<8}")

    print("-" * 90)
    print(f"{'TOTALES':<50} {'':<12} {total_submitted:<10} {total_indexed:<10} {total_errors:<8}")
    pct = (total_indexed / total_submitted * 100) if total_submitted else 0
    print(f"\n{total_indexed} de {total_submitted} URLs indexadas ({pct:.1f}%)")


def inspect_url(service, url):
    body = {
        'inspectionUrl': url,
        'siteUrl': SITE_DOMAIN,
    }
    response = service.urlInspection().index().inspect(body=body).execute()
    result = response.get('inspectionResult', {})
    index_status = result.get('indexStatusResult', {})
    verdict = index_status.get('verdict', 'N/A')
    coverage = index_status.get('coverageState', 'N/A')
    print(f"\nURL: {url}")
    print(f"Veredicto: {verdict}")
    print(f"Estado de cobertura: {coverage}")
    print(f"Robots.txt: {'Permitido' if index_status.get('robotsTxtState') == 'ALLOWED' else index_status.get('robotsTxtState', 'N/A')}")
    print(f"Canonical: {index_status.get('canonical', 'N/A')}")
    sitemap_refs = result.get('sitemap', [])
    if sitemap_refs:
        print(f"Sitemaps referidos: {', '.join(s['path'] for s in sitemap_refs)}")
    verdicts = {
        'PASS': 'Indexado correctamente',
        'PARTIAL': 'Indexado parcialmente',
        'FAIL': 'No indexado (error)',
        'NEUTRAL': 'No indexado (no descubierto)',
    }
    print(f" -> {verdicts.get(verdict, verdict)}")
    return verdict


def inspect_product_urls(service, product_paths):
    for path in product_paths:
        url = f'https://suplementospanama.net/{path}'
        inspect_url(service, url)
        print()


def query_analytics(service, start_date, end_date, dimensions=None, row_limit=10):
    body = {
        'startDate': start_date,
        'endDate': end_date,
        'rowLimit': row_limit,
    }
    if dimensions:
        body['dimensions'] = dimensions
    return service.searchanalytics().query(siteUrl=SITE_URL, body=body).execute()


def show_analytics(service, days=7):
    end = datetime.date.today()
    start = end - datetime.timedelta(days=days)
    print(f"\nConsultando rendimiento: {start} a {end}")
    result = query_analytics(service, start.isoformat(), end.isoformat(),
                              dimensions=['query'], row_limit=15)
    rows = result.get('rows', [])
    if not rows:
        print("Sin datos en el período.")
        return
    print(f"{'Query':<35} {'Clicks':<8} {'Impresiones':<12} {'CTR':<8} {'Pos':<6}")
    print("-" * 69)
    total_clicks = 0
    total_imp = 0
    for row in rows:
        q = row['keys'][0][:33]
        clicks = row['clicks']
        imp = row['impressions']
        ctr = row['ctr']
        pos = row['position']
        total_clicks += clicks
        total_imp += imp
        print(f"{q:<35} {clicks:<8} {imp:<12} {ctr:<8.1%} {pos:<6.1f}")
    print("-" * 69)
    print(f"{'TOTAL':<35} {total_clicks:<8} {total_imp:<12}")


def menu():
    service = get_service()
    while True:
        print("\n" + "=" * 50)
        print("GOOGLE SEARCH CONSOLE — suplementospanama.net")
        print("=" * 50)
        print("1. Ver rendimiento (últimos 7 días)")
        print("2. Estado de sitemaps (indexación general)")
        print("3. Inspeccionar una URL")
        print("4. Inspeccionar URLs de combos nuevos")
        print("5. Enviar sitemap")
        print("0. Salir")
        opt = input("\nOpción: ").strip()

        if opt == '1':
            show_analytics(service)
        elif opt == '2':
            sitemap_status(service)
        elif opt == '3':
            url = input("URL completa (https://suplementospanama.net/...): ").strip()
            if url:
                inspect_url(service, url)
        elif opt == '4':
            combos = [
                "producto/combo-1",
                "producto/combo-2",
            ]
            print("Revisando URLs de combos...")
            inspect_product_urls(service, combos)
        elif opt == '5':
            sitemap_path = input("Ruta del sitemap (ej: product-sitemap.xml): ").strip()
            if sitemap_path:
                service.sitemaps().submit(siteUrl=SITE_URL, feedpath=sitemap_path).execute()
                print(f"Sitemap {sitemap_path} enviado a Google.")
        elif opt == '0':
            break
        else:
            print("Opción inválida.")

        input("\nPresiona Enter para continuar...")


if __name__ == '__main__':
    if len(sys.argv) > 1:
        mode = sys.argv[1]
        service = get_service()
        if mode == 'sitemaps':
            sitemap_status(service)
        elif mode == 'analytics':
            days = int(sys.argv[2]) if len(sys.argv) > 2 else 7
            show_analytics(service, days)
        elif mode == 'inspect':
            if len(sys.argv) > 2:
                inspect_url(service, sys.argv[2])
            else:
                print("Uso: python gsc_query.py inspect <url>")
    else:
        menu()
