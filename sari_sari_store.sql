-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 06:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sari_sari_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `call_invitations`
--

CREATE TABLE `call_invitations` (
  `id` int(11) NOT NULL,
  `caller_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `caller_name` varchar(255) NOT NULL,
  `status` enum('pending','accepted','rejected','ended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `call_invitations`
--

INSERT INTO `call_invitations` (`id`, `caller_id`, `receiver_id`, `caller_name`, `status`, `created_at`) VALUES
(1, 18, 17, 'User', 'ended', '2025-09-09 16:05:50'),
(2, 17, 18, 'User', 'ended', '2025-09-09 16:34:42'),
(3, 16, 17, 'User', 'ended', '2025-09-10 07:31:45'),
(4, 20, 21, 'Caller', 'ended', '2025-10-22 15:38:01'),
(5, 20, 21, 'Caller', 'ended', '2025-10-22 15:38:23'),
(6, 21, 20, 'Caller', 'ended', '2025-10-22 15:38:44'),
(7, 20, 21, 'Caller', 'ended', '2025-10-22 15:43:30'),
(8, 20, 21, 'Caller', 'ended', '2025-10-22 15:43:59'),
(9, 21, 20, 'Caller', 'rejected', '2025-10-22 15:44:18'),
(10, 20, 21, 'Caller', 'ended', '2025-10-22 15:44:31'),
(11, 21, 20, 'Caller', 'ended', '2025-10-22 15:45:26'),
(12, 21, 20, 'Caller', 'ended', '2025-10-22 15:45:44'),
(13, 20, 21, 'Caller', 'ended', '2025-10-22 15:45:53'),
(14, 20, 21, 'Caller', 'ended', '2025-10-22 15:46:01'),
(15, 20, 21, 'Caller', 'ended', '2025-10-22 15:46:17'),
(16, 21, 20, 'Caller', 'ended', '2025-10-22 15:46:34'),
(17, 20, 21, 'Caller', 'ended', '2025-10-22 15:46:45'),
(18, 20, 21, 'Caller', 'ended', '2025-10-22 15:46:58'),
(19, 20, 21, 'Caller', 'ended', '2025-10-22 15:47:10'),
(20, 21, 20, 'Caller', 'ended', '2025-10-22 15:47:22'),
(21, 20, 21, 'Caller', 'ended', '2025-10-22 15:51:31'),
(22, 21, 20, 'Caller', 'ended', '2025-10-22 15:51:40'),
(23, 20, 21, 'Caller', 'ended', '2025-10-22 15:51:53'),
(24, 21, 20, 'Caller', 'ended', '2025-10-22 15:52:02'),
(25, 20, 21, 'Caller', 'ended', '2025-10-22 15:54:15'),
(26, 20, 21, 'Caller', 'ended', '2025-10-22 15:54:25'),
(27, 21, 20, 'Caller', 'ended', '2025-10-22 15:55:18'),
(28, 20, 21, 'Caller', 'ended', '2025-10-22 16:00:22'),
(29, 20, 21, 'Caller', 'ended', '2025-10-22 16:00:31'),
(30, 21, 20, 'Caller', 'ended', '2025-10-22 16:02:29'),
(31, 20, 21, 'Caller', 'ended', '2025-10-22 16:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `call_signals`
--

CREATE TABLE `call_signals` (
  `id` int(11) NOT NULL,
  `call_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `payload` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `call_signals`
--

INSERT INTO `call_signals` (`id`, `call_id`, `sender_id`, `receiver_id`, `type`, `payload`, `created_at`) VALUES
(76, 9, 21, 20, 'offer', '{\"sdp\":\"v=0\\r\\no=- 355409083646865829 2 IN IP4 127.0.0.1\\r\\ns=-\\r\\nt=0 0\\r\\na=group:BUNDLE 0 1\\r\\na=extmap-allow-mixed\\r\\na=msid-semantic: WMS c3409cb8-37ae-4209-97ac-3e0ab26b63d1\\r\\nm=audio 9 UDP/TLS/RTP/SAVPF 111 63 9 0 8 13 110 126\\r\\nc=IN IP4 0.0.0.0\\r\\na=rtcp:9 IN IP4 0.0.0.0\\r\\na=ice-ufrag:kwUk\\r\\na=ice-pwd:gd5/GZ+2Qxlw7kdfoUZrvtEe\\r\\na=ice-options:trickle\\r\\na=fingerprint:sha-256 B5:7A:F5:59:B1:54:45:9C:F0:59:C5:52:19:7D:70:11:DE:2D:46:44:E5:58:F2:D1:B0:07:76:1B:75:43:10:0D\\r\\na=setup:actpass\\r\\na=mid:0\\r\\na=extmap:1 urn:ietf:params:rtp-hdrext:ssrc-audio-level\\r\\na=extmap:2 http://www.webrtc.org/experiments/rtp-hdrext/abs-send-time\\r\\na=extmap:3 http://www.ietf.org/id/draft-holmer-rmcat-transport-wide-cc-extensions-01\\r\\na=extmap:4 urn:ietf:params:rtp-hdrext:sdes:mid\\r\\na=sendrecv\\r\\na=msid:c3409cb8-37ae-4209-97ac-3e0ab26b63d1 9c3f7a69-7b2c-495f-bf52-c3407a586160\\r\\na=rtcp-mux\\r\\na=rtcp-rsize\\r\\na=rtpmap:111 opus/48000/2\\r\\na=rtcp-fb:111 transport-cc\\r\\na=fmtp:111 minptime=10;useinbandfec=1\\r\\na=rtpmap:63 red/48000/2\\r\\na=fmtp:63 111/111\\r\\na=rtpmap:9 G722/8000\\r\\na=rtpmap:0 PCMU/8000\\r\\na=rtpmap:8 PCMA/8000\\r\\na=rtpmap:13 CN/8000\\r\\na=rtpmap:110 telephone-event/48000\\r\\na=rtpmap:126 telephone-event/8000\\r\\na=ssrc:3031801706 cname:3hxgG22pa6B8GcTc\\r\\na=ssrc:3031801706 msid:c3409cb8-37ae-4209-97ac-3e0ab26b63d1 9c3f7a69-7b2c-495f-bf52-c3407a586160\\r\\nm=video 9 UDP/TLS/RTP/SAVPF 96 97 103 104 107 108 109 114 115 116 117 118 39 40 45 46 98 99 100 101 119 120 49 50 123 124 125\\r\\nc=IN IP4 0.0.0.0\\r\\na=rtcp:9 IN IP4 0.0.0.0\\r\\na=ice-ufrag:kwUk\\r\\na=ice-pwd:gd5/GZ+2Qxlw7kdfoUZrvtEe\\r\\na=ice-options:trickle\\r\\na=fingerprint:sha-256 B5:7A:F5:59:B1:54:45:9C:F0:59:C5:52:19:7D:70:11:DE:2D:46:44:E5:58:F2:D1:B0:07:76:1B:75:43:10:0D\\r\\na=setup:actpass\\r\\na=mid:1\\r\\na=extmap:14 urn:ietf:params:rtp-hdrext:toffset\\r\\na=extmap:2 http://www.webrtc.org/experiments/rtp-hdrext/abs-send-time\\r\\na=extmap:13 urn:3gpp:video-orientation\\r\\na=extmap:3 http://www.ietf.org/id/draft-holmer-rmcat-transport-wide-cc-extensions-01\\r\\na=extmap:5 http://www.webrtc.org/experiments/rtp-hdrext/playout-delay\\r\\na=extmap:6 http://www.webrtc.org/experiments/rtp-hdrext/video-content-type\\r\\na=extmap:7 http://www.webrtc.org/experiments/rtp-hdrext/video-timing\\r\\na=extmap:8 http://www.webrtc.org/experiments/rtp-hdrext/color-space\\r\\na=extmap:4 urn:ietf:params:rtp-hdrext:sdes:mid\\r\\na=extmap:10 urn:ietf:params:rtp-hdrext:sdes:rtp-stream-id\\r\\na=extmap:11 urn:ietf:params:rtp-hdrext:sdes:repaired-rtp-stream-id\\r\\na=sendrecv\\r\\na=msid:c3409cb8-37ae-4209-97ac-3e0ab26b63d1 4b969135-3d63-455d-8c00-c4baf8f90a8b\\r\\na=rtcp-mux\\r\\na=rtcp-rsize\\r\\na=rtpmap:96 VP8/90000\\r\\na=rtcp-fb:96 goog-remb\\r\\na=rtcp-fb:96 transport-cc\\r\\na=rtcp-fb:96 ccm fir\\r\\na=rtcp-fb:96 nack\\r\\na=rtcp-fb:96 nack pli\\r\\na=rtpmap:97 rtx/90000\\r\\na=fmtp:97 apt=96\\r\\na=rtpmap:103 H264/90000\\r\\na=rtcp-fb:103 goog-remb\\r\\na=rtcp-fb:103 transport-cc\\r\\na=rtcp-fb:103 ccm fir\\r\\na=rtcp-fb:103 nack\\r\\na=rtcp-fb:103 nack pli\\r\\na=fmtp:103 level-asymmetry-allowed=1;packetization-mode=1;profile-level-id=42001f\\r\\na=rtpmap:104 rtx/90000\\r\\na=fmtp:104 apt=103\\r\\na=rtpmap:107 H264/90000\\r\\na=rtcp-fb:107 goog-remb\\r\\na=rtcp-fb:107 transport-cc\\r\\na=rtcp-fb:107 ccm fir\\r\\na=rtcp-fb:107 nack\\r\\na=rtcp-fb:107 nack pli\\r\\na=fmtp:107 level-asymmetry-allowed=1;packetization-mode=0;profile-level-id=42001f\\r\\na=rtpmap:108 rtx/90000\\r\\na=fmtp:108 apt=107\\r\\na=rtpmap:109 H264/90000\\r\\na=rtcp-fb:109 goog-remb\\r\\na=rtcp-fb:109 transport-cc\\r\\na=rtcp-fb:109 ccm fir\\r\\na=rtcp-fb:109 nack\\r\\na=rtcp-fb:109 nack pli\\r\\na=fmtp:109 level-asymmetry-allowed=1;packetization-mode=1;profile-level-id=42e01f\\r\\na=rtpmap:114 rtx/90000\\r\\na=fmtp:114 apt=109\\r\\na=rtpmap:115 H264/90000\\r\\na=rtcp-fb:115 goog-remb\\r\\na=rtcp-fb:115 transport-cc\\r\\na=rtcp-fb:115 ccm fir\\r\\na=rtcp-fb:115 nack\\r\\na=rtcp-fb:115 nack pli\\r\\na=fmtp:115 level-asymmetry-allowed=1;packetization-mode=0;profile-level-id=42e01f\\r\\na=rtpmap:116 rtx/90000\\r\\na=fmtp:116 apt=115\\r\\na=rtpmap:117 H264/90000\\r\\na=rtcp-fb:117 goog-remb\\r\\na=rtcp-fb:117 transport-cc\\r\\na=rtcp-fb:117 ccm fir\\r\\na=rtcp-fb:117 nack\\r\\na=rtcp-fb:117 nack pli\\r\\na=fmtp:117 level-asymmetry-allowed=1;packetization-mode=1;profile-level-id=4d001f\\r\\na=rtpmap:118 rtx/90000\\r\\na=fmtp:118 apt=117\\r\\na=rtpmap:39 H264/90000\\r\\na=rtcp-fb:39 goog-remb\\r\\na=rtcp-fb:39 transport-cc\\r\\na=rtcp-fb:39 ccm fir\\r\\na=rtcp-fb:39 nack\\r\\na=rtcp-fb:39 nack pli\\r\\na=fmtp:39 level-asymmetry-allowed=1;packetization-mode=0;profile-level-id=4d001f\\r\\na=rtpmap:40 rtx/90000\\r\\na=fmtp:40 apt=39\\r\\na=rtpmap:45 AV1/90000\\r\\na=rtcp-fb:45 goog-remb\\r\\na=rtcp-fb:45 transport-cc\\r\\na=rtcp-fb:45 ccm fir\\r\\na=rtcp-fb:45 nack\\r\\na=rtcp-fb:45 nack pli\\r\\na=fmtp:45 level-idx=5;profile=0;tier=0\\r\\na=rtpmap:46 rtx/90000\\r\\na=fmtp:46 apt=45\\r\\na=rtpmap:98 VP9/90000\\r\\na=rtcp-fb:98 goog-remb\\r\\na=rtcp-fb:98 transport-cc\\r\\na=rtcp-fb:98 ccm fir\\r\\na=rtcp-fb:98 nack\\r\\na=rtcp-fb:98 nack pli\\r\\na=fmtp:98 profile-id=0\\r\\na=rtpmap:99 rtx/90000\\r\\na=fmtp:99 apt=98\\r\\na=rtpmap:100 VP9/90000\\r\\na=rtcp-fb:100 goog-remb\\r\\na=rtcp-fb:100 transport-cc\\r\\na=rtcp-fb:100 ccm fir\\r\\na=rtcp-fb:100 nack\\r\\na=rtcp-fb:100 nack pli\\r\\na=fmtp:100 profile-id=2\\r\\na=rtpmap:101 rtx/90000\\r\\na=fmtp:101 apt=100\\r\\na=rtpmap:119 H264/90000\\r\\na=rtcp-fb:119 goog-remb\\r\\na=rtcp-fb:119 transport-cc\\r\\na=rtcp-fb:119 ccm fir\\r\\na=rtcp-fb:119 nack\\r\\na=rtcp-fb:119 nack pli\\r\\na=fmtp:119 level-asymmetry-allowed=1;packetization-mode=1;profile-level-id=64001f\\r\\na=rtpmap:120 rtx/90000\\r\\na=fmtp:120 apt=119\\r\\na=rtpmap:49 H265/90000\\r\\na=rtcp-fb:49 goog-remb\\r\\na=rtcp-fb:49 transport-cc\\r\\na=rtcp-fb:49 ccm fir\\r\\na=rtcp-fb:49 nack\\r\\na=rtcp-fb:49 nack pli\\r\\na=fmtp:49 level-id=123;profile-id=1;tier-flag=0;tx-mode=SRST\\r\\na=rtpmap:50 rtx/90000\\r\\na=fmtp:50 apt=49\\r\\na=rtpmap:123 red/90000\\r\\na=rtpmap:124 rtx/90000\\r\\na=fmtp:124 apt=123\\r\\na=rtpmap:125 ulpfec/90000\\r\\na=ssrc-group:FID 1545477657 2132088959\\r\\na=ssrc:1545477657 cname:3hxgG22pa6B8GcTc\\r\\na=ssrc:1545477657 msid:c3409cb8-37ae-4209-97ac-3e0ab26b63d1 4b969135-3d63-455d-8c00-c4baf8f90a8b\\r\\na=ssrc:2132088959 cname:3hxgG22pa6B8GcTc\\r\\na=ssrc:2132088959 msid:c3409cb8-37ae-4209-97ac-3e0ab26b63d1 4b969135-3d63-455d-8c00-c4baf8f90a8b\\r\\n\",\"type\":\"offer\"}', '2025-10-22 15:44:19'),
(77, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:2627546142 1 udp 2122262783 2001:4456:c15:5a00:4d17:2239:562f:388a 49667 typ host generation 0 ufrag kwUk network-id 2 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(78, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:1099682433 1 udp 2122129151 192.168.1.229 49666 typ host generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(79, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:2457276672 1 udp 2122197247 2001:4456:c15:5a00:f1b0:4a56:858:fcca 49668 typ host generation 0 ufrag kwUk network-id 3 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(80, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:1099682433 1 udp 2122129151 192.168.1.229 49669 typ host generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(81, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:2627546142 1 udp 2122262783 2001:4456:c15:5a00:4d17:2239:562f:388a 49670 typ host generation 0 ufrag kwUk network-id 2 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(82, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:2457276672 1 udp 2122197247 2001:4456:c15:5a00:f1b0:4a56:858:fcca 49671 typ host generation 0 ufrag kwUk network-id 3 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(83, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:4005639103 1 udp 1685921535 119.111.182.102 49819 typ srflx raddr 192.168.1.229 rport 49666 generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(84, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:4005639103 1 udp 1685921535 119.111.182.102 49820 typ srflx raddr 192.168.1.229 rport 49669 generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(85, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:3797077638 1 tcp 1518283007 2001:4456:c15:5a00:4d17:2239:562f:388a 9 typ host tcptype active generation 0 ufrag kwUk network-id 2 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(86, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:1061433369 1 tcp 1518149375 192.168.1.229 9 typ host tcptype active generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(87, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:3971543960 1 tcp 1518217471 2001:4456:c15:5a00:f1b0:4a56:858:fcca 9 typ host tcptype active generation 0 ufrag kwUk network-id 3 network-cost 10\",\"sdpMid\":\"0\",\"sdpMLineIndex\":0,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(88, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:1061433369 1 tcp 1518149375 192.168.1.229 9 typ host tcptype active generation 0 ufrag kwUk network-id 1 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(89, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:3797077638 1 tcp 1518283007 2001:4456:c15:5a00:4d17:2239:562f:388a 9 typ host tcptype active generation 0 ufrag kwUk network-id 2 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19'),
(90, 9, 21, 20, 'candidate', '{\"candidate\":\"candidate:3971543960 1 tcp 1518217471 2001:4456:c15:5a00:f1b0:4a56:858:fcca 9 typ host tcptype active generation 0 ufrag kwUk network-id 3 network-cost 10\",\"sdpMid\":\"1\",\"sdpMLineIndex\":1,\"usernameFragment\":\"kwUk\"}', '2025-10-22 15:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_unsent` tinyint(1) DEFAULT 0,
  `deleted_by_sender` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by_receiver` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `is_unsent`, `deleted_by_sender`, `deleted_by_receiver`) VALUES
(1, 16, 15, 'wakwak', '2025-09-05 05:24:54', 0, 0, 0),
(2, 16, 17, 'wakwak', '2025-09-05 05:26:43', 0, 0, 0),
(3, 16, 17, 'pangittt', '2025-09-05 05:26:53', 0, 0, 0),
(4, 17, 16, '', '2025-09-05 05:29:32', 1, 0, 0),
(5, 17, 16, '', '2025-09-05 05:54:48', 1, 0, 0),
(7, 16, 17, 'samad ka??', '2025-09-05 05:58:23', 0, 0, 0),
(8, 17, 16, '', '2025-09-05 06:04:33', 1, 0, 0),
(9, 16, 17, 'asdqweqwe', '2025-09-05 06:04:53', 0, 0, 0),
(10, 16, 14, 'sadqweqwe', '2025-09-05 06:04:59', 0, 0, 0),
(21, 16, 17, 'sad', '2025-09-05 06:25:32', 1, 0, 0),
(22, 17, 16, 'haysssss ana2 lang', '2025-09-05 06:27:27', 0, 0, 0),
(23, 17, 16, 'buanggggggggg', '2025-09-05 06:29:04', 0, 0, 0),
(24, 16, 17, 'ikaw sad!', '2025-09-05 06:29:14', 0, 0, 0),
(25, 16, 17, 'wakwak', '2025-09-05 06:42:21', 0, 0, 0),
(26, 16, 17, 'sadqweqwe', '2025-09-05 06:47:01', 0, 0, 0),
(27, 17, 16, '', '2025-09-05 06:47:12', 1, 0, 0),
(28, 17, 16, 'hi poo', '2025-09-05 07:14:12', 0, 0, 0),
(29, 17, 16, 'kumusta kana akii?', '2025-09-05 07:14:19', 0, 0, 0),
(30, 16, 17, 'okay lang naman! ikaw ba?', '2025-09-05 07:14:27', 0, 0, 0),
(31, 16, 17, 'asdqweqwjasd', '2025-09-05 07:28:39', 0, 0, 0),
(32, 17, 14, 'hi', '2025-09-05 10:01:38', 0, 0, 0),
(33, 17, 16, 'edi wow', '2025-09-05 10:02:19', 0, 0, 0),
(34, 17, 16, 'wakwak', '2025-09-06 05:26:45', 0, 0, 0),
(35, 17, 16, 'pangitttt', '2025-09-06 05:26:55', 0, 0, 0),
(36, 16, 17, 'sakauysss', '2025-09-06 05:26:59', 0, 0, 0),
(37, 17, 16, 'hiii', '2025-09-09 15:11:46', 0, 0, 0),
(38, 16, 17, '', '2025-09-09 15:16:48', 1, 0, 0),
(39, 16, 17, 'pushhh', '2025-09-09 15:17:03', 0, 0, 0),
(40, 16, 17, 'ana ana lang atake', '2025-09-09 15:17:47', 0, 0, 0),
(41, 16, 17, 'chuyy', '2025-09-09 15:22:04', 0, 0, 0),
(42, 17, 16, 'wakwakkkkkK', '2025-09-09 15:22:23', 0, 0, 0),
(43, 16, 17, '', '2025-09-09 15:26:08', 1, 0, 0),
(44, 16, 17, 'passss', '2025-09-09 15:45:11', 0, 0, 0),
(45, 17, 16, 'anana', '2025-09-09 15:57:24', 0, 0, 0),
(46, 17, 16, 'wqeqwdasd', '2025-09-09 15:57:28', 0, 0, 0),
(47, 17, 18, 'airawaw', '2025-09-09 16:00:23', 0, 0, 0),
(48, 17, 16, 'wakwak', '2025-09-10 07:30:46', 0, 0, 0),
(49, 17, 16, '', '2025-09-10 07:30:56', 1, 0, 0),
(50, 21, 20, 'pangit', '2025-10-22 15:06:14', 0, 0, 0),
(51, 21, 20, 'sakauy', '2025-10-22 15:29:15', 0, 0, 0),
(52, 21, 20, 'pass', '2025-10-22 15:29:20', 0, 0, 0),
(53, 20, 21, '', '2025-10-22 15:29:27', 1, 0, 0),
(54, 21, 20, 'sadka', '2025-10-22 15:47:06', 0, 0, 0),
(55, 20, 21, 'hiii puuu', '2025-10-22 16:05:59', 0, 0, 0),
(56, 20, 21, 'paposas ka?', '2025-10-22 16:06:11', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `deleted_messages`
--

CREATE TABLE `deleted_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_messages`
--

INSERT INTO `deleted_messages` (`id`, `user_id`, `message_id`) VALUES
(12, 16, 2),
(11, 16, 3),
(21, 16, 4),
(6, 16, 5),
(19, 16, 7),
(22, 16, 8),
(20, 16, 9),
(24, 16, 10),
(9, 16, 22),
(10, 16, 23),
(8, 16, 24),
(2, 16, 25),
(1, 16, 26),
(23, 16, 27),
(25, 16, 39),
(13, 17, 2),
(17, 17, 3),
(18, 17, 7),
(14, 17, 9),
(15, 17, 21),
(7, 17, 22),
(5, 17, 23),
(4, 17, 24),
(3, 17, 25),
(16, 17, 26);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('add','remove','adjust') NOT NULL,
  `quantity` int(11) NOT NULL,
  `old_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `description`, `created_at`) VALUES
(1, 'Red Horse', 120.00, 0, '1L', '2025-05-27 14:26:42'),
(4, 'Sardines (555 or Ligo)', 20.00, 2, '155g can ', '2025-05-27 16:11:49'),
(6, 'Lucky Me! Pancit Canton', 13.00, 0, 'Per pack', '2025-05-27 18:04:54'),
(7, 'Argentina Corned Beef', 35.00, 7, '150g can', '2025-05-27 18:06:20'),
(8, 'Piattos', 10.00, 6, 'Small pack', '2025-05-27 18:06:41'),
(9, 'Chippy', 10.00, 9, 'Small Pack', '2025-05-27 18:07:05'),
(10, 'Rebisco Crackers', 8.00, 10, 'Single Pack', '2025-05-27 18:07:29'),
(11, 'Hansel Mocha Biscuits', 8.00, 10, 'Small Pack', '2025-05-27 18:07:57'),
(12, 'White Rabbit Candy', 1.00, 4, 'Per piece', '2025-05-27 18:08:16'),
(13, 'Maxx Menthol Candy', 1.00, 8, 'Per piece', '2025-05-27 18:08:31'),
(14, 'Tang Orange Powder', 8.00, 10, 'Sachet (25g)', '2025-05-27 18:08:51'),
(15, 'Milo Powder', 10.00, 10, 'Sachet (22g)', '2025-05-27 18:09:16'),
(16, 'Nescafé Classic', 7.00, 10, 'Sachet (2g)', '2025-05-27 18:09:36'),
(17, 'Kopiko Blanca', 8.00, 10, 'Sachet (27g)', '2025-05-27 18:09:59'),
(18, 'Bottled Water (Nature Spring)', 15.00, 9, '500ml', '2025-05-27 18:10:35'),
(19, 'Coke/Sprite (Glass bottle)', 15.00, 8, '250ml', '2025-05-27 18:10:57'),
(20, 'Cooking Oil', 25.00, 10, '250ml (in bottle/plastic)', '2025-05-27 18:11:17'),
(21, 'Ice cubes', 15.00, 3, 'Per pack', '2025-05-28 02:39:59'),
(22, '     Stick Bread', 45.00, 4, '200g', '2025-05-28 05:36:39'),
(25, 'Colt45s', 35.00, 150, '500ml (in bottle)', '2025-09-05 07:15:58');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `cash_received` decimal(10,2) NOT NULL,
  `change_given` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `receipt_number`, `total_amount`, `cash_received`, `change_given`, `created_at`) VALUES
(14, 'RECEIPT-683ff16704356', 24000.00, 25000.00, 1000.00, '2025-06-04 07:10:31'),
(15, 'RECEIPT-68b2d61a5c9fc', 75000.00, 75500.00, 500.00, '2025-08-30 10:44:42'),
(16, 'RECEIPT-68ba8e3aeea47', 175.00, 200.00, 25.00, '2025-09-05 07:16:10'),
(17, 'RECEIPT-68ba970197079', 60.00, 60.00, 0.00, '2025-09-05 07:53:37'),
(18, 'RECEIPT-68ba9f7105bca', 30.00, 30.00, 0.00, '2025-09-05 08:29:37'),
(19, 'RECEIPT-68bab77227500', 105.00, 105.00, 0.00, '2025-09-05 10:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_name`, `quantity`, `price`, `created_at`) VALUES
(29, 14, 'Redhorse', 200, 120.00, '2025-06-04 07:10:31'),
(30, 15, 'Redhorse', 600, 125.00, '2025-08-30 10:44:42'),
(31, 16, 'Colt45s', 5, 35.00, '2025-09-05 07:16:10'),
(32, 17, 'Sardines (555 or Ligo)', 3, 20.00, '2025-09-05 07:53:37'),
(33, 18, 'Piattos', 3, 10.00, '2025-09-05 08:29:37'),
(34, 19, 'Argentina Corned Beef', 3, 35.00, '2025-09-05 10:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','cashier') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`, `created_at`, `last_active`) VALUES
(20, '', 'markywakwak', 'mansuetomarky@gmail.com', '$2y$10$CsLAihgVPiq4G4.Ek9xIW.RtGJ8tPD9wsgyYqWaNbYehJ3BaeDivG', 'user', '2025-10-22 14:38:04', '2025-10-23 00:13:31'),
(21, '', 'abbywakwak', 'hunzkie123@gmail.com', '$2y$10$QOYimYj2Jt.kOLXYbvauI..WgeDcrdu8gBujqSHrBUP6t4iyOch4K', 'user', '2025-10-22 15:05:57', '2025-10-23 00:12:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `call_invitations`
--
ALTER TABLE `call_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `caller_id` (`caller_id`);

--
-- Indexes for table `call_signals`
--
ALTER TABLE `call_signals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_delete` (`user_id`,`message_id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_token` (`token`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call_invitations`
--
ALTER TABLE `call_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `call_signals`
--
ALTER TABLE `call_signals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=470;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD CONSTRAINT `inventory_log_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
