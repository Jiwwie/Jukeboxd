<?php
require_once '_functions.php';
$db = connectToDb();
isLoggedIn();

$user_id = $_SESSION['userId'];

if (!empty($_FILES['image']['name'])) {
    $pfp = uploadFile();
    if ($pfp) {
        $stmt = $db->prepare("UPDATE jb_users SET pfp = ? WHERE id = ?");
        $stmt->bind_param("si", $pfp, $user_id);
        $stmt->execute();
        redirectWithMessage("profile.php", "Profile picture updated!", "success");
    }
}

redirectWithMessage("profile.php", "Upload failed.", "error");