<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=sari_sari_store", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$receipt_number = $_SESSION['last_receipt'] ?? null;

if (!$receipt_number) {
    echo "No recent receipt to display.";
    exit;
}

// Fetch transaction
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE receipt_number = ?");
$stmt->execute([$receipt_number]);
$transaction = $stmt->fetch();

if (!$transaction) {
    echo "Transaction not found.";
    exit;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM transaction_items WHERE transaction_id = ?");
$stmt->execute([$transaction['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Clear last receipt session
unset($_SESSION['last_receipt']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .receipt { width: 400px; margin: auto; border: 1px solid #000; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border-bottom: 1px solid #ccc; padding: 8px; text-align: left; }
        h2 { margin-bottom: 5px; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #555; }
        .print-btn {
            margin-top: 20px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body onload="window.print()">

<div class="receipt">
    <h2>Sari-Sari Store</h2>
    <p><strong>Receipt #: </strong><?= htmlspecialchars($transaction['receipt_number']) ?></p>
    <p><strong>Date: </strong><?= date("F j, Y, g:i a", strtotime($transaction['created_at'])) ?></p>

    <table>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Total: </strong>₱<?= number_format($transaction['total_amount'], 2) ?></p>
    <p><strong>Cash: </strong>₱<?= number_format($transaction['cash_received'], 2) ?></p>
    <p><strong>Change: </strong>₱<?= number_format($transaction['change_given'], 2) ?></p>

    <div class="footer">Thank you for shopping!</div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>
</div>

</body>
</html>
