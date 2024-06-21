<?php
session_start();

echo <<<_INIT
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='styles.css'>
    <link rel='icon' href='favicon.png' type='image/png'>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <script src='jquery-3.7.1.min.js'></script>
_INIT;

require_once 'functions.php';

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $loggedin = TRUE;
    $userstr = "Logged in as: $user";
} else {
    $loggedin = FALSE;
    $userstr = 'Welcome Guest';
}

echo <<<_MAIN
    <title>FlexGrid: $userstr</title>
    </head>
    <body>
    
    _MAIN;

if ($loggedin) {
    echo <<<_LOGGEDIN
    <div class="navbar">
        <div class="navbar-left">
            <img src='icon1.jpg' alt='FlexGrid' class='site-icon'>
        </div>
        <div class="navbar-right">
            <a href='index.php'>Home</a>
            <a href='members.php'>Members</a>
            <a href='splits.php'>Splits</a>
            <a href='my_profile.php'>Profile</a>           
            <a href='create_post.php'>Create Post</a>
            <div class='username'>$userstr</div>
            <a href='logout.php'>Log Out</a>
        </div>
    </div>
    <div class="content">
_LOGGEDIN;
} else {
    echo <<<_GUEST
    <div class="navbar">
        <div class="navbar-left">
            <img src='icon1.jpg' alt='FlexGrid' class='site-icon'>
        </div>
        <div class="navbar-right">
            <a href='signup.php'>Sign Up</a>
            <a href='login.php'>Log In</a>
        </div>
    </div>
    <div class='center'>
    <p class='info'>You must be logged in to use this app</p>
    </div>
_GUEST;
}
?>
