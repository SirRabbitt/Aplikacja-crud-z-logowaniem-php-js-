<?php
session_start();
require_once "pdo.php";

// Sprawdzenie, czy użytkownik jest już zalogowany
if ( isset($_SESSION['user_id']) ) {
    header('Location: index.php');
    return;
}

// Obsługa logowania
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset($_SESSION['user_id']); // W przypadku niepowodzenia logowania, zapobiega pozostawaniu zalogowanym

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Wszystkie pola są wymagane";
        header('Location: login.php');
        return;
    } else {
        $sql = "SELECT user_id, name FROM users WHERE email = :email AND password = :pass";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':email' => $_POST['email'],
            ':pass' => $_POST['pass'])
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row !== false ) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['success'] = "Zalogowano jako ".$row['name'];
            header('Location: index.php');
            return;
        } else {
            $_SESSION['error'] = "Nieprawidłowe dane logowania";
            header('Location: login.php');
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Twoje Imię - Logowanie</title>
</head>
<body>
<h1>Logowanie</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
    <p>Adres email:
        <input type="text" name="email"></p>
    <p>Hasło:
        <input type="password" name="pass"></p>
    <input type="submit" value="Zaloguj">
</form>
</body>
</html>
