<?php

// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Users - Jukeboxd";
include 'partials/top.php';

$users = allUsers($db);
$following = getFollowing($db, $_SESSION["userId"]);
?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>List of users</h1>
        <ul class="user-list">
            <?php foreach ($users as $user): ?>
                <?php if ($user['id'] == $_SESSION['userId']) continue; ?>
                <li class="user-list-item">
                    
                <a href="profile.php?user_id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a>
                
                <?php if (in_array($user['id'], $following)): ?>
                    <a href="x_unfollow.php?user_id=<?php echo $user['id']; ?>" class="btn-secondary">Unfollow</a>
                <?php else: ?>
                    <a href="x_follow.php?user_id=<?php echo $user['id']; ?>" class="btn">+ Follow</a>
                <?php endif; ?>
                
                </li>
                
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>