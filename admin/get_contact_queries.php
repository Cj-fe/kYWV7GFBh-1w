<?php
include 'includes/timezone.php';
require_once 'includes/config.php';
require_once 'includes/firebaseRDB.php';

date_default_timezone_set('Asia/Manila');

$firebase = new firebaseRDB($databaseURL);

// Fetch contact query data from Firebase
$contactQueryData = $firebase->retrieve("contact_query");

// Decode JSON data into associative array
$contactQueries = json_decode($contactQueryData, true) ?: [];

// Helper function to calculate time elapsed
function timeElapsed($timestamp) {
    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes == 1 ? "1 min" : "$minutes mins";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours == 1 ? "1 hour" : "$hours hours";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days == 1 ? "Yesterday" : "$days days";
    } else {
        return date("M j", $timestamp);
    }
}

// Process and format contact queries
$formatted_queries = [];
foreach ($contactQueries as $id => $query) {
    $timestamp = strtotime($query['date']);
    $formatted_queries[] = [
        'id' => $id,
        'name' => $query['name'],
        'email' => $query['email'],
        'subject' => $query['subject'],
        'message' => $query['message'],
        'time_elapsed' => timeElapsed($timestamp),
        'timestamp' => $timestamp
    ];
}

// Sort queries by timestamp (newest first)
usort($formatted_queries, function ($a, $b) {
    return $b['timestamp'] - $a['timestamp'];
});

// Limit to the 5 most recent queries
$formatted_queries = array_slice($formatted_queries, 0, 5);

// Set the content type to JSON
header('Content-Type: application/json');

// Return JSON with formatted queries and total count
echo json_encode([
    'queries' => $formatted_queries,
    'total_count' => count($contactQueries)
]);
?>