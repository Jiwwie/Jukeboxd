<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Get values from form
$name = $_POST["name"];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $result = uploadFile();
    
    if ($result !== false) {
        echo "File uploaded successfully! Path: " . $result;
        // Now you can save $result to your database
    } else {
        echo "File upload failed!";
    }
}

$image = $result;

//Insert artist into database
$stmt = $db->prepare("INSERT INTO jb_artists (name, image) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $image);
$stmt->execute();

// Redirect to database page
header("Location: database.php");