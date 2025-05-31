<?php
header('Content-Type: text/xml');
$host = 'localhost';
$dbname = 'lb_pdo_workers';
$username = 'test';
$password = '0000';
$project_name = $_POST['project_name'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT SUM(DATEDIFF(w.time_end, w.time_start)) AS time_spent
                           FROM PROJECT p
                           LEFT JOIN WORK w ON w.FID_PROJECTS = p.ID_PROJECTS
                           WHERE p.name = :project_name");

    $stmt->bindParam(':project_name', $project_name, PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<project_data>';
    echo '<project_name>' . htmlspecialchars($project_name) . '</project_name>';
    
    if ($res && $res['time_spent'] !== null) {
        echo '<time_spent>' . $res['time_spent'] . '</time_spent>';
        echo '<status>success</status>';
    } else {
        echo '<time_spent>0</time_spent>';
        echo '<status>no_data</status>';
    }
    
    echo '</project_data>';

} catch (PDOException $e) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<project_data>';
    echo '<error>' . $e->getMessage() . '</error>';
    echo '<status>error</status>';
    echo '</project_data>';
}
?>