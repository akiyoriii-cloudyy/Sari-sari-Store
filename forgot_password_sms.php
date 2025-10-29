<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/sms_service.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    // Normalize PH: 09XXXXXXXXX or +639XXXXXXXXX -> +639XXXXXXXXX
    if (preg_match('/^09\d{9}$/', $phone)) {
        $phone = '+63' . substr($phone, 1);
    } elseif (preg_match('/^\+?63\d{10}$/', $phone)) {
        $phone = '+' . ltrim($phone, '+');
    }

    if (!preg_match('/^\+639\d{9}$/', $phone)) {
        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Please enter a valid phone number.</p>";
    } else {
        // Find user by phone
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE phone = ? LIMIT 1");
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $message = "<p style='color:#ff6b6b;text-align:center;'>❌ No account found with that phone number.</p>";
        } else {
            // Generate OTP
            $otp = strval(random_int(100000, 999999));
            $expires = time() + 600; // 10 minutes

            // Ensure table exists (safe NOOP if already exists)
            $conn->query("CREATE TABLE IF NOT EXISTS sms_password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                phone VARCHAR(32) NOT NULL,
                otp_code VARCHAR(10) NOT NULL,
                expires INT NOT NULL,
                attempts INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(phone), INDEX(user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Remove existing for this phone
            $stmtDel = $conn->prepare("DELETE FROM sms_password_resets WHERE phone = ?");
            $stmtDel->bind_param('s', $phone);
            $stmtDel->execute();
            $stmtDel->close();

            // Insert new OTP
            $stmtIns = $conn->prepare("INSERT INTO sms_password_resets (user_id, phone, otp_code, expires) VALUES (?, ?, ?, ?)");
            $stmtIns->bind_param('issi', $user['id'], $phone, $otp, $expires);
            $stmtIns->execute();
            $stmtIns->close();

            // Send SMS
            $smsText = "Your Sari-Sari Store OTP is $otp. It expires in 10 minutes.";
            [$ok, $resp] = send_sms($phone, $smsText);

            if ($ok) {
                $_SESSION['reset_phone'] = $phone;
                header('Location: verify_otp_sms.php');
                exit();
            } else {
                $safe = htmlspecialchars($resp, ENT_QUOTES, 'UTF-8');
                if (defined('SMS_DEV_SHOW_OTP') && SMS_DEV_SHOW_OTP) {
                    $_SESSION['reset_phone'] = $phone;
                    $otpSafe = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
                    $message = "<p style='color:#00ff88;text-align:center;'>🧪 Dev mode: SMS blocked. Use OTP: <strong>$otpSafe</strong>. <a href='verify_otp_sms.php'>Continue</a></p>"
                             . "<p style='color:#ffcc00;text-align:center;'>Provider response: $safe</p>";
                } else {
                    $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Failed to send SMS: $safe</p>";
                }
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
<title>Forgot Password via SMS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<?= file_get_contents("style.css"); ?>
</style>
</head>
<body>
<div class="wrapper">
    <h2>Forgot Password (SMS)</h2>
    <?= $message; ?>
    <form method="POST">
        <div class="input-field">
            <input type="tel" name="phone" placeholder=" " required>
            <label>Enter your phone number</label>
        </div>
        <button type="submit">Send OTP</button>
    </form>
    <div class="register">
        <p>Prefer email? <a href="forgot_password.php">Reset via Email</a> | <a href="index.php">Back to Login</a></p>
    </div>
</div>
</body>
</html>
