<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Concerts - Jukeboxd";
include 'partials/top.php';

// Get artist ID from URL parameter
$artist_id = (int)$_GET['artist_id'];
$artist_name = htmlspecialchars($db->query("SELECT name FROM jb_artists WHERE id = $artist_id")->fetch_assoc()['name']);
$artist_reviews = getReviewsByArtist($db, $artist_id);

?>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="divider"></div>
    <div class="container">
        <h1><?php echo $artist_name; ?> - Concerts</h1>
        <a href="add_concert.php?artist_id=<?php echo $artist_id; ?>" class="btn">Don't see your concert? Add it!</a>

        <div class="grid">
            <?php
            // Fetch concerts for the given artist ID
            $concerts = $db->query("SELECT * FROM jb_concerts WHERE artist_id = $artist_id ORDER BY date DESC");

            // Loop through concerts and display them
            while ($concert = $concerts->fetch_assoc()) {
                $venue = htmlspecialchars($concert['venue']);
                $city = htmlspecialchars($concert['city']);
                $date = htmlspecialchars($concert['date']);
                echo '<div class="concert-card">';
                echo '<h2>' . $venue . ' - ' . $city . '</h2>';
                echo '<p>' . date("F j, Y", strtotime($date)) . '</p>';
                echo '</div>';  
            }
            ?>
        </div>

        <div class="divider"></div>
        <h2>Recent reviews for <?php echo $artist_name; ?></h2>
        <div class="divider"></div>

        <div class="reviews-list">
            <?php foreach ($artist_reviews as $r):
                $stars = str_repeat('<span class="star filled">★</span>', $r['rating']) . str_repeat('<span class="star">★</span>', 5 - $r['rating']);
            ?>
            <div class="review-card">
                <div class="review-artist-img" style="background-image: url('<?php echo htmlspecialchars($r['artist_image']); ?>')"></div>
                <div class="review-body">
                    <div class="review-meta">
                        <a href="profile.php?user_id=<?php echo $r['user_id']; ?>" class="review-author"><?php echo htmlspecialchars($r['username']); ?></a>
                        <span class="review-artist"><?php echo htmlspecialchars($artist_name); ?></span>
                        <span class="review-venue"><?php echo htmlspecialchars($r['venue']); ?> · <?php echo htmlspecialchars($r['city']); ?></span>
                        <span class="review-date"><?php echo date("F j, Y", strtotime($r['date'])); ?></span>
                    </div>
                    <div class="review-stars"><?php echo $stars; ?></div>
                    <?php if (!empty($r['review'])): ?><p class="review-text"><?php echo htmlspecialchars($r['review']); ?></p><?php endif; ?>
                    <span class="review-logged">Logged <?php echo date("M j, Y", strtotime($r['created_at'])); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
