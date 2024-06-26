<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

$user = $_SESSION['user'];

// Check if post_id is provided in the query string
if (isset($_GET['id'])) {
    $idSlug = sanitizeString($_GET['id']);
    list($postId, $slug) = explode('-', $idSlug, 2);

    // Handle reply form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
        $replyText = sanitizeString($_POST['replyText']);
        if (!empty($replyText)) {
            queryMysql("INSERT INTO replies (post_id, user, text, created_at) VALUES ('$postId', '$user', '$replyText', NOW())");
            queryMysql("UPDATE posts SET num_replies = num_replies + 1 WHERE id = $postId");
            $_SESSION['reply_message'] = 'Reply added successfully.';
        } else {
            $_SESSION['reply_message'] = 'Reply cannot be empty.';
        }
        // Redirect to avoid resubmission and add a query parameter to indicate a reply was submitted
        header("Location: view_post.php?id=$idSlug#replies");
        exit();
    }

    // Handle reply deletion
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_reply'])) {
        $replyId = sanitizeString($_POST['reply_id']);
        queryMysql("DELETE FROM replies WHERE id = '$replyId' AND user = '$user'");
        queryMysql("UPDATE posts SET num_replies = num_replies - 1 WHERE id = $postId");
        $_SESSION['reply_message'] = 'Reply deleted successfully.';
        // Redirect to avoid resubmission
        header("Location: view_post.php?id=$idSlug#replies");
        exit();
    }

    // Query to fetch post details
    $query = "SELECT * FROM posts WHERE id = '$postId' AND slug='$slug'";
    $result = queryMysql($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Increment view count for the post if the last view was more than an hour ago
        $lastViewTime = isset($_SESSION['last_view_time_' . $postId]) ? $_SESSION['last_view_time_' . $postId] : 0;
        if (time() - $lastViewTime > 3600) {
            queryMysql("UPDATE posts SET num_views = num_views + 1 WHERE id = $postId");
            $_SESSION['last_view_time_' . $postId] = time();
        }

        // Fetch other details
        $postUser = $row['user'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $splitId = $row['split_id']; // Fetch split_id instead of split name
        $media = unserialize($row['media']);
        $numReplies = $row['num_replies'];
        $numViews = $row['num_views'];
        $created_at = date('F j, Y', strtotime($row['created_at']));

        // Fetch and display replies
        $repliesQuery = "SELECT * FROM replies WHERE post_id = '$postId' ORDER BY created_at ASC";
        $repliesResult = queryMysql($repliesQuery);

        // Fetch split name from splits table
        $splitName = '';
        if ($splitId) {
            $splitResult = queryMysql("SELECT name FROM splits WHERE id = '$splitId'");
            if ($splitResult->num_rows == 1) {
                $splitRow = $splitResult->fetch_assoc();
                $splitName = htmlspecialchars($splitRow['name']);
            }
        }

        // Display post details
        echo "<div class='post-details'>
                <h3>$title</h3>
                <p>$description</p>";

        if ($splitName) echo "<p>Split: $splitName</p>";

        echo "</div>";

        // Fetch and display workouts
        if ($splitId) {
            $workoutsQuery = "SELECT * FROM workouts WHERE split_id = '$splitId'";
            $workoutsResult = queryMysql($workoutsQuery);

            if ($workoutsResult->num_rows > 0) {
                echo "<div class='workouts-container'><h4>Workouts</h4>";
                while ($workoutRow = $workoutsResult->fetch_assoc()) {
                    $workoutName = htmlspecialchars($workoutRow['name']);
                    $lastWeight = htmlspecialchars($workoutRow['last_weight']);
                    $lastSets = htmlspecialchars($workoutRow['last_sets']);
                    $lastReps = htmlspecialchars($workoutRow['last_reps']);

                    echo "<div class='workout'>
                            <p><strong>$workoutName</strong></p>";
                    if (!empty($lastWeight)) echo "<p>Weight: {$lastWeight}kg</p>";
                    if (!empty($lastSets)) echo "<p>Sets: {$lastSets} sets</p>";
                    if (!empty($lastReps)) echo "<p>Reps: {$lastReps} reps</p>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<div class='workouts-container'><p>No workouts found for this split.</p></div>";
            }
        }

      
        echo"<div class='post-stats'>
        <p>Posted by: $postUser</p>
        <p>Date Posted: $created_at</p>
        <p>Views: $numViews</p>
        <p>Replies: $numReplies</p>
        </div>";

        // Display success or error message
        if (isset($_SESSION['reply_message'])) {
            echo "<div class='message'><p>{$_SESSION['reply_message']}</p></div>";
            unset($_SESSION['reply_message']);
        }

         // Fetch user profile image
         $profileResult = queryMysql("SELECT image FROM profiles WHERE user='$user'");
         $profileImage = '';
         if ($profileResult->num_rows == 1) {
             $profileRow = $profileResult->fetch_assoc();
             $profileImage = $profileRow['image'];
         }
 
         // Display reply form
         echo "<div class='reply-section'>
                 <form method='post' action='view_post.php?id=$idSlug'>
                     <div class='reply-form'>
                         <div class='profile-image'>";
         if ($profileImage) {
             echo '<img src="data:image/jpeg;base64,' . base64_encode($profileImage) . '" alt="Profile Image" class="profile-pic">';
         }
         echo "        </div>
                         <textarea name='replyText' placeholder='Write your reply here...' required class='reply-text'></textarea>
                         <button type='submit' name='reply' class='reply-button'>Reply</button>
                     </div>
                 </form>";

        if ($repliesResult->num_rows > 0) {
            echo "<div class='replies-container'><h4>Replies</h4>";
            while ($replyRow = $repliesResult->fetch_assoc()) {
                $replyId = $replyRow['id'];
                $replyUser = htmlspecialchars($replyRow['user']);
                $replyText = htmlspecialchars($replyRow['text']);
                $replyCreatedAt = date('F j, Y, g:i a', strtotime($replyRow['created_at']));

                echo "<div class='reply'>
                        <p><strong>$replyUser</strong> replied on $replyCreatedAt:</p>
                        <p>$replyText</p>";
                
                // Display delete button if the reply belongs to the logged-in user
                if ($replyUser == $user) {
                    echo "<form method='post' action='view_post.php?id=$idSlug' onsubmit='return confirm(\"Are you sure you want to delete this reply?\");'>
                            <input type='hidden' name='reply_id' value='$replyId'>
                            <button type='submit' class='link-button' name='delete_reply'>Delete</button>
                          </form>";
                }

                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div class='replies-container'><p>No replies yet. Be the first to reply!</p></div>";
        }

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to the bottom if the page has the #replies anchor
    if (window.location.hash === '#replies') {
        window.scrollTo(0, document.body.scrollHeight);
    }
});
</script>

