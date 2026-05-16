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
$reviews = getReviewsByUser($db, $user_id);

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
        
        <div class="profile-info">
            <img src="<?php echo $profile_user['pfp'] ? $profile_user['pfp'] : 'img/default-pfp.png'; ?>" alt="Profile Picture" class="profile-pic">
            
            <div class="profile-stats">
                <p>Concerts logged: <?php echo count($reviews); ?></p>
                <!-- Future stats like favorite artist, average rating, etc. can go here -->
            </div>
        </div>

        <?php if ($is_own_profile): ?>
            <a href="log_concert.php" class="btn">Log a concert →</a>
            <a href="database.php" class="btn">Explore database →</a>
            <a href="x_logout.php" class="btn-secondary">Log out</a>
        <?php endif; ?>

        <h2>Concerts logged</h2>
        <?php if (count($reviews) > 0): ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <h3><?php echo $review['venue']; ?> - <?php echo date("F j, Y", strtotime($review['date'])); ?></h3>
                        <p>Rating: <?php echo str_repeat("★", $review['rating']) . str_repeat("☆", 5 - $review['rating']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No concerts logged yet.</p>
        <?php endif; ?>
    </div>

</body>

</html>