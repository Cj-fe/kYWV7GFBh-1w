<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
    $('.selectpicker').selectpicker();

    // Event listener for when the selection changes
    $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var selectedOptions = $(this).val();  // Get the selected options

        // Check if 'All' is selected
        if (selectedOptions && selectedOptions.includes('All')) {
            // Clear all other selections and only select 'All'
            $(this).selectpicker('val', 'All');
        } else {
            // Deselect 'All' if any other option is selected
            var index = selectedOptions.indexOf('All');
            if (index > -1) {
                selectedOptions.splice(index, 1);
                $(this).selectpicker('val', selectedOptions);
            }
        }

        // Update the hidden input with selected options (if any)
        if (selectedOptions) {
            $('#selected-options').val(selectedOptions.join(', '));
        }
    });
});

</script>
<?php
// Include the FirebaseRDB class file
require_once 'includes/firebaseRDB.php';

require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

$batchYears = $firebase->retrieve("batch_yr");
$batchYears = json_decode($batchYears, true); // Decode JSON data into associative arrays



// Fetch data from Firebase
$courseKey = "course"; // Replace with your actual Firebase path or key for courses

$data = $firebase->retrieve($courseKey);
$data = json_decode($data, true); // Decode JSON data into associative arrays

?>
<div class="modal fade" id="addnew">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title"><b>Survey <i class="fa fa-angle-right"></i> Survey Set <i
                            class="fa fa-angle-right"></i> Add</b></h2>
                <hr>
            </div>
            <div class="modal-body">
                <form id="addSurveyForm" class="form-horizontal" method="POST" action="survey_add.php"
                    enctype="multipart/form-data" onsubmit="return validateForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <div class="personal_information">
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Primary Details</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="type" class="col-form-label">Type</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="Built in survey">Built in survey</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="survey_title" class="col-form-label">Survey Title</label>
                                        <input type="text" class="form-control" id="survey_title" name="survey_title">
                                        <small class="error-message" id="add_title_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="survey_desc" class="col-form-label">Survey Description</label>
                                        <textarea class="form-control" id="survey_desc" name="survey_desc"
                                            style="height: 200px;"></textarea>
                                        <small class="error-message" id="add_desc_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="survey_batch" class="col-form-label">Send To:</label>
                                        <div class="bootstrap-select col-sm-10">
                                            <select class="selectpicker form-control" id="survey_batch"
                                                name="survey_batch[]" multiple title="Choose Batch....">
                                                <option value="All">All</option>

                                                <?php
                                                if (!empty($batchYears) && is_array($batchYears)) {
                                                    foreach ($batchYears as $batchId => $batchDetails) {
                                                        $batchYear = isset($batchDetails['batch_yrs']) ? $batchDetails['batch_yrs'] : 'Unknown';
                                                        echo "<option value=\"" . htmlspecialchars($batchId) . "\">" . htmlspecialchars($batchYear) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <small class="error-message" id="survey_batch_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="survey_courses" class="col-form-label">Send To:</label>
                                        <div class="bootstrap-select col-sm-10">
                                            <select class="selectpicker form-control" id="survey_courses"
                                                name="survey_courses[]" multiple title="Choose Course....">
                                                <option value="All">All</option>

                                                <?php
                                                if (is_array($data)) {
                                                    foreach ($data as $courseId => $details) {
                                                        $courseCode = isset($details['courCode']) ? htmlspecialchars($details['courCode']) : 'Unknown';
                                                        echo "<option value=\"" . htmlspecialchars($courseId) . "\">" . $courseCode . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <small class="error-message" id="survey_courses_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="edit_address" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Timeframe</h4>
                            </label>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label for="survey_start" class="col-form-label">Start Date</label>
                                        <input type="date" class="form-control" id="survey_start" name="survey_start"
                                            value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                                        <small class="error-message" id="add_startdate_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="survey_end" class="col-form-label">End Date</label>
                                        <input type="date" class="form-control" id="survey_end" name="survey_end"
                                            min="<?php echo date('Y-m-d'); ?>">
                                        <small class="error-message" id="add_enddate_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-flat pull-right btn-class" name="add"
                            style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                                class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                                class="fa fa-close"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title"><b>Survey <i class="fa fa-angle-right"></i> Survey Set <i
                            class="fa fa-angle-right"></i>
                        Edit</b></h2>
                <hr>
            </div>
            <div class="modal-body">
                <form id="editSurveyForm" class="form-horizontal" method="POST" action="survey_edit.php"
                    enctype="multipart/form-data" onsubmit="return validateEditForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <input type="hidden" name="id" id="editId" value="">
                    <div class="personal_information">
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Primary Details</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_type" class="col-form-label">Type</label>
                                        <select class="form-control" name="edit_type" id="edit_type">
                                            <option value="Built in survey">Built in survey</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_survey_title" class="col-form-label">Survey Title</label>
                                        <input type="text" class="form-control" id="edit_survey_title"
                                            name="edit_survey_title">
                                        <small class="error-message" id="edit_title_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_survey_desc" class="col-form-label">Survey Description</label>
                                        <textarea class="form-control" id="edit_survey_desc" name="edit_survey_desc"
                                            style="height: 200px;"></textarea>
                                        <small class="error-message" id="edit_desc_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <!-- New field for Survey Batch -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_survey_batch" class="col-form-label">Send To:</label>
                                        <div class="bootstrap-select col-sm-10">
                                            <select class="selectpicker form-control" id="edit_survey_batch"
                                                name="edit_survey_batch[]" multiple title="Choose Batch....">
                                                <option value="All">All</option>
                                                <?php
                                                if (!empty($batchYears) && is_array($batchYears)) {
                                                    foreach ($batchYears as $batchId => $batchDetails) {
                                                        $batchYear = isset($batchDetails['batch_yrs']) ? $batchDetails['batch_yrs'] : 'Unknown';
                                                        echo "<option value=\"" . htmlspecialchars($batchId) . "\">" . htmlspecialchars($batchYear) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <small class="error-message" id="edit_survey_batch_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <!-- New field for Survey Courses -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_survey_courses" class="col-form-label">Send To:</label>
                                        <div class="bootstrap-select col-sm-10">
                                            <select class="selectpicker form-control" id="edit_survey_courses"
                                                name="edit_survey_courses[]" multiple title="Choose Course....">
                                                <option value="All">All</option>
                                                <?php
                                                if (is_array($data)) {
                                                    foreach ($data as $courseId => $details) {
                                                        $courseCode = isset($details['courCode']) ? htmlspecialchars($details['courCode']) : 'Unknown';
                                                        echo "<option value=\"" . htmlspecialchars($courseId) . "\">" . $courseCode . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <small class="error-message" id="edit_survey_courses_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="edit_address" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Timeframe</h4>
                            </label>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label for="edit_survey_start" class="col-form-label">Start Date</label>
                                        <input type="date" class="form-control" id="edit_survey_start"
                                            name="edit_survey_start">
                                        <small class="error-message" id="edit_startdate_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="edit_survey_end" class="col-form-label">End Date</label>
                                        <input type="date" class="form-control" id="edit_survey_end"
                                            name="edit_survey_end" min="<?php echo date('Y-m-d'); ?>">
                                        <small class="error-message" id="edit_enddate_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-flat pull-right btn-class" name="edit"
                            style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                                class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                                class="fa fa-close"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <div class="modal-body">
                <form id="deleteSurveyForm" class="form-horizontal" method="POST" action="survey_delete.php">
                    <input type="hidden" class="deleteId" name="id">
                    <div class="text-center">
                        <p>Are you sure you want to delete this Survey?</p>
                        <h2 class="edit_survey_title"></h2>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default pull-right btn-flat btn-class" name="delete"
                    style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
                    <i class="fa fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-flat  btn-class" data-dismiss="modal">
                    <i class="fa fa-close"></i> Close
                </button>
            </div>

            </form>
        </div>
    </div>
</div>


<style>
    .mb-3 {
        margin-bottom: 30px;
    }

    .partime-radio,
    .status-radio {
        display: none;
        /* Hide the actual radio buttons */
    }

    .radio-label {
        display: inline-block;
        cursor: pointer;
        padding: 8px 20px;
        margin-right: 10px;
        border-radius: 5px;
        background-color: #e0e0e0;
        /* Default background color */
    }

    /* Styling when radio button is checked */
    .partime-radio:checked+.radio-label,
    .status-radio:checked+.radio-label {
        background-color: #ff5252 !important;
        /* Change background color when checked */
        color: white;
        /* Change text color when checked */
    }

    .right-div {
        float: right;
        /* Float the div to the right */
        width: 30%;
        /* Adjust width as needed */
        padding: 10px;
        /* Add padding for spacing */
        border-left: 1px solid #ccc;
        /* Example border */
        height: 100%;
        /* Ensure it covers the full height if needed */
    }
</style>