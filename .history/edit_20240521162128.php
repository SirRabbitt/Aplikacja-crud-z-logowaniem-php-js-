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

// Pobierz informacje o edukacji
$stmt = $pdo->prepare("SELECT Education.year, Institution.name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE Education.profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz listę szkół z bazy danych
$stmt = $pdo->query('SELECT * FROM Institution');
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Edytuj Profil - <?php echo htmlentities($row['first_name']." ".$row['last_name']); ?></title>
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
<p>Edukacja:
    <select name="edu_school">
        <?php foreach ($institutions as $institution) : ?>
            <option value="<?php echo htmlentities($institution['institution_id']); ?>" <?php if(isset($educations[0]['name']) && $educations[0]['name'] == $institution['name']) echo 'selected="selected"'; ?>><?php echo htmlentities($institution['name']); ?></option>
        <?php endforeach; ?>
    </select>
    Rok: <input type="text" name="edu_year" value="<?php if(isset($educations[0]['year'])) echo htmlentities($educations[0]['year']); ?>">
</p>
<input type="hidden" name="profile_id" value="<?php echo $row['profile_id']; ?>">
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Anuluj">
</form>
</body>
</html>
