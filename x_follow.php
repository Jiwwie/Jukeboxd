<?php
require_once '_functions.php';
$db = connectToDb();
isLoggedIn();

$following_id = $_GET['user_id'] ?? '';
if ($following_id && is_numeric($following_id)) {
    follow($db, $_SESSION['userId'], $following_id);
}

header("Location: users.php");
