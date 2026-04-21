<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Feed - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>Friend activity</h1>
    </div>
</body>

</html>