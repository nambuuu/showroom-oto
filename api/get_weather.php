<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

// Tọa độ Hà Nội để lấy thời tiết thời gian thực từ Open-Meteo
$url = 'https://api.open-meteo.com/v1/forecast?latitude=21.0285&longitude=105.8542&current_weather=true';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout ngắn để tránh treo trang
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response && $httpcode === 200) {
    echo $response;
} else {
    // Trả về mặc định nếu API ngoài bị lỗi hoặc timeout
    echo json_encode([
        'current_weather' => [
            'temperature' => 28.0
        ],
        'is_fallback' => true
    ]);
}
?>
