<?php
declare(strict_types=1);

/*
 * Example database configuration for deployment.
 * Copy this file to config/database.php and replace the placeholder values
 * with credentials from the target hosting environment.
 */

if (!defined('SSVDP_APP')) {
    define('SSVDP_APP', true);
}

$databaseConfig = [
    'host' => 'DB_HOST_HERE',
    'database' => 'DB_NAME_HERE',
    'username' => 'DB_USER_HERE',
    'password' => 'DB_PASSWORD_HERE',
    'charset' => 'utf8mb4',
];

function get_database_connection(): PDO
{
    global $databaseConfig;

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $databaseConfig['host'],
        $databaseConfig['database'],
        $databaseConfig['charset']
    );

    return new PDO($dsn, $databaseConfig['username'], $databaseConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
