<?php include '../includes/session.php'; ?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'includes/header.php'; ?>
    
    <?php
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    // Retrieve the gallery_id from the query string
    $gallery_id = $_GET['gallery_id'];

    // Retrieve the gallery data from the "gallery" node
    $gallery_data = $firebase->retrieve("gallery/" . $gallery_id);
    $gallery_data = json_decode($gallery_data, true);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);

    // Retrieve the gallery view data from the "gallery_view" node
    $gallery_view_data = $firebase->retrieve("gallery_view");
    $gallery_view_data = json_decode($gallery_view_data, true);

    // Check if $gallery_view_data is null and set it to an empty array if it is
    if (is_null($gallery_view_data)) {
        $gallery_view_data = [];
    }

    // Filter the gallery view data to only include the items with the current gallery_id
    $filtered_gallery_view_data = array_filter($gallery_view_data, function($item) use ($gallery_id) {
        return $item['gallery_id'] == $gallery_id;
    });
    ?>

</head>

<body>
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
                        <!-- Gallery View Content -->
                        <div class="midde_cont">
                            <div class="container-fluid">
                                <div class="row column4 graph">
                                    <div class="col-md-12">
                                        <div class="white_shd full margin_bottom_30">
                                            <div class="full graph_head">
                                                <div class="heading1 margin_0">
                                                    <h2><i class="fa fa-image"></i> <?php echo $gallery_data['gallery_name']; ?></h2>
                                                </div>
                                            </div>
                                            <div class="full gallery_section_inner">
                                                <div class="row">
                                                    <?php
                                                    if (empty($filtered_gallery_view_data)) {
                                                        echo "<p style='text-align:center; font-size:18px;'>No Gallery Available</p>";
                                                    } else {
                                                        foreach ($filtered_gallery_view_data as $gallery_view_item) {
                                                            ?>
                                                            <div class="col-sm-4 col-md-3 margin_bottom_30">
                                                                <div class="column">
                                                                    <a data-fancybox="gallery" href="../admin/<?php echo $gallery_view_item['image_url']; ?>">
                                                                        <img class="img-responsive" src="../admin/<?php echo $gallery_view_item['image_url']; ?>" alt="Gallery Image" />
                                                                    </a>
                                                                </div>
                                                                <div class="heading_section">
                                                                    <h4 title="<?php echo basename($gallery_view_item['image_url']); ?>"><?php echo basename($gallery_view_item['image_url']); ?></h4>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
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
        </div>
    </div>

    <?php include 'global_chatbox.php'?>

</body>

</html>
