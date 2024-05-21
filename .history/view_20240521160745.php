<?php
session_start();
require_once "pdo.php";

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Check if the profile_id parameter is passed
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile identifier";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the entry exists
if ($row === false) {
    $_SESSION['error'] = "Invalid profile identifier";
    header("Location: index.php");
    return;
}

// Get education information
$stmt = $pdo->prepare("SELECT * FROM Education WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>b82706ae - <?php echo htmlentities($row['first_name']." ".$row['last_name']); ?></title>
</head>
<body>
<h1>Profile</h1>
<p>First Name: <?php echo htmlentities($row['first_name']); ?></p>
<p>Last Name: <?php echo htmlentities($row['last_name']); ?></p>
<p>Email Address: <?php echo htmlentities($row['email']); ?></p>
<p>Headline: <?php echo htmlentities($row['headline']); ?></p>
<p>Summary: <?php echo htmlentities($row['summary']); ?></p>

<h2>Education</h2>
<ul>
<?php foreach ($educations as $education) : ?>
    <li><?php echo htmlentities($education['year']); ?>: <?php echo htmlentities($education['institution_id']); ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
