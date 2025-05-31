<?php
$host = 'localhost';
$dbname = 'lb_pdo_workers';
$username = 'test';
$password = '0000';
$chief = $_POST['chief_name'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(w.ID_Worker) AS subordinates 
                           FROM WORKER w 
                           JOIN DEPARTMENT d ON w.FID_Department = d.ID_Department 
                           WHERE d.chief = :chief");

    $stmt->bindParam(':chief', $chief, PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        echo "<div>Chief: " . htmlspecialchars($chief) . "</div>";
        echo "<div>Subordinates: " . $res['subordinates'] . "</div>";
    } else {
        echo "<div>No subordinates found for chief: " . htmlspecialchars($chief) . "</div>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>