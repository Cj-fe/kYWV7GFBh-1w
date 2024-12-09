<?php
// Include the FirebaseRDB class file
require_once 'includes/firebaseRDB.php';

require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Fetch data from Firebase
$courseKey = "course"; // Replace with your actual Firebase path or key for courses

$data = $firebase->retrieve($courseKey);
$data = json_decode($data, true); // Decode JSON data into associative arrays

$batchYears = $firebase->retrieve("batch_yr");
$batchYears = json_decode($batchYears, true); // Decode JSON data into associative arrays

$alumniData = $firebase->retrieve("alumni"); // Fetch existing alumni data
$alumniData = json_decode($alumniData, true);

echo '<script>';
echo 'var existingAlumni = ' . json_encode($alumniData) . ';';
echo '</script>';
?>

<div class="modal fade" id="addnew">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Adjust 'modal-lg' for different screen sizes -->
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h2 class="modal-title"><b>Alumni <i class="fa fa-angle-right"></i> Manage Alumni <i
              class="fa fa-angle-right"></i> Add</b></h2>
        <hr>
      </div>
      <div class="modal-body">
        <form id="addAlumniForm" class="form-horizontal" method="POST" action="alumni_listadd.php" <input type="hidden"
          name="token" value="<?php echo $_SESSION['token']; ?>">
          <div class="personal_information">
            <div class="form-group row" style="border-bottom: 1px solid silver !important;">
              <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                <h4>Personal Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="firstname" class="col-form-label">Firstname</label>
                    <input type="text" class="form-control" id="firstname" name="firstname">

                  </div>
                  <div class="col-sm-6 mb-3">
                    <label for="lastname" class="col-form-label">Lastname</label>
                    <input type="text" class="form-control" id="lastname" name="lastname">
                    <small id="studentlastnameErrorMessage" style="display:none; color:red;">
                      <i class="fa fa-info-circle"></i> This field is required
                    </small>
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="middlename" class="col-form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middlename" name="middlename">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="auxillaryname" class="col-form-label">Auxiliary Name</label>
                    <input type="text" class="form-control" id="auxiliaryname" name="auxiliaryname">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="birthdate" class="col-form-label">Birthdate</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="civilstatus" class="col-form-label">Civil Status</label>
                    <input type="text" class="form-control" id="civilstatus" name="civilstatus">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="gender" class="col-form-label">Sex</label><br>
                    <input type="radio" id="male" name="gender" value="Male" class="gender-radio">
                    <label for="male" class="radio-label">Male</label>

                    <input type="radio" id="female" name="gender" value="Female" class="gender-radio">
                    <label for="female" class="radio-label">Female</label>
                  </div>

                </div>
              </div>
            </div>

            <div class="form-group row" style="border-bottom: 1px solid silver !important;">
              <label for="address" class="col-sm-3 col-form-label text-sm-end">
                <h4>Contact Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="addressline1" class="col-form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="addressline1" name="addressline1">
                  </div>
                  <div class="col-sm-6 mb-3">
                    <label for="city" class="col-form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="state" class="col-form-label">Province</label>
                    <input type="text" class="form-control" id="state" name="state">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="zipcode" class="col-form-label">Zip Code</label>
                    <input type="text" class="form-control" id="zipcode" name="zipcode">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="contactnumber" class="col-form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contactnumber" name="contactnumber" pattern="\d{11}"
                      maxlength="11" title="Contact number must be 11 digits">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="email" class="col-form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                    <small id="emailErrorMessage" style="display:none; color:red;">
                      <i class="fa fa-info-circle"></i> This Email Already Exists
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label for="course" class="col-sm-3 col-form-label text-sm-end">
                <h4>Alumni Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="course" class="col-form-label" >Course</label>
                    <select class="form-control" id="course" name="course" required>
                      <option value="">Select Course</option>
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

                  <div class="col-sm-6 mb-3">
                    <label for="batch" class="col-form-label">Batch Year</label>
                    <select class="form-control" id="batch" name="batch" required>
                      <option value="">Select Batch Year</option>
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


                  <div class="col-sm-6 mb-3">
                    <label for="studentid" class="col-form-label">Alumni ID</label>
                    <input type="text" class="form-control" id="studentid" name="studentid">
                    <small id="studentidHelp" class="form-text text-muted">Format: 1234-5678</small>
                    <small id="studentidErrorMessage" style="display:none; color:red;">
                      <i class="fa fa-info-circle"></i> This Alumni ID Already Exists
                    </small>
                    <small id="studentidYearInfoMessage" style="display:none; color:red;">
                      <i class="fa fa-info-circle"></i> This Alumni ID Is Not Yet An Alumni, the ID should be lower to 4
                      years
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>


      </div>
      <div class="modal-footer">

        <button type="submit" class="btn btn-flat pull-right btn-class" name="add"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i class="fa fa-save"></i>
          Save</button>
        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
            class="fa fa-close"></i> Close</button>
        </form>
      </div>
    </div>
  </div>
</div>



<div class='modal fade' id='editModal' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Adjust 'modal-lg' for different screen sizes -->
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h2 class="modal-title"><b>Alumni <i class="fa fa-angle-right"></i> Manage Alumni <i
              class="fa fa-angle-right"></i> Edit</b></h2>
        <hr>
      </div>
      <div class="modal-body">
        <form id="editAlumniForm" class="form-horizontal" method="POST" action="alumni_edit.php">
          <!-- Form fields for editing alumni data -->
          <input type="hidden" id="editId" name="id">

          <div class="personal_information">
            <div class="form-group row" style="border-bottom: 1px solid silver !important;">
              <label for="edit_firstname" class="col-sm-3 col-form-label text-sm-end">
                <h4>Personal Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="edit_firstname" class="col-form-label">Firstname</label>
                    <input type="text" class="form-control" id="editFirstname" name="edit_firstname">
                  </div>
                  <div class="col-sm-6 mb-3">
                    <label for="edit_lastname" class="col-form-label">Lastname</label>
                    <input type="text" class="form-control" id="editLastname" name="edit_lastname" required>
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_middlename" class="col-form-label">Middle Name</label>
                    <input type="text" class="form-control" id="editMiddlename" name="edit_middlename">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_auxiliaryname" class="col-form-label">Auxiliary Name</label>
                    <input type="text" class="form-control" id="editAuxiliaryname" name="edit_auxiliaryname">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_birthdate" class="col-form-label">Birthdate</label>
                    <input type="date" class="form-control" id="editBirthdate" name="edit_birthdate">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_civilstatus" class="col-form-label">Civil Status</label>
                    <input type="text" class="form-control" id="editCivilstatus" name="edit_civilstatus">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_gender" class="col-form-label">Sex</label><br>
                    <input type="radio" id="editMale" name="edit_gender" value="Male" class="gender-radio">
                    <label for="edit_male" class="radio-label">Male</label>

                    <input type="radio" id="editMemale" name="edit_gender" value="Female" class="gender-radio">
                    <label for="edit_female" class="radio-label">Female</label>
                  </div>

                </div>
              </div>
            </div>

            <div class="form-group row" style="border-bottom: 1px solid silver !important;">
              <label for="edit_address" class="col-sm-3 col-form-label text-sm-end">
                <h4>Contact Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="edit_addressline1" class="col-form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="editAddressline1" name="edit_addressline1">
                  </div>
                  <div class="col-sm-6 mb-3">
                    <label for="edit_city" class="col-form-label">City</label>
                    <input type="text" class="form-control" id="editCity" name="edit_city">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_state" class="col-form-label">Province</label>
                    <input type="text" class="form-control" id="editState" name="edit_state">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_zipcode" class="col-form-label">Zip Code</label>
                    <input type="text" class="form-control" id="editZipcode" name="edit_zipcode">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_contactnumber" class="col-form-label">Contact Number</label>
                    <input type="tel" class="form-control" id="editContactnumber" name="edit_contactnumber">
                  </div>

                  <div class="col-sm-6 mb-3">
                    <label for="edit_email" class="col-form-label">Email</label>
                    <input type="email" class="form-control" id="editEmail" name="edit_email">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label for="edit_course" class="col-sm-3 col-form-label text-sm-end">
                <h4>Alumni Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <label for="edit_course" class="col-form-label">Course</label>
                    <select class="form-control" id="editCourse" name="edit_course" required>
                      <option value="">Select Course</option>
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

                  <div class="col-sm-6 mb-3">
                    <label for="edit_batch" class="col-form-label">Batch Year</label>
                    <select class="form-control" id="editBatch" name="edit_batch" required>
                      <option value="">Select Batch Year</option>
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

                  <div class="col-sm-6 mb-3">
                    <label for="edit_studentid" class="col-form-label">Alumni ID</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="editStudentid" name="edit_studentid">
                      <div class="input-group-append">
                        <span class="input-group-text reset-id" style="cursor: pointer; display: none;">Reset</span>
                      </div>
                    </div>
              
                    <small id="editstudentidErrorMessage" style="display:none; color:red;">
                      <i class="fa fa-info-circle"></i> This Alumni ID Already Exists
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-flat pull-right btn-class" name="edit_add"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
          <i class="fa fa-save"></i> Save
        </button>
        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
        </form>
      </div>
    </div>
  </div>
</div>





<!-- Delete -->
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
        <form class="form-horizontal" id="deleteAlumniForm" method="POST">
          <input type="hidden" class="deleteId" name="id">
          <div class="text-center">
            <p>School ID: <span class="editStudentid"></span></p>
            <h2 class="editFirstname editMiddlename editLastname"></h2>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default pull-right btn-flat btn-class" form="deleteAlumniForm"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
          <i class="fa fa-trash"></i> Delete
        </button>
        <button type="button" class="btn btn-flat btn-class" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>


<!--==============Import File=================-->

<div class="modal fade" id="exportnew">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h2 class="modal-title"><b>Alumni <i class="fa fa-angle-right"></i> Manage Alumni <i
              class="fa fa-angle-right"></i> Import</b></h2>
        <hr>
      </div>

      <div class="modal-body">
        <form id="importFileForm" class="form-horizontal" method="POST" action="import_file.php"
          enctype="multipart/form-data">
          <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
          <div class="personal_information">
            <div class="form-group row">
              <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                <h4>Import Information</h4>
              </label>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-12">
                    <input type="file" name="import_file" class="form-control" required />
                  </div>
                </div>
              </div>
            </div>

            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            </div>
          </div>
          <div id="uploadStatus" style="margin-top: 10px;"></div>
      </div>
      <div class="modal-footer" style="margin-top:30px">
        <button type="submit" class="btn btn-flat pull-right btn-class" name="import_excel_btn"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
          <i class="fa fa-save"></i> Save
        </button>
      </div>
      </form>
    </div>
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
              <th>Alumnni ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Sex</th>
              <th>Course</th>
              <th>Batch</th>
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

<style>
  .gender-radio {
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
  .gender-radio:checked+.radio-label {
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

  .mb-3 {
    margin-bottom: 30px;
  }

  .error {
    border-color: red;
  }

  .progress-container {
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 5px;
    background: #f3f3f3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .progress-bar {
    height: 20px;
    line-height: 20px;
    background: linear-gradient(to right, #da8cff, #9a55ff) !important;
    color: white;
    text-align: center;
    border-radius: 10px;
    font-weight: bold;
    transition: width 0.4s ease-in-out;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  /* Add some animation to make it look smoother */
  @keyframes progress-animation {
    0% {
      width: 0;
    }

    100% {
      width: 100%;
    }
  }

  /* Apply the animation to the progress bar */
  #progress-bar {
    animation: progress-animation 2s ease-in-out;
  }
</style>

<script>
  $(document).ready(function () {
  $('.datepicker-year').datepicker({
    format: 'yyyy',
    viewMode: 'years',
    minViewMode: 'years',
    autoclose: true
  });

  const studentIdInput = document.getElementById('studentid');
  const editStudentIdInput = document.getElementById('editStudentid');
  const resetIdButton = document.querySelector('.reset-id');

  function validateStudentId(input) {
    let value = input.value;

    // Check if the ID is in the 1234-5678 format
    const isStandardFormat = /^\d{4}-\d{4}$/.test(value);

    if (isStandardFormat) {
      // Standard format logic
      value = value.replace(/\D/g, ''); // Remove all non-digit characters
      if (value.length > 8) {
        value = value.substring(0, 8); // Limit to 8 digits
      }
      // Format with dash in the middle
      if (value.length > 4) {
        value = value.slice(0, 4) + '-' + value.slice(4);
      }
      input.value = value;

      const currentYear = new Date().getFullYear();
      const idYear = parseInt(value.slice(0, 4), 10);
      const isYearValid = currentYear - idYear >= 4;

      if (isYearValid) {
        input.classList.remove('error');
        document.getElementById('studentidYearInfoMessage').style.display = 'none';
      } else {
        input.classList.add('error');
        document.getElementById('studentidYearInfoMessage').style.display = 'block';
      }

     
      if (resetIdButton) resetIdButton.style.display = 'none';
      return isYearValid;
    } else {
      // Non-standard format logic (e.g., -O4VZlnaiVkvz3rSp_Fx)

      if (resetIdButton) resetIdButton.style.display = 'block';
      input.classList.remove('error');
      document.getElementById('studentidYearInfoMessage').style.display = 'none';
      return true; // Consider non-standard format as valid
    }
  }

  function generateRandomId() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_';
    let result = '-';
    for (let i = 0; i < 19; i++) {
      result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
  }

  if (studentIdInput) {
    studentIdInput.addEventListener('input', function () {
      validateStudentId(this);
    });
  }

  if (editStudentIdInput) {
    editStudentIdInput.addEventListener('input', function () {
      validateStudentId(this);
    });
  }

  if (resetIdButton) {
    resetIdButton.addEventListener('click', function () {
      editStudentIdInput.value = generateRandomId();
      validateStudentId(editStudentIdInput);
    });
  }

  // Initial validation on page load
  if (editStudentIdInput) {
    validateStudentId(editStudentIdInput);
  }

  document.getElementById('contactnumber').addEventListener('input', function (e) {
    const input = e.target;
    input.value = input.value.replace(/\D/g, ''); // Remove non-digit characters
  });

  // Handle form submission for adding alumni
  document.getElementById('addAlumniForm').addEventListener('submit', function (event) {
    var email = document.getElementById('email').value.trim();
    var studentId = document.getElementById('studentid').value.trim();

    var isEmailExisting = false;
    var isStudentIdExisting = false;

    // Ensure existingAlumni is defined
    if (typeof existingAlumni !== 'undefined') {
      for (var key in existingAlumni) {
        if (existingAlumni.hasOwnProperty(key)) {
          var alumni = existingAlumni[key];
          if (alumni['email'].toLowerCase() === email.toLowerCase()) {
            isEmailExisting = true;
          }
          if (alumni['studentid'].toLowerCase() === studentId.toLowerCase()) {
            isStudentIdExisting = true;
          }
        }
      }
    }

    // Display error messages and prevent form submission if needed
    if (isEmailExisting) {
      event.preventDefault();
      document.getElementById('emailErrorMessage').style.display = 'block';
    } else {
      document.getElementById('emailErrorMessage').style.display = 'none';
    }

    if (isStudentIdExisting) {
      event.preventDefault();
      document.getElementById('studentidErrorMessage').style.display = 'block';
    } else {
      document.getElementById('studentidErrorMessage').style.display = 'none';
    }

    if (!validateStudentId(studentIdInput)) {
      event.preventDefault();
    }
  });

  // Handle form submission for editing alumni
  document.getElementById('editAlumniForm').addEventListener('submit', function (event) {
    var email = document.getElementById('editEmail').value.trim();
    var studentId = document.getElementById('editStudentid').value.trim();

    var isEditEmailExisting = false;
    var isEditStudentIdExisting = false;

    // Ensure existingAlumni is defined
    if (typeof existingAlumni !== 'undefined') {
      for (var key in existingAlumni) {
        if (existingAlumni.hasOwnProperty(key)) {
          var alumni = existingAlumni[key];
          if (alumni['email'].toLowerCase() === email.toLowerCase()) {
            isEditEmailExisting = true;
          }
          if (alumni['studentid'].toLowerCase() === studentId.toLowerCase()) {
            isEditStudentIdExisting = true;
          }
        }
      }
    }

    // Display error messages and prevent form submission if needed
    if (isEditEmailExisting) {
      event.preventDefault();
      document.getElementById('editEmailErrorMessage').style.display = 'block';
    } else {
      document.getElementById('editEmailErrorMessage').style.display = 'none';
    }

    if (isEditStudentIdExisting) {
      event.preventDefault();
      document.getElementById('editStudentidErrorMessage').style.display = 'block';
    } else {
      document.getElementById('editStudentidErrorMessage').style.display = 'none';
    }

    if (!validateStudentId(editStudentIdInput)) {
      event.preventDefault();
    }
  });
});
</script>