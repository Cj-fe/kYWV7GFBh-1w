<?php include '../includes/session.php'; ?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <!-- Include header -->
    <?php include 'includes/header.php'; ?>
    <?php

    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';


    $firebase = new firebaseRDB($databaseURL);
    $jobId = $_GET['id'] ?? '';

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);

    if ($jobId) {
        $job = json_decode($firebase->retrieve("job/" . $jobId), true);
        if ($job) {
            $workTime = $job['work_time'] ?? 'N/A';
            $jobDetails = [
                'title' => $job['job_title'] ?? 'N/A',
                'workTime' => $workTime,
                'company' => $job['company_name'] ?? 'N/A',
                'expertiseSpecification' => $job['expertise_specification'] ?? 'No expertise specification available.',
                'aboutTheRole' => $job['about_the_role'] ?? 'No details about the role available.',
                'description' => $job['job_description'] ?? 'No description available.',
                'imagePath' => $job['image_path'] ?? 'N/A',
                'job_created' => $job['job_created'] ?? 'N/A',
                'backgroundColor' => ($workTime == 'Full-Time') ? '#e6f7ff' : '#fff0f5',
                'color' => ($workTime == 'Full-Time') ? '#0066cc' : '#cc0066',
            ];
            $jobDetailsJson = json_encode($jobDetails);
        }

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

        $alumni_id = isset($_SESSION["user"]["id"]) ? $_SESSION["user"]["id"] : null;

        // Retrieve alumni data
        if ($alumni_id) {
            $alumniData = $firebase->retrieve("alumni/{$alumni_id}");
            $alumniData = json_decode($alumniData, true);
            $alumniProfileUrl = $alumniData["profile_url"] ?? '';
            $alumniFirstName = $alumniData["firstname"] ?? '';
            $alumniLastName = $alumniData["lastname"] ?? '';
        }
    }

    $data = json_decode($firebase->retrieve("job"), true);
    $jobsData = [];

    $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

    // Retrieve admin data
    $adminData = $firebase->retrieve($adminPath);
    $adminData = json_decode($adminData, true);


    foreach ($data as $id => $job) {
        if ($job['status'] === 'Active') {
            $job['id'] = $id;  // Add the id to the job array
            $jobsData[] = $job;
        }
    }
    usort($jobsData, function ($a, $b) {
        return strtotime($b['job_created']) - strtotime($a['job_created']);
    });
    ?>

    <?php
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
    ?>

</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Start Header Top Area -->
    <?php include 'includes/navbar.php'; ?>
    <!-- End Header Top Area -->

    <!-- Mobile Menu start -->

    <!-- Mobile Menu end -->

    <!-- Main Menu area start -->


    <!-- End Sale Statistic area -->
    <div class="main-content"></div>
    <div class="sale-statistic-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                    <!-- Job Articles -->
                    <div class="sale-statistic-inner notika-shadow mg-tb-30" style="border-radius: 1rem">
                        <!-- Example job item -->
                        <div class="curved-inner-pro">
                            <div class="curved-ctn">
                                <div class="image-section">
                                    <img class="profile" src="../admin/<?php echo $adminData["image_url"]; ?>" alt="">
                                </div>
                                <div class="info-section">
                                    <h2> <?php echo $adminData["firstname"] . " " . $adminData["lastname"]; ?></h2>
                                    <span><?= htmlspecialchars($jobDetails['job_created']) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($jobDetailsJson): ?>
                            <?php $jobDetails = json_decode($jobDetailsJson, true); ?>
                            <div class="content">
                                <h3><?= htmlspecialchars($jobDetails['title']) ?></h3>
                                <div class="job-timesced"
                                    style="background-color: <?= $jobDetails['backgroundColor'] ?>; color: <?= $jobDetails['color'] ?>">
                                    <?= htmlspecialchars($jobDetails['workTime']) ?>
                                </div>
                                <br>
                                <h4><?= htmlspecialchars($jobDetails['company']) ?></h4>
                                <div class="expertise-specification">
                                    <strong>Expertise Specification:</strong>
                                    <p><?= htmlspecialchars_decode($jobDetails['expertiseSpecification']) ?></p>
                                </div>
                                <div class="about-the-role">
                                    <strong>About the Role:</strong>
                                    <p><?= htmlspecialchars_decode($jobDetails['aboutTheRole']) ?></p>
                                </div>
                                <div class="job-description">
                                    <strong>Job Description:</strong>
                                    <p><?= htmlspecialchars_decode($jobDetails['description']) ?></p>
                                </div>
                            </div>
                            <br><br>
                            <img style="border-radius: 1rem"
                                src="../admin/<?= htmlspecialchars($jobDetails['imagePath']) ?>" class="job_img"
                                alt="news image">


                            <br><br>



                            <div class="comments-section">
                                <h1><i class="fa fa-wechat"></i> Comments</h1>
                                <ul id="comment-list" class="comment-list">
                                    <?php if (empty($jobComments)): ?>
                                        <li id="no-comments-message" class="center-message">Be the First to Comment</li>
                                    <?php else: ?>
                                        <?php foreach ($jobComments as $commentId => $comment): ?>
                                            <?php
                                            $commenterData = $firebase->retrieve("alumni/{$comment["alumni_id"]}");
                                            $commenterData = json_decode($commenterData, true);
                                            $commenterProfileUrl = $commenterData["profile_url"] ?? '';
                                            $commenterFirstName = $commenterData["firstname"] ?? '';
                                            $commenterLastName = $commenterData["lastname"] ?? '';
                                            ?>
                                            <li data-comment-id="<?php echo $commentId; ?>" style="list-style:none;">
                                                <div class="comment-avatar"><img src="<?php echo $commenterProfileUrl; ?>" alt="">
                                                </div>
                                                <div class="comment-box">
                                                    <div class="comment-header">
                                                        <h6 class="comment-author">

                                                            <a
                                                                href="view_alumni_details.php?id=<?php echo htmlspecialchars($comment['alumni_id']); ?>"><?php echo $commenterFirstName . " " . $commenterLastName; ?></a>

                                                        </h6>
                                                        <span><?php echo $comment["date_ago"]; ?></span>
                                                        <i class="fa fa-reply reply-button"></i>
                                                        <i class="fa fa-heart heart-icon <?php echo in_array($alumni_id, $comment["liked_by"] ?? []) ? 'liked' : ''; ?>"
                                                            data-comment-id="<?php echo $commentId; ?>"></i>
                                                        <span
                                                            class="heart-count"><?php echo isset($comment["heart_count"]) ? $comment["heart_count"] : 0; ?></span>
                                                    </div>
                                                    <div class="comment-body">
                                                        <?php echo htmlspecialchars($comment["comment"]); ?>
                                                    </div>
                                                </div>
                                            </li>
                                            <div class="reply-container" style="display: none;">
                                                <form class="reply-form">
                                                    <textarea class="reply-textarea"
                                                        placeholder="Write your reply here..."></textarea>
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
                                                            <div class="comment-avatar"><img
                                                                    src="<?php echo htmlspecialchars($replyAuthorData["profile_url"] ?? ''); ?>"
                                                                    alt=""></div>
                                                            <div class="comment-box">
                                                                <div class="comment-header">
                                                                    <h6 class="comment-author">
                                                                        <a
                                                                            href="#"><?php echo htmlspecialchars($replyAuthorData["firstname"] ?? '') . " " . htmlspecialchars($replyAuthorData["lastname"] ?? ''); ?></a>
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
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                                <br><br><br> <br>

                                <div id="uniquePanelBody" class="card-body">
                                    <form id="commentForm" method="POST" action="comment_job.php">
                                        <div class="form-group">
                                            <textarea name="comment" placeholder="Write your comment here!"
                                                class="form-control pb-cmnt-textarea" id="uniqueCommentTextarea"
                                                rows="3"></textarea>
                                        </div>
                                        <div class="text-right">
                                            <button class="btn btn-primary" type="button" id="submitComment">Share</button>
                                        </div>
                                        <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">
                                        <input type="hidden" name="alumni_id" value="<?php echo $alumni_id; ?>">
                                    </form>
                                </div>
                            </div>



                        <?php else: ?>
                            <p>Job not found.</p>
                        <?php endif; ?>

                    </div>
                    <!-- More Job items can be added similarly -->
                </div>

                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                    <div class="right-section">
                        <!-- Event Item Example -->
                        <div class="notika-shadow mg-tb-30 sm-res-mg-t-0 full-height wow fadeInRight"
                            data-wow-delay="0.2s">

                            <?php foreach ($jobsData as $job) {
                                // Skip the current job being viewed
                                if ($job['id'] == $jobId)
                                    continue;
                                ?>
                                <?php
                                $jobTitle = htmlspecialchars($job['job_title']);
                                $workTime = htmlspecialchars($job['work_time']);
                                $company = htmlspecialchars($job['company_name']);
                                $jobDate = htmlspecialchars($job['job_created']);
                                $imagePath = htmlspecialchars($job['image_path'] ?? ''); // Get the image path
                                $id = htmlspecialchars($job['id']);

                                // Determine background color and text color based on work time
                                $backgroundColor = ($workTime == 'Full-Time') ? '#e6f7ff' : '#fff0f5';
                                $color = ($workTime == 'Full-Time') ? '#0066cc' : '#cc0066';
                                ?>
                                <a href="visit_job.php?id=<?php echo $id; ?>">
                                    <div class="card">
                                        <img src="../admin/<?php echo $imagePath; ?>" alt="Event Image" class="event_image">
                                        <div class="card-content">
                                            <div class="job-container">
                                                <div class="job-title"><?php echo $jobTitle; ?></div>
                                                <div class="job-timesced"
                                                    style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $color; ?>;">
                                                    <?php echo $workTime; ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="card-date"><?php echo $company; ?></div>
                                            <div class="card-date">Posted on <?php echo $jobDate; ?></div>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- End Event Item Example -->

                    <a href="job_view.php" class="btn btn-primary btn-icon-notika waves-effect">Show More</a>


                </div>
            </div>
        </div>
    </div>

    <?php include 'global_chatbox.php' ?>


    <!-- Custom JS -->
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

        document.addEventListener("DOMContentLoaded", function () {
            const toggleButtons = document.querySelectorAll(".toggle-button");

            toggleButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const description = this.previousElementSibling;
                    if (description.classList.contains("expanded")) {
                        description.classList.remove("expanded");
                        this.textContent = "Show More...";
                    } else {
                        description.classList.add("expanded");
                        this.textContent = "Show Less";
                    }
                });

                // Check if description needs expanding
                const description = button.previousElementSibling;
                if (description.scrollHeight > description.clientHeight) {
                    button.style.display = "block";
                } else {
                    button.style.display = "none";
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
            var heartedComments = new Set();

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

                $submitButton.prop('disabled', true);
                var formData = $form.serialize();
                $textarea.val('');
                $submitButton.prop('disabled', false);

                var $tempMessage = $('<div class="temp-message">Submitting your comment...</div>');
                $form.after($tempMessage);

                $.ajax({
                    type: 'POST',
                    url: 'comment_job.php',
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
                    return;
                }

                $.ajax({
                    url: 'refresh_job.php',
                    type: 'GET',
                    data: { job_id: '<?php echo $jobId; ?>', last_update: lastUpdate, alumni_id: '<?php echo $alumni_id; ?>' },
                    success: function (response) {
                        var $commentsList = $('#comment-list');

                        if (response.trim() !== '') {
                            var $newCommentsList = $(response);

                            // Preserve hearted comments
                            heartedComments.forEach(function (commentId) {
                                var $oldComment = $commentsList.find('li[data-comment-id="' + commentId + '"]');
                                var $newComment = $newCommentsList.find('li[data-comment-id="' + commentId + '"]');

                                if ($oldComment.length && $newComment.length) {
                                    var $oldHeart = $oldComment.find('.fa-heart');
                                    var $oldHeartCount = $oldComment.find('.heart-count');
                                    $newComment.find('.fa-heart').replaceWith($oldHeart.clone());
                                    $newComment.find('.heart-count').text($oldHeartCount.text());
                                }
                            });

                            // Preserve open reply forms
                            $commentsList.find('.reply-container:visible').each(function () {
                                var commentId = $(this).closest('li').data('comment-id');
                                var $newReplyContainer = $newCommentsList.find('li[data-comment-id="' + commentId + '"] + .reply-container');
                                if ($newReplyContainer.length) {
                                    $newReplyContainer.replaceWith($(this).clone());
                                }
                            });

                            $commentsList.html($newCommentsList.html());
                            lastUpdate = Date.now();
                        }

                        attachEventListeners();
                    },
                    error: function () {
                        console.log('Error refreshing comments');
                    }
                });
            }

            function attachEventListeners() {
                // Reply button click event
                $(document).off('click', '.reply-button').on('click', '.reply-button', function () {
                    var $commentItem = $(this).closest('li');
                    var commentId = $commentItem.data('comment-id');
                    var $replyContainer = $commentItem.next('.reply-container');

                    if ($replyContainer.is(':hidden')) {
                        $replyContainer.show();
                        openReplyFormId = commentId;
                        isReplyFormOpen = true;
                    } else {
                        $replyContainer.hide();
                        isReplyFormOpen = false;
                        openReplyFormId = null;
                    }
                });

                // Reply form submission
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

                    $replyTextarea.val('');

                    var $tempMessage = $('<div class="temp-message">Submitting your reply...</div>');
                    $form.after($tempMessage);

                    $.ajax({
                        type: 'POST',
                        url: 'reply_job.php',
                        data: {
                            comment: replyContent,
                            parent_comment_id: parentCommentId,
                            job_id: '<?php echo $jobId; ?>',
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

                // Heart icon click event
                $(document).off('click', '.heart-icon').on('click', '.heart-icon', function () {
                    var $heartIcon = $(this);
                    var commentId = $heartIcon.data('comment-id');
                    var $heartCount = $heartIcon.next('.heart-count');
                    var currentCount = parseInt($heartCount.text());

                    $.ajax({
                        type: 'POST',
                        url: 'like_job_comment.php',
                        data: {
                            comment_id: commentId,
                            alumni_id: '<?php echo $alumni_id; ?>'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                if (response.action === 'liked') {
                                    $heartIcon.addClass('liked');
                                    $heartCount.text(currentCount + 1);
                                    heartedComments.add(commentId);
                                } else {
                                    $heartIcon.removeClass('liked');
                                    $heartCount.text(currentCount - 1);
                                    heartedComments.delete(commentId);
                                }
                            }
                        },
                        error: function () {
                            console.log('Error updating like');
                        }
                    });
                });
            }

            attachEventListeners();

            setInterval(function () {
                if (!isReplyFormOpen) {
                    refreshComments();
                }
            }, refreshInterval);
        });
    </script>
</body>

</html>