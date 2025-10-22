<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check for pending calls
$stmt = $conn->prepare("SELECT * FROM call_invitations WHERE receiver_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$call = $result->fetch_assoc();
$stmt->close();

if ($call) {
    echo "<div id='incomingCallModal' style='position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:flex; align-items:center; justify-content:center;'>";
    echo "<div style='background:#0b1220; border:2px solid #3b82f6; border-radius:20px; padding:30px; text-align:center; max-width:400px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.5);'>";
    echo "<div style='font-size:48px; margin-bottom:20px;'>📞</div>";
    echo "<h2 style='color:#e2e8f0; margin-bottom:10px; font-size:24px;'>Incoming Video Call</h2>";
    echo "<p style='color:#94a3b8; margin-bottom:30px; font-size:16px;'>" . htmlspecialchars($call['caller_name']) . " is calling you</p>";
    echo "<div style='display:flex; gap:15px; justify-content:center;'>";
    echo "<button onclick='acceptCall(" . $call['id'] . ", " . $call['caller_id'] . ")' style='background:#22c55e; color:#fff; border:none; padding:12px 24px; border-radius:12px; cursor:pointer; font-size:16px; font-weight:600; display:flex; align-items:center; gap:8px;'>✅ Accept</button>";
    echo "<button onclick='rejectCall(" . $call['id'] . ")' style='background:#ef4444; color:#fff; border:none; padding:12px 24px; border-radius:12px; cursor:pointer; font-size:16px; font-weight:600; display:flex; align-items:center; gap:8px;'>❌ Decline</button>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<script>";
    echo "function acceptCall(callId, callerId) {";
    echo "  fetch('call_signaling.php', {";
    echo "    method: 'POST',";
    echo "    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },";
    echo "    body: 'action=accept_call&call_id=' + callId";
    echo "  }).then(() => {";
    echo "    window.open('video_call.php?with=' + callerId + '&call_id=' + callId, '_blank', 'width=1200,height=800');";
    echo "    document.getElementById('incomingCallModal').remove();";
    echo "  });";
    echo "}";
    echo "function rejectCall(callId) {";
    echo "  fetch('call_signaling.php', {";
    echo "    method: 'POST',";
    echo "    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },";
    echo "    body: 'action=reject_call&call_id=' + callId";
    echo "  }).then(() => {";
    echo "    document.getElementById('incomingCallModal').remove();";
    echo "  });";
    echo "}";
    echo "</script>";
} else {
    echo "<!-- No incoming calls -->";
}
?>
