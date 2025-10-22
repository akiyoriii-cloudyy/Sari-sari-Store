<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date("U") + 1800; // 30 minutes expiry

        // Remove existing reset requests for this email
        $conn->query("DELETE FROM password_resets WHERE email = '$email'");
        $conn->query("INSERT INTO password_resets (email, token, expires) VALUES ('$email', '$token', '$expires')");

        // Update the link to your actual localhost path
        $resetLink = "http://localhost/sari-sari-store/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'marcobatiller07@gmail.com'; 
            $mail->Password   = 'npweofzfpueykpuk'; // app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('marcobatiller07@gmail.com', 'SARI-SARI STORE');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "
                <p>We received a password reset request for your account.</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 30 minutes.</p>
            ";

            $mail->send();
            $message = "<p style='color:#00ff88;text-align:center;'>✅ A password reset link has been sent to your email.</p>";
        } catch (Exception $e) {
            $message = "<p style='color:#ff6b6b;text-align:center;'>❌ Mailer Error: {$mail->ErrorInfo}</p>";
        }
    } else {
        $message = "<p style='color:#ff6b6b;text-align:center;'>❌ No user found with this email.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | Sari-Sari Store</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<?= file_get_contents("style.css"); ?>
</style>
</head>
<body>

<div class="wrapper">
    <h2>Forgot Password</h2>
    <?= $message; ?>
    <form method="POST">
        <div class="input-field">
            <input type="email" name="email" id="email" placeholder=" " required>
            <label for="email">Enter your email</label>
        </div>
        <button type="submit">Send Reset Link</button>
    </form>
    <div class="register">
        <p>Remembered your password? <a href="index.php">Login here</a></p>
    </div>
</div>

</body>
</html>