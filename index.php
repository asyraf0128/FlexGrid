<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='center'>";

// Get the posts from the database
$query = "
    SELECT posts.id, posts.user, posts.title, posts.slug, posts.description, posts.split, posts.media, posts.visibility, posts.created_at, posts.num_replies, posts.num_views
    FROM posts 
    INNER JOIN friends ON posts.user = friends.user
    WHERE friends.friend='$user' OR posts.user='$user' OR posts.visibility='public'
    GROUP BY posts.id
    ORDER BY posts.created_at DESC";

$result = queryMysql($query);

$num = $result->num_rows;

echo "<div class='posts'>";

if ($num == 0) {
    echo "<p>No posts to display</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        $postId = $row['id'];
        $postUser = $row['user'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $slug = htmlspecialchars($row['slug']);
        $split = htmlspecialchars($row['split']);
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
                // If no picture is posted, use the posting user's profile picture
                $thumbnailSrc = "$postUser.jpg";
            }

        echo "<div class='post'>
            <a href='view_post.php?id=$postId-$slug'>
                <img class='thumbnail' src='$thumbnailSrc' alt='Thumbnail'>
                <h3>$title</h3>
            </a>
            <p>Posted by: $postUser</p>
            <p>Date Posted: $created_at</p>
            <p>Replies: $numReplies</p>
            <p>Views: $numViews</p>";

        if ($split) echo "<p>Split: $split</p>";

         // Display delete button if the post belongs to the logged-in user
         if ($postUser == $user) {
            echo "<form method='post' action='delete_post.php' onsubmit='return confirm(\"Are you sure you want to delete this post?\");'>
                <input type='hidden' name='post_id' value='$postId'>
                <input type='hidden' name='slug' value='$slug'>
                <button type='submit'>Delete</button>
            </form>";
        }

        echo "</div>";
    }
}

echo "</div></body></html>";
?>
