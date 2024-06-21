<?php
require_once 'functions.php';

$post_id = sanitizeString($_POST['post_id']);
$comment = sanitizeString($_POST['comment']);

if (empty($post_id) || empty($comment)) {
    echo json_encode(["error" => "Invalid post ID or comment."]);
    exit();
}

// Insert the comment into the database
queryMysql("INSERT INTO comments (user, post_id, comment, created_at) VALUES ('$user', '$post_id', '$comment', NOW())");

// Get the updated comment count
$result = queryMysql("SELECT COUNT(*) AS comment_count FROM comments WHERE post_id='$post_id'");
$row = $result->fetch_assoc();
$comment_count = $row['comment_count'];

// Return the updated comment count as JSON
echo json_encode(['comment_count' => $comment_count]);
?>
