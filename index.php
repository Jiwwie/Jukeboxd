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
    <div class="container">
        <h1>Jukeboxd</h1>

        <?php writeMessage('login'); ?>
        <a href="signup.php" class="btn">Create account</a>
        <a href="login.php" class="btn">Log in</a>

    </div>
</body>

</html>