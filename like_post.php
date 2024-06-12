<?php
require_once 'functions.php';

$post_id = sanitizeString($_POST['post_id']);

if (empty($post_id)) {
    echo json_encode(["error" => "Invalid post ID."]);
    exit();
}

// Check if the user has already liked the post
$result = queryMysql("SELECT * FROM likes WHERE user='$user' AND post_id='$post_id'");

if ($result->num_rows) {
    // If the user has liked the post, remove the like
    queryMysql("DELETE FROM likes WHERE user='$user' AND post_id='$post_id'");
    $liked = false;
} else {
    // If the user has not liked the post, add a like
    queryMysql("INSERT INTO likes (user, post_id) VALUES ('$user', '$post_id')");
    $liked = true;
}

// Get the updated like count
$result = queryMysql("SELECT COUNT(*) AS like_count FROM likes WHERE post_id='$post_id'");
$row = $result->fetch_assoc();
$like_count = $row['like_count'];

// Return the updated like count and status as JSON
echo json_encode(['liked' => $liked, 'like_count' => $like_count]);
?>
