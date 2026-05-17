<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Get values from form
$artist_id = $_POST["artist_id"];
$venue = $_POST["venue"];
$city = $_POST["city"];
$date = $_POST["date"];

// Check if all required fields are set
if (!$artist_id || !$venue || !$city || !$date) {
    redirectWithMessage("add_concert.php", "Please fill in all fields.", "concert_error");
}

// Check if artist already exists
if (concertExists($db, $artist_id, $venue, $city, $date)) {
    redirectWithMessage("add_concert.php", "Uh oh! A concert with those details already exists. Contact an administrator if you're having trouble.", "concert_error");
}

// Save concert if venue, city and date are set
saveConcert($db, $artist_id, $venue, $city, $date);

// Redirect to database page
header("Location: show_concerts.php?artist_id=" . $artist_id);