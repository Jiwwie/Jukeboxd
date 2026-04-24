<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Add Artist - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">

        <form action="x_save_artist.php" method="post" class ="card" enctype="multipart/form-data">
            <h1>Add Artist</h1>
            <p>Did you double check for your artist or band? Make sure you aren't adding a duplicates to the database</p>
            <?php writeMessage("error"); ?>  

            <input type="text" name="name" placeholder="Artist name">
            <input type="file" name="image">
            
            <input type="submit" value="Add Artist">
        </form>
        
    </div>

</body>

</html>