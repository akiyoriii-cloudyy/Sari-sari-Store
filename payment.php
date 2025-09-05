<?php
session_start();

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sari_sari_store", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
}

// Initialize total
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}

// Handle the payment process
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    $cash = floatval($_POST['cash']);
    $change = $cash - $total;

    // Check if sufficient cash is provided
    if ($cash < $total) {
        $error = "Insufficient cash!";
    } else {
        try {
            // Begin database transaction
            $pdo->beginTransaction();

            // Process each item in the cart
            foreach ($_SESSION['cart'] as $item) {
                // Check stock availability
                $check = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
                $check->execute([$item['id']]);
                $stock = $check->fetchColumn();

                if ($stock < $item['quantity']) {
                    throw new Exception("Not enough stock for " . $item['name']);
                }

                // Update product stock
                $update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update->execute([$item['quantity'], $item['id']]);
            }

            // Generate receipt number
            $receipt_no = uniqid("RECEIPT-");

            // Insert transaction record
            $pdo->prepare("INSERT INTO transactions (receipt_number, total_amount, cash_received, change_given) VALUES (?, ?, ?, ?)")
                ->execute([$receipt_no, $total, $cash, $change]);
            $transaction_id = $pdo->lastInsertId();

            // Insert each item into transaction_items table
            foreach ($_SESSION['cart'] as $item) {
                $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_name, quantity, price) VALUES (?, ?, ?, ?)")
                    ->execute([$transaction_id, $item['name'], $item['quantity'], $item['price']]);
            }

            // Clear the cart and save receipt
            $_SESSION['last_receipt'] = $receipt_no;
            $_SESSION['cart'] = [];

            // Commit the transaction
            $pdo->commit();

            // Redirect to generate receipt page
            header("Location: generate_receipt.php");
            exit;
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f8;
        }
        h2 {
            text-align: center;
            color: #34495e;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px 15px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #ecf0f1;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        input[type="number"] {
            padding: 8px;
            width: 150px;
            margin-right: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1em;
            border-radius: 5px;
        }
        button:hover {
            background-color: #27ae60;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Payment</h2>

<?php if (!empty($_SESSION['cart'])): ?>
    <table>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach ($_SESSION['cart'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="total">Total</td>
            <td class="total">₱<?= number_format($total, 2) ?></td>
        </tr>
    </table>

    <form method="POST">
        <label for="cash">Cash Received (₱):</label>
        <input type="number" id="cash" name="cash" step="0.01" required>
        <button type="submit" name="pay">Pay</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

<?php else: ?>
    <p style="text-align:center;">No items in cart.</p>
<?php endif; ?>

</body>
</html>
