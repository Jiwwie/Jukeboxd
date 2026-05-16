<?php
require_once '_functions.php';
$db = connectToDb();

// Must be logged in
isLoggedIn();

$concert_id = $_POST['concert_id'];
$rating     = $_POST['rating'];
$review     = $_POST['review'] ?? '';
$user_id    = $_SESSION['userId'];

// Validate required fields
if (!$concert_id || !is_numeric($concert_id) || !$rating) {
    redirectWithMessage("log_concert.php", "Please select a concert and give it a rating.", "empty");
}
if ($rating < 1 || $rating > 5) {
    redirectWithMessage("log_concert.php", "Invalid rating value.", "empty");
}
if (strlen($review) > 1000) {
    redirectWithMessage("log_concert.php", "Review is too long. Maximum 1000 characters.", "empty");
}

// Save the review
saveReview($db, $user_id, $concert_id, $rating, $review);

/*
// Handle image uploads (up to 5)
if (!empty($_FILES['image']['name'][0])) {
    $target_dir        = "img/";
    $allowed_ext       = ['png', 'jpg', 'jpeg'];
    $max_size          = 5 * 1024 * 1024; // 5MB
    $file_count        = count($_FILES['image']['name']);

    for ($i = 0; $i < $file_count && $i < 5; $i++) {
        // Skip empty slots in the array
        if ($_FILES['image']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $original_name = $_FILES['image']['name'][$i];
        $tmp_path      = $_FILES['image']['tmp_name'][$i];
        $file_size     = $_FILES['image']['size'][$i];
        $ext           = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $name_no_ext   = pathinfo($original_name, PATHINFO_FILENAME);

        // Validate extension and size
        if (!in_array($ext, $allowed_ext)) continue;
        if ($file_size > $max_size) continue;

        // Build a safe, unique filename
        $safe_name   = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $name_no_ext);
        $target_file = $target_dir . $safe_name . '_' . time() . '_' . $i . '.' . $ext;

        if (move_uploaded_file($tmp_path, $target_file)) {
            // Store file path in jb_images linked to this review
            $img_stmt = $db->prepare("INSERT INTO jb_images (review_id, file_path) VALUES (?, ?)");
            $img_stmt->bind_param("is", $review_id, $target_file);
            $img_stmt->execute();
        }
    }
}
*/

redirectWithMessage("profile.php", "Concert logged successfully!", "success");