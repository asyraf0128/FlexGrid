<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postId = sanitizeString($_POST['post_id']);
    $slug = sanitizeString($_POST['slug']);

    // Check if the post belongs to the logged-in user
    $query = "SELECT * FROM posts WHERE id='$postId' AND user='$user' AND slug='$slug'";
    $result = queryMysql($query);

    if ($result->num_rows > 0) {
        // Delete the post
        $deleteQuery = "DELETE FROM posts WHERE id='$postId' AND user='$user'";
        queryMysql($deleteQuery);
        header("Location: my_profile.php");
        exit();
    } else {
        echo "<div class='center'>You do not have permission to delete this post.</div>";
    }
}
?>
