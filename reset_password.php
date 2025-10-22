<?php
include 'connect.php';
$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Secure prepared statement for token check
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires > ?");
    $now = time();
    $stmt->bind_param("si", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Invalid or expired token.</p>";
    } else {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $newPass = $_POST['password'];
            $confirmPass = $_POST['confirm_password'];

            if ($newPass !== $confirmPass) {
                $message = "<p style='color:#ff6b6b;text-align:center;'>⚠️ Passwords do not match.</p>";
            } else {
                // Get old hashed password using prepared statement
                $stmtUser = $conn->prepare("SELECT password FROM users WHERE email = ?");
                $stmtUser->bind_param("s", $email);
                $stmtUser->execute();
                $userResult = $stmtUser->get_result();

                if ($userResult->num_rows > 0) {
                    $user = $userResult->fetch_assoc();
                    $oldHashedPassword = $user['password'];

                    if (password_verify($newPass, $oldHashedPassword)) {
                        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ You cannot reuse your old password.</p>";
                    } else {
                        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);

                        // Update new password securely
                        $stmtUpdate = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                        $stmtUpdate->bind_param("ss", $hashedPass, $email);
                        $stmtUpdate->execute();

                        // Delete the token after successful reset
                        $stmtDelete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                        $stmtDelete->bind_param("s", $email);
                        $stmtDelete->execute();

                        // Redirect with success message
                        header("Location: index.php?reset_success=1");
                        exit();
                    }
                }
            }
        }
    }
} else {
    $message = "<p style='color:#ff6b6b;text-align:center;'>❌ No token provided.</p>";
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | Sari-Sari Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<?= file_get_contents("style.css"); ?>
</style>
</head>
<body>

<div class="wrapper">
    <h2>Reset Password</h2>
    <?= $message; ?>
    <form method="POST">
        <div class="input-field">
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">New Password</label>
        </div>
        <div class="input-field">
            <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required>
            <label for="confirm_password">Confirm Password</label>
        </div>
        <button type="submit">Update Password</button>
    </form>
    <div class="register">
        <p>Go back to <a href="index.php">Login</a></p>
    </div>
</div>

</body>
</html>
