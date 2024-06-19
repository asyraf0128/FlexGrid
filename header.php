<?php //
    session_start();

echo <<<_INIT
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta charset='utf-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1'>
                        <link rel='stylesheet' href='stylessss.css'>
                        <link rel='icon' href='favicon.png' type='image/png'>
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
        </head>
        <body>
            <div data-role='page'>
                <div data-role='header'>
                    <div id='logo'
                        class='center'>Flex<img id='flex' src='flexgridicon.jpg'>Grid</div>
                    <div class='username'>$userstr</div>
                </div>
                <div data-role='content'>

        _MAIN;

            if ($loggedin)
            {
        echo <<<_LOGGEDIN
                <div class='center'>
                    <a data-role='button' data-inline='true'
                        href='index.php'>Home</a>
                    <a data-role='button' data-inline='true'
                        href='members.php'>Members</a>
                    <a data-role='button' data-inline='true'
                        href='splits.php'>Splits</a>
                    <a data-role='button' data-inline='true'
                        href='messages.php'>Messages</a>
                    <a data-role='button' data-inline='true'
                        href='my_profile.php'>Profile</a>
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
                    <a data-role='button' data-inline='true' 
                        href ='index.php'>Home</a>
                    <a data-role='button' data-inline='true' 
                        href ='signup.php'>Sign Up</a>
                    <a data-role='button' data-inline='true'
                        href ='login.php'>Log In</a>
                </div>
                <p class='info'>(You must be logged in to use this app)</p>

        _GUEST;
            }
?>