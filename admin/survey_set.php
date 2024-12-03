<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/firebaseRDB.php';

require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

// Get the unique ID from the URL parameter
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id) {
    // Retrieve the specific survey set using the unique ID
    $survey = $firebase->retrieve("survey_set/$id");
    $survey = json_decode($survey, true);

    if ($survey) {
        $title = $survey['survey_title'];
        $description = $survey['survey_desc'];
        $startDate = $survey['survey_start'];
        $endDate = $survey['survey_end'];
        $dateCreated = $survey['surveys_created'];  // Assuming 'survey_created' field exists

        // Retrieve questions related to the survey set
        $questions = $firebase->retrieve("questions");
        $questions = json_decode($questions, true);

        // Check if questions is an array
        $related_questions = [];
        if (is_array($questions)) {
            // Filter questions by survey_set_unique_id
            $related_questions = array_filter($questions, function ($question) use ($id) {
                return isset($question['survey_set_unique_id']) && $question['survey_set_unique_id'] === $id;
            });
        }
    } else {
        echo "Survey not found.";
        exit;
    }
} else {
    echo "Invalid ID.";
    exit;
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    Survey <i class="fa fa-angle-right"></i> <?php echo $title ?>
                </h1>
                <div class="box-inline ">

                    <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat"><i
                            class="fa fa-plus-circle"></i>&nbsp;&nbsp; New</a>

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
                    <li class="active">Survey Set</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Reminder</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
                    unset($_SESSION['success']);
                }
                ?>
                <div class="row">
                    <div class="col-md-9">
                        <div class="box">
                            <div class="box-header with-border"></div>
                            <div class="box-body">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><b>Survey Questionnaire</b></h3>
                                    </div>
                                    <form action="" id="manage-sort">
                                        <div class="card-body ui-sortable">
                                            <?php foreach ($related_questions as $question_id => $question): ?>
                                                <div class="callout callout-info"
                                                    style="  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);">
                                                    <div class="row">

                                                        <div class="col-md-10" style="color:black">
                                                            <h5><?= $question['question'] ?></h5>
                                                        </div>
                                                        <div class="col-md-2 text-right">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-default dropdown-toggle"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    <i class="fa fa-ellipsis-v text-dark"></i>
                                                                </button>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuButton">
                                                                    <a class="dropdown-item edit_question text-dark open-modal"
                                                                        href="javascript:void(0)"
                                                                        data-id="<?php echo $question_id; ?>"
                                                                        style="color:black">Edit</a>
                                                                    <a class="dropdown-item delete_question text-dark delete-modal"
                                                                        href="javascript:void(0)" style="color:black"
                                                                        data-id="<?php echo $question_id; ?>">Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="qid[]" value="<?= $question_id ?>">
                                                            <?php if ($question['type'] == 'radio_opt'): ?>
                                                                <?php $options = json_decode($question['frm_option'], true); ?>
                                                                <?php foreach ($options as $option_id => $option): ?>
                                                                    <div class="icheck-primary">
                                                                        <input type="radio" id="option_<?= $option_id ?>"
                                                                            name="answer[<?= $question_id ?>]"
                                                                            value="<?= $option_id ?>">
                                                                        <label for="option_<?= $option_id ?>"
                                                                            style="color:black"><?= $option ?></label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($question['type'] == 'check_opt'): ?>
                                                                <?php $options = json_decode($question['frm_option'], true); ?>
                                                                <?php foreach ($options as $option_id => $option): ?>
                                                                    <div class="icheck-primary">
                                                                        <input type="checkbox" id="option_<?= $option_id ?>"
                                                                            name="answer[<?= $question_id ?>][]"
                                                                            value="<?= $option_id ?>">
                                                                        <label for="option_<?= $option_id ?>"
                                                                            style="color:black"><?= $option ?></label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($question['type'] == 'textfield_s'): ?>
                                                                <div class="form-group">
                                                                    <textarea name="answer[<?= $question_id ?>]" cols="30"
                                                                        rows="4" class="form-control"></textarea>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="box">
                            <div class="box-header with-border"></div>
                            <div class="box-body survey-detail">
                                <center>
                                    <p><strong>Title</strong><br> <?php echo $title ?></p>
                                    <p style="text-align:justify;"><strong>Description</strong><br> <?php echo $description ?></p>
                                    <hr>
                                    <p><strong>Start Date</strong><br> <?php echo $startDate ?></p>
                                    <p><strong>End Date</strong><br> <?php echo $endDate ?></p>
                                    <p><strong>Date Created</strong><br> <?php echo $dateCreated ?></p>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>


            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/survey_set_edit_modal.php'; ?>
        <?php include 'includes/survey_set_add_modal.php'; ?>
        <?php include 'includes/survey_set_delete_modal.php'; ?>
    </div>



    <?php include 'includes/scripts.php'; ?>
    <script>
        $(document).ready(function () {
            $('#addSurveySetForm').on('submit', function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'survey_set_add.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Error details:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        showAlert('error', 'An unexpected error occurred.');
                    }
                });
            });

            
            $('#editQuestionForm').on('submit', function (event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'survey_set_edit.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                        } else if (response.status === 'info') {
                            showAlert('info', response.message); // Handle 'info' status for no data change
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Error details:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        showAlert('error', 'An unexpected error occurred.');
                    }
                });
            });




            $('#manage-delete-question').on('submit', function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'survey_set_delete.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        console.log('Raw response:', response);
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Error details:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        showAlert('error', 'An unexpected error occurred.');
                    }
                });
            });


            function showAlert(type, message) {
                Swal.fire({
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 2500,
                    willClose: () => {
                        if (type === 'success') {
                            location.reload();
                        }
                    }
                });
            }

            function showAlertEdit(type, message) {
                let iconType = 'info';
                let title = 'Oops...';

                switch (type) {
                    case 'info':
                        iconType = 'info';
                        title = 'Oops...';
                        break;
                }

                Swal.fire({
                    icon: iconType,
                    title: title,
                    text: message,
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'swal-title',
                        htmlContainer: 'swal-text',
                        confirmButton: 'swal-button'
                    }
                });
            }
        });
    </script>


</body>

</html>


<style>
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }


    td {
        padding: 8px !important;

        vertical-align: middle !important;
        max-width: 200px !important;
        /* Adjust maximum width as needed */
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
</style>