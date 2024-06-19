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


echo "<h3>Your Posts</h3>";

$postResult = queryMysql("SELECT * FROM posts WHERE user='$user' ORDER BY created_at DESC");

if ($postResult->num_rows) {
    while ($postRow = $postResult->fetch_array(MYSQLI_ASSOC)) {
        $postId = $postRow['id'];
        $postTitle = htmlspecialchars($postRow['title']);
        $postDescription = htmlspecialchars($postRow['description']);
        $postMedia = unserialize($postRow['media']);
        $postVisibility = htmlspecialchars($postRow['visibility']);
        $postCreatedAt = htmlspecialchars($postRow['created_at']);

        echo "<div class='post'>";
        echo "<h4>$postTitle</h4>";
        echo "<p>$postDescription</p>";

        if (!empty($postMedia)) {
            foreach ($postMedia as $mediaPath) {
                $mediaType = mime_content_type($mediaPath);
                if (strpos($mediaType, 'image/') === 0) {
                    echo "<img src='$mediaPath' alt='Post Image' style='max-width: 200px; max-height: 200px;'>";
                } elseif (strpos($mediaType, 'video/') === 0) {
                    echo "<video src='$mediaPath' controls style='max-width: 200px; max-height: 200px;'></video>";
                }
            }
        }

        echo "<p>Visibility: $postVisibility</p>";
        echo "<p>Posted on: $postCreatedAt</p>";
        echo "<a href='edit_post.php?id=$postId'>Edit Post</a>";
        echo "</div><br>";
    }
} else {
    echo "<p>No posts found.</p>";
}

echo "</div></body></html>";
?>