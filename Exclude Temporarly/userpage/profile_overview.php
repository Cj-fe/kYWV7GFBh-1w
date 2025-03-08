<?php include '../includes/session.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    <?php
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);
    $alumni_data = $firebase->retrieve("alumni");
    $alumni_data = json_decode($alumni_data, true);
    $category_data = $firebase->retrieve("category");
    $category_data = json_decode($category_data, true);
    $courseData = $firebase->retrieve("course");
    $courseData = json_decode($courseData, true);
    $batchData = $firebase->retrieve("batch_yr");
    $batchData = json_decode($batchData, true);

    function getBatchYear($batchData, $batch_id) {
        return isset($batchData[$batch_id]['batch_yrs']) ? htmlspecialchars($batchData[$batch_id]['batch_yrs']) : "Unknown Batch";
    }

    function getCourseName($courseData, $course_id) {
        return isset($courseData[$course_id]['course_name']) ? htmlspecialchars($courseData[$course_id]['course_name']) : "Unknown Course";
    }

    $current_user_id = $_SESSION['alumni_id'];
    $current_user = $alumni_data[$current_user_id] ?? null;
    $messages = json_decode($firebase->retrieve("messages"), true);
    $messages_json = json_encode($messages);

    if (!$current_user) {
        echo "User not found";
        exit;
    }

    function getValue($array, $key) {
        return isset($array[$key]) && !empty($array[$key]) ? htmlspecialchars($array[$key]) : "N/A";
    }

    function getCourseCode($courseData, $course_id) {
        return isset($courseData[$course_id]['courCode']) ? htmlspecialchars($courseData[$course_id]['courCode']) : "Unknown";
    }

    $work_classification_id = getValue($current_user, 'work_classification');
    $category_name = getCourseCode($category_data, $work_classification_id);

    function generateQRCode($batchYear, $studentId, $courseCode) {
        $data = "MCCALUMNI-{$batchYear}-{$studentId}-{$courseCode}-{$batchYear}";
        $size = 200;
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
        return $apiUrl;
    }

    $qrCodeUrl = generateQRCode(
        getBatchYear($batchData, getValue($current_user, 'batch')),
        getValue($current_user, 'studentid'),
        getCourseCode($courseData, getValue($current_user, 'course'))
    );
    ?>
    <style>
        /* Existing styles */
        .profile-info { display: grid; grid-template-columns: 1fr 1fr 1fr; grid-template-rows: auto 1fr; gap: 20px; }
        .profile-image-container { grid-column: span 3; display: flex; justify-content: center; margin-bottom: 20px; }
        .profile-image-container img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); }
        .profile-info div { background-color: #f9f9f9; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .profile-info h3 { margin-top: 0; font-size: 18px; color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 10px; }
        .profile-info p { margin: 10px 0; font-size: 16px; color: #333; }
        .profile-info p strong { font-weight: 600; color: #555; }
        .sidebar { position: fixed; top: 0; left: 0; width: 250px; height: 100%; background-color: #f4f4f4; padding: 15px; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); overflow-y: auto; }
        .sidebar h2 { margin-top: 0; }
        .sidebar ul { list-style-type: none; padding: 0; }
        .sidebar ul li { margin: 10px 0; }
        .sidebar ul li a { text-decoration: none; color: #333; font-weight: bold; }
        .profile-content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }
        .post-col { width: 100%; box-sizing: border-box; }
        .profile-section { margin-bottom: 30px; }
        /* New styles for the ID card */
        .id-card-container { display: flex; justify-content: center; gap: 20px; flex-wrap: nowrap; max-width: 1240px; padding: 20px; }
        .id-card { width: 600px; height: 340px; border-radius: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); overflow: hidden; position: relative; }
        .card-content { width: 100%; height: 100%; display: flex; justify-content: space-between; position: relative; z-index: 1; }
        .card-front, .card-back { background-color: rgba(33, 37, 51, 0.9); /* Slightly transparent background */ }
        .card-left, .card-back-left { width: 55%; padding: 20px; color: white; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        /* New styles for background image */
        .id-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('img/card_background.png'); /* Replace with your actual image path */ background-repeat: no-repeat; background-position: center; background-size: cover; /* Adjust this value to change the opacity */ z-index: 0; }
        .card-left::before, .card-back-left::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%; height: 100%; background-image: url('img/logo/school_logo.png'); background-repeat: no-repeat; background-position: center; background-size: contain; opacity: 0.1; z-index: 0; }
        .card-right, .card-back-right { width: 45%; background: #1c1e29; display: flex; justify-content: center; align-items: center; padding: 0; overflow: hidden; }
        .card-back-right { display: flex; justify-content: center; align-items: center; }
        .card-right img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .logo-card, .card-text, .contact-info, .signature { position: relative; z-index: 1; }
        .logo-card { width: 50px; height: 50px; }
        .card-text h2 { color: #F5921D; font-size: 22px; margin: 0; }
        .card-text p { margin: 5px 0; font-size: 14px; }
        .card-text h3 { margin: 10px 0; font-size: 20px; font-weight: bold; }
        .contact-info h3 { color: #F5921D; font-size: 20px; margin-bottom: 10px; }
        .signature { margin-top: 20px; }
        .signature-line { width: 100%; height: 2px; background-color: #ffffff; }
        .barcode img { width: 100%; height: auto; }
        .qr-code img { width: 100%; height: auto; max-width: 200px; max-height: 200px; }
        @media (max-width: 1260px) { .id-card { width: 100%; max-width: 600px; } }
        @media (max-width: 768px) { .profile-info { grid-template-columns: 1fr; } .profile-image-container { grid-column: 1; } }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="profile-content">
        <div id="personal-info" class="profile-section">
            <h3>Personal Info</h3>
            <div class="post-col" style="width:100% !important">
                <div class="write-post-container">
                    <div class="id-card-container">
                        <!-- Front Side -->
                        <div class="id-card">
                            <div class="card-content card-front">
                                <div class="card-left">
                                    <div class="logo-card">
                                        <img src="img/logo/alumni_logo.png" alt=" Logo">
                                    </div>
                                    <div class="card-text">
                                        <h4>MCC ALUMNI</h4>
                                        <p>Madridejos Community College, Alumni Association.</p>
                                        <h3><?php echo strtoupper(getValue($current_user, 'lastname') . ', ' . getValue($current_user, 'firstname') . ' ' . substr(getValue($current_user, 'middlename'), 0, 1) . '.'); ?> </h3>
                                        <p class="id-number"><?php echo getValue($current_user, 'studentid'); ?></p>
                                        <p><?php echo getCourseName($courseData, getValue($current_user, 'course')); ?> </p>
                                        <p>BATCH <?php echo getBatchYear($batchData, getValue($current_user, 'batch')); ?> </p>
                                    </div>
                                </div>
                                <div class="card-right">
                                    <img src="../images/no_image.png" alt="Graduate Picture" class="" onerror="if (this.src != 'uploads/profile.jpg') this.src = 'uploads/profile.jpg';">
                                </div>
                            </div>
                        </div>
                        <!-- Back Side -->
                        <div class="id-card">
                            <div class="card-content card-back">
                                <div class="card-back-left">
                                    <div class="contact-info">
                                        <h3>Contact Information</h3>
                                        <p>Madridejos Community College</p>
                                        <p>Bunakan, Madridejos, Cebu</p>
                                        <p>Contact Number: <?php echo getValue($current_user, 'contactnumber'); ?></p>
                                        <p>Email: <?php echo getValue($current_user, 'email'); ?></p>
                                    </div>
                                    <div class="signature">
                                        <p>Signature</p>
                                        <div class="signature-line"></div>
                                    </div>
                                </div>
                                <div class="qr-code">
                                    <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-container">
                    <center>
                        <div class="profile-image-container">
                            <img src="<?php echo htmlspecialchars(getValue($current_user, 'profile_url')); ?>" alt="Profile Picture" onerror="if (this.src != 'uploads/profile.jpg') this.src = 'uploads/profile.jpg';">
                        </div>
                    </center>
                    <div class="profile-info">
                        <!-- Personal Info Column -->
                        <div class="personal-info">
                            <h3>Personal Info</h3>
                            <p><strong>Name:</strong> <?php echo getValue($current_user, 'auxiliaryname') . ' ' . getValue($current_user, 'firstname') . ' ' . getValue($current_user, 'middlename') . ' ' . getValue($current_user, 'lastname'); ?> </p>
                            <p><strong>Birthday:</strong><?php $birthdate = getValue($current_user, 'birthdate'); $formatted_date = date("F j, Y", strtotime($birthdate)); echo $formatted_date; ?> </p>
                            <p><strong>Gender:</strong> <?php echo getValue($current_user, 'gender'); ?></p>
                            <p><strong>Address:</strong> <?php echo getValue($current_user, 'barangay') . ', ' . getValue($current_user, 'city') . ', ' . getValue($current_user, 'state'); ?> </p>
                            <p><strong>Civil Status:</strong> <?php echo getValue($current_user, 'civilstatus'); ?></p>
                            <p><strong>Alumni ID:</strong> <?php echo getValue($current_user, 'studentid'); ?></p>
                        </div>
                        <!-- Employment Info Column -->
                        <div class="employment-info">
                            <h3>Employment Info</h3>
                            <p><strong>Status:</strong> <span class="btn-warning notika-btn-warning" style="padding:5px 10px 5px 10px; border-radius:10px"><?php echo getValue($current_user, 'work_status'); ?></span> </p>
                            <p><strong>Company:</strong> <?php echo getValue($current_user, 'name_company'); ?></p>
                            <p><strong>Position:</strong> <?php echo getValue($current_user, 'work_position'); ?></p>
                            <p><strong>Start Date:</strong> <?php $start_date = getValue($current_user, 'date_for_current_employment'); $formatted_date = date("F j, Y", strtotime($start_date)); echo $formatted_date; ?> </p>
                            <p><strong>Work Status:</strong> <?php echo getValue($current_user, 'work_employment_status'); ?></p>
                            <p><strong>Work Classification:</strong> <?php echo $category_name; ?></p>
                            <p><strong>Monthly Income:</strong> <?php echo getValue($current_user, 'current_monthly_income'); ?></p>
                            <p><strong>Job Satisfaction:</strong> <?php echo getValue($current_user, 'job_satisfaction'); ?></p>
                        </div>
                        <!-- Contact Info Column -->
                        <div class="contact-info">
                            <h3>Contact Info</h3>
                            <p><strong>Email:</strong> <?php echo getValue($current_user, 'email'); ?></p>
                            <p><strong>Phone Number:</strong> <?php echo getValue($current_user, 'contactnumber'); ?> </p>
                            <p><strong>City:</strong> <?php echo getValue($current_user, 'city'); ?></p>
                            <p><strong>State:</strong> <?php echo getValue($current_user, 'state'); ?></p>
                            <p><strong>Zip Code:</strong> <?php echo getValue($current_user, 'zipcode'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'global_chatbox.php' ?>
</body>
</html>