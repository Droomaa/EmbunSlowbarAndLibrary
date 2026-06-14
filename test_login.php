<?php
$data = json_encode(['username' => 'owner_sandro', 'password' => 'password123']);
$ch = curl_init('http://127.0.0.1:8080/api/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
echo curl_exec($ch);
