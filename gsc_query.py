import os
import json
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
import datetime

SCOPES = ['https://www.googleapis.com/auth/webmasters.readonly']
SITE_URL = 'https://suplementospanama.net'
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


def query_gsc(service, start_date, end_date, dimensions=None, row_limit=10):
    body = {
        'startDate': start_date,
        'endDate': end_date,
        'rowLimit': row_limit,
    }
    if dimensions:
        body['dimensions'] = dimensions
    return service.searchanalytics().query(siteUrl=SITE_URL, body=body).execute()


def main():
    service = get_service()
    end = datetime.date.today()
    start = end - datetime.timedelta(days=7)

    print(f"Consultando GSC: {start} a {end}")
    result = query_gsc(service, start.isoformat(), end.isoformat(),
                        dimensions=['query'], row_limit=10)

    rows = result.get('rows', [])
    if not rows:
        print("Sin datos en los últimos 7 días.")
        return

    print(f"{'Query':<30} {'Clicks':<8} {'Impresiones':<12} {'CTR':<8} {'Pos':<6}")
    print("-" * 64)
    for row in rows:
        query = row['keys'][0][:28]
        clicks = row['clicks']
        impressions = row['impressions']
        ctr = row['ctr']
        position = row['position']
        print(f"{query:<30} {clicks:<8} {impressions:<12} {ctr:<8.1%} {position:<6.1f}")

    print(f"\nTotal: {len(rows)} queries")


if __name__ == '__main__':
    main()
