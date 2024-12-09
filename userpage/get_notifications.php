<?php
session_start();

require_once 'includes/navbar_php_script.php';

// Generate and return the updated notifications HTML content
if (!empty($notifications)) {
    foreach ($notifications as $notification) {
        ?>
        <div class="notification-item" data-href="<?php
        switch ($notification['type']):
            case 'reply':
            case 'reaction':
                switch ($notification['content_type']):
                    case 'news':
                        echo htmlspecialchars('visit_news.php?id=' . $notification['news_id'] . '#comment-' . $notification['comment_id']);
                        break;
                    case 'job':
                        echo htmlspecialchars('visit_job.php?id=' . $notification['job_id'] . '#comment-' . $notification['comment_id']);
                        break;
                    case 'event':
                        echo htmlspecialchars('visit_event.php?id=' . $notification['event_id'] . '#comment-' . $notification['comment_id']);
                        break;
                    default:
                        echo htmlspecialchars($notification['content_type'] . '.php?id=' . $notification[$notification['content_type'] . '_id'] . '#comment-' . $notification['comment_id']);
                endswitch;
                break;
            case 'forum_post_reaction':
                echo htmlspecialchars('forum.php?id=' . $notification['post_id']);
                break;
            case 'forum_comment':
            case 'forum_reply':
            case 'forum_comment_reaction':
                echo htmlspecialchars('forum.php?id=' . $notification['post_id'] . '#comment-' . $notification['comment_id']);
                break;
            case 'admin_job':
                echo htmlspecialchars('visit_job.php?id=' . $notification['id']);
                break;
            case 'admin_event':
            case 'event_invitation':
                echo htmlspecialchars('visit_event.php?id=' . $notification['id']);
                break;
            case 'admin_news':
                echo htmlspecialchars('visit_news.php?id=' . $notification['id']);
                break;
            case 'admin_gallery':
                echo htmlspecialchars('view_gallery.php?id=' . $notification['id']);
                break;
            default:
                echo '#';
        endswitch;
        ?>">
            <?php
            // Determine image source based on notification type
            switch ($notification['type']):
                case 'reply':
                    $imageSrc = $notification['replier_profile'] ?? '../images/logo/notification.png';
                    break;
                case 'reaction':
                case 'forum_post_reaction':
                case 'forum_comment_reaction':
                    $imageSrc = $notification['reactor_profile'] ?? '../images/logo/notification.png';
                    break;
                case 'forum_comment':
                    $imageSrc = $notification['commenter_profile'] ?? '../images/logo/notification.png';
                    break;
                case 'forum_reply':
                    $imageSrc = $notification['replier_profile'] ?? '../images/logo/notification.png';
                    break;
                case 'admin_job':
                    $imageSrc = '../images/logo/suitcase.png';
                    break;
                case 'admin_event':
                case 'event_invitation':
                    $imageSrc = '../images/logo/calendar.png';
                    break;
                case 'admin_news':
                    $imageSrc = '../images/logo/newspaper.png';
                    break;
                case 'admin_gallery':
                    $imageSrc = '../images/logo/photo-gallery.png';
                    break;
                default:
                    $imageSrc = '../images/logo/notification.png';
            endswitch;
            ?>
            <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Notification Icon">

            <div class="notification-info">
                <p>
                    <?php
                    switch ($notification['type']):
                        case 'reply':
                            echo '<strong>' . htmlspecialchars($notification['replier_name']) . '</strong> replied to your comment on a ' . htmlspecialchars($notification['content_type']);
                            break;
                        case 'reaction':
                            echo '<strong>' . htmlspecialchars($notification['reactor_name']) . '</strong> reacted to your comment on a ' . htmlspecialchars($notification['content_type']);
                            break;
                        case 'forum_post_reaction':
                            echo '<strong>' . htmlspecialchars($notification['reactor_name']) . '</strong> reacted with ' . htmlspecialchars($notification['reaction_type']) . ' to your forum post';
                            break;
                        case 'forum_comment':
                            echo '<strong>' . htmlspecialchars($notification['commenter_name']) . '</strong> commented on your forum post';
                            break;
                        case 'forum_reply':
                            echo '<strong>' . htmlspecialchars($notification['replier_name']) . '</strong> replied to your comment in a forum';
                            break;
                        case 'forum_comment_reaction':
                            echo '<strong>' . htmlspecialchars($notification['reactor_name']) . '</strong> reacted to your comment in a forum';
                            break;
                        case 'admin_job':
                            echo 'New job posting: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                            break;
                        case 'admin_event':
                            echo 'New event: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                            break;
                        case 'admin_news':
                            echo 'New news article: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                            break;
                        case 'admin_gallery':
                            echo 'New gallery album: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                            break;
                        case 'event_invitation':
                            echo 'You\'re invited to the event: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                            break;
                        default:
                            echo 'New notification: <strong>' . htmlspecialchars($notification['title']) . '</strong>';
                    endswitch;
                    ?>
                </p>
                <span class="notification-time"><?php echo htmlspecialchars(getTimeAgo($notification['date'])); ?></span>
            </div>
        </div>
        <?php
    }
} else {
    echo '<p class="no-notifications">No new notifications</p>';
}
?>
