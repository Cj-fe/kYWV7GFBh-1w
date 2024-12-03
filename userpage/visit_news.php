<?php include "../includes/session.php"; ?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include "includes/header.php"; ?>

</head>

<body>
    <?php include "includes/navbar.php"; ?>


    <?php
    require_once "../includes/firebaseRDB.php";
    require_once "../includes/config.php";
    $firebase = new firebaseRDB($databaseURL);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);

    function isCommentLikedByUser($comment, $alumniId)
    {
        return isset($comment['liked_by'][$alumniId]);
    }

    function getHeartCount($comment)
    {
        return isset($comment['liked_by']) ? count($comment['liked_by']) : 0;
    }


    function timeAgo($timestamp)
    {
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

    if (isset($_GET["id"])) {
        $news_id = $_GET["id"];
        $news_data = $firebase->retrieve("news/{$news_id}");
        $news_data = json_decode($news_data, true);

        $adminData = $firebase->retrieve("admin");
        $adminData = json_decode($adminData, true);

        if ($news_data) {

            $image_url = htmlspecialchars($news_data["image_url"]);
            $news_author = htmlspecialchars($news_data["news_author"]);
            $news_created = htmlspecialchars($news_data["news_created"]);
            $news_description = $news_data["news_description"];
            $news_title = htmlspecialchars($news_data["news_title"]);

            $alumni_id = isset($_SESSION["user"]["id"])
                ? $_SESSION["user"]["id"]
                : null;

            $isLiked = false;
            if ($alumni_id && isset($event_data['likes']) && is_array($event_data['likes'])) {
                $isLiked = in_array($alumni_id, $event_data['likes']);
            }

            $alumniData = $firebase->retrieve("alumni/{$alumni_id}");
            $alumniData = json_decode($alumniData, true);
            $alumniProfileUrl = $alumniData["profile_url"];
            $alumniFirstName = $alumniData["firstname"];
            $alumniLastName = $alumniData["lastname"];

            $commentData = $firebase->retrieve("news_comments");
            $commentData = json_decode($commentData, true);
            $newsComments = [];
            if (is_array($commentData)) {
                foreach ($commentData as $commentId => $comment) {
                    if ($comment["news_id"] === $news_id) {
                        $comment["date_ago"] = timeAgo(
                            $comment["date_commented"],
                        );
                        $newsComments[$commentId] = $comment;
                        $isLiked = in_array(
                            $alumni_id,
                            $comment["liked_by"] ?? [],
                        );
                    }
                }
            }
            ?>
            <!-- Your existing news display code here -->
            <div class="breadcomb-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="breadcomb-list">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="breadcomb-wp">
                                            <div class="breadcomb-icon">
                                                <img class="profile" src="../admin/<?php echo $adminData[
                                                    "image_url"
                                                ]; ?>" alt="">
                                            </div>
                                            <div class="breadcomb-ctn">
                                                <h4> <?php echo $adminData["firstname"] . " " . $adminData["lastname"]; ?></h4>
                                                <div class="visited-content">
                                                    <i class="uploader">Author: <?php echo $news_author; ?>
                                                    </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <p class="date-uploaded">Date Posted: <?php echo $news_created; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background:white; padding: 20px 190px 20px 20px; text-align: justify;">
                        <h2><?php echo $news_title; ?></h2>
                        <br><br>
                        <?php echo $news_description; ?>
                    </div>
                    <div class="background">
                        <img style="width:100%; height: 500px; object-fit: cover;" src="../admin/<?php echo $image_url; ?>"
                            alt="">
                    </div>
                    <div id="lottie-container"
                        style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; display: none;">
                    </div>

                    <div class="post">
                        <div class="reactions">
                            <button
                                class="btn btn-like <?php echo in_array($alumni_id, $news_data['likes'] ?? []) ? 'liked' : ''; ?>"
                                data-news-id="<?php echo $news_id; ?>">
                                <i class="fa fa-thumbs-up"></i>
                                <?php echo in_array($alumni_id, $news_data['likes'] ?? []) ? 'Liked' : 'Like'; ?>
                            </button>
                            <span class="like-count">&nbsp; &nbsp;
                                &nbsp;<?php echo isset($news_data['likes']) ? count($news_data['likes']) : 0; ?></span>
                        </div>
                        <div class="comment-count"><i class="fa fa-comment"></i>
                            <span><?php echo count($newsComments); ?></span>
                        </div>
                    </div>

                    <div class="additional-content" style="width: 100%; background: white; padding: 20px;">
                        <br><br>

                        <div class="comments-container">
                            <h1><i class="fa fa-wechat"></i> Comments</h1>
                            <ul id="comments-list" class="comments-list">
                                <?php if (empty($newsComments)): ?>
                                    <li id="no-comments-message" class="center-message">Be the First to Comment</li>
                                <?php else: ?>
                                    <?php foreach ($newsComments as $commentId => $comment): ?>
                                        <?php
                                        $commenterData = $firebase->retrieve("alumni/{$comment["alumni_id"]}");
                                        $commenterData = json_decode($commenterData, true);
                                        $commenterProfileUrl = htmlspecialchars($commenterData["profile_url"]);
                                        $commenterFirstName = htmlspecialchars($commenterData["firstname"]);
                                        $commenterLastName = htmlspecialchars($commenterData["lastname"]);
                                        ?>
                                        <li data-comment-id="<?php echo $commentId; ?>">
                                            <div class="comment-main-level">
                                                <div class="comment-avatar"><img src="<?php echo $commenterProfileUrl; ?>" alt=""></div>
                                                <div class="comment-box">
                                                    <div class="comment-head">
                                                        <h6 class="comment-name by-author">
                                                            <a href="#"><?php echo $commenterFirstName .
                                                                " " .
                                                                $commenterLastName; ?></a>
                                                        </h6>
                                                        <span><?php echo $comment[
                                                            "date_ago"
                                                        ]; ?></span>
                                                        <i class="fa fa-reply reply-button"></i>
                                                        <i class="fa fa-heart heart-icon" data-comment-id="<?php echo $commentId; ?>"
                                                            data-liked="<?php echo $isLiked ? 'true' : 'false'; ?>"></i>

                                                    </div>
                                                    <div class="comment-content">
                                                        <?php echo htmlspecialchars(
                                                            $comment["comment"],
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="reply-container" style="display: none;">
                                                <form class="reply-form">
                                                    <textarea class="reply-textarea" placeholder="Write your reply here..."></textarea>
                                                    <button type="submit" class="btn btn-primary submit-reply">Reply</button>

                                                    <input type="hidden" name="parent_comment_id" value="<?php echo $commentId; ?>">
                                                </form>
                                            </div>
                                            <ul class="comments-list reply-list">
                                                <?php if (
                                                    isset($comment["replies"])
                                                ): ?>
                                                    <?php foreach ($comment["replies"] as $replyId => $reply): ?>
                                                        <?php
                                                        $replyAuthorData = $firebase->retrieve("alumni/{$reply["alumni_id"]}");
                                                        $replyAuthorData = json_decode($replyAuthorData, true);
                                                        $replyAuthorProfileUrl = htmlspecialchars($replyAuthorData["profile_url"]);
                                                        $replyAuthorFirstName = htmlspecialchars($replyAuthorData["firstname"]);
                                                        $replyAuthorLastName = htmlspecialchars($replyAuthorData["lastname"]);
                                                        ?>
                                                        <li>
                                                            <div class="comment-avatar"><img src="<?php echo $replyAuthorData[
                                                                "profile_url"
                                                            ]; ?>" alt=""></div>
                                                            <div class="comment-box">
                                                                <div class="comment-head">
                                                                    <h6 class="comment-name by-author">
                                                                        <a href="#"><?php echo $replyAuthorData[
                                                                            "firstname"
                                                                        ] .
                                                                            " " .
                                                                            $replyAuthorData[
                                                                                "lastname"
                                                                            ]; ?></a>
                                                                    </h6>
                                                                    <span><?php echo timeAgo(
                                                                        $reply[
                                                                            "date_replied"
                                                                        ],
                                                                    ); ?></span>
                                                                </div>
                                                                <div class="comment-content">
                                                                    <?php echo htmlspecialchars(
                                                                        $reply[
                                                                            "comment"
                                                                        ],
                                                                    ); ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>


                        <div class="container pb-cmnt-container">
                            <div class="row">
                                <div class="col-md-11 col-md-offset-0">
                                    <div id="uniquePanelInfo" class="panel panel-info">
                                        <div id="uniquePanelBody" class="panel-body">
                                            <form id="commentForm" method="POST" action="comment_news.php">
                                                <textarea name="comment" placeholder="Write your comment here!"
                                                    class="pb-cmnt-textarea" id="uniqueCommentTextarea"></textarea>
                                                <button class="btn btn-primary pull-right" type="button"
                                                    id="submitComment">Share</button>
                                                <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
                                                <input type="hidden" name="alumni_id" value="<?php echo $alumni_id; ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
        } else {
            echo "News item not found.";
        }
    } else {
        echo "Invalid request.";
    }
    ?>
        </div>
    </div>

    <?php include 'global_chatbox.php' ?>


    <script>
        $('#logoutBtn').on('click', function () {
            swal({
                title: "Are you sure?",
                text: "You will be directed to the main page!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Logout!",
                cancelButtonText: "No, cancel!",
            }).then(function (isConfirm) {
                if (isConfirm) {
                    swal("Logout!", "Logging out", "success").then(function () {
                        window.location.href = '../logout.php';
                    });
                } else {
                    swal("Cancelled", "Your Logout is Cancelled :)", "error");
                }
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            var openReplyFormId = null;
            var refreshInterval = 5000;
            var lastUpdate = Date.now();
            var currentOpenReplyContent = '';
            var caretPosition = 0;
            var isReplyFormOpen = false;
            var heartedComments = new Set(); // Store IDs of comments that have been hearted


            $('#submitComment').click(function () {
                var $submitButton = $(this);
                var $form = $('#commentForm');
                var $textarea = $('.pb-cmnt-textarea');
                var commentContent = $textarea.val().trim();

                if (commentContent === "") {
                    swal({
                        title: 'Oops...',
                        text: 'Please enter a comment before sharing.',
                        type: 'warning',
                        timer: 5000,
                        onOpen: function () {
                            swal.showLoading()
                        }
                    }).then(
                        function () { },
                        function (dismiss) {
                            if (dismiss === 'timer') {
                                console.log('I was closed by the timer')
                            }
                        }
                    )
                    return;
                }

                // Disable the button temporarily to prevent double-clicking
                $submitButton.prop('disabled', true);

                // Prepare the form data
                var formData = $form.serialize();

                // Clear the textarea and re-enable the button immediately
                $textarea.val('');
                $submitButton.prop('disabled', false);

                // Show a temporary "Submitting..." message
                var $tempMessage = $('<div class="temp-message">Submitting your comment...</div>');
                $form.after($tempMessage);

                $.ajax({
                    type: 'POST',
                    url: 'comment_news.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        $tempMessage.remove();
                        if (response.status === 'success') {
                            refreshComments();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Your comment has been added.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function () {
                        $tempMessage.remove();
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while submitting your comment.',
                            icon: 'error'
                        });
                    }
                });
            });

            function refreshComments() {
                if (isReplyFormOpen) {
                    return; // Skip refresh if a reply form is open
                }

                if (openReplyFormId) {
                    var $currentOpenReplyForm = $(`#comments-list li[data-comment-id="${openReplyFormId}"] .reply-container`);
                    var $currentTextarea = $currentOpenReplyForm.find('.reply-textarea');
                    currentOpenReplyContent = $currentTextarea.val();
                    caretPosition = $currentTextarea[0].selectionStart;
                }

                $.ajax({
                    url: 'refresh_news.php',
                    type: 'GET',
                    data: { news_id: '<?php echo $news_id; ?>', last_update: lastUpdate, alumni_id: '<?php echo $alumni_id; ?>' },
                    success: function (response) {
                        var $commentsList = $('#comments-list');

                        if (response.trim() !== '') {
                            var $newCommentsList = $(response);

                            // Preserve heart counts and liked status for hearted comments
                            heartedComments.forEach(function (commentId) {
                                var $oldComment = $commentsList.find('li[data-comment-id="' + commentId + '"]');
                                var $newComment = $newCommentsList.find('li[data-comment-id="' + commentId + '"]');

                                if ($oldComment.length && $newComment.length) {
                                    var $oldHeart = $oldComment.find('.fa-heart');
                                    var $oldHeartCount = $oldComment.find('.heart-count');
                                    var $newHeart = $newComment.find('.fa-heart');
                                    var $newHeartCount = $newComment.find('.heart-count');

                                    $newHeart.addClass('liked');
                                    $newHeartCount.text($oldHeartCount.text());
                                }
                            });

                            $commentsList.html($newCommentsList);
                            lastUpdate = Date.now();
                        }

                        if (openReplyFormId) {
                            var $newReplyContainer = $commentsList.find(`li[data-comment-id="${openReplyFormId}"] .reply-container`);
                            $newReplyContainer.html(`
                    <form class="reply-form">
                        <textarea class="reply-textarea" placeholder="Write your reply here...">${currentOpenReplyContent}</textarea>
                        <button type="submit" class="btn btn-primary submit-reply">Reply</button>
                        <input type="hidden" name="parent_comment_id" value="${openReplyFormId}">
                    </form>
                `).show();

                            var $newTextarea = $newReplyContainer.find('.reply-textarea');
                            $newTextarea.focus();
                            $newTextarea[0].setSelectionRange(caretPosition, caretPosition);
                        }

                        attachEventListeners();
                    },
                    error: function () {
                        console.log('Error refreshing comments');
                    }
                });
            }



            function attachEventListeners() {
                $(document).off('click', '.reply-button').on('click', '.reply-button', function () {
                    var $commentItem = $(this).closest('li');
                    var commentId = $commentItem.data('comment-id');
                    var $replyContainer = $commentItem.find('.reply-container');

                    if ($replyContainer.is(':empty')) {
                        var replyForm = `
                    <form class="reply-form">
                        <textarea class="reply-textarea" placeholder="Write your reply here..."></textarea>
                        <button type="submit" class="btn btn-primary submit-reply">Reply</button>
                        <input type="hidden" name="parent_comment_id" value="${commentId}">
                    </form>
                `;
                        $replyContainer.html(replyForm).show();
                        openReplyFormId = commentId;
                        isReplyFormOpen = true;
                    } else {
                        $replyContainer.toggle();
                        isReplyFormOpen = $replyContainer.is(':visible');
                        openReplyFormId = isReplyFormOpen ? commentId : null;
                    }
                });

                $(document).off('submit', '.reply-form').on('submit', '.reply-form', function (e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $replyTextarea = $form.find('.reply-textarea');
                    var replyContent = $replyTextarea.val().trim();
                    var parentCommentId = $form.find('input[name="parent_comment_id"]').val();

                    if (replyContent === "") {
                        Swal.fire({
                            title: 'Oops...',
                            text: 'Please enter a reply before submitting.',
                            icon: 'warning',
                            timer: 5000,
                            timerProgressBar: true
                        });
                        return;
                    }

                    // Clear the textarea immediately
                    $replyTextarea.val('');

                    // Show a temporary "Submitting..." message
                    var $tempMessage = $('<div class="temp-message">Submitting your reply...</div>');
                    $form.after($tempMessage);

                    $.ajax({
                        type: 'POST',
                        url: 'reply_news.php',
                        data: {
                            comment: replyContent,
                            parent_comment_id: parentCommentId,
                            news_id: '<?php echo $news_id; ?>',
                            alumni_id: '<?php echo $alumni_id; ?>'
                        },
                        dataType: 'json',
                        success: function (response) {
                            $tempMessage.remove();
                            if (response.status === 'success') {
                                isReplyFormOpen = false;
                                refreshComments();
                                openReplyFormId = null;
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Your reply has been added.',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function () {
                            $tempMessage.remove();
                            Swal.fire({
                                title: 'Error',
                                text: 'An error occurred while submitting your reply.',
                                icon: 'error'
                            });
                        }
                    });
                });
            }

            // Initial attachment of event listeners
            attachEventListeners();

            // Set interval to refresh comments every 5 seconds
            setInterval(function () {
                if (!isReplyFormOpen) {
                    refreshComments();
                }
            }, refreshInterval);
        });
    </script>


    <script>
        $(document).ready(function () {
            $(document).on('click', '.fa-heart', function () {
                var $heart = $(this);
                var commentId = $heart.data('comment-id');
                var isLiked = $heart.hasClass('liked');

                $.ajax({
                    url: 'update_heart_news.php',
                    method: 'POST',
                    data: {
                        comment_id: commentId,
                        alumni_id: '<?php echo $alumni_id; ?>',
                        is_liked: !isLiked
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $heart.toggleClass('liked', data.is_liked);
                            $heart.next('.heart-count').text(data.heart_count);
                        }
                    }
                });
            });

            function updateHearts() {
                $('.fa-heart').each(function () {
                    var $heart = $(this);
                    var commentId = $heart.data('comment-id');

                    $.ajax({
                        url: 'get_news_heart_status.php',
                        method: 'GET',
                        data: {
                            comment_id: commentId,
                            alumni_id: '<?php echo $alumni_id; ?>'
                        },
                        success: function (response) {
                            var data = JSON.parse(response);
                            $heart.toggleClass('liked', data.is_liked);
                            $heart.next('.heart-count').text(data.heart_count);
                        }
                    });
                });
            }

            updateHearts();
            setInterval(updateHearts, 5000);
        });


        $(document).ready(function () {
            var newsId = $('.btn-like').data('news-id');
            var alumniId = $('.btn-like').data('alumni-id');

            var animation = lottie.loadAnimation({
                container: document.getElementById('lottie-container'),
                renderer: 'svg',
                loop: false,
                autoplay: false,
                path: 'js/lottie/check.json',
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid slice'
                }
            });

            function resizeLottieAnimation() {
                var container = document.getElementById('lottie-container');
                var desiredWidth = 500;
                var desiredHeight = 500;

                container.style.width = desiredWidth + 'px';
                container.style.height = desiredHeight + 'px';

                if (animation) {
                    animation.resize();
                }
            }

            window.addEventListener('resize', resizeLottieAnimation);
            resizeLottieAnimation();

            function playLikeAnimation() {
                $('#lottie-container').show();
                animation.goToAndPlay(0);

                var animationDuration = animation.getDuration() * 1000;
                setTimeout(function () {
                    $('#lottie-container').hide();
                }, animationDuration);
            }

            $('.btn-like').on('click', function () {
                var $likeButton = $(this);
                var $likeCount = $likeButton.siblings('.like-count');
                var isCurrentlyLiked = $likeButton.hasClass('liked');

                if (!isCurrentlyLiked) {
                    playLikeAnimation();
                }

                $.ajax({
                    url: 'update_like_news_count.php',
                    method: 'POST',
                    data: {
                        news_id: newsId,
                        alumni_id: '<?php echo $alumni_id; ?>'
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $likeButton.toggleClass('liked', data.is_liked);
                            $likeCount.text(data.like_count);

                            $likeButton.html('<i class="fa fa-thumbs-up"></i> ' + (data.is_liked ? 'Liked' : 'Like'));
                        }
                    }
                });
            });

            function updateCounts() {
                $.ajax({
                    url: 'get_news_like_count.php',
                    method: 'GET',
                    data: {
                        news_id: newsId,
                        alumni_id: '<?php echo $alumni_id; ?>'
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        $('.comment-count span').text(data.comment_count);
                        $('.like-count').text(data.like_count);
                        $('.btn-like').each(function () {
                            var $btn = $(this);
                            var isLiked = data.is_liked;
                            $btn.toggleClass('liked', isLiked);
                            $btn.html('<i class="fa fa-thumbs-up"></i> ' + (isLiked ? 'Liked' : 'Like'));
                        });
                    }
                });
            }

            updateCounts();
            setInterval(updateCounts, 5000);
        });
    </script>
    <style>
        .btn-like {
            background-color: white;
            color: black;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-like.liked {
            background-color: blue;
            color: white;
        }

        .fa-heart {
            color: #ccc !important;
            cursor: pointer;
        }

        .fa-heart.liked {
            color: red !important;
        }
    </style>
</body>

</html>