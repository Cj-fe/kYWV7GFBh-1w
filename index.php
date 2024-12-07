<?php
session_start();
require_once 'includes/firebaseRDB.php'; // Use require_once to prevent multiple inclusions
require_once 'includes/config.php'; // Include configuration file

// Function to check if data is empty
function isDataEmpty($data)
{
    return empty($data);
}

// Initialize Firebase
$firebase = new firebaseRDB($databaseURL);

// Retrieve admin data
$adminData = $firebase->retrieve("admin");
$adminData = json_decode($adminData, true);

// Ensure admin data is retrieved
if (!$adminData || !isset($adminData[$adminNodeKey])) {
    header('Location: includes/404.html');
    exit();
}

$adminNode = $adminData[$adminNodeKey];

// Verify layer_one and layer_two exist in the structure
if (!isset($adminNode[$layer_one]) || !isset($adminNode[$layer_one][$layer_two])) {
    header('Location: includes/404.html');
    exit();
}

$layerNode = $adminNode[$layer_one][$layer_two];

// Check if token_url is not in URL but exists in admin data
if (!isset($_GET['token_url']) && isset($layerNode['token_url'])) {
    // Redirect to the same page with token_url parameter
    header('Location: ' . $_SERVER['PHP_SELF'] . '?token_url=' . urlencode($layerNode['token_url']));
    exit();
}

// Check if token_url parameter exists and matches
if (!isset($_GET['token_url']) || $_GET['token_url'] !== $layerNode['token_url']) {
    header('Location: includes/404.html');
    exit();
}

// Check if the user is an alumni and redirect accordingly
if (isset($_SESSION['alumni'])) {
    if ($_SESSION['forms_completed'] == false) {
        header('location: userpage/alumni_profile.php');
    } else {
        header('location: userpage/index.php');
    }
    exit();
}

// Retrieve data from Firebase
$data = json_decode($firebase->retrieve("news"), true) ?? [];
$eventData = json_decode($firebase->retrieve("event"), true) ?? [];
$jobData = json_decode($firebase->retrieve("job"), true) ?? [];

// Sort and slice data as needed
usort($jobData, function ($a, $b): int {
    return strtotime($b['job_created']) - strtotime($a['job_created']);
});
usort($data, function ($a, $b) {
    return strtotime($b['news_created']) - strtotime($a['news_created']);
});
$data = array_slice($data, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">

<style>
    .swal2-shown,
    .swal2-height-auto {
        padding: 0 !important;
    }
</style>
<?php include 'includes/header.php' ?>

<body>

    <!-- Navbar Start -->
    <?php include 'includes/navbar.php' ?>
    <!-- Navbar End -->

    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="homepage/img/carousel-1.png" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                    style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Welcome Back
                                </h5>
                                <h1 class="display-3 text-white animated slideInDown">Reconnect. Rediscover.
                                </h1>
                                <p class="fs-5 text-white mb-4 pb-2">Welcome to the MCC Alumni Network! We are thrilled
                                    to have you back and eager to help you reconnect with old friends, reimagine your
                                    future, and rediscover the spirit of our beloved institution.</p>
                                <a href="includes/auth.php"
                                    class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft openFormButton">Signin</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="homepage/img/graduation_carousel_1.png" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                    style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                </h5>
                                <h1 class="display-3 text-white animated slideInDown">Exclusive Events & Career
                                    Opportunities
                                </h1>
                                <p class="fs-5 text-white mb-4 pb-2">Gain access to alumni-only events. Explore
                                    exclusive job listings, mentorship programs, and career development resources
                                    tailored just for you. Whether it’s professional networking or social gatherings,
                                    there’s always something exciting happening. Your success is our pride.</p>
                                <a href="includes/auth.php"
                                    class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft openFormButton">Signin</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="homepage/img/graduation_carousel.png" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                    style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                </h5>
                                <h1 class="display-3 text-white animated slideInDown">Give Back & Stay Informed
                                </h1>
                                <p class="fs-5 text-white mb-4 pb-2">Share your experience, volunteer, or contribute to
                                    our various programs. Receive updates on the latest news, research breakthroughs,
                                    and achievements from our community. Celebrate the milestones that keep our legacy
                                    alive. Your involvement can inspire current students and shape the future of our
                                    institution.</p>
                                <a href="includes/auth.php"
                                    class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft openFormButton">Signin</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Service Start -->

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-2" style="height: 250px;"> <!-- Adjust height as needed -->
                            <i class="fa fa-3x fa-newspaper text-primary mb-4"></i>
                            <h5 class="mb-3">News</h5>
                            <p>Explore our comprehensive news section to stay informed about the latest events, and
                                updates</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-2" style="height: 250px;"> <!-- Adjust height as needed -->
                            <i class="fa fa-3x fa-calendar text-primary mb-4"></i>
                            <h5 class="mb-3">Event</h5>
                            <p>Stay engaged with Madridejos Community College's vibrant alumni network. </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-2" style="height: 250px;"> <!-- Adjust height as needed -->
                            <i class="fa fa-3x fa-briefcase text-primary mb-4"></i>
                            <h5 class="mb-3">Job Opportunities</h5>
                            <p>Find the latest job openings tailored for our alumni. </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-2" style="height: 250px;"> <!-- Adjust height as needed -->
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">Gallery Library</h5>
                            <p>Discover photos, videos, and more from our alumni events and achievements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service End -->


    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100"
                            src="homepage/img/Community-College-Madridejos-ee8b60f4.jpeg" alt=""
                            style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start  pe-3">About Us</h6>
                    <h1 class="mb-4">Welcome to MCC ALUMNI </h1>
                    <p class="mb-4">Welcome to the Madridejos Community College (MCC) Alumni Network! Our community is
                        built on the shared experiences and achievements of our graduates, fostering lifelong
                        connections and supporting each other's professional and personal growth.</p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Celebrating Achievements:
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Lifelong Learning</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Community Engagement
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Connecting Graduates</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Networking Opportunities
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Career Development
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- DEPARTMENT Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center  px-3">DEPARTMENT</h6>
                <h1 class="mb-5">DEPARTMENT</h1>
            </div>
            <div class="row g-3">
                <div class="col-lg-7 col-md-6">
                    <div class="row g-3">
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.1s">
                            <a class="position-relative d-block overflow-hidden" href="">
                                <img class="img-fluid" src="homepage/img/bsit.png" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">IT Department</h5>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.3s">
                            <a class="position-relative d-block overflow-hidden" href="">
                                <img class="img-fluid" src="homepage/img/education.png" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">Education Department</h5>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.3s">
                            <a class="position-relative d-block overflow-hidden" href="">
                                <img class="img-fluid" src="homepage/img/bshm.png" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">HM Department</h5>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.5s">
                            <a class="position-relative d-block overflow-hidden" href="">
                                <img class="img-fluid" src="homepage/img/bsba.png" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">BSBA Department</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 wow zoomIn" data-wow-delay="0.7s" style="min-height: 350px;">
                    <a class="position-relative d-block h-100 overflow-hidden" href="">
                        <img class="img-fluid position-absolute w-100 h-100" src="images/no_image.png" alt=""
                            style="object-fit: cover;">

                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- DEPARTMENT nd -->


    <!-- News Start -->

    </style>


    <?php if (!isDataEmpty($data)): ?>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
                    <h6 class="section-title bg-white text-center px-3">News</h6>
                    <h1 class="mb-5">News</h1>
                </div>
                <div class="owl-carousel testimonial-carousel position-relative">
                    <?php foreach ($data as $news): ?>
                        <div class="testimonial-item">
                            <div class="item">
                                <a class="openFormButton probootstrap-featured-news-box">
                                    <figure class="probootstrap-media">
                                        <img src="admin/<?php echo htmlspecialchars($news['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                            alt="News Image" class="img-responsive fixed-dimension-img">
                                    </figure>
                                    <div class="probootstrap-text">
                                        <h3 class="news-title">
                                            <?php echo htmlspecialchars($news['news_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p class="news-description">
                                            <?php echo htmlspecialchars(strip_tags($news['news_description']), ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                        <span class="probootstrap-date"><i
                                                class="icon-calendar"></i><?php echo htmlspecialchars($news['news_created'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="includes/auth.php" class="btn openFormButton" style="float:right">View All</a>
            </div>
        </div>
    <?php endif; ?>
    <!-- News End -->


    <!-- Event Start -->

    <?php if (!isDataEmpty($eventData)): ?>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title bg-white text-center px-3">EVENT</h6>
                    <h1 class="mb-5">EVENT</h1>
                </div>
                <div class="row g-4 justify-content-center">
                    <?php foreach ($eventData as $event): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="item">
                                <center>
                                    <a class="openFormButton probootstrap-featured-news-box">
                                        <figure class="probootstrap-media">
                                            <img src="admin/<?php echo htmlspecialchars($event['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                                alt="Event Image" class="img-responsive fixed-dimension-img">
                                        </figure>
                                        <div class="probootstrap-text"
                                            style="border-top: 1px solid silver; border-left: 1px solid silver; border-right: 1px solid silver; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);">
                                            <h3 class="event-title">
                                                <?php echo htmlspecialchars($event['event_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                            <p class="event-description">
                                                <?php echo htmlspecialchars(strip_tags($event['event_description']), ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                            <span class="probootstrap-date" style="font-size:14px"><i
                                                    class="icon-calendar"></i><b>Date Posted:</b>
                                                <?php echo htmlspecialchars($event['event_created'], ENT_QUOTES, 'UTF-8'); ?> |
                                                <b>Date of Event:</b>
                                                <?php echo htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </a>
                                </center>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="includes/auth.php" class="btn openFormButton" style="float:right">View All</a>
            </div>
        </div>
    <?php endif; ?>


    <!-- Event End -->


    <?php if (!isDataEmpty($jobData)): ?>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title bg-white text-center px-3">JOBS</h6>
                    <h1 class="mb-5">Available Job Listings</h1>
                </div>
                <div class="row g-4 justify-content-center">
                    <?php foreach ($jobData as $job): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="item">
                                <center>
                                    <a class="openFormButton probootstrap-featured-news-box">
                                        <figure class="probootstrap-media">
                                            <img src="admin/<?php echo htmlspecialchars($job['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                                alt="Job Image" class="img-responsive fixed-dimension-img">
                                        </figure>
                                        <div class="probootstrap-text"
                                            style="border-top: 1px solid silver; border-left: 1px solid silver; border-right: 1px solid silver; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);">
                                            <h3 class="job-title">
                                                <?php echo htmlspecialchars($job['job_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                            <p class="event-description">
                                                <?php echo htmlspecialchars(strip_tags($job['job_description']), ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                            <span class="probootstrap-date" style="font-size:14px"><i
                                                    class="icon-calendar"></i><b>Date Posted:</b>
                                                <?php echo htmlspecialchars($job['job_created'], ENT_QUOTES, 'UTF-8'); ?> |
                                                <b>Company:</b>
                                                <?php echo htmlspecialchars($job['company_name'], ENT_QUOTES, 'UTF-8'); ?> |
                                                <b>Work Time:</b>
                                                <?php echo htmlspecialchars($job['work_time'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </a>
                                </center>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Footer Start -->
    <?php include 'includes/footer.php' ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script nonce="<random-nonce>" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script nonce="<random-nonce>"
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script nonce="<random-nonce>" src="homepage/lib/wow/wow.min.js"></script>
    <script nonce="<random-nonce>" src="homepage/lib/easing/easing.min.js"></script>
    <script nonce="<random-nonce>" src="homepage/lib/waypoints/waypoints.min.js"></script>
    <script nonce="<random-nonce>" src="homepage/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script nonce="<random-nonce>" src="homepage/js/main.js"></script>

    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.onkeydown = function (e) {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                (e.ctrlKey && e.key === 'U')
            ) {
                e.preventDefault();
            }
        };

        // Disable developer tools
        function disableDevTools() {
            if (window.devtools.isOpen) {
                window.location.href = "about:blank";
            }
        }

        // Check for developer tools every 100ms
        setInterval(disableDevTools, 100);

        // Disable selecting text
        document.onselectstart = function (e) {
            e.preventDefault();
        };
    </script>
    <!-- Modal -->

</body>

</html>