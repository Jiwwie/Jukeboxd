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
    <div class="divider"></div>
    <div class="container">
        <?php writeMessage("success"); ?>
        <?php writeMessage("error"); ?>
        
        <div class="profile-info">
            
            <img src="<?php echo $profile_user['pfp'] ? htmlspecialchars($profile_user['pfp']) : 'img/default-pfp.png'; ?>" alt="Profile Picture" class="profile-pic">

        <div>
            <h1><?php echo htmlspecialchars($profile_user['username']); ?></h1>
            <?php if ($is_own_profile): ?>
                <form action="x_save_profile.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="image" accept="image/*" onchange="this.form.submit()">
                </form>
            <?php endif; ?>
        </div>

            <div class="profile-stats">
                <p>Concerts logged: <?php echo count($reviews); ?></p>
                <!-- Room for future stats (fav artist, average rating etc) -->
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
            <?php foreach ($reviews as $r): 
                $stars = str_repeat('<span class="star filled">★</span>', $r['rating']) . str_repeat('<span class="star">★</span>', 5 - $r['rating']);
            ?>
            <div class="review-card">
                <div class="review-artist-img" style="background-image: url('<?php echo htmlspecialchars($r['artist_image'] ?? ''); ?>')"></div>
                <div class="review-body">
                    <div class="review-meta">
                        <span class="review-artist"><?php echo htmlspecialchars($r['artist_name']); ?></span>
                        <span class="review-venue"><?php echo htmlspecialchars($r['venue']); ?> · <?php echo htmlspecialchars($r['city']); ?></span>
                        <span class="review-date"><?php echo date("F j, Y", strtotime($r['date'])); ?></span>
                    </div>
                <div class="review-stars"><?php echo $stars; ?></div>
                    <?php if (!empty($r['review'])): ?>
                    <p class="review-text"><?php echo nl2br(htmlspecialchars($r['review'])); ?></p>
                    <?php endif; ?>
                </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No concerts logged yet.</p>
        <?php endif; ?>

    </div>

</body>

</html>