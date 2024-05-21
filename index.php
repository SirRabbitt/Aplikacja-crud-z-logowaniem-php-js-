<?php
session_start();
require_once "pdo.php";

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo "<h1> <a href='login.php'>Please log in</a> to access this page.</h1>";

}

// Function to validate positions
function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if (strlen($year) == 0 || strlen($desc) == 0) return "All fields are required";
        if (!is_numeric($year)) return "Year must be numeric";
    }
    return true;
}

// Function to insert positions
function insertPositions($pdo, $profile_id) {
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc
        ));
        $rank++;
    }
}

// Function to delete positions
function deletePositions($pdo, $profile_id) {
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profile_id));
}

// Get the list of profiles
$stmt = $pdo->query("SELECT * FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae</title>

</head>
<body>
<h1>added</h1>
<table>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($rows as $row) : ?>
    <tr>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td>
            <a href="view.php?profile_id=<?php echo $row['profile_id']; ?>">View</a> |
            <a href="edit.php?profile_id=<?php echo $row['profile_id']; ?>">Edit</a> |
            <a href="delete.php?profile_id=<?php echo $row['profile_id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<p><a href="add.php">Add New Entry</a></p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
