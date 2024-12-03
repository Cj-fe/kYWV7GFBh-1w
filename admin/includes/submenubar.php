
<div class="manage-alumni">
    <button type="button" class="btn-gradient-info btn-fw" id="toggle-button">
        <span class="menu-title">Manage Alumni</span>
        <i class="fa fa-cog menu-icon"></i>
    </button>
    <center>
        <ul id="dropdown-menu">
            <br>
            <li><a class="sub-side-modal" data-toggle="modal" data-target="#addDepartment"><i class="fa fa-building"></i>&nbsp; Add Department</a></li>
            <li><a class="sub-side-modal" data-toggle="modal" data-target="#course-modal"><i class="fa fa-laptop"></i>&nbsp; Add Course</a></li>
            <li><a class="sub-side-modal" data-toggle="modal" data-target="#batch-modal"><i class="fa fa-laptop"></i>&nbsp; Add Batch</a></li>
        </ul>
    </center>
</div>
<div>
    <div class="alumni-count-container">
        <span class="all-alumni"><a href="alumni.php">All Alumni</a></span>
        <div class="count"><?php echo $totalAlumniCount; ?></div>
    </div>
</div>
<hr>
<?php
// Output courses grouped by department
foreach ($filteredCourses as $departmentName => $courses) {
    echo '<button class="collapsible transparent">' . htmlspecialchars($departmentName) . '<i class="fa fa-angle-right arrow"></i></button>';
    echo '<div class="contents">';
    foreach ($courses as $course) {
        echo '<button class="collaps-department transparent" data-course-id="' . htmlspecialchars($course['courseId']) . '">' . htmlspecialchars($course['courseCode']) . '<i class="fa fa-angle-right arrow"></i></button>';
        echo '<div class="content-batch">';
        foreach ($course['batchYears'] as $batchId => $batchYear) {
            echo '<button class="collaps-year transparent" data-course-id="' . htmlspecialchars($course['courseId']) . '" data-batch-id="' . htmlspecialchars($batchId) . '">' . htmlspecialchars($batchYear) . '</button>';
        }
        echo '</div>';
    }
    echo '</div>';
}
?>
<script>
    document.querySelectorAll('.collaps-year').forEach(button => {
        button.addEventListener('click', function () {
            const courseId = this.getAttribute('data-course-id');
            const batchId = this.getAttribute('data-batch-id');
            window.location.href = `alumni.php?course=${courseId}&batch=${batchId}`;
        });
    });
</script>