# ScriptDeploy Marketplace (PHP + MySQL)

Updated system includes:

- Single landing page (`/`) with premium design sections (features/about/contact)
- Authentication with role-based redirects
- Login with **Email or Username + Password** (separate Buyer/Admin and Developer auth pages)
- Register fields: Username, Email, Password, Support Number (separate portals are available).
- Buyer panel: dashboard, shop filters, buy flow, my projects, invoices, tickets
- Developer panel: dashboard, brands, my projects upload, clients, warns, account, tickets
- Brand storefront system: developer creates brand+subdomain, projects appear in brand store
- Admin panel: dashboard, users, categories, warns, ticket replies

## UX / SEO Improvements

- Responsive layout for mobile/tablet/desktop
- Better visual hierarchy and professional card-based UI
- SEO-friendly metadata on major pages (`title`, `description`, OG tags)
- User-friendly flows and clear menu navigation for both roles

## Auth Redirect Rules

- Buyer login success → `/user/dashboard`
- Developer login success → `/dev/dashboard`
- Admin login success → `/admin/dashboard` (via `/admin/login`)

Admin login is separate and protected by IP whitelist (`admin_ip_whitelist` table).

## URL Structure

- `/login`
- `/register` (Buyer)
- `/dev/login` (Developer)
- `/dev/register` (Developer)
- `/user/dashboard`
- `/dev/dashboard`
- `/admin/login`
- `/admin/dashboard`
- `/admin/settings`
- `/shop`
- `/store/{subdomain}`
- `/dev/brands`
- `/dev/wallet`
- `/dev/wallet/transection`
- `/user/my-projects`
- `/user/invoice`
- `/user/wallet`
- `/user/wallet/transection`
- `/ticket`

## Setup

### Local

```bash
mysql -u root -p < db.sql
php -S 0.0.0.0:8000
```

### cPanel / phpMyAdmin

1. Create a database from cPanel.
2. Open phpMyAdmin and select that database.
3. Import `db.sql` directly (this file is prepared to run without `CREATE DATABASE` / `USE`).

Then open `http://localhost:8000`.


### cPanel Routing Note

- This project includes a root `.htaccess` so clean URLs like `/login`, `/dev/dashboard`, `/admin/users` work on Apache/cPanel.
- Make sure `mod_rewrite` is enabled (default on most cPanel hosts).


## Create First Admin

After importing DB, create an admin user from MySQL (replace hash with your own bcrypt hash):

```sql
INSERT INTO users (username,email,password,support_number,role,status)
VALUES ('admin','admin@example.com','$2y$10$replace_with_bcrypt_hash','8801xxxxxxx','admin','active');
```

## Security + Business Logic Added

- Session based login and role middleware
- Password hashing
- Prepared SQL statements
- Warn model and ban-compatible status field
- Invoice renew flow with 15-day late handling (6% fee)
- Subscription duration-based pricing (month-based)
- Buyer wallet + Developer wallet support (both roles)

> Note: Auto deployment is simulated in success flow UI with progress animation and generated site/admin credentials.


## Auto build + delivery flow

1. Developer creates brand and subdomain (`/dev/brands`).
2. Developer uploads project under that brand (`/dev/my-projects`).
3. Brand storefront shows projects at `/store/{subdomain}`.
4. Buyer purchases from storefront and system auto-creates order with delivered build details.


## Developer Package System

Developers must activate a package from `/dev/package` before using full platform features.

- **Starter (৳999/mo):** 1 brand, 5 scripts, 20 installs/month, subdomain only
- **Pro (৳2999/mo):** 3 brands, 20 scripts, 200 installs/month, subdomain only
- **Enterprise (৳7999/mo):** unlimited brands/scripts/installs, custom domain allowed

Enforcements added:
- Buyer payment uses buyer wallet balance during purchase flow.
- Brand creation limit by package
- Script upload limit by package
- Purchase/install monthly limit by developer package
- Custom domain purchase allowed only for Enterprise


## Admin Security & User Provisioning

- Admin login page is separate: `/admin/login`
- Admin access is restricted to whitelisted IPs from `admin_ip_whitelist`
- Admin can create buyer/developer/admin users from `/admin/users`
- Admin can manage whitelist IP entries from `/admin/users`

- Admin can fully control landing website content from `/admin/settings`.
