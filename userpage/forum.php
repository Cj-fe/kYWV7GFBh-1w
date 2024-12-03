<?php include '../includes/session.php'; ?>
<!doctype html>
<html class="no-js" lang="">


<head>
    <?php include 'includes/header.php' ?>
    <!-- Bootstrap CSS -->
    <?php
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    $forum_data = $firebase->retrieve("forum");
    $forum_data = json_decode($forum_data, true);

    $alumni_data = $firebase->retrieve("alumni");
    $alumni_data = json_decode($alumni_data, true);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);

    function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Manila')); // Adjust to you local timezone
        $ago = new DateTime($datetime, new DateTimeZone('Asia/Manila')); // Adjust to your local timezone
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }


    // Set the default timezone to your local timezone
    date_default_timezone_set('Asia/Manila'); // Adjust this to your local timezone
    
    ?>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/forum_modal.php'; ?>

    <div class="main-content">
        <div class="sale-statistic-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                        <div class="sale-statistic-inner notika-shadow mg-tb-30" style="border-radius: 1rem">
                            <button type="button" class="btn btn-primary"
                                style="position: absolute; top: 10px; right: 10px; border-radius: 50%;"
                                data-toggle="modal" data-target="#forumModal">
                                +
                            </button>
                        </div>
                        <?php
                        if (!empty($forum_data)) {
                            $forum_data_with_id = [];
                            foreach ($forum_data as $forum_id => $forum_post) {
                                $forum_data_with_id[] = array_merge(['forum_id' => $forum_id], $forum_post);
                            }

                            usort($forum_data_with_id, function ($a, $b) {
                                return strtotime($b['createdAt']) - strtotime($a['createdAt']);
                            });

                            foreach ($forum_data_with_id as $forum_post) {
                                $forum_id = $forum_post['forum_id'];
                                $alumni_id = $forum_post['alumniId'] ?? null;
                                $current_alumni = $alumni_data[$alumni_id] ?? null;

                                $alumni_name = 'Unknown Alumni';
                                $profile_url = '../images/profile.png';

                                if ($current_alumni) {
                                    $alumni_name = $current_alumni['firstname'] . ' ' . $current_alumni['lastname'];
                                    $profile_url = $current_alumni['profile_url'] ?? '../images/profile.png';
                                }

                                $created_at = $forum_post['createdAt'] ?? null;
                                $formatted_date = 'Unknown Date';
                                $time_ago = '';

                                if ($created_at) {
                                    $date = new DateTime($created_at);
                                    $formatted_date = $date->format('F j, Y');
                                    $time_ago = time_elapsed_string($created_at);
                                }
                                ?>

                                <div class="sale-statistic-inner notika-shadow mg-tb-30" style="border-radius: 1rem"
                                    data-forum-id="<?php echo $forum_id; ?>">
                                    <div class="curved-inner-pro">
                                        <div class="image-section">
                                            <img class="profile"
                                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"
                                                src="<?php echo htmlspecialchars($profile_url); ?>" alt="profile image">
                                        </div>
                                        <div class="info-section">
                                            <h2><?php echo htmlspecialchars($alumni_name); ?></h2>
                                            <span style="font-size:11px"><?php echo htmlspecialchars($formatted_date); ?> &bull;
                                                <?php echo $time_ago; ?></span>
                                        </div>
                                        <?php
                                        $user_id = $_SESSION['user']['id'];

                                        if ($alumni_id === $user_id) {
                                            ?>
                                            <div class="dropdown">
                                                <button class="btn btn-default btn-icon-notika dropdown-toggle" type="button"
                                                    id="dropdownMenu<?php echo $forum_id; ?>" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="notika-icon notika-menu"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu<?php echo $forum_id; ?>">
                                                    <a class="dropdown-item delete-forum" href="#"
                                                        data-forum-id="<?php echo $forum_id; ?>" data-toggle="modal"
                                                        data-target="#deleteForumModal">Delete</a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="content">
                                        <h1><?php echo htmlspecialchars($forum_post['forumName'] ?? 'Untitled'); ?></h1>
                                        <div>
                                            <?php echo $forum_post['forumDescription'] ?? 'No description available'; ?>
                                        </div>

                                        <!-- Reaction buttons -->
                                        <div class="reaction-buttons" data-forum-id="<?php echo $forum_id; ?>">
                                            <span class="reaction-btn" data-reaction="like">
                                                <i class="fa fa-thumbs-up"></i> Like <span class="reaction-count">0</span>
                                            </span>
                                            <span class="reaction-btn" data-reaction="love">
                                                <i class="fa fa-heart"></i> Love <span class="reaction-count">0</span>
                                            </span>
                                            <span class="reaction-btn" data-reaction="laugh">
                                                <i class="fa fa-smile-o"></i> Haha <span class="reaction-count">0</span>
                                            </span>
                                            <span class="reaction-btn" data-reaction="wow">
                                                <i class="fa fa-surprise"></i> Wow <span class="reaction-count">0</span>
                                            </span>
                                            <span class="reaction-btn" data-reaction="sad">
                                                <i class="fa fa-sad-tear"></i> Sad <span class="reaction-count">0</span>
                                            </span>
                                            <span class="reaction-btn" data-reaction="angry">
                                                <i class="fa fa-angry"></i> Angry <span class="reaction-count">0</span>
                                            </span>
                                        </div>

                                        <div class="comments-section">
                                            <div class="add-comment">
                                                <input type="text" class="comment-input" placeholder="Add a comment..."
                                                    data-forum-id="<?php echo $forum_id; ?>">
                                                <button class="add-comment-btn" data-forum-id="<?php echo $forum_id; ?>"
                                                    id="comment-btn-<?php echo $forum_id; ?>">Post</button>
                                            </div>
                                            <div class="comment-list" id="comment-list-<?php echo $forum_id; ?>">
                                                <?php
                                                $all_comments = $firebase->retrieve("forum_comments");

                                                if ($all_comments === null) {
                                                    echo '<div class="no-comments">Unable to retrieve comments at this time.</div>';
                                                } else {
                                                    $all_comments = json_decode($all_comments, true);

                                                    if (!is_array($all_comments)) {
                                                        echo '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                                                    } else {
                                                        $forum_comments = array_filter($all_comments, function ($comment) use ($forum_id) {
                                                            return isset($comment['forum_id']) && $comment['forum_id'] === $forum_id;
                                                        });

                                                        if ($forum_comments) {
                                                            foreach ($forum_comments as $comment_id => $comment) {
                                                                $commenter = $alumni_data[$comment['alumni_id']] ?? null;
                                                                $commenter_name = $commenter ? $commenter['firstname'] . ' ' . $commenter['lastname'] : 'Unknown Alumni';
                                                                $commenter_profile = $commenter['profile_url'] ?? '../images/profile.png';
                                                                ?>
                                                                <div class="comment" data-comment-id="<?php echo $comment_id; ?>">
                                                                    <div class="comment-author">
                                                                        <img src="<?php echo htmlspecialchars($commenter_profile); ?>"
                                                                            class="comment-avatar" alt="author">
                                                                        <span><a
                                                                                href="view_alumni_details.php?id=<?php echo htmlspecialchars($comment['alumni_id']); ?>"><?php echo htmlspecialchars($commenter_name); ?></a></span>

                                                                        &nbsp;&nbsp;&nbsp;
                                                                        <span
                                                                            class="comment-time"><?php echo time_elapsed_string($comment['date_commented']); ?></span>
                                                                    </div>
                                                                    <div class="comment-content">
                                                                        <?php echo htmlspecialchars($comment['comment']); ?>
                                                                    </div>

                                                                    <div class="reply-section">
                                                                        <span class="heart-btn" data-comment-id="<?php echo $comment_id; ?>">
                                                                            <i
                                                                                class="fa <?php echo in_array($_SESSION['user']['id'], $comment['liked_by'] ?? []) ? 'fa-heart' : 'fa-heart-o'; ?>"></i>
                                                                        </span>
                                                                        <span
                                                                            class="heart-count"><?php echo $comment['heart_count'] ?? 0; ?></span>
                                                                        &nbsp;&nbsp;
                                                                        <span class="dislike-btn" data-comment-id="<?php echo $comment_id; ?>">
                                                                            <i
                                                                                class="fa <?php echo in_array($_SESSION['user']['id'], $comment['disliked_by'] ?? []) ? 'fa-thumbs-down' : 'fa-thumbs-o-down'; ?>"></i>
                                                                        </span>
                                                                        <span
                                                                            class="dislike-count"><?php echo $comment['dislike_count'] ?? 0; ?></span>
                                                                        <button class="reply-btn"
                                                                            data-comment-id="<?php echo $comment_id; ?>">Reply</button>
                                                                        <div class="reply-input-area" style="display: none;">
                                                                            <input type="text" class="reply-input"
                                                                                placeholder="Write a reply...">
                                                                            <button class="submit-reply-btn"
                                                                                data-comment-id="<?php echo $comment_id; ?>">Reply</button>
                                                                        </div>
                                                                        <div class="reply-list">
                                                                            <!-- Replies go here -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        } else {
                                                            echo '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="no-forum-message" style="text-align:center; padding:20px; font-size:18px;">NO FORUM AVAILABLE AT THE MOMENT</div>';
                        }
                        ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                        <div class="right-section">
                            <!-- Event, Job, and Forum sections will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'global_chatbox.php'; ?>



    <script src="../bower_components/ckeditor/ckeditor.js"></script>


    <script>
        function submitForm() {
            // Sync CKEditor content before form submission
            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement();
            }
            return true;
        }

        $(function () {
            // Replace the <textarea id="editor1"> with a CKEditor instance
            CKEDITOR.replace('editor1');

            // Optional: If you want to prevent the form from submitting when pressing Enter in the CKEditor
            CKEDITOR.on('instanceReady', function (evt) {
                evt.editor.on('contentDom', function () {
                    evt.editor.document.on('keydown', function (event) {
                        if (event.data.getKeystroke() == 13) {
                            event.cancel();
                        }
                    });
                });
            });
        });

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

        $(document).ready(function () {
            $('#addForumForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'forum_add.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            swal({
                                title: "Success!",
                                text: response.message,
                                type: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Refresh the page after the timer
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            swal({
                                title: "Oops...",
                                text: response.message,
                                type: "error",
                                timer: 3000,
                                showConfirmButton: true
                            });

                            // Optional: Refresh the page after the error alert (comment out if not needed)
                            // setTimeout(function() {
                            //     window.location.reload();
                            // }, 3000);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error:', textStatus, errorThrown);
                        swal({
                            title: "Oops...",
                            text: "Something went wrong! Error: " + textStatus,
                            type: "error",
                            timer: 3000,
                            showConfirmButton: true
                        });

                        // Optional: Refresh the page after the error alert (comment out if not needed)
                        // setTimeout(function() {
                        //     window.location.reload();
                        // }, 3000);
                    }
                });
            });
        });


        $(document).ready(function () {
            $('.add-comment-btn').click(function () {
                var forumId = $(this).data('forum-id');
                var $input = $('.comment-input[data-forum-id="' + forumId + '"]');
                var commentContent = $input.val().trim();

                if (commentContent === "") {
                    swal({
                        title: 'Oops...',
                        text: 'Please enter a comment before posting.',
                        type: 'warning',
                        timer: 5000,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: 'comment_forum.php',
                    data: {
                        comment: commentContent,
                        forum_id: forumId,
                        alumni_id: '<?php echo $_SESSION['user']['id']; ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $input.val('');
                            refreshComments(forumId);
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
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while submitting your comment.',
                            icon: 'error'
                        });
                    }
                });
            });


        });
    </script>

    <script>
        $(document).ready(function () {
            initializeEventListeners();

            // Refresh comments for all forums every 5 seconds
            setInterval(function () {
                $('.sale-statistic-inner').each(function () {
                    var forumId = $(this).find('.add-comment-btn').data('forum-id');
                    if (forumId) {
                        refreshComments(forumId);
                    }
                });
            }, 5000);
        });

        function initializeEventListeners() {
            $(document).on('click', '.add-comment-btn', addCommentWithCooldown);
            $(document).on('click', '.reply-btn', toggleReplyInput);
            $(document).on('click', '.submit-reply-btn', submitReply);
        }

        function addCommentWithCooldown() {
            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }

            addComment.call(this);

            // Disable the button and start cooldown
            $btn.prop('disabled', true);
            var originalText = $btn.text();
            var countdown = 5;

            var intervalId = setInterval(function () {
                $btn.text(originalText + ' (' + countdown + ')');
                countdown--;

                if (countdown < 0) {
                    clearInterval(intervalId);
                    $btn.prop('disabled', false).text(originalText);
                }
            }, 1000);
        }

        function addComment() {
            var forumId = $(this).data('forum-id');
            var $input = $('.comment-input[data-forum-id="' + forumId + '"]');
            var commentContent = $input.val().trim();

            if (commentContent === "") {
                Swal.fire({
                    title: 'Oops...',
                    text: 'Please enter a comment before posting.',
                    icon: 'warning',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            // Removed the AJAX call to comment_forum.php
            // Instead, call a function that will handle the comment submission
            submitComment(forumId, commentContent);
        }

        function submitComment(forumId, commentContent) {
            // TO DO: Implement the logic to submit the comment
            // You can use a JavaScript function or a different AJAX call to handle the comment submission
            console.log('Comment submitted:', forumId, commentContent);
        }

        function refreshComments(forumId) {
            $.ajax({
                url: 'get_forum_comment.php',
                method: 'GET',
                data: { forum_id: forumId },
                success: function (response) {
                    var $commentList = $('#comment-list-' + forumId);
                    var $addCommentSection = $commentList.find('.add-comment').detach();

                    // Store the state, content, and focus of reply input areas
                    var replyStates = {};
                    var focusedInputId = null;
                    $commentList.find('.comment').each(function () {
                        var commentId = $(this).find('.submit-reply-btn').data('comment-id');
                        var $replyArea = $(this).find('.reply-input-area');
                        var $replyInput = $replyArea.find('.reply-input');
                        replyStates[commentId] = {
                            isVisible: $replyArea.is(':visible'),
                            content: $replyInput.val()
                        };
                        if ($replyInput.is(':focus')) {
                            focusedInputId = commentId;
                        }
                    });

                    $commentList.html(response);
                    $commentList.prepend($addCommentSection);

                    // Restore the state, content, and focus of reply input areas
                    $commentList.find('.comment').each(function () {
                        var commentId = $(this).find('.submit-reply-btn').data('comment-id');
                        if (replyStates[commentId]) {
                            var $replyArea = $(this).find('.reply-input-area');
                            var $replyInput = $replyArea.find('.reply-input');
                            if (replyStates[commentId].isVisible) {
                                $replyArea.show();
                            }
                            $replyInput.val(replyStates[commentId].content);
                            if (commentId === focusedInputId) {
                                $replyInput.focus();
                                // Set cursor position to the end of the input
                                var inputLength = $replyInput.val().length;
                                $replyInput[0].setSelectionRange(inputLength, inputLength);
                            }
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error refreshing comments:', error);
                }
            });
        }

        function toggleReplyInput() {
            $(this).siblings('.reply-input-area').toggle();
        }
        function submitReply() {
            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }

            var commentId = $btn.data('comment-id');
            var $replyInput = $btn.siblings('.reply-input');
            var replyContent = $replyInput.val().trim();
            var forumId = $btn.closest('.sale-statistic-inner').find('.add-comment-btn').data('forum-id');

            if (replyContent === "") {
                Swal.fire({
                    title: 'Oops...',
                    text: 'Please enter a reply before submitting.',
                    icon: 'warning',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            // Disable the button and start cooldown
            $btn.prop('disabled', true);
            var originalText = $btn.text();
            var countdown = 5;

            var intervalId = setInterval(function () {
                $btn.text(originalText + ' (' + countdown + ')');
                countdown--;

                if (countdown < 0) {
                    clearInterval(intervalId);
                    $btn.prop('disabled', false).text(originalText);
                }
            }, 1000);

            $.ajax({
                url: 'reply_forum.php',
                method: 'POST',
                data: {
                    forum_id: forumId,
                    comment_id: commentId,
                    reply: replyContent
                },
                success: function (response) {
                    refreshComments(forumId);
                    $replyInput.val(''); // Clear the input after successful submission
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your reply has been added.',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting your reply.',
                        icon: 'error'
                    });
                    // In case of error, enable the button immediately
                    clearInterval(intervalId);
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        }


    </script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.heart-btn, .dislike-btn', function () {
                var $this = $(this);
                var commentId = $this.data('comment-id');
                var action = $this.hasClass('heart-btn') ? 'like' : 'dislike';

                $.ajax({
                    url: 'update_heart_forum.php',
                    method: 'POST',
                    data: {
                        comment_id: commentId,
                        action: action
                    },
                    success: function (response) {
                        console.log('Response:', response); // For debugging
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            $this.siblings('.' + action + '-count').text(data[action + '_count']);

                            // Toggle the icon
                            var $icon = $this.find('i');
                            if (action === 'like') {
                                $icon.toggleClass('fa-heart-o fa-heart', data.liked);
                            } else {
                                $icon.toggleClass('fa-thumbs-o-down fa-thumbs-down', data.disliked);
                            }

                            // Update the opposite button
                            var $oppositeBtn = action === 'like' ? $('.dislike-btn[data-comment-id="' + commentId + '"]') : $('.heart-btn[data-comment-id="' + commentId + '"]');
                            var $oppositeIcon = $oppositeBtn.find('i');
                            if (action === 'like') {
                                $oppositeIcon.toggleClass('fa-thumbs-down fa-thumbs-o-down', !data.disliked);
                            } else {
                                $oppositeIcon.toggleClass('fa-heart fa-heart-o', !data.liked);
                            }
                            $oppositeBtn.siblings('.' + (action === 'like' ? 'dislike' : 'heart') + '-count').text(data[action === 'like' ? 'dislike_count' : 'heart_count']);
                        } else {
                            console.error('Error updating rating:', data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            });
        });

        $(document).ready(function () {
            // Function to update reaction buttons
            function updateReactionButtons($reactionButtons, reactionCounts, userReaction) {
                $reactionButtons.find('.reaction-btn').removeClass('active');
                $reactionButtons.find('.reaction-count').text('0');

                $.each(reactionCounts, function (reaction, count) {
                    $reactionButtons.find('[data-reaction="' + reaction + '"] .reaction-count').text(count);
                });

                if (userReaction) {
                    $reactionButtons.find('[data-reaction="' + userReaction + '"]').addClass('active');
                }
            }

            function loadInitialReactions() {
                $('.reaction-buttons').each(function () {
                    var $reactionButtons = $(this);
                    var forumId = $reactionButtons.data('forum-id');

                    $.ajax({
                        url: 'get_forum_like_count.php',
                        method: 'GET',
                        data: { forum_id: forumId },
                        success: function (response) {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                updateReactionButtons($reactionButtons, data.reaction_counts, data.user_reaction);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error loading reactions:', error);
                        }
                    });
                });
            }

            // Load initial reactions when the page loads
            loadInitialReactions();

            function updateReactionButtons($reactionButtons, reactionCounts, userReaction) {
                $reactionButtons.find('.reaction-btn').removeClass('active');
                $reactionButtons.find('.reaction-count').text('0');

                $.each(reactionCounts, function (reaction, count) {
                    $reactionButtons.find('[data-reaction="' + reaction + '"] .reaction-count').text(count);
                });

                if (userReaction) {
                    $reactionButtons.find('[data-reaction="' + userReaction + '"]').addClass('active');
                }
            }

            // Handle reaction button clicks
            $(document).ready(function () {
                // Load initial reactions when the page loads
                loadInitialReactions();

                // Handle reaction button clicks
                $('.reaction-btn').on('click', function () {
                    var $this = $(this);
                    var forumId = $this.closest('.reaction-buttons').data('forum-id');
                    var reactionType = $this.data('reaction');

                    $.ajax({
                        url: 'update_reaction_forum.php',
                        method: 'POST',
                        data: {
                            forum_id: forumId,
                            reaction_type: reactionType
                        },
                        success: function (response) {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                updateReactionButtons($this.closest('.reaction-buttons'), data.reaction_counts, data.user_reaction);
                            } else {
                                console.error('Error updating reaction:', data.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                });
            });

        });

        $(document).ready(function () {
            // When delete option is clicked in dropdown
            $(document).on('click', '.delete-forum', function (e) {
                e.preventDefault();
                var forumId = $(this).data('forum-id');
                $('#forum_id').val(forumId);
                $('#deleteForumModal').modal('show');
            });

            // When confirm delete button is clicked
            $('#confirmDelete').on('click', function () {
                var forumId = $('#forum_id').val();

                $.ajax({
                    url: 'delete_forum.php',
                    type: 'POST',
                    data: {
                        forum_id: forumId
                    },
                    dataType: 'json',
                    success: function (response) {
                        $('#deleteForumModal').modal('hide');

                        if (response.status === 'success') {
                            swal({
                                title: "Success!",
                                text: response.message,
                                type: "success",
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Find and remove the forum post element
                            removeForum(forumId);
                        } else {
                            swal({
                                title: "Oops...",
                                text: response.message,
                                type: "error",
                                timer: 3000,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#deleteForumModal').modal('hide');
                        console.error('AJAX error:', textStatus, errorThrown);

                        swal({
                            title: "Oops...",
                            text: "Something went wrong! Error: " + textStatus,
                            type: "error",
                            timer: 3000,
                            showConfirmButton: true
                        });
                    }
                });
            });

            // Function to remove forum element with animation
            function removeForum(forumId) {
                const $forumElement = $(`.sale-statistic-inner[data-forum-id="${forumId}"]`);

                if ($forumElement.length) {
                    $forumElement.animate({
                        opacity: 0,
                        height: 0,
                        padding: 0,
                        margin: 0
                    }, 500, function () {
                        $(this).remove();

                        // Check if there are no more forums
                        if ($('.sale-statistic-inner').length === 0) {
                            $('.col-lg-8').append(
                                '<div class="no-forum-message" style="text-align:center; padding:20px; font-size:18px;">' +
                                'NO FORUM AVAILABLE AT THE MOMENT</div>'
                            );
                        }

                        // Adjust the layout to ensure the scrollbar appears if needed
                        adjustLayout();
                    });
                }
            }
            function removeForum(forumId) {
                const $forumElement = $(`.sale-statistic-inner[data-forum-id="${forumId}"]`);

                if ($forumElement.length) {
                    $forumElement.animate({
                        opacity: 0,
                        height: 0,
                        padding: 0,
                        margin: 0
                    }, 500, function () {
                        $(this).remove();

                        // Check if there are no more forums
                        if ($('.sale-statistic-inner').length === 0) {
                            $('.col-lg-8').append(
                                '<div class="no-forum-message" style="text-align:center; padding:20px; font-size:18px;">' +
                                'NO FORUM AVAILABLE AT THE MOMENT</div>'
                            );
                        }

                        // Adjust the layout to ensure the scrollbar appears if needed
                        adjustLayout();
                    });
                }
            }

            function adjustLayout() {
                // Check if the content height is less than the viewport height
                const contentHeight = $('.main-content').outerHeight();
                const viewportHeight = $(window).height();

                if (contentHeight < viewportHeight) {
                    // Add a temporary element to force the scrollbar
                    $('.main-content').append('<div class="temp-scrollbar-filler" style="height: 1px;"></div>');
                } else {
                    // Remove the temporary element if it exists
                    $('.temp-scrollbar-filler').remove();
                }
            }

            // Handle modal dismiss
            $('#deleteForumModal').on('hidden.bs.modal', function () {
                $('#forum_id').val(''); // Clear the forum ID when modal is closed
            });
        });
    </script>
</body>

</html>