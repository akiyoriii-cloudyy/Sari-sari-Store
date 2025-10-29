<?php
require_once __DIR__ . '/connect.php';

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) {
    header('Location: forgot_password_sms.php');
    exit();
}

$message = '';
$userId = (int)$_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPass = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if ($newPass === '' || $confirmPass === '') {
        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Please fill in both password fields.</p>";
    } elseif ($newPass !== $confirmPass) {
        $message = "<p style='color:#ff6b6b;text-align:center;'>⚠️ Passwords do not match.</p>";
    } else {
        // Get old hashed password
        $stmtUser = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmtUser->bind_param('i', $userId);
        $stmtUser->execute();
        $res = $stmtUser->get_result();
        $user = $res->fetch_assoc();
        $stmtUser->close();

        if ($user) {
            $oldHashed = $user['password'];
            if (password_verify($newPass, $oldHashed)) {
                $message = "<p style='color:#ff6b6b;text-align:center;'>❌ You cannot reuse your old password.</p>";
            } else {
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                $stmtUp = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmtUp->bind_param('si', $hashed, $userId);
                $stmtUp->execute();
                $stmtUp->close();

                // Cleanup OTP entries for this user/phone
                if (isset($_SESSION['reset_phone'])) {
                    $phone = $_SESSION['reset_phone'];
                    $stmtDel = $conn->prepare("DELETE FROM sms_password_resets WHERE phone = ?");
                    $stmtDel->bind_param('s', $phone);
                    $stmtDel->execute();
                    $stmtDel->close();
                }

                // Clear session flags
                unset($_SESSION['otp_verified']);
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_phone']);

                header('Location: index.php?reset_success=1');
                exit();
            }
        } else {
            $message = "<p style='color:#ff6b6b;text-align:center;'>❌ User not found.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password (SMS) | Sari-Sari Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<?= file_get_contents("style.css"); ?>
</style>
</head>
<body>
<div class="wrapper">
    <h2>Reset Password (SMS)</h2>
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
        <p>Back to <a href="index.php">Login</a></p>
    </div>
</div>
</body>
</html>
