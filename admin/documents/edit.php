<?php
require_once __DIR__ . '/../includes/auth.php';
admin_require_auth();
admin_flash('error', 'Documents / Resources has been disabled. Existing document records are preserved for backward compatibility.');
header('Location: ' . admin_url('dashboard.php'));
exit;
