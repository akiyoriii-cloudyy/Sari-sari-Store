<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id']);

// ✅ Check if the message exists and was sent by the current user
$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE id = ? AND sender_id = ?");
$stmt->bind_param("ii", $message_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$msg = $result->fetch_assoc();
$stmt->close();

if (!$msg) {
    echo json_encode(["success" => false, "error" => "Message not found or not yours"]);
    exit();
}

// ✅ Mark message as unsent
$stmt = $conn->prepare("UPDATE chat_messages SET is_unsent = 1, message = '' WHERE id = ?");
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to unsend"]);
}
$stmt->close();
?>
