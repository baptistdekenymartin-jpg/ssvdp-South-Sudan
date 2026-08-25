<?php
require_once __DIR__ . '/includes/auth.php';
admin_log('logout', 'admin_user', isset($_SESSION['admin_user_id']) ? (int) $_SESSION['admin_user_id'] : null, 'Staff user signed out.');
admin_clear_session();
header('Location: ' . admin_url('login.php'));
exit;