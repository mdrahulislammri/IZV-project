# ScriptDeploy Marketplace (PHP + MySQL)

Updated system includes:

- Single landing page (`/`) with premium design sections (features/about/contact)
- Authentication with role-based redirects
- Login with **Email or Username + Password**
- Register fields: Username, Email, Password, Support Number, Role
- Buyer panel: dashboard, shop filters, buy flow, my projects, invoices, tickets
- Developer panel: dashboard, my projects upload, clients, warns, account, tickets
- Admin panel: dashboard, users, categories, warns, ticket replies

## UX / SEO Improvements

- Responsive layout for mobile/tablet/desktop
- Better visual hierarchy and professional card-based UI
- SEO-friendly metadata on major pages (`title`, `description`, OG tags)
- User-friendly flows and clear menu navigation for both roles

## Auth Redirect Rules

- Buyer login success → `/user/dashboard`
- Developer login success → `/dev/dashboard`

## URL Structure

- `/login`
- `/register`
- `/user/dashboard`
- `/dev/dashboard`
- `/admin/dashboard`
- `/shop`
- `/user/my-projects`
- `/user/invoice`
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

> Note: Auto deployment is simulated in success flow UI with progress animation and generated site/admin credentials.
