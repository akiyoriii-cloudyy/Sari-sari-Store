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


        body { font-family: 'Poppins', sans-serif; margin: 0; background: #f8fafc; }
        .chat-container { display: flex; height: 90vh; border-radius: 16px; overflow: hidden; backdrop-filter: blur(15px); background: rgba(255,255,255,0.9); box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06); }

        /* Sidebar */
        .user-list { width: 25%; background: rgba(14,24,36,0.95); color: #fff; border-right: 1px solid rgba(255,255,255,0.08); padding: 12px; overflow-y: auto; }
        .user-list h3 { margin-top: 0; font-weight: 600; letter-spacing: .3px; }
        .online { color: green; font-size: 12px; }
        .offline { color: gray; font-size: 12px; }
        .user-list ul { list-style: none; padding: 0; margin: 0; }
        .user-list li { padding: 10px 12px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; margin: 6px 4px; background: rgba(255,255,255,0.06); transition: .2s; }
        .user-list li:hover { background: rgba(52,152,219,0.12); transform: translateX(2px); }
        .user-list a { color: #fff; text-decoration: none; }

        /* Chat box */
        .chat-box { flex: 1; display: flex; flex-direction: column; background: transparent; }
        .messages { flex: 1; padding: 16px; overflow-y: auto; background: transparent; }

        /* Input box */
        .input-box { display: flex; border-top: 1px solid rgba(0,0,0,0.06); background: rgba(255,255,255,0.9); }
        .input-box input { flex: 1; padding: 12px; border: 2px solid rgba(52,152,219,0.1); border-radius: 12px; outline: none; font-size: 14px; margin: 8px; background: rgba(255,255,255,0.9); }
        .input-box button { padding: 12px 20px; background: linear-gradient(135deg, #3498db, #2980b9); color: #fff; border: none; cursor: pointer; border-radius: 12px; margin: 8px; box-shadow: 0 4px 15px rgba(52,152,219,0.2); }

        /* Messages */
        .message { position: relative; max-width: 65%; margin: 8px 0; padding: 10px 14px; border-radius: 18px; font-size: 14px; line-height: 1.4; box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
        .sent { background: linear-gradient(135deg, #3498db, #2980b9); color: #fff; margin-left: auto; border-bottom-right-radius: 4px; }
        .received { background: rgba(255,255,255,0.9); margin-right: auto; border-bottom-left-radius: 4px; border: 1px solid rgba(0,0,0,0.06); }

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

        .call-dock { position: fixed; inset: 0; background: #000; height: 100vh; width: 100vw; display: none; z-index: 10000; }
        .video-wrap { position: relative; width: 100%; height: 100%; }
        #remoteVideo { width: 100%; height: 100%; object-fit: cover; }
        #localVideo { position: absolute; right: 16px; bottom: 92px; width: 180px; height: 130px; background: #222; object-fit: cover; border-radius: 10px; border: 2px solid #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.4); cursor: move; }
        .call-controls { position: absolute; left: 0; right: 0; bottom: 18px; display: flex; gap: 14px; justify-content: center; }
        .call-controls button { background: rgba(255,255,255,0.95); color: #000; border: none; padding: 14px 16px; border-radius: 28px; font-size: 15px; box-shadow: 0 6px 18px rgba(0,0,0,0.25); }
        .end-btn { background: #e53935 !important; color: #fff !important; }
        .badge { position: absolute; top: 16px; left: 16px; background: rgba(0,0,0,0.5); color: #fff; padding: 6px 10px; border-radius: 12px; font-size: 12px; }
        .badge-local { position: absolute; right: 16px; bottom: 232px; background: rgba(0,0,0,0.5); color: #fff; padding: 4px 8px; border-radius: 10px; font-size: 11px; }
        .top-actions { display: flex; gap: 8px; padding: 10px; border-bottom: 1px solid rgba(0,0,0,0.06); background: rgba(255,255,255,0.9); align-items: center; }
        .btn-icon-round { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3498db, #2980b9); color: #fff; box-shadow: 0 6px 20px rgba(52,152,219,0.35); border: none; }
        .btn-icon-round:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }

        /* Incoming Call Modal */
        .incoming-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 9999; }
        .incoming-modal { width: 360px; background: rgba(255,255,255,0.95); border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); padding: 20px; text-align: center; backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.06); }
        .incoming-title { font-weight: 600; margin-bottom: 8px; color: #1e293b; }
        .incoming-sub { color: #64748b; margin-bottom: 16px; }
        .incoming-avatar { width: 84px; height: 84px; border-radius: 50%; background: linear-gradient(135deg, #3498db, #2980b9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px; box-shadow: 0 8px 24px rgba(52,152,219,0.35); }
        .incoming-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-accept { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; border: none; padding: 10px 16px; border-radius: 12px; box-shadow: 0 6px 18px rgba(34,197,94,0.35); }
        .btn-decline { background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff; border: none; padding: 10px 16px; border-radius: 12px; box-shadow: 0 6px 18px rgba(231,76,60,0.35); }
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
            <div class="top-actions">
                <button id="startCallBtn" class="btn-icon-round" <?= $with ? '' : 'disabled' ?> title="Start video call">📞</button>
            </div>
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
            <div class="call-dock" id="callDock">
                <div class="video-wrap">
                    <video id="remoteVideo" autoplay playsinline></video>
                    <div class="badge">Remote</div>
                    <video id="localVideo" autoplay muted playsinline></video>
                    <div class="badge-local">You</div>
                    <div class="call-controls">
                        <button id="switchCamBtn">📷 Camera</button>
                        <button id="micBtn">🎙️ Mic</button>
                        <button id="speakerBtn">🔊 Speaker</button>
                        <button id="endCallBtn" class="end-btn">⛔ End</button>
                    </div>
                </div>
            </div>
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
    
    let pc = null, localStream = null, remoteStream = null, callId = null, isCaller = false, pollTimer = null, facing = 'user', pollTick = 0;
    let remoteUserId = <?= $with ? intval($with) : 'null' ?>;
    const callDock = document.getElementById('callDock');
    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const startCallBtn = document.getElementById('startCallBtn');
    const switchCamBtn = document.getElementById('switchCamBtn');
    const endCallBtn = document.getElementById('endCallBtn');
    const speakerBtn = document.getElementById('speakerBtn');
    const micBtn = document.getElementById('micBtn');
    let micMuted = false;

    async function getMedia(facingMode) {
        if (localStream) localStream.getTracks().forEach(t => t.stop());
        const constraints = { audio: true, video: { facingMode: { ideal: facingMode } } };
        localStream = await navigator.mediaDevices.getUserMedia(constraints);
        localVideo.srcObject = localStream;
        if (pc) {
            const senders = pc.getSenders().filter(s => s.track && s.track.kind === 'video');
            const vTrack = localStream.getVideoTracks()[0];
            if (senders[0] && vTrack) await senders[0].replaceTrack(vTrack);
            const aSenders = pc.getSenders().filter(s => s.track && s.track.kind === 'audio');
            const aTrack = localStream.getAudioTracks()[0];
            if (aSenders[0] && aTrack) await aSenders[0].replaceTrack(aTrack);
        }
    }

    function showDock(show) { 
        callDock.style.display = show ? 'block' : 'none'; 
        if (show) {
            const el = callDock;
            if (el.requestFullscreen) { try { el.requestFullscreen(); } catch(e) {} }
        } else {
            if (document.fullscreenElement) { try { document.exitFullscreen(); } catch(e) {} }
        }
    }

    function createPC() {
        pc = new RTCPeerConnection({
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        });
        remoteStream = new MediaStream();
        remoteVideo.srcObject = remoteStream;
        if (localStream) localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
        pc.ontrack = e => { e.streams[0].getTracks().forEach(t => remoteStream.addTrack(t)); };
        pc.onicecandidate = e => {
            if (e.candidate) postSignal('candidate', JSON.stringify(e.candidate));
        };
        pc.onconnectionstatechange = () => {
            if (['failed','disconnected','closed'].includes(pc.connectionState)) endCall(false);
        };
    }

    async function startCall() {
        isCaller = true;
        remoteUserId = <?= $with ? intval($with) : 'null' ?>;
        const form = new FormData();
        form.append('action','initiate_call');
        form.append('receiver_id','<?= $with ?>');
        form.append('caller_name','Caller');
        const res = await fetch('call_signaling.php', { method:'POST', body: form }).then(r=>r.json());
        if (!res.success) return;
        callId = res.call_id;
        await getMedia(facing);
        createPC();
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        await postSignal('offer', JSON.stringify(offer));
        showDock(true);
        startPolling();
    }

    async function acceptIncoming(cId, fromId) {
        callId = cId;
        isCaller = false;
        if (!remoteUserId) remoteUserId = fromId;
        const form = new FormData();
        form.append('action','accept_call');
        form.append('call_id', callId);
        await fetch('call_signaling.php', { method:'POST', body: form });
        await getMedia(facing);
        createPC();
        showDock(true);
        startPolling();
    }

    async function postSignal(type, payload) {
        const form = new FormData();
        form.append('action','post_signal');
        form.append('call_id', callId);
        form.append('to', remoteUserId);
        form.append('type', type);
        form.append('payload', payload);
        await fetch('call_signaling.php', { method:'POST', body: form });
    }

    async function handleSignals(signals) {
        for (const s of signals) {
            if (s.type === 'offer' && !isCaller) {
                const offer = JSON.parse(s.payload);
                await pc.setRemoteDescription(offer);
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                await postSignal('answer', JSON.stringify(answer));
            } else if (s.type === 'answer' && isCaller) {
                await pc.setRemoteDescription(JSON.parse(s.payload));
            } else if (s.type === 'candidate') {
                try { await pc.addIceCandidate(JSON.parse(s.payload)); } catch(e) {}
            } else if (s.type === 'bye') {
                endCall(false);
            }
        }
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(async () => {
            if (!callId) return;
            const url = 'call_signaling.php?action=poll_signals&call_id=' + callId;
            const res = await fetch(url).then(r=>r.json()).catch(()=>null);
            if (res && res.success && res.signals) await handleSignals(res.signals);
            // Periodically check call status as a fallback
            pollTick = (pollTick + 1) % 3;
            if (pollTick === 0) {
                const st = await fetch('call_signaling.php?action=check_call_status&call_id=' + callId).then(r=>r.json()).catch(()=>null);
                if (st && st.success && (st.status === 'ended' || st.status === 'rejected')) {
                    endCall(false);
                }
            }
        }, 1000);
    }

    async function endCall(notify=true) {
        if (notify && callId) {
            // Notify remote peer to close
            try { await postSignal('bye', '{}'); } catch(e) {}
            const form = new FormData();
            form.append('action','end_call');
            form.append('call_id', callId);
            fetch('call_signaling.php', { method:'POST', body: form });
            const form2 = new FormData();
            form2.append('action','clear_signals');
            form2.append('call_id', callId);
            fetch('call_signaling.php', { method:'POST', body: form2 });
        }
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
        if (pc) { pc.close(); pc = null; }
        if (localStream) { localStream.getTracks().forEach(t=>t.stop()); localStream = null; }
        showDock(false);
        callId = null; isCaller = false;
    }

    // Incoming call modal
    let incomingShown = false;
    function showIncomingModal(meta) {
        if (!window.__incomingOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'incoming-overlay';
            overlay.innerHTML = `
                <div class="incoming-modal">
                  <div class="incoming-title">Incoming Call</div>
                  <div class="incoming-sub">from <strong id="incName"></strong></div>
                  <div class="incoming-avatar">📞</div>
                  <div class="incoming-actions">
                    <button id="incAccept" class="btn-accept">Accept</button>
                    <button id="incDecline" class="btn-decline">Decline</button>
                  </div>
                </div>`;
            document.body.appendChild(overlay);
            window.__incomingOverlay = overlay;
        }
        const overlay = window.__incomingOverlay;
        overlay.style.display = 'flex';
        overlay.querySelector('#incName').textContent = meta.caller_name || 'User';
        overlay.querySelector('#incAccept').onclick = () => { incomingShown = false; overlay.style.display = 'none'; acceptIncoming(meta.call_id, meta.caller_id); };
        overlay.querySelector('#incDecline').onclick = () => {
            incomingShown = false; overlay.style.display = 'none';
            const form = new FormData(); form.append('action','reject_call'); form.append('call_id', meta.call_id); fetch('call_signaling.php', { method:'POST', body: form });
        };
        incomingShown = true;
    }

    async function checkIncoming() {
        const res = await fetch('call_signaling.php?action=check_incoming_calls').then(r=>r.json()).catch(()=>null);
        if (res && res.success && res.has_call && !incomingShown) {
            showIncomingModal(res);
        }
    }

    if (startCallBtn) startCallBtn.onclick = () => { if (!callId) startCall(); };
    if (endCallBtn) endCallBtn.onclick = () => endCall(true);
    if (switchCamBtn) switchCamBtn.onclick = async () => { facing = facing === 'user' ? 'environment' : 'user'; await getMedia(facing); };
    if (speakerBtn) speakerBtn.onclick = () => { 
        remoteVideo.muted = !remoteVideo.muted; 
        speakerBtn.textContent = remoteVideo.muted ? '🔇 Muted' : '🔊 Speaker'; 
    };
    if (micBtn) micBtn.onclick = () => {
        micMuted = !micMuted;
        if (localStream) localStream.getAudioTracks().forEach(t => t.enabled = !micMuted);
        micBtn.textContent = micMuted ? '🔇 Mic Off' : '🎙️ Mic';
    };

    // Draggable local PiP
    (function enableDragPiP(){
        let dragging = false, startX=0, startY=0, origLeft=null, origTop=null;
        function onDown(e){
            dragging = true; const rect = localVideo.getBoundingClientRect();
            startX = (e.touches?e.touches[0].clientX:e.clientX) - rect.left;
            startY = (e.touches?e.touches[0].clientY:e.clientY) - rect.top;
            // switch to left/top positioning for easier dragging
            const r = window.getComputedStyle(localVideo).right;
            const b = window.getComputedStyle(localVideo).bottom;
            if (r !== 'auto' || b !== 'auto'){
                const cur = localVideo.getBoundingClientRect();
                localVideo.style.left = cur.left + 'px';
                localVideo.style.top = cur.top + 'px';
                localVideo.style.right = 'auto';
                localVideo.style.bottom = 'auto';
            }
            origLeft = parseFloat(localVideo.style.left)||localVideo.getBoundingClientRect().left;
            origTop = parseFloat(localVideo.style.top)||localVideo.getBoundingClientRect().top;
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
            document.addEventListener('touchmove', onMove, {passive:false});
            document.addEventListener('touchend', onUp);
        }
        function onMove(e){ if(!dragging) return; if(e.cancelable) e.preventDefault();
            const x = (e.touches?e.touches[0].clientX:e.clientX) - startX;
            const y = (e.touches?e.touches[0].clientY:e.clientY) - startY;
            localVideo.style.left = Math.max(0, Math.min(window.innerWidth - localVideo.offsetWidth, x)) + 'px';
            localVideo.style.top = Math.max(0, Math.min(window.innerHeight - localVideo.offsetHeight, y)) + 'px';
        }
        function onUp(){ dragging = false;
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
            document.removeEventListener('touchmove', onMove);
            document.removeEventListener('touchend', onUp);
        }
        localVideo.addEventListener('mousedown', onDown);
        localVideo.addEventListener('touchstart', onDown, {passive:false});
    })();

    // Auto-accept when navigated with autoAccept=call_id
    const __qs = new URLSearchParams(location.search);
    const __autoAccept = __qs.get('autoAccept');
    if (__autoAccept) { try { acceptIncoming(parseInt(__autoAccept), <?= $with ?>); } catch(e) {} }
    setInterval(checkIncoming, 3000);
    <?php endif; ?>
    // Global incoming call checker (when not inside a specific chat)
    <?php if (!$with): ?>
    async function checkIncomingGlobal() {
        const res = await fetch('call_signaling.php?action=check_incoming_calls').then(r=>r.json()).catch(()=>null);
        if (res && res.success && res.has_call && !incomingShown) {
            // Show modal here; on accept, redirect and auto-accept
            showIncomingModal({
                caller_name: res.caller_name,
                call_id: res.call_id,
                caller_id: res.caller_id
            });
            // Override accept button to redirect
            const overlay = window.__incomingOverlay;
            overlay.querySelector('#incAccept').onclick = () => {
                incomingShown = false; overlay.style.display = 'none';
                window.location.href = 'chat.php?with=' + res.caller_id + '&autoAccept=' + res.call_id;
            };
        }
    }
    setInterval(checkIncomingGlobal, 3000);
    <?php endif; ?>
    </script>
</body>
</html>
