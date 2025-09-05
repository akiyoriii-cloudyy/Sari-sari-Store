<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // No password by default
$dbname = "sari_sari_store"; // Ensure this matches your database

// Create a database connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and report errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8 (prevents special character issues)
$conn->set_charset("utf8");

// ✅ Update last_active for logged-in user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $conn->query("UPDATE users SET last_active = NOW() WHERE id = $uid");
}
?>
