<?php
    require_once 'header.php';
    $error = $user = $pass = "";

    if (isset($_POST['user']))
    {
        $user   =   sanitizeString($_POST['user']);
        $pass   =   sanitizeString($_POST['pass']);
        
        if ($user == "" || $pass == "")
            $error = 'Not all fields were filled';
        else
        {
            $result = queryMySQL("SELECT user,pass FROM members
                WHERE user='$user' AND pass='$pass'");

            if ($result->num_rows == 0)
            {
                $error = "Invalid login attempt";
            }
            else
            {
                $_SESSION['user'] = $user;
                $_SESSION['pass'] = $pass;
                die("You are now loggged in. Please <a data-transition='slide'
                    href='header.php?view=$user'>click here</a> to continue.</div>
                        </body></html>");
            }
        }
    }

    echo <<<_END
            <form method='post' action='login.php'>
            <div class="imgcontainer">
                <img src="flexgridlogo.jpg" alt="Logo" class="logo">
            </div>
            
            <div class="container">
                <div class="form-box">
                    <div data-role='fieldcontain'>
                        <label></label>
                        <span class='error'>$error</span>
                    </div>
                    <div data-role='field contain' class='details'>
                        <label></label>
                        Please enter your details to log in
                    </div>
                    <div data-role='fieldcontain'>
                        <label class='username'>Username</label>
                        <input type='text' placeholder="Enter Username" maxlength='16' name='user' value='$user'>
                    </div>
                    <div data-role='fieldcontain'>
                        <label class='password'>Password</label>
                        <input type='password' placeholder="Enter Password" maxlength='16' name='pass' value='$pass'>
                    </div>
                    <div data-role='fieldcontain'>
                        <label></label>
                        <input class='button' data-transition='slide' type='submit' value='Login'>
                    </div>
                </div>
            </div>
            </form>
            </div>
            </body>
        </html>
        _END;
?>
        