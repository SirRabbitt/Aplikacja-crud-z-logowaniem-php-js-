<?php
session_start();
require_once "pdo.php";

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
    if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1 ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    } elseif ( ! is_numeric($_POST['year']) ) {
        $_SESSION['error'] = "Year must be an integer";
        header("Location: add.php");
        return;
    } elseif ( ! is_numeric($_POST['mileage']) ) {
        $_SESSION['error'] = "Mileage must be an integer";
        header("Location: add.php");
        return;
    } else {
        $sql = "INSERT INTO autos (make, model, year, mileage) VALUES (:make, :model, :year, :mileage)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage'])
        );
        $_SESSION['success'] = "Record added";
        header("Location: index.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Twoje Imię - Dodaj samochód</title>
</head>
<body>
<h1>Dodaj nowy samochód</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>Marka:
<input type="text" name="make"></p>
<p>Model:
<input type="text" name="model"></p>
<p>Rok:
<input type="text" name="year"></p>
<p>Przebieg:
<input type="text" name="mileage"></p>
<input type="submit" value="Dodaj">
<input type="submit" name="cancel" value="Anuluj">
</form>
</body>
</html>
