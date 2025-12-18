<?php
header('Content-Type: application/json');
require __DIR__ . '/config.php';

$nonce  = gmdate('c');
$secret = hash('sha512', $nonce . APP_KEY);

$payload = json_encode([
    'app_id'     => APP_ID,
    'nonce'      => $nonce,
    'secret'     => $secret,
    'grant_type' => 'client_credentials'
]);

$ch = curl_init('https://apis.sandbox.globalpay.com/ucp/accesstoken');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'X-GP-Version: 2021-03-22'
    ],
    CURLOPT_POSTFIELDS     => $payload
]);

$response = curl_exec($ch);

$data = json_decode($response, true);
if (!is_array($data) || !isset($data['token'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'access_token_failed',
        'raw'   => $response
    ]);
    exit;
}

echo json_encode(['accessToken' => $data['token']]);
exit;
