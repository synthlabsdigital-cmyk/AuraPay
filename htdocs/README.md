# AuraPay — Lending Platform Framework

A complete PHP 8+ / MySQL lending platform built to the AuraPay Master Build Specification.

## Technology Stack

- **PHP 8.0+** (strict types, no frameworks)
- **MySQL 8.0+** / MariaDB 10.6+
- **HTML5** with **Bootstrap 5.3** (via CDN)
- **Vanilla JavaScript** (no build step)
- **PDO** for database access

## Requirements

- PHP 8.0 or higher with PDO MySQL extension
- MySQL 8.0 or MariaDB 10.6
- Apache with mod_rewrite, mod_headers enabled
- Composer is NOT required — no third-party PHP packages

## Installation

### 1. Database

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p aurapay < database/seed.sql
```

### 2. Configuration

Edit `config/database.php` with your MySQL credentials:

```php
return [
    'host'    => '127.0.0.1',
    'port'    => 3306,
    'name'    => 'aurapay',
    'user'    => 'your_db_user',
    'pass'    => 'your_db_password',
    'charset' => 'utf8mb4',
];
```

Application-level settings (name, timezone, currency, support contact) are in `config/app.php`. Product branding and loan/credit parameters are in `config/product.php`. No `.env` file or environment variables are required.

### 3. File Permissions

Ensure the following directories are writable by the web server:

```bash
chmod -R 755 uploads/ logs/ storage/
```

### 4. Default Admin

The installer auto-creates a default admin on first run if one does not exist:

- **Email:** `admin@aurapay.ph`
- **Password:** `Admin@12345`

Change this password immediately after first login.

## Directory Structure

```
/
├── config/           Configuration files (app, database, session, constants, routes, product)
├── database/         SQL schema and seed data
├── helpers/          PHP helper classes (Database, Auth, Loan, Credit, etc.)
├── includes/         Shared layout files (header, footer, bootstrap)
├── auth/             Authentication pages (login, register, verify_otp, etc.)
├── pages/            Customer portal pages
├── admin/            Admin portal pages
├── assets/           CSS, JS, images, fonts
├── uploads/          User-uploaded documents (writable)
├── logs/             Application logs (writable)
├── storage/          Cache, sessions, temp (writable)
├── index.php         Landing page
└── .htaccess         Apache configuration
```

## Features

### Customer Portal
- Registration with email OTP verification (development OTP popup — no SMS)
- Login with account lockout after 5 failed attempts
- Profile management (personal, address, employment, financial, ID, emergency contact)
- Document upload (government ID, proof of income, proof of billing, selfie)
- Credit evaluation engine (weighted scoring: employment, income, documents, identity, history)
- Loan application with live summary calculator
- Loan history with full details and amortization schedule
- Payment processing with installment tracking
- Transaction history with filtering and pagination
- Timeline view of account journey
- In-app notifications
- Settings (change password, account info)

### Admin Portal
- Hidden admin login (separate from customer login)
- Dashboard with key metrics
- Customer management (search, view, suspend, reactivate)
- Loan application review workflow (pending → under review → approved → disbursed)
- Loan management with full detail view
- Credit evaluation overview
- Transaction management
- Reports (portfolio summary, loan status breakdown, monthly trends, top customers)
- Activity logs (audit trail with type and severity filtering)
- Configuration (loan parameters, credit thresholds, security, contact, branding)
- Maintenance mode toggle with custom messages
- Late penalty application

### Security
- CSRF tokens on all forms
- Password hashing with bcrypt
- Session regeneration to prevent fixation
- Account lockout after failed login attempts
- File upload validation (type, size, MIME)
- .htaccess protection of sensitive directories
- Security headers (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection)

## Development OTP

In development, the OTP is displayed in a modal popup instead of being sent via SMS. This is controlled by the `dev_otp_enabled` setting (default: `1`). Set to `0` in production.

## Business Rules

- **Currency:** Philippine Peso (₱)
- **Loan range:** ₱5,000 – ₱50,000
- **Loan term:** 3 – 12 months
- **Interest rate:** 3.5% per month (configurable)
- **Processing fee:** ₱150 (configurable)
- **Late penalty:** 2% per week after 3-day grace period
- **Credit score range:** 300 – 850
- **Approval threshold:** 580
- **OTP:** 6-digit, 10-minute expiry, max 5 attempts
- **One active loan per customer** at a time

## License

Proprietary — AuraPay Lending Inc.
