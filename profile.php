<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='my_profile_container'>";

$user = isset($_GET['user']) ? sanitizeString($_GET['user']) : $_SESSION['user'];
$isOwnProfile = ($user == $_SESSION['user']);

if ($isOwnProfile) {
    echo "<h3>Your Profile</h3>";
} else {
    echo "<h3>$user's Profile</h3>";
}

$result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

if ($result->num_rows) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $text = stripslashes($row['text']);
    $workouts = $row['workouts'];
    $height = $row['height'];
    $weight = $row['weight'];
    $country = $row['country'];
    $image = $row['image'];

    if ($image) {
        echo '<p><img src="data:image/jpeg;base64,' . base64_encode($image) . '" alt="Profile Image" style="max-width: 200px; max-height: 200px;"></p>';
    } else {
        echo "<p>No profile image uploaded.</p>";
    }

    echo "<p>Text: $text</p>";
    echo "<p>Workouts: $workouts</p>";
    echo "<p>Height: $height</p>";
    echo "<p>Weight: $weight</p>";
    echo "<p>Country: $country</p>";
} else {
    echo "<p>No profile details found.</p>";
}

if ($isOwnProfile) {
    echo "<br><a href ='edit_profile.php'>Edit Profile</a>";
}

echo "<h3>$user's Posts</h3>";

// Query posts for the viewed user
$postResult = queryMysql("SELECT * FROM posts WHERE user='$user' ORDER BY created_at DESC");

if ($postResult->num_rows) {
    echo "<div class='posts'>";
    while ($postRow = $postResult->fetch_array(MYSQLI_ASSOC)) {
        $postId = $postRow['id'];
        $postTitle = htmlspecialchars($postRow['title']);
        $postDescription = htmlspecialchars($postRow['description']);
        $postSlug = htmlspecialchars($postRow['slug']);
        $postMedia = unserialize($postRow['media']);
        $postVisibility = htmlspecialchars($postRow['visibility']);
        $postCreatedAt = htmlspecialchars($postRow['created_at']);

        // Determine thumbnail source
        $thumbnailSrc = '';
        if (!empty($postMedia) && is_array($postMedia)) {
            foreach ($postMedia as $mediaPath) {
                $fileType = mime_content_type($mediaPath);
                if (strpos($fileType, 'image/') === 0) {
                    $thumbnailSrc = $mediaPath;
                    break;
                }
            }
        }
        if (!$thumbnailSrc) {
            // Use default icon or user's profile picture
            $profilePicPath = "$user.jpg";
            if (file_exists($profilePicPath)) {
                $thumbnailSrc = $profilePicPath;
            } else {
                $thumbnailSrc = "icon2.jpg"; // Default icon
            }
        }

        echo "<div class='post'>";
        echo "<img class='thumbnail' src='$thumbnailSrc' alt='Thumbnail'>";
        echo "<div class='post-content'>";
        echo "<a href='view_post.php?id=$postId-$postSlug'>";
        echo "<h3>$postTitle</h3>";
        echo "</a>";
        echo "<p>$postDescription</p>";

        // Display media (image or video)
        if (!empty($postMedia) && is_array($postMedia)) {
            foreach ($postMedia as $mediaPath) {
                $fileType = mime_content_type($mediaPath);
                if (strpos($fileType, 'image/') === 0) {
                    echo "<img src='$mediaPath' alt='Post Image' style='max-width: 200px; max-height: 200px;'>";
                } elseif (strpos($fileType, 'video/') === 0) {
                    echo "<video src='$mediaPath' controls style='max-width: 200px; max-height: 200px;'></video>";
                }
            }
        }

        echo "<p>Visibility: $postVisibility</p>";
        echo "<p>Posted on: $postCreatedAt</p>";

        // Allow editing for own posts
        if ($isOwnProfile) {
            echo "<a href='edit_post.php?id=$postId'>Edit Post</a>";
        }

        echo "</div></div>";
    }
    echo "</div>"; // Close posts container
} else {
    echo "<p>No posts found.</p>";
}

echo "</div></body></html>";
?>
