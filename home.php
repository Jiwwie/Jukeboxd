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
        <h1>Home</h1>

        <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
        <p>What do you want to do today?</p>

        <a href="log_concert.php" class="btn">Log a concert</a>
        <a href="database.php" class="btn">Browse concerts</a>
        <a href="feed.php" class="btn">Friend activity</a>
        <a href="profile.php" class="btn">My profile</a>
        
    </div>

</body>

</html>