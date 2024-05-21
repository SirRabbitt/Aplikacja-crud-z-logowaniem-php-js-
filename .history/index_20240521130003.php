<?php
session_start();
require_once "pdo.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['name'])) {
    echo "<p> <a href='login.php'>Please log in</a> to access this page.</p>";
   
}
// Pobierz listę profili
$stmt = $pdo->query("SELECT * FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae</title>

</head>
<body>
<h1>Lista Profili</h1>
<table>
    <tr>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Akcje</th>
    </tr>
    <?php foreach ($rows as $row) : ?>
    <tr>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td>
            <a href="view.php?profile_id=<?php echo $row['profile_id']; ?>">Wyświetl</a> |
            <a href="edit.php?profile_id=<?php echo $row['profile_id']; ?>">Edytuj</a> |
            <a href="delete.php?profile_id=<?php echo $row['profile_id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten profil?')">Usuń</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="add.php">Add New Entry</a></p>
<p><a href="logout.php">Wyloguj</a></p>
</body>
</html>
