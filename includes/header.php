<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = session_save_path();
    if ($sessionPath !== '' && (!is_dir($sessionPath) || !is_writable($sessionPath))) {
        $localSessionPath = dirname(__DIR__) . '/.runtime/sessions';
        if (!is_dir($localSessionPath)) {
            @mkdir($localSessionPath, 0775, true);
        }
        if (is_dir($localSessionPath) && is_writable($localSessionPath)) {
            session_save_path($localSessionPath);
        }
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

require_once __DIR__ . '/../config/site-content.php';
require_once __DIR__ . '/content-database.php';

$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if (strpos($currentScript, '/admin/') !== false) {
    $currentPage = 'admin';
} elseif ($currentPage === 'index') {
    $currentPage = 'home';
}

$pageTitle = isset($pageTitle) ? $pageTitle : $siteConfig['default_page_title'];
$pageDescription = isset($pageDescription) ? $pageDescription : $siteConfig['site_description'];
$styleVersion = (string) (@filemtime(__DIR__ . '/../assets/css/style.css') ?: ($assetVersion ?? '1'));
$responsiveVersion = (string) (@filemtime(__DIR__ . '/../assets/css/responsive.css') ?: ($assetVersion ?? '1'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($pageDescription); ?>">
    <title><?php echo e($pageTitle); ?> | <?php echo e($siteConfig['site_name']); ?></title>
    <meta property="og:title" content="<?php echo e($pageTitle); ?>">
    <meta property="og:description" content="<?php echo e($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo site_url('assets/images/hero/hero-graphic.svg'); ?>">
    <link rel="icon" href="<?php echo site_url('assets/images/logo/ssvdp-logo.jpg'); ?>" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/style.css') . '?v=' . rawurlencode($styleVersion); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/responsive.css') . '?v=' . rawurlencode($responsiveVersion); ?>">
</head>
<body>
    <header class="site-header" data-site-header>
        <div class="container header-inner">
            <a class="brand" href="<?php echo site_url('index.php'); ?>" aria-label="SSVP South Sudan home">
                <img src="<?php echo site_url($siteConfig['logo']); ?>" alt="Society of St. Vincent de Paul South Sudan logo" class="site-logo" width="104" height="104">
                <span class="brand-text">
                    <strong>SSVP South Sudan</strong>
                    <small>Serviens in Spe</small>
                </span>
            </a>
            <button class="mobile-nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="primary-navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <?php require __DIR__ . '/navigation.php'; ?>
        </div>
    </header>

    <main id="main-content">
