<?php
    session_start();
    require_once 'header.php';

    echo "<div class='center'>Welcome to FlexGrid,";

    if ($loggedin)  echo " $user, you are logged in";
    else            echo ' please sign up or log in';

    echo <<<_END
        </div><br>
        </div>
        <div data-role="footer">
            <h4>Team 6</h4<
        </body>
    </html>
    _END;
?>