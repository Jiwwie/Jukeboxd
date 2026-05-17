<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Add Concert - Jukeboxd";
include 'partials/top.php';

$artist_name = htmlspecialchars($db->query("SELECT name FROM jb_artists WHERE id = " . $_GET['artist_id'])->fetch_assoc()['name']);
$artist_id = (int)$_GET['artist_id'];

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">

        <form action="x_save_concert.php" method="post" class ="card">
            <h1>Add Concert for <?php echo $artist_name; ?></h1>
            <p>Did you double check for your concert? Make sure you aren't adding a duplicates to the database</p>
            
            <input type="hidden" name="artist_id" value= "<?php echo $artist_id; ?>">
            <input type="text" name="venue" placeholder="Venue">
            <input type="text" name="city" placeholder="City">
            <input type="date" name="date">

            <p><?php writeMessage("concert_error"); ?></p>
            
            <input type="submit" value="Add Concert">

        </form>
        
    </div>

</body>

</html>