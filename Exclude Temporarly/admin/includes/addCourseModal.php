<!-- Add -->
<?php
require_once 'firebaseRDB.php';

// Your Firebase Realtime Database URL
require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Fetch departments data from Firebase
$dataDepartmentts = $firebase->retrieve("departments");
$dataDepartmentts = json_decode($dataDepartmentts, true); // Decode JSON data into associative arrays


$courseData = $firebase->retrieve("course");
$courseData = json_decode($courseData, true);

echo '<script>';
echo 'var existingCourses = ' . json_encode($courseData) . ';';
echo '</script>';


?>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="course-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add Course</b></h4>
      </div>
      <div class="modal-body">
        <form id="addCourseForm" class="form-horizontal" method="POST" action="course_add.php">
          <div class="form-group">
            <label for="course_name" class="col-sm-3 control-label">Course</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="course_name" name="course_name" required>
              <small id="courseErrorMessage" style="display:none; color:red;"><i class="fa fa-info-circle"></i> This
                Course Already Exists</small>
            </div>
          </div>

          <div class="form-group">
            <label for="courCode" class="col-sm-3 control-label">Course Code</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="courCode" name="courCode" required>
              <small id="codeErrorMessage" style="display:none; color:red;"><i class="fa fa-info-circle"></i> This
                Course Code Already Exists</small>
            </div>
          </div>

          <div class="form-group">
            <label for="department" class="col-sm-3 control-label">Department</label>
            <div class="col-sm-9">
              <select class="form-control" id="department" name="department" required>
                <option value="" disabled>Select a department</option>
                <?php
                if (is_array($dataDepartmentts)) {
                  foreach ($dataDepartmentts as $departmentId => $details) {
                    $departmentName = isset($details['Department Name']) ? $details['Department Name'] : 'Unknown';
                    echo "<option value=\"" . htmlspecialchars($departmentId) . "\">" . htmlspecialchars($departmentName) . "</option>";
                  }
                }
                ?>
              </select>
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


<script>
  function fetchDepartments() {
    $.ajax({
      url: 'department_row.php',
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        var departmentSelect = $('#department');
        departmentSelect.empty(); // Clear existing options
        departmentSelect.append('<option value="" disabled>Select a department</option>');

        $.each(data, function (departmentId, details) {
          var departmentName = details['Department Name'] || 'Unknown';
          departmentSelect.append('<option value="' + departmentId + '">' + departmentName + '</option>');
        });
      },
      error: function (xhr, status, error) {
        console.error('Error fetching departments:', error);
      }
    });
  }

  // Fetch departments initially
  fetchDepartments();

  // Set interval to fetch departments periodically (e.g., every 30 seconds)

  document.getElementById('addCourseForm').addEventListener('submit', function (event) {
    var courseName = document.getElementById('course_name').value.trim();
    var courCode = document.getElementById('courCode').value.trim();
    var department = document.getElementById('department').value;

    var isCourseExisting = false;
    var isCodeExisting = false;

    for (var key in existingCourses) {
      if (existingCourses.hasOwnProperty(key)) {
        var course = existingCourses[key];
        if (course['course_name'].toLowerCase() === courseName.toLowerCase() &&
          course['department'] === department) {
          isCourseExisting = true;
        }
        if (course['courCode'].toLowerCase() === courCode.toLowerCase() &&
          course['department'] === department) {
          isCodeExisting = true;
        }
      }
    }

    if (isCourseExisting) {
      event.preventDefault();
      document.getElementById('courseErrorMessage').style.display = 'block';
    } else {
      document.getElementById('courseErrorMessage').style.display = 'none';
    }

    if (isCodeExisting) {
      event.preventDefault();
      document.getElementById('codeErrorMessage').style.display = 'block';
    } else {
      document.getElementById('codeErrorMessage').style.display = 'none';
    }
  });

  // Add event listeners for input fields to hide error messages
  document.getElementById('course_name').addEventListener('input', function () {
    document.getElementById('courseErrorMessage').style.display = 'none';
  });

  document.getElementById('courCode').addEventListener('input', function () {
    document.getElementById('codeErrorMessage').style.display = 'none';
  });

  document.getElementById('department').addEventListener('change', function () {
    document.getElementById('courseErrorMessage').style.display = 'none';
    document.getElementById('codeErrorMessage').style.display = 'none';
  });


</script>