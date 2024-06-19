<?php
    require_once 'header.php';
    require_once 'functions.php'; // Assuming this file contains sanitizeString, queryMysql, and destroySession functions

    echo <<<_END
    <script>
        function checkUser(user)
        {
            if (user.value == '')
            {
                $('#used').html('&nbsp;');
                return;
            }

            $.post
            (
                'checkuser.php',
                { user : user.value },
                function(data)
                {
                    $('#used').html(data);
                }
            );
        }
    </script>
_END;

    $error = $user = $pass = "";
    if (isset($_SESSION['user'])) destroySession();

    if (isset($_POST['user']))
    {
        $user = sanitizeString($_POST['user']);
        $pass = sanitizeString($_POST['pass']);

        if ($user == "" || $pass == "")
            $error = 'Not all fields were entered<br><br>';
        else
        {
            $result = queryMysql("SELECT * FROM members WHERE user='$user'");

            if ($result->num_rows)
                $error = 'That username is already in use<br><br>';
            else
            {
                queryMysql("INSERT INTO members (user, pass) VALUES('$user', '$pass')");
                die('<h4>Account created</h4>Please log in.</div></body></html>');
            }
        }
    }

    $user = htmlspecialchars($user);
    $pass = htmlspecialchars($pass);

    echo <<<_END
    <form method='post' action='signup.php'>$error
    <div class="imgcontainer">
        <img src="flexgridlogo.jpg" alt="Logo" class="logo">
    </div>

    <div class="container">
        <div class="form-box">
            <div data-role='fieldcontain' class='details'>
                <label></label>
                Please enter your desired username and password
            </div>
            <div data-role='fieldcontain'>
                <label class='username'>Username</label>
                <input type='text' placeholder="Enter Username" maxlength='16' name='user' value='$user' onBlur='checkUser(this)'>
                <label></label><div id='used'>&nbsp;</div>
            </div>
            <div data-role='fieldcontain'>
                <label class='password'>Password</label>
                <input type='password' placeholder="Enter Password" maxlength='16' name='pass' value='$pass'>
            </div>
            <div data-role='fieldcontain'>
                <label></label>
                <input class='button' data-transition='slide' type='submit' value='Sign Up'>
            </div>
        </div>
    </div>
    </form>
    </body>
    </html>
_END;
?>
