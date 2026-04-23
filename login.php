<?php

// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Set page title and include top partial
$title = "Log in - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>Log in</h1>

        <form action="x_login.php" method="post">
            <?php writeMessage("login_error"); ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn">Log in</button>
        </form>

        <a href="signup.php" class="btn">Don't have an account? Sign up →</a>
    </div> 


</body>

</html>