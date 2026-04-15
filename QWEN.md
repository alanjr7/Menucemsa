# Menucemsa - Hospital Management System

## Project Overview

**Menucemsa** is a comprehensive hospital/clinical management system built with **Laravel 12** (PHP 8.2+). It provides a full-stack web application for managing all aspects of a hospital or clinic, including patient admissions, surgeries, pharmacy, billing, emergency services, ICU (UTI), and administrative reporting.

### Architecture

- **Backend:** Laravel 12 (MVC pattern)
- **Frontend:** Blade templates + Tailwind CSS + Alpine.js + Vite
- **Database:** SQLite (default), configurable for MySQL via `doctrine/dbal`
- **Authentication:** Laravel Breeze with role-based access control
- **Styling:** Tailwind CSS with custom components

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **PHP** | ^8.2 |
| **Framework** | Laravel 12 |
| **Frontend** | Tailwind CSS, Alpine.js, Vite 7 |
| **Auth** | Laravel Breeze |
| **Database ORM** | Eloquent + Doctrine DBAL |
| **Testing** | PHPUnit, Faker, Mockery |
| **Code Style** | Laravel Pint |
| **Dev Server** | Laravel Sail, artisan serve |

## User Roles

The system implements a role-based access control (RBAC) with the following roles:

| Role | Description |
|------|------------|
| `admin` | Full system access |
| `reception` | Patient admissions, appointments, triage |
| `dirmedico` | Medical director - oversees medical operations |
| `doctor` | Attending physicians - patient consultations |
| `emergencia` | Emergency room staff |
| `caja` | Cashier - billing and payments |
| `farmacia` | Pharmacy - inventory and sales |
| `gerente` | Manager - reports and KPIs |
| `uti` | ICU (UTI) staff |

## Key Modules

### 1. Reception / Admissions
- Patient registration and lookup
- Appointment scheduling and management
- Triage system
- External consultations (consulta externa)
- Emergency intake
- Hospitalization admissions

### 2. Operating Room (Quirófano)
- Surgery scheduling and management
- Operating room availability
- Calendar view
- Surgical medication tracking
- Emergency surgery scheduling

### 3. Pharmacy
- Medication inventory management
- Point of sale (POS)
- Sales and receipts
- Client management
- Pharmacy reports

### 4. Cash Management (Caja)
- Daily cash register opening/closing
- Payment processing
- Patient billing accounts
- Financial summaries
- Audit trails
- UTI billing integration

### 5. Medical Areas
- **Emergency:** Emergency patient management and surgery scheduling
- **Nursing (Enfermería):** Nursing workflow management
- **ICU (UTI):** Bed management, admissions, vital signs, daily records, medications, supplies, catering, recipes
- **Hospitalization:** Inpatient management
- **Doctor Dashboard:** Patient consultation workflow

### 6. Administration
- Insurance management
- Fee schedules (tarifarios)
- Accounts receivable
- Specialties management
- Doctor management
- Activity logs
- Warehouse medication storage

### 7. Management Reports (Gerencial)
- KPIs and dashboards
- Comprehensive reports

### 8. Security / Users
- User management
- Role-based access control

## Core Models (49 total)

Notable models include:
- `User` - System users with roles
- `Paciente` - Patients
- `Medico` - Doctors
- `Cita` / `CitaQuirurgica` - Appointments / Surgical appointments
- `Cirugia` - Surgeries
- `Quirofano` - Operating rooms
- `Emergency` - Emergency records
- `Consulta` - Medical consultations
- `Receta` / `DetalleReceta` - Prescriptions
- `Hospitalizacion` - Hospitalizations
- `Farmacia` / `Medicamentos` / `InventarioFarmacia` - Pharmacy
- `VentaFarmacia` / `DetalleVentaFarmacia` - Pharmacy sales
- `Caja` / `CajaDiaria` / `CajaSession` / `MovimientoCaja` - Cash management
- `CuentaCobro` / `PagoCuenta` - Billing accounts
- `UtiBed` / `UtiAdmission` / `UtiVitalSign` / `UtiDailyRecord` / `UtiMedication` / `UtiSupply` / `UtiCatering` - ICU management
- `Triage` - Triage records
- `Seguro` - Insurance
- `Tarifa` - Fee schedules
- `Especialidad` - Medical specialties
- `ActivityLog` - System activity logging

## Project Structure

```
Menucemsa/
├── app/
│   ├── Console/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/  # Organized by area (Admin, Caja, Farmacia, Medical, Reception, etc.)
│   │   ├── Middleware/   # Custom middleware (role checking)
│   │   ├── Requests/     # Form request validation
│   │   └── Resources/    # API resources
│   ├── Listeners/        # Event listeners
│   ├── Models/           # 49 Eloquent models
│   ├── Observers/        # Model observers
│   ├── Policies/         # Authorization policies
│   ├── Providers/        # Service providers
│   ├── Services/         # Business logic services
│   └── View/             # View components
├── bootstrap/            # Framework bootstrap files
├── config/               # Laravel configuration
├── database/
│   ├── migrations/       # 60 database migrations
│   ├── factories/        # Model factories for testing
│   └── seeders/          # Database seeders
├── resources/
│   ├── views/            # 125+ Blade templates
│   └── js/css            # Frontend assets
├── routes/
│   ├── web.php           # Main web routes (547 lines)
│   ├── auth.php          # Breeze auth routes
│   └── console.php       # Console/artisan routes
└── tests/                # PHPUnit tests
```

## Building and Running

### Initial Setup

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy env file and generate key
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build frontend assets
npm run build
```

### Development

```bash
# Run all services (server, queue, logs, vite) concurrently
composer run dev

# Or run individually:
php artisan serve        # Start dev server
npm run dev              # Start Vite dev server
php artisan queue:listen # Start queue worker
php artisan pail         # View logs
```

### Testing

```bash
# Run tests
composer run test
# Or:
php artisan test
```

### Code Style

```bash
# Format code with Laravel Pint
./vendor/bin/pint
```

## Key Commands

```bash
php artisan migrate              # Run database migrations
php artisan migrate:fresh        # Fresh migration (drop all tables)
php artisan db:seed              # Seed the database
php artisan serve                # Start development server
php artisan cache:clear          # Clear application cache
php artisan config:clear         # Clear configuration cache
php artisan route:list           # List all routes
php artisan tinker               # Interactive PHP console
```

## Development Conventions

- **PSR-4 autoloading** for `App\` namespace mapped to `app/`
- **Controllers** are organized by functional area (Admin, Caja, Farmacia, Medical, Reception, Seguridad, Gerencial)
- **Routes** are grouped by role-based middleware (e.g., `role:admin|reception|dirmedico`)
- **Blade templates** use Tailwind CSS classes and Alpine.js for interactivity
- **API routes** are prefixed with `/api/` within web routes
- **Models** use Eloquent relationships and casts for data types
- **Migrations** are timestamped and organized chronologically
- **Database** uses SQLite by default (configurable in `.env`)

## Environment Configuration

Key `.env` variables (defaults from `.env.example`):
- `DB_CONNECTION=sqlite` (default database)
- `APP_DEBUG=true`
- `SESSION_DRIVER=database`
- `QUEUE_CONNECTION=database`
- `CACHE_STORE=database`
