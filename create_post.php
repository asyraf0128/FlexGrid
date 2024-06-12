<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

$error = '';
$title = '';
$description = '';
$split = '';
$visibility = 'public';
$mediaPaths = [];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['discard'])) {
        header("Location: index.php");
        exit();
    }

    $title = sanitizeString($_POST['title']);
    $description = sanitizeString($_POST['description']);
    $split = sanitizeString($_POST['split']);
    $visibility = sanitizeString($_POST['visibility']);


    // Generate slug
    $slug = generateSlug($title);
    
    // Ensure unique slug
    $existingSlugs = queryMysql("SELECT slug FROM posts WHERE slug LIKE '$slug%'");
    $slugCount = $existingSlugs->num_rows;
    if ($slugCount > 0) {
        $slug .= '-' . ($slugCount + 1);
    }

    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        $uploadsDir = 'uploads/';
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
            $is_workout = !empty($split) ? 1 : 0;
            $mediaSerialized = $mediaUploaded ? "'" . serialize($mediaPaths) . "'" : 'NULL';

            $result = queryMysql("INSERT INTO posts (user, title, slug, description, split, media, visibility, is_workout) VALUES ('$user', '$title', '$slug', '$description', '$split', $mediaSerialized, '$visibility', $is_workout)");

            if ($result) {
                header("Location: index.php");
                exit();
            } else {
                $error = 'Failed to create post. Please try again.';
            }
        }
    }
}

echo <<<_END
<div class="center">
    <h3>Create a Post</h3>
    <form method='post' action='create_post.php' enctype='multipart/form-data' onsubmit='return confirmDiscard(event)'>
        <div data-role='fieldcontain'>
            <label for='title'>Post Title:</label>
            <input type='text' id='title' name='title' value='$title' required>
        </div>
        <div data-role='fieldcontain'>
            <label for='description'>Post Description:</label>
            <textarea id='description' name='description'>$description</textarea>
        </div>
        <div data-role='fieldcontain'>
            <label for='split'>Select Split:</label>
            <select id='split' name='split'>
                <option value=''>Select...</option>
                <option value='Split 1'>Split 1</option>
                <option value='Split 2'>Split 2</option>
                <option value='Split 3'>Split 3</option>
            </select>
        </div>
        <div data-role='fieldcontain'>
            <label for='visibility'>Post Visibility:</label>
            <select id='visibility' name='visibility'>
                <option value='public' <?= $visibility == 'public' ? 'selected' : '' ?>>Public</option>
                <option value='private' <?= $visibility == 'private' ? 'selected' : '' ?>>Private</option>
            </select>
        </div>
        <div data-role='fieldcontain'>
            <label for='media'>Upload Image/Video:</label>
            <input type='file' id='media' name='media[]' accept='image/*,video/*' multiple onchange="previewFiles()">
        </div>
        <div id="preview-container">
            <!-- Thumbnails will be shown here -->
        </div>
        <div data-role='fieldcontain'>
            <button type='button' onclick="removeFiles()">Remove Media</button>
        </div>
        <div data-role='fieldcontain'>
            <input type='submit' value='Create Post'>
            <button type='submit' name='discard' value='discard'>Discard Post</button>
        </div>
    </form>
    <div class='center'>$error</div>
</div>
<script>
    function previewFiles() {
        const files = document.getElementById('media').files;
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        for (const file of files) {
            const reader = new FileReader();
            reader.onload = function(event) {
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
            return confirm('Are you sure you want to discard this post?');
        }
        return true;
    }
</script>
</body>
</html>
_END;
?>
