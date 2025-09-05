<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];

// ✅ Get all users except current
$result = $conn->query("SELECT id, name, last_active FROM users WHERE id != $user_id");

echo "<ul>";
while ($row = $result->fetch_assoc()) {
    $last_active = strtotime($row['last_active']);
    $is_online = ($last_active !== false && (time() - $last_active) < 300); // 5 minutes

    echo "<li style='padding:10px; border-bottom:1px solid #ddd; display:flex; align-items:center; justify-content:space-between;'>";
    echo "<a href='chat.php?with=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
    echo "<span class='" . ($is_online ? "online" : "offline") . "'>";
    echo $is_online ? "🟢" : "⚪";
    echo "</span>";
    echo "</li>";
}
echo "</ul>";
