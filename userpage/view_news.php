<?php include '../includes/session.php'; ?>


<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'includes/header.php' ?>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Start Header Top Area -->
    <?php include 'includes/navbar.php' ?>
    <!-- End Header Top Area -->



    <?php
    // Include Firebase database handling class
    require_once '../includes/firebaseRDB.php';

    // Initialize Firebase URL
    require_once '../includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve news data from Firebase
    $data = $firebase->retrieve("news");
    $data = json_decode($data, true);

    $adminData = $firebase->retrieve("admin");
    $adminData = json_decode($adminData, true);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);


    // Extract admin profile image URL
    $adminFirstName = $adminData['firstname'];
    $adminLastName = $adminData['lastname'];

    // Check if data exists and iterate through each news item
    if ($data && is_array($data)) {
        // Build a new array where keys are preserved
        $indexed_data = [];
        foreach ($data as $news_id => $news_item) {
            // Ensure $news_id matches the unique Firebase key
            $indexed_data[$news_id] = $news_item;
        }

        // Sort indexed data by news_created, descending order
        uasort($indexed_data, function ($a, $b) {
            return strtotime($b['news_created']) - strtotime($a['news_created']);
        });

        // Iterate through sorted data and output
        foreach ($indexed_data as $news_id => $news_item) {
            // Retrieve sanitized data
            $image_url = htmlspecialchars($news_item['image_url']);
            $news_author = htmlspecialchars($news_item['news_author']);
            $news_created = htmlspecialchars($news_item['news_created']);

            $news_description = nl2br(preg_replace('/\n{2,}/', '<br><br>', strip_tags($news_item['news_description'])));
            $news_title = htmlspecialchars($news_item['news_title']);
            ?>


            <div class="breadcomb-area wow fadeInUp" data-wow-delay="<?php echo number_format($delay, 1); ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <div class="main_card">
                                <div class="news_card">
                                    <div class="news_image">
                                        <img src="../admin/<?php echo $image_url; ?>" alt="News Image">
                                    </div>
                                    <div class="news_content">
                                        <h3><?php echo $news_title; ?></h3>
                                        <div class="post_info">
                                            <p>Author: <?php echo $news_author; ?></p>
                                            <p class="date_posted"><?php echo $news_created; ?></p>
                                        </div>
                                        <div class="news-description" style="margin-top:20px;">
                                            <p><?php echo $news_description; ?></p>
                                        </div>
                                        <div style="margin-top:20px">
                                            <a id="newsLink" href="visit_news.php?id=<?php echo urlencode($news_id); ?>"
                                                class="btn btn-default btn-icon-notika">
                                                <i class="notika-icon notika-next"></i> READ...
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <?php
        }
    }
    ?>



    <?php include 'global_chatbox.php' ?>

</body>

</html>