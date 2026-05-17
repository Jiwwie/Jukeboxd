<?php

// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Feed - Jukeboxd";
include 'partials/top.php';

$reviews = feedReviews($db, $_SESSION['userId']);

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>Friend activity</h1>
        <p>Follow more users to fill your feed!</p>
        <a href="users.php" class="btn">All users →</a>

<?php if ($reviews->num_rows === 0): ?>
    <p>No activity yet — follow some users to fill your feed!</p>
    <a href="users.php" class="btn">All users →</a>
    <?php else: ?>
        <div class="reviews-list">
            <?php while ($r = $reviews->fetch_assoc()):
                $stars = str_repeat('<span class="star filled">★</span>', $r['rating']) . str_repeat('<span class="star">★</span>', 5 - $r['rating']);
            ?>
            <div class="review-card">
                <div class="review-artist-img" style="background-image: url('<?php echo htmlspecialchars($r['artist_image']); ?>')"></div>
                <div class="review-body">
                    <div class="review-meta">
                        <a href="profile.php?user_id=<?php echo $r['user_id']; ?>" class="review-author"><?php echo htmlspecialchars($r['username']); ?></a>
                        <span class="review-artist"><?php echo htmlspecialchars($r['artist_name']); ?></span>
                        <span class="review-venue"><?php echo htmlspecialchars($r['venue']); ?> · <?php echo htmlspecialchars($r['city']); ?></span>
                        <span class="review-date"><?php echo date("F j, Y", strtotime($r['date'])); ?></span>
                    </div>
                    <div class="review-stars"><?php echo $stars; ?></div>
                    <?php if (!empty($r['review'])): ?><p class="review-text"><?php echo htmlspecialchars($r['review']); ?></p><?php endif; ?>
                    <span class="review-logged">Logged <?php echo date("M j, Y", strtotime($r['created_at'])); ?></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    </div>
</body>

</html>