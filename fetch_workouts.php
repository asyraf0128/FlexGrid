<?php
// fetch_workouts.php
require_once 'functions.php';

echo "<link rel='stylesheet' href='styles.css'>";

if (isset($_POST['split_id'])) {
    $splitId = sanitizeString($_POST['split_id']);
    $workouts = []; // Fetch workouts from database based on $splitId

    // Example fetch query (replace with your actual database query)
    $result = queryMysql("SELECT * FROM workouts WHERE split_id='$splitId'");
    while ($row = $result->fetch_assoc()) {
        // Assuming $row contains necessary workout details
        $workouts[] = $row;
    }

    // Generate HTML for workouts
    ob_start(); // Start output buffering
    foreach ($workouts as $workout) {
        echo "<div>";
        echo "<ul>";
        echo "<li>";
        echo "<label>{$workout['name']}</label>";
        echo "<input type='number' name='workouts[{$workout['id']}][weight]' placeholder='Weight (kg)' value='{$workout['last_weight']}'>";
        echo "<input type='number' name='workouts[{$workout['id']}][sets]' placeholder='Sets' value='{$workout['last_sets']}'>";
        echo "<input type='number' name='workouts[{$workout['id']}][reps]' placeholder='Reps' value='{$workout['last_reps']}'>";
        echo "</li>";
        echo "</ul>";
        echo "</div>";
    }
    $html = ob_get_clean(); // Get buffered output and clear buffer

    echo $html;
    exit();
}
?>
