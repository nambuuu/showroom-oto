<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Cho phép frontend gọi không bị CORS

// Mặc định Hà Nội nếu không có tham số
$lat = isset($_GET['lat']) ? $_GET['lat'] : '21.0285';
$lng = isset($_GET['lng']) ? $_GET['lng'] : '105.8542';

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current_weather=true";

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // Vô hiệu hoá xác thực SSL nếu môi trường XAMPP local gặp vấn đề certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        echo $response;
    } else {
        http_response_code($httpCode);
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lấy dữ liệu thời tiết', 'open_meteo_response' => json_decode($response)]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
