<?php
session_start();
include 'connect.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Normalize phone (PH): allow 09XXXXXXXXX or +639XXXXXXXXX, store as +639XXXXXXXXX
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    if (preg_match('/^09\d{9}$/', $phone)) {
        $phone = '+63' . substr($phone, 1);
    } elseif (preg_match('/^\+?63\d{10}$/', $phone)) {
        $phone = '+' . ltrim($phone, '+');
    }

    // Check if the email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    // Check if the phone already exists
    $check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $check_phone->bind_param("s", $phone);
    $check_phone->execute();
    $check_phone->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Error: Email already exists!'); window.history.back();</script>";
    } elseif ($check_phone->num_rows > 0) {
        echo "<script>alert('Error: Phone number already exists!'); window.history.back();</script>";
    } else {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.history.back();</script>";
        }

        $stmt->close();
    }

    $check_email->close();
    if (isset($check_phone)) { $check_phone->close(); }
}

$conn->close();
?>
