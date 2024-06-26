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


function generateSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}

// Function to fetch workout frequency data for the logged-in user
function fetchWorkoutFrequencyData($user) {
    global $connection; // Assuming $connection is your MySQLi connection object

    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS workout_month, COUNT(*) AS num_workouts
              FROM posts
              WHERE is_workout = 1 AND user = '$user'
              GROUP BY workout_month";
    $result = $connection->query($query);

    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['workout_month'];
        $data[] = $row['num_workouts'];
    }

    return [
        'labels' => $labels,
        'data' => $data
    ];
}

// Function to fetch workout intensity data for the logged-in user
function fetchWorkoutIntensityData($user) {
    global $connection; // Assuming $connection is your MySQLi connection object

    $query = "SELECT DATE(created_at) AS workout_date, AVG(media) AS avg_weight
              FROM posts
              WHERE is_workout = 1 AND media IS NOT NULL AND user = '$user'
              GROUP BY workout_date";
    $result = $connection->query($query);

    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['workout_date'];
        $data[] = $row['avg_weight'];
    }

    return [
        'labels' => $labels,
        'data' => $data
    ];
}

?>
