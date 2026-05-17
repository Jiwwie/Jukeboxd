<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Home - Jukeboxd";
include 'partials/top.php';

$reviews = feedReviews($db, $_SESSION['userId']);

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <div class="divider"></div>
        <h1>Home</h1>

        <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
        <p>What do you want to do today?</p>

        <div class="home-actions">
            <a href="log_concert.php" class="home-btn">+ Log a concert</a>
            <a href="database.php" class="home-btn">Browse concerts</a>
            <a href="feed.php" class="home-btn">Friend activity</a>
            <a href="users.php" class="home-btn">Browse users</a>
            <a href="profile.php" class="home-btn">My profile</a>
        </div>

        <div class="divider"></div>

        </div>

</body>

</html>