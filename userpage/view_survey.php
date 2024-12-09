<?php include '../includes/session.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Survey Dashboard</title>
    <?php include 'includes/header.php' ?>

</head>

<body>
    <?php include 'includes/navbar.php' ?>
    <div class="main-content">
        <div class="sale-statistic-area">
            <div class="container">
                <div class="row">
                    <div class="tab-container">
                        <button class="tab active" data-tab="pending">Pending Surveys</button>
                        <button class="tab" data-tab="completed">Completed Surveys</button>
                    </div>

                    <?php
                    $survey = $firebase->retrieve("survey_set");
                    $survey = json_decode($survey, true);

                    $alumni_id = $_SESSION['alumni_id'];

                    // Retrieve alumni data to get the course
                    $alumni_data = $firebase->retrieve("alumni/$alumni_id");
                    $alumni_data = json_decode($alumni_data, true);
                    $alumni_course = $alumni_data['course'] ?? null;

                    // Retrieve survey_log for the current alumni
                    $survey_log = $firebase->retrieve("survey_log");
                    $survey_log = json_decode($survey_log, true);

                    // Create a lookup array for quick status checks
                    $completed_surveys = [];
                    if ($survey_log) {
                        foreach ($survey_log as $log) {
                            if ($log['alumni_id'] == $alumni_id && $log['status'] == 'done') {
                                $completed_surveys[$log['survey_id']] = true;
                            }
                        }
                    }

                    function isSurveyActive($surveyData)
                    {
                        $currentDate = date('Y-m-d');
                        $startDate = $surveyData['survey_start'];
                        $endDate = $surveyData['survey_end'];

                        return ($currentDate >= $startDate && $currentDate <= $endDate);
                    }

                    function isSurveyApplicable($surveyData, $alumni_course)
                    {
                        if (!is_array($surveyData)) {
                            error_log("Survey data is not an array for course: $alumni_course");
                            return false;
                        }

                        if (isset($surveyData['survey_batch']) && is_array($surveyData['survey_batch']) && in_array("All", $surveyData['survey_batch'])) {
                            return true;
                        }

                        if (isset($surveyData['survey_courses']) && is_array($surveyData['survey_courses'])) {
                            return in_array($alumni_course, $surveyData['survey_courses']);
                        }

                        error_log("Survey data missing expected keys for course: $alumni_course");
                        return false;
                    }

                    function renderSurveyCard($id, $surveyData, $status)
                    {
                        $isDisabled = $status === 'done' ? 'onclick="event.preventDefault();"' : 'onclick="openSurvey(\'' . $id . '\')"';
                        $opacity = (isSurveyActive($surveyData)) ? '1' : '0.5';
                        $currentDate = date('Y-m-d');
                        $endDate = $surveyData['survey_end'];

                        $cardContent = '
                <div id="survey-' . $id . '" class="survey-card" ' . $isDisabled . ' style="opacity: ' . $opacity . ';">
                    <div class="container-card">
                        <h2 class="meta-card-title">' . htmlspecialchars($surveyData['survey_title']) . '</h2>
                        <p class="card-description">' . htmlspecialchars($surveyData['survey_desc']) . '</p>
                        <p class="card-meta">Start: ' . htmlspecialchars($surveyData['survey_start']) . '</p>
                        <p class="card-meta">End: ' . htmlspecialchars($surveyData['survey_end']) . '</p>
                        <p class="card-meta">Created: ' . htmlspecialchars($surveyData['surveys_created']) . '</p>
                        ' . ($status === 'done' ? '<div class="survey-status status-done">Completed</div>' : '') . '
                    </div>
                </div>';

                        if ($currentDate >= $surveyData['survey_start']) {
                            return $cardContent;
                        } else {
                            return ''; // Return empty string if survey hasn't started yet
                        }
                    }
                    ?>

                    <div id="pending" class="tab-content active">
                        <div class="gradient-cards">
                            <?php
                            if (!empty($survey) && is_array($survey)) {
                                $pendingCount = 0;
                                foreach ($survey as $id => $surveyData) {
                                    if (!isset($completed_surveys[$id]) && isSurveyActive($surveyData) && isSurveyApplicable($surveyData, $alumni_course)) {
                                        $card = renderSurveyCard($id, $surveyData, 'pending');
                                        if (!empty($card)) {
                                            echo $card;
                                            $pendingCount++;
                                        }
                                    }
                                }
                                if ($pendingCount == 0) {
                                    echo '<p class="no-surveys-message">No pending surveys available.</p>';
                                }
                            } else {
                                echo '<p class="no-surveys-message">No surveys available.</p>';
                            }
                            ?>
                        </div>
                    </div>

                    <div id="completed" class="tab-content">
                        <div class="gradient-cards">
                            <?php
                            if (!empty($survey) && is_array($survey)) {
                                $completedCount = 0;
                                foreach ($survey as $id => $surveyData) {
                                    if (isset($completed_surveys[$id]) && isSurveyApplicable($surveyData, $alumni_course)) {
                                        $card = renderSurveyCard($id, $surveyData, 'done');
                                        if (!empty($card)) {
                                            echo $card;
                                            $completedCount++;
                                        }
                                    }
                                }
                                if ($completedCount == 0) {
                                    echo '<p class="no-surveys-message">No completed surveys yet.</p>';
                                }
                            } else {
                                echo '<p class="no-surveys-message">No surveys available.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Function to open survey details page
            function openSurvey(id) {
                window.location.href = 'visit_survey.php?id=' + id;
            }

            // Function to switch tabs
            function openTab(evt, tabName) {
                var tabContents = document.getElementsByClassName("tab-content");
                for (var i = 0; i < tabContents.length; i++) {
                    tabContents[i].classList.remove("active");
                }

                var tabLinks = document.getElementsByClassName("tab");
                for (var i = 0; i < tabLinks.length; i++) {
                    tabLinks[i].classList.remove("active");
                }

                document.getElementById(tabName).classList.add("active");
                evt.currentTarget.classList.add("active");
            }

            // Add event listeners to tabs
            document.addEventListener('DOMContentLoaded', function () {
                var tabs = document.getElementsByClassName("tab");
                for (var i = 0; i < tabs.length; i++) {
                    tabs[i].addEventListener('click', function (event) {
                        openTab(event, this.getAttribute('data-tab'));
                    });
                }
            });

            // Check if survey was just completed and show SweetAlert
            <?php
            if (isset($_SESSION['survey_completed']) && $_SESSION['survey_completed']) {
                unset($_SESSION['survey_completed']);
                echo "
                                Swal.fire({
                                    title: 'Thank You!',
                                    text: 'Your survey has been submitted successfully.',
                                    icon: 'success',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                        ";
            }
            ?>
        </script>


</body>

</html>