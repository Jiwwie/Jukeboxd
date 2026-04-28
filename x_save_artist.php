<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Get values from form
$name = $_POST["name"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $result = uploadFile();
    
    if ($result !== false) {
        $image = $result;
    } else {
        redirectWithMessage("add_artist.php", "No file attached", "file_error");
    }
}

saveArtist($db, $name, $image);

// Redirect to database page
header("Location: database.php");