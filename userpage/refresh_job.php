<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

date_default_timezone_set('Asia/Manila');


$firebase = new firebaseRDB($databaseURL);

function timeAgo($timestamp) {
    $currentTime = time();
    $commentTime = strtotime($timestamp);
    $difference = $currentTime - $commentTime;

    if ($difference < 60) {
        return "Just now";
    } elseif ($difference >= 60 && $difference < 3600) {
        $time = round($difference / 60);
        return $time . " minute" . ($time > 1 ? "s" : "") . " ago";
    } elseif ($difference >= 3600 && $difference < 86400) {
        $time = round($difference / 3600);
        return $time . " hour" . ($time > 1 ? "s" : "") . " ago";
    } else {
        $time = round($difference / 86400);
        return $time . " day" . ($time > 1 ? "s" : "") . " ago";
    }
}

if (isset($_GET['job_id']) && isset($_GET['last_update']) && isset($_GET['alumni_id'])) {
    $jobId = $_GET['job_id'];
    $lastUpdate = $_GET['last_update'];
    $alumniId = $_GET['alumni_id'];

    $commentData = $firebase->retrieve("job_comments");
    $commentData = json_decode($commentData, true);
    $jobComments = [];

    if (is_array($commentData)) {
        foreach ($commentData as $commentId => $comment) {
            if ($comment["job_id"] === $jobId) {
                $comment["date_ago"] = timeAgo($comment["date_commented"]);
                $jobComments[$commentId] = $comment;
            }
        }
    }

    if (!empty($jobComments)) {
        echo '<ul id="comment-list" class="comment-list">';
        foreach ($jobComments as $commentId => $comment) {
            $commenterData = $firebase->retrieve("alumni/{$comment["alumni_id"]}");
            $commenterData = json_decode($commenterData, true);
            $commenterProfileUrl = $commenterData["profile_url"] ?? '';
            $commenterFirstName = $commenterData["firstname"] ?? '';
            $commenterLastName = $commenterData["lastname"] ?? '';
            ?>
            <li data-comment-id="<?php echo $commentId; ?>" style="list-style:none;">
                <div class="comment-avatar"><img src="<?php echo $commenterProfileUrl; ?>" alt=""></div>
                <div class="comment-box">
                    <div class="comment-header">
                        <h6 class="comment-author">
                            <a href="view_alumni_details.php?id=<?php echo htmlspecialchars($comment['alumni_id']); ?>"><?php echo $commenterFirstName . " " . $commenterLastName; ?></a>
                        </h6>
                        <span><?php echo $comment["date_ago"]; ?></span>
                        <i class="fa fa-reply reply-button"></i>
                        <i class="fa fa-heart heart-icon <?php echo in_array($alumniId, $comment["liked_by"] ?? []) ? 'liked' : ''; ?>"
                            data-comment-id="<?php echo $commentId; ?>"></i>
                        <span class="heart-count"><?php echo isset($comment["heart_count"]) ? $comment["heart_count"] : 0; ?></span>
                    </div>
                    <div class="comment-body">
                        <?php echo htmlspecialchars($comment["comment"]); ?>
                    </div>
                </div>
            </li>
            <div class="reply-container" style="display: none;">
                <form class="reply-form">
                    <textarea class="reply-textarea" placeholder="Write your reply here..."></textarea>
                    <button type="submit" class="btn btn-primary submit-reply">Reply</button>
                    <input type="hidden" name="parent_comment_id" value="<?php echo $commentId; ?>">
                </form>
            </div>
            <ul class="comment-list reply-list">
                <?php if (isset($comment["replies"]) && is_array($comment["replies"])): ?>
                    <?php foreach ($comment["replies"] as $replyId => $reply): ?>
                        <?php
                        $replyAuthorData = $firebase->retrieve("alumni/{$reply["alumni_id"]}");
                        $replyAuthorData = json_decode($replyAuthorData, true);
                        ?>
                        <li>
                            <div class="comment-avatar"><img src="<?php echo htmlspecialchars($replyAuthorData["profile_url"] ?? ''); ?>" alt=""></div>
                            <div class="comment-box">
                                <div class="comment-header">
                                    <h6 class="comment-author">
                                    <a href="view_alumni_details.php?id=<?php echo htmlspecialchars($comment['alumni_id']); ?>"><?php echo htmlspecialchars($replyAuthorData["firstname"] ?? '') . " " . htmlspecialchars($replyAuthorData["lastname"] ?? ''); ?></a>

                                    </h6>
                                    <span><?php echo timeAgo($reply["date_replied"]); ?></span>
                                </div>
                                <div class="comment-body">
                                    <?php echo htmlspecialchars($reply["comment"]); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <?php
        }
        echo '</ul>';
    } else {
        echo '<ul id="comment-list" class="comment-list"><li id="no-comments-message" class="center-message">Be the First to Comment</li></ul>';
    }
}
?>