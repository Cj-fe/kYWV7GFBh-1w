<!----navbar_php_script.php------------>

<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

$alumni_data = $firebase->retrieve("alumni");
$alumni_data = json_decode($alumni_data, true);

$messages = $firebase->retrieve("messages");
$messages = json_decode($messages, true);

$all_alumni = $firebase->retrieve("alumni");
$all_alumni = json_decode($all_alumni, true);


// Extract alumni names and IDs
$alumni_info = [];
foreach ($alumni_data as $id => $alumni) {
    if (isset($alumni['status']) && $alumni['status'] === 'verified') {
        $alumni_info[] = [
            'id' => $id,
            'name' => $alumni['firstname'] . ' ' . $alumni['lastname'],
            'profile_url' => isset($alumni['profile_url']) ? $alumni['profile_url'] : '../images/profile.jpg',
            'status' => $alumni['status']
        ];
    }
}

// Convert alumni info to JSON for JavaScript use
$alumni_info_json = json_encode($alumni_info);

// Fetch notifications
$current_user_id = $_SESSION['user']['id']; // Assuming you have the user's ID in the session
$notifications = getNotifications($firebase, $current_user_id);
$notification_count = count($notifications);

function getNotifications($firebase, $current_user_id)
{
    $notifications = [];



    // Fetch news comments
    $news_comments = $firebase->retrieve("news_comments");
    $news_comments = json_decode($news_comments, true);

    // Fetch event comments
    $event_comments = $firebase->retrieve("event_comments");
    $event_comments = json_decode($event_comments, true);

    // Fetch job comments
    $job_comments = $firebase->retrieve("job_comments");
    $job_comments = json_decode($job_comments, true);

    // Fetch forum posts
    $forum_posts = $firebase->retrieve("forum");
    $forum_posts = json_decode($forum_posts, true);

    // Fetch forum comments
    $forum_comments = $firebase->retrieve("forum_comments");
    $forum_comments = json_decode($forum_comments, true);

    // Fetch jobs
    $jobs = $firebase->retrieve("job");
    $jobs = json_decode($jobs, true);

    // Fetch events
    $events = $firebase->retrieve("event");
    $events = json_decode($events, true);

    // Fetch news
    $news = $firebase->retrieve("news");
    $news = json_decode($news, true);

    // Fetch gallery
    $gallery = $firebase->retrieve("gallery");
    $gallery = json_decode($gallery, true);

    // Process news comments
    processComments($firebase, $current_user_id, $news_comments, $notifications, 'news');

    // Process event comments
    processComments($firebase, $current_user_id, $event_comments, $notifications, 'event');

    // Process job comments
    processComments($firebase, $current_user_id, $job_comments, $notifications, 'job');

    // Process forum posts and comments
    processForumNotifications($firebase, $current_user_id, $forum_posts, $forum_comments, $notifications);

    processAdminContent($jobs, $notifications, 'job');

    // Process event notifications
    processAdminContent($events, $notifications, 'event', $firebase, $current_user_id);

    // Process news notifications
    processAdminContent($news, $notifications, 'news');

    // Process gallery notifications
    processAdminContent($gallery, $notifications, 'gallery');


    // Sort notifications by date, most recent first
    usort($notifications, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    return $notifications;
}

function processComments($firebase, $current_user_id, $comments, &$notifications, $type)
{
    if (!is_array($comments)) {
        return; // Exit the function if $comments is not an array
    }

    foreach ($comments as $comment_id => $comment) {
        // Notifications for replies
        if (isset($comment['alumni_id']) && $comment['alumni_id'] == $current_user_id && isset($comment['replies']) && is_array($comment['replies'])) {
            foreach ($comment['replies'] as $reply_id => $reply) {
                if (isset($reply['alumni_id']) && $reply['alumni_id'] != $current_user_id) {
                    $replier_data = $firebase->retrieve("alumni/" . $reply['alumni_id']);
                    $replier_data = json_decode($replier_data, true);
                    if ($replier_data) {
                        $replier_name = $replier_data['firstname'] . ' ' . $replier_data['lastname'];
                        $replier_profile = isset($replier_data['profile_url']) ? $replier_data['profile_url'] : '../images/profile.jpg';

                        $notifications[] = [
                            'type' => 'reply',
                            'content_type' => $type,
                            'replier_name' => $replier_name,
                            'replier_profile' => $replier_profile,
                            'date' => $reply['date_replied'],
                            'comment_id' => $comment_id,
                            $type . '_id' => $comment[$type . '_id']
                        ];
                    }
                }
            }
        }

        // Notifications for reactions
        if (isset($comment['alumni_id']) && $comment['alumni_id'] == $current_user_id && isset($comment['liked_by']) && is_array($comment['liked_by'])) {
            foreach ($comment['liked_by'] as $reactor_id) {
                if ($reactor_id != $current_user_id) {
                    $reactor_data = $firebase->retrieve("alumni/" . $reactor_id);
                    $reactor_data = json_decode($reactor_data, true);
                    if ($reactor_data) {
                        $reactor_name = $reactor_data['firstname'] . ' ' . $reactor_data['lastname'];
                        $reactor_profile = isset($reactor_data['profile_url']) ? $reactor_data['profile_url'] : '../images/profile.jpg';

                        $notifications[] = [
                            'type' => 'reaction',
                            'content_type' => $type,
                            'reactor_name' => $reactor_name,
                            'reactor_profile' => $reactor_profile,
                            'date' => date('Y-m-d H:i:s'),
                            'comment_id' => $comment_id,
                            $type . '_id' => $comment[$type . '_id']
                        ];
                    }
                }
            }
        }
    }
}

function processForumNotifications($firebase, $current_user_id, $forum_posts, $forum_comments, &$notifications)
{
    // Process forum post reactions
    if (is_array($forum_posts)) {
        foreach ($forum_posts as $post_id => $post) {
            if (isset($post['alumniId']) && $post['alumniId'] == $current_user_id) {
                // Process likes
                if (isset($post['liked_by']) && is_array($post['liked_by'])) {
                    foreach ($post['liked_by'] as $reactor_id => $reaction_date) {
                        if ($reactor_id != $current_user_id) {
                            $reactor_data = $firebase->retrieve("alumni/" . $reactor_id);
                            $reactor_data = json_decode($reactor_data, true);
                            if ($reactor_data) {
                                $reactor_name = $reactor_data['firstname'] . ' ' . $reactor_data['lastname'];
                                $reactor_profile = isset($reactor_data['profile_url']) ? $reactor_data['profile_url'] : '../images/profile.jpg';
                                $notifications[] = [
                                    'type' => 'forum_post_reaction',
                                    'content_type' => 'forum',
                                    'reactor_name' => $reactor_name,
                                    'reactor_profile' => $reactor_profile,
                                    'date' => $reaction_date,
                                    'post_id' => $post_id,
                                    'reaction_type' => 'like'
                                ];
                                // Log notificatio
                                error_log("Notification added for forum post reaction: " . json_encode($notifications[count($notifications) - 1]));
                            }
                        }
                    }
                }
                // Process dislikes
                if (isset($post['dislike_by']) && is_array($post['dislike_by'])) {
                    foreach ($post['dislike_by'] as $reactor_id => $reaction_date) {
                        if ($reactor_id != $current_user_id) {
                            $reactor_data = $firebase->retrieve("alumni/" . $reactor_id);
                            $reactor_data = json_decode($reactor_data, true);
                            if ($reactor_data) {
                                $reactor_name = $reactor_data['firstname'] . ' ' . $reactor_data['lastname'];
                                $reactor_profile = isset($reactor_data['profile_url']) ? $reactor_data['profile_url'] : '../images/profile.jpg';
                                $notifications[] = [
                                    'type' => 'forum_post_reaction',
                                    'content_type' => 'forum',
                                    'reactor_name' => $reactor_name,
                                    'reactor_profile' => $reactor_profile,
                                    'date' => $reaction_date,
                                    'post_id' => $post_id,
                                    'reaction_type' => 'dislike'
                                ];
                                // Log notification
                                error_log("Notification added for forum post reaction: " . json_encode($notifications[count($notifications) - 1]));
                            }
                        }
                    }
                }
            }
        }
    }

    // Process forum comments and replies
    if (is_array($forum_comments)) {
        foreach ($forum_comments as $comment_id => $comment) {
            // Notifications for comments on user's posts
            if (
                isset($forum_posts[$comment['forum_id']]) &&
                isset($forum_posts[$comment['forum_id']]['alumniId']) &&
                $forum_posts[$comment['forum_id']]['alumniId'] == $current_user_id &&
                isset($comment['alumni_id']) &&
                $comment['alumni_id'] != $current_user_id
            ) {
                $commenter_data = $firebase->retrieve("alumni/" . $comment['alumni_id']);
                $commenter_data = json_decode($commenter_data, true);
                if ($commenter_data) {
                    $commenter_name = $commenter_data['firstname'] . ' ' . $commenter_data['lastname'];
                    $commenter_profile = isset($commenter_data['profile_url']) ? $commenter_data['profile_url'] : '../images/profile.jpg';

                    $notifications[] = [
                        'type' => 'forum_comment',
                        'content_type' => 'forum',
                        'commenter_name' => $commenter_name,
                        'commenter_profile' => $commenter_profile,
                        'date' => $comment['date_commented'],
                        'post_id' => $comment['forum_id'],
                        'comment_id' => $comment_id
                    ];
                }
            }

            // Notifications for replies to user's comments
            if (isset($comment['alumni_id']) && $comment['alumni_id'] == $current_user_id && isset($comment['replies']) && is_array($comment['replies'])) {
                foreach ($comment['replies'] as $reply_id => $reply) {
                    if (isset($reply['alumni_id']) && $reply['alumni_id'] != $current_user_id) {
                        $replier_data = $firebase->retrieve("alumni/" . $reply['alumni_id']);
                        $replier_data = json_decode($replier_data, true);
                        if ($replier_data) {
                            $replier_name = $replier_data['firstname'] . ' ' . $replier_data['lastname'];
                            $replier_profile = isset($replier_data['profile_url']) ? $replier_data['profile_url'] : '../images/profile.jpg';

                            $notifications[] = [
                                'type' => 'forum_reply',
                                'content_type' => 'forum',
                                'replier_name' => $replier_name,
                                'replier_profile' => $replier_profile,
                                'date' => $reply['date_replied'],
                                'post_id' => $reply['forum_id'],
                                'comment_id' => $comment_id,
                                'reply_id' => $reply_id
                            ];
                        }
                    }
                }
            }

            // Notifications for reactions to user's comments
            if (isset($comment['alumni_id']) && $comment['alumni_id'] == $current_user_id && isset($comment['liked_by']) && is_array($comment['liked_by'])) {
                foreach ($comment['liked_by'] as $reactor_id) {
                    if ($reactor_id != $current_user_id) {
                        $reactor_data = $firebase->retrieve("alumni/" . $reactor_id);
                        $reactor_data = json_decode($reactor_data, true);
                        if ($reactor_data) {
                            $reactor_name = $reactor_data['firstname'] . ' ' . $reactor_data['lastname'];
                            $reactor_profile = isset($reactor_data['profile_url']) ? $reactor_data['profile_url'] : '../images/profile.jpg';

                            $notifications[] = [
                                'type' => 'forum_comment_reaction',
                                'content_type' => 'forum',
                                'reactor_name' => $reactor_name,
                                'reactor_profile' => $reactor_profile,
                                'date' => date('Y-m-d H:i:s'),
                                'post_id' => $comment['forum_id'],
                                'comment_id' => $comment_id
                            ];
                        }
                    }
                }
            }
        }
    }
}


function processAdminContent($content, &$notifications, $type, $firebase = null, $current_user_id = null)
{
    if (!is_array($content)) {
        return;
    }

    foreach ($content as $id => $item) {
        $date = isset($item[$type . '_created']) ? $item[$type . '_created'] : (isset($item['created_on']) ? $item['created_on'] : date('Y-m-d H:i:s'));
        $title = isset($item[$type . '_title']) ? $item[$type . '_title'] : (isset($item['gallery_name']) ? $item['gallery_name'] : 'New ' . ucfirst($type));

        $notifications[] = [
            'type' => 'admin_' . $type,
            'content_type' => $type,
            'title' => $title,
            'date' => $date,
            'id' => $id
        ];

        // Process event invitations only if it's an event and we have the necessary parameters
        if ($type === 'event' && $firebase !== null && $current_user_id !== null) {
            processEventInvitations($item, $id, $firebase, $current_user_id, $notifications);
        }
    }
}

function processEventInvitations($event, $event_id, $firebase, $current_user_id, &$notifications)
{
    $user_data = $firebase->retrieve("alumni/" . $current_user_id);
    $user_data = json_decode($user_data, true);

    if (!$user_data) {
        return;
    }

    $user_batch = $user_data['batch'] ?? '';
    $user_course = $user_data['course'] ?? '';

    $course_invited = json_decode($event['course_invited'] ?? '[]', true);
    $batch_invited = json_decode($event['event_invited'] ?? '[]', true);

    if (in_array($user_course, $course_invited) || in_array($user_batch, $batch_invited)) {
        $notifications[] = [
            'type' => 'event_invitation',
            'content_type' => 'event',
            'title' => $event['event_title'],
            'date' => $event['event_created'] ?? date('Y-m-d H:i:s'),
            'id' => $event_id
        ];
    }
}


function getNewNotificationCount($firebase, $current_user_id, $notifications)
{
    $notification_log = $firebase->retrieve("notification_log/" . $current_user_id);
    $notification_log = json_decode($notification_log, true);

    $last_check = isset($notification_log['last_notification_check']) ? $notification_log['last_notification_check'] : '1970-01-01 00:00:00';

    $new_count = 0;
    foreach ($notifications as $notification) {
        if (strtotime($notification['date']) > strtotime($last_check)) {
            $new_count++;
        }
    }

    return $new_count;
}

$new_notification_count = getNewNotificationCount($firebase, $current_user_id, $notifications);


function updateLastNotificationCheck($firebase, $user_id)
{
    $timestamp = date('Y-m-d H:i:s');

    // Check if the user has an entry in the notification_log
    $notification_log = $firebase->retrieve("notification_log/" . $user_id);
    $notification_log = json_decode($notification_log, true);

    if ($notification_log === null) {
        // If the user doesn't have an entry, create a new one
        $firebase->write("notification_log/" . $user_id, ["last_notification_check" => $timestamp]);
    } else {
        // If the user has an entry, update the last_notification_check
        $firebase->update("notification_log/" . $user_id, ["last_notification_check" => $timestamp]);
    }
}



// Process messages
$processed_messages = [];
if (is_array($messages) && !empty($messages)) {
    foreach ($messages as $message_id => $message) {
        $sender_id = $message['senderId'];
        $receiver_id = $message['receiverId'];

        // Only process messages where the current user is either the sender or receiver
        if ($sender_id == $current_user_id || $receiver_id == $current_user_id) {
            $other_user_id = ($sender_id == $current_user_id) ? $receiver_id : $sender_id;

            if (!isset($processed_messages[$other_user_id]) || strtotime($message['timestamp']) > strtotime($processed_messages[$other_user_id]['timestamp'])) {
                $processed_messages[$other_user_id] = $message;
            }
        }
    }
}

$message_count = countMessages($processed_messages);
function countMessages($processed_messages)
{
    return count($processed_messages);
}

function countUnreadMessages($messages, $current_user_id)
{
    $unread_count = 0;

    if (is_array($messages)) {
        foreach ($messages as $message_id => $message) {
            // Count only messages where message_active is 0 and the receiver is the current user
            if (isset($message['message_active']) && $message['message_active'] === 0 && $message['receiverId'] === $current_user_id) {
                $unread_count++;
            }
        }
    }

    return $unread_count;
}

// Use the function to get the count
$message_count = countUnreadMessages($messages, $current_user_id);

function getTimeAgo($date)
{
    $time_ago = strtotime($date);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;

    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "1 day ago" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}

$current_page = $_SERVER['PHP_SELF'];

function isActive($page)
{
    global $current_page;
    if ($page == 'index.php') {
        return $current_page == '/index.php';
    } else {
        return strpos($current_page, $page) !== false;
    }
}
?>