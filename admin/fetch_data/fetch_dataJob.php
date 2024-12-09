<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);
$data = $firebase->retrieve("job");
$data = json_decode($data, true);

if (is_array($data)) {
    foreach ($data as $id => $job) {
        // Strip HTML tags from job_description
        $job_description_plain = strip_tags($job['job_description']);

        // Prepare image HTML if image_url is available
        $image_html = '';
        if (isset($job['image_url']) && !empty($job['image_url'])) {
            $image_html = "<img src='{$job['image_url']}' alt='job Image' width='65px' height='65px'>";
        }

        // Handle status with conditions
        $status = $job['status'];
        if ($status === "Archive") {
            $status_html = '<span class="label label-danger" style="font-size: 12px !important; padding: 5px 20px !important; background: #ff5252 !important">ARCHIVE</span>';
        } else {
            $status_html = '<span class="label label-success" style="font-size: 12px !important; padding: 5px 20px !important; background: transparent !important; color:black !important; border: 1px solid black !important">ACTIVE</span>';
        }

        // Apply additional styling if work_time is "Full-Time"
        $work_time = $job['work_time'];
        if ($work_time === "Full-Time") {
            $workTime_html = '<span class="label label-danger" style="font-size: 12px !important; padding: 5px 20px !important; background: #ff5252 !important">FULL TIME</span>';
        } else {
            $workTime_html = '<span class="label label-success" style="font-size: 12px !important; padding: 5px 20px !important; background: gold !important; color:black !important; border: 1px solid black !important">PART TIME</span>';
        }

        echo "<tr>
                <td>{$status_html}</td>
                <td>{$workTime_html}</td>
                <td>{$job['job_title']}</td>
                <td>{$job['company_name']}</td>
                <td class='description-cell'>{$job_description_plain}</td>
                <td>{$job['job_created']}</td>
                <td>
                    <a class='btn btn-success btn-sm btn-flat open-modal' data-id='$id'>EDIT</a>
                    <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }
}
?>
