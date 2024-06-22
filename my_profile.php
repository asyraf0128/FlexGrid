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

echo "<br><a href ='edit_profile.php' class='edit-profile-link'>Edit Profile</a>";
echo "</div>";

echo "<h3>Your Posts</h3>";

$postResult = queryMysql("SELECT * FROM posts WHERE user='$user' ORDER BY created_at DESC");

if ($postResult->num_rows) {
    echo "<div class='posts'>";
    while ($row = $postResult->fetch_assoc()) {
        $postId = $row['id'];
        $postUser = $row['user'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $slug = htmlspecialchars($row['slug']);
        $splitName = htmlspecialchars($row['split_name']);
        $media = unserialize($row['media']);
        $numReplies = $row['num_replies'];
        $numViews = $row['num_views'];
        $created_at = date('F j, Y', strtotime($row['created_at']));

        // Determine the thumbnail source
        $thumbnailSrc = '';
        if ($media && is_array($media)) {
            foreach ($media as $mediaPath) {
                $fileType = mime_content_type($mediaPath);
                if (strpos($fileType, 'image/') === 0) {
                    $thumbnailSrc = $mediaPath;
                    break;
                }
            }
        }
        if (!$thumbnailSrc) {
            // Check if the user's profile picture exists
            $profilePicPath = "$postUser.jpg";
            if (file_exists($profilePicPath)) {
                $thumbnailSrc = $profilePicPath;
            } else {
                // Use the default icon if no other image is available
                $thumbnailSrc = "icon2.jpg";
            }
        }

        echo "<div class='post'>
            <img class='thumbnail' src='$thumbnailSrc' alt='Thumbnail'>
            <div class='post-content'>
                <a href='view_post.php?id=$postId-$slug'>
                <h3>$title</h3>
                </a>
                <p>$description</p>
                <p>$splitName</p>
                <p>Posted by: $postUser</p>
                <p>Date Posted: $created_at</p>
                <p>Replies: $numReplies</p>
                <p>Views: $numViews</p>";

        // Display delete button if the post belongs to the logged-in user
        if ($postUser == $user) {
            echo "<div class='post-actions'>
                <form method='get' action='edit_post.php' class='inline-form'>
                    <input type='hidden' name='post_id' value='$postId'>
                    <input type='hidden' name='slug' value='$slug'>
                    <button type='submit' class='link-button'>Edit</button>
                </form>
                <form method='post' action='delete_post.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to delete this post?\");'>
                    <input type='hidden' name='post_id' value='$postId'>
                    <input type='hidden' name='slug' value='$slug'>
                    <button type='submit' class='link-button'>Delete</button>
                </form>
            </div>";
        }

        echo "</div></div>";
    }
    echo "</div>";
} else {
    echo "<p>No posts found.</p>";
}

echo "</div></body></html>";
?>
