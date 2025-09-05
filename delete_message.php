<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id']);
$type = $_POST['type'] ?? "me";

// ✅ Fetch the message
$msg = $conn->query("SELECT * FROM chat_messages WHERE id = $message_id")->fetch_assoc();
if (!$msg) {
    echo json_encode(["success" => false, "error" => "Message not found"]);
    exit();
}

// 🔹 Delete only for me
if ($type === "me") {
    $stmt = $conn->prepare("
        INSERT INTO deleted_messages (user_id, message_id) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE user_id=user_id
    ");
    $stmt->bind_param("ii", $user_id, $message_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(["success" => true]);

// 🔹 Delete for everyone (only sender can do this)
} elseif ($type === "all" && $msg['sender_id'] == $user_id) {
    $conn->query("DELETE FROM chat_messages WHERE id = $message_id");
    // Optional: clean deleted_messages table
    $conn->query("DELETE FROM deleted_messages WHERE message_id = $message_id");
    echo json_encode(["success" => true]);

} else {
    echo json_encode(["success" => false, "error" => "Not allowed"]);
}
?>
