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

// Funkcja edytująca pozycję profilu
function updatePosition($pdo, $position_id, $rank, $year, $description) {
    $stmt = $pdo->prepare('UPDATE Position SET rank = :rank, year = :year, description = :desc WHERE position_id = :pid');
    $stmt->execute(array(
        ':pid' => $position_id,
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $description
    ));
}

// Funkcja usuwająca pozycję profilu
function deletePosition($pdo, $position_id) {
    $stmt = $pdo->prepare('DELETE FROM Position WHERE position_id = :pid');
    $stmt->execute(array(':pid' => $position_id));
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
<p>Position: <input type="text" name="year1"> <input type="button" value="+" id="addPosBtn"></p>
<div id="position_fields"></div>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>
$(document).ready(function() {
    var countPos = 1;

    $('#addPosBtn').click(function(event) {
        event.preventDefault();
        if (countPos >= 9) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        var newDiv = $(document.createElement('div')).attr('id', 'position' + countPos);
        newDiv.after().html('<p>Position: <input type="text" name="year' + countPos + '"> <input type="button" value="-" onclick="$(\'#' + newDiv.attr('id') + '\').remove(); return false;"></p><textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>');
        newDiv.appendTo('#position_fields');
    });
});
</script>
</body>
</html>
