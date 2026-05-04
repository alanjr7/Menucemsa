# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Full setup from scratch
composer run setup

# Start all dev services concurrently (Laravel server, queue worker, Pail log viewer, Vite)
composer run dev

# Run tests (clears config first)
composer run test

# Run a single test file or method
php artisan test --filter TestClassName
php artisan test tests/Feature/SomeTest.php

# PHP code formatting
./vendor/bin/pint
./vendor/bin/pint app/Http/Controllers/SomeController.php

# Database
php artisan migrate
php artisan migrate:refresh
```

## Architecture Overview

**Menucemsa** is a hospital management system (HMS) for a Bolivian clinic. It manages 8 major operational areas: Reception, Emergency, Operating Rooms (Quirófano), Hospitalization (Internación), ICU (UTI), Pharmacy (Farmacia), Billing (Caja), and Administration.

### Route & Controller Organization

All routes live in `routes/web.php` (no separate `routes/api.php`). AJAX endpoints are mixed into the same file with `/api/` prefixes and return JSON. Controllers are namespaced by domain:

- `App\Http\Controllers\Reception\*` — patient intake flows (emergency, hospitalization, outpatient, ICU, unified ingreso)
- `App\Http\Controllers\Medical\*` — physician-facing views per area
- `App\Http\Controllers\Admin\*` — administrative management (users, rates, insurance, UTI admin)
- `App\Http\Controllers\Farmacia\*` — pharmacy (inventory, point-of-sale, sales, reports)
- `App\Http\Controllers\Caja\*` — billing (daily cash operations, financial management)
- `App\Http\Controllers\Gerencial\*` — management reports and KPIs

Routes are protected by `role:` middleware using these role strings: `admin`, `administrador`, `reception`, `dirmedico`, `doctor`, `caja`, `gerente`, `farmacia`, `emergencia`, `enfermera-emergencia`, `internacion`, `enfermera-internacion`, `cirujano`, `uti`.

### Dashboard Routing

`DashboardController` redirects authenticated users to their role-specific dashboard view. This is the entry point after login.

### Service Layer

Complex business logic lives in `app/Services/`:

- `CuentaCobroService` — all billing logic: insurance pre-authorization, payment allocation, account consolidation, duplicate detection
- `AplicarSeguroService` — insurance claim processing
- `ActivityLogService` — audit trail writes
- `NotificationService` — user notification delivery

### Security & Audit

Three layers of security applied globally to all web routes via `bootstrap/app.php`:

1. `CheckUserStatus` — user must be active
2. `ForceHttp` — strips HTTPS in dev
3. `AuditMiddleware` — logs every request via `ActivityLogService`

IP whitelist enforcement via `CheckIpAccess` middleware (applied per route group). Audit logs are queryable via the Admin panel at `/seguridad/activity-logs`.

### Data Model Notes

- `Paciente` uses `ci` (cédula de identidad) as the primary key, not `id`. Many related models reference `paciente_ci`.
- `CuentaCobro` represents a patient billing account; `CuentaCobroDetalle` are the line items. Deleted line items go to `CuentaCobroDetalleEliminado` for audit purposes.
- ICU (UTI) has its own parallel model hierarchy: `UtiAdmission`, `UtiBed`, `UtiVitalSign`, `UtiDailyRecord`, etc.
- `MovimientoCaja` tracks every financial transaction for the cash register.

### Frontend

Blade templates with Alpine.js for interactivity and Tailwind CSS for styling. Chart.js is used for dashboards. No SPA framework — all pages are server-rendered with selective AJAX calls via Axios.

Assets built with Vite. Entry point: `resources/js/app.js` and `resources/css/app.css`.

### Custom Artisan Commands

Located in `app/Console/Commands/`. Useful for data maintenance:

- `CerrarHospitalizacionesDuplicadas` — closes duplicate hospitalization records
- `ConsolidarCuentasDuplicadas` / `ConsolidarCuentasPaciente` — merges duplicate billing accounts
- `UnificarEspecialidadesCommand` — merges duplicate specialty entries

