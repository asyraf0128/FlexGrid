<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

// Get the posts from the database
$query = "
    SELECT posts.id, posts.user, posts.title, posts.slug, posts.description, splits.name as split_name, posts.media, posts.visibility, posts.created_at, posts.num_replies, posts.num_views
    FROM posts 
    LEFT JOIN friends ON posts.user = friends.user
    LEFT JOIN splits ON posts.split_id = splits.id
    WHERE friends.friend='$user' OR posts.user='$user' OR posts.visibility='public'
    GROUP BY posts.id
    ORDER BY posts.created_at DESC";

$result = queryMysql($query);

if (!$result) {
    die("Database query failed: " . $connection->error);
}

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

        echo "</div></div>";
    }
}

echo "</div></body></html>";
?>
