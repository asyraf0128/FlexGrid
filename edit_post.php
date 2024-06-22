<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

$error = '';
$postId = '';
$title = '';
$description = '';
$split = '';
$visibility = '';
$mediaPaths = [];

// Fetch split groups for the logged-in user
$splitGroups = queryMysql("SELECT * FROM split_groups WHERE user='$user'");

if (isset($_GET['id'])) {
    $postId = sanitizeString($_GET['id']);
    $post = queryMysql("SELECT * FROM posts WHERE id='$postId' AND user='$user'");

    if ($post->num_rows == 0) {
        die("Post not found or you don't have permission to edit this post.</div></body></html>");
    }

    $post = $post->fetch_assoc();
    $title = htmlspecialchars($post['title']);
    $description = htmlspecialchars($post['description']);
    $split = $post['split'];
    $visibility = $post['visibility'];
    $mediaPaths = unserialize($post['media']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Discard action
    if (isset($_POST['discard'])) {
        header("Location: index.php");
        exit();
    }

    // Sanitize input values
    $splitId = sanitizeString($_POST['split']);
    $title = sanitizeString($_POST['title']);
    $description = sanitizeString($_POST['description']);
    $visibility = sanitizeString($_POST['visibility']);

    // Generate unique slug for the post
    $slug = generateSlug($title);
    $existingSlugs = queryMysql("SELECT slug FROM posts WHERE slug LIKE '$slug%' AND id != '$postId'");
    $slugCount = $existingSlugs->num_rows;
    if ($slugCount > 0) {
        $slug .= '-' . ($slugCount + 1);
    }

    // Validate title
    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        // Handle file uploads
        $uploadsDir = 'uploads/';
        $mediaUploaded = false;

        foreach ($_FILES['media']['name'] as $key => $name) {
            if (!empty($name)) {
                $mediaPath = $uploadsDir . basename($name);
                $tmpName = $_FILES['media']['tmp_name'][$key];

                if (file_exists($tmpName) && is_uploaded_file($tmpName)) {
                    $fileType = mime_content_type($tmpName);

                    // Check file type (image or video)
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

        // If no errors, proceed to update the database
        if (empty($error)) {
            $is_workout = !empty($split) ? 1 : 0;
            $mediaSerialized = $mediaUploaded ? "'" . serialize($mediaPaths) . "'" : 'NULL';

            // Update post in database
            $result = queryMysql("UPDATE posts SET title='$title', slug='$slug', description='$description', split='$splitId', media=$mediaSerialized, visibility='$visibility', is_workout='$is_workout' WHERE id='$postId' AND user='$user'");

            if ($result) {
                header("Location: index.php");
                exit();
            } else {
                $error = 'Failed to update post. Please try again.';
            }
        }
    }
}
?>

<div>
    <h3>Edit Post</h3>
    <form method="post" action="edit_post.php?id=<?= $postId ?>" enctype="multipart/form-data" onsubmit="return confirmDiscard(event)">
        <div data-role="fieldcontain">
            <label for="title">Post Title:</label>
            <input type="text" id="title" name="title" value="<?= $title ?>" required>
        </div>
        <div data-role="fieldcontain">
            <label for="description">Post Description:</label>
            <textarea id="description" name="description"><?= $description ?></textarea>
        </div>
        <div data-role="fieldcontain">
            <label for="split">Select Split:</label>
            <br>
            <select id="split" name="split">
                <option value="">Select...</option>
                <?php
                if ($splitGroups->num_rows > 0) {
                    while ($group = $splitGroups->fetch_assoc()) {
                        $groupId = $group['id'];
                        $groupName = htmlspecialchars($group['name']);
                        $selected = $groupId == $split ? 'selected' : '';
                        echo "<option value='$groupId' $selected>$groupName</option>";
                    }
                }
                ?>
            </select>
        </div>
        <br>
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
            <!-- Existing Thumbnails will be shown here -->
            <?php
            if (!empty($mediaPaths)) {
                foreach ($mediaPaths as $mediaPath) {
                    if (strpos(mime_content_type($mediaPath), 'image/') === 0) {
                        echo "<img src='$mediaPath' style='max-width: 100px; max-height: 100px;'>";
                    } else if (strpos(mime_content_type($mediaPath), 'video/') === 0) {
                        echo "<video src='$mediaPath' style='max-width: 100px; max-height: 100px;' controls></video>";
                    }
                }
            }
            ?>
        </div>
        <div data-role="fieldcontain">
            <button type="button" class="link-button" onclick="removeFiles()">Remove Media</button>
        </div>
        <br>
        <div data-role="fieldcontain">
            <input type="submit" class="link-button" value="Update Post">
            <button type="submit" class="link-button" name="discard" value="discard">Discard Changes</button>
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
            return confirm('Are you sure you want to discard the changes?');
        }
        return true;
    }
</script>
</body>
</html>
