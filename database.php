<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Home - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>List of Artists</h1>
        <a href="add_artist" class="btn">Don't see your artist? Add them!</a>

        
        
    </div>

</body>

</html>