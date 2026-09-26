<?php

require_once __DIR__ . '/security.php';
ssvdp_configure_error_handling();
ssvdp_security_headers(false);

ssvdp_start_secure_session();

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
$canonicalPath = isset($canonicalPath) ? (string) $canonicalPath : ltrim($currentPage === 'home' ? 'index.php' : basename($_SERVER['PHP_SELF'] ?? 'index.php'), '/');
$canonicalUrl = isset($canonicalUrl) ? (string) $canonicalUrl : production_url($canonicalPath);
$robotsMeta = isset($pageRobots) ? (string) $pageRobots : 'index, follow';
$siteTitle = $pageTitle === $siteConfig['site_name'] || str_contains($pageTitle, '|') ? $pageTitle : $pageTitle . ' | ' . $siteConfig['site_name'];
$organizationAddress = preg_replace('/^Office:\s*/', '', (string) ($contactInformation['office'] ?? 'Lologo 2, North of Freedom Bridge, Juba, South Sudan'));
$structuredData = array(
    array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'SSVP South Sudan',
        'alternateName' => 'Society of St. Vincent de Paul South Sudan',
        'url' => production_url('')
    ),
    array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteConfig['organization_name'] ?? 'The Society of St. Vincent de Paul (SSVP) - South Sudan',
        'alternateName' => $siteConfig['organization_alternate_name'] ?? 'SSVP South Sudan',
        'url' => production_url(''),
        'logo' => production_asset_url($siteConfig['logo']),
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => $organizationAddress,
            'addressLocality' => 'Juba',
            'addressCountry' => 'SS'
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => $contactInformation['office_coordinates']['latitude'] ?? '4.8112763',
            'longitude' => $contactInformation['office_coordinates']['longitude'] ?? '31.5991651'
        )
    )
);
$styleVersion = (string) (@filemtime(__DIR__ . '/../assets/css/style.css') ?: ($assetVersion ?? '1'));
$responsiveVersion = (string) (@filemtime(__DIR__ . '/../assets/css/responsive.css') ?: ($assetVersion ?? '1'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($pageDescription); ?>">
    <meta name="robots" content="<?php echo e($robotsMeta); ?>">
    <title><?php echo e($siteTitle); ?></title>
    <meta property="og:title" content="<?php echo e($siteTitle); ?>">
    <meta property="og:description" content="<?php echo e($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo e(production_asset_url('assets/images/logo/ssvdp-logo-cutout.png')); ?>">
    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">
    <script type="application/ld+json"><?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
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
