<?php
$dbhost = 'localhost'; 
$dbname = 'mydatabase';
$dbuser = 'root';
$dbpass = 'Jireh@05';

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

function showProfile($user){
    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
   
    if ($result->num_rows)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if (!empty($row['image'])) {
            $imageData = base64_encode($row['image']);
            echo "<br><img src='data:image/jpeg;base64,$imageData' alt='Profile Image' width='600' /><br>";
        }
    }
}
?>