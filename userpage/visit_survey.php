<?php
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Get the survey_set_unique_id from the URL parameter
$survey_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$survey_id) {
    die("No survey ID provided");
}

// Retrieve the survey details
$surveyData = $firebase->retrieve("survey_set/$survey_id");
$surveyData = json_decode($surveyData, true);

// Retrieve all questions
$questionData = $firebase->retrieve("questions");
$questionData = json_decode($questionData, true);

// Filter questions based on survey_set_unique_id
$filteredQuestions = array_filter($questionData, function ($question) use ($survey_id) {
    return isset($question['survey_set_unique_id']) && $question['survey_set_unique_id'] == $survey_id;
});

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumni_id = $_SESSION['alumni_id']; // Assuming the alumni ID is stored in the session
    $date_created = date('Y-m-d H:i:s');

    foreach ($filteredQuestions as $questionId => $question) {
        $answer = isset($_POST["question_$questionId"]) ? $_POST["question_$questionId"] : null;

        if ($answer !== null) {
            $answerData = [
                'survey_id' => $survey_id,
                'alumni_id' => $alumni_id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
                'question_id' => $questionId,
                'date_created' => $date_created
            ];

            $result = $firebase->insert("answers", $answerData);

            if ($result === null) {
                // Handle error
                echo "Error inserting answer for question $questionId<br>";
            }
        }
    }

    // Log the survey completion in the survey_log
    $logData = [
        'alumni_id' => $alumni_id,
        'survey_id' => $survey_id,
        'completion_date' => $date_created,
        'status' => 'done'
    ];
    $firebase->insert("survey_log", $logData);

    // Set a session variable to trigger the SweetAlert
    $_SESSION['survey_completed'] = true;

    // Redirect back to the view_survey.php page
    header("Location: view_survey.php");
    exit();
}

// Function to generate question HTML based on question type
function generateQuestionHTML($question, $questionId)
{
    $html = '<div class="form-group">';
    $html .= '<label class="survey-question-modern">' . htmlspecialchars($question['question']) . '</label>';

    switch ($question['type']) {
        case 'radio_opt':
            $html .= '<div class="radio-group-modern">';
            $options = json_decode($question['frm_option'], true);
            foreach ($options as $optionId => $optionText) {
                $html .= '<label class="radio-modern">';
                $html .= '<input type="radio" name="question_' . $questionId . '" value="' . htmlspecialchars($optionId) . '">';
                $html .= '<span class="custom-radio"></span>' . htmlspecialchars($optionText);
                $html .= '</label>';
            }
            $html .= '</div>';
            break;
        case 'check_opt':
            $html .= '<div class="checkbox-group-modern">';
            $options = json_decode($question['frm_option'], true);
            foreach ($options as $optionId => $optionText) {
                $html .= '<label class="checkbox-modern">';
                $html .= '<input type="checkbox" name="question_' . $questionId . '[]" value="' . htmlspecialchars($optionId) . '">';
                $html .= '<span class="custom-checkbox"></span>' . htmlspecialchars($optionText);
                $html .= '</label>';
            }
            $html .= '</div>';
            break;
        case 'textfield_s':
            $html .= '<textarea class="form-control-modern" name="question_' . $questionId . '" rows="4" placeholder="Type your answer here..."></textarea>';
            break;
        default:
            $html .= '<p>Unsupported question type</p>';
    }

    $html .= '</div>';
    return $html;
}
?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'includes/header.php' ?>
    <style>
      

        /* Modern Card Design */
        .card-modern {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            max-width: 100%;
        }

        .card-modern:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Section Titles */
        .section-title {
            font-size: 1.2em;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        /* Modern List Styling */
        ul {
            list-style-type: none;
            padding-left: 0;
        }

        .event-item-modern,
        .job-item-modern,
        .forum-item-modern {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .event-date,
        .event-title,
        .job-title,
        .forum-title {
            font-size: 0.95em;
            color: #555;
            transition: color 0.3s ease;
        }

        .event-date {
            background-color: #007bff;
            color: #ffffff;
            border-radius: 5px;
            padding: 5px 10px;
            margin-right: 10px;
            font-weight: bold;
        }

        .event-item-modern:hover .event-title,
        .job-item-modern:hover .job-title,
        .forum-item-modern:hover .forum-title {
            color: #007bff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .right-section {
                position: static;
                height: auto;
            }
        }
    </style>

</head>

<body>
    <?php include 'includes/navbar.php' ?>

    <div class="main-content">
        <div class="sale-statistic-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                        <form class="survey-form-modern" method="POST">
                            <div class="form-header">
                                <h1 class="form-title"><?php echo htmlspecialchars($surveyData['survey_title']); ?></h1>
                                <p class="form-description"><?php echo htmlspecialchars($surveyData['survey_desc']); ?>
                                </p>
                            </div>

                            <?php
                            foreach ($filteredQuestions as $questionId => $question) {
                                echo generateQuestionHTML($question, $questionId);
                            }
                            ?>

                            <div class="form-group">
                                <button type="submit" class="btn-modern">Submit</button>
                            </div>
                        </form>
                    </div>

                   <!-- <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12" >
                        <div class="right-section">
                            <div class="content-wrapper-modern">
                                <div class="card-modern">
                                    <h3 class="section-title">Upcoming Events</h3>
                                    <ul class="event-list-modern">
                                        <li class="event-item-modern">
                                            <span class="event-date">Sep 15, 2024</span>
                                            <p class="event-title">Alumni Meetup</p>
                                        </li>
                                        <li class="event-item-modern">
                                            <span class="event-date">Sep 20, 2024</span>
                                            <p class="event-title">Webinar on Industry Trends</p>
                                        </li>
                                        <li class="event-item-modern">
                                            <span class="event-date">Oct 5, 2024</span>
                                            <p class="event-title">Job Fair</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>-->


                </div>
            </div>
        </div>
    </div>


</body>

</html>