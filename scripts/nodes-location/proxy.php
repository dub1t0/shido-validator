<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Adjust CORS as needed

// Get the API URL from the query parameter
$apiUrl = isset($_GET['apiUrl']) ? urldecode($_GET['apiUrl']) : null;

if ($apiUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    } else {
        echo $response;
    }
    curl_close($ch);
} else {
    echo json_encode(['error' => 'No API URL provided']);
}
