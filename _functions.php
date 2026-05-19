<?php
// Start session and load installed packages
session_start();
require_once 'vendor/autoload.php';

/**
 * Establishes a connection to the database using credentials from the .env file.
 *
 * @return mysqli The database connection object.
 */
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

/**
 * Stores a message in the session and redirects to a given URL.
 *
 * @param string $url     The URL to redirect to.
 * @param string $message The message to store in the session.
 * @param string $key     The session key under which the message is stored.
 * @return void
 */
function redirectWithMessage($url, $message, $key) {
        $_SESSION["message"][$key] = $message;
        header("Location: $url");
        exit();
    }

/**
 * Outputs and clears a session message for a given key.
 *
 * @param string $key The session key of the message to display.
 * @return void
 */
function writeMessage($key) {
    if (isset($_SESSION["message"][$key])) {
        echo "<p>";
        echo $_SESSION["message"][$key];
        echo "</p>";
        unset($_SESSION["message"][$key]);
        }  
    }

/**
 * Checks if the user is logged in. Redirects to index.php with a message if not.
 *
 * @return void
 */
function isLoggedIn() {
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != TRUE) {
        $_SESSION["message"]["login"] = "Log in to view page.";
        header("Location: index.php");
        exit();
        }
    }

/**
 * Redirects to home.php if the user is already logged in.
 * Intended for use on index.php only.
 *
 * @return void
 */
function indexRedirect() {
    // Redirect to home if already logged in, only used on index.php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
        header("Location: home.php");
        exit();
        }
    }

/**
 * Fetches a user record by username.
 *
 * @param mysqli $db       The database connection object.
 * @param string $username The username to look up.
 * @return array|null      Associative array of the user row, or null if not found.
 */
function getUserByUsername($db, $username) {
        $statement = $db->prepare("SELECT * FROM jb_users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result();
        $user = $result->fetch_assoc();
        return $user;
    }

/**
 * Fetches a user's public profile data (id, username, pfp) by user ID.
 *
 * @param mysqli $db      The database connection object.
 * @param int    $user_id The ID of the user to retrieve.
 * @return array|null     Associative array with id, username, and pfp, or null if not found.
 */
function getUserProfile($db, $user_id) {
    $stmt = $db->prepare("SELECT id, username, pfp FROM jb_users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile_user = $stmt->get_result()->fetch_assoc();
    return $profile_user;
}

/**
 * Creates a new user with a hashed password.
 *
 * @param mysqli  $db       The database connection object.
 * @param string  $username The desired username.
 * @param string  $password The plain-text password to hash and store.
 * @return void
 */
function createUser($db, $username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $db->prepare("INSERT INTO jb_users (username, password) VALUES (?, ?)");
    $statement->bind_param("ss", $username, $hashedPassword);
    $statement->execute();
}

/**
 * Retrieves all users ordered by ID descending.
 *
 * @param mysqli $db The database connection object.
 * @return array     Array of associative arrays, each containing id and username.
 */
function allUsers($db) {
    $result = $db->query("SELECT id, username FROM jb_users ORDER BY id DESC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

/**
 * Returns the list of user IDs that a given user is following.
 *
 * @param mysqli $db      The database connection object.
 * @param int    $user_id The ID of the follower.
 * @return int[]          Array of followed user IDs.
 */
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

/**
 * Creates a follow relationship between two users.
 *
 * @param mysqli $db           The database connection object.
 * @param int    $follower_id  The ID of the user who is following.
 * @param int    $following_id The ID of the user to be followed.
 * @return void
 */
function follow($db, $follower_id, $following_id) {
    $stmt = $db->prepare("INSERT IGNORE INTO jb_follows (follower_id, following_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();
}

/**
 * Removes a follow relationship between two users.
 *
 * @param mysqli $db           The database connection object.
 * @param int    $follower_id  The ID of the user who is unfollowing.
 * @param int    $following_id The ID of the user to be unfollowed.
 * @return void
 */
function unfollow($db, $follower_id, $following_id) {
    $stmt = $db->prepare("DELETE FROM jb_follows WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();
}

/**
 * Checks whether an artist with the given name already exists in the database.
 *
 * @param mysqli $db   The database connection object.
 * @param string $name The artist name to check.
 * @return bool        True if the artist exists, false otherwise.
 */
function artistExists($db, $name) {
    $statement = $db->prepare("SELECT id FROM jb_artists WHERE name = ?");
    $statement->bind_param("s", $name);
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}

/**
 * Checks whether a concert already exists for a given artist, venue, city, and date.
 *
 * @param mysqli $db        The database connection object.
 * @param int    $artist_id The ID of the artist.
 * @param string $venue     The venue name.
 * @param string $city      The city name.
 * @param string $date      The concert date.
 * @return bool             True if the concert exists, false otherwise.
 */
function concertExists($db, $artist_id, $venue, $city, $date) {
    $statement = $db->prepare("SELECT id FROM jb_concerts WHERE venue = ? AND city = ? AND date = ? AND artist_id = ?");
    $statement->bind_param("sssi", $venue, $city, $date, $artist_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}

/**
 * Checks whether a user has already submitted a review for a given concert.
 *
 * @param mysqli $db         The database connection object.
 * @param int    $user_id    The ID of the user.
 * @param int    $concert_id The ID of the concert.
 * @return bool              True if a review exists, false otherwise.
 */
function reviewExists($db, $user_id, $concert_id) {
    $stmt = $db->prepare("SELECT id FROM jb_reviews WHERE user_id = ? AND concert_id = ?");
    $stmt->bind_param("ii", $user_id, $concert_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Inserts a new artist record into the database.
 *
 * @param mysqli  $db    The database connection object.
 * @param string  $name  The artist's name.
 * @param string  $image The filename or path of the artist's image.
 * @return void
 */
function saveArtist($db, $name, $image) {
    $statement = $db->prepare("INSERT INTO jb_artists (name, image) VALUES (?, ?)");
    $statement->bind_param('ss', $name, $image);
    $statement->execute();
}

/**
 * Inserts a new concert record into the database.
 *
 * @param mysqli $db        The database connection object.
 * @param int    $artist_id The ID of the performing artist.
 * @param string $venue     The venue name.
 * @param string $city      The city where the concert takes place.
 * @param string $date      The date of the concert.
 * @return void
 */
function saveConcert($db, $artist_id, $venue, $city, $date) {
    $statement = $db->prepare("INSERT INTO jb_concerts (artist_id, venue, city, date) VALUES (?, ?, ?, ?)");
    $statement->bind_param('isss', $artist_id, $venue, $city, $date);
    $statement->execute();
}

/**
 * Inserts a new review into the database and returns its ID.
 *
 * @param mysqli  $db         The database connection object.
 * @param int     $user_id    The ID of the reviewing user.
 * @param int     $concert_id The ID of the concert being reviewed.
 * @param int     $rating     The numeric rating given.
 * @param string  $review     The review text.
 * @return int                The ID of the newly inserted review.
 */
function saveReview($db, $user_id, $concert_id, $rating, $review) {
    $statement = $db->prepare("INSERT INTO jb_reviews (user_id, concert_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
    $statement->bind_param("iiis", $user_id, $concert_id, $rating, $review);
    $statement->execute();
    return $db->insert_id;
}

/**
 * Retrieves all reviews written by a specific user, including concert and artist details.
 *
 * @param mysqli $db      The database connection object.
 * @param int    $user_id The ID of the user whose reviews to fetch.
 * @return array          Array of associative arrays, each representing a review with concert and artist info.
 */
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

/**
 * Fetches the 20 most recent reviews from users that the given user follows.
 *
 * @param mysqli $db      The database connection object.
 * @param int    $user_id The ID of the logged-in user.
 * @return mysqli_result  The result set of feed reviews with concert, artist, and user info.
 */
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

/**
 * Retrieves all concerts associated with a specific artist.
 *
 * @param mysqli $db        The database connection object.
 * @param int    $artist_id The ID of the artist.
 * @return array            Array of associative arrays, each containing concert id, name, and date.
 */
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

/**
 * Retrieves the 10 most recent reviews for all concerts by a specific artist.
 *
 * @param mysqli $db        The database connection object.
 * @param int    $artist_id The ID of the artist.
 * @return array            Array of associative arrays, each containing review, concert, user, and artist image info.
 */
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

/**
 * Handles file upload from a form, validates extension and size, and moves the file to the img/ directory.
 *
 * @return string|false The relative path to the uploaded file on success, or false on failure.
 */
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
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        return false;
    }
    // Move the uploaded file to img folder
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        return $target_file;
    }

    return false;
}