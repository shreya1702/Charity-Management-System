<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_NAME', 'charity_db');

// Base URL — auto-detected, works in any subfolder
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/admin/donor'));

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("<div style='font-family:Arial;padding:30px;color:red;'>
        <h3>Database Connection Failed</h3>
        <p>" . $conn->connect_error . "</p>
        <p>Please update <code>includes/config.php</code> with your MySQL credentials and make sure the database is set up using <code>sql/schema.sql</code>.</p>
    </div>");
}

$conn->set_charset("utf8mb4");

function base($path = '') {
    $base = '/charity-management-system';
    return $base . '/' . ltrim($path, '/');
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function isDonorLoggedIn() {
    return isset($_SESSION['donor_id']);
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . base('admin/login.php'));
        exit();
    }
}

function requireDonor() {
    if (!isDonorLoggedIn()) {
        header("Location: " . base('donor/login.php'));
        exit();
    }
}

function sanitize($conn, $val) {
    return $conn->real_escape_string(trim($val));
}

function formatCurrency($amount) {
    return '₹' . number_format($amount, 2);
}
?>
