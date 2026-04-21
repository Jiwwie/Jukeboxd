<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Profile - Jukeboxd";
include 'partials/top.php';

?>

<body>

    <h1>Profile</h1>


</body>

</html>