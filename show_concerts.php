<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Get artist ID from URL parameter
$artist_id = $_GET['artist_id'];
$artis_name = $db->query("SELECT name FROM jb_artists WHERE id = $artist_id")->fetch_assoc()['name'];

// Set page title and include top partial
$title = "Concerts - Jukeboxd";
include 'partials/top.php';
?>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1><?php echo $artis_name; ?> - Concerts</h1>
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
        
    </div>