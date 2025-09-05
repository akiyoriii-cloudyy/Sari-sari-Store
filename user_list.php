<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'];
$users = $conn->query("SELECT id, username, last_active FROM users WHERE id != $user_id");

echo "<h3>Users</h3><ul>";
while ($row = $users->fetch_assoc()):
    $last_active = strtotime($row['last_active']);
    $is_online = ($last_active !== false && (time() - $last_active) < 300);
    echo "<li><a href='chat.php?with={$row['id']}'>" 
        . htmlspecialchars($row['username']) . " " 
        . ($is_online ? "<span style='color:green;font-size:12px;'>🟢 Online</span>" : "<span style='color:gray;font-size:12px;'>⚪ Offline</span>")
        . "</a></li>";
endwhile;
echo "</ul>";
