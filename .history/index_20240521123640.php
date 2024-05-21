<?php
session_start();
require_once "pdo.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
}

$stmt = $pdo->query("SELECT * FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Twoje imię i nazwisko</title>
</head>
<body>
<h1>Lista Profili</h1>
<?php
foreach ($rows as $row) {
    echo "<p><a href='view.php?profile_id=".$row['profile_id']."'>".$row['first_name']." ".$row['last_name']."</a></p>";
}
?>
<p><a href="add.php">Dodaj nowy profil</a></p>
<p><a href="logout.php">Wyloguj</a></p>
</body>
</html>
