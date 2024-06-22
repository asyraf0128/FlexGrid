<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

if (isset($_GET['view'])) {
    $view = sanitizeString($_GET['view']);

    if ($view == $user) $name = "Your";
    else                $name = "$view's";

    echo "<div class='container'><h3>$name Profile</h3>";

    showProfile($view);
    echo "<a class='button' href='messages.php?view=$view'>View $name messages</a>";
    die("</div></body></html>");
}

if (isset($_GET['add'])) {
    $add = sanitizeString($_GET['add']);

    $result = queryMysql("SELECT * FROM friends WHERE user='$add' AND friend='$user'");
    if (!$result->num_rows)
        queryMysql("INSERT INTO friends VALUES ('$add', '$user')");
} elseif (isset($_GET['remove'])) {
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$remove' AND friend='$user'");
}

echo <<<_HTML
<div class="container">
    <h2>Search Members</h2>
    <form method="post" action="members.php" class="center">
        <input type="text" name="searchQuery" class="input-text" placeholder="Enter username" required>
        <button type="submit" class="button">Search</button>
    </form>
_HTML;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchQuery'])) {
    $searchQuery = sanitizeString($_POST['searchQuery']);
    $result = queryMysql("SELECT user FROM members WHERE user='$searchQuery'");

    if ($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $searchedUser = $row['user'];
        echo "<ul class='list-group'>";
        echo "<li class='list-group-item'><a href='members.php?view=$searchedUser'>$searchedUser</a>";
        $follow = "Follow";

        $result1 = queryMysql("SELECT * FROM friends WHERE user='$searchedUser' AND friend='$user'");
        $t1 = $result1->num_rows;

        $result2 = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='$searchedUser'");
        $t2 = $result2->num_rows;

        if (($t1 + $t2) > 1) echo " <span class='following-text'>mutual</span>";
        elseif ($t1)         echo " <span class='following-text'>following</span>";
        elseif ($t2)         { echo " <span class='following-text'>follower</span>";
                               $follow = "Reciprocate"; }

        if (!$t1) 
            echo " <a href='members.php?add=$searchedUser' class='button small'>$follow</a>";
        else
            echo " <button href='members.php?remove=$searchedUser' class='button'>Remove</button>";

        echo "</li></ul>";
    } else {
        echo "<p>No user found with username '$searchQuery'.</p>";
    }
}

echo "<h3>Members You Follow</h3><ul class='list-group'>";

$result = queryMysql("SELECT user FROM friends WHERE friend='$user'");
$num = $result->num_rows;

for ($j = 0; $j < $num; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $followedUser = $row['user'];

    echo "<li class='list-group-item'><a href='members.php?view=$followedUser'>$followedUser</a>";
    $follow = "Follow";

    $result1 = queryMysql("SELECT * FROM friends WHERE user='$followedUser' AND friend='$user'");
    $t1 = $result1->num_rows;

    $result2 = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='$followedUser'");
    $t2 = $result2->num_rows;

    if (($t1 + $t2) > 1) echo "  <span class='following-text'>mutual</span>";
    elseif ($t1)         echo "  <span class='following-text'>following</span>";
    elseif ($t2)         { echo " <span class='following-text'>follower</span>";
                           $follow = "Reciprocate"; }

    if (!$t1) 
        echo " <a href='members.php?add=$followedUser' class='button small'>$follow</a>";
    else
        echo " <button href='members.php?remove=$followedUser' class='button'>Remove</button>";

    echo "</li>";
}
echo "</ul></div></body></html>";
?>
