<?php
session_start();
require_once 'api/config.php';

$database = new Database();
$db = $database->getConnection();

// Get transactions with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

$stmt = $db->prepare("
    SELECT * FROM transactions 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$limit, $offset]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$stmt = $db->query("SELECT COUNT(*) as total FROM transactions");
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Sari-Sari Store</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        h2 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 3px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        .transactions-table th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 16px;
            font-size: 0.95rem;
        }

        .transactions-table th:first-child {
            border-top-left-radius: 12px;
        }

        .transactions-table th:last-child {
            border-top-right-radius: 12px;
        }

        .transactions-table td {
            padding: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        .transactions-table tr:hover {
            background: rgba(52, 152, 219, 0.05);
        }

        .transactions-table tr:last-child td {
            border-bottom: none;
        }

        .amount {
            font-family: 'Poppins', monospace;
            font-weight: 500;
        }

        .positive {
            color: #2ecc71;
        }

        .receipt-number {
            font-family: 'Poppins', monospace;
            color: #64748b;
            font-size: 0.9rem;
        }

        .timestamp {
            color: #64748b;
            font-size: 0.9rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination button {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            background: rgba(52, 152, 219, 0.2);
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .current {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px;
            padding-left: 40px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .search-box input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .date-filter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .date-filter input {
            padding: 12px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: #1e293b;
        }

        .date-filter input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #3498db;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .transactions-table {
                display: block;
                overflow-x: auto;
            }

            .filters {
                flex-direction: column;
            }

            .date-filter {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>
            <i class="fas fa-history"></i>
            Transaction History
        </h2>

        <div class="filters">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchReceipt" placeholder="Search by receipt number...">
            </div>
            <div class="date-filter">
                <input type="date" id="startDate" placeholder="Start Date">
                <span>to</span>
                <input type="date" id="endDate" placeholder="End Date">
            </div>
        </div>

        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>No transactions found</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Total Amount (₱)</th>
                            <th>Cash Received (₱)</th>
                            <th>Change Given (₱)</th>
                            <th>Transaction Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td>
                                    <span class="receipt-number">
                                        <?= htmlspecialchars($transaction['receipt_number']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="amount">
                                        ₱<?= number_format($transaction['total_amount'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="amount">
                                        ₱<?= number_format($transaction['cash_received'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="amount positive">
                                        ₱<?= number_format($transaction['change_given'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="timestamp">
                                        <?= date('Y-m-d h:i:s A', strtotime($transaction['created_at'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <button <?= $page <= 1 ? 'disabled' : '' ?> onclick="changePage(<?= $page - 1 ?>)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <button class="<?= $i === $page ? 'current' : '' ?>" onclick="changePage(<?= $i ?>)">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                    
                    <button <?= $page >= $total_pages ? 'disabled' : '' ?> onclick="changePage(<?= $page + 1 ?>)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function changePage(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.search = urlParams.toString();
        }

        document.getElementById('searchReceipt').addEventListener('input', function(e) {
            // Implement client-side filtering
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.transactions-table tbody tr');
            
            rows.forEach(row => {
                const receiptNumber = row.querySelector('.receipt-number').textContent.toLowerCase();
                row.style.display = receiptNumber.includes(searchTerm) ? '' : 'none';
            });
        });

        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        [startDate, endDate].forEach(input => {
            input.addEventListener('change', function() {
                if (startDate.value && endDate.value) {
                    const start = new Date(startDate.value);
                    const end = new Date(endDate.value);
                    
                    document.querySelectorAll('.transactions-table tbody tr').forEach(row => {
                        const timestamp = new Date(row.querySelector('.timestamp').textContent);
                        row.style.display = (timestamp >= start && timestamp <= end) ? '' : 'none';
                    });
                }
            });
        });
    </script>
</body>
</html> 