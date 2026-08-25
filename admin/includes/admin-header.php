<?php
$user = $adminUser ?? admin_require_auth();
$adminTitle = $adminTitle ?? 'Dashboard';
$activeNav = $activeNav ?? 'dashboard';
$flashMessages = admin_take_flash();
require_once __DIR__ . '/communication.php';
$phase3Notifications = admin_phase3_count('unread_messages') + admin_phase3_count('new_involved');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($adminTitle); ?> | SSVDP Admin</title>
    <link rel="icon" href="<?php echo site_url('assets/images/logo/ssvdp-logo.jpg'); ?>" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css') . '?v=' . rawurlencode((string) (@filemtime(__DIR__ . '/../../assets/css/admin.css') ?: '1')); ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <?php require __DIR__ . '/admin-sidebar.php'; ?>
    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-menu-toggle" type="button" data-admin-menu aria-label="Toggle menu"><i class="bi bi-list" aria-hidden="true"></i></button>
            <div>
                <h1><?php echo e($adminTitle); ?></h1>
            </div>
            <div class="admin-account">
                <a class="admin-bell" href="<?php echo admin_url('messages/'); ?>" title="Communication notifications"><i class="bi bi-bell" aria-hidden="true"></i><?php echo admin_phase3_badge($phase3Notifications); ?></a>
                <span><?php echo e($user['name']); ?><small>Administrator</small></span>
                <a href="<?php echo admin_url('account/password.php'); ?>">Change Password</a><a href="<?php echo admin_url('logout.php'); ?>">Logout</a>
            </div>
        </header>
        <?php foreach ($flashMessages as $flashMessage) : ?>
            <div class="admin-alert admin-alert--<?php echo e($flashMessage['type']); ?>"><?php echo e($flashMessage['message']); ?></div>
        <?php endforeach; ?>
        <main class="admin-content">
