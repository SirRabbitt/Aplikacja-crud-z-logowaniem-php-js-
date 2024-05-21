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
    $_SESSION['error'] = "Brak dostępu do edycji tego profilu";
    header("Location: index.php");
    return;
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "Wszystkie pola są wymagane";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    } elseif (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Adres e-mail musi zawierać @";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    } else {
        $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid');
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => $_POST['profile_id']
        ));
        $_SESSION['success'] = "Profil zaktualizowany pomyślnie";
        header("Location: index.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edytuj Profil</title>
</head>
<body>
<h1>Edytuj Profil</h1>
<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>".$_SESSION['error']."</p>";
    unset($_SESSION['error']);
}
?>
<form method="POST">
<p>Imię: <input type="text" name="first_name" value="<?php echo htmlentities($row['first_name']); ?>"></p>
<p>Nazwisko: <input type="text" name="last_name" value="<?php echo htmlentities($row['last_name']); ?>"></p>
<p>Adres e-mail: <input type="text" name="email" value="<?php echo htmlentities($row['email']); ?>"></p>
<p>Tytuł: <input type="text" name="headline" value="<?php echo htmlentities($row['headline']); ?>"></p>
<p>Podsumowanie: <textarea name="summary"><?php echo htmlentities($row['summary']); ?></textarea></p>
<input type="hidden" name="profile_id" value="<?php echo $row['profile_id']; ?>">
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Anuluj">
</form>
</body>
</html>
