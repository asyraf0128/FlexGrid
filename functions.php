<?php
$dbhost = 'localhost'; 
$dbname = 'flexgrid';
$dbuser = 'amir';
$dbpass = 'H@M21sc0';

$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error) die("Fatal Error");

function createTable($name, $query)
{
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
}

function queryMysql($query)
{
    global $connection;
    $result = $connection->query($query);
    if (!$result) die("Fatal Error: " . $connection->error);
    return $result;
}

function destroySession()
{
    $_SESSION = array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time() - 2592000, '/');

    session_destroy();
}

// Removes potentially malicious code or tags from user input
function sanitizeString($var)
{
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
}

function showProfile($user)
{
    if (file_exists("$user.jpg"))
        echo "<img src='$user.jpg' style='float:left;'>";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
    else echo "<p>Nothing to see here, yet</p><br>";
}

function generateSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}
?>
