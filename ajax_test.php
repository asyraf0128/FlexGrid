<?php
header('Content-Type: application/json');

$response = [
    'success' => true,
    'message' => 'Hello from the server!'
];

echo json_encode($response);
?>
