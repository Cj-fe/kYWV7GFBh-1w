<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
function getAlumniName($alumniId, $alumniData)
{
    if (isset($alumniData[$alumniId]['firstname']) && isset($alumniData[$alumniId]['lastname'])) {
        return $alumniData[$alumniId]['firstname'] . ' ' . $alumniData[$alumniId]['lastname'];
    }
    return 'Unknown';
}

function displaySurveyQuestionsAndAnswers($surveyId)
{
    global $firebase;

    // Fetch survey details
    $surveyData = $firebase->retrieve("survey_set/$surveyId");
    $survey = json_decode($surveyData, true) ?: [];

    if (empty($survey)) {
        echo "<p>Survey not found or is empty.</p>";
        return;
    }

    // Fetch alumni data
    $alumniData = $firebase->retrieve("alumni");
    $alumni = json_decode($alumniData, true) ?: [];

    echo "<div class='row'>";
    echo "<div class='col-md-9'>";  // Left column for survey results

    echo "<h2>" . htmlspecialchars($survey['survey_title'] ?? 'Untitled Survey') . "</h2>";
    echo "<p>" . htmlspecialchars($survey['survey_desc'] ?? 'No description available.') . "</p>";

    // Fetch questions for this survey
    $questionsData = $firebase->retrieve("questions");
    $questions = json_decode($questionsData, true) ?: [];

    // Fetch answers for this survey
    $answersData = $firebase->retrieve("answers");
    $answers = json_decode($answersData, true) ?: [];

    echo "<div class='box'>";
    echo "<div class='box-header'>";
    echo "<h3 class='box-title'>Survey Results</h3>";
    echo "</div>";
    echo "<div class='box-body no-padding'>";
    echo "<table class='table'>";
    echo "<tr><th style='width: 5%'>#</th><th style='width: 30%'>Question</th><th style='width: 55%'>Progress</th><th style='width: 10%'>Percentage</th></tr>";

    $questionCount = 0;
    $allRespondents = [];
    foreach ($questions as $questionId => $question) {
        if (($question['survey_set_unique_id'] ?? '') == $surveyId) {
            $questionCount++;
            $respondents = [];

            if (($question['type'] ?? '') === 'textfield_s') {
                // Handle textfield_s type questions
                $textAnswers = [];
                foreach ($answers as $answerId => $answer) {
                    if (($answer['question_id'] ?? '') == $questionId) {
                        $textAnswers[] = $answer['answer'] ?? '';
                        $respondents[] = $answer['alumni_id'] ?? '';
                    }
                }

                $totalRespondents = count(array_filter($respondents));

                echo "<tr>";
                echo "<td>" . $questionCount . "</td>";
                echo "<td>" . htmlspecialchars($question['question'] ?? 'Unknown Question') . "</td>";
                echo "<td>";
                echo "<div style='max-height: 200px; overflow-y: auto;'>";
                foreach ($textAnswers as $textAnswer) {
                    echo "<div>" . htmlspecialchars($textAnswer) . "</div>";
                }
                echo "</div>";
                echo "</td>";
                echo "<td>N/A</td>";
                echo "</tr>";
            } elseif (($question['type'] ?? '') === 'check_opt') {
                // Handle check_opt type questions
                $options = json_decode($question['frm_option'] ?? '{}', true) ?: [];
                $answerCounts = array_fill_keys(array_keys($options), 0);

                foreach ($answers as $answerId => $answer) {
                    if (($answer['question_id'] ?? '') == $questionId) {
                        $answerValues = json_decode($answer['answer'] ?? '[]', true) ?: [];
                        foreach ($answerValues as $value) {
                            if (isset($answerCounts[$value])) {
                                $answerCounts[$value]++;
                            }
                        }
                        $respondents[] = $answer['alumni_id'] ?? '';
                    }
                }

                $totalRespondents = count(array_filter($respondents));

                echo "<tr>";
                echo "<td rowspan='" . (count($options) + 1) . "'>" . $questionCount . "</td>";
                echo "<td rowspan='" . (count($options) + 1) . "'>" . htmlspecialchars($question['question'] ?? 'Unknown Question') . "</td>";
                echo "<td colspan='2'></td>";  // Empty cell for spacing
                echo "</tr>";

                foreach ($options as $optionId => $optionText) {
                    $count = $answerCounts[$optionId] ?? 0;
                    $percentage = $totalRespondents > 0 ? round(($count / $totalRespondents) * 100, 2) : 0;
                    echo "<tr>";
                    echo "<td>";
                    echo "<div style='display: flex; justify-content: space-between;'>";
                    echo "<span>" . htmlspecialchars($optionText) . "</span>";
                    echo "<span>(" . $count . " responses)</span>";
                    echo "</div>";
                    echo "<div class='progress progress-xs'>";
                    echo "<div class='progress-bar progress-bar-primary' style='width: " . $percentage . "%'></div>";
                    echo "</div>";
                    echo "</td>";
                    echo "<td><span class='badge bg-light-blue'>" . $percentage . "%</span></td>";
                    echo "</tr>";
                }
            } else {
                // Existing code for other question types (e.g., radio_opt)
                $options = json_decode($question['frm_option'] ?? '{}', true) ?: [];
                $answerCounts = array_fill_keys(array_keys($options), 0);

                foreach ($answers as $answerId => $answer) {
                    if (($answer['question_id'] ?? '') == $questionId) {
                        $answerValue = $answer['answer'] ?? '';
                        if (isset($answerCounts[$answerValue])) {
                            $answerCounts[$answerValue]++;
                            $respondents[] = $answer['alumni_id'] ?? '';
                        }
                    }
                }

                $totalRespondents = count(array_filter($respondents));

                echo "<tr>";
                echo "<td rowspan='" . (count($options) + 1) . "'>" . $questionCount . "</td>";
                echo "<td rowspan='" . (count($options) + 1) . "'>" . htmlspecialchars($question['question'] ?? 'Unknown Question') . "</td>";
                echo "<td colspan='2'></td>";  // Empty cell for spacing
                echo "</tr>";

                foreach ($options as $optionId => $optionText) {
                    $count = $answerCounts[$optionId] ?? 0;
                    $percentage = $totalRespondents > 0 ? round(($count / $totalRespondents) * 100, 2) : 0;
                    echo "<tr>";
                    echo "<td>";
                    echo "<div style='display: flex; justify-content: space-between;'>";
                    echo "<span>" . htmlspecialchars($optionText) . "</span>";
                    echo "<span>(" . $count . " responses)</span>";
                    echo "</div>";
                    echo "<div class='progress progress-xs'>";
                    echo "<div class='progress-bar progress-bar-primary' style='width: " . $percentage . "%'></div>";
                    echo "</div>";
                    echo "</td>";
                    echo "<td><span class='badge bg-light-blue'>" . $percentage . "%</span></td>";
                    echo "</tr>";
                }
            }

            echo "<tr><td colspan='4'>Total Respondents: " . $totalRespondents . "</td></tr>";
            $allRespondents = array_merge($allRespondents, $respondents);
        }
    }

    echo "</table>";
    echo "</div>";
    echo "</div>";

    echo "</div>";  // End of left column

    // Right column for respondents list
    echo "<div class='col-md-3'>";
    echo "<div class='box box-solid' style='border-radius: none !important;'>";
    echo "<div class='box-header with-border'>";
    echo "<h3 class='box-title'>Respondents</h3>";
    echo "<div class='box-tools'>";
    echo "<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>";
    echo "</div>";
    echo "</div>";
    echo "<div class='box-body no-padding'>";
    echo "<ul class='nav nav-pills nav-stacked'>";
    
    $uniqueRespondents = array_filter(array_unique($allRespondents));
    foreach ($uniqueRespondents as $respondentId) {
        $respondentName = getAlumniName($respondentId, $alumni);
        echo "<li><a href='alumni_profile.php?id=" . urlencode($respondentId) . "'>" . htmlspecialchars($respondentName) . "</a></li>";
    }
    
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    echo "</div>";  // End of right column

    echo "</div>";  // End of row
}
?>

<style>
    .box-header {
        width: 100%;
        height: 35px;
        background: white !important;
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
    }
    .box{
        border-radius: 0 !important;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    Survey <i class="fa fa-angle-right"></i> Results
                </h1>
                <div class="box-inline">
                    <div class="search-container">
                        <input type="text" class="search-input" id="search-input" placeholder="Search...">
                        <button class="search-button" onclick="filterTable()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Survey</li>
                    <li class="active">Results</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-3">
                        <div class="box box-solid" style="border-radius: none !important;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Surveys</h3>
                                <div class="box-tools">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body no-padding">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php
                                    $surveysData = $firebase->retrieve("survey_set");
                                    $surveys = json_decode($surveysData, true) ?: [];
                                    $surveyId = isset($_GET['id']) ? $_GET['id'] : null;
                                    foreach ($surveys as $id => $survey) {
                                        echo '<li' . ($id == $surveyId ? ' class="active"' : '') . '><a href="?id=' . $id . '">' . htmlspecialchars($survey['survey_title'] ?? 'Untitled Survey') . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Survey Results</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <?php
                                if ($surveyId) {
                                    displaySurveyQuestionsAndAnswers($surveyId);
                                } else {
                                    echo "<p>No survey selected. Please choose a survey from the list.</p>";
                                }
                                ?>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                    <!-- /. box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/news_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>

    <script>
        $(function () {
            // Initialize iCheck for all checkboxes
            $('.mailbox-messages input[type="checkbox"]').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            // Check/uncheck all checkboxes functionality
            $('.checkbox-toggle').click(function () {
                var $this = $(this);
                var isChecked = $this.data('clicks');
                if (isChecked) {
                    // Uncheck all checkboxes
                    $('.mailbox-messages input[type="checkbox"]').iCheck('uncheck');
                    $this.find('i').removeClass('fa-check-square-o').addClass('fa-square-o');
                } else {
                    // Check all checkboxes
                    $('.mailbox-messages input[type="checkbox"]').iCheck('check');
                    $this.find('i').removeClass('fa-square-o').addClass('fa-check-square-o');
                }
                $this.data('clicks', !isChecked);
            });
            $(".mailbox-star").click(function (e) {
                e.preventDefault();
                var $this = $(this).find("a > i");
                $this.toggleClass("glyphicon-star glyphicon-star-empty fa-star fa-star-o");
            });
        });

        function filterTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search-input");
            filter = input.value.toUpperCase();
            table = document.querySelector(".table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1]; // Assuming the question text is in the second column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>
</html>