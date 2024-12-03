<?php

$uniqueCoursesData = $firebase->retrieve("course");
$uniqueBatchYearsData = $firebase->retrieve("batch_yr");
$uniqueAlumniData = $firebase->retrieve("alumni");
$uniqueEventsData = $firebase->retrieve("event");
$uniqueEventParticipationData = $firebase->retrieve("event_participation");

$uniqueCourses = json_decode($uniqueCoursesData, true) ?: [];
$uniqueBatchYears = json_decode($uniqueBatchYearsData, true) ?: [];
$uniqueAlumni = json_decode($uniqueAlumniData, true) ?: [];
$uniqueEvents = json_decode($uniqueEventsData, true) ?: [];
$uniqueEventParticipations = json_decode($uniqueEventParticipationData, true) ?: [];

// Group participants by event, batch year, and course
$uniqueGroupedParticipants = [];

foreach ($uniqueEventParticipations as $participationId => $participationDetails) {
    $uniqueEventId = $participationDetails['event_id'];
    $uniqueAlumniId = $participationDetails['alumni_id'];

    if (isset($uniqueEvents[$uniqueEventId]) && isset($uniqueAlumni[$uniqueAlumniId])) {
        $uniqueAlumniDetails = $uniqueAlumni[$uniqueAlumniId];
        $uniqueBatchId = $uniqueAlumniDetails['batch'];
        $uniqueCourseId = $uniqueAlumniDetails['course'];

        if (!isset($uniqueGroupedParticipants[$uniqueEventId])) {
            $uniqueGroupedParticipants[$uniqueEventId] = [];
        }

        if (!isset($uniqueGroupedParticipants[$uniqueEventId][$uniqueBatchId])) {
            $uniqueGroupedParticipants[$uniqueEventId][$uniqueBatchId] = [];
        }

        if (!in_array($uniqueCourseId, $uniqueGroupedParticipants[$uniqueEventId][$uniqueBatchId])) {
            $uniqueGroupedParticipants[$uniqueEventId][$uniqueBatchId][] = $uniqueCourseId;
        }
    }
}
?>
<div>
    <div class="alumni-count-container" style="padding-top: 20px">
        <span class="all-alumni"><a href="event_report.php">All Respondents</a></span>
    </div>
</div>
<hr>
<?php
foreach ($uniqueGroupedParticipants as $uniqueEventId => $uniqueBatches) {
    $uniqueEvent = $uniqueEvents[$uniqueEventId];
    echo '<button class="collapsible transparent"><span>' . htmlspecialchars($uniqueEvent['event_title']) . '</span><i class="fa fa-angle-right arrow"></i></button>';
    echo '<div class="contents">';

    foreach ($uniqueBatches as $uniqueBatchId => $uniqueCourseIds) {
        foreach ($uniqueCourseIds as $uniqueCourseId) {
            if (isset($uniqueCourses[$uniqueCourseId]) && isset($uniqueBatchYears[$uniqueBatchId])) {
                $uniqueBatchYear = $uniqueBatchYears[$uniqueBatchId]['batch_yrs'];
                $uniqueCourseCode = $uniqueCourses[$uniqueCourseId]['courCode'];
                $uniqueUrl = "event_report.php?event_id={$uniqueEventId}&course={$uniqueCourseId}&batch={$uniqueBatchId}";
                echo '<button class="collaps-department transparent"><a href="' . htmlspecialchars($uniqueUrl) . '">' . htmlspecialchars($uniqueCourseCode) . ' - Batch ' . htmlspecialchars($uniqueBatchYear) . '</a></button>';
            }
        }
    }

    echo '</div>';
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function closeAllExcept(current, className) {
            var uniqueColl = document.getElementsByClassName(className);
            for (var i = 0; i < uniqueColl.length; i++) {
                if (uniqueColl[i] !== current) {
                    uniqueColl[i].classList.remove("active");
                    var uniqueContent = uniqueColl[i].nextElementSibling;
                    if (uniqueContent) {
                        uniqueContent.classList.remove("active");
                    }
                }
            }
        }

        function toggleContent(className) {
            var uniqueColl = document.getElementsByClassName(className);
            for (var i = 0; i < uniqueColl.length; i++) {
                uniqueColl[i].addEventListener("click", function (event) {
                    event.preventDefault(); // Prevent the default action
                    closeAllExcept(this, className);
                    this.classList.toggle("active");
                    var uniqueContent = this.nextElementSibling;
                    if (uniqueContent) {
                        uniqueContent.classList.toggle("active");
                    }
                });
            }
        }

        toggleContent("collapsible");
    });
</script>