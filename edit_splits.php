<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    // Handling POST requests for various actions (create, delete, rename)
    // Code omitted for brevity, refer to your existing PHP script
}

// HTML form for creating a new split group
echo "<h3>Manage Splits</h3>";

echo "<h4>Create Split Group</h4>";
echo <<<_END
<form method='post' action='edit_splits.php' class='white-background-form'>
    <input type='text' name='group_name' placeholder='Group Name' required>
    <button type='submit' name='create_group' class='link-button-edit'>Create Group</button>
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
