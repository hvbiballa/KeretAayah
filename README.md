# KeretaAyah

KeretaAyah is a Laravel, Inertia, Vue, Tailwind CSS, and MySQL car rental management application.

The system supports customer car browsing, hourly and daily bookings, availability checks, customer document verification, manual payment tracking, and admin/staff operations.

## Main Features

- Public car listing and car details pages
- Hourly and daily rental booking flow
- Availability calendar and server-side overlap validation
- Customer email verification before new bookings
- Customer Government ID and Driving License verification
- Private verification document access
- Manual payment tracking with customer proof upload
- Admin car, rental, customer, verification, and payment management
- Staff payment review access
- KeretaAyah-branded email notifications

## Tech Stack

- Laravel 12
- Inertia.js
- Vue 3
- Tailwind CSS 4
- MySQL
- Vite

## Local Development

Install PHP and Node dependencies:

```bash
composer install
npm install
```

Create and configure the environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

For cPanel production deployment, see [DEPLOYMENT.md](DEPLOYMENT.md).
