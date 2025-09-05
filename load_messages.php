<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];
$with = intval($_GET['with']);

// ✅ Update last_active
$conn->query("UPDATE users SET last_active = NOW() WHERE id = $user_id");

// ✅ Get deleted message IDs for this user
$deleted = $conn->query("SELECT message_id FROM deleted_messages WHERE user_id = $user_id");
$deleted_ids = [];
while ($row = $deleted->fetch_assoc()) {
    $deleted_ids[] = $row['message_id'];
}

// ✅ Fetch chat messages (skip deleted-for-me)
$msgs = $conn->query("
    SELECT * FROM chat_messages 
    WHERE ((sender_id=$user_id AND receiver_id=$with) 
        OR (sender_id=$with AND receiver_id=$user_id))
    ORDER BY created_at ASC
");

// ✅ Get chat partner info
$userInfo = $conn->query("SELECT name, last_active FROM users WHERE id = $with")->fetch_assoc();
$is_online = false;
if ($userInfo) {
    $last_active = strtotime($userInfo['last_active']);
    if ($last_active !== false && (time() - $last_active) < 300) {
        $is_online = true;
    }
}

// ✅ Chat header
echo "<div style='text-align:center; margin-bottom:10px; font-weight:bold;'>";
echo htmlspecialchars($userInfo['name']) . " ";
echo $is_online ? "<span style='color:green'>🟢 Online</span>" : "<span style='color:gray'>⚪ Offline</span>";
echo "</div>";

// ✅ Display messages
while ($msg = $msgs->fetch_assoc()):
    if (in_array($msg['id'], $deleted_ids)) continue; // skip deleted-for-me

    $class = $msg['sender_id'] == $user_id ? 'sent' : 'received';
    echo "<div class='message $class' data-id='{$msg['id']}'>";

    // 🔹 Unsent messages
    if (!empty($msg['is_unsent'])) {
        echo "<div class='unsent-text'><i>Message unsent</i></div>";
    } else {
        echo htmlspecialchars($msg['message']);
    }

    // 🔹 Timestamp
    echo "<div class='timestamp'>" . date("g:i A · M j", strtotime($msg['created_at'])) . "</div>";

    // 🔹 Action buttons
    echo "<div class='actions'>";
    if ($msg['sender_id'] == $user_id && empty($msg['is_unsent'])) {
        echo "<button class='unsend' onclick='unsendMessage({$msg['id']})'>🟠 Unsend</button>";
        echo "<button class='delete' onclick='deleteMessage({$msg['id']}, \"me\")'>❌ Delete for me</button>";
        echo "<button class='delete' onclick='deleteMessage({$msg['id']}, \"all\")'>🟥 Delete for everyone</button>";
    } elseif ($msg['sender_id'] != $user_id) {
        echo "<button class='delete' onclick='deleteMessage({$msg['id']}, \"me\")'>❌ Delete for me</button>";
    }
    echo "</div>";

    echo "</div>";
endwhile;
?>
