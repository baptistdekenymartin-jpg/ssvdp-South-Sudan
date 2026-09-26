# Production Security Checklist

Before publishing SSVP South Sudan to production:

- Set `SSVP_ENV=production` or `SSVP_PRODUCTION=1` in the hosting environment.
- Enable HTTPS and set `SSVP_FORCE_HTTPS=1` after the certificate is installed.
- Keep `display_errors` off and confirm PHP errors are written to server-side logs only.
- Use strong unique database credentials; keep `config/database.php` out of Git and outside public backups.
- Remove default/test admin accounts and review active Super Administrator users.
- Confirm admin passwords are at least 12 characters with upper/lower case, number, and symbol.
- Confirm `.htaccess` is honored by Apache/Hostinger and `.git`, `.env`, `config`, `includes`, backups, logs, and SQL dumps are not web-accessible.
- Confirm `uploads/.htaccess` blocks PHP/script execution while still serving images.
- Keep writable permissions limited to `uploads/` and `.runtime/` where required.
- Enable scheduled database and upload backups stored outside the public web root.
- Verify admin pages send `Cache-Control: no-store`, frame protection, `nosniff`, referrer policy, and CSP headers.
- Verify public forms use CSRF fields, honeypot fields, server-side validation, and rate limiting.
- Review Activity Log after deployment for login failures, password changes, user changes, publish/unpublish, archive/delete, exports, and security actions.
- Do not publish private data, admin endpoints, database config, or credentials into `/docs` static preview.
