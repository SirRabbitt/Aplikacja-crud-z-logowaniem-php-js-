<?php
session_start();
require_once "pdo.php";

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "Wszystkie pola są wymagane";
        header("Location: login.php");
        return;
    } else {
        $check = hash('md5', 'XyZzy12*_'.$_POST['pass']);
        $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :em AND password = :pw");
        $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Niepoprawny adres e-mail lub hasło";
            header("Location: login.php");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae</title>
<script>
function showError() {
    alert("Niepoprawny adres e-mail lub hasło");
}
</script>
</head>
<body>

<?php
if (isset($_SESSION['error'])) {
    echo "<script>showError();</script>";
    unset($_SESSION['error']);
}
?>
<h2>Please log in</h2>
<form method="POST">
<p>Adres e-mail: <input type="text" name="email"></p>
<p>Hasło: <input type="password" name="pass"></p>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Anuluj">
</form>
</body>
</html>
