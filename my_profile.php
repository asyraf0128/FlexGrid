<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<h3>Your Profile</h3>";

$user = $_SESSION['user'];

$result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

if ($result->num_rows)
{
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $text = stripslashes($row['text']);
    $workouts = $row['workouts'];
    $height = $row['height'];
    $weight = $row['weight'];
    $country = $row['country'];
    $image = $row['image'];

    if ($image){
        echo '<p><img src="data:image/jpeg;base64,' . base64_encode($image) . '" alt="Profile Image" style="max-width: 200px; max-height: 200px;"></p>';
    } else {
        echo "<p>No profile image uploaded.</p>";
    }

    echo "<p>Text: $text</p>";
    echo "<p>Workouts: $workouts</p>";
    echo "<p>Height: $height</p>";
    echo "<p>Weight: $weight</p>";
    echo "<p>Country: $country</p>";

}
else
{
    echo "<p>No profile details found.</p>";
}

echo "<br><a href ='edit_profile.php'>Edit Profile</a>";
echo "</div></body></html>";
?>