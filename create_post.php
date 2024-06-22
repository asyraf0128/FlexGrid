<?php
require_once 'header.php';

// Check if user is logged in, otherwise redirect or display appropriate message
if (!$loggedin) {
    die("Please log in to create a post."); // Example error handling
}

$error = '';
$title = '';
$description = '';
$visibility = 'public';
$mediaPaths = [];
$user = $_SESSION['user'];

// Fetch default split group id for the user (it should always exist)
$defaultSplit = queryMysql("SELECT id FROM split_groups WHERE user='$user' AND is_default=TRUE");
$defaultSplitId = $defaultSplit->num_rows > 0 ? $defaultSplit->fetch_assoc()['id'] : null;

// Fetch splits for the default split group
$splits = queryMysql("SELECT * FROM splits WHERE group_id='$defaultSplitId'");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle actions like discarding post
    if (isset($_POST['discard'])) {
        header("Location: index.php");
        exit();
    }

    // Sanitize input values
    $splitId = sanitizeString($_POST['split']);
    $title = sanitizeString($_POST['title']);
    $description = sanitizeString($_POST['description']);
    $visibility = sanitizeString($_POST['visibility']);

    // Handle file uploads and validate them
    $uploadsDir = 'uploads/'; // Define your uploads directory
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

    // If there are no errors, proceed to insert into the database
    if (empty($error)) {
        $is_workout = !empty($splitId) ? 1 : 0;
        $mediaSerialized = $mediaUploaded ? "'" . serialize($mediaPaths) . "'" : 'NULL';

        // Generate unique slug for the post
        $slug = generateSlug($title);
        $existingSlugs = queryMysql("SELECT slug FROM posts WHERE slug LIKE '$slug%'");
        $slugCount = $existingSlugs->num_rows;
        if ($slugCount > 0) {
            $slug .= '-' . ($slugCount + 1);
        }

        // Insert post into database with the selected split_id
        $query = "INSERT INTO posts (user, title, slug, description, split_id, media, visibility, is_workout) 
                  VALUES ('$user', '$title', '$slug', '$description', '$splitId', $mediaSerialized, '$visibility', $is_workout)";

        if (queryMysql($query)) {
            $postId = $connection->insert_id; // Assuming $connection is your MySQLi connection object
            echo "Debug: Post ID is " . $postId; // Debugging statement

            if (!empty($_POST['workouts'])) {
                foreach ($_POST['workouts'] as $workoutId => $details) {
                    $weight = sanitizeString($details['weight']) ?: 'NULL';
                    $sets = sanitizeString($details['sets']) ?: 'NULL';
                    $reps = sanitizeString($details['reps']) ?: 'NULL';

                    // Insert workout details into workout_details table
                    queryMysql("INSERT INTO workout_details (post_id, workout_id, weight, sets, reps) 
                                VALUES ('$postId', '$workoutId', $weight, $sets, $reps)");

                    // Update workouts table with last_weight, last_sets, last_reps
                    queryMysql("UPDATE workouts SET last_weight=$weight, last_sets=$sets, last_reps=$reps WHERE id='$workoutId'");
                }
            }
            header("Location: index.php");
            exit();
        } else {
            $error = 'Failed to create post. Please try again.';
        }
    }
}
?>

<div class="center">
    <h3>Create a Post</h3>
</div>

<div class="post-background-form">
    <form method="post" action="create_post.php" enctype="multipart/form-data" onsubmit="return confirmDiscard(event)">
        <div data-role="fieldcontain">
            <label for="title">Post Title:</label>
            <br>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div data-role="fieldcontain">
            <label for="description">Post Description:</label>
            <br>
            <textarea id="description" name="description"  placeholder="Enter your post description..."><?= htmlspecialchars($description) ?></textarea>
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
                        $selected = $splitId == $defaultSplitId ? 'selected' : '';
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
        <div id="preview-container"></div>
        <div data-role="fieldcontain">
            <button type="button" onclick="removeFiles()" class="link-button">Remove Media</button>
        </div>
        <br>
        <div data-role="fieldcontain">
            <input type="submit" value="Create Post" class="link-button">
            <button type="submit" name="discard" value="discard" class="link-button">Discard Post</button>
        </div>
    </form>
    <div class="center"><?= $error ?></div> <!-- Display any errors here -->
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
            return confirm('Are you sure you want to discard this post?');
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
        .catch(error => console.error('Error fetching workouts:', error));
    }
</script>

</body>
</html>
