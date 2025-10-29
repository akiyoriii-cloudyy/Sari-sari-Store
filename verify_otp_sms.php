<?php
require_once __DIR__ . '/connect.php';

$message = '';
if (!isset($_SESSION['reset_phone'])) {
    header('Location: forgot_password_sms.php');
    exit();
}
$phone = $_SESSION['reset_phone'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

    if ($otp === '') {
        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Please enter the OTP.</p>";
    } else {
        $now = time();

        // Fetch current reset row
        $stmt = $conn->prepare("SELECT id, user_id, attempts, expires FROM sms_password_resets WHERE phone = ? AND otp_code = ? LIMIT 1");
        $stmt->bind_param('ss', $phone, $otp);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Invalid OTP.</p>";
        } else {
            if ((int)$row['attempts'] >= 5) {
                $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Too many attempts. Please request a new OTP.</p>";
            } elseif ((int)$row['expires'] < $now) {
                $message = "<p style='color:#ff6b6b;text-align:center;'>❌ OTP expired. Please request a new OTP.</p>";
            } else {
                // Mark one more attempt only if invalid, since this is valid we continue
                $_SESSION['otp_verified'] = 1;
                $_SESSION['reset_user_id'] = (int)$row['user_id'];
                header('Location: reset_password_sms.php');
                exit();
            }

            // Increment attempts on failure cases
            if (!isset($_SESSION['otp_verified'])) {
                $stmtUp = $conn->prepare("UPDATE sms_password_resets SET attempts = attempts + 1 WHERE id = ?");
                $stmtUp->bind_param('i', $row['id']);
                $stmtUp->execute();
                $stmtUp->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP (SMS) | Sari-Sari Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<?= file_get_contents("style.css"); ?>
</style>
</head>
<body>
<div class="wrapper">
    <h2>Verify OTP</h2>
    <p style="text-align:center;">We sent an OTP to <?= htmlspecialchars($phone) ?>.</p>
    <?= $message; ?>
    <form method="POST">
        <div class="input-field">
            <input type="text" name="otp" placeholder=" " pattern="[0-9]{6}" maxlength="6" required>
            <label>Enter 6-digit OTP</label>
        </div>
        <button type="submit">Verify</button>
    </form>
    <div class="register">
        <p><a href="forgot_password_sms.php">Resend OTP</a> | <a href="index.php">Back to Login</a></p>
    </div>
</div>
</body>
</html>
