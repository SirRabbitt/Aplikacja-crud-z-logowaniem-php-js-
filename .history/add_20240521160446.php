<?php
session_start();
require_once "pdo.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Funkcja dodająca nową pozycję profilu
function insertPosition($pdo, $profile_id, $rank, $year, $description) {
    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $description
    ));
}

// Funkcja dodająca nową edukację
// Funkcja dodająca nową edukację
function insertEducation($pdo, $profile_id, $institution_name, $rank, $year) {
    // Sprawdź czy istnieje instytucja o podanej nazwie
    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
    $stmt->execute(array(':name' => $institution_name));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        // Jeśli instytucja nie istnieje, dodaj ją do tabeli Institution
        $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
        $stmt->execute(array(':name' => $institution_name));
        $institution_id = $pdo->lastInsertId();
    } else {
        // Jeśli instytucja istnieje, pobierz jej identyfikator
        $institution_id = $row['institution_id'];
    }

    // Dodaj rekord do tabeli Education
    $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year) VALUES (:pid, :iid, :rank, :year)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':iid' => $institution_id,
        ':rank' => $rank,
        ':year' => $year
    ));
}

// Funkcja usuwająca pozycję profilu
function deletePosition($pdo, $position_id) {
    $stmt = $pdo->prepare('DELETE FROM Position WHERE position_id = :pid');
    $stmt->execute(array(':pid' => $position_id));
}

// Funkcja usuwająca edukację
function deleteEducation($pdo, $profile_id, $institution_id) {
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid AND institution_id = :iid');
    $stmt->execute(array(':pid' => $profile_id, ':iid' => $institution_id));
}

// Pobierz listę szkół z bazy danych
$stmt = $pdo->query('SELECT * FROM Institution');
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        $_SESSION['error'] = "Email address must contain @";
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
        $profile_id = $pdo->lastInsertId();

        // Dodaj pozycje profilu
        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;
            insertPosition($pdo, $profile_id, $rank, $_POST['year' . $i], $_POST['desc' . $i]);
            $rank++;
        }

        // Dodaj edukację
        if (isset($_POST['edu_school'])) {
            $institution_id = $_POST['edu_school'];
            $year = $_POST['edu_year'];
            insertEducation($pdo, $profile_id, $institution_id, $rank, $year);
        }

        $_SESSION['success'] = "Profile added";
        header("Location: index.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Profile</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <h1>Add Profile</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <p>First Name: <input type="text" name="first_name"></p>
        <p>Last Name: <input type="text" name="last_name"></p>
        <p>Email Address: <input type="text" name="email"></p>
        <p>Headline: <input type="text" name="headline"></p>
        <p>Summary: <textarea name="summary"></textarea></p>
        <p>Education:
            <input type="text" id="edu_school" name="edu_school" placeholder="Search for a school">
            Year: <input type="text" name="edu_year">
        </p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>

    <script>
        $(document).ready(function() {
            // Pobierz listę szkół z bazy danych
            var institutions = <?php echo json_encode($institutions); ?>;

            $('#edu_school').autocomplete({
                source: institutions.map(function(institution) {
                    return institution.name;
                }),
                minLength: 2
            });
        });
    </script>
</body>
</html>

