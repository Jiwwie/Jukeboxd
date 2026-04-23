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
function createUser($db, $username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $db->prepare("INSERT INTO jb_users (username, password) VALUES (?, ?)");
    $statement->bind_param("ss", $username, $hashedPassword);
    $statement->execute();
}
//Post functions 

//Other functions
