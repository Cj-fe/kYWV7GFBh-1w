<?php
session_start();
if (isset($_SESSION['alumni'])) {
    if ($_SESSION['forms_completed'] == false) {
        header('location: userpage/alumni_profile.php');
    } else {
        header('location: userpage/index.php');
    }
    exit();
}
?>
<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Get current date
$date = date('Y-m-d');

// Check if an entry for today exists
$existingData = $firebase->retrieve("track_visitors/$date");
$existingData = json_decode($existingData, true);

if ($existingData) {
    // Increment visitor count
    $newCount = $existingData['count'] + 1;
    $firebase->update("track_visitors", $date, ['count' => $newCount]);
} else {
    // Create new entry for today
    $firebase->insert("track_visitors", [$date => ['count' => 1]]);
}
?>
<?php
require_once 'includes/firebaseRDB.php';

$firebase = new firebaseRDB($databaseURL);

$data = $firebase->retrieve("news");
$data = json_decode($data, true);

$eventData = $firebase->retrieve("event");
$eventData = json_decode($eventData, true);

// Sort data by date in descending order
usort($data, function ($a, $b) {
    return strtotime($b['news_created']) - strtotime($a['news_created']);
});

// Slice to get only the first 5 items
$data = array_slice($data, 0, 5);

?>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        var urlParams = new URLSearchParams(window.location.search);
        var error = urlParams.get('error');
        if (error) {
            var decodedError = decodeURIComponent(error);
            var title, footer;

            if (decodedError.includes("No matching alumni found")) {
                title = "No Match Found";

            } else if (decodedError.includes("already verified")) {
                title = "Already Verified";
                footer = '<a href="#">Forgot your password?</a>';
            } else {
                title = "Oppps..";

            }

            Swal.fire({
                icon: "error",
                title: title,
                text: decodedError,
                footer: footer
            });

            // Remove the error parameter from the URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
<!DOCTYPE html>
<html lang="en">


<?php include 'includes/header.php' ?>


<body>


    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
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
                    <h1 class="display-3 text-white animated slideInDown">News</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">News</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


      <!-- News Start -->
   
      </style>


<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
            <h6 class="section-title bg-white text-center px-3">News</h6>
            <h1 class="mb-5">News</h1>
        </div>
        <div class="owl-carousel testimonial-carousel position-relative">
            <?php foreach ($data as $key => $news): ?>
                <div class="testimonial-item">
                    <div class="item">
                        <a class="openFormButton probootstrap-featured-news-box">
                            <figure class="probootstrap-media">
                                <img src="admin/<?php echo $news['image_url']; ?>" alt="News Image"
                                    class="img-responsive fixed-dimension-img">
                            </figure>
                            <div class="probootstrap-text">
                                <h3 class="news-title"><?php echo $news['news_title']; ?></h3>
                                <p class="news-description"><?php echo strip_tags($news['news_description']); ?></p>
                                <span class="probootstrap-date"><i
                                        class="icon-calendar"></i><?php echo $news['news_created']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="includes/auth.php" class="btn openFormButton" style="float:right">View All</a>
    </div>
</div>
<!-- News End -->









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