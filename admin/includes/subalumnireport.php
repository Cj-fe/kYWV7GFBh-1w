<?php
// Fetch courses, departments, batch years, and alumni data from Firebase
$courseDataFromFirebase = $firebase->retrieve("course");
$batchYearDataFromFirebase = $firebase->retrieve("batch_yr");
$alumniDataFromFirebase = $firebase->retrieve("alumni");

// Decode JSON data into associative arrays
$decodedCourses = json_decode($courseDataFromFirebase, true) ?: [];
$decodedBatchYears = json_decode($batchYearDataFromFirebase, true) ?: [];
$decodedAlumni = json_decode($alumniDataFromFirebase, true) ?: [];

// Prepare an array to store alumni grouped by batch year
$groupedAlumniByBatch = [];

// Count the total number of alumni with forms_completed set to true
$completedFormsAlumniCount = 0;

// Iterate through alumni and group by batch year
foreach ($decodedAlumni as $individualAlumniId => $individualAlumniDetails) {
    // Check if forms_completed is true
    if (isset($individualAlumniDetails['forms_completed']) && $individualAlumniDetails['forms_completed']) {
        $completedFormsAlumniCount++;
        $currentBatchId = $individualAlumniDetails['batch'];
        $currentCourseId = $individualAlumniDetails['course'];

        // Initialize the batch year array if it doesn't exist
        if (!isset($groupedAlumniByBatch[$currentBatchId])) {
            $groupedAlumniByBatch[$currentBatchId] = [];
        }

        // Add the course to the batch year if not already added
        if (!in_array($currentCourseId, $groupedAlumniByBatch[$currentBatchId])) {
            $groupedAlumniByBatch[$currentBatchId][] = $currentCourseId;
        }
    }
}
?>

<div>
    <div class="alumni-count-container" style="padding-top: 20px">
        <span class="all-alumni"><a href="alumni_report.php">All Respondent</a></span>
        <div class="count"><?php echo $completedFormsAlumniCount; ?></div>
    </div>
</div>
<hr>
<?php
// Output alumni grouped by batch year
foreach ($groupedAlumniByBatch as $currentBatchId => $courseIdList) {
    $currentBatchYear = isset($decodedBatchYears[$currentBatchId]['batch_yrs']) ? $decodedBatchYears[$currentBatchId]['batch_yrs'] : 'Unknown Batch Year';
    echo '<button class="collapsible transparent">'. 'BATCH' . ' ' . htmlspecialchars($currentBatchYear) . '<i class="fa fa-angle-right arrow"></i></button>';
    echo '<div class="contents">';
    // Output courses within the batch year
    foreach ($courseIdList as $currentCourseId) {
        if (isset($decodedCourses[$currentCourseId])) {
            $currentCourseCode = $decodedCourses[$currentCourseId]['courCode'];
            $currentCourseName = $decodedCourses[$currentCourseId]['course_name'];
            echo '<button class="collaps-department transparent" data-course-id="' . htmlspecialchars($currentCourseId) . '" data-batch-id="' . htmlspecialchars($currentBatchId) . '">' . htmlspecialchars($currentCourseCode) . '</button>';
        }
    }
    echo '</div>';
}
?>
<script>
document.querySelectorAll('.collaps-department').forEach(button => {
    button.addEventListener('click', function () {
        const courseId = this.getAttribute('data-course-id');
        const batchId = this.getAttribute('data-batch-id');
        window.location.href = `alumni_report.php?course=${courseId}&batch=${batchId}`;
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function closeAllExcept(current, className) {
        var coll = document.getElementsByClassName(className);
        for (var i = 0; i < coll.length; i++) {
            if (coll[i] !== current) {
                coll[i].classList.remove("active");
                var content = coll[i].nextElementSibling;
                if (content) {
                    content.classList.remove("active");
                }
            }
        }
    }
    function toggleContent(className) {
        var coll = document.getElementsByClassName(className);
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function () {
                // Close all other collapsibles of the same class
                closeAllExcept(this, className);
                // Toggle the clicked collapsible
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content) {
                    content.classList.toggle("active");
                }
            });
        }
    }
    toggleContent("collapsible");
});
</script>