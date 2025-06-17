

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


    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5" style="height: 40%">
    <div class="position-relative">
        <img class="img-fluid" src="homepage/img/carousel-1.png" alt="" style="height: 150px; width: 100%; object-fit: cover;">
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
            style="background: rgba(24, 29, 56, .7);">
            <div class="container">
                <div class="row justify-content-start">
                    <!-- Your content here -->
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Carousel End -->

    
    <!-- 404 Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-exclamation-triangle display-1 text-primary"></i>
                    <h1 class="display-1">404</h1>
                    <h1 class="mb-4">Page Not Found</h1>
                    <p class="mb-4">We’re sorry, the page you have looked for does not exist in our website! Maybe go to our home page or try to use a search?</p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php">Go Back To Home</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 404 End -->
        


    <!-- Footer Start -->
    <?php include 'includes/footer.php' ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="homepage/lib/wow/wow.min.js"></script>


  
    <!-- Template Javascript -->
    <script src="homepage/js/main.js"></script>


    <!-- Modal -->
    <?php include 'includes/auth.php' ?>
</body>

</html>
