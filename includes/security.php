<?php

declare(strict_types=1);

function ssvdp_is_production(): bool
{
    $env = strtolower((string) (getenv('SSVDP_ENV') ?: getenv('APP_ENV') ?: ''));
    return in_array($env, array('production', 'prod'), true)
        || getenv('SSVDP_PRODUCTION') === '1';
}

function ssvdp_is_https_request(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function ssvdp_is_local_request(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    return $host === ''
        || str_starts_with($host, 'localhost')
        || str_starts_with($host, '127.0.0.1')
        || str_starts_with($host, '[::1]');
}

function ssvdp_configure_error_handling(): void
{
    ini_set('log_errors', '1');
    ini_set('display_errors', ssvdp_is_production() ? '0' : (getenv('SSVDP_DISPLAY_ERRORS') === '1' ? '1' : '0'));
    ini_set('display_startup_errors', '0');
}

function ssvdp_security_headers(bool $admin = false): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

    $style = "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net";
    $script = "'self' 'unsafe-inline' https://cdn.jsdelivr.net";
    $font = "'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:";
    $image = "'self' data: blob:";
    $frame = "'self' https://www.google.com https://maps.google.com";
    $upgrade = ssvdp_is_https_request() && !ssvdp_is_local_request() ? '; upgrade-insecure-requests' : '';
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; img-src {$image}; font-src {$font}; style-src {$style}; script-src {$script}; connect-src 'self'; frame-src {$frame}; form-action 'self'{$upgrade}");

    if ($admin) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

function ssvdp_require_https_in_production(): void
{
    $forceHttps = getenv('SSVDP_FORCE_HTTPS') === '1' || ssvdp_is_production();
    if (!$forceHttps || ssvdp_is_local_request() || ssvdp_is_https_request() || headers_sent()) {
        return;
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($host !== '') {
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

function ssvdp_start_secure_session(string $name = ''): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if ($name !== '') {
        session_name($name);
    }

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

    session_set_cookie_params(array(
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => ssvdp_is_https_request(),
    ));
    session_start();
}

function ssvdp_rate_limit_key(string $scope, string $subject = ''): string
{
    $ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    return hash('sha256', $scope . '|' . $ip . '|' . strtolower($subject));
}

function ssvdp_session_rate_limited(string $scope, int $limit, int $windowSeconds, string $subject = ''): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }

    $key = 'rate_' . ssvdp_rate_limit_key($scope, $subject);
    $now = time();
    $attempts = array_values(array_filter($_SESSION[$key] ?? array(), static function ($timestamp) use ($now, $windowSeconds): bool {
        return is_int($timestamp) && ($now - $timestamp) < $windowSeconds;
    }));
    $attempts[] = $now;
    $_SESSION[$key] = $attempts;

    return count($attempts) > $limit;
}
