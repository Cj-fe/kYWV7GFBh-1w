<?php
// Include necessary files
require_once 'firebaseRDB.php';
require_once 'config.php';

// Initialize Firebase
$firebase = new firebaseRDB($databaseURL);

// Retrieve gallery data
$galleryData = $firebase->retrieve("gallery");
$galleryData = json_decode($galleryData, true);

// Sort gallery data by creation date in descending order
usort($galleryData, function ($a, $b) {
    return strtotime($b['created_on']) - strtotime($a['created_on']);
});

// Limit gallery data to 6 items
$galleryData = array_slice($galleryData, 0, 6);
?>
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Quick Link</h4>
                <a class="btn btn-link" href="">About Us</a>
                <a class="btn btn-link" href="">Contact Us</a>
                <a class="btn btn-link" href="">Privacy Policy</a>
                <a class="btn btn-link" href="">Terms & Condition</a>
                <a class="btn btn-link" href="">FAQs & Help</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Contact</h4>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>09096437668</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>johnchristianfariola@example.com</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Gallery</h4>
                <div class="row g-2 pt-2">
                    <?php foreach ($galleryData as $galleryItem): ?>
                        <?php if (isset($galleryItem['image_url']) && !empty($galleryItem['image_url'])): ?>
                            <div class="col-4">
                                <img class="img-fluid bg-light p-1"
                                    src="admin/<?php echo htmlspecialchars($galleryItem['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($galleryItem['gallery_name']); ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Logo</h4>
                <div class="row g-2 pt-2">
                    <img src="../images/logo/fb.png" alt="" style="border-radius: 50%; width: 100px; height: auto; ">
                </div>

            </div>


        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">Mcc Alumni Tracker</a>, All Right Reserved.
                    Designed By <a class="border-bottom" href="">John Christian Fariola</a><br><br>
                    Distributed By <a class="border-bottom" href="">IT DEPARTMENT</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="">Home</a>
                        <a href="">Cookies</a>
                        <a href="">Help</a>
                        <a href="">FQAs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>