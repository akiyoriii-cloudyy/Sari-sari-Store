<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // No password by default
$dbname = "sari_sari_store"; // Make sure this matches your database name

// Create a database connection using MySQLi (Object-Oriented)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and report errors
if ($conn->connect_error) {
    exit("Database connection failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8 (prevents special character issues)
$conn->set_charset("utf8");

// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Securely update last_active for logged-in user
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("UPDATE users SET last_active = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
    }
}
?>
