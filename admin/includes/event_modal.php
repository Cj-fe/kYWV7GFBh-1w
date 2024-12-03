<!-- Add -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.replace) {
            CKEDITOR.replace('event_description');
        } else {
            console.error('CKEditor is not defined or replace method is missing.');
        }
    });

    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }


    function validateAddEventForm() {
        var isValid = true;

        // Synchronize CKEditor content with the textarea
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['event_description']) {
            CKEDITOR.instances['event_description'].updateElement();
        }

        // Validate event_title
        var eventTitle = document.getElementById('event_title').value;
        var eventTitleError = document.getElementById('event_title_error');
        if (eventTitle.trim() === "") {
            eventTitleError.style.display = 'block';
            isValid = false;
        } else {
            eventTitleError.style.display = 'none';
        }

        // Validate event_date
        var eventDate = document.getElementById('event_date').value;
        var eventDateError = document.getElementById('event_date_error');
        if (eventDate.trim() === "") {
            eventDateError.style.display = 'block';
            isValid = false;
        } else {
            eventDateError.style.display = 'none';
        }

        // Validate event_venue
        var eventVenue = document.getElementById('event_venue').value;
        var eventVenueError = document.getElementById('event_venue_error');
        if (eventVenue.trim() === "") {
            eventVenueError.style.display = 'block';
            isValid = false;
        } else {
            eventVenueError.style.display = 'none';
        }

        // Validate event_author
        var eventAuthor = document.getElementById('event_author').value;
        var eventAuthorError = document.getElementById('event_author_error');
        if (eventAuthor.trim() === "") {
            eventAuthorError.style.display = 'block';
            isValid = false;
        } else {
            eventAuthorError.style.display = 'none';
        }

        // Validate event_description
        var eventDescription = document.getElementById('event_description').value;
        var eventDescriptionError = document.getElementById('event_description_error');
        if (eventDescription.trim() === "") {
            eventDescriptionError.style.display = 'block';
            isValid = false;
        } else {
            eventDescriptionError.style.display = 'none';
        }

        // Validate imageUploadAdd
        var imageUploadAdd = document.getElementById('imageUploadAdd').value;
        var imageUploadAddError = document.getElementById('imageUploadAdd_error');
        if (imageUploadAdd.trim() === "") {
            imageUploadAddError.style.display = 'block';
            isValid = false;
        } else {
            imageUploadAddError.style.display = 'none';
        }

        if (isValid) {
            document.getElementById('submitButton').disabled = true;
        }

        return isValid;
    }

    function validateEditEventForm() {
        var isValid = true;

        // Validate editTitle
        var editTitle = document.getElementById('editTitle').value;
        var editTitleError = document.getElementById('editTitle_error');
        if (editTitle.trim() === "") {
            editTitleError.style.display = 'block';
            isValid = false;
        } else {
            editTitleError.style.display = 'none';
        }

        // Validate editEventDate
        var editEventDate = document.getElementById('editEventDate').value;
        var editEventDateError = document.getElementById('editEventDate_error');
        if (editEventDate.trim() === "") {
            editEventDateError.style.display = 'block';
            isValid = false;
        } else {
            editEventDateError.style.display = 'none';
        }

        // Validate editEventVenue
        var editEventVenue = document.getElementById('editEventVenue').value;
        var editEventVenueError = document.getElementById('editEventVenue_error');
        if (editEventVenue.trim() === "") {
            editEventVenueError.style.display = 'block';
            isValid = false;
        } else {
            editEventVenueError.style.display = 'none';
        }

        // Validate editAuthor
        var editAuthor = document.getElementById('editAuthor').value;
        var editAuthorError = document.getElementById('editAuthor_error');
        if (editAuthor.trim() === "") {
            editAuthorError.style.display = 'block';
            isValid = false;
        } else {
            editAuthorError.style.display = 'none';
        }

        // Validate editDesc
        var editDesc = document.getElementById('editDesc').value;
        var editDescError = document.getElementById('editDesc_error');
        if (editDesc.trim() === "") {
            editDescError.style.display = 'block';
            isValid = false;
        } else {
            editDescError.style.display = 'none';
        }

        return isValid;
    }

    $(document).ready(function () {
        $('.selectpicker').selectpicker();

        $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            var selectedOptions = $(this).val();
            $('#selected-options').val(selectedOptions.join(', '));
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
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Event <i
                            class="fa fa-angle-right"></i> Add</b></h2>
            </div>
            <div class="modal-body" style="padding: 0 !important;">
                <form id="addEventForm" class="form-horizontal flex-container" method="POST" action="event_add.php"
                    enctype="multipart/form-data" onsubmit="return validateAddEventForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <div class="form-container">
                        <div class="form-group">
                            <label for="event_title" class="col-sm-2 control-label">New Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="event_title" name="event_title">
                                <small class="error-message" id="event_title_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event_date" class="col-sm-2 control-label">Event Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="event_date" name="event_date" min="<?php $currentDate = date('Y-m-d');
                                echo $currentDate; ?>">
                                <small class="error-message" id="event_date_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event_venue" class="col-sm-2 control-label">Event Venue</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="event_venue" name="event_venue">
                                <small class="error-message" id="event_venue_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event_author" class="col-sm-2 control-label">Author</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="event_author" name="event_author">
                                <small class="error-message" id="event_author_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="course_invited" class="col-sm-2 control-label">Course Invited</label>
                            <div class="bootstrap-select col-sm-10">
                                <select class="selectpicker form-control" id="course_invited" name="course_invited[]"
                                    multiple title="Choose Course....">
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

                            <small class="error-message" id="event_invited_error" style="color:red; display:none;"><i
                                    class="fa fa-info-circle"></i> This field is
                                required.</small>
                        </div>
                        <div class="form-group">
                            <label for="event_invited" class="col-sm-2 control-label">Batch Invited</label>
                            <div class="bootstrap-select col-sm-10">
                                <select class="selectpicker form-control" id="event_invited" name="event_invited[]"
                                    multiple title="Choose Batch....">
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

                            <small class="error-message" id="event_invited_error" style="color:red; display:none;"><i
                                    class="fa fa-info-circle"></i> This field is
                                required.</small>
                        </div>
                        <div class="form-group">
                            <label for="event_description" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="event_description"
                                    name="event_description"></textarea>
                                <small class="error-message" id="event_description_error"
                                    style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is
                                    required.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="submitButton" class="btn btn-flat pull-right btn-class"
                                style="background: linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                                    class="fa fa-save"></i> Save</button>
                            <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                                    class="fa fa-close"></i> Close</button>
                        </div>
                    </div>
                    <div class="right-div">
                        <div class="image-upload-container">
                            <div class="image-preview" id="imagePreviewAdd">
                                <img id="imagePreviewImg" src="" alt="Image Preview" style="display:none;">
                                <div class="upload-controls">
                                    <label for="imageUploadAdd" class="image-upload-label">Edit</label>
                                    <input type="file" id="imageUploadAdd" name="imageUpload" accept="image/*"
                                        onchange="previewImage(event, 'imagePreviewImg')" style="display:none;">
                                </div>
                            </div>
                            <h5 class="h5">Thumbnail</h5>
                            <small class="error-message" id="imageUploadAdd_error" style="color:red; display:none;"><i
                                    class="fa fa-info-circle"></i> This field is
                                required.</small>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Edit <i
                            class="fa fa-angle-right"></i> Edit</b></h2>
            </div>
            <div class="modal-body" style="padding: 0 !important;">
                <form id="editEventForm" class="form-horizontal flex-container" method="POST" action="event_edit.php"
                    enctype="multipart/form-data" onsubmit="return validateEditEventForm()">
                    <div class="form-container">
                        <input type="hidden" name="id" id="editId" value=""> <!-- Hidden input for ID -->
                        <div class="form-group">
                            <label for="editTitle" class="col-sm-2 control-label">New Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editTitle" name="edit_title">
                                <small class="error-message" id="editTitle_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="editEventDate" class="col-sm-2 control-label">Event Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="editEventDate" name="edit_date">
                                <small class="error-message" id="editEventDate_error"
                                    style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is
                                    required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editEventVenue" class="col-sm-2 control-label">Event Venue</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editEventVenue" name="edit_venue">
                                <small class="error-message" id="editEventVenue_error"
                                    style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is
                                    required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editAuthor" class="col-sm-2 control-label">Author</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editAuthor" name="edit_author">
                                <small class="error-message" id="editAuthor_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_course_invited" class="col-sm-2 control-label">Course Invited</label>
                            <div class="bootstrap-select col-sm-10">
                                <select class="selectpicker form-control" id="edit_course_invited"
                                    name="edit_course_invited[]" multiple title="Choose Course....">
                                    <?php
                                    if (is_array($data)) {
                                        foreach ($data as $courseId => $details) {
                                            $courseCode = isset($details['courCode']) ? htmlspecialchars($details['courCode']) : 'Unknown';
                                            echo "<option value=\"" . htmlspecialchars($courseId) . "\" data-content=\"" . $courseCode . "\">" . $courseCode . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_event_invited" class="col-sm-2 control-label">Batch Invited</label>
                            <div class="bootstrap-select col-sm-10">
                                <select class="selectpicker form-control" id="edit_event_invited"
                                    name="edit_event_invited[]" multiple title="Choose Batch....">
                                    <?php
                                    if (!empty($batchYears) && is_array($batchYears)) {
                                        foreach ($batchYears as $batchId => $batchDetails) {
                                            $batchYear = isset($batchDetails['batch_yrs']) ? $batchDetails['batch_yrs'] : 'Unknown';
                                            echo "<option value=\"" . htmlspecialchars($batchId) . "\" data-content=\"" . $batchYear . "\">" . $batchYear . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="editDesc" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="editDesc" name="edit_description"></textarea>
                                <small class="error-message" id="editDesc_error" style="color:red; display:none;"><i
                                        class="fa fa-info-circle"></i> This field is required.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-flat pull-right btn-class"
                                style="background: linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                                    class="fa fa-save"></i> Save</button>
                            <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                                    class="fa fa-close"></i> Close</button>
                        </div>
                    </div>
                    <div class="right-div">
                        <div class="image-upload-container">
                            <div class="image-preview" id="imagePreviewEdit">
                                <img id="imagePreviewImg2" src="" alt="Image Preview" style="display:none;">
                                <div class="upload-controls">
                                    <label for="imageUploadEdit" class="image-upload-label">Edit</label>
                                    <input type="file" id="imageUploadEdit" name="imageUpload" accept="image/*"
                                        onchange="previewImage(event, 'imagePreviewImg2')" style="display:none;">
                                </div>
                            </div>
                            <h5 class="h5">Thumbnail</h5>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <div class="modal-body">
                <form id="deleteEventForm" class="form-horizontal" method="POST" action="event_delete.php">
                    <input type="hidden" class="deleteId" name="id">
                    <div class="text-center">
                        <!-- event Image -->

                        <div class="img-container">
                            <div class="overlay">
                                <a id="imageLink" href="#" target="_blank" class="btn btn-danger btn-flat">View
                                    Image</a>

                            </div>
                            <img id="imagePreviewImg3" src="" alt="Image Preview" style="display:none;">
                        </div>


                        <br><br>

                        <h3 class="title"></h3>
                        <!-- event Content -->
                        <div class="description-container">
                            <!-- Description content will be inserted here -->
                        </div>
                    </div>
            </div>
            <div class="modal-footer">

                <button type="submit" class="btn btn-default pull-right btn-flat btn-class" name="delete"
                    style="background:#EE4E4E; color:white;">
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



<!--==============Print File=================-->
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Alumni Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="printTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th> <!-- Checkbox column -->
                            <th>Alumni ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Date Responded</th>
                            <th>Event Participated</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody">
                        <!-- Data will be copied here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger mb-2" id="removeSelectedButton">Remove Selected</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printModalButton">Print</button>
            </div>
        </div>
    </div>
</div>



<!--========FOR DELETE=========-->
<style>
    .img-container {
        position: relative;
        width: 100%;
        height: 200px;

        overflow: hidden;
    }

    .overlay {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #333;
        transition: top 0.3s ease;
        opacity: 0.5;
    }

    .img-container:hover .overlay {
        top: 0;
    }

    .img-container img {
        width: 100%;
        height: auto;
        display: block;
    }

    .overlay-text {
        color: #ffffff;
        /* Text color */
        font-size: 18px;
        /* Font size */
        text-align: center;
        /* Center text */
        padding: 10px;
        /* Padding around text */
    }

    .title,
    .description-container h1,
    .description-container h2,
    .description-container h3,
    .description-container h4,
    .description-container h5,
    .description-container h6 {
        text-align: left;
        margin-right: 90px;
    }



    .description-container p {
        text-align: justify;
        margin-right: 90px;
    }

    .title,
    .description-container {
        font-family: Arial, Helvetica, sans-serif;


        padding: 40px;
    }
</style>