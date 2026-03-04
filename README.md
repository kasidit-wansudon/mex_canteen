# Canteen Management System

Mobile-first canteen reservation system with user booking, admin QR validation, dashboard analytics, and exportable reports.

## Stack

- Backend: Laravel 8.83.29, PHP 7.4.33, MySQL, Laravel Sanctum
- Frontend: Vue 3.5.24, Vant 4.9.21, Vue I18n 11.1.12, Axios 0.21.4, Tailwind CSS 3.4.18
- Build: Laravel Mix 6.0.49 (Webpack), Node.js 20.19.6, npm 10.8.2

## Features

- User management with staff classification (`jumbo`, `latam`) and enable/disable status
- Visitor account creation with validity date range
- Mobile booking flow:
  - meal plan selection by date and meal type
  - reservation modes: `self`, `self_invitation`, `self_pickup`, `invitation_only`, `pickup_only`
  - QR token generation with expiry + single-use enforcement
  - reservation edit/cancel + booking history
- Admin modules:
  - daily meal plan publish/edit
  - QR validation scanner endpoint
  - dashboard metrics (`day/week/month`)
  - daily/monthly reports + 7-day grid
  - CSV export for daily report

## Project Structure

- API routes: `routes/api.php`
- SPA routes: `routes/web.php`
- Domain models: `app/Models`
- API controllers: `app/Http/Controllers/API/Canteen`
- Admin API controllers: `app/Http/Controllers/API/Canteen/Admin`
- Form requests: `app/Http/Requests/Canteen`
- API resources: `app/Http/Resources/Canteen`
- Report repository: `app/Repositories/Canteen/ReportRepository.php`
- Services:
  - `app/Services/Canteen/MealPlanService.php`
  - `app/Services/Canteen/QrCodeService.php`
- Vue app: `resources/js`

## Database

Main tables:

1. `users`
2. `visitors`
3. `meal_plans`
4. `reservations`
5. `meal_collections`
6. `collection_logs`

Migrations are in `database/migrations`.

## Environment

Copy environment file:

```bash
cp .env.example .env
```

Configure multiple database connections in `.env`:

- Primary: `DB_*`
- Catty: `DB_*_CATTY`
- WMS: `DB_*_WMS`

## Install & Run

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Authentication

- API login: `POST /api/auth/login`
- Uses Sanctum bearer token (`Authorization: Bearer <token>`)
- Admin endpoints require:
  - authenticated token
  - user role = `admin`

Seed users:

- Admin: `A0001 / password123`
- User: `U1001 / password123`

## API Endpoints

### Auth & User

- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/user/profile`

### Meal & Reservation

- `GET /api/meal-plans/daily`
- `GET /api/reservations`
- `POST /api/reservations`
- `GET /api/reservations/{id}`
- `GET /api/reservations/{id}/qr`
- `PUT /api/reservations/{id}`
- `DELETE /api/reservations/{id}`

### Admin

- `POST /api/admin/meal-plans`
- `POST /api/admin/qr-validate`
- `GET /api/admin/dashboard/stats`
- `GET /api/admin/reports/daily`
- `GET /api/admin/reports/monthly`
- `GET /api/admin/reports/weekly-grid`
- `GET /api/admin/users`
- `PATCH /api/admin/users/{id}/status`
- `POST /api/admin/users/visitor`

## Frontend Routes

- `/` user meal selection
- `/reservation` reservation QR
- `/history` history calendar/list
- `/admin` admin login
- `/admin/menu` meal plan management
- `/admin/scan` QR validation
- `/admin/dashboard` dashboard
- `/admin/users` user management
- `/admin/reports` reports & export

## OpenAPI

See: `docs/openapi.yaml`

## Notes

- Default locale: Chinese (`zh-CN`), fallback English (`en`)
- Timezone: `Asia/Shanghai` (CST)
- Daily meal plans are cached for 10 minutes
- Report queries are centralized in repository to avoid scattered complex SQL
