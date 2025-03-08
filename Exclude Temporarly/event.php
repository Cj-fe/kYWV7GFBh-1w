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
                    <h1 class="display-3 text-white animated slideInDown">Events</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Events</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


      <!-- News Start -->
   
      </style>

      <!-- Event Start -->

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center px-3">EVENT</h6>
                <h1 class="mb-5">EVENT</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($eventData as $key => $event): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="item">
                            <center>
                                <a class="openFormButton probootstrap-featured-news-box">
                                    <figure class="probootstrap-media">
                                        <img src="admin/<?php echo $event['image_url']; ?>" alt="Event Image"
                                            class="img-responsive fixed-dimension-img">
                                    </figure>
                                    <div class="probootstrap-text" style="border-top: 1px solid silver; border-left: 1px solid silver; border-right: 1px solid silver; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);">                                        <h3 class="event-title"><?php echo $event['event_title']; ?></h3>
                                        <p class="event-description"><?php echo strip_tags($event['event_description']); ?>
                                        </p>
                                        <span class="probootstrap-date" style="font-size:14px"><i
                                                class="icon-calendar"></i><b>Date Posted:</b> <?php echo $event['event_created']; ?> | <b>Date of Event:</b>   <?php echo $event['event_date']; ?></span>
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


    <!-- Event End -->









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