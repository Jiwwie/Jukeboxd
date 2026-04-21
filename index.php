<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Set page title and include top partial
$title = "Home - Jukeboxd";
include 'partials/top.php';

// Redirect to home if already logged in
indexRedirect();

?>

<body>

    <h1>Jukeboxd</h1>

    <?php writeMessage('login'); ?>
    <a href="signup.php">Create account</a>
    <br>
    <a href="login.php">Log in</a>

</body>

</html>