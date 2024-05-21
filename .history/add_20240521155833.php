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
