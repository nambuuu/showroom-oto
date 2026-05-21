<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

$lat = isset($_GET['lat']) ? $_GET['lat'] : '21.0285';
$lng = isset($_GET['lng']) ? $_GET['lng'] : '105.8542';

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current_weather=true";

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    if ($response && $httpCode >= 200 && $httpCode < 300) {
        echo $response;
        exit;
    }

    throw new Exception('Weather API returned HTTP ' . $httpCode);
} catch (Exception $e) {
    error_log('Weather API error: ' . $e->getMessage());
    echo json_encode([
        'current_weather' => [
            'temperature' => 28.0,
        ],
        'is_fallback' => true,
    ]);
}
