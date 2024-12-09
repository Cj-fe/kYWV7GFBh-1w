
<!DOCTYPE html>
<html>

<head>
    <?php
    include '../includes/session.php';
    include 'includes/header.php';
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);
    $alumni_data = $firebase->retrieve("alumni");
    $alumni_data = json_decode($alumni_data, true);
    $categoriesData = $firebase->retrieve('category');
    $categories = json_decode($categoriesData, true);
    $batchYears = $firebase->retrieve("batch_yr");
    $batchYears = json_decode($batchYears, true);
    $courseKey = "course";
    $data = $firebase->retrieve($courseKey);
    $data = json_decode($data, true);
    $current_user_id = $_SESSION['alumni_id'];
    $current_user = $alumni_data[$current_user_id] ?? null;
    $messages = json_decode($firebase->retrieve("messages"), true);
    $messages_json = json_encode($messages);

    if (!$current_user) {
        echo "User not found";
        exit;
    }

    function getValue($array, $key)
    {
        return isset($array[$key]) && !empty($array[$key]) ? htmlspecialchars($array[$key], ENT_QUOTES, 'UTF-8') : "N/A";
    }

    function formatValue($value)
    {
        if (is_numeric($value)) {
            return number_format((float) $value, 2);
        } else {
            return 'N/A';
        }
    }

    function formatDate($date) {
        if (empty($date) || $date == 'N/A') {
            return 'N/A';
        }
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return 'Invalid Date';
        }
        return date("Y-m-d", $timestamp); // Format for HTML5 date input
    }
    

    $birthdate = getValue($current_user, 'birthdate');
    $birthdateFormatted = formatDate($birthdate);
    $firstEmploymentDate = getValue($current_user, 'first_employment_date');
    $firstEmploymentDateFormatted = formatDate($firstEmploymentDate);
    $currentEmploymentDate = getValue($current_user, 'date_for_current_employment');
    $currentEmploymentDateFormatted = formatDate($currentEmploymentDate);
    ?>

    <style>
        .dropdown-menu {
            transform: translate(0) !important;
        }
    </style>
</head>

<body>

    <?php include 'includes/navbar.php'; ?>




    <!-----PROFILE PAGE---->
    <?php include 'includes/sidebar.php'; ?>

    <div class="profile-content">
        <?php
        // At the top of main.php, after starting the session
        if (isset($_SESSION['update_message'])) {
            echo '<div class="alert alert-info">' . $_SESSION['update_message'] . '</div>';
            unset($_SESSION['update_message']); // Clear the message after displaying it
        }
        ?>
        <form id="updateProfileForm" action="edit_user_account.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
            <div id="personal-info" class="profile-section">
                <h3>Personal Information</h3>
                <div class="post-col" style="width:100% !important">
                    <!-- Post Section -->
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="firstname" class="form-label">
                                        <i class="fas fa-user icon"></i> First Name
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="firstname" name="firstname" class="form-control"
                                            placeholder="Firstname"
                                            value="<?php echo getValue($current_user, 'firstname'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="middlename" class="form-label">
                                        <i class="fas fa-user-circle icon"></i> Middle Name
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="middlename" name="middlename" class="form-control"
                                            placeholder="Middlename"
                                            value="<?php echo getValue($current_user, 'middlename'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="lastname" class="form-label">
                                        <i class="fas fa-user-tag icon"></i> Last Name
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="lastname" name="lastname" class="form-control"
                                            placeholder="Lastname"
                                            value="<?php echo getValue($current_user, 'lastname'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="regionSelect" class="form-label">
                                        <i class="fas fa-globe icon"></i> Region
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="regionSelect" name="region" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Loading regions...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="provinceSelect" class="form-label">
                                        <i class="fas fa-map-marker-alt icon"></i> Province
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="provinceSelect" name="state" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Select a region first</option>
                                            <?php
                                            $current_state = getValue($current_user, 'state');
                                            if (!empty($current_state)) {
                                                echo "<option value=\"$current_state\" selected>$current_state</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="citySelect" class="form-label">
                                        <i class="fas fa-city icon"></i> City/Municipality
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="citySelect" name="city" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Select a province first</option>
                                            <?php
                                            $current_city = getValue($current_user, 'city');
                                            if (!empty($current_city)) {
                                                echo "<option value=\"$current_city\" selected>$current_city</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="barangaySelect" class="form-label">
                                        <i class="fas fa-map icon"></i> Barangay
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="barangaySelect" name="barangay" class="form-control selectpicker"
                                            data-live-search="true">
                                            <option value="">Select a city/municipality first</option>
                                            <?php
                                            $current_barangay = getValue($current_user, 'barangay');
                                            if (!empty($current_barangay)) {
                                                echo "<option value=\"$current_barangay\" selected>$current_barangay</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">


                                <div class="form-group">
                                    <label for="birthdate" class="form-label">
                                        <i class="fas fa-calendar-alt icon"></i> Birth Date
                                    </label>
                                    <div class="nk-datapk-ctm form-elet-mg" id="data_1">
                                        <div class="input-group date nk-int-st">
                                            <span class="input-group-addon"></span>
                                            <input type="date" id="birthdate" class="form-control"
                                                value="<?php echo $birthdateFormatted; ?>">
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars icon"></i> Gender
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="gender" name="gender" class="form-control selectpicker">
                                            <option value="Male" <?php echo (getValue($current_user, 'gender') == 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo (getValue($current_user, 'gender') == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="civilstatus" class="form-label">
                                        <i class="fas fa-heart icon"></i> Civil Status
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="civilstatus" name="civilstatus" class="form-control selectpicker">
                                            <option value="Single" <?php echo (getValue($current_user, 'civilstatus') == 'Single') ? 'selected' : ''; ?>>Single</option>
                                            <option value="Married" <?php echo (getValue($current_user, 'civilstatus') == 'Married') ? 'selected' : ''; ?>>Married</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <h3>Contact</h3>
                <div class="post-col" style="width:100% !important">
                    <!-- Post Section -->
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="contactnumber" class="form-label">
                                        <i class="fas fa-phone icon"></i> Contact Number
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="contactnumber" name="contactnumber" class="form-control"
                                            value="<?php echo getValue($current_user, 'contactnumber'); ?>"
                                            placeholder="Contact Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="reserve_email" class="form-label">
                                        <i class="fas fa-envelope icon"></i> Email
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="reserve_email" name="reserve_email" class="form-control"
                                            value="<?php echo getValue($current_user, 'reserve_email'); ?>"
                                            placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="addressline1" class="form-label">
                                        <i class="fas fa-home icon"></i> Current Address
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="addressline1" name="addressline1" class="form-control"
                                            value="<?php echo getValue($current_user, 'addressline1'); ?>"
                                            placeholder="Address">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="zipcode" class="form-label">
                                        <i class="fas fa-map-pin icon"></i> Zip Code
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="zipcode" name="zipcode" class="form-control"
                                            value="<?php echo getValue($current_user, 'zipcode'); ?>"
                                            placeholder="Zip Code">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <h3>Employment Information</h3>
                <div class="post-col" style="width:100% !important">
                    <!-- Post Section -->
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="work_status" class="form-label"><i class="fas fa-briefcase icon"></i>
                                        Employment Status</label>
                                    <div class="nk-int-st">
                                        <select id="work_status" name="work_status" class="form-control selectpicker">
                                            <option>Select Status</option>
                                            <option value="Employed" <?php echo (getValue($current_user, 'work_status') == 'Employed') ? 'selected' : ''; ?>>Employed</option>
                                            <option value="Unemployed" <?php echo (getValue($current_user, 'work_status') == 'Unemployed') ? 'selected' : ''; ?>>Unemployed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="first_employment_date" class="form-label"><i
                                            class="fas fa-calendar-alt icon"></i> First Employment Date</label>
                                    <div class="nk-datapk-ctm form-elet-mg" id="data_1">
                                        <div class="input-group date nk-int-st">
                                            <span class="input-group-addon"></span>
                                            <input type="date" class="form-control" name="first_employment_date"
                                                value="<?php echo getValue($current_user, 'first_employment_date'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="date_for_current_employment" class="form-label"><i
                                            class="fas fa-calendar-day icon"></i> Current Employment Date</label>
                                    <div class="nk-datapk-ctm" id="data_1">
                                        <div class="input-group date nk-int-st">
                                            <span class="input-group-addon"></span>
                                            <input type="date" class="form-control" name="date_for_current_employment"
                                                value="<?php echo getValue($current_user, 'date_for_current_employment'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="company_name" class="form-label"><i class="fas fa-building icon"></i>
                                        Company Name</label>
                                    <div class="nk-int-st">
                                        <input type="text" id="company_name" name="name_company" class="form-control"
                                            value="<?php echo getValue($current_user, 'name_company'); ?>"
                                            placeholder="Company Name">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="employment_location" class="form-label"><i
                                            class="fas fa-map-marker-alt icon"></i> Employment Location</label>
                                    <div class="nk-int-st">
                                        <input type="text" id="employment_location" name="employment_location"
                                            class="form-control"
                                            value="<?php echo getValue($current_user, 'employment_location'); ?>"
                                            placeholder="Employment Location">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="type_of_work" class="form-label"><i class="fas fa-tools icon"></i> Type
                                        of Work</label>
                                    <div class="nk-int-st">
                                        <input type="text" id="type_of_work" name="type_of_work" class="form-control"
                                            value="<?php echo getValue($current_user, 'type_of_work'); ?>"
                                            placeholder="Type of Work">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="work_position" class="form-label"><i class="fas fa-user-tie icon"></i>
                                        Position</label>
                                    <div class="nk-int-st">
                                        <input type="text" id="work_position" name="work_position" class="form-control"
                                            value="<?php echo getValue($current_user, 'work_position'); ?>"
                                            placeholder="Position">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="monthly_income" class="form-label"><i
                                            class="fas fa-dollar-sign icon"></i> Monthly Income</label>
                                    <div class="nk-int-st">
                                        <input type="text" id="monthly_income" name="current_monthly_income"
                                            class="form-control"
                                            value="<?php echo getValue($current_user, 'current_monthly_income'); ?>"
                                            placeholder="Monthly Income">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="job_satisfaction" class="form-label"><i class="fas fa-smile icon"></i>
                                        Job Satisfaction</label>
                                    <div class="nk-int-st">
                                        <select id="job_satisfaction" name="job_satisfaction"
                                            class="form-control selectpicker">
                                            <option value="Very Satisfied" <?php echo (getValue($current_user, 'job_satisfaction') == 'Very Satisfied') ? 'selected' : ''; ?>>Very
                                                Satisfied</option>
                                            <option value="Satisfied" <?php echo (getValue($current_user, 'job_satisfaction') == 'Satisfied') ? 'selected' : ''; ?>>Satisfied
                                            </option>
                                            <option value="Neutral" <?php echo (getValue($current_user, 'job_satisfaction') == 'Neutral') ? 'selected' : ''; ?>>Neutral</option>
                                            <option value="Dissatisfied" <?php echo (getValue($current_user, 'job_satisfaction') == 'Dissatisfied') ? 'selected' : ''; ?>>Dissatisfied
                                            </option>
                                            <option value="Very Dissatisfied" <?php echo (getValue($current_user, 'job_satisfaction') == 'Very Dissatisfied') ? 'selected' : ''; ?>>Very
                                                Dissatisfied</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="related_to_course" class="form-label"><i
                                            class="fas fa-graduation-cap icon"></i> Related to Course of Study?</label>
                                    <div class="nk-int-st">
                                        <select id="related_to_course" name="work_related"
                                            class="form-control selectpicker">
                                            <option value="Yes" <?php echo (getValue($current_user, 'related_to_course') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                            <option value="No" <?php echo (getValue($current_user, 'related_to_course') == 'No') ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="work_classification" class="form-label"><i class="fas fa-tags icon"></i>
                                        Work Classification</label>
                                    <div class="nk-int-st">
                                        <select id="work_classification" name="work_classification"
                                            class="form-control selectpicker">
                                            <option
                                                value="<?php echo htmlspecialchars($_SESSION['user']['category_id']); ?>">
                                                <?php echo htmlspecialchars($_SESSION['user']['category']); ?>
                                            </option>
                                            <?php
                                            if (!empty($categories) && is_array($categories)) {
                                                foreach ($categories as $categoryId => $categoryDetails) {
                                                    $categoryName = isset($categoryDetails['category_name']) ? htmlspecialchars($categoryDetails['category_name']) : 'Unknown';
                                                    echo "<option value=\"" . htmlspecialchars($categoryId) . "\">" . $categoryName . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <h3>Education Information</h3>
                <div class="post-col" style="width:100% !important">
                    <!-- Post Section -->
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="course" class="form-label">
                                        <i class="fa fa-book icon"></i> Course
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="course" name="course" class="form-control selectpicker">
                                            <option
                                                value="<?php echo htmlspecialchars($_SESSION['user']['course_id']); ?>">
                                                <?php echo htmlspecialchars($_SESSION['user']['course']); ?>
                                            </option>
                                            <?php
                                            if (is_array($data)) {
                                                foreach ($data as $courseId => $details) {
                                                    $courseCode = isset($details['courCode']) ? htmlspecialchars($details['courCode']) : 'Unknown';
                                                    echo "<option value=\"" . htmlspecialchars($courseId) . "\">" . $courseCode . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="major" class="form-label">
                                        <i class="fa fa-graduation-cap icon"></i> Major
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="major" class="form-control" name="major"
                                            value="<?php echo getValue($current_user, 'major'); ?>" placeholder="Major">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="batch" class="form-label">
                                        <i class="fa fa-calendar-alt icon"></i> Batch
                                    </label>
                                    <div class="nk-int-st">
                                        <select id="batch" name="batch" class="form-control selectpicker">
                                            <option
                                                value="<?php echo htmlspecialchars($_SESSION['user']['batch_id']); ?>">
                                                <?php echo htmlspecialchars($_SESSION['user']['batch']); ?>
                                            </option>
                                            <?php
                                            if (!empty($batchYears) && is_array($batchYears)) {
                                                foreach ($batchYears as $batchId => $batchDetails) {
                                                    $batchYear = isset($batchDetails['batch_yrs']) ? htmlspecialchars($batchDetails['batch_yrs']) : 'Unknown';
                                                    echo "<option value=\"" . htmlspecialchars($batchId) . "\">" . $batchYear . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="graduation_year" class="form-label">
                                        <i class="fa fa-calendar icon"></i> Year of Graduation
                                    </label>
                                    <div class="form-group nk-datapk-ctm form-elet-mg" id="data_1">
                                        <div class="input-group date nk-int-st">
                                            <span class="input-group-addon"></span>
                                            <input type="text" class="form-control" name="graduation_year"
                                                value="<?php echo getValue($current_user, 'graduation_year'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Picture Section -->
                <h3>Profile Picture</h3>
                <div class="post-col" style="width:100% !important">
                    <div class="post-container">
                        <center>
                            <div class="profile-image-container">
                                <input type="file" id="profileImageInput" name="profile_url" accept="image/*"
                                    style="display: none;">

                                <img id="profileImage" src="<?php echo getValue($current_user, 'profile_url'); ?>"
                                    alt="Profile Picture"
                                    onerror="if (this.src != 'uploads/profile.jpg') this.src = 'uploads/profile.jpg';">


                                <div class="overlay">
                                    <span>Change Image</span>
                                </div>
                            </div>
                        </center>
                    </div>
                </div>

                <!-- Cover Photo Section -->
                <h3>Cover Photo</h3>
                <div class="post-col" style="width:100% !important">
                    <div class="post-container">
                        <div class="cover-photo-container">
                            <input type="file" id="coverPhotoInput" name="cover_photo_url" accept="image/*"
                                style="display: none;">


                            <img id="coverPhoto" src="<?php echo getValue($current_user, 'cover_photo_url'); ?>"
                                alt="Profile Picture" class="cover-photo"
                                onerror="if (this.src != 'img/dafault_cover.jpg') this.src = 'img/dafault_cover.jpg';">

                            <div class="cover-overlay">
                                <span>Change Cover</span>
                            </div>
                        </div>
                    </div>
                </div>


                <h3>Type your Bio</h3>
                <div class="post-col" style="width:100% !important">
                    <!-- Post Section -->
                    <div class="post-container">
                        <textarea id="editor1" name="bio" rows="10"
                            cols="80">     <?php echo htmlspecialchars(getValue($current_user, 'bio')); ?></textarea>
                        <div class="char-count">
                            <span id="charCount">0</span> / 101 characters
                        </div>
                        <div class="error" id="charError" style="display: none;">
                            Bio cannot exceed 101 characters.
                        </div>
                    </div>
                </div>
                <button type="submit" style="float:right" class="btn btn-primary" onclick="submitForm()">Update
                    Profile</button>

            </div>
        </form>

    </div>

    <?php include 'global_chatbox.php' ?>


</body>

</html>
<script src="../bower_components/ckeditor/ckeditor.js"></script>


<script>
    var maxLength = 101;

    // Initialize CKEditor
    CKEDITOR.replace('editor1', {
        on: {
            instanceReady: function (evt) {
                updateCharCount();
            }
        }
    });

    // Function to update character count and limit input
    function updateCharCount() {
        var editor = CKEDITOR.instances.editor1;
        var content = editor.getData();
        var text = content.replace(/<[^>]*>/g, ''); // Strip HTML tags
        var currentLength = text.length;

        document.getElementById('charCount').textContent = currentLength;

        if (currentLength > maxLength) {
            var truncatedText = text.slice(0, maxLength);
            editor.setData(truncatedText);
            document.getElementById('charError').style.display = 'block';
        } else {
            document.getElementById('charError').style.display = 'none';
        }
    }

    // Update character count on CKEditor instance change
    CKEDITOR.instances.editor1.on('change', function () {
        updateCharCount();
    });

    // Prevent further input when limit is reached
    CKEDITOR.instances.editor1.on('key', function (evt) {
        var editor = evt.editor;
        var content = editor.getData();
        var text = content.replace(/<[^>]*>/g, '');

        if (text.length >= maxLength && evt.data.keyCode != 8 && evt.data.keyCode != 46) {
            evt.cancel();
        }
    });

    // Sync CKEditor content with the underlying <textarea> elements before form submission
    function submitForm() {
        for (var instanceName in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
                CKEDITOR.instances[instanceName].updateElement();
            }
        }
        return true;
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const regionSelect = document.getElementById('regionSelect');
        const provinceSelect = document.getElementById('provinceSelect');
        const citySelect = document.getElementById('citySelect');
        const barangaySelect = document.getElementById('barangaySelect');
        let regionsData = [];
        let provincesData = [];
        let municipalitiesData = [];
        let barangaysData = [];

        // Fetch the JSON data for regions
        fetch('json/regions.json')
            .then(response => response.json())
            .then(data => {
                regionsData = data;
                populateRegions();
            })
            .catch(error => {
                console.error('Error loading regions:', error);
                regionSelect.innerHTML = '<option value="">Error loading regions</option>';
                $(regionSelect).selectpicker('refresh');
            });

        // Fetch the JSON data for provinces
        fetch('json/provinces.json')
            .then(response => response.json())
            .then(data => {
                provincesData = data;
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
            });

        // Fetch the JSON data for municipalities
        fetch('json/municipalities.json')
            .then(response => response.json())
            .then(data => {
                municipalitiesData = data;
            })
            .catch(error => {
                console.error('Error loading municipalities:', error);
            });

        // Fetch the JSON data for barangays
        fetch('json/barangays.json')
            .then(response => response.json())
            .then(data => {
                barangaysData = data;
            })
            .catch(error => {
                console.error('Error loading barangays:', error);
            });

        function populateRegions() {
            regionSelect.innerHTML = '<option value="">Select a region</option>';
            regionsData.forEach(region => {
                const option = document.createElement('option');
                option.value = region.designation;
                option.textContent = `${region.designation} - ${region.name}`;
                regionSelect.appendChild(option);
            });
            $(regionSelect).selectpicker('refresh');
        }

        function populateProvinces(regionDesignation) {
            provinceSelect.innerHTML = '<option value="">Select a province</option>';
            const filteredProvinces = provincesData.filter(province => province.region === regionDesignation);

            let currentStateSelected = false;

            filteredProvinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;

                if (province.name === '<?php echo $current_state; ?>') {
                    option.selected = true;
                    currentStateSelected = true;
                }

                provinceSelect.appendChild(option);
            });

            if (!currentStateSelected && '<?php echo $current_state; ?>' !== '') {
                const option = document.createElement('option');
                option.value = '<?php echo $current_state; ?>';
                option.textContent = '<?php echo $current_state; ?>';
                option.selected = true;
                provinceSelect.appendChild(option);
            }

            $(provinceSelect).selectpicker('refresh');

        }

        function populateMunicipalities(provinceName) {
            citySelect.innerHTML = '<option value="">Select a city/municipality</option>';
            const filteredMunicipalities = municipalitiesData.filter(municipality => municipality.province === provinceName);

            let currentCitySelected = false;

            filteredMunicipalities.forEach(municipality => {
                const option = document.createElement('option');
                option.value = municipality.name;
                option.textContent = municipality.name;

                if (municipality.name === '<?php echo $current_city; ?>') {
                    option.selected = true;
                    currentCitySelected = true;
                }

                citySelect.appendChild(option);
            });

            // If the current city is not in the list of municipalities for the selected province,
            // add it as an option
            if (!currentCitySelected && '<?php echo $current_city; ?>' !== '') {
                const option = document.createElement('option');
                option.value = '<?php echo $current_city; ?>';
                option.textContent = '<?php echo $current_city; ?>';
                option.selected = true;
                citySelect.appendChild(option);
            }

            $(citySelect).selectpicker('refresh');
        }

        function populateBarangays(cityName) {
            barangaySelect.innerHTML = '<option value="">Select a barangay</option>';
            const filteredBarangays = barangaysData.filter(barangay => barangay.citymun === cityName);

            let currentBarangaySelected = false;

            filteredBarangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.name;
                option.textContent = barangay.name;

                if (barangay.name === '<?php echo $current_barangay; ?>') {
                    option.selected = true;
                    currentBarangaySelected = true;
                }

                barangaySelect.appendChild(option);
            });

            // If the current barangay is not in the list of barangays for the selected city,
            // add it as an option
            if (!currentBarangaySelected && '<?php echo $current_barangay; ?>' !== '') {
                const option = document.createElement('option');
                option.value = '<?php echo $current_barangay; ?>';
                option.textContent = '<?php echo $current_barangay; ?>';
                option.selected = true;
                barangaySelect.appendChild(option);
            }

            $(barangaySelect).selectpicker('refresh');
        }

        regionSelect.addEventListener('change', (event) => {
            const selectedRegion = event.target.value;
            provinceSelect.innerHTML = '<option value="">Select a province</option>';
            citySelect.innerHTML = '<option value="">Select a province first</option>';
            barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';

            if (selectedRegion) {
                populateProvinces(selectedRegion);
            } else {
                provinceSelect.innerHTML = '<option value="">Select a region first</option>';
                citySelect.innerHTML = '<option value="">Select a province first</option>';
                barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';
            }

            $(provinceSelect).selectpicker('refresh');
            $(citySelect).selectpicker('refresh');
            $(barangaySelect).selectpicker('refresh');
        });

        provinceSelect.addEventListener('change', (event) => {
            const selectedProvince = event.target.value;
            citySelect.innerHTML = '<option value="">Select a city/municipality</option>';
            barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';

            if (selectedProvince) {
                populateMunicipalities(selectedProvince);
            } else {
                citySelect.innerHTML = '<option value="">Select a province first</option>';
                barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';
            }

            $(citySelect).selectpicker('refresh');
            $(barangaySelect).selectpicker('refresh');
        });

        citySelect.addEventListener('change', (event) => {
            const selectedCity = event.target.value;
            barangaySelect.innerHTML = '<option value="">Select a barangay</option>';

            if (selectedCity) {
                populateBarangays(selectedCity);
            } else {
                barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';
            }

            $(barangaySelect).selectpicker('refresh');
        });

        barangaySelect.addEventListener('change', (event) => {
            console.log('Selected barangay:', event.target.value);
        });
    });

</script>
<script>
    // Profile Image Change
    document.querySelector('.profile-image-container').addEventListener('click', function () {
        document.getElementById('profileImageInput').click();
    });

    document.getElementById('profileImageInput').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('profileImage').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Cover Photo Change
    document.querySelector('.cover-photo-container').addEventListener('click', function () {
        document.getElementById('coverPhotoInput').click();
    });

    document.getElementById('coverPhotoInput').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('coverPhoto').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

</script>