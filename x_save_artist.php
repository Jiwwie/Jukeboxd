<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Get values from form
$name = $_POST["name"];

// Check if artist already exists
if (artistExists($db, $name)) {
    redirectWithMessage("add_artist.php", "Uh oh! An artist with that name already exists. Contact an administrator if you're having trouble.", "artist_error");
}

// Check if file is uploaded and handle it
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $result = uploadFile();
    
    if ($result !== false) {
        $image = $result;
    } else {
        redirectWithMessage("add_artist.php", "File error", "file_error");
    }
}

// Save artist if name and image are set
if (isset($image)) {
    saveArtist($db, $name, $image);
}

// Redirect to database page
header("Location: database.php");