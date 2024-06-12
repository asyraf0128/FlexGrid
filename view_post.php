<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

// Check if post_id is provided in the query string
if (isset($_GET['id'])) {
    $idSlug = sanitizeString($_GET['id']);
    list($postId, $slug) = explode('-', $idSlug, 2);
    
    // Query to fetch post details
    $query = "SELECT * FROM posts WHERE id = '$postId' AND slug='$slug'";
    $result = queryMysql($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Increment view count for the post
        queryMysql("UPDATE posts SET num_views = num_views + 1 WHERE id = $postId");

        // Fetch other details
        $postUser = $row['user'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $split = htmlspecialchars($row['split']);
        $media = unserialize($row['media']);
        $numReplies = $row['num_replies'];
        $numViews = $row['num_views'];
        $created_at = date('F j, Y', strtotime($row['created_at']));

        // Display post details
        echo "<div class='center'>
                <h3>$title</h3>
                <p>Description: $description</p>
                <p>Posted by: $postUser</p>
                <p>Date Posted: $created_at</p>
                <p>Replies: $numReplies</p>
                <p>Views: $numViews</p>";

        if ($split) echo "<p>Split: $split</p>";

        echo "</div>";
    } else {
        // Handle case where post_id is invalid or not found
        echo "<div class='center'><p>Post not found.</p></div>";
    }
} else {
    // Handle case where post_id is not provided in query string
    echo "<div class='center'><p>Invalid request.</p></div>";
}

echo "</div></body></html>";
?>
