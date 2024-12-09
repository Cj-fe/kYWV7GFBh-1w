<?php
if (is_array($alumniData) && count($alumniData) > 0) {
    foreach ($alumniData as $id => $alumni) {
        if (!isset($alumni['forms_completed']) || $alumni['forms_completed'] !== true) {
            continue;
        }

        $courseId = $alumni['course'];
        $batchId = $alumni['batch'];
        $workStatus = $alumni['work_status'];
        $workClassification = isset($alumni['work_classification']) ? $alumni['work_classification'] : '';

        if ($filterCourse && $filterCourse != $courseId) {
            continue;
        }
        if ($filterBatch && $filterBatch != $batchId) {
            continue;
        }
        if ($filterStatus && $filterStatus != $workStatus) {
            continue;
        }
        if ($filterWorkClassification && $filterWorkClassification != $workClassification) {
            continue;
        }

        // Count based on work status
        if ($workStatus === 'Employed') {
            $status_html = '<span class="label label-success" style="font-size: 12px !important; padding: 5px 20px !important; background: #4caf50 !important">EMPLOYED</span>';
            $employedCount++;
        } elseif ($workStatus === 'Unemployed') {
            $status_html = '<span class="label label-danger" style="font-size: 12px !important; padding: 5px 20px !important; background: #ff5252 !important">UNEMPLOYED</span>';
            $unemployedCount++;
        } else {
            $status_html = '<span class="label label-default" style="font-size: 12px !important; padding: 5px 20px !important; background: #b0bec5 !important">UNKNOWN</span>';
        }
        $totalCount++;

        $batchName = isset($batchData[$batchId]['batch_yrs']) ? $batchData[$batchId]['batch_yrs'] : 'Unknown Batch';
        $courseName = isset($courseData[$courseId]['courCode']) ? $courseData[$courseId]['courCode'] : 'Unknown Course';
        $workClassificationName = isset($categoryData[$workClassification]['category_name']) ? $categoryData[$workClassification]['category_name'] : 'Unknown';

        // Add default image if no profile image is found
        $profileImage = isset($alumni['profile_url']) && !empty($alumni['profile_url']) ? $alumni['profile_url'] : 'uploads/profile.jpg';
        $image_html = "<img src='../userpage/" . htmlspecialchars($profileImage) . "' alt='Profile Image' width='65px' height='65px'>";

        echo "<tr>
            <td style='display:none;'></td>
            <td>{$image_html}</td>
            <td>" . htmlspecialchars($alumni['studentid']) . "</td>
            <td>" . htmlspecialchars($alumni['firstname']) . " " . htmlspecialchars($alumni['middlename']) . " " . htmlspecialchars($alumni['lastname']) . "</td>
            <td>" . htmlspecialchars($courseName) . "</td>
            <td>" . htmlspecialchars($batchName) . "</td>
            <td style='text-align:center;'>{$status_html}</td>
            <td>" . htmlspecialchars($workClassificationName) . "</td>
            <td>
                <a href='alumni_profile.php?id=" . htmlspecialchars($id) . "' class='btn btn-warning btn-sm btn-flat'>VIEW</a>
            </td>
        </tr>";
    }
}
?>