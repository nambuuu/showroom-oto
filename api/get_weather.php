<?php
header('Content-Type: application/json');

// Tọa độ Hà Nội để lấy thời tiết thời gian thực từ Open-Meteo
$url = 'https://api.open-meteo.com/v1/forecast?latitude=21.0285&longitude=105.8542&current_weather=true';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    echo $response;
} else {
    // Trả về mặc định nếu API ngoài bị lỗi
    echo json_encode([
        'current_weather' => [
            'temperature' => 28.0
        ]
    ]);
}
?>
