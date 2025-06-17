<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/firebaseRDB.php';

require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);
// Fetch gallery data from Firebase
$surveyData = $firebase->retrieve("survey_set");

// Decode JSON data into associative arrays
$survey = json_decode($surveyData, true) ?: [];
?>




<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    Report <i class="fa fa-angle-right"></i> Survey
                </h1>
                <div class="box-inline ">

                    <div class="search-container">
                        <input type="text" class="search-input" id="search-input" placeholder="Search...">
                        <button class="search-button" onclick="filterTable()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Survey Report</li>
                    <li class="active" style="color:white">Survey Report List</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Reminder</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
                    unset($_SESSION['success']);
                }
                ?>



                <div class="row">
                    <div class="col-xs-12">
                        <div class="box"
                            style="min-height:20px; background: transparent !important; border:none; box-shadow:none">

                            <div class="box-body" style="padding:30px; ">
                                <div class="report-container">
                                    <!-- Loop through survey data -->
                                    <?php foreach ($survey as $surveyId => $surveyDetails): ?>
                                        <div class="report-item">
                                            <div class="report-box"
                                                style="background-image: url('../images/bakground.png'); background-size: cover; background-position: center;">
                                                <img src="../images/school_logo.png" class="report-img" alt="">
                                                <div class="report-detail">
                                                    <h4><?php echo htmlspecialchars($surveyDetails['survey_title']); ?></h4>
                                                </div>
                                                <div class="overlay" style="background-color: rgba(0, 0, 0, 0.5);">
                                                    <a href="answer_set.php?id=<?php echo htmlspecialchars($surveyId); ?>"
                                                        class='btn btn-default btn-class'
                                                        style="opacity: 1; background:linear-gradient(to right, #90caf9, #047edf 99%); color:white; width: 100px; border: none;">View
                                                        Report</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/survey_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <script>

    </script>
</body>

</html>