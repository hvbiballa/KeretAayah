# KeretaAyah cPanel Deployment Guide

This guide prepares the KeretaAyah Laravel, Inertia, Vue, and MySQL application for shared hosting under:

https://keretaayah.myuni.agency

Do not commit real secrets. Store production credentials only in the production `.env` file on the server.

## 1. Local Preparation

Run these commands locally before uploading files:

```bash
composer install
npm install
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Confirm these production values will be used on the server:

```env
APP_NAME=KeretaAyah
APP_ENV=production
APP_DEBUG=false
APP_URL=https://keretaayah.myuni.agency
APP_TIMEZONE=Asia/Kuala_Lumpur
```

Do not upload local development uploads by default:

- `storage/app/private`
- `storage/app/public/uploads`

Only migrate those folders if you are intentionally moving live uploaded customer documents, payment proofs, or car images.

## 2. Recommended cPanel File Structure

Preferred structure, if your host allows it:

```text
/home/account/keretaayah/        Laravel project files
/home/account/public_html/       Contents of Laravel public/ folder
```

The domain document root should point to Laravel's `public` folder. This keeps `.env`, `storage`, `vendor`, and application code outside the public web root.

If cPanel forces the domain to use `public_html`, place the Laravel project outside `public_html` and copy only the `public` folder contents into `public_html`. Then update `public_html/index.php` paths so they point to the actual project location.

Avoid placing the full Laravel project inside `public_html` unless the host gives no alternative. If that is unavoidable, protect sensitive files and folders with `.htaccess` rules and confirm `.env` cannot be downloaded.

## 3. Required Server Settings

Use:

- PHP 8.2 or newer
- MySQL or MariaDB
- Composer 2
- Node.js only if building assets on the server

Required PHP extensions commonly needed by Laravel:

- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- Filter
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO MySQL
- Session
- Tokenizer
- XML

Writable paths:

```text
storage/
bootstrap/cache/
```

Typical permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

On shared hosting, adjust permissions only as needed by the hosting provider. Avoid broad `777` permissions unless the host specifically requires it.

## 4. Database Setup

In cPanel:

1. Create a MySQL database.
2. Create a MySQL user.
3. Assign the user to the database with required privileges.
4. Update production `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_cpanel_database
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password
```

Run:

```bash
php artisan migrate --force
```

Seed admin and staff accounts only when needed:

```bash
php artisan db:seed --force
```

The seeder creates these accounts if missing:

- `admin@keretaayah.myuni.agency`
- `staff@keretaayah.myuni.agency`

If `SEEDED_ADMIN_PASSWORD` and `SEEDED_STAFF_PASSWORD` are set in `.env`, those values are used for new accounts. If they are blank, secure random passwords are generated and printed once in the console. Save them securely and change them after first login.

Existing admin/staff passwords are not overwritten by the seeder.

## 5. Email Setup

Use the production mail server values:

```env
MAIL_MAILER=smtp
MAIL_HOST=server403.web-hosting.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl

MAIL_AUTH_USERNAME=auth@keretaayah.myuni.agency
MAIL_AUTH_PASSWORD=

MAIL_NOREPLY_USERNAME=no-reply@keretaayah.myuni.agency
MAIL_NOREPLY_PASSWORD=

MAIL_FROM_ADDRESS=no-reply@keretaayah.myuni.agency
MAIL_FROM_NAME="${APP_NAME}"

AUTH_MAIL_FROM_ADDRESS=auth@keretaayah.myuni.agency
NOREPLY_MAIL_FROM_ADDRESS=no-reply@keretaayah.myuni.agency
```

Use:

- `auth@keretaayah.myuni.agency` for email verification and password reset.
- `no-reply@keretaayah.myuni.agency` for booking, payment, verification, and system emails.

Do not include real mailbox passwords in committed files.

## 6. File Uploads And Storage

Public car images use Laravel public storage:

```bash
php artisan storage:link
```

If cPanel does not support symlinks, manually mirror `storage/app/public` to `public/storage` or ask the hosting provider to enable symlink support.

Private customer verification documents and payment proofs are stored on the private local disk and should be served only through authenticated controller routes. Do not expose `storage/app/private` under `public_html`.

## 7. Deployment Commands

On the server, run:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

Run `php artisan key:generate` only when creating a new production `.env` without an existing `APP_KEY`. Do not regenerate `APP_KEY` for an existing production app unless you understand the impact on encrypted data and sessions.

Run seeding only when creating or refreshing initial admin/staff accounts:

```bash
php artisan db:seed --force
```

If SSH is unavailable, use cPanel Terminal if provided. Otherwise run commands locally and upload the prepared files, then use any host-provided tool for migrations if available.

## 8. Frontend Assets

Build assets before upload:

```bash
npm run build
```

Upload `public/build` with the production files. `public/build` is ignored by git, so it must be included in the deployment package or built on the server.

Do not upload `node_modules` to production.

## 9. Scheduler And Cron

The current application does not require Laravel Scheduler for core booking, verification, payment, or email workflows.

No cron job is required at this time.

If scheduled tasks are added later, configure cPanel cron like this:

```bash
* * * * * php /home/account/keretaayah/artisan schedule:run >> /dev/null 2>&1
```

## 10. Security Checklist

Before launch:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://keretaayah.myuni.agency`
- A strong `APP_KEY` is present.
- `.env` is outside public access.
- `storage/app/private` is not publicly accessible.
- Customer verification documents require login and authorization.
- Payment proofs require login and authorization.
- Admin routes require admin role.
- Staff payment routes require admin or staff role.
- Customer booking routes require login, email verification, and approved customer verification.
- SMTP passwords are stored only in `.env`.
- Admin/staff seeded passwords are saved securely and changed after first login.
- HTTPS is enabled for the domain.
- Upload size limits allow 5 MB document/proof uploads.
- `public/storage` points only to public uploads.
- Local development uploads are not uploaded unless intentionally migrating live data.

## 11. Production Readiness Checks

Useful checks before final upload:

```bash
php artisan route:list
php artisan migrate:status
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Useful checks after deployment:

```bash
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Manually verify:

- Browser title and UI show KeretaAyah.
- Public car pages load.
- Customer registration sends email verification.
- Password reset email sends from the auth account.
- Verified and approved customer can create a booking.
- Unverified customer cannot create a booking.
- Booking confirmation emails send.
- Admin receives new-booking notification.
- Customer payment proof upload is private and viewable only by authorized users.
- Admin/staff can review payments.
- Verification documents are private and viewable only by the customer/admin.

## 12. Troubleshooting

If emails fail:

- Confirm mailbox passwords are correct.
- Confirm SMTP port `465` is allowed by the host.
- Confirm `MAIL_ENCRYPTION=ssl`.
- Run `php artisan config:clear` after changing `.env`.

If assets do not load:

- Confirm `npm run build` completed.
- Confirm `public/build` exists on the server.
- Confirm the domain document root points to the correct public folder.

If uploads do not display:

- Run `php artisan storage:link`.
- Confirm `public/storage` points to `storage/app/public`.
- Confirm file permissions are readable by the web server.

If private documents are exposed:

- Confirm `storage/app/private` is not inside `public_html`.
- Confirm `config/filesystems.php` does not directly serve the private local disk.
- Access documents only through the application routes.
