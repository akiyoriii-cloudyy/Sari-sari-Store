<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if ($message !== "") {
        // ✅ Insert new message
        $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, created_at, is_unsent) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

        if ($stmt->execute()) {
            // ✅ Update last_active (keep sender online)
            $update = $conn->prepare("UPDATE users SET last_active = NOW() WHERE id = ?");
            $update->bind_param("i", $sender_id);
            $update->execute();
            $update->close();

            echo json_encode(["success" => true]);
            exit();
        }
    }
}

echo json_encode(["success" => false]);
