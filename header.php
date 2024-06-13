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
                        <link rel='stylesheet' href='home.css'>
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
         <header class="header">
          <nav>
            <div data-role='page'>
                <div data-role='header'>
                    <label class='logo'
                        id='center'>Flex<img id='flex' src='flex.gif'>Grid</div>
                    <div class='username'>$userstr</div>
                </div>
                <div data-role='content'>
          </nav>

        _MAIN;

            if ($loggedin)
            {
        echo <<<_LOGGEDIN
        
          <nav>
            <nav class="navbar">
              <div class="center">
                <ul>
                    <li><a data-role='button' data-inline='true' data-icon='home'
                        data-transition="slide" href='members.php?view=$user'>Home</a></li>
                    <li><a data-role='button' data-inline='true'
                        data-transition="slide" href='members.php'>Members</a></li>
                    <li><a data-role='button' data-inline='true'
                        data-transition="slide" href='friends.php'>Friends</a></li>
                    <li><a data-role='button' data-inline='true'
                        data-transition="slide" href='messages.php'>Messages</a></li>
                    <li><a data-role='button' data-inline='true'
                        data-transition="slide" href='profile.php'>Edit Profile</a></li>
                    <li><a data-role='button' data-inline='true'
                        data-transition="slide" href='logout.php'>Log Out</a></li>
                </ul>
              </div>
            </nav>
        
        _LOGGEDIN;
            }
            else
            {
        echo <<<_GUEST

          <nav>
                <nav class="navbar">
                <div class="center">
                 <ul>
                    <li><a data-role='button' data-inline='true' data-icon='home'
                        data-transition='slide' href ='index.php'>Home</a></li>
                    <li><a data-role='button' data-inline='true' data-icon='home'
                        data-transition='slide' href ='signup.php'>Sign Up</a></li>
                    <li><a data-role='button' data-inline='true' data-icon='home'
                        data-transition='slide' href ='login.php'>Log In</a></li>
                 </ul>
                </div>
                </nav>
         </header>
        </body>
        </html>

                <p class='info'>(You must be logged in to use this app)</p>

        _GUEST;
            }
?>