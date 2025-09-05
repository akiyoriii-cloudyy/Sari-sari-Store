<?php
session_start();
include 'connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_id = $_POST['delete_user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success = "User ID $delete_id has been deleted.";
    } else {
        $error = "Error deleting user.";
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_update'])) {
    $update_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $update_id);
    if ($stmt->execute()) {
        $success = "User ID $update_id updated successfully.";
    } else {
        $error = "Error updating user.";
    }
}

// Fetch all users
$sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// Determine if a user is being edited
$edit_id = $_POST['update_user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Sari-Sari Store</title>
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

        .feedback {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feedback.success {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            border-left: 4px solid #2ecc71;
        }

        .feedback.error {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        .users-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }

        .users-table th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 16px;
            font-size: 0.95rem;
        }

        .users-table th:first-child {
            border-top-left-radius: 12px;
        }

        .users-table th:last-child {
            border-top-right-radius: 12px;
        }

        .users-table td {
            padding: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        .users-table tr:hover {
            background: rgba(52, 152, 219, 0.05);
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table input[type="text"],
        .users-table input[type="email"] {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid rgba(52, 152, 219, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .users-table input[type="text"]:focus,
        .users-table input[type="email"]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-update {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2);
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
        }

        .btn-save {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }

        .btn-remove {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        }

        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .actions form {
            margin: 0;
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

            .users-table {
                display: block;
                overflow-x: auto;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>
            <i class="fas fa-users"></i>
            Manage Users
        </h2>

        <?php if (isset($success)): ?>
            <div class="feedback success">
                <i class="fas fa-check-circle"></i>
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="feedback error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <?php if ($edit_id == $row['id']): ?>
                                    <form action="manageusers.php" method="POST">
                                        <td><?= $row['id'] ?><input type="hidden" name="user_id" value="<?= $row['id'] ?>"></td>
                                        <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required></td>
                                        <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required></td>
                                        <td><?= $row['created_at'] ?></td>
                                        <td class="actions">
                                            <button type="submit" name="save_update" class="btn btn-save">
                                                <i class="fas fa-save"></i>
                                                Save
                                            </button>
                                        </td>
                                    </form>
                                <?php else: ?>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td class="actions">
                                        <form action="manageusers.php" method="POST">
                                            <input type="hidden" name="update_user_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-update">
                                                <i class="fas fa-edit"></i>
                                                Update
                                            </button>
                                        </form>
                                        <form action="manageusers.php" method="POST">
                                            <input type="hidden" name="delete_user_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-remove" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No registered users found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
