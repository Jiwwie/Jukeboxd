<?php
// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Log Concert - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>Log Concert</h1>
        <p>Does your artist or concert not exist in our database yet? Add it!</p>
        <a href="database.php" class="btn">To database →</a>

        <form action="x_save_log.php" method="post" class ="post-form">
            <h2>Log Concert</h2>
            <?php writeMessage("empty"); ?>  

            <select name="artist">
                <!-- Options will be populated with artist data from the database -->
                
            </select>

            <select name="concert">
                <option value="">Select a concert</option>
                <!-- Options will be populated with concert data from the database -->
            </select>

            <p><textarea name="review" placeholder="Write a review here..." rows="6"></textarea></p>
            <p><input type="submit" value="Log"></p>
        </form>

        
    </div>

</body>

</html>