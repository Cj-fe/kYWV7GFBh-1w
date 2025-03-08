

<?php
include 'includes/timezone.php';
require_once 'includes/config.php';
require_once 'includes/firebaseRDB.php';

date_default_timezone_set('Asia/Manila');

$firebase = new firebaseRDB($databaseURL);

// Fetch data from Firebase
$alumniData = $firebase->retrieve("alumni");
$newsData = $firebase->retrieve("news");
$eventData = $firebase->retrieve("event");
$jobData = $firebase->retrieve("job");
$newsCommentsData = $firebase->retrieve("news_comments");
$eventCommentsData = $firebase->retrieve("event_comments");
$jobCommentsData = $firebase->retrieve("job_comments");

// Decode JSON data into associative arrays
$alumni = json_decode($alumniData, true) ?: [];
$news = json_decode($newsData, true) ?: [];
$events = json_decode($eventData, true) ?: [];
$jobs = json_decode($jobData, true) ?: [];
$newsComments = json_decode($newsCommentsData, true) ?: [];
$eventComments = json_decode($eventCommentsData, true) ?: [];
$jobComments = json_decode($jobCommentsData, true) ?: [];

// Helper function to get the alumni name
function getAlumniName($alumni_id, $alumni)
{
    if (isset($alumni[$alumni_id])) {
        return $alumni[$alumni_id]['firstname'] . ' ' . $alumni[$alumni_id]['lastname'];
    }
    return "Unknown";
}

// Helper function to get the item title (news, event, or job)
function getItemTitle($item_id, $items)
{
    if (isset($items[$item_id])) {
        return isset($items[$item_id]['news_title']) ? $items[$item_id]['news_title'] :
            (isset($items[$item_id]['event_title']) ? $items[$item_id]['event_title'] :
                (isset($items[$item_id]['job_title']) ? $items[$item_id]['job_title'] : "Unknown"));
    }
    return "Unknown";
}

// Helper function to calculate time elapsed since the activity occurred
function timeElapsed($timestamp) {
    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes == 1 ? "1 minute ago" : "$minutes minutes ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours == 1 ? "1 hour ago" : "$hours hours ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days == 1 ? "1 day ago" : "$days days ago";
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks == 1 ? "1 week ago" : "$weeks weeks ago";
    } else {
        return date("F j, Y", $timestamp);
    }
}

// Combine all comments and likes into a unified activity log
$all_activities = [];

// Process comments (news, event, job)
foreach ($newsComments as $id => $comment) {
    $all_activities[] = [
        'type' => 'news_comment',
        'alumni_id' => $comment['alumni_id'],
        'comment' => $comment['comment'],
        'item_id' => $comment['news_id'],
        'date' => strtotime($comment['date_commented'])
    ];
}
foreach ($eventComments as $id => $comment) {
    $all_activities[] = [
        'type' => 'event_comment',
        'alumni_id' => $comment['alumni_id'],
        'comment' => $comment['comment'],
        'item_id' => $comment['event_id'],
        'date' => strtotime($comment['date_commented'])
    ];
}
foreach ($jobComments as $id => $comment) {
    $all_activities[] = [
        'type' => 'job_comment',
        'alumni_id' => $comment['alumni_id'],
        'comment' => $comment['comment'],
        'item_id' => $comment['job_id'],
        'date' => strtotime($comment['date_commented'])
    ];
}

// Process likes (news, event, job)
foreach ($news as $id => $item) {
    if (isset($item['likes'])) {
        foreach ($item['likes'] as $alumni_id => $timestamp) {
            $all_activities[] = [
                'type' => 'news_like',
                'alumni_id' => $alumni_id,
                'item_id' => $id,
                'date' => strtotime($timestamp)
            ];
        }
    }
}
foreach ($events as $id => $item) {
    if (isset($item['likes'])) {
        foreach ($item['likes'] as $alumni_id => $timestamp) {
            $all_activities[] = [
                'type' => 'event_like',
                'alumni_id' => $alumni_id,
                'item_id' => $id,
                'date' => strtotime($timestamp)
            ];
        }
    }
}
foreach ($jobs as $id => $item) {
    if (isset($item['likes'])) {
        foreach ($item['likes'] as $alumni_id => $timestamp) {
            $all_activities[] = [
                'type' => 'job_like',
                'alumni_id' => $alumni_id,
                'item_id' => $id,
                'date' => strtotime($timestamp)
            ];
        }
    }
}

// Sort all activities by date (newest first)
usort($all_activities, function ($a, $b) {
    return $b['date'] - $a['date'];
});

// Get the last read timestamp (from session or GET parameter)
$last_read_timestamp = isset($_GET['last_read_timestamp']) ? intval($_GET['last_read_timestamp']) : 0;

$new_activity_count = 0;
$formatted_activities = [];

// Process all activities, count the new ones, and format them for output
foreach ($all_activities as $activity) {
    if ($activity['date'] > $last_read_timestamp) {
        $new_activity_count++;
    }
    
    $alumni_name = getAlumniName($activity['alumni_id'], $alumni);
    $item_title = getItemTitle($activity['item_id'], 
        strpos($activity['type'], 'news') !== false ? $news : 
        (strpos($activity['type'], 'event') !== false ? $events : $jobs));
    
    $action = strpos($activity['type'], 'comment') !== false ? 'commented on' : 'liked';
    $item_type = explode('_', $activity['type'])[0];
    
    $formatted_activities[] = [
        'alumni_name' => $alumni_name,
        'item_title' => $item_title,
        'action' => $action,
        'item_type' => $item_type,
        'comment' => isset($activity['comment']) ? htmlspecialchars($activity['comment']) : '',
        'profile_url' => $alumni[$activity['alumni_id']]['profile_url'] ?? 'img/default-avatar.jpg',
        'time_elapsed' => timeElapsed($activity['date']),
        'timestamp' => $activity['date']
    ];
}

// Set the content type to JSON
header('Content-Type: application/json');

// Return JSON with new activity count and formatted activities
echo json_encode([
    'activities' => $formatted_activities, // No limit to the number of notifications
    'new_activity_count' => $new_activity_count
]);
?>