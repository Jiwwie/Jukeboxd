<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Home - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <div class="container">
        <h1>Home</h1>
    </div>

</body>

</html>