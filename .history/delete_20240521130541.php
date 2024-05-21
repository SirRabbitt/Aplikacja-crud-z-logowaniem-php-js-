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

// Sprawdź czy użytkownik jest właścicielem wpisu
if ($row['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "Brak dostępu do usunięcia tego profilu";
    header("Location: index.php");
    return;
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_POST['profile_id']));
    $_SESSION['success'] = "Profil usunięty pomyślnie";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae</title>
</head>
<body>
<h1>Czy na pewno chcesz usunąć ten profil?</h1>
<form method="POST">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Anuluj">
<input type="hidden" name="profile_id" value="<?php echo $row['profile_id']; ?>">
</form>
</body>
</html>
