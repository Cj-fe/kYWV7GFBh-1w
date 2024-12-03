<?php
include 'includes/session.php';

// Function to update admin notification timestamp
function updateAdminNotificationTimestamp($firebase)
{
    date_default_timezone_set('Asia/Manila');
    $currentTimestamp = date('Y-m-d H:i:s');

    try {
        // Prepare the updated data
        $updatedData = [
            'notification_timestamp' => $currentTimestamp
        ];

        // Update the Firebase data
        $updateResult = $firebase->update("admin", '', $updatedData);
        return $updateResult;
    } catch (Exception $e) {
        error_log("Failed to update notification timestamp: " . $e->getMessage());
        return false;
    }
}

// Function to check if a log entry is new based on notification timestamp
function isNewLogEntry($logTimestamp, $notificationTimestamp)
{
    if (empty($logTimestamp) || empty($notificationTimestamp)) {
        return false;
    }

    $logTime = strtotime($logTimestamp);
    $notificationTime = strtotime($notificationTimestamp);

    // Return true if the log time is greater than the notification time
    return $logTime > $notificationTime;
}

function getNewLabelHtml($logTimestamp, $notificationTimestamp)
{
    if (isNewLogEntry($logTimestamp, $notificationTimestamp)) {
        return '<small class="label pull-right bg-green">new</small>';
    }
    return '';
}

function sortLogsByTimestamp(&$groupedLogs)
{
    krsort($groupedLogs);

    foreach ($groupedLogs as $date => &$logs) {
        uasort($logs, function ($a, $b) {
            // Determine the latest timestamp for each log entry
            $aLatestTimestamp = isset($a['timestamps']) ? end($a['timestamps']) : $a['timestamp'];
            $bLatestTimestamp = isset($b['timestamps']) ? end($b['timestamps']) : $b['timestamp'];

            // Sort by the latest timestamp in descending order
            return strtotime($bLatestTimestamp) - strtotime($aLatestTimestamp);
        });

        // Sort timestamps within login attempts in descending order
        foreach ($logs as $key => &$logData) {
            if (isset($logData['type']) && $logData['type'] === 'login_attempt') {
                if (isset($logData['timestamps'])) {
                    usort($logData['timestamps'], function ($a, $b) {
                        return strtotime($b) - strtotime($a);
                    });
                }
            }
        }
    }
}

if (!isset($_SESSION['refresh_count'])) {
    $_SESSION['refresh_count'] = 0;
}

$updateTimestampResult = updateAdminNotificationTimestamp($firebase);

// Retrieve the selected date range from the form
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Function to filter logs by date range
function filterLogsByDateRange($logs, $startDate, $endDate)
{
    $filteredLogs = [];

    foreach ($logs as $key => $log) {
        $logDate = date('Y-m-d', strtotime($log['timestamp']));

        if ($logDate >= $startDate && $logDate <= $endDate) {
            $filteredLogs[$key] = $log;
        }
    }

    return $filteredLogs;
}

// Retrieve logs from Firebase
$logsData = $firebase->retrieve("logs");
$survey = json_decode($logsData, true) ?: [];

// Filter logs if date range is set
if ($startDate && $endDate) {
    $survey = filterLogsByDateRange($survey, $startDate, $endDate);
}

// Group and sort logs
$groupedLogs = [];
foreach ($survey as $key => $log) {
    $logType = $log['type'] ?? 'login_attempt';
    $date = date('Y-m-d', strtotime($log['timestamp']));

    if (!isset($groupedLogs[$date])) {
        $groupedLogs[$date] = [];
    }

    if ($logType === 'login_attempt') {
        $ip = $log['ip'] ?? 'N/A';
        if (!isset($groupedLogs[$date][$ip])) {
            $groupedLogs[$date][$ip] = [
                'attempts' => 0,
                'username' => $log['username'] ?? 'Unknown',
                'timestamps' => [],
                'latitude' => $log['latitude'] ?? 'N/A',
                'longitude' => $log['longitude'] ?? 'N/A',
                'device' => $log['device'] ?? 'Unknown Device',
                'browser' => $log['browser'] ?? 'Unknown Browser',
                'type' => 'login_attempt'
            ];
        }

        $groupedLogs[$date][$ip]['attempts'] += $log['attempts'] ?? 1;
        $groupedLogs[$date][$ip]['timestamps'][] = $log['timestamp'];
    } elseif ($logType === 'admin_profile_update') {
        $updateKey = 'profile_update_' . $key;
        $groupedLogs[$date][$updateKey] = [
            'content' => $log['content'] ?? 'Profile Update',
            'username' => $log['username'] ?? 'Admin',
            'timestamp' => $log['timestamp'],
            'type' => 'admin_profile_update'
        ];
    } elseif ($logType === 'gmail_code_2fa_request') {
        $verificationKey = '2fa_request_' . $key;
        $groupedLogs[$date][$verificationKey] = [
            'content' => $log['content'] ?? 'Verification Code Request',
            'email' => $log['email'] ?? 'N/A',
            'status' => $log['status'] ?? 'N/A',
            'timestamp' => $log['timestamp'],
            'type' => 'gmail_code_2fa_request'
        ];
    } elseif ($logType === 'number_code_2fa_verification') {
        $verificationKey = 'number_2fa_' . $key;
        $groupedLogs[$date][$verificationKey] = [
            'content' => $log['content'] ?? 'Number Verification',
            'email' => $log['email'] ?? 'N/A',
            'status' => $log['status'] ?? 'N/A',
            'timestamp' => $log['timestamp'],
            'type' => 'number_code_2fa_verification'
        ];
    } elseif ($logType === 'forgot_password') {
        $forgotPasswordKey = 'forgot_password_' . $key;
        $groupedLogs[$date][$forgotPasswordKey] = [
            'content' => $log['content'] ?? 'Forgot Password Reset Link',
            'email' => $log['email'] ?? 'N/A',
            'status' => $log['status'] ?? 'N/A',
            'timestamp' => $log['timestamp'],
            'type' => 'forgot_password'
        ];
    } elseif ($logType === 'password_reset') {
        $passwordResetKey = 'password_reset_' . $key;
        $groupedLogs[$date][$passwordResetKey] = [
            'content' => $log['content'] ?? 'You update your forgot password',
            'status' => $log['status'] ?? 'N/A',
            'timestamp' => $log['timestamp'],
            'type' => 'password_reset'
        ];
    }
}

sortLogsByTimestamp($groupedLogs);

?>

<!DOCTYPE html>
<html>

<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .timeline-header .label.bg-green {
            font-size: 10px;
            padding: 3px 5px;
            margin-left: 5px;
            position: relative;
            top: -2px;
        }

        .date-filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .date-filter-form label {
            margin: 0 5px;
        }

        .date-filter-form input {
            padding: 5px;
            width: 150px;
        }

        .date-filter-form button {
            padding: 5px 10px;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header box-header-background">
                <h1>Account Activity</h1>
            </section>

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
                            <div class="box-body" style="padding:30px;">
                                <div class="col-md-12">
                                    <form method="GET" action="" class="date-filter-form">
                                        <label for="start_date">Start Date:</label>
                                        <input type="text" id="start_date" name="start_date" class="date-picker"
                                            required>

                                        <label for="end_date">End Date:</label>
                                        <input type="text" id="end_date" name="end_date" class="date-picker" required>

                                        <button type="submit">Filter</button>
                                    </form>
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#timeline" data-toggle="tab">Activity
                                                    Timeline</a></li>
                                            
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="timeline">
                                                <ul class="timeline timeline-inverse">
                                                    <?php foreach ($groupedLogs as $date => $logs): ?>
                                                        <li class="time-label">
                                                            <span class="bg-red">
                                                                <?php echo date('j M. Y', strtotime($date)); ?>
                                                            </span>
                                                        </li>

                                                        <?php foreach ($logs as $key => $logData): ?>
                                                            <?php if (!isset($logData['type']) || $logData['type'] === 'login_attempt'): ?>
                                                                <?php foreach ($logData['timestamps'] as $timestamp): ?>
                                                                    <?php
                                                                    $newLabel = getNewLabelHtml($timestamp, $user['notification_timestamp']);
                                                                    $latitude = $logData['latitude'] ?? 'N/A';
                                                                    $longitude = $logData['longitude'] ?? 'N/A';
                                                                    $device = $logData['device'] ?? 'Unknown Device';
                                                                    $browser = $logData['browser'] ?? 'Unknown Browser';
                                                                    ?>
                                                                    <li>
                                                                        <i class="fa fa-warning bg-red"></i>
                                                                        <div class="timeline-item">
                                                                            <span class="time">
                                                                                <i class="fa fa-clock-o"></i>
                                                                                <?php echo date('H:i', strtotime($timestamp)); ?>
                                                                            </span>
                                                                            <h4 class="timeline-header">
                                                                                Login Attempt Detected <?php echo $newLabel; ?>
                                                                            </h4>
                                                                            <div class="timeline-body">
                                                                                IP: <?php echo htmlspecialchars($key); ?><br>
                                                                                Username: <?php echo htmlspecialchars($logData['username']); ?><br>
                                                                                Timestamp: <?php echo date('H:i:s', strtotime($timestamp)); ?><br>
                                                                                Latitude: <?php echo htmlspecialchars($latitude); ?><br>
                                                                                Longitude: <?php echo htmlspecialchars($longitude); ?><br>
                                                                                Device: <?php echo htmlspecialchars($device); ?><br>
                                                                                Browser: <?php echo htmlspecialchars($browser); ?>
                                                                            </div>
                                                                            <div class="timeline-footer">
                                                                                <a class="btn btn-primary btn-xs"
                                                                                    href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($latitude . ',' . $longitude); ?>"
                                                                                    target="_blank">Trace Location</a>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($logData['type'] === 'admin_profile_update'): ?>
                                                                <li>
                                                                    <i class="fa fa-user bg-blue"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <?php echo date('H:i', strtotime($logData['timestamp'])); ?>
                                                                        </span>
                                                                        <h3 class="timeline-header">
                                                                            Profile Update <?php echo getNewLabelHtml($logData['timestamp'], $user['notification_timestamp']); ?>
                                                                        </h3>
                                                                        <div class="timeline-body">
                                                                            User:
                                                                            <?php echo htmlspecialchars($logData['username']); ?><br>
                                                                            Update:
                                                                            <?php echo htmlspecialchars($logData['content']); ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php elseif ($logData['type'] === 'gmail_code_2fa_request'): ?>
                                                                <li>
                                                                    <i class="fa fa-key bg-yellow"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <?php echo date('H:i', strtotime($logData['timestamp'])); ?>
                                                                        </span>
                                                                        <h3 class="timeline-header">
                                                                            Gmail Verification 2FA <?php echo getNewLabelHtml($logData['timestamp'], $user['notification_timestamp']); ?>
                                                                        </h3>
                                                                        <div class="timeline-body">
                                                                            Email:
                                                                            <?php echo htmlspecialchars($logData['email']); ?><br>
                                                                            Status: <span
                                                                                class="label <?php echo $logData['status'] === 'success' ? 'label-success' : 'label-danger'; ?>">
                                                                                <?php echo htmlspecialchars(ucfirst($logData['status'])); ?>
                                                                            </span><br>
                                                                            Details:
                                                                            <?php echo htmlspecialchars($logData['content']); ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php elseif ($logData['type'] === 'number_code_2fa_verification'): ?>
                                                                <li>
                                                                    <i class="fa fa-check-circle bg-green"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <?php echo date('H:i', strtotime($logData['timestamp'])); ?>
                                                                        </span>
                                                                        <h3 class="timeline-header">
                                                                            Number Verification 2FA <?php echo getNewLabelHtml($logData['timestamp'], $user['notification_timestamp']); ?>
                                                                        </h3>
                                                                        <div class="timeline-body">
                                                                            Email:
                                                                            <?php echo htmlspecialchars($logData['email']); ?><br>
                                                                            Status: <span
                                                                                class="label <?php echo $logData['status'] === 'success' ? 'label-success' : 'label-danger'; ?>">
                                                                                <?php echo htmlspecialchars(ucfirst($logData['status'])); ?>
                                                                            </span><br>
                                                                            Details:
                                                                            <?php echo htmlspecialchars($logData['content']); ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php elseif ($logData['type'] === 'forgot_password'): ?>
                                                                <li>
                                                                    <i class="fa fa-unlock-alt bg-purple"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <?php echo date('H:i', strtotime($logData['timestamp'])); ?>
                                                                        </span>
                                                                        <h3 class="timeline-header">
                                                                            Forgot Password Reset Link <?php echo getNewLabelHtml($logData['timestamp'], $user['notification_timestamp']); ?>
                                                                        </h3>
                                                                        <div class="timeline-body">
                                                                            Email:
                                                                            <?php echo htmlspecialchars($logData['email']); ?><br>
                                                                            Status: <span
                                                                                class="label <?php echo $logData['status'] === 'success' ? 'label-success' : 'label-danger'; ?>">
                                                                                <?php echo htmlspecialchars(ucfirst($logData['status'])); ?>
                                                                            </span><br>
                                                                            Details:
                                                                            <?php echo htmlspecialchars($logData['content']); ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php elseif ($logData['type'] === 'password_reset'): ?>
                                                                <li>
                                                                    <i class="fa fa-refresh bg-orange"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <?php echo date('H:i', strtotime($logData['timestamp'])); ?>
                                                                        </span>
                                                                        <h3 class="timeline-header">
                                                                            Password Reset Updated <?php echo getNewLabelHtml($logData['timestamp'], $user['notification_timestamp']); ?>
                                                                        </h3>
                                                                        <div class="timeline-body">
                                                                            Status: <span
                                                                                class="label <?php echo $logData['status'] === 'success' ? 'label-success' : 'label-danger'; ?>">
                                                                                <?php echo htmlspecialchars(ucfirst($logData['status'])); ?>
                                                                            </span><br>
                                                                            Details:
                                                                            <?php echo htmlspecialchars($logData['content']); ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>

                                                    <li>
                                                        <i class="fa fa-clock-o bg-gray"></i>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr on the date inputs
        flatpickr(".date-picker", {
            dateFormat: "Y-m-d", // Set the date format
            altInput: true, // Show an alternative input with a more readable date format
            altFormat: "F j, Y", // Format for the alternative input
            allowInput: true // Allow manual input
        });
    </script>
</body>

</html>