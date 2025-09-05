<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=sari_sari_store", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Add to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $item_exists = false;
        foreach ($_SESSION['cart'] ?? [] as &$item) {
            if ($item['id'] == $product_id) {
                $new_quantity = $item['quantity'] + $quantity;
                if ($new_quantity > $product['stock']) {
                    $_SESSION['error'] = "Not enough stock for " . $product['name'];
                } else {
                    $item['quantity'] = $new_quantity;
                }
                $item_exists = true;
                break;
            }
        }

        if (!$item_exists) {
            if ($quantity > $product['stock']) {
                $_SESSION['error'] = "Not enough stock for " . $product['name'];
            } else {
                $_SESSION['cart'][] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                ];
            }
        }
    }
}

// Remove from cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_item'])) {
    $item_id = $_POST['item_id_to_remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $item_id) {
            unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$products = $pdo->query("SELECT * FROM products WHERE stock > 0")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>POS System</title>
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
            margin: 0;
            padding: 20px;
        }

        .main-layout {
            display: flex;
            gap: 25px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .container {
            flex: 2;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }

        .sidebar {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            position: sticky;
            top: 20px;
        }

        h2 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
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

        h3 {
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        form {
            margin-top: 25px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
        }

        .form-group.autocomplete {
            flex: 3;
        }

        .form-group.quantity {
            flex: 0.5;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 500;
            font-size: 0.95rem;
        }

        input[type=text], 
        input[type=number] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            color: #1e293b;
        }

        input[type=text]:focus,
        input[type=number]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        button {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 25px;
        }

        th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 16px;
            font-size: 0.95rem;
        }

        th:first-child {
            border-top-left-radius: 12px;
        }

        th:last-child {
            border-top-right-radius: 12px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        tr:hover {
            background: rgba(52, 152, 219, 0.05);
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
            color: #c0392b;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .autocomplete {
            position: relative;
            display: block;
            flex: 2;
        }

        .autocomplete-items {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 250px;
            overflow-y: auto;
        }

        .autocomplete-items div {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .autocomplete-items div:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .product-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .product-card strong {
            color: #1e293b;
            font-weight: 600;
        }

        .product-card p {
            color: #64748b;
            margin: 5px 0;
        }

        .product-price {
            color: #2ecc71;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .stock-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(52, 152, 219, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(52, 152, 219, 0.5);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                position: static;
                max-height: none;
            }

            form {
                flex-direction: column;
                gap: 15px;
            }

            .form-group,
            .form-group.autocomplete,
            .form-group.quantity {
                width: 100%;
                flex: none;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="main-layout">
    <div class="container">
        <h2><i class="fas fa-shopping-cart"></i> POS System</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Product Search and Add -->
        <form method="POST" autocomplete="off">
            <div class="form-group autocomplete">
                <label for="product_search">
                    <i class="fas fa-search"></i>
                    Search Product
                </label>
                <input id="product_search" type="text" name="search" placeholder="Type to search...">
                <input type="hidden" name="product_id" id="product_id">
            </div>
            <div class="form-group quantity">
                <label for="quantity">
                    <i class="fas fa-box"></i>
                    Quantity
                </label>
                <input type="number" id="quantity" name="quantity" placeholder="Qty" min="1" required>
            </div>
            <button type="submit" name="add_to_cart" class="btn">
                <i class="fas fa-plus"></i>
                Add to Cart
            </button>
        </form>

        <!-- Cart Table -->
        <?php if (!empty($_SESSION['cart'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $key => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td>₱<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="item_id_to_remove" value="<?= $item['id'] ?>">
                                    <button type="submit" name="cancel_item" class="btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 600;">Total:</td>
                        <td colspan="2" style="font-weight: 600; color: #2ecc71;">₱<?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="sidebar">
        <h3><i class="fas fa-box"></i> Available Products</h3>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <strong><?= htmlspecialchars($product['name']) ?></strong>
                <p class="product-price">₱<?= number_format($product['price'], 2) ?></p>
                <p>
                    <span class="stock-badge">
                        <i class="fas fa-cubes"></i>
                        Stock: <?= $product['stock'] ?>
                    </span>
                </p>
                <?php if (!empty($product['description'])): ?>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const products = <?= json_encode($products) ?>;
    
    function autocomplete(inp) {
        let currentFocus;
        
        inp.addEventListener("input", function(e) {
            let a, b, i, val = this.value;
            closeAllLists();
            if (!val) { return false; }
            currentFocus = -1;
            
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(a);
            
            for (i = 0; i < products.length; i++) {
                if (products[i].name.toLowerCase().includes(val.toLowerCase())) {
                    b = document.createElement("DIV");
                    let productText = `${products[i].name} - ₱${products[i].price} (Stock: ${products[i].stock})`;
                    b.innerHTML = productText;
                    b.innerHTML += `<input type='hidden' value='${products[i].id}'>`;
                    b.addEventListener("click", function(e) {
                        document.getElementById("product_id").value = this.getElementsByTagName("input")[0].value;
                        inp.value = this.textContent;
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        });
        
        inp.addEventListener("keydown", function(e) {
            let x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                currentFocus++;
                addActive(x);
            } else if (e.keyCode == 38) {
                currentFocus--;
                addActive(x);
            } else if (e.keyCode == 13) {
                e.preventDefault();
                if (currentFocus > -1) {
                    if (x) x[currentFocus].click();
                }
            }
        });
        
        function addActive(x) {
            if (!x) return false;
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("autocomplete-active");
        }
        
        function removeActive(x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }
        
        function closeAllLists(elmnt) {
            let x = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
        
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
    }
    
    autocomplete(document.getElementById("product_search"));
</script>

</body>
</html>
