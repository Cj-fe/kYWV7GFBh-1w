<?php
require_once '../includes/firebaseRDB.php';
date_default_timezone_set('Asia/Manila');

// Initialize Firebase URL
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);
$event_id = $_GET['event_id'];
$current_user_id = $_GET['alumni_id']; // Make sure to pass this from your JavaScript

// Default profile image URL
$defaultProfileUrl = 'uploads/profile.jpg';

// Fetch comments for the event
$commentData = $firebase->retrieve("event_comments");
$commentData = json_decode($commentData, true);

// Check if there are any comments
if (empty($commentData) || !is_array($commentData)) {
    echo '<li id="no-comments-message" class="center-message">Be the First to Comment</li>';
} else {
    // Filter comments for this event and generate HTML
    $html = '';
    foreach ($commentData as $commentId => $comment) {
        if (isset($comment['event_id']) && $comment['event_id'] === $event_id) {
            $commenterData = $firebase->retrieve("alumni/{$comment['alumni_id']}");
            $commenterData = json_decode($commenterData, true);
            $commenterProfileUrl = $commenterData['profile_url'] ?? $defaultProfileUrl;
            $commenterFirstName = $commenterData['firstname'] ?? 'Unknown';
            $commenterLastName = $commenterData['lastname'] ?? 'User';

            // Get the heart count, default to 0 if not set
            $heartCount = isset($comment['heart_count']) ? $comment['heart_count'] : 0;

            // Check if the current user has liked this comment
            $isLiked = isset($comment['liked_by'][$current_user_id]);
            $likedClass = $isLiked ? 'liked' : '';

            $html .= '<li data-comment-id="' . $commentId . '">
                <div class="comment-main-level">
                    <div class="comment-avatar"><img src="' . $commenterProfileUrl . '" alt=""></div>
                    <div class="comment-box">
                        <div class="comment-head">
                            <h6 class="comment-name by-author">
                                <a href="view_alumni_details.php?id=' . htmlspecialchars($comment['alumni_id']) . '">' . $commenterFirstName . ' ' . $commenterLastName . '</a>
                            </h6>
                            <span>' . timeAgo($comment['date_commented']) . '</span>
                            <i class="fa fa-reply reply-button"></i>
                            <i class="fa fa-heart ' . $likedClass . '" data-comment-id="' . $commentId . '"></i>
                            <span style="float:right;" class="heart-count">' . $heartCount . '</span>
                        </div>
                        <div class="comment-content">
                            ' . htmlspecialchars($comment['comment']) . '
                        </div>
                    </div>
                </div>
                <div class="reply-container" style="display: none;"></div>
                <ul class="comments-list reply-list">';

            if (isset($comment['replies']) && is_array($comment['replies'])) {
                foreach ($comment['replies'] as $replyId => $reply) {
                    $replyAuthorData = $firebase->retrieve("alumni/{$reply['alumni_id']}");
                    $replyAuthorData = json_decode($replyAuthorData, true);
                    $replyAuthorProfileUrl = $replyAuthorData['profile_url'] ?? $defaultProfileUrl;
                    $replyAuthorFirstName = $replyAuthorData['firstname'] ?? 'Unknown';
                    $replyAuthorLastName = $replyAuthorData['lastname'] ?? 'User';

                    $html .= '<li>
                        <div class="comment-avatar"><img src="' . $replyAuthorProfileUrl . '" alt=""></div>
                        <div class="comment-box">
                            <div class="comment-head">
                                <h6 class="comment-name by-author">
                                <a href="view_alumni_details.php?id=' . htmlspecialchars($reply['alumni_id']) . '">' . $replyAuthorFirstName . ' ' . $replyAuthorLastName . '</a>
                                </h6>
                                <span>' . timeAgo($reply['date_replied']) . '</span>
                            </div>
                            <div class="comment-content">
                                ' . htmlspecialchars($reply['comment']) . '
                            </div>
                        </div>
                    </li>';
                }
            }

            $html .= '</ul></li>';
        }
    }

    if (empty($html)) {
        echo '<li id="no-comments-message" class="center-message">Be the First to Comment</li>';
    } else {
        echo $html;
    }
}

function timeAgo($dateString)
{
    $date = new DateTime($dateString);
    $now = new DateTime();
    $interval = $now->diff($date);

    if ($interval->y > 0) {
        return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
    } elseif ($interval->m > 0) {
        return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
    } elseif ($interval->d > 0) {
        return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}
?>