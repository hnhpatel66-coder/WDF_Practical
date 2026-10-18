
<?php

// Show errors during local development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Database connection details
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "PRACTICAL_10";

// Connect to MySQL
try {
    $conn = new mysqli(
        $host,
        $db_user,
        $db_pass,
        $db_name
    );

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("Database connection failed. Check XAMPP and database settings.");
}


// PHP SESSION CONFIGURATION

// Enable strict session mode
ini_set('session.use_strict_mode', '1');

// Use cookies for session IDs
ini_set('session.use_only_cookies', '1');

// Configure session storage path
$sessionPath = "C:/xampp/tmp";

if (is_dir($sessionPath) && is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

// Check if HTTPS is enabled
$isHttps = !empty($_SERVER['HTTPS'])
    && $_SERVER['HTTPS'] !== 'off';

// Configure session cookies
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start PHP session
if (session_status() === PHP_SESSION_NONE) {
    if (!session_start()) {
        die("Unable to start PHP session.");
    }
}

?>
