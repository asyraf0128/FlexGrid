<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='center'>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_group'])) {
        $groupName = sanitizeString($_POST['group_name']);
        queryMysql("INSERT INTO split_groups (user, name) VALUES ('$user', '$groupName')");
    } elseif (isset($_POST['create_split'])) {
        $groupId = sanitizeString($_POST['group_id']);
        $splitName = sanitizeString($_POST['split_name']);
        queryMysql("INSERT INTO splits (group_id, name) VALUES ('$groupId', '$splitName')");
    } elseif (isset($_POST['create_workout'])) {
        $splitId = sanitizeString($_POST['split_id']);
        $workoutName = sanitizeString($_POST['workout_name']);
        queryMysql("INSERT INTO workouts (split_id, name) VALUES ('$splitId', '$workoutName')");
    } elseif (isset($_POST['set_default'])) {
        $groupId = sanitizeString($_POST['group_id']);
        queryMysql("UPDATE split_groups SET is_default = FALSE WHERE user = '$user'");
        queryMysql("UPDATE split_groups SET is_default = TRUE WHERE id = '$groupId' AND user = '$user'");
    }
}

// Fetch user's split groups
$splitGroups = queryMysql("SELECT * FROM split_groups WHERE user='$user'");

echo "<h3>Manage Splits</h3>";

echo "<h4>Create Split Group</h4>";
echo <<<_END
<form method='post' action='manage_splits.php'>
    <input type='text' name='group_name' placeholder='Group Name' required>
    <button type='submit' name='create_group'>Create Group</button>
</form>
_END;

if ($splitGroups->num_rows > 0) {
    echo "<h4>Your Split Groups</h4>";

    while ($group = $splitGroups->fetch_assoc()) {
        $groupId = $group['id'];
        $groupName = htmlspecialchars($group['name']);
        $isDefault = $group['is_default'] ? " (Default)" : "";

        echo "<div class='split-group'>
            <h5>$groupName $isDefault</h5>
            <form method='post' action='manage_splits.php'>
                <input type='hidden' name='group_id' value='$groupId'>
                <input type='text' name='split_name' placeholder='Split Name' required>
                <button type='submit' name='create_split'>Add Split</button>
                <button type='submit' name='set_default'>Set as Default</button>
            </form>";

        $splits = queryMysql("SELECT * FROM splits WHERE group_id='$groupId'");
        if ($splits->num_rows > 0) {
            echo "<ul>";
            while ($split = $splits->fetch_assoc()) {
                $splitId = $split['id'];
                $splitName = htmlspecialchars($split['name']);

                echo "<li>
                    $splitName
                    <form method='post' action='manage_splits.php'>
                        <input type='hidden' name='split_id' value='$splitId'>
                        <input type='text' name='workout_name' placeholder='Workout Name' required>
                        <button type='submit' name='create_workout'>Add Workout</button>
                    </form>";

                $workouts = queryMysql("SELECT * FROM workouts WHERE split_id='$splitId'");
                if ($workouts->num_rows > 0) {
                    echo "<ul>";
                    while ($workout = $workouts->fetch_assoc()) {
                        $workoutName = htmlspecialchars($workout['name']);
                        echo "<li>$workoutName</li>";
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
