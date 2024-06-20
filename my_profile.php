<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='my_profile_container'>";
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
        echo '<p><img src="data:image/jpeg;base64,' . base64_encode($image) . '" alt="Profile Image" class="profile-image"></p>';
    } else {
        echo "<p class= 'profile-image'>No profile image uploaded.</p>";
    }

    echo "<div class='profile-details'>";
    echo "<p><span>Bio:</span> $text</p>";
    echo "<p><span>Workouts:</span> $workouts</p>";
    echo "<p><span>Height(cm):</span> $height</p>";
    echo "<p><span>Weight(kg):</span> $weight</p>";
    echo "<p><span>Country:</span> $country</p>";
    echo "</div>";

}
else
{
    echo "<p>No profile details found.</p>";
}

echo "<br><a href ='edit_profile.php' class='edit-profile-link'>Edit Profile</a>";
echo "</div>";
echo "</body></html>";
?>