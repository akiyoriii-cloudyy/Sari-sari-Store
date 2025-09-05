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
        .message { color: green; text-align: center; margin-bottom: 15px; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="wrapper" id="loginSection">
    <form action="index.php" method="POST">
        <h2>Welcome to Quadro FFA</h2>

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
            <p>Don't have an account? <a href="#" id="registerLink">Register</a> | <a href="#" id="forgotLink">Forgot Password?</a></p>
        </div>
    </form>
</div>

<div id="signupSection" class="wrapper hidden">
    <h2>Sign Up</h2>
    <form action="register.php" method="POST">
        <div class="input-field">
            <input type="text" name="name" required />
            <label>Enter your Name</label>
        </div>
        <div class="input-field">
            <input type="email" name="email" required />
            <label>Enter your Email</label>
        </div>
        <div class="input-field">
            <input type="password" name="password" required />
            <label>Create a Password</label>
        </div>
        <button type="submit">Sign Up</button>
        <div class="register">
            <p>Already have an account? <a href="#" id="loginLink">Log In</a></p>
        </div>
    </form>
</div>

<div id="forgotPasswordSection" class="wrapper hidden">
    <h2>Forgot Password</h2>
    <form action="forgot_password.php" method="POST">
        <div class="input-field">
            <input type="email" name="email" required />
            <label>Enter your Email</label>
        </div>
        <button type="submit">Reset Password</button>
        <div class="register">
            <p>Remember your password? <a href="#" id="backToLogin">Log In</a></p>
        </div>
    </form>
</div>

<script>
    const loginSection = document.getElementById("loginSection");
    const signupSection = document.getElementById("signupSection");
    const forgotSection = document.getElementById("forgotPasswordSection");

    document.getElementById("registerLink")?.addEventListener("click", () => {
        loginSection.classList.add("hidden");
        signupSection.classList.remove("hidden");
        forgotSection.classList.add("hidden");
    });

    document.getElementById("loginLink")?.addEventListener("click", () => {
        loginSection.classList.remove("hidden");
        signupSection.classList.add("hidden");
        forgotSection.classList.add("hidden");
    });

    document.getElementById("forgotLink")?.addEventListener("click", () => {
        loginSection.classList.add("hidden");
        signupSection.classList.add("hidden");
        forgotSection.classList.remove("hidden");
    });

    document.getElementById("backToLogin")?.addEventListener("click", () => {
        loginSection.classList.remove("hidden");
        signupSection.classList.add("hidden");
        forgotSection.classList.add("hidden");
    });
</script>

</body>
</html>
