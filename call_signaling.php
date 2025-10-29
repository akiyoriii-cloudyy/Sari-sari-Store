<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Ensure signaling table exists
$conn->query("CREATE TABLE IF NOT EXISTS call_signals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    call_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    type VARCHAR(32) NOT NULL,
    payload MEDIUMTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ensure invitations table exists
$conn->query("CREATE TABLE IF NOT EXISTS call_invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caller_id INT NOT NULL,
    receiver_id INT NOT NULL,
    caller_name VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','accepted','rejected','ended') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

switch ($action) {
    case 'initiate_call':
        $receiver_id = intval($_POST['receiver_id']);
        $caller_name = $_POST['caller_name'] ?? 'Unknown';
        
        // Store call invitation in database
        $stmt = $conn->prepare("INSERT INTO call_invitations (caller_id, receiver_id, caller_name, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("iis", $user_id, $receiver_id, $caller_name);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "call_id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to initiate call"]);
        }
        $stmt->close();
        break;
        
    case 'check_incoming_calls':
        // Check for pending calls to this user
        $stmt = $conn->prepare("SELECT * FROM call_invitations WHERE receiver_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $call = $result->fetch_assoc();
        $stmt->close();
        
        if ($call) {
            echo json_encode([
                "success" => true, 
                "has_call" => true,
                "call_id" => $call['id'],
                "caller_id" => $call['caller_id'],
                "caller_name" => $call['caller_name']
            ]);
        } else {
            echo json_encode(["success" => true, "has_call" => false]);
        }
        break;
        
    case 'accept_call':
        $call_id = intval($_POST['call_id']);
        
        // Update call status to accepted
        $stmt = $conn->prepare("UPDATE call_invitations SET status = 'accepted' WHERE id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $call_id, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to accept call"]);
        }
        $stmt->close();
        break;
        
    case 'reject_call':
        $call_id = intval($_POST['call_id']);
        
        // Update call status to rejected
        $stmt = $conn->prepare("UPDATE call_invitations SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $call_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(["success" => true]);
        break;
        
    case 'check_call_status':
        $call_id = intval($_GET['call_id']);
        
        $stmt = $conn->prepare("SELECT status FROM call_invitations WHERE id = ?");
        $stmt->bind_param("i", $call_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $call = $result->fetch_assoc();
        $stmt->close();
        
        if ($call) {
            echo json_encode(["success" => true, "status" => $call['status']]);
        } else {
            echo json_encode(["success" => false, "error" => "Call not found"]);
        }
        break;
        
    case 'end_call':
        $call_id = intval($_POST['call_id']);
        
        // Mark call as ended
        $stmt = $conn->prepare("UPDATE call_invitations SET status = 'ended' WHERE id = ?");
        $stmt->bind_param("i", $call_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(["success" => true]);
        break;

    case 'post_signal':
        $call_id = intval($_POST['call_id'] ?? 0);
        $receiver_id = intval($_POST['to'] ?? 0);
        $type = $_POST['type'] ?? '';
        $payload = $_POST['payload'] ?? '';
        if (!$call_id || !$receiver_id || !$type || !$payload) {
            echo json_encode(["success" => false, "error" => "Invalid params"]);
            break;
        }
        $stmt = $conn->prepare("INSERT INTO call_signals (call_id, sender_id, receiver_id, type, payload) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $call_id, $user_id, $receiver_id, $type, $payload);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to post signal"]);
        }
        $stmt->close();
        break;

    case 'poll_signals':
        $call_id = intval($_GET['call_id'] ?? 0);
        if (!$call_id) {
            echo json_encode(["success" => false, "error" => "Missing call_id"]);
            break;
        }
        $stmt = $conn->prepare("SELECT id, sender_id, type, payload FROM call_signals WHERE receiver_id = ? AND call_id = ? ORDER BY id ASC LIMIT 50");
        $stmt->bind_param("ii", $user_id, $call_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $signals = [];
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $signals[] = $row;
            $ids[] = intval($row['id']);
        }
        $stmt->close();
        if (!empty($ids)) {
            $ids_str = implode(',', array_map('intval', $ids));
            $conn->query("DELETE FROM call_signals WHERE id IN ($ids_str)");
        }
        echo json_encode(["success" => true, "signals" => $signals]);
        break;

    case 'clear_signals':
        $call_id = intval($_POST['call_id'] ?? 0);
        if ($call_id) {
            $stmt = $conn->prepare("DELETE FROM call_signals WHERE call_id = ?");
            $stmt->bind_param("i", $call_id);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(["success" => true]);
        break;
        
    default:
        echo json_encode(["success" => false, "error" => "Invalid action"]);
}
?>
