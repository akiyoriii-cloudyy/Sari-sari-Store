<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$cashier_name = $_SESSION['username'] ?? 'Unknown Cashier';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sari-Sari Store POS - Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <div class="logo">
                    <h2>Sari-Sari POS</h2>
                </div>
                <ul class="menu">
                    <li class="active" data-link="viewItem_content.php">
                        <i class="fas fa-boxes"></i>
                        <span>VIEW ITEMS</span>
                    </li>
                    <li data-link="POS.php">
                        <i class="fas fa-cash-register"></i>
                        <span>POS</span>
                    </li>
                    <li data-link="payment.php">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>PAYMENT</span>
                    </li>
                    <li data-link="Transaction.php">
                        <i class="fas fa-receipt"></i>
                        <span>TRANSACTIONS</span>
                    </li>
                    <li data-link="ManageUsers.php">
                        <i class="fas fa-users"></i>
                        <span>MANAGE USERS</span>
                    </li>
                    <li data-link="admin_monitoring.php">
                        <i class="fas fa-chart-line"></i>
                        <span>SYSTEM MONITORING</span>
                    </li>

                    <!-- ✅ New Chat menu -->
                    <li data-link="chat.php">
                        <i class="fas fa-comments"></i>
                        <span>CHAT</span>
                    </li>
                </ul>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-name"><?= htmlspecialchars($cashier_name) ?></div>
                <form class="logout-form" action="logout.php" method="POST">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="datetime">
                    <div class="date" id="current-date"></div>
                    <div class="time" id="current-time"></div>
                </div>
                <div class="order-info">
                    <div class="cashier-name">
                        Cashier: <span><?= htmlspecialchars($cashier_name) ?></span>
                    </div>
                </div>
            </div>

            <!-- Load dynamic section here -->
            <iframe id="content-frame" src="viewItem_content.php"></iframe>
        </div>
    </div>

    <script>
        const menuItems = document.querySelectorAll('.menu li');
        const contentFrame = document.getElementById('content-frame');

        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                menuItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                contentFrame.src = item.getAttribute('data-link');
            });
        });

        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            
            document.getElementById("current-date").textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById("current-time").textContent = now.toLocaleTimeString('en-US', timeOptions);
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>
