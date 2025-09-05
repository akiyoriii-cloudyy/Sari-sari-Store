<?php
include 'connect.php'; // Updated to use connect.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "A password reset link has been sent to your email.";
    } else {
        echo "No user found with this email.";
    }
}
?>
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <button type="submit">Reset Password</button>
</form>
