<?php
require_once 'connect.php';

// ✅ Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$database_status = "connected";
$php_version = phpversion();
$last_updated = date('Y-m-d H:i:s');

$api_endpoints = [
    '/API/admin.php' => 'ok',
    '/API/categories.php' => 'ok',
    '/API/customers.php' => 'ok',
    '/API/login.php' => 'ok',
    '/API/orders-create.php' => 'ok',
    '/API/orders.php' => 'ok',
    '/API/products.php' => 'ok',
    '/API/register.php' => 'ok'
];

// Get Log Files Status (simulated)
$log_files = [
    [
        'file' => '/logs/api_errors.log',
        'status' => 'Exists',
        'size' => '0.38 KB',
        'last_modified' => date('Y-m-d H:i:s', strtotime('-1 hour'))
    ],
    [
        'file' => '/logs/errors.log',
        'status' => 'Exists',
        'size' => '0.25 KB',
        'last_modified' => date('Y-m-d H:i:s', strtotime('-2 hours'))
    ],
    [
        'file' => '/logs/db_error_log.txt',
        'status' => 'Exists',
        'size' => '0.55 KB',
        'last_modified' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
    ]
];

// Get Recent Activity Logs (simulated)
$recent_activities = [
    [
        'time' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
        'admin' => $_SESSION['username'] ?? 'Unknown',
        'action' => 'Order Created',
        'endpoint' => 'orders-create.php'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Monitoring Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(0, 20, 40, 0.05), rgba(0, 10, 30, 0.1));
            padding: 20px;
            color: #1e293b;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #1e293b;
            position: relative;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 4px;
        }

        .monitoring-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .monitoring-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .monitoring-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.95rem;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-ok {
            color: #27ae60;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .status-error {
            color: #e74c3c;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 10px;
            border-radius: 12px;
            position: relative;
        }

        .log-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 600px;
        }

        .log-table th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 12px;
            font-size: 0.9rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .log-table th:first-child {
            border-top-left-radius: 12px;
        }

        .log-table th:last-child {
            border-top-right-radius: 12px;
        }

        .log-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
        }

        .log-table tr:hover td {
            background: rgba(52, 152, 219, 0.05);
        }

        .log-table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-success {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }

        .badge-error {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }

        .file-size {
            color: #64748b;
            font-size: 0.85rem;
        }

        .timestamp {
            color: #64748b;
            font-size: 0.85rem;
        }

        @media (max-width: 1200px) {
            .monitoring-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .monitoring-card {
                padding: 15px;
            }

            .table-wrapper {
                margin: 0 -15px;
                width: calc(100% + 30px);
                border-radius: 0;
            }

            .log-table {
                min-width: 500px;
            }

            .log-table th,
            .log-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-chart-line"></i>
            System Monitoring Dashboard
        </h1>
        
        <div class="monitoring-grid">
            <!-- System Status -->
            <div class="monitoring-card">
                <h2>
                    <i class="fas fa-desktop"></i>
                    System Status
                </h2>
                <div class="status-item">
                    <span>Database:</span>
                    <span class="status-ok">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $database_status; ?>
                    </span>
                </div>
                <div class="status-item">
                    <span>PHP Version:</span>
                    <span class="badge badge-success"><?php echo $php_version; ?></span>
                </div>
                <div class="status-item">
                    <span>Last Updated:</span>
                    <span class="timestamp"><?php echo $last_updated; ?></span>
                </div>
            </div>

            <!-- API Status -->
            <div class="monitoring-card">
                <h2>
                    <i class="fas fa-plug"></i>
                    API Status
                </h2>
                <?php foreach ($api_endpoints as $endpoint => $status): ?>
                <div class="status-item">
                    <span><?php echo $endpoint; ?></span>
                    <span class="status-ok">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $status; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Log Files Status -->
            <div class="monitoring-card">
                <h2>
                    <i class="fas fa-file-alt"></i>
                    Log Files Status
                </h2>
                <div class="table-wrapper">
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Status</th>
                                <th>Size</th>
                                <th>Last Modified</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($log_files as $log): ?>
                            <tr>
                                <td><?php echo $log['file']; ?></td>
                                <td><span class="badge badge-success"><?php echo $log['status']; ?></span></td>
                                <td><span class="file-size"><?php echo $log['size']; ?></span></td>
                                <td><span class="timestamp"><?php echo $log['last_modified']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity Logs -->
            <div class="monitoring-card">
                <h2>
                    <i class="fas fa-history"></i>
                    Recent Activity Logs
                </h2>
                <div class="table-wrapper">
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Endpoint</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_activities as $activity): ?>
                            <tr>
                                <td><span class="timestamp"><?php echo $activity['time']; ?></span></td>
                                <td><?php echo $activity['admin']; ?></td>
                                <td><span class="badge badge-success"><?php echo $activity['action']; ?></span></td>
                                <td><?php echo $activity['endpoint']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh the page every 5 minutes
        setTimeout(() => {
            window.location.reload();
        }, 5 * 60 * 1000);
    </script>
</body>
</html>
