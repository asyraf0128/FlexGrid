<?php //
    session_start();

echo <<<_INIT
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta charset='utf-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1'>
                        <link rel='stylesheet' href='jquery.mobile-1.4.5.min.css'>
                        <link rel='stylesheet' href='styles.css'>
                        <script src='jquery-3.7.1.min.js'></script>
                        
    _INIT;

        require_once 'functions.php';

        $userstr = 'Welcome Guest';

        if (isset($_SESSION['user']))
        {
            $user       = $_SESSION['user'];
            $loggedin   = TRUE;
            $userstr    ="Logged in as: $user";
        }
        else $loggedin = FALSE;



    echo <<<_MAIN
        <title>FlexGrid: $userstr</title>
        </head>
        <body>
            <div data-role='page'>
                <div data-role='header'>
                    <div id='logo'
                        class='center'><img id='flex' src='flexgridicon.jpg'>FlexGrid</div>
                    <div class='username'>$userstr</div>
                </div>
                <div data-role='content'>

        _MAIN;

            if ($loggedin)
            {
        echo <<<_LOGGEDIN
                <div class='center'>
                    <a data-role='button' data-inline='true' data-icon='home'
                        href='index.php'>Home</a>
                    <a data-role='button' data-inline='true'
                        href='members.php'>Members</a>
                    <a data-role='button' data-inline='true'
                        href='friends.php'>Friends</a>
                    <a data-role='button' data-inline='true'
                        href='messages.php'>Messages</a>
                    <a data-role='button' data-inline='true'
                        href='profile.php'>Edit Profile</a>
                    <a data-role='button' data-inline='true'
                        href='logout.php'>Log Out</a>
                    <a data-role='button' data-inline='true'
                        href='create_post.php'>Create Post</a>
                </div>
        _LOGGEDIN;
            }
            else
            {
        echo <<<_GUEST
                <div class='center'>
                    <a data-role='button' data-inline='true' data-icon='home'
                        href ='index.php'>Home</a>
                    <a data-role='button' data-inline='true' data-icon='home'
                        href ='signup.php'>Sign Up</a>
                    <a data-role='button' data-inline='true' data-icon='home'
                        href ='login.php'>Log In</a>
                </div>
                <p class='info'>(You must be logged in to use this app)</p>

        _GUEST;
            }
?>