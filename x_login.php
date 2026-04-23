<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Get username and password from form
$username = $_POST["username"];
$password = $_POST["password"];

// Get user from database
$user = getUserByUsername($db, $username);
$hashedPassword = $user["password"];

// If user doesn't exist: go back to index.php and show error message
if ( ! $user) {
    redirectWithMessage("login.php", "Password or username incorrect", "login_error");
    exit();
}

// If password is incorrect: go back to index.php and show error message
if ( ! password_verify($password, $hashedPassword)) {
    redirectWithMessage("login.php", "Password or username incorrect", "login_error");
    exit();
}

// If everything is correct: log in user and redirect to home.php
$_SESSION["loggedin"] = TRUE;
$_SESSION["userId"] = $user["id"];
$_SESSION["username"] = $username;

header('Location: home.php');