<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'lb_pdo_workers';
$username = 'test';
$password = '0000';


$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $pdo->prepare("INSERT INTO ajax_logs 
                          (request_time, browser_info, user_agent, latitude, longitude, 
                           location_accuracy, request_type, request_data, ip_address, session_id) 
                          VALUES 
                          (:request_time, :browser_info, :user_agent, :latitude, :longitude, 
                           :location_accuracy, :request_type, :request_data, :ip_address, :session_id)");

    function getClientIP() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    $params = [
        ':request_time' => $data['request_time'] ?? date('Y-m-d H:i:s'),
        ':browser_info' => $data['browser_info'] ?? null,
        ':user_agent' => $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
        ':latitude' => $data['latitude'] ?? null,
        ':longitude' => $data['longitude'] ?? null,
        ':location_accuracy' => $data['location_accuracy'] ?? null,
        ':request_type' => $data['request_type'] ?? null,
        ':request_data' => $data['request_data'] ?? null,
        ':ip_address' => getClientIP(),
        ':session_id' => $data['session_id'] ?? null
    ];

    $stmt->execute($params);
    
    $logId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'log_id' => $logId,
        'message' => 'Request logged successfully',
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'success' => false
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'General error: ' . $e->getMessage(),
        'success' => false
    ]);
}
?>