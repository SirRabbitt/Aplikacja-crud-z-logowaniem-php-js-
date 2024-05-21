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

        // Aktualizuj edukację
        if (isset($_POST['edu_school']) && isset($_POST['edu_year'])) {
            $edu_school = $_POST['edu_school'];
            $edu_year = $_POST['edu_year'];

            $stmt = $pdo->prepare('UPDATE Education SET institution_id = :iid, year = :year WHERE profile_id = :pid');
            $stmt->execute(array(
                ':iid' => $edu_school,
                ':year' => $edu_year,
                ':pid' => $_POST['profile_id']
            ));
        }

        $_SESSION['success'] = "Profil zaktualizowany pomyślnie";
        header("Location: index.php");
        return;
    }
}
?>
