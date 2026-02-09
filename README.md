# DevScript Market (PHP + MySQL)

A lightweight marketplace MVP based on your requested flow:

- Landing page
- Login / Register
- Developer dashboard
- Developer uploads script + image
- Developer creates brand with subdomain
- Buyers open brand storefront and purchase script
- Payment simulation triggers automatic delivery record

## Tech Stack

- PHP 8+
- MySQL
- HTML + Tailwind CSS (CDN)
- Vanilla JS (minimal; mostly server-rendered PHP)

## Setup

1. Create database schema:

```bash
mysql -u root -p < db.sql
```

2. Update database credentials in `config.php`.

3. Start local PHP server from project root:

```bash
php -S 0.0.0.0:8000
```

4. Open:

- `http://localhost:8000` landing page
- `http://localhost:8000/register.php` create buyer/developer
- `http://localhost:8000/developer/dashboard.php` developer features

## Core Pages

- `/index.php` — landing page + featured script list
- `/login.php` — login
- `/register.php` — register (buyer/developer)
- `/developer/dashboard.php` — create brand, upload scripts
- `/brand.php?subdomain=xyz` — storefront by subdomain key
- `/purchase.php?script_id=1` — payment simulation + auto delivery
- `/user/deliveries.php` — delivered website packages

## Notes

- Subdomain routing is represented as a stored subdomain field and query access (`brand.php?subdomain=...`).
- Payment integration is mocked for MVP; plug real gateways later.
- Uploaded files are saved in `/uploads`.
