<?php
// login as staff to get token
$ch = curl_init('http://127.0.0.1:8080/api/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => 'staff_barista', 'password' => 'password123']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
$res = curl_exec($ch);
$login_data = json_decode($res, true);
$token = $login_data['token'];

echo "Token: $token\n";

function hit($url) {
    global $token;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
    $res = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "URL: $url\nStatus: $status\nResponse: " . substr($res, 0, 500) . "\n\n";
}

hit('http://127.0.0.1:8080/api/karyawan/pos/menus');
