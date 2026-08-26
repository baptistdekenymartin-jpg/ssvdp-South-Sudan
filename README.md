# SSVP South Sudan Website

## Purpose
This project creates the official public website for the Society of St. Vincent de Paul South Sudan using PHP, MySQL, HTML5, CSS3 and vanilla JavaScript. The first phase focuses on the public homepage, shared header/footer, reusable configuration and database preparation for later administration integration.

## Technology Used
- PHP 8+
- MySQL / MariaDB through XAMPP
- HTML5 and CSS3
- Vanilla JavaScript
- Bootstrap Icons

## XAMPP Setup
1. Start Apache and MySQL in XAMPP.
2. Place this project in the XAMPP web root at `C:\xampp\htdocs\ssvdp-south-sudan`.
3. Open the project in your browser at http://localhost/ssvdp-south-sudan/.

## Database Import
1. Open phpMyAdmin at http://localhost/phpmyadmin/.
2. Import `database/ssvdp_database.sql`. The script creates the `ssvdp_south_sudan` database if it does not already exist.
3. Update local connection settings in `config/database.php` if your XAMPP MySQL username or password differs from the default.


## Database Configuration
`config/database.php` contains local or production credentials and is intentionally excluded from Git. Use `config/database.example.php` as the safe template when setting up a new environment.

Required values:
- `host`: database host name
- `database`: database name
- `username`: database user
- `password`: database password
- `charset`: usually `utf8mb4`

## Where to Add Assets
- Official logo: `assets/images/logo/` and then update the image path in `includes/header.php` and `includes/footer.php`.
- Homepage activity image: replace `assets/images/placeholders/activity-placeholder.svg` or update the image path in `index.php`.
- Contact details and impact figures: `config/site-content.php`

## Security Preparation
- `config/database.php` prepares PDO with exceptions, associative fetches and native prepared statements.
- `config/site-content.php` includes escaping and CSRF helper functions for Phase 2 forms.
- `includes/header.php` starts sessions with HttpOnly and SameSite cookie settings and disables public PHP error display.
- No administrator account or plain-text password is inserted by the database script.

## What Remains for Phase 2
- Full admin authentication and dashboard
- Dynamic content management for news, programmes, gallery and resources
- Complete contact form processing and content approval workflow
