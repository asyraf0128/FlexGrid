<?php
require_once 'header.php';

echo <<<_END
<head>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <title>FlexGrid: Workout Graphs</title>
</head>
<body>
    <div data-role='page'>
        <div data-role='header'>
            <div id='logo' class='center'>Flex<img id='flex' src='flexgridicon.jpg'>Grid</div>
            <div class='username'>Logged in as: <?php echo $userstr; ?></div>
        </div>
        <div data-role='content' class='center'>
            <h3>Your Workout Graphs</h3>
            <canvas id="frequencyChart"></canvas>
            <canvas id="improvementChart"></canvas>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.getJSON('fetch_workout_data.php', function(data) {
                // Process data for frequency chart
                const workoutDates = data.map(item => item.date);
                const uniqueDates = [...new Set(workoutDates)];
                const frequencyData = uniqueDates.map(date => {
                    return workoutDates.filter(d => d === date).length;
                });

                // Process data for improvement chart
                const workoutNames = [...new Set(data.map(item => item.workout_name))];
                const improvementData = workoutNames.map(name => {
                    return {
                        label: name,
                        data: data.filter(item => item.workout_name === name).map(item => item.performance),
                        fill: false,
                        borderColor: getRandomColor()
                    };
                });

                // Render frequency chart
                const ctx1 = document.getElementById('frequencyChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: uniqueDates,
                        datasets: [{
                            label: 'Workout Frequency',
                            data: frequencyData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'day'
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Render improvement chart
                const ctx2 = document.getElementById('improvementChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: uniqueDates,
                        datasets: improvementData
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'day'
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                function getRandomColor() {
                    const letters = '0123456789ABCDEF';
                    let color = '#';
                    for (let i = 0; i < 6; i++) {
                        color += letters[Math.floor(Math.random() * 16)];
                    }
                    return color;
                }
            });
        });
    </script>
</body>
_END;

?>

