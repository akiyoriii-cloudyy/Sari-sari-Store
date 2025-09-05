<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$with = isset($_GET['with']) ? intval($_GET['with']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>

        .actions {
    margin-top: 5px;
    display: flex;
    gap: 8px;
}

.actions button {
    padding: 4px 8px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: 0.2s;
}

.actions .unsend {
    background: orange;
    color: white;
}

.actions .delete {
    background: #ff4d4d;
    color: white;
}

.actions button:hover {
    opacity: 0.8;
}


        body { font-family: 'Poppins', sans-serif; margin: 0; background: #f0f2f5; }
        .chat-container { display: flex; height: 90vh; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }

        /* Sidebar */
        .user-list { width: 25%; background: #fff; border-right: 1px solid #ddd; padding: 10px; overflow-y: auto; }
        .user-list h3 { margin-top: 0; }
        .online { color: green; font-size: 12px; }
        .offline { color: gray; font-size: 12px; }

        /* Chat box */
        .chat-box { flex: 1; display: flex; flex-direction: column; background: #fff; }
        .messages { flex: 1; padding: 15px; overflow-y: auto; background: #e5ddd5; }

        /* Input box */
        .input-box { display: flex; border-top: 1px solid #ccc; background: #fff; }
        .input-box input { flex: 1; padding: 12px; border: none; outline: none; font-size: 14px; }
        .input-box button { padding: 12px 20px; background: #0084ff; color: #fff; border: none; cursor: pointer; border-radius: 4px; margin: 5px; }

        /* Messages */
        .message { position: relative; max-width: 65%; margin: 8px 0; padding: 10px 14px; border-radius: 18px; font-size: 14px; line-height: 1.4; }
        .sent { background: #0084ff; color: #fff; margin-left: auto; border-bottom-right-radius: 4px; }
        .received { background: #f1f0f0; margin-right: auto; border-bottom-left-radius: 4px; }

        /* Timestamp */
        .timestamp { font-size: 11px; color: #999; margin-top: 3px; text-align: right; }

        /* Unsent */
        .unsent-text { font-size: 13px; font-style: italic; color: #888; text-align: center; width: 100%; }

        /* Menu like Messenger */
        .menu { position: absolute; top: 0; right: -40px; }
        .menu-btn {
            cursor: pointer;
            font-size: 18px;
            background: transparent;
            border: none;
            color: #666;
            display: none;
        }
        .message.sent:hover .menu-btn { display: inline-block; }

        .menu-options {
            display: none;
            position: absolute;
            top: 25px;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            z-index: 100;
            min-width: 120px;
        }
        .menu-options button {
            display: block;
            width: 100%;
            padding: 10px 14px;
            border: none;
            background: none;
            text-align: left;
            font-size: 14px;
            cursor: pointer;
        }
        .menu-options button.unsend {
            color: #ff9800;
            font-weight: bold;
        }
        .menu-options button.delete {
            color: #e53935;
            font-weight: bold;
        }
        .menu-options button:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- User List -->
        <div class="user-list">
            <h3>Users</h3>
            <div id="userList">Loading...</div>
        </div>

        <!-- Chat Box -->
        <div class="chat-box">
            <div class="messages" id="messages">
                <?php if (!$with): ?>
                    <p style="text-align:center; margin-top:20px;">👈 Select a user to start chatting</p>
                <?php endif; ?>
            </div>

            <?php if ($with): ?>
            <form class="input-box" id="chatForm">
                <input type="hidden" name="receiver_id" value="<?= $with ?>">
                <input type="text" name="message" id="messageInput" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Load Users
    function loadUsers() {
        fetch("load_users.php")
            .then(res => res.text())
            .then(data => document.getElementById("userList").innerHTML = data);
    }
    setInterval(loadUsers, 5000);
    loadUsers();

    <?php if ($with): ?>
    // Load Messages
    function loadMessages() {
        fetch("load_messages.php?with=<?= $with ?>")
            .then(res => res.text())
            .then(data => {
                let msgBox = document.getElementById("messages");
                msgBox.innerHTML = data;
                msgBox.scrollTop = msgBox.scrollHeight;
                attachMenuHandlers();
            });
    }
    setInterval(loadMessages, 2000);
    loadMessages();

    // Send Message
    document.getElementById("chatForm").addEventListener("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        fetch("send_message.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById("messageInput").value = "";
                loadMessages();
            } else {
                alert("Message not sent.");
            }
        });
    });

    // Menu handlers
    function attachMenuHandlers() {
        document.querySelectorAll(".menu-btn").forEach(btn => {
            btn.onclick = function(e) {
                e.stopPropagation();
                let menu = this.nextElementSibling;
                document.querySelectorAll(".menu-options").forEach(m => m.style.display = "none");
                menu.style.display = menu.style.display === "block" ? "none" : "block";
            };
        });
        document.addEventListener("click", () => {
            document.querySelectorAll(".menu-options").forEach(m => m.style.display = "none");
        });
    }

    // Unsend
    function unsendMessage(id) {
        if (!confirm("Unsend this message for everyone?")) return;
        fetch("unsent_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "message_id=" + id
        }).then(res => res.json()).then(data => {
            if (data.success) loadMessages(); else alert("Failed to unsend.");
        });
    }

    // Delete
    function deleteMessage(id) {
        if (!confirm("Delete this message permanently?")) return;
        fetch("delete_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "message_id=" + id
        }).then(res => res.json()).then(data => {
            if (data.success) loadMessages(); else alert("Failed to delete.");
        });
    }
    <?php endif; ?>
    </script>
</body>
</html>
