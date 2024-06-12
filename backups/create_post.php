<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

$error = '';
$title = '';
$description = '';
$split = '';
$visibility = 'public';
$imagePath = '';
$videoPath = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['discard'])) {
        header("Location: index.php");
        exit();
    }

    $title = sanitizeString($_POST['title']);
    $description = sanitizeString($_POST['description']);
    $split = sanitizeString($_POST['split']);
    $visibility = sanitizeString($_POST['visibility']);

    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        $uploadsDir = 'uploads/';
        $imageUploaded = false;
        $videoUploaded = false;

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $uploadsDir . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $imageUploaded = true;
            } else {
                $error = 'Failed to upload image.';
            }
        }

        if (!empty($_FILES['video']['name'])) {
            $videoPath = $uploadsDir . basename($_FILES['video']['name']);
            if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
                $videoUploaded = true;
            } else {
                $error = 'Failed to upload video.';
            }
        }

        if (empty($error)) {
            $is_workout = !empty($split) ? 1 : 0;
            $imagePath = $imageUploaded ? "'$imagePath'" : 'NULL';
            $videoPath = $videoUploaded ? "'$videoPath'" : 'NULL';

            $result = queryMysql("INSERT INTO posts (user, title, description, split, image, video, visibility, is_workout) VALUES ('$user', '$title', '$description', '$split', $imagePath, $videoPath, '$visibility', $is_workout)");

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
            <label for='image'>Upload Image:</label>
            <input type='file' id='image' name='image' onchange="previewFile('image')">
        </div>
        <div data-role='fieldcontain'>
            <label for='video'>Upload Video:</label>
            <input type='file' id='video' name='video' onchange="previewFile('video')">
        </div>
        <div id="preview-container">
            <!-- Thumbnails will be shown here -->
        </div>
        <div data-role='fieldcontain'>
            <button type='button' onclick="removeFile('image')">Remove Image</button>
            <button type='button' onclick="removeFile('video')">Remove Video</button>
        </div>
        <div data-role='fieldcontain'>
            <input data-transition='slide' type='submit' value='Create Post'>
            <button type='submit' name='discard' value='discard'>Discard Post</button>
        </div>
    </form>
    <div class='center'>$error</div>
</div>
<script>
    function previewFile(type) {
        const file = document.getElementById(type).files[0];
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.createElement(type === 'image' ? 'img' : 'video');
                preview.src = event.target.result;
                preview.width = 200;
                if (type === 'video') {
                    preview.controls = true;
                }
                previewContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    }

    function removeFile(type) {
        document.getElementById(type).value = '';
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
