<?php
// Load functions and connect to database
require_once 'vendor/_functions.php';
$db = connectToDb();

// Get values from form
$username = $_POST["username"];
$password = $_POST["password"];
$passwordConfirm = $_POST["password_confirm"];

// Check if passwords match and if the user already exists
if ($password !== $passwordConfirm) {
    redirectWithMessage("index.php", "Passwords do not match.", "register");
}

if (getUserByUsername($db, $username)) {
    redirectWithMessage("index.php", "Username already exists.", "register");
}

// Save the new user to the database
createUser($db, $username, $password);

// Redirect to login page
header("Location: login.php");
