<?php
require_once __DIR__ . '/../includes/auth.php';
admin_require_auth();
admin_flash('error', 'Impact Updates has been disabled. Use Programme Updates for programme progress and achievements.');
header('Location: ' . admin_url('dashboard.php'));
exit;
