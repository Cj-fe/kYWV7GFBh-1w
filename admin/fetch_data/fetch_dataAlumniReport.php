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

        $batchName = isset($batchData[$batchId]['batch_yrs']) ? $batchData[$batchId]['batch_yrs'] : 'Unknown Batch';
        $courseName = isset($courseData[$courseId]['courCode']) ? $courseData[$courseId]['courCode'] : 'Unknown Course';
        $workClassificationName = isset($categoryData[$workClassification]['category_name']) ? $categoryData[$workClassification]['category_name'] : 'Unknown';

        echo "<tr>
            <td style='display:none;'><input type='checkbox' class='modal-checkbox' data-id='" . htmlspecialchars($id) . "'></td>
            <td>" . htmlspecialchars($alumni['firstname']) . "</td>
            <td>" . htmlspecialchars($alumni['middlename']) . "</td>
            <td>" . htmlspecialchars($alumni['lastname']) . "</td>
            <td>" . htmlspecialchars($courseName) . "</td>
            <td>" . htmlspecialchars($batchName) . "</td>
            <td>" . htmlspecialchars($alumni['work_status']) . "</td>
            <td>" . htmlspecialchars($alumni['date_responded']) . "</td>
            <td>
                <a class='btn btn-warning btn-sm btn-flat open-modal' data-id='" . htmlspecialchars($id) . "'>VIEW</a>
            </td>
        </tr>";
    }
}
?>