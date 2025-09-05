<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$with = intval($_GET['with']);

$msgs = $conn->query("SELECT * FROM chat_messages 
                      WHERE (sender_id=$user_id AND receiver_id=$with) 
                         OR (sender_id=$with AND receiver_id=$user_id) 
                      ORDER BY created_at ASC");

while ($msg = $msgs->fetch_assoc()):
    $class = $msg['sender_id'] == $user_id ? 'sent' : 'received';
    echo "<div class='message $class'>" . htmlspecialchars($msg['message']) . "</div>";
endwhile;
