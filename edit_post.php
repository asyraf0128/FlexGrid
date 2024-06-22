<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

// Check if user is logged in
if (!$loggedin) {
    die("Please log in to edit a post.");
}

$error = '';
$user = $_SESSION['user'];
$postId = isset($_GET['id']) ? sanitizeString($_GET['id']) : null;

// Validate and fetch post details for editing
if ($postId) {
    $postResult = queryMysql("SELECT * FROM posts WHERE id='$postId' AND user='$user'");
    if ($postResult->num_rows > 0) {
        $post = $postResult->fetch_assoc();
        $title = htmlspecialchars($post['title']);
        $description = htmlspecialchars($post['description']);
        $visibility = $post['visibility'];
        $mediaPaths = unserialize($post['media']);
        $splitId = $post['split_id'];
    } else {
        $error = "Post not found or you do not have permission to edit this post.";
    }
} else {
    $error = "Invalid post ID.";
}

// Fetch splits for the default split group
$defaultSplit = queryMysql("SELECT id FROM split_groups WHERE user='$user' AND is_default=TRUE");
$defaultSplitId = $defaultSplit->num_rows > 0 ? $defaultSplit->fetch_assoc()['id'] : null;
$splits = queryMysql("SELECT * FROM splits WHERE group_id='$defaultSplitId'");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['discard'])) {
        header("Location: index.php");
        exit();
    }

    $splitId = sanitizeString($_POST['split']);
    $description = sanitizeString($_POST['description']);
    $visibility = sanitizeString($_POST['visibility']);

    $uploadsDir = 'uploads/';
    $mediaPaths = [];
    $mediaUploaded = false;

    foreach ($_FILES['media']['name'] as $key => $name) {
        if (!empty($name)) {
            $mediaPath = $uploadsDir . basename($name);
            $tmpName = $_FILES['media']['tmp_name'][$key];

            if (file_exists($tmpName) && is_uploaded_file($tmpName)) {
                $fileType = mime_content_type($tmpName);

                if (strpos($fileType, 'image/') === 0 || strpos($fileType, 'video/') === 0) {
                    if (move_uploaded_file($tmpName, $mediaPath)) {
                        $mediaPaths[] = $mediaPath;
                        $mediaUploaded = true;
                    } else {
                        $error = 'Failed to upload file: ' . $name;
                        break;
                    }
                } else {
                    $error = 'Invalid file type: ' . $name;
                    break;
                }
            } else {
                $error = 'Temporary file for ' . $name . ' does not exist.';
                break;
            }
        }
    }

    if (empty($error)) {
        $is_workout = !empty($splitId) ? 1 : 0;
        $mediaSerialized = $mediaUploaded ? "'" . serialize($mediaPaths) . "'" : 'NULL';

        $query = "UPDATE posts SET description='$description', split_id='$splitId', media=$mediaSerialized, visibility='$visibility', is_workout='$is_workout' WHERE id='$postId' AND user='$user'";

        if (queryMysql($query)) {
            if (!empty($_POST['workouts'])) {
                queryMysql("DELETE FROM workout_details WHERE post_id='$postId'");

                foreach ($_POST['workouts'] as $workoutId => $details) {
                    $weight = sanitizeString($details['weight']) ?: 'NULL';
                    $sets = sanitizeString($details['sets']) ?: 'NULL';
                    $reps = sanitizeString($details['reps']) ?: 'NULL';

                    queryMysql("INSERT INTO workout_details (post_id, workout_id, weight, sets, reps) VALUES ('$postId', '$workoutId', $weight, $sets, $reps)");
                    queryMysql("UPDATE workouts SET last_weight=$weight, last_sets=$sets, last_reps=$reps WHERE id='$workoutId'");
                }
            }
            header("Location: index.php");
            exit();
        } else {
            $error = 'Failed to update post. Please try again.';
        }
    }
}
?>

<!-- HTML Form to Edit Post -->
<div class="center">
    <h3>Edit Post</h3>
</div>

<div class="post-background-form">
    <form method="post" action="edit_post.php?id=<?= $postId ?>" enctype="multipart/form-data" onsubmit="return confirmDiscard(event)">
        <div data-role="fieldcontain">
            <label for="title">Post Title:</label>
            <br>
            <input type="text" id="title" name="title" value="<?= $title ?>" disabled>
        </div>
        <div data-role="fieldcontain">
            <label for="description">Post Description:</label>
            <br>
            <textarea id="description" name="description" placeholder="Enter your post description..."><?= $description ?></textarea>
        </div>
        <div data-role="fieldcontain">
            <label for="split">Select Split:</label>
            <select id="split" name="split" onchange="fetchWorkouts(this.value)">
                <option value="">Select...</option>
                <?php
                if ($splits->num_rows > 0) {
                    while ($split = $splits->fetch_assoc()) {
                        $splitId = $split['id'];
                        $splitName = htmlspecialchars($split['name']);
                        $selected = $splitId == $post['split_id'] ? 'selected' : '';
                        echo "<option value='$splitId' $selected>$splitName</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div id="workout-container"></div>
        <div data-role="fieldcontain">
            <label for="visibility">Post Visibility:</label>
            <select id="visibility" name="visibility">
                <option value="public" <?= $visibility == 'public' ? 'selected' : '' ?>>Public</option>
                <option value="private" <?= $visibility == 'private' ? 'selected' : '' ?>>Private</option>
            </select>
        </div>
        <div data-role="fieldcontain">
            <label for="media">Upload Image/Video:</label>
            <input type="file" class="link-button" id="media" name="media[]" accept="image/*,video/*" multiple onchange="previewFiles()">
        </div>
        <div id="preview-container">
            <?php foreach ($mediaPaths as $mediaPath): ?>
                <?php if (strpos($mediaPath, 'uploads/') !== false): ?>
                    <?php if (strpos(mime_content_type($mediaPath), 'image/') === 0): ?>
                        <img src="<?= $mediaPath ?>" style="max-width: 200px; max-height: 200px;">
                    <?php elseif (strpos(mime_content_type($mediaPath), 'video/') === 0): ?>
                        <video controls style="max-width: 200px; max-height: 200px;">
                            <source src="<?= $mediaPath ?>" type="<?= mime_content_type($mediaPath) ?>">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div data-role="fieldcontain">
            <button type="button" onclick="removeFiles()" class="link-button">Remove Media</button>
        </div>
        <br>
        <div data-role="fieldcontain">
            <input type="submit" value="Update Post" class="link-button">
            <button type="submit" name="discard" value="discard" class="link-button">Discard Changes</button>
        </div>
    </form>
    <div class="center"><?= $error ?></div>
</div>

<script>
    function previewFiles() {
        const files = document.getElementById('media').files;
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        for (const file of files) {
            const reader = new FileReader();
            reader.onload = function (event) {
                let preview;
                if (file.type.startsWith('image/')) {
                    preview = document.createElement('img');
                } else if (file.type.startsWith('video/')) {
                    preview = document.createElement('video');
                    preview.controls = true;
                }
                preview.src = event.target.result;
                preview.style.maxWidth = '200px';
                preview.style.maxHeight = '200px';
                previewContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    }

    function removeFiles() {
        document.getElementById('media').value = '';
        document.getElementById('preview-container').innerHTML = '';
    }

    function confirmDiscard(event) {
        if (event.submitter.name === 'discard') {
            return confirm('Are you sure you want to discard your changes?');
        }
        return true;
    }

    function fetchWorkouts(splitId) {
        if (!splitId) {
            document.getElementById('workout-container').innerHTML = '';
            return;
        }

        fetch('fetch_workouts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                split_id: splitId
            })
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('workout-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching workouts:', error);
        });
    }
</script>
