<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

echo <<<_END
<script>
$(document).ready(function() {
    $('.like-button').click(function(event) {
        event.preventDefault();
        var post_id = $(this).data('post-id');
        var likeButton = $(this);
        
        console.log("Like button clicked for post_id: " + post_id);
        
        $.ajax({
            type: 'POST',
            url: 'like_post.php',
            data: { post_id: post_id },
            dataType: 'json',
            success: function(response) {
                console.log("Response: ", response);
                if (response.liked) {
                    likeButton.text('Unlike');
                } else {
                    likeButton.text('Like');
                }
                $('#like-count-' + post_id).text(response.like_count);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
            }
        });
    });

    $('.comment-form').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        // Get the form data
        var formData = $(this).serialize();

        // Send an AJAX request to comment_post.php
        $.ajax({
            type: 'POST',
            url: 'comment_post.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Handle the response
                console.log(response); // Log the response for debugging
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(error); // Log any errors for debugging
            }
        });
    });
    
    $('.share-button').click(function(event) {
        event.preventDefault();
        var post_id = $(this).data('post-id');

        $.ajax({
            type: 'POST',
            url: 'share_post.php',
            data: { post_id: post_id },
            success: function(response) {
                alert('Post shared!');
            }
        });
    });
});
</script>
_END;

if (!$loggedin) die("</div></body></html>");

echo "<div class='center'>";

// Get the posts from the database
$query = "
    SELECT posts.id, posts.user, posts.title, posts.description, posts.split, posts.image, posts.video, posts.visibility, posts.created_at, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
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
    for ($j = 0; $j < $num; ++$j) {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $postId = $row['id'];
        $postUser = $row['user'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $split = htmlspecialchars($row['split']);
        $image = htmlspecialchars($row['image']);
        $video = htmlspecialchars($row['video']);
        $likeCount = $row['like_count'];
        $commentCount = $row['comment_count'];

        echo "<div class='post'>
                <h3>$title</h3>
                <p>$description</p>";

        if ($split) echo "<p>Split: $split</p>";
        if ($image) echo "<img src='$image' alt='Post Image'>";
        if ($video) echo "<video src='$video' controls></video>";

        echo "<div class='post-actions'>
                <button class='like-button' data-post-id='$postId'>Like</button>
                <span id='like-count-$postId'>$likeCount</span> Likes
                <form class='comment-form' method='post'>
                    <input type='hidden' name='post_id' value='$postId'>
                    <input type='text' name='comment' placeholder='Add a comment'>
                    <button type='submit'>Comment</button>
                </form>
                <span id='comment-count-$postId'>$commentCount</span> Comments
                <button class='share-button' data-post-id='$postId'>Share</button>
              </div>
              <div class='profile-actions'>
                <a href='profile.php?user=$postUser'><img src='$postUser.jpg' alt='$postUser'></a>";

        // Unfollow button
        echo "<form method='post' action='unfollow_user.php'>
                <input type='hidden' name='unfollow_user' value='$postUser'>
                <button type='submit'>Unfollow</button>
              </form>";

        echo "</div></div>";
    }
}

echo "</div></body></html>";
?>
