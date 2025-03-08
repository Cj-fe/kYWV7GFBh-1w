<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);
$data = $firebase->retrieve("deleted_job");
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

        // Convert the timestamp to readable format
        $date = new DateTime($job['deleted_at']);
        $formatted_date = $date->format('F j, Y H:i:s');

        // Escape job title for JS safety
        $escaped_title = htmlspecialchars($job['job_title'], ENT_QUOTES, 'UTF-8');

        echo "<tr>
                <td>{$status_html}</td>
                <td>{$workTime_html}</td>
                <td>{$job['job_title']}</td>
                <td>{$job['company_name']}</td>
                <td class='description-cell'>{$job_description_plain}</td>
                <td>{$formatted_date}</td>
                <td>
                    <a class='btn btn-info btn-sm btn-flat open-restore' 
                       data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "' 
                       data-title='" . $escaped_title . "'>
                         RESTORE
                    </a>
                    <a class='btn btn-danger btn-sm btn-flat open-delete' 
                       data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "' 
                       data-title='" . $escaped_title . "'>
                        <i class='fa fa-trash'></i> DELETE
                    </a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No archived jobs found</td></tr>";
}
?>