<?php
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize user input
        $forumName = isset($_POST['forumName']) ? trim(strip_tags($_POST['forumName'])) : '';
        $forumDescription = isset($_POST['editor1']) ? trim(strip_tags($_POST['editor1'])) : '';

        if (empty($forumName) || empty($forumDescription)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Forum name and description are required'
            ]);
            exit;
        }

        $firebase = new firebaseRDB($databaseURL);

        // Set the timezone to Asia/Manila
        $date = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $createdAt = $date->format('Y-m-d H:i:s');

        $data = [
            'forumName' => htmlspecialchars($forumName, ENT_QUOTES, 'UTF-8'),
            'forumDescription' => htmlspecialchars($forumDescription, ENT_QUOTES, 'UTF-8'),
            'alumniId' => $_SESSION['user']['id'],
            'createdAt' => $createdAt
        ];

        $result = $firebase->insert("forum", $data);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Forum post created successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create forum post'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}