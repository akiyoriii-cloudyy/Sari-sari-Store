<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$with = isset($_GET['with']) ? intval($_GET['with']) : null;
$call_id = isset($_GET['call_id']) ? intval($_GET['call_id']) : null;

if (!$with) {
    header("Location: chat.php");
    exit();
}

// Get user info
$userInfo = $conn->query("SELECT name FROM users WHERE id = $with")->fetch_assoc();
$currentUser = $conn->query("SELECT name FROM users WHERE id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call - <?= htmlspecialchars($userInfo['name']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            height: 100vh;
            overflow: hidden;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: #000;
        }

        .remote-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #1e293b;
        }

        .local-video {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 200px;
            height: 150px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #3b82f6;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            z-index: 10;
        }

        .call-header {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            background: rgba(0,0,0,0.7);
            padding: 12px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .call-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .call-status {
            font-size: 14px;
            color: #22c55e;
        }

        .call-controls {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            z-index: 10;
        }

        .control-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }

        .control-btn:hover {
            transform: scale(1.1);
        }

        .control-btn:active {
            transform: scale(0.95);
        }

        .mic-btn {
            background: #374151;
            color: #e2e8f0;
        }

        .mic-btn.muted {
            background: #ef4444;
            color: #fff;
        }

        .speaker-btn {
            background: #374151;
            color: #e2e8f0;
        }

        .speaker-btn.muted {
            background: #ef4444;
            color: #fff;
        }

        .camera-btn {
            background: #374151;
            color: #e2e8f0;
        }

        .camera-btn.off {
            background: #ef4444;
            color: #fff;
        }

        .end-call-btn {
            background: #ef4444;
            color: #fff;
            width: 70px;
            height: 70px;
        }

        .call-timer {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .connecting-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }

        .connecting-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #374151;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .connecting-text {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .connecting-subtext {
            font-size: 14px;
            color: #94a3b8;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: #e2e8f0;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .back-btn:hover {
            background: rgba(0,0,0,0.8);
        }
    </style>
</head>
<body>
    <div class="video-container">
        <!-- Connecting overlay -->
        <div class="connecting-overlay" id="connectingOverlay">
            <div class="connecting-spinner"></div>
            <div class="connecting-text">Connecting...</div>
            <div class="connecting-subtext">Waiting for <?= htmlspecialchars($userInfo['name']) ?> to join</div>
        </div>

        <!-- Call header -->
        <div class="call-header">
            <h2><?= htmlspecialchars($userInfo['name']) ?></h2>
            <div class="call-status" id="callStatus">Connecting...</div>
        </div>

        <!-- Call timer -->
        <div class="call-timer" id="callTimer" style="display: none;">00:00</div>

        <!-- Back button -->
        <button class="back-btn" onclick="endCall()">← Back to Chat</button>

        <!-- Video elements -->
        <video id="remoteVideo" class="remote-video" autoplay playsinline></video>
        <video id="localVideo" class="local-video" autoplay playsinline muted></video>

        <!-- Call controls -->
        <div class="call-controls">
            <button class="control-btn mic-btn" id="micBtn" onclick="toggleMic()" title="Microphone">
                🎤
            </button>
            <button class="control-btn speaker-btn" id="speakerBtn" onclick="toggleSpeaker()" title="Speaker">
                🔊
            </button>
            <button class="control-btn camera-btn" id="cameraBtn" onclick="toggleCamera()" title="Camera">
                📹
            </button>
            <button class="control-btn end-call-btn" onclick="endCall()" title="End Call">
                📞
            </button>
        </div>
    </div>

    <script>
        // WebRTC configuration
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };

        let localStream;
        let remoteStream;
        let peerConnection;
        let isCallActive = false;
        let callStartTime;
        let callTimer;

        // DOM elements
        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const connectingOverlay = document.getElementById('connectingOverlay');
        const callStatus = document.getElementById('callStatus');
        const callTimerElement = document.getElementById('callTimer');
        const micBtn = document.getElementById('micBtn');
        const speakerBtn = document.getElementById('speakerBtn');
        const cameraBtn = document.getElementById('cameraBtn');

        // Initialize call
        async function initCall() {
            try {
                // Get user media
                localStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                });

                localVideo.srcObject = localStream;

                // Create peer connection
                peerConnection = new RTCPeerConnection(configuration);

                // Add local stream to peer connection
                localStream.getTracks().forEach(track => {
                    peerConnection.addTrack(track, localStream);
                });

                // Handle remote stream
                peerConnection.ontrack = (event) => {
                    remoteStream = event.streams[0];
                    remoteVideo.srcObject = remoteStream;
                    onCallConnected();
                };

                // Handle ICE candidates
                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        // In a real app, send this to the other peer via signaling server
                        console.log('ICE candidate:', event.candidate);
                    }
                };

                // For demo purposes, simulate connection after 3 seconds
                setTimeout(() => {
                    onCallConnected();
                }, 3000);

            } catch (error) {
                console.error('Error accessing media devices:', error);
                callStatus.textContent = 'Error: Could not access camera/microphone';
            }
        }

        function onCallConnected() {
            connectingOverlay.style.display = 'none';
            callStatus.textContent = 'Connected';
            callTimerElement.style.display = 'block';
            isCallActive = true;
            callStartTime = Date.now();
            startCallTimer();
        }

        function startCallTimer() {
            callTimer = setInterval(() => {
                const elapsed = Date.now() - callStartTime;
                const minutes = Math.floor(elapsed / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                callTimerElement.textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Control functions
        function toggleMic() {
            if (localStream) {
                const audioTrack = localStream.getAudioTracks()[0];
                if (audioTrack) {
                    audioTrack.enabled = !audioTrack.enabled;
                    micBtn.classList.toggle('muted', !audioTrack.enabled);
                    micBtn.textContent = audioTrack.enabled ? '🎤' : '🎤';
                }
            }
        }

        function toggleSpeaker() {
            // Note: Speaker control is limited in browsers for security reasons
            // This is a visual toggle only
            speakerBtn.classList.toggle('muted');
            speakerBtn.textContent = speakerBtn.classList.contains('muted') ? '🔇' : '🔊';
        }

        function toggleCamera() {
            if (localStream) {
                const videoTrack = localStream.getVideoTracks()[0];
                if (videoTrack) {
                    videoTrack.enabled = !videoTrack.enabled;
                    cameraBtn.classList.toggle('off', !videoTrack.enabled);
                    cameraBtn.textContent = videoTrack.enabled ? '📹' : '📹';
                }
            }
        }

        function endCall() {
            if (callTimer) {
                clearInterval(callTimer);
            }

            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }

            if (peerConnection) {
                peerConnection.close();
            }

            // End call in database if call_id exists
            <?php if ($call_id): ?>
            fetch("call_signaling.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=end_call&call_id=<?= $call_id ?>"
            });
            <?php endif; ?>

            // Redirect back to chat
            window.location.href = `chat.php?with=<?= $with ?>`;
        }

        // Handle page unload
        window.addEventListener('beforeunload', endCall);

        // Initialize call when page loads
        window.addEventListener('load', initCall);
    </script>
</body>
</html>
