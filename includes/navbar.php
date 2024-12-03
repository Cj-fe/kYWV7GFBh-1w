<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h2 class="m-0 text-primary"><i class="fa fa-graduation-cap me-3"></i>MCC ALUMNI</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">

        <?php
        $current_page = basename($_SERVER['PHP_SELF']); // Get the current page name

        if (isset($_SESSION['alumni'])) {
            echo '
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../logout.php" class="nav-item nav-link">LOGOUT</a>
            </div>
            ';
        } else {
            echo '
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link ' . ($current_page == 'index.php' ? 'active' : '') . '">Home</a>
                <a href="about.php" class="nav-item nav-link ' . ($current_page == 'about.php' ? 'active' : '') . '">About</a>
                <a href="course.php" class="nav-item nav-link ' . ($current_page == 'course.php' ? 'active' : '') . '">Courses</a>
                
              <!--  <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle ' . (($current_page == '404.php') ? 'active' : '') . '" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="404.php" class="dropdown-item ' . ($current_page == '404.php' ? 'active' : '') . '">In Memoriam</a>
                        <a href="404.php" class="dropdown-item ' . ($current_page == '404.php' ? 'active' : '') . '">Testimonial</a>
                    </div>
                </div>-->

                <a href="news.php" class="nav-item nav-link ' . ($current_page == 'news.php' ? 'active' : '') . '">News</a>

                <a href="event.php" class="nav-item nav-link ' . ($current_page == 'event.php' ? 'active' : '') . '">Event</a>

                <a href="job.php" class="nav-item nav-link ' . ($current_page == 'job.php' ? 'active' : '') . '">Job Posting</a>

                <a href="https://mccdocumenttracker.com/" class="nav-item nav-link ' . ($current_page == 'https://mccdocumenttracker.com/' ? 'active' : '') . '">MCC DOCUMENT TRACKER</a>


               <!-- <a href="contact.php" class="nav-item nav-link ' . ($current_page == 'contact.php' ? 'active' : '') . '">Contact</a> -->
            </div>
            ';
        }
        ?>

    </div>
</nav>
