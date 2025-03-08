<?php

// Fetch data with unique variable names
$academicProgramsRaw = $firebase->retrieve("course");
$graduationYearsRaw = $firebase->retrieve("batch_yr");
$graduatesDataRaw = $firebase->retrieve("alumni");

// Decode JSON with distinctive names
$academicPrograms = json_decode($academicProgramsRaw, true) ?: [];
$graduationYears = json_decode($graduationYearsRaw, true) ?: [];
$graduatesRegistry = json_decode($graduatesDataRaw, true) ?: [];

// Initialize tracking arrays
$graduatesByClassYear = [];
$completedFormsCount = 0;

// Process graduate data
foreach ($graduatesRegistry as $registryId => $graduateProfile) {
    if (isset($graduateProfile['forms_completed']) && $graduateProfile['forms_completed']) {
        $completedFormsCount++;

        $classYearId = $graduateProfile['batch'];
        $programId = $graduateProfile['course'];

        if (!isset($graduatesByClassYear[$classYearId])) {
            $graduatesByClassYear[$classYearId] = [];
        }

        if (!in_array($programId, $graduatesByClassYear[$classYearId])) {
            $graduatesByClassYear[$classYearId][] = $programId;
        }
    }
}
?>

<div>
    <div class="alumni-count-container">
        <span class="all-alumni"><a href="field_of_work.php">All Alumni</a></span>
        <div class="count"><?php echo $completedFormsCount; ?></div>
    </div>
</div>
<hr>

<?php
foreach ($graduatesByClassYear as $classYearId => $programIds) {
    $graduationDate = isset($graduationYears[$classYearId]['batch_yrs'])
        ? $graduationYears[$classYearId]['batch_yrs']
        : 'Unspecified Year';

    echo '<button class="collapsible transparent">BATCH ' .
        htmlspecialchars($graduationDate) .
        '<i class="fa fa-angle-right arrow"></i></button>';
    echo '<div class="contents">';

    foreach ($programIds as $programId) {
        if (isset($academicPrograms[$programId])) {
            $programCode = $academicPrograms[$programId]['courCode'];
            $programName = $academicPrograms[$programId]['course_name'];

            echo '<div class="course-container">';
            echo '<button class="collaps-department transparent" ' .
                'data-program-id="' . htmlspecialchars($programId) . '" ' .
                'data-class-year-id="' . htmlspecialchars($classYearId) . '">' .
                htmlspecialchars($programCode) . '</button>';

            echo '<select class="employment-status-select" style="display:none;">';
            echo '<option value="">Select Status</option>';
            echo '<option value="Unemployed">Unemployed</option>';
            echo '<option value="Employed">Employed</option>';
            echo '</select>';
            echo '</div>';
        }
    }
    echo '</div>';
}
?>
