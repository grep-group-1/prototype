# CapitalEdge Funding Platform

## Premium Fintech Funding Platform — University Prototype

A complete, database-backed fintech web application demonstrating how a simulated funding arrangement platform could operate. Built with **Core PHP 8.2+**, **MySQL/MariaDB**, **Bootstrap 5**, and designed for local development with **XAMPP**.

> **IMPORTANT:** This is a university prototype. No real money, investments, or financial services are involved. All data is simulated for testing purposes.

---

## Features

- **Secure Authentication:** Registration, login, logout, password reset, email verification
- **Role-Based Access Control:** Funder, Administrator, Compliance Reviewer, Finance Operator
- **Simulated KYC:** Mock identity verification workflow with configurable outcomes
- **Funding Agreements:** Two configurable profit-sharing arrangements with versioned contracts
- **Double-Entry Ledger:** Auditable financial records with balanced journal entries
- **Simulated Deposits:** Test payment gateway with idempotency protection
- **Profit-Sharing Calculator:** Server-side calculations using decimal-safe arithmetic
- **Distribution System:** Automated distribution calculations based on approved financial results
- **Withdrawal Processing:** Request, review, approve, and complete withdrawal workflow
- **Admin Dashboard:** User management, KYC review, financial operations, audit logs
- **Account Statements:** Downloadable CSV statements
- **Notifications:** In-app notification system
- **Responsive Design:** Premium UI with mobile, tablet, and desktop support
- **Security Controls:** CSRF protection, XSS prevention, prepared statements, rate limiting

---

## Technology Stack

| Component | Technology |
|-----------|-----------|
| Backend | Core PHP 8.2+ (OOP, PDO, sessions) |
| Database | MySQL/MariaDB (InnoDB, foreign keys, DECIMAL types) |
| Frontend | HTML5, CSS3, Bootstrap 5, JavaScript |
| Charts | Chart.js |
| Icons | Bootstrap Icons |
| Fonts | Inter (Google Fonts) |
| Server | Apache (XAMPP) |
| Development | XAMPP (Apache + MySQL + PHP + phpMyAdmin) |

---

## Requirements

- **XAMPP** (version 8.2+ recommended) — includes Apache, PHP, MySQL/MariaDB
- PHP extensions: `pdo_mysql`, `mbstring`, `json`, `openssl` (usually included)
- A modern web browser (Chrome, Firefox, Edge)
- About 50MB of disk space

---

## Installation Guide

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP following the installer instructions
3. Launch the XAMPP Control Panel

### Step 2: Start Services

1. In XAMPP Control Panel, click **Start** next to **Apache**
2. Click **Start** next to **MySQL**
3. Both should show a green "running" status

**Troubleshooting:** If Apache fails to start, port 80 may be in use. Change Apache's port to 8080 in `httpd.conf`. If MySQL fails, check if another MySQL instance is running on port 3306.

### Step 3: Place the Project

Copy the project to your XAMPP htdocs directory:

**Windows:**
```
C:\xampp\htdocs\premium-fintech\
```

**Linux (XAMPP/LAMPP):**
```
/opt/lampp/htdocs/premium-fintech/
```

Ensure the directory structure looks like:
```
premium-fintech/
├── app/
├── config/
├── database/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── resources/
├── routes/
├── storage/
├── tests/
├── .htaccess
└── README.md
```

### Step 4: Create the Database

1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser
2. Click **"New"** in the left sidebar
3. Enter database name: `premium_fintech`
4. Select collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

### Step 5: Import the Schema

1. In phpMyAdmin, select the `premium_fintech` database
2. Click the **"Import"** tab
3. Click **"Choose File"** and select `database/schema.sql`
4. Click **"Go"** at the bottom

### Step 6: Import the Seed Data

1. Still in phpMyAdmin with `premium_fintech` selected
2. Click the **"Import"** tab again
3. Select `database/seed.sql`
4. Click **"Go"**

### Step 7: Configure Database Credentials

Open `config/database.php` and verify the settings match your XAMPP setup:

```php
'host' => '127.0.0.1',
'port' => 3306,
'database' => 'premium_fintech',
'username' => 'root',      // XAMPP default
'password' => '',           // XAMPP default (empty)
```

### Step 8: Set Passwords

Run the seed script to set proper password hashes:

**Option A — Command Line:**
```bash
cd /path/to/premium-fintech
php database/seed_demo.php
```

**Option B — Browser (one-time):**
Navigate to `http://localhost/premium-fintech/database/seed_demo.php`

Then **delete or restrict access** to the seed script.

### Step 9: Access the Application

Open your browser and navigate to:

```
http://localhost/premium-fintech/
```

Or if Apache runs on port 8080:
```
http://localhost:8080/premium-fintech/
```

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@capitaledge.test | Admin@123 |
| Funder | funder@capitaledge.test | Funder@123 |
| Compliance | compliance@capitaledge.test | Compliance@123 |
| Finance | finance@capitaledge.test | Finance@123 |

> **Note:** These are demo accounts for local testing only.

---

## How to Test the Application

### Testing as a Funder:
1. Log in as `funder@capitaledge.test`
2. Complete KYC verification (simulated — result is random)
3. Browse and accept a funding arrangement
4. Make a simulated deposit (e.g., UGX 5,000,000)
5. View your dashboard and transaction history
6. Download a CSV statement

### Testing as an Administrator:
1. Log in as `admin@capitaledge.test`
2. Review KYC cases and approve/reject
3. Confirm deposits and allocate capital
4. Record financial results
5. Approve financial results (triggers distribution calculations)
6. Approve and pay distributions
7. Process withdrawal requests
8. View reports and reconciliation
9. Check audit logs

---

## Project Structure

```
premium-fintech/
├── app/                        # Application code
│   ├── Controllers/            # Request handlers
│   │   ├── Admin/             # Admin controllers
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── ...
│   ├── Helpers/               # Utility classes
│   │   ├── Csrf.php
│   │   ├── Escape.php
│   │   ├── Flash.php
│   │   ├── Format.php
│   │   └── Url.php
│   ├── Middleware/            # Request middleware
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── ...
│   ├── Models/               # Data models (future)
│   ├── Repositories/         # Data access (future)
│   └── Services/             # Business logic
│       ├── Auth.php
│       ├── Database.php
│       ├── PaymentGateway.php
│       └── Router.php
├── config/                    # Configuration
│   ├── app.php
│   └── database.php
├── database/                  # Database files
│   ├── schema.sql
│   ├── seed.sql
│   └── seed_demo.php
├── public/                    # Web-accessible directory
│   ├── index.php             # Front controller
│   ├── .htaccess             # URL rewriting
│   └── assets/
│       ├── css/style.css
│       ├── js/app.js
│       └── images/
├── resources/views/          # View templates
│   ├── layouts/
│   ├── auth/
│   ├── public/
│   ├── funder/
│   ├── admin/
│   └── errors/
├── storage/                  # Writable storage
│   ├── logs/
│   └── exports/
├── tests/                    # Test files
├── .env.example              # Environment template
├── .gitignore
├── .htaccess                 # Root rewrite rules
└── README.md
```

---

## Security Features

- **CSRF Protection:** Token validation on all state-changing requests
- **XSS Prevention:** All output escaped with `htmlspecialchars()` (UTF-8)
- **SQL Injection Prevention:** PDO prepared statements throughout
- **Password Security:** `password_hash()` with bcrypt, `password_verify()`
- **Session Security:** Regenerated IDs, httponly cookies, samesite
- **Rate Limiting:** Login lockout after failed attempts
- **Authorization:** Server-side role and ownership checks on every route
- **Financial Integrity:** Double-entry ledger, database transactions, idempotency keys
- **Input Validation:** Server-side validation of all form inputs
- **Error Handling:** No stack traces or credentials in error output

---

## Architecture Highlights

### Double-Entry Ledger
Every financial operation creates a balanced journal with at least two entries (debit + credit). Account balances are derived from the sum of entries. The reconciliation report verifies that cached balances match calculated totals.

### Profit-Sharing Calculation
Uses integer arithmetic on minor currency units to avoid floating-point errors. Funder share = (Net Result × Capital Ratio) × Profit Share Percentage. Losses carry forward per agreement terms.

### Idempotency
Payment and withdrawal requests include unique idempotency keys to prevent duplicate processing even if a request is submitted multiple times.

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Apache won't start | Change port in httpd.conf; stop Skype/IIS |
| MySQL won't start | Stop other MySQL instances; check data directory permissions |
| "Database connection failed" | Verify credentials in `config/database.php` |
| 404 errors on pages | Enable `mod_rewrite` in Apache; check `.htaccess` |
| Blank white page | Enable error display in `config/app.php` (`env` = `local`) |
| Session errors | Check PHP session save path in `php.ini` |
| "Class not found" | Verify directory structure matches autoloader expectations |
| Passwords don't work | Run `database/seed_demo.php` to set correct hashes |

---

## Testing Checklist

- [ ] Register a new account
- [ ] Log in and verify dashboard loads
- [ ] Complete KYC verification
- [ ] Accept a funding arrangement
- [ ] Make a simulated deposit
- [ ] Verify ledger entries are created
- [ ] Log in as admin, allocate capital
- [ ] Record and approve financial results
- [ ] Verify distribution calculations
- [ ] Request and process a withdrawal
- [ ] Download CSV statement
- [ ] Verify CSRF protection (inspect form tokens)
- [ ] Verify unauthorized access is blocked (403)
- [ ] Run reconciliation report (no discrepancies)
- [ ] Verify audit logs record all actions

---

## Disclaimer

This is a **university prototype** for educational purposes. It does not constitute:
- A licensed investment service
- A savings account or deposit-taking facility
- A payment service or money transmitter
- Financial advice of any kind

Before any real-money deployment, obtain: qualified legal advice, regulatory approvals, independent security audit, and compliance assessment for applicable laws.

---

## License

This project is created for educational purposes as part of a university Computer Science program.

---

*Built for the Computer Science Department — CapitalEdge Funding Platform Prototype*
