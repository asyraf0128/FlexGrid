<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

if (isset($_GET['view']))
{
    $view = sanitizeString($_GET['view']);

    if ($view == $user) $name = "Your";
    else                $name = "$view's";

    echo "<h3>$name Profile</h3>";

    showProfile($view);
    echo "<a class='button'
         href='messages.php?view=$view'>View $name messages</a>";
    die("</div></body></html>");
}

if (isset($_GET['add']))
{
    $add = sanitizeString($_GET['add']);

    $result = queryMysql("SELECT * FROM friends WHERE user='$add' AND friend='$user'");
    if(!$result->num_rows)
        queryMysql("INSERT INTO friends VALUES ('$add', '$user')");
}
elseif (isset($_GET['remove']))
{
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$remove' AND friend='$user'");
}

echo <<<_HTML
<div class="center">
    <h2>Search Members</h2>
    <form method="post" action="members.php">
        <input type="text" name="searchQuery" placeholder="Enter username" required>
        <input type="submit" value="Search">
    </form>
</div>
_HTML;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchQuery'])) {
    $searchQuery = sanitizeString($_POST['searchQuery']);
    $result = queryMysql("SELECT user FROM members WHERE user='$searchQuery'");

    if ($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $searchedUser = $row['user'];
        echo "<ul>";
        echo "<li><a href='members.php?view=$searchedUser'>$searchedUser</a>";
        $follow = "follow";

        // Check if you are following them
        $result1 = queryMysql("SELECT * FROM friends WHERE user='$searchedUser' AND friend='$user'");
        $t1 = $result1->num_rows;

        // Check if they are following you
        $result2 = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='$searchedUser'");
        $t2 = $result2->num_rows;

        if (($t1 + $t2) > 1) echo " &harr; is a mutual friend";
        elseif ($t1)         echo " &larr; you are following";
        elseif ($t2)         { echo " &rarr; is following you";
                               $follow = "recip"; }

        // Display follow/unfollow link based on follow status
        if (!$t1) 
            echo " [<a href='members.php?add=$searchedUser'>$follow</a>]";
        else
            echo " [<a href='members.php?remove=$searchedUser'>unfollow</a>]";

        echo "</li></ul>";
    } else {
        echo "<p>No user found with username '$searchQuery'.</p>";
    }
}

echo "<h3>Members You Follow</h3><ul>";

$result = queryMysql("SELECT user FROM friends WHERE friend='$user'");
$num = $result->num_rows;

for ($j = 0; $j < $num; ++$j)
{
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $followedUser = $row['user'];

    echo "<li><a href='members.php?view=$followedUser'>$followedUser</a>";
    $follow = "follow";

    // Check if you are following them
    $result1 = queryMysql("SELECT * FROM friends WHERE user='$followedUser' AND friend='$user'");
    $t1 = $result1->num_rows;

    // Check if they are following you
    $result2 = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='$followedUser'");
    $t2 = $result2->num_rows;

    if (($t1 + $t2) > 1) echo " &harr; is a mutual friend";
    elseif ($t1)         echo " &larr; you are following";
    elseif ($t2)         { echo " &rarr; is following you";
                           $follow = "recip"; }

    // Display follow/unfollow link based on follow status
    if (!$t1) 
        echo " [<a href='members.php?add=$followedUser'>$follow</a>]";
    else
        echo " [<a href='members.php?remove=$followedUser'>unfollow</a>]";

    echo "</li>";
}
echo "</ul></div></body></html>";
?>
