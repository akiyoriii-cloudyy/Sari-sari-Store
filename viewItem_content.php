<?php
session_start();
// Database connection
$host = 'localhost';
$db = 'sari_sari_store';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new product
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = $_POST['description'] ?? '';

        if (empty($name) || $price <= 0 || $stock < 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            exit;
        }

        $sql = "INSERT INTO products (name, price, stock, description) 
                VALUES (:name, :price, :stock, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':stock' => $stock,
            ':description' => $description
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
        exit;
    }
    
    // Update product stock
    if (isset($_POST['update_stock'])) {
        // Debug: Log received data
        error_log("Received update request with data: " . print_r($_POST, true));
        
        $product_id = $_POST['product_id'];
        $new_stock = intval($_POST['new_stock']);
        $new_name = $_POST['new_name'];
        $new_price = floatval($_POST['new_price']);
        $new_description = $_POST['new_description'];
        
        if ($new_stock < 0) {
            echo json_encode(['status' => 'error', 'message' => 'Stock cannot be negative']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE products SET stock = :stock, name = :name, price = :price, description = :description WHERE id = :id");
            $params = [
                ':stock' => $new_stock,
                ':name' => $new_name,
                ':price' => $new_price,
                ':description' => $new_description,
                ':id' => $product_id
            ];
            
            // Debug: Log SQL parameters
            error_log("Executing update with parameters: " . print_r($params, true));
            
            $stmt->execute($params);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No changes made to the product']);
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
        }
        exit;
    }
    
    // Delete product
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
        exit;
    }
}

// Get product list
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store - View Items</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            padding: 30px;
            background: linear-gradient(135deg, rgba(0, 20, 40, 0.05), rgba(0, 10, 30, 0.1));
        }

        .main-content {
            width: 100%;
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

        /* Add Product Form */
        #add-product-form {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 500;
            font-size: 0.95rem;
        }

        #add-product-form input,
        #add-product-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            color: #1e293b;
        }

        #add-product-form input:focus,
        #add-product-form textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        #add-product-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        #add-product-form button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }

        #add-product-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        /* Table Styles */
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
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

        /* Button Styles */
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            margin: 0 4px;
        }

        .edit-btn {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2);
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
        }

        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            margin: 15% auto;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15);
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal h3 {
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            color: #64748b;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #e74c3c;
        }

        #update-stock-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #update-stock-form label {
            color: #1e293b;
            font-weight: 500;
        }

        #update-stock-form input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        #update-stock-form input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        #update-stock-form button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }

        #update-stock-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            td, th {
                padding: 12px;
            }

            .action-btn {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <h2>View & Add Products</h2>
            
            <form id="add-product-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter product name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" placeholder="Enter price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" placeholder="Enter stock quantity" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea id="description" name="description" placeholder="Enter product description" rows="3"></textarea>
                </div>
                <button type="submit">
                    <i class="fas fa-plus"></i>
                    Add Product
                </button>
            </form>

            <div class="table-container">
                <h3>Product List</h3>
                <table id="product-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price (₱)</th>
                            <th>Stock</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr data-id="<?= htmlspecialchars($product['id']) ?>">
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>₱<?= number_format($product['price'], 2) ?></td>
                                <td class="stock-cell"><?= htmlspecialchars($product['stock']) ?></td>
                                <td><?= htmlspecialchars($product['description'] ?? '') ?></td>
                                <td>
                                    <button class="action-btn edit-btn" onclick="openEditModal(<?= $product['id'] ?>, <?= $product['stock'] ?>)">
                                        <i class="fas fa-edit"></i>
                                        Update
                                    </button>
                                    <button class="action-btn delete-btn" onclick="deleteProduct(<?= $product['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Stock Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Update Product</h3>
            <form id="update-stock-form">
                <input type="hidden" id="edit-product-id" name="product_id">
                <div class="form-group">
                    <label for="new_name">Product Name</label>
                    <input type="text" id="new_name" name="new_name" required>
                </div>
                <div class="form-group">
                    <label for="new_price">Price (₱)</label>
                    <input type="number" id="new_price" name="new_price" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="new_stock">Stock Quantity</label>
                    <input type="number" id="new_stock" name="new_stock" min="0" required>
                </div>
                <div class="form-group">
                    <label for="new_description">Description</label>
                    <textarea id="new_description" name="new_description" rows="3"></textarea>
                </div>
                <button type="submit">
                    <i class="fas fa-save"></i>
                    Update Product
                </button>
            </form>
        </div>
    </div>

    <script>
    // Add product
    document.getElementById('add-product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('add_product', 'true');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            alert(result.message);
            if (result.status === 'success') {
                this.reset();
                location.reload();
            }
        });
    });

    // Update stock
    function openEditModal(productId, currentStock) {
        const modal = document.getElementById('editModal');
        const row = document.querySelector(`tr[data-id="${productId}"]`);
        
        document.getElementById('edit-product-id').value = productId;
        document.getElementById('new_name').value = row.cells[0].textContent;
        document.getElementById('new_price').value = parseFloat(row.cells[1].textContent.replace('₱', ''));
        document.getElementById('new_stock').value = currentStock;
        document.getElementById('new_description').value = row.cells[3].textContent;
        
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('active'), 10);
    }

    document.querySelector('.close').addEventListener('click', function() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
        setTimeout(() => modal.style.display = 'none', 300);
    });

    document.getElementById('update-stock-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('update_stock', 'true');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            alert(result.message);
            if (result.status === 'success') {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the product.');
        });
    });

    // Delete product
    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            const formData = new FormData();
            formData.append('delete_product', 'true');
            formData.append('product_id', productId);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                if (result.status === 'success') {
                    document.querySelector(`tr[data-id="${productId}"]`).remove();
                }
            });
        }
    }
    </script>
</body>
</html>