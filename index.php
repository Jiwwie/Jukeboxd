<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Set page title and include top partial
$title = "Jukeboxd";
include 'partials/top.php';

// Redirect to home if already logged in
indexRedirect();

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">

        <?php writeMessage('login'); ?>
        <a href="signup.php" class="btn">Create account</a>
        <a href="login.php" class="btn">Log in</a>

        <h2>Welcome to Jukeboxd!</h2>
        <p>Jukeboxd lets you share your concert history with friends and discover the next performance you'll love!</p>
        <p>Get started today by creating an account or logging in</p>

    </div>
</body>

</html>