<?php
include 'includes/session.php';
include 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/firebaseRDB.php';

$firebase = new firebaseRDB($databaseURL);

// Get the alumni ID from the URL
$alumniId = $_GET['id'] ?? '';
if (!$alumniId) {
    die("No alumni ID provided");
}

// Fetch the alumni data
$alumniData = $firebase->retrieve("alumni/$alumniId");
$alumni = json_decode($alumniData, true);
if (!$alumni) {
    die("Alumni not found");
}

// Fetch course, batch, and category data
$courseData = json_decode($firebase->retrieve("course"), true) ?: [];
$batchData = json_decode($firebase->retrieve("batch_yr"), true) ?: [];
$categoryData = json_decode($firebase->retrieve("category"), true) ?: [];
?>

<style>
.box-header {
    width: 100%;
    height: 35px;
    background: white !important;
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
}
.box {
    border-radius: 0 !important;
}
</style>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper content-flex">
        <!-- Main container -->
        <div class="main-container">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1> Alumni Status </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-3">
                        <!-- Profile Image -->
                        <div class="box box-primary">
                            <div class="box-body box-profile">
                                <img class="profile-user-img img-responsive img-circle" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" src="../userpage/<?php echo htmlspecialchars(!empty($alumni['profile_url']) ? $alumni['profile_url'] : 'uploads/profile.jpg'); ?>" alt="User profile picture">
                                <h3 class="profile-username text-center">
                                    <?php echo htmlspecialchars((!empty($alumni['auxiliaryname']) ? $alumni['auxiliaryname'] . ' ' : '') . (!empty($alumni['firstname']) ? $alumni['firstname'] . ' ' : '') . (!empty($alumni['middlename']) ? $alumni['middlename'] . ' ' : '') . (!empty($alumni['lastname']) ? $alumni['lastname'] : 'N/A')); ?>
                                </h3>
                                <p class="text-muted text-center">BATCH <?php echo htmlspecialchars(isset($batchData[$alumni['batch']]['batch_yrs']) ? $batchData[$alumni['batch']]['batch_yrs'] : 'N/A'); ?> | ALUMNI ID: <?php echo htmlspecialchars(!empty($alumni['studentid']) ? $alumni['studentid'] : 'N/A'); ?></p>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->

                        <!-- About Me Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">About Me</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <strong><i class="fa fa-book margin-r-5"></i> Education</strong>
                                <br><br>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars(isset($courseData[$alumni['course']]['courCode']) ? $courseData[$alumni['course']]['courCode'] : 'N/A'); ?>
                                </p>
                                <hr>
                                <strong><i class="fa fa-map-marker margin-r-5"></i> Address</strong>
                                <br><br>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars((!empty($alumni['addressline1']) ? $alumni['addressline1'] . ', ' : '') . (!empty($alumni['city']) ? $alumni['city'] . ', ' : '') . (!empty($alumni['state']) ? $alumni['state'] : 'N/A')); ?>
                                </p>
                                <hr>
                                <strong><i class="fa fa-pencil margin-r-5"></i> Work Status</strong>
                                <br><br>
                                <p>
                                    <span class="label label-<?php echo htmlspecialchars(isset($alumni['work_status']) && $alumni['work_status'] === 'Employed' ? 'success' : 'danger'); ?>">
                                        <?php echo htmlspecialchars(isset($alumni['work_status']) ? $alumni['work_status'] : 'N/A'); ?>
                                    </span>
                                </p>
                                <hr>
                                <strong><i class="fa fa-file-text-o margin-r-5"></i> Work Classification</strong>
                                <br><br>
                                <p><?php echo htmlspecialchars((isset($alumni['work_classification']) && isset($categoryData[$alumni['work_classification']]['category_name'])) ? $categoryData[$alumni['work_classification']]['category_name'] : 'N/A'); ?></p>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#activity" data-toggle="tab">Personal</a></li>
                                <!-- <li><a href="#timeline" data-toggle="tab">Timeline</a></li>
                                <li><a href="#settings" data-toggle="tab">Settings</a></li>-->
                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane" id="activity">
                                    <!-- Activity content -->
                                    <h3><i class="fa fa-info"></i> Personal Information</h3>
                                    <hr>
                                    <div class="personal-info-grid">
                                        <div class="info-item">
                                            <i class="fa fa-birthday-cake"></i>
                                            <span class="info-label">Date of Birth</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['birthdate']) ? $alumni['birthdate'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-venus-mars"></i>
                                            <span class="info-label">Sex</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['gender']) ? $alumni['gender'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-group"></i>
                                            <span class="info-label">Civil Status</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['civilstatus']) ? $alumni['civilstatus'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-location-arrow"></i>
                                            <span class="info-label">City</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['city']) ? $alumni['city'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-map"></i>
                                            <span class="info-label">Province</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['state']) ? $alumni['state'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-map-pin"></i>
                                            <span class="info-label">Zip Code</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['zipcode']) ? $alumni['zipcode'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-phone"></i>
                                            <span class="info-label">Contact Number</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['contactnumber']) ? $alumni['contactnumber'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-envelope"></i>
                                            <span class="info-label">Email</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['email']) ? $alumni['email'] : 'N/A'); ?></span>
                                        </div>
                                    </div>
                                    <br><br>
                                    <h3><i class="fa fa-building-o"></i> Employment Information</h3>
                                    <hr>
                                    <div class="employment-info-grid">
                                        <div class="info-item">
                                            <i class="fa fa-building"></i>
                                            <span class="info-label">Organization/Company</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['name_company']) ? $alumni['name_company'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-location-arrow"></i>
                                            <span class="info-label">Location</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['employment_location']) ? $alumni['employment_location'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-id-badge"></i>
                                            <span class="info-label">Position</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['work_position']) ? $alumni['work_position'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-tasks"></i>
                                            <span class="info-label">Type of Work</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['type_of_work']) ? $alumni['type_of_work'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-check-circle"></i>
                                            <span class="info-label">Employment Status</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['work_employment_status']) ? $alumni['work_employment_status'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-flag-checkered"></i>
                                            <span class="info-label">First Employment</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['first_employment_date']) ? $alumni['first_employment_date'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-clock-o"></i>
                                            <span class="info-label">Current Employment</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['date_for_current_employment']) ? $alumni['date_for_current_employment'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-money"></i>
                                            <span class="info-label">Monthly Income</span>
                                            <span class="info-value">PHP <?php echo htmlspecialchars(!empty($alumni['current_monthly_income']) ? $alumni['current_monthly_income'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-smile-o"></i>
                                            <span class="info-label">Job Satisfaction</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['job_satisfaction']) ? $alumni['job_satisfaction'] : 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fa fa-graduation-cap"></i>
                                            <span class="info-label">Work Related to Course</span>
                                            <span class="info-value"><?php echo htmlspecialchars(!empty($alumni['work_related']) ? $alumni['work_related'] : 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="timeline">
                                    <!-- Timeline content -->
                                    <h3>Career Timeline</h3>
                                    <?php if (isset($alumni['work_status']) && $alumni['work_status'] === 'Employed'): ?>
                                        <ul class="timeline">
                                            <li>
                                                <i class="fa fa-briefcase bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars(!empty($alumni['date_for_current_employment']) ? $alumni['date_for_current_employment'] : 'N/A'); ?></span>
                                                    <h3 class="timeline-header">Employed at <?php echo htmlspecialchars(!empty($alumni['name_company']) ? $alumni['name_company'] : 'N/A'); ?></h3>
                                                    <div class="timeline-body">
                                                        <p><strong>Name of the Organization/Company:</strong> <?php echo htmlspecialchars(!empty($alumni['name_company']) ? $alumni['name_company'] : 'N/A'); ?></p>
                                                        <p><strong>Location of Employment:</strong> <?php echo htmlspecialchars(!empty($alumni['employment_location']) ? $alumni['employment_location'] : 'N/A'); ?></p>
                                                        <p><strong>Position:</strong> <?php echo htmlspecialchars(!empty($alumni['work_position']) ? $alumni['work_position'] : 'N/A'); ?></p>
                                                        <p><strong>Type Of Work:</strong> <?php echo htmlspecialchars(!empty($alumni['type_of_work']) ? $alumni['type_of_work'] : 'N/A'); ?></p>
                                                        <p><strong>Employment Status:</strong> <?php echo htmlspecialchars(!empty($alumni['work_employment_status']) ? $alumni['work_employment_status'] : 'N/A'); ?></p>
                                                        <p><strong>First Employment:</strong> <?php echo htmlspecialchars(!empty($alumni['first_employment_date']) ? $alumni['first_employment_date'] : 'N/A'); ?></p>
                                                        <p><strong>Current Employment:</strong> <?php echo htmlspecialchars(!empty($alumni['date_for_current_employment']) ? $alumni['date_for_current_employment'] : 'N/A'); ?></p>
                                                        <p><strong>Monthly Income:</strong> PHP <?php echo htmlspecialchars(!empty($alumni['current_monthly_income']) ? $alumni['current_monthly_income'] : 'N/A'); ?></p>
                                                        <p><strong>Job Satisfaction:</strong> <?php echo htmlspecialchars(!empty($alumni['job_satisfaction']) ? $alumni['job_satisfaction'] : 'N/A'); ?></p>
                                                        <p><strong>Work Related to Course:</strong> <?php echo htmlspecialchars(!empty($alumni['work_related']) ? $alumni['work_related'] : 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <i class="fa fa-briefcase bg-aqua"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars(!empty($alumni['first_employment_date']) ? $alumni['first_employment_date'] : 'N/A'); ?></span>
                                                    <h3 class="timeline-header">First Employment Date</h3>
                                                </div>
                                            </li>
                                        </ul>
                                    <?php else: ?>
                                        <p>No employment history available.</p>
                                    <?php endif; ?>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="settings">
                                    <!-- Settings content -->
                                    <h3>Account Settings</h3>
                                    <p>Last updated: <?php echo htmlspecialchars(!empty($alumni['date_responded']) ? $alumni['date_responded'] : 'N/A'); ?></p>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
        </div>
        <!-- Sidebar -->
    </div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</div>
</body>
</html>