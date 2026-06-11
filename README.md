# Charity Management System

A full-stack web application built with **PHP, MySQL, HTML5, CSS3, and JavaScript** for managing charity operations — donors, beneficiaries, donations, and reports.

## Features

### Admin Panel
- Secure admin login
- Dashboard with live stats (total donors, beneficiaries, donations, amount raised)
- Manage donors — add, view, delete; see how much each donor has contributed
- Manage beneficiaries — add/edit individuals, families, and organizations; toggle active/inactive
- Donations management — filter by donor, status, or date range; approve or cancel donations
- Reports & Analytics — donation breakdown by category, payment method, monthly trend, top donors

### Donor Portal
- Register and login securely
- Personal dashboard with donation summary
- Make a donation — choose amount, category, beneficiary, payment method
- Full donation history with status tracking

## Tech Stack

- **Backend:** PHP (procedural + OOP-style helpers)
- **Database:** MySQL with relational schema (foreign keys, indexed queries)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Auth:** PHP sessions + `password_hash` / `password_verify`
- **Architecture:** MVC-inspired folder structure with shared includes

## Project Structure

```
charity-management-system/
├── index.php               # Landing page
├── sql/
│   └── schema.sql          # DB schema + seed data
├── includes/
│   ├── config.php          # DB connection + helper functions
│   ├── header.php          # Shared nav header
│   └── footer.php          # Shared footer
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── donors.php
│   ├── beneficiaries.php
│   ├── donations.php
│   └── reports.php
├── donor/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── donate.php
│   └── history.php
└── assets/
    ├── css/style.css
    └── js/main.js
```

## Setup & Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+ or MariaDB
- Apache/Nginx with `mod_rewrite` (XAMPP / WAMP / LAMP)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/charity-management-system.git
   cd charity-management-system
   ```

2. **Set up the database**
   - Open phpMyAdmin (or MySQL CLI)
   - Run the SQL file:
     ```bash
     mysql -u root -p < sql/schema.sql
     ```

3. **Configure database credentials**
   - Open `includes/config.php`
   - Update:
     ```php
     define('DB_USER', 'root');   // your MySQL username
     define('DB_PASS', '');       // your MySQL password
     ```

4. **Place project in web root**
   - For XAMPP: copy to `htdocs/charity-management-system/`
   - For WAMP: copy to `www/charity-management-system/`

5. **Access in browser**
   ```
   http://localhost/charity-management-system/
   ```

## Default Credentials

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@charity.com   | password |

> ⚠️ Change these immediately after setup.

## Database Schema

| Table          | Purpose                                  |
|----------------|------------------------------------------|
| `admins`       | Admin accounts                           |
| `donors`       | Donor accounts with login credentials    |
| `beneficiaries`| Individuals/families/orgs receiving aid  |
| `categories`   | Donation categories (Education, Food...) |
| `donations`    | Transaction records linking donors & beneficiaries |


