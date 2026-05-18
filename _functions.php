<?php
// Start session and load installed packages
session_start();
require_once 'vendor/autoload.php';

function connectToDb() {
    // Load secrets from the file .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
    $dotenv->load();

    // Connect to database
    $db = new mysqli(
        'ostrawebb.se', 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASS'],
        $_ENV['DB_USER']
    );

    return $db;
}

// Redirect functions
function redirectWithMessage($url, $message, $key) {
        $_SESSION["message"][$key] = $message;
        header("Location: $url");
        exit();
    }
function writeMessage($key) {
    if (isset($_SESSION["message"][$key])) {
        echo "<p>";
        echo $_SESSION["message"][$key];
        echo "</p>";
        unset($_SESSION["message"][$key]);
        }  
    }
function isLoggedIn() {
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != TRUE) {
        $_SESSION["message"]["login"] = "Log in to view page.";
        header("Location: index.php");
        exit();
        }
    }
function indexRedirect() {
    // Redirect to home if already logged in, only used on index.php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
        header("Location: home.php");
        exit();
        }
    }

//User functions
function getUserByUsername($db, $username) {
        $statement = $db->prepare("SELECT * FROM jb_users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result();
        $user = $result->fetch_assoc();
        return $user;
    }
function getUserProfile($db, $user_id) {
    $stmt = $db->prepare("SELECT id, username, pfp FROM jb_users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile_user = $stmt->get_result()->fetch_assoc();
    return $profile_user;
}
function createUser($db, $username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $db->prepare("INSERT INTO jb_users (username, password) VALUES (?, ?)");
    $statement->bind_param("ss", $username, $hashedPassword);
    $statement->execute();
}
function allUsers($db) {
    $result = $db->query("SELECT id, username FROM jb_users ORDER BY id DESC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}
function getFollowing($db, $user_id) {
    $statement = $db->prepare("SELECT following_id FROM jb_follows WHERE follower_id = ?");
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $result = $statement->get_result();

    $following = [];
    while ($row = $result->fetch_assoc()) {
        $following[] = $row['following_id'];
    }
    return $following;
}
function follow($db, $follower_id, $following_id) {
    $stmt = $db->prepare("INSERT IGNORE INTO jb_follows (follower_id, following_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();
}

function unfollow($db, $follower_id, $following_id) {
    $stmt = $db->prepare("DELETE FROM jb_follows WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();
}
//db functions
function artistExists($db, $name) {
    $statement = $db->prepare("SELECT id FROM jb_artists WHERE name = ?");
    $statement->bind_param("s", $name);
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}
function concertExists($db, $artist_id, $venue, $city, $date) {
    $statement = $db->prepare("SELECT id FROM jb_concerts WHERE venue = ? AND city = ? AND date = ? AND artist_id = ?");
    $statement->bind_param("sssi", $venue, $city, $date, $artist_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}
function reviewExists($db, $user_id, $concert_id) {
    $stmt = $db->prepare("SELECT id FROM jb_reviews WHERE user_id = ? AND concert_id = ?");
    $stmt->bind_param("ii", $user_id, $concert_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}
function saveArtist($db, $name, $image) {
    $statement = $db->prepare("INSERT INTO jb_artists (name, image) VALUES (?, ?)");
    $statement->bind_param('ss', $name, $image);
    $statement->execute();
}
function saveConcert($db, $artist_id, $venue, $city, $date) {
    $statement = $db->prepare("INSERT INTO jb_concerts (artist_id, venue, city, date) VALUES (?, ?, ?, ?)");
    $statement->bind_param('isss', $artist_id, $venue, $city, $date);
    $statement->execute();
}
function saveReview($db, $user_id, $concert_id, $rating, $review) {
    $statement = $db->prepare("INSERT INTO jb_reviews (user_id, concert_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
    $statement->bind_param("iiis", $user_id, $concert_id, $rating, $review);
    $statement->execute();
    return $db->insert_id;
}
function getReviewsByUser($db, $user_id) {
    $statement = $db->prepare(
        "SELECT r.id, r.rating, r.review, r.created_at, c.venue, c.city, c.date, a.name AS artist_name, a.image AS artist_image
         FROM jb_reviews r
         JOIN jb_concerts c ON r.concert_id = c.id
         JOIN jb_artists a ON c.artist_id = a.id
         WHERE r.user_id = ?
         ORDER BY r.created_at DESC"
    );
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $result = $statement->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    return $reviews;
}
function feedReviews($db, $user_id) {
    $statement = $db->prepare(
        "SELECT r.id, r.rating, r.review, r.created_at, c.venue, c.city, c.date, a.name AS artist_name, u.username, u.id AS user_id, a.image AS artist_image
         FROM jb_reviews r
         JOIN jb_concerts c ON r.concert_id = c.id
         JOIN jb_artists a ON c.artist_id = a.id
         JOIN jb_users u ON r.user_id = u.id
         WHERE r.user_id IN (SELECT following_id FROM jb_follows WHERE follower_id = ?)
         ORDER BY r.created_at DESC
         LIMIT 20"
    );
    $statement->bind_param("i", $user_id);
    $statement->execute();
    return $statement->get_result();
}
function getConcertsByArtist($db, $artist_id) {
    $statement = $db->prepare("SELECT id, name, date FROM jb_concerts WHERE artist_id = ?");
    $statement->bind_param("i", $artist_id);
    $statement->execute();
    $result = $statement->get_result();

    $concerts = [];
    while ($row = $result->fetch_assoc()) {
        $concerts[] = $row;
    }
    return $concerts;
}
function getReviewsByArtist($db, $artist_id) {
    $statement = $db->prepare(
        "SELECT 
            r.id,
            r.rating,
            r.review,
            r.created_at,

            c.venue,
            c.city,
            c.date,

            u.id AS user_id,
            u.username,

            a.image AS artist_image

         FROM jb_reviews r

         JOIN jb_concerts c ON r.concert_id = c.id
         JOIN jb_users u ON r.user_id = u.id
         JOIN jb_artists a ON c.artist_id = a.id

         WHERE c.artist_id = ?

         ORDER BY r.created_at DESC
         
         LIMIT 10"
    );
    $statement->bind_param("i", $artist_id);
    $statement->execute();
    $result = $statement->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}

//Other functions
function uploadFile() {
    $target_dir = "img/";
    $allowed_extensions = ['png', 'jpg', 'jpeg'];
    $name_without_ext = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $safe_filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $name_without_ext) . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $safe_filename;

    // Validate file extension
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    // Validate file size (max 5MB)
    if ($_FILES["image"]["size"] > 5 * 800 * 800) {
        return false;
    }
    // Move the uploaded file to img folder
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        return $target_file;
    }

    return false;
}
