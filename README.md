# Opis

Aplikacja PHP czytająca plik csv i generująca dashboard.
Zastosowano prosty MVC bez DI kontenera, gdzie za model służą DTO.

## Wymagania

- DDEV
- Docker

lub

- PHP 8.4
- Composer

## Uruchomienie aplikacji

Należy pobrać aplikację z gh.

```bash
git clone https://github.com/JSlomian/csv
cd csv
```

### Uruchomienie przez DDEV

```bash
ddev start
ddev composer dump-autoload
```

Aplikacja będzie dostępna przez

```text
https://csv.ddev.site
```

### Uruchomienie przez wbudowany serwer PHP

```bash
composer dump-autoload
php -S localhost:8000 -t public/
```

Aplikacja będzie dostępna przez

```bash
localhost:8000
```

## Dane wejściowe

Docelowy plik wejściowy umieść jako:

```text
data/sales.csv
```

CSV powinien zawierać kolumny:

```text
event_id,event_date,city,category,order_id,ticket_qty,status,utm_source,utm_campaign,utm_content,sold_out
```
