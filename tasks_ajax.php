<?php
header('Content-Type: application/json');
$host = 'localhost';
$dbname = 'lb_pdo_workers';
$username = 'test';
$password = '0000';

$project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';
$date = isset($_POST['task_date']) ? $_POST['task_date'] : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT w.FID_Worker, w.FID_Projects, w.time_start, w.time_end, w.description
                            FROM WORK w
                            JOIN PROJECT p ON w.FID_Projects = p.ID_Projects
                            WHERE p.name = :project_name AND w.date = :date");
                            
    $stmt->bindParam(':project_name', $project_name, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>