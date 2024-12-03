<?php
require_once '../includes/firebaseRDB.php'; // Initialize Firebase URL
date_default_timezone_set('Asia/Manila');
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

$news_id = htmlspecialchars($_GET['news_id']); // Sanitize input
$current_user_id = htmlspecialchars($_GET['alumni_id']); // Sanitize input

// Fetch comments for the news article
$commentData = $firebase->retrieve("news_comments");
$commentData = json_decode($commentData, true);

// Check if there are any comments
if (empty($commentData) || !is_array($commentData)) {
    echo '<li id="no-comments-message" class="center-message">Be the First to Comment</li>';
} else {
    // Filter comments for this news article and generate HTML
    $html = '';
    foreach ($commentData as $commentId => $comment) {
        if (isset($comment['news_id']) && $comment['news_id'] === $news_id) {
            $commenterData = $firebase->retrieve("alumni/{$comment['alumni_id']}");
            $commenterData = json_decode($commenterData, true);
            $commenterProfileUrl = htmlspecialchars($commenterData['profile_url'] ?? 'default_profile_url.jpg');
            $commenterFirstName = htmlspecialchars($commenterData['firstname'] ?? 'Unknown');
            $commenterLastName = htmlspecialchars($commenterData['lastname'] ?? 'User');

            // Check if the current user has liked this comment
            $isLiked = isset($comment['liked_by'][$current_user_id]);
            $likedClass = $isLiked ? 'liked' : '';

            // Calculate heart count based on the number of entries in liked_by
            $heartCount = isset($comment['liked_by']) ? count($comment['liked_by']) : 0;

            $html .= '<li data-comment-id="' . htmlspecialchars($commentId) . '">
                <div class="comment-main-level">
                    <div class="comment-avatar"><img src="' . $commenterProfileUrl . '" alt=""></div>
                    <div class="comment-box">
                        <div class="comment-head">
                            <h6 class="comment-name by-author">
                                <a href="#">' . $commenterFirstName . ' ' . $commenterLastName . '</a>
                            </h6>
                            <span>' . htmlspecialchars(timeAgo($comment['date_commented'])) . '</span>
                            <i class="fa fa-reply reply-button"></i>
                            <i class="fa fa-heart ' . $likedClass . '" data-comment-id="' . htmlspecialchars($commentId) . '"></i>
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
                    $replyAuthorProfileUrl = htmlspecialchars($replyAuthorData['profile_url'] ?? 'default_profile_url.jpg');
                    $replyAuthorFirstName = htmlspecialchars($replyAuthorData['firstname'] ?? 'Unknown');
                    $replyAuthorLastName = htmlspecialchars($replyAuthorData['lastname'] ?? 'User');

                    $html .= '<li>
                        <div class="comment-avatar"><img src="' . $replyAuthorProfileUrl . '" alt=""></div>
                        <div class="comment-box">
                            <div class="comment-head">
                                <h6 class="comment-name by-author">
                                    <a href="#">' . $replyAuthorFirstName . ' ' . $replyAuthorLastName . '</a>
                                </h6>
                                <span>' . htmlspecialchars(timeAgo($reply['date_replied'])) . '</span>
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

function timeAgo($dateString) {
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