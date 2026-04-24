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
        <a href="add_artist.php" class="btn">Don't see your artist? Add them!</a>

        <div class="grid">
            <?php
            // Fetch artists from database
            $artists = $db->query("SELECT * FROM jb_artists order by name asc");

            // Loop through artists and display them
            while ($artist = $artists->fetch_assoc()) {
                echo '<div class="artist_card">';
                echo '<h2>' . htmlspecialchars($artist['name']) . '</h2>';
                echo '</div>';  
            }
            ?>
        </div>
        
    </div>

</body>

</html>