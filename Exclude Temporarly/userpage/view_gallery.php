<?php include '../includes/session.php'; ?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'includes/header.php'; ?>

    <?php

    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    $album_data = $firebase->retrieve("gallery");
    $album_data = json_decode($album_data, true);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);


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
    <!-- Main Menu area start-->


    <?php include 'includes/forum_modal.php'; ?>

    <div class="main-content" style="margin-top:30px;  margin-bottom:30px">
        <div class="sale-statistic-area">
            <div class="container">
                <div class="full_container">
                    <div class="inner_container">
                        <!-- Media Gallery Content -->
                        <div class="midde_cont">
                            <div class="container-fluid">
                                <!-- Gallery section -->
                                <div class="row column4 graph">
                                    <div class="col-md-12">
                                        <div class="white_shd full margin_bottom_30">
                                            <div class="full graph_head">
                                                <div class="heading1 margin_0">
                                                    <h2> <i class="fa fa-image"></i> Media Gallery</h2>
                                                </div>
                                            </div>
                                            <div class="full gallery_section_inner">
                                                <div class="row">
                                                    <?php
                                                    if (!empty($album_data)) {
                                                        foreach ($album_data as $gallery_id => $gallery) {
                                                            ?>
                                                            <div class="col-sm-4 col-md-3 margin_bottom_30">
                                                                <div class="column">
                                                                    <a
                                                                        href="visit_gallery.php?gallery_id=<?php echo $gallery_id; ?>">
                                                                        <img class="img-responsive"
                                                                            src="../admin/<?php echo $gallery['image_url']; ?>"
                                                                            alt="Gallery Image" />
                                                                    </a>
                                                                </div>
                                                                <div class="heading_section">
                                                                    <h4><?php echo $gallery['gallery_name']; ?></h4>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo "No galleries found.";
                                                    }
                                                    ?>
                                                </div>
                                                <!-- Add more gallery items as needed -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Gallery section -->
                            </div>
                        </div>
                        <!-- end content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'global_chatbox.php'?>



  
</body>

</html>