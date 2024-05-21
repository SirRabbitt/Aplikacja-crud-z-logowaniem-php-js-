<?php
require_once "pdo.php";

if (isset($_GET['term'])) {
    $term = $_GET['term'] . '%';
    
    $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :term');
    $stmt->execute(array(':term' => $term));
    
    $schools = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $schools[] = $row['name'];
    }
    
    echo json_encode($schools);
}
?>
