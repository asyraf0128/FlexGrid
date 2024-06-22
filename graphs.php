<?php
// graphs.php

require_once 'header.php';
?>

<h2>Workout Frequency</h2>
<div id="frequencyChart" style="width: 80%; height: 400px; margin: 0 auto;"></div>

<h2>Workout Intensity</h2>
<div id="intensityChart" style="width: 80%; height: 400px; margin: 0 auto;"></div>

<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch data for workout frequency
    <?php
    $frequencyData = fetchWorkoutFrequencyData();
    $frequencyLabels = json_encode($frequencyData['labels']);
    $frequencyData = json_encode($frequencyData['data']);
    ?>

    // Configure frequency chart
    var frequencyData = [{
        x: <?php echo $frequencyLabels; ?>,
        y: <?php echo $frequencyData; ?>,
        type: 'scatter',
        mode: 'lines+markers',
        name: 'Workout Frequency'
    }];

    var frequencyLayout = {
        title: 'Workout Frequency',
        xaxis: {
            title: 'Date'
        },
        yaxis: {
            title: 'Number of Workouts'
        }
    };

    Plotly.newPlot('frequencyChart', frequencyData, frequencyLayout);

    // Fetch data for workout intensity
    <?php
    $intensityData = fetchWorkoutIntensityData();
    $intensityLabels = json_encode($intensityData['labels']);
    $intensityData = json_encode($intensityData['data']);
    ?>

    // Configure intensity chart
    var intensityData = [{
        x: <?php echo $intensityLabels; ?>,
        y: <?php echo $intensityData; ?>,
        type: 'scatter',
        mode: 'lines+markers',
        name: 'Workout Intensity'
    }];

    var intensityLayout = {
        title: 'Workout Intensity',
        xaxis: {
            title: 'Date'
        },
        yaxis: {
            title: 'Average Weight (kg)'
        }
    };

    Plotly.newPlot('intensityChart', intensityData, intensityLayout);
});
</script>

<?php
require_once 'footer.php';
?>
