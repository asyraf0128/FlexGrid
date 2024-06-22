<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='center'>";

$user = $_SESSION['user'];

// Variable to hold redirection URL after processing POST
$redirectUrl = 'edit_splits.php';

// Check if user has any split groups
$splitGroups = queryMysql("SELECT * FROM split_groups WHERE user='$user'");
$hasSplitGroups = $splitGroups->num_rows > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_group'])) {
        $groupName = sanitizeString($_POST['group_name']);
        queryMysql("INSERT INTO split_groups (user, name) VALUES ('$user', '$groupName')");

        // Check if this is the first split group for the user
        if (!$hasSplitGroups) {
            // Fetch the ID of the newly created split group
            $newGroupId = $connection->insert_id; // Assuming $connection is your MySQLi connection object

            // Update this group as the default for the user
            queryMysql("UPDATE split_groups SET is_default = TRUE WHERE id = '$newGroupId'");
        }

        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['create_split'])) {
        $groupId = sanitizeString($_POST['group_id']);
        $splitName = sanitizeString($_POST['split_name']);
        queryMysql("INSERT INTO splits (group_id, name) VALUES ('$groupId', '$splitName')");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['create_workout'])) {
        $splitId = sanitizeString($_POST['split_id']);
        $workoutName = sanitizeString($_POST['workout_name']);
        queryMysql("INSERT INTO workouts (split_id, name) VALUES ('$splitId', '$workoutName')");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['rename_group'])) {
        $groupId = sanitizeString($_POST['group_id']);
        $newName = sanitizeString($_POST['new_name']);
        queryMysql("UPDATE split_groups SET name='$newName' WHERE id='$groupId' AND user='$user'");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['rename_split'])) {
        $splitId = sanitizeString($_POST['split_id']);
        $newName = sanitizeString($_POST['new_name']);
        queryMysql("UPDATE splits SET name='$newName' WHERE id='$splitId'");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['rename_workout'])) {
        $workoutId = sanitizeString($_POST['workout_id']);
        $newName = sanitizeString($_POST['new_name']);
        queryMysql("UPDATE workouts SET name='$newName' WHERE id='$workoutId'");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['delete_group'])) {
        $groupId = sanitizeString($_POST['group_id']);
        queryMysql("DELETE FROM split_groups WHERE id='$groupId' AND user='$user'");
        queryMysql("DELETE FROM splits WHERE group_id='$groupId'");
        queryMysql("DELETE FROM workouts WHERE split_id IN (SELECT id FROM splits WHERE group_id='$groupId')");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['delete_split'])) {
        $splitId = sanitizeString($_POST['split_id']);
        queryMysql("DELETE FROM splits WHERE id='$splitId'");
        queryMysql("DELETE FROM workouts WHERE split_id='$splitId'");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    } elseif (isset($_POST['delete_workout'])) {
        $workoutId = sanitizeString($_POST['workout_id']);
        queryMysql("DELETE FROM workouts WHERE id='$workoutId'");
        // Redirect after POST to prevent duplicate form submission
        header("Location: $redirectUrl");
        exit;
    }
}

// Fetch user's split groups
$splitGroups = queryMysql("SELECT * FROM split_groups WHERE user='$user'");

echo "<h3>Manage Splits</h3>";

echo "<h4>Create Split Group</h4>";
echo <<<_END
<form method='post' action='edit_splits.php'>
    <input type='text' name='group_name' placeholder='Group Name' required>
    <button type='submit' name='create_group'>Create Group</button>
</form>
_END;

if ($splitGroups->num_rows > 0) {
    // Display existing split groups and their associated splits and workouts
    echo "<h4>Your Split Groups</h4>";

    while ($group = $splitGroups->fetch_assoc()) {
        $groupId = $group['id'];
        $groupName = htmlspecialchars($group['name']);

        echo "<div class='split-group'>
            <h5>$groupName</h5>
            <form method='post' action='edit_splits.php' style='display:inline;'>
                <input type='hidden' name='group_id' value='$groupId'>
                <button type='submit' name='delete_group' class='link-button'>Delete Group</button>
            </form>
            <br>
            <form method='post' action='edit_splits.php' class='white-background-form'>
                <input type='hidden' name='group_id' value='$groupId'>
                <input type='text' name='new_name' placeholder='New Group Name' required>
                <button type='submit' name='rename_group' class='link-button-edit'>Rename</button>
            </form>
            <form method='post' action='edit_splits.php' class='white-background-form'>
                <input type='hidden' name='group_id' value='$groupId'>
                <input type='text' name='split_name' placeholder='Split Name' required>
                <button type='submit' name='create_split' class='link-button-edit'>Add Split</button>
            </form>";

        // Fetch and display splits for the current group
        $splits = queryMysql("SELECT * FROM splits WHERE group_id='$groupId'");
        if ($splits->num_rows > 0) {
            echo "<ul>";
            while ($split = $splits->fetch_assoc()) {
                $splitId = $split['id'];
                $splitName = htmlspecialchars($split['name']);

                echo "<li>
                    $splitName
                    <form method='post' action='edit_splits.php' style='display:inline;'>
                        <input type='hidden' name='split_id' value='$splitId'>
                        <button type='submit' name='delete_split' class='link-button'>Delete Split</button>
                    </form>
                    <br>
                    <form method='post' action='edit_splits.php' class='white-background-form'>
                        <input type='hidden' name='split_id' value='$splitId'>
                        <input type='text' name='new_name' placeholder='New Split Name' required>
                        <button type='submit' name='rename_split' class='link-button-edit'>Rename</button>
                    </form>
                    <form method='post' action='edit_splits.php' class='white-background-form'>
                        <input type='hidden' name='split_id' value='$splitId'>
                        <input type='text' name='workout_name' placeholder='Workout Name' required>
                        <button type='submit' name='create_workout' class='link-button-edit'>Add Workout</button>
                    </form>";

                // Fetch and display workouts for the current split
                $workouts = queryMysql("SELECT * FROM workouts WHERE split_id='$splitId'");
                if ($workouts->num_rows > 0) {
                    echo "<ul>";
                    while ($workout = $workouts->fetch_assoc()) {
                        $workoutId = $workout['id'];
                        $workoutName = htmlspecialchars($workout['name']);
                        echo "<li>
                            $workoutName
                            <form method='post' action='edit_splits.php' style='display:inline;'>
                                <input type='hidden' name='workout_id' value='$workoutId'>
                                <button type='submit' name='delete_workout' class='link-button'>Delete Workout</button>
                            </form>
                            <br>
                            <form method='post' action='edit_splits.php' class='white-background-form'>
                                <input type='hidden' name='workout_id' value='$workoutId'>
                                <input type='text' name='new_name' placeholder='New Workout Name' required>
                                <button type='submit' name='rename_workout' class='link-button-edit'>Rename</button>
                            </form>
                            </li>";
                    }
                    echo "</ul>";
                }

                echo "</li>";
            }
            echo "</ul>";
        }

        echo "</div>";
    }
}

echo "</div></body></html>";
?>

<script>
// JavaScript to maintain scroll position after form submission
window.onload = function() {
    // Get the current scroll position and store it
    var scrollPosition = sessionStorage.getItem('scrollPosition');
    
    // Restore scroll position if it exists
    if (scrollPosition) {
        document.documentElement.scrollTop = scrollPosition;
        sessionStorage.removeItem('scrollPosition'); // Remove stored position after restoring
    }

    // Store scroll position when submitting a form
    document.addEventListener('submit', function(event) {
        var form = event.target;
        if (form.tagName.toLowerCase() === 'form') {
            sessionStorage.setItem('scrollPosition', document.documentElement.scrollTop);
        }
    });
};
</script>