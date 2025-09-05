<?php
// users.php
include 'db.php';

// Retrieve users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] == 'get_all') {
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    // Add a user
    if ($data['action'] == 'add') {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$data['username'], $passwordHash, $data['role']]);
        echo json_encode(['message' => 'User added successfully!']);
    }

    // Delete a user
    if ($data['action'] == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['message' => 'User deleted successfully!']);
    }

    // Update a user
    if ($data['action'] == 'update') {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([$data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $data['id']]);
        echo json_encode(['message' => 'User updated successfully!']);
    }
}
?>
