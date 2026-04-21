<?php

// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Set page title and include top partial
$title = "Sign up - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <div class="container">
        <h1>Sign up</h1>
        <form action="x_register.php" method="POST">
            <h2>Create account</h2>
            <?php writeMessage("register_error"); ?>        
            <p><input type="text" name="username" placeholder="Username"></p>
            <p><input type="password" name="password" placeholder="Password"></p>
            <p><input type="password" name="password_confirm" placeholder="Confirm Password" required></p>
            <p><input type="submit" value="Create account"></p>
    </div>
</body>

</html>