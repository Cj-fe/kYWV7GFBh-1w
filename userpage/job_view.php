<?php include '../includes/session.php'; ?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'includes/header.php' ?>
    <?php
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';
    $firebase = new firebaseRDB($databaseURL);

    $data = $firebase->retrieve("job");
    $data = json_decode($data, true);

    $messages = json_decode($firebase->retrieve("messages"), true);

    // Convert messages array to JSON for JavaScript
    $messages_json = json_encode($messages);

    // Array to store active jobs
    $activeJobs = [];

    // Collect all active jobs
    foreach ($data as $id => $job) {
        if ($job['status'] === 'Active') {
            $job['id'] = $id; // Store the job ID
            $activeJobs[] = $job;
        }
    }

    // Sort active jobs by date posted (most recent first)
    usort($activeJobs, function ($a, $b) {
        return strtotime($b['job_created']) - strtotime($a['job_created']);
    });

    // Make the data available to the HTML file
    $jobsData = $activeJobs;
    ?>
    <style>
        .breadcomb-area {
            padding: 20px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .rows {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
        }

        .card p {
            color: #777;
            line-height: 1.6;
        }

        .breadcomb-area {
            padding: 20px 0;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            /* Add some bottom margin to create spacing */
        }

        .section-title::before {
            position: absolute;
            content: "";
            width: calc(100% + 80px);
            height: 2px;
            top: -10px;
            /* Move the top line higher */
            left: -40px;
            background: #06BBCC;
            z-index: -1;
        }

        .section-title::after {
            position: absolute;
            content: "";
            width: calc(100% + 120px);
            height: 2px;
            bottom: -8px;
            /* Move the bottom line lower */
            left: -60px;
            background: #06BBCC;
            z-index: 1000;
        }

        .section-title.text-start::before {
            width: calc(100% + 40px);
            left: 0;
        }

        .section-title.text-start::after {
            width: calc(100% + 60px);
            left: 0;
        }
    </style>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browsserupgrade">You are using an <strong>outdated</strong> browsser. Please <a href="http://browssehappy.com/">upgrade your browsser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Start Header Top Area -->
    <?php include 'includes/navbar.php' ?>
    <!-- End Header Top Area -->


    <!-- News items would be dynamically inserted here -->
    <div class="breadcomb-area ">
        <div class="text-center ">
            <h6 class="section-title bg-white text-center  px-3">ACTIVE JOB</h6>
            <h1 class="mb-5">ACTIVE JOB</h1>
        </div>

        <div class="container">
            <div class="rows">
                <!-- Include the PHP file to access the data -->


                <!-- Display the sorted active jobs -->
                <?php foreach ($jobsData as $job) { ?>
                    <?php
                    $jobTitle = $job['job_title'];
                    $workTime = $job['work_time'];
                    $company = $job['company_name'];
                    $jobDate = $job['job_created'];
                    $id = $job['id'];

                    // Determine background color and text color based on work time
                    $backgroundColor = ($workTime == 'Full-Time') ? '#e6f7ff' : '#fff0f5';
                    $color = ($workTime == 'Full-Time') ? '#0066cc' : '#cc0066';
                    ?>
                    <a href="visit_job.php?id=<?php echo $id; ?>">
                        <div class="card">

                            <div class="card-content">
                                <div class="job-container">
                                    <div class="job-title"><?php echo $jobTitle; ?></div>
                                    <div class="job-timesced"
                                        style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $color; ?>;">
                                        <?php echo $workTime; ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-date"><?php echo $company; ?></div>
                                <div class="card-date">Posted on <?php echo $jobDate; ?></div>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>


    <div class="breadcomb-area ">
        <div class="text-center ">
            <h6 class="section-title bg-white text-center  px-3">ARCHIVE JOB</h6>
            <h1 class="mb-5">ARCHIVE JOB</h1>
        </div>
        <div class="container">
            <div class="rows">
                <?php
                $data = $firebase->retrieve("job");
                $data = json_decode($data, true);

                function sortByDatesJob($a, $b)
                {
                    $dateA = strtotime($a['job_created']);
                    $dateB = strtotime($b['job_created']);
                    return $dateB - $dateA;
                }

                if (is_array($data)) {
                    usort($data, 'sortByDatesJob');
                    foreach ($data as $id => $job) {
                        if (isset($job['status']) && $job['status'] == 'Archive') {
                            $jobTitle = isset($job['job_title']) ? strip_tags($job['job_title']) : 'N/A';
                            $workTime = isset($job['work_time']) ? strip_tags($job['work_time']) : 'N/A';
                            $company = isset($job['company_name']) ? strip_tags($job['company_name']) : 'N/A';
                            $jobDate = isset($job['job_created']) ? strip_tags($job['job_created']) : 'N/A';

                            $backgroundColor = ($workTime == 'Full-Time') ? 'rgb(255, 105, 105)' : 'gold';
                            $color = ($workTime == 'Full-Time') ? 'white' : 'black';

                            echo '
                            <div class="card">
                                <div class="card-content">
                                    <div class="job-container">
                                        <div class="job-title">' . $jobTitle . '</div>
                                        <div class="job-timesced" style="background-color: ' . $backgroundColor . '; color: ' . $color . ';">' . $workTime . '</div>
                                    </div>
                                    <hr>
                                    <div class="card-date">' . $company . '</div>
                                    <div class="card-date">Posted on ' . $jobDate . '</div>
                                </div>
                            </div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>


    <?php include 'global_chatbox.php'?>

</body>

</html>