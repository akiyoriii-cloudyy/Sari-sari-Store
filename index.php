<?php
session_start();
include 'connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit();
}

$logoutMessage = '';
if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $logoutMessage = "You have been successfully logged out.";
}

$resetSuccess = '';
if (isset($_GET['reset_success']) && $_GET['reset_success'] == '1') {
    $resetSuccess = "✅ Your password has been successfully updated! You can now log in.";
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        header("Location: homepage.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login Form</title>
    <link rel="stylesheet" href="login.css" />
    <style>
        .message { color: green; text-align: center; margin-bottom: 15px; transition: opacity 0.5s ease; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
        .hidden { display: none; }
    </style>
</head>
<body>

<!-- LOGIN SECTION -->

<div class="wrapper" id="loginSection">
    <form action="index.php" method="POST">
        <h2>Welcome to Quadro FFA</h2>

```
    <?php if ($resetSuccess): ?><div class="message" id="resetMessage"><?= htmlspecialchars($resetSuccess) ?></div><?php endif; ?>
    <?php if ($logoutMessage): ?><div class="message"><?= htmlspecialchars($logoutMessage) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="input-field">
            <input type="email" name="email" required />
            <label>Enter your Email</label>
        </div>
        <div class="input-field">
            <input type="password" name="password" required />
            <label>Enter your Password</label>
        </div>
        <button type="submit">Log In</button>
        <div class="register">
            <p>Don't have an account? <a href="#" id="registerLink">Register</a> | <a href="forgot_password.php">Forgot via Email</a> | <a href="forgot_password_sms.php">Forgot via SMS</a></p>
        </div>
    </form>
```

</div>

<!-- SIGN UP SECTION -->

<div id="signupSection" class="wrapper hidden">
    <h2>Sign Up</h2>
    <form id="signupForm" action="register.php" method="POST">
        <div class="input-field">
            <input type="text" name="name" required />
            <label>Enter your Name</label>
        </div>
        <div class="input-field">
            <input type="email" name="email" required />
            <label>Enter your Email</label>
        </div>
        <div class="input-field">
            <input type="tel" name="phone" pattern="^(\+?63|0)9\d{9}$" required />
            <label>Enter your Phone Number</label>
        </div>
        <div class="input-field">
            <input type="password" id="password" name="password" required />
            <label>Create a Password</label>
        </div>
        <div class="input-field">
            <input type="password" id="confirm_password" name="confirm_password" required />
            <label>Confirm Password</label>
        </div>
        <div id="confirmError" style="color:red; text-align:center; display:none;">Passwords do not match!</div>
        <button type="submit">Sign Up</button>
        <div class="register">
            <p>Already have an account? <a href="#" id="loginLink">Log In</a></p>
        </div>
    </form>
</div>

<script>
    const loginSection = document.getElementById("loginSection");
    const signupSection = document.getElementById("signupSection");

    document.getElementById("registerLink")?.addEventListener("click", () => {
        loginSection.classList.add("hidden");
        signupSection.classList.remove("hidden");
    });

    document.getElementById("loginLink")?.addEventListener("click", () => {
        loginSection.classList.remove("hidden");
        signupSection.classList.add("hidden");
    });

    // Confirm password check
    const signupForm = document.getElementById('signupForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const confirmError = document.getElementById('confirmError');

    signupForm.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            confirmError.style.display = 'block';
        } else {
            confirmError.style.display = 'none';
        }
    });

    // ✅ Fade out success message after 4 seconds
    const resetMessage = document.getElementById('resetMessage');
    if (resetMessage) {
        setTimeout(() => {
            resetMessage.style.opacity = '0';
        }, 4000);
    }
</script>

</body>
</html>
