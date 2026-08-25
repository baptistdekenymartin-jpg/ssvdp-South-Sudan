<?php
require_once __DIR__ . '/includes/auth.php';
header('Location: ' . (admin_current_user() ? admin_url('dashboard.php') : admin_url('login.php')));
exit;
