<?php
require_once 'functions.php';

if (!$loggedin) {
    echo json_encode(["error" => "You need to be logged in to share posts."]);
    exit();
}

$post_id = sanitizeString($_POST['post_id']);

if (empty($post_id)) {
    echo json_encode(["error" => "Invalid post ID."]);
    exit();
}

// Insert the share action into the database
queryMysql("INSERT INTO shares (user, post_id, shared_at) VALUES ('$user', '$post_id', NOW())");

// Return a success message
echo json_encode(['shared' => true]);
?>
