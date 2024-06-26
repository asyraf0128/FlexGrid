<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<div class='center'>";

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_default'])) {
    $groupId = sanitizeString($_POST['group_id']);
    queryMysql("UPDATE split_groups SET is_default = FALSE WHERE user = '$user'");
    queryMysql("UPDATE split_groups SET is_default = TRUE WHERE id = '$groupId' AND user = '$user'");
    // Redirect to avoid form resubmission
    header("Location: splits.php");
    exit();
}

// Fetch user's split groups
$splitGroups = queryMysql("SELECT * FROM split_groups WHERE user='$user'");

echo "<h3>Your Split Groups</h3>";

echo "<form method='get' action='edit_splits.php' style='margin-top: 10px;'>
<button type='submit' class='link-button'>Edit</button>
</form>";

if ($splitGroups->num_rows > 0) {
    echo "<h4>Select Default Split Group</h4>";
    while ($group = $splitGroups->fetch_assoc()) {
        $groupId = $group['id'];
        $groupName = htmlspecialchars($group['name']);
        $isDefault = $group['is_default'] ? " (Default)" : "";

        echo "<div class='split-group'>
            <h5>$groupName $isDefault</h5>
            <form method='post' action='splits.php' class='set-default-form' data-group-name='$groupName'>
                <input type='hidden' name='group_id' value='$groupId'>
                <button type='submit' name='set_default' class='link-button'>Set as Default</button>
            </form>";

        $splits = queryMysql("SELECT * FROM splits WHERE group_id='$groupId'");
        if ($splits->num_rows > 0) {
            echo "<ul>";
            while ($split = $splits->fetch_assoc()) {
                $splitId = $split['id'];
                $splitName = htmlspecialchars($split['name']);

                echo "<li>
                    $splitName
                    <ul>";
                $workouts = queryMysql("SELECT * FROM workouts WHERE split_id='$splitId'");
                if ($workouts->num_rows > 0) {
                    while ($workout = $workouts->fetch_assoc()) {
                        $workoutName = htmlspecialchars($workout['name']);
                        echo "<li>$workoutName</li>";
                    }
                }
                echo "</ul>
                </li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }
} else {
    echo "<p>No split groups found. Create one in the edit page.</p>";
}

echo "</div></body></html>";
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.set-default-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const groupName = this.getAttribute('data-group-name');
            const confirmSetDefault = confirm(`Are you sure you want to set ${groupName} as default?`);
            if (!confirmSetDefault) {
                e.preventDefault();
            }
        });
    });
});
</script>
