<?php
session_start();
require_once "pdo.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Sprawdź czy przekazano parametr profile_id
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Brak identyfikatora profilu";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Sprawdź czy wpis istnieje
if ($row === false) {
    $_SESSION['error'] = "Nieprawidłowy identyfikator profilu";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae - <?php echo htmlentities($row['first_name']." ".$row['last_name']); ?></title>
</head>
<body>
<h1>Profil</h1>
<p>Imię: <?php echo htmlentities($row['first_name']); ?></p>
<p>Nazwisko: <?php echo htmlentities($row['last_name']); ?></p>
<p>Adres e-mail: <?php echo htmlentities($row['email']); ?></p>
<p>Tytuł: <?php echo htmlentities($row['headline']); ?></p>
<p>Podsumowanie: <?php echo htmlentities($row['summary']); ?></p>
</body>
</html>
