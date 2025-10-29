<?php
require_once __DIR__ . '/sms_config.php';

function sms_log($line) {
    if (!defined('SMS_DEV_ENABLE_LOGS') || !SMS_DEV_ENABLE_LOGS) return;
    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL;
    @file_put_contents(__DIR__ . '/sms_log.txt', $entry, FILE_APPEND);
}

function send_sms($to, $message) {
    $to = preg_replace('/[^0-9+]/', '', $to);
    if ($to === '' || $message === '') { return [false, 'Invalid to/message']; }
    if (!defined('SMS_API_KEY') || SMS_API_KEY === '') {
        sms_log('ERROR: Missing SMS_API_KEY');
        return [false, 'Missing SMS API key. Set SMS_API_KEY in sms_config.php'];
    }

    $payload = [
        'apikey' => SMS_API_KEY,
        'number' => $to,
        'message' => $message,
        'sendername' => SMS_SENDER
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, SMS_PROVIDER_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    sms_log('REQUEST: ' . json_encode($payload));
    sms_log('RESPONSE: HTTP ' . $httpCode . ' err=' . ($err ?: 'none') . ' body=' . $response);
    curl_close($ch);

    if ($err) {
        return [false, $err];
    }
    if ($httpCode < 200 || $httpCode >= 300) {
        return [false, 'HTTP ' . $httpCode . ' ' . $response];
    }
    return [true, $response];
}
