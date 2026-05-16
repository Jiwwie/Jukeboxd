<?php

// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();


// Show profile and profile actions if user_id = prfile_id, else show profile only
$user_id = $_GET['user_id'] ?? $_SESSION['userId'];
$is_own_profile = ($user_id == $_SESSION['userId']);

$profile_user = getUserProfile($db, $user_id);

// If user doesn't exist, redirect home
if (!$profile_user) {
    header("Location: home.php");
    exit();
    }
    
// Set page title and include top partial
    $title = $profile_user['username'] . " - Jukeboxd";
    include 'partials/top.php';
?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1><?php echo $profile_user['username']; ?>'s profile</h1>
        <?php writeMessage("success"); ?>
        
        <?php getReviewsByUser($db, $user_id) ?>

        <a href="x_logout.php" class="btn-secondary">Log out</a>
    </div>

</body>

</html>