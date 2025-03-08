<?php

// Include necessary files
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Retrieve job data
$jobData = $firebase->retrieve("job");
$jobData = json_decode($jobData, true);

// Check if jobData is not null and is an array
if (is_array($jobData)) {
    // Sort job data by creation date in descending order
    usort($jobData, function ($a, $b) {
        return strtotime($b['job_created']) - strtotime($a['job_created']);
    });
} else {
    // Initialize jobData as an empty array if it's null
    $jobData = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php' ?>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include 'includes/navbar.php' ?>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Job Listings</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Job Listings</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Job Listings Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center px-3">JOBS</h6>
                <h1 class="mb-5">Available Job Listings</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if (!empty($jobData)): ?>
                    <?php foreach ($jobData as $key => $job): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="item">
                                <center>
                                    <a class="openFormButton probootstrap-featured-news-box">
                                        <figure class="probootstrap-media">
                                            <img src="admin/<?php echo $job['image_path']; ?>" alt="Job Image" class="img-responsive fixed-dimension-img">
                                        </figure>
                                        <div class="probootstrap-text" style="border-top: 1px solid silver; border-left: 1px solid silver; border-right: 1px solid silver; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);">
                                            <h3 class="job-title"><?php echo $job['job_title']; ?></h3>
                                            <p class="event-description"><?php echo strip_tags($job['job_description']); ?></p>
                                            <span class="probootstrap-date" style="font-size:14px"><i class="icon-calendar"></i><b>Date Posted:</b> <?php echo $job['job_created']; ?> | <b>Company:</b> <?php echo $job['company_name']; ?> | <b>Work Time:</b> <?php echo $job['work_time']; ?></span>
                                        </div>
                                    </a>
                                </center>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <center>
                    <h3>No job listings available at the moment.</h3>
                    </center>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Job Listings End -->

    <!-- Footer Start -->
    <?php include 'includes/footer.php' ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="homepage/lib/wow/wow.min.js"></script>
    <script src="homepage/lib/easing/easing.min.js"></script>
    <script src="homepage/lib/waypoints/waypoints.min.js"></script>
    <script src="homepage/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="homepage/js/main.js"></script>

   
</body>
</html>