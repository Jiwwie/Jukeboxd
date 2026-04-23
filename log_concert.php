<?php
// Load functions and connect to database
require_once '_functions.php';
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

        <form action="x_save_log.php" method="post" class ="card">
            <h1>Log Concert</h1>
            <?php writeMessage("empty"); ?>  

            <select name="artist">
                <option value="">Select an artist</option>
                <!-- Options will be populated with artist data from the database -->
                
            </select>

            <select name="concert">
                <option value="">Select a concert</option>
                <!-- Options will be populated with concert data from the database -->
            </select>

            <div class="rating">
                <p>Your rating:</p>
                <input type="radio" name="rating" value="5">
                <label for="str5"></label>
                <input type="radio" name="rating" value="4">
                <label for="str4"></label>
                <input type="radio" name="rating" value="3">
                <label for="str3"></label>
                <input type="radio" name="rating" value="2">
                <label for="str2"></label>
                <input type="radio" name="rating" value="1">
                <label for="str1"></label>
            </div>
            
            <textarea name="review" placeholder="Write a review here..." rows="6"></textarea>
            <input type="submit" value="Log">
        </form>
        
        <p>Does your artist or concert not exist in our database yet? Add it!</p>
        <a href="database.php" class="btn">To database →</a>
        
    </div>

</body>

</html>
