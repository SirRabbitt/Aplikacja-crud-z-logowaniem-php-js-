<?php
session_start();
require_once "pdo.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    } elseif (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Adres e-mail musi zawierać @";
        header("Location: add.php");
        return;
    } else {
        $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        ));
        $_SESSION['success'] = "added";
        header("Location: index.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae</title>
</head>
<body>
<h1>Dodaj Profil</h1>
<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>".$_SESSION['error']."</p>";
    unset($_SESSION['error']);
}
?>
<form method="POST">
<p>Imię: <input type="text" name="first_name"></p>
<p>Nazwisko: <input type="text" name="last_name"></p>
<p>Adres e-mail: <input type="text" name="email"></p>
<p>Tytuł: <input type="text" name="headline"></p>
<p>Podsumowanie: <textarea name="summary"></textarea></p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Anuluj">
</form>
</body>
</html>
