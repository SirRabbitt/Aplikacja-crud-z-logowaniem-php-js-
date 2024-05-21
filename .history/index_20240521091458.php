<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>kamil król - Lista Samochodów</title>
</head>
<body>
<h1>Lista Samochodów</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}

echo '<table border="1">'."\n";
echo "<tr><th>Marka</th><th>Model</th><th>Rok</th><th>Przebieg</th><th>Akcje</th></tr>";

$stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(htmlentities($row['make']));
    echo("</td><td>");
    echo(htmlentities($row['model']));
    echo("</td><td>");
    echo(htmlentities($row['year']));
    echo("</td><td>");
    echo(htmlentities($row['mileage']));
    echo("</td><td>");
    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edytuj</a> / ');
    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Usuń</a>');
    echo("</td></tr>\n");
}

echo "</table>\n";
?>
<br>
<a href="add.php">Dodaj nowy</a>
<br>
<a href="logout.php">Wyloguj</a>
</body>
</html>
