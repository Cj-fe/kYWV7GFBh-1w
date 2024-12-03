<?php
require_once 'firebaseRDB.php';

// Initialize Firebase URL
require_once 'config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

$adminData = $firebase->retrieve("departments");
$adminData = json_decode($adminData, true);

echo '<script>';
echo 'var existingDepartments = ' . json_encode($adminData) . ';';
echo '</script>';
?>

<!-- Modal -->
<div class="modal fade" id="addDepartment">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Department</b></h4>
      </div>
      <div class="modal-body">
        <form id="addDepartmentForm"  class="form-horizontal" method="POST" action="department_add.php">
          <div class="form-group">
            <label for="departmentName" class="col-sm-3 control-label">Department Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="departmentName" name="departmentName" required>
              <small id="errorMessage" style="display:none; color:red;"><i class="fa fa-info-circle"></i> This Department Already Exists</small>
            </div>  
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-flat pull-right btn-class" name="add"
            style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i class="fa fa-save"></i>
            Save</button>
            <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                class="fa fa-close"></i> Close</button>
          </div>
        </form>
      </div> 
    </div>
  </div>
</div>

<script>
document.getElementById('departmentName').addEventListener('input', function() {
  document.getElementById('errorMessage').style.display = 'none';
});

document.getElementById('addDepartmentForm').addEventListener('submit', function(event) {
  var departmentName = document.getElementById('departmentName').value.trim();
  var isExisting = false;

  for (var key in existingDepartments) {
    if (existingDepartments.hasOwnProperty(key)) {
      if (existingDepartments[key]['Department Name'].toLowerCase() === departmentName.toLowerCase()) {
        isExisting = true;
        break;
      }
    }
  }

  if (isExisting) {
    event.preventDefault();
    document.getElementById('errorMessage').style.display = 'block';
  }
});


/*===========AJAX==========
$(document).ready(function() {
    $('form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
            type: 'POST',
            url: 'department_add.php', // Update with the correct path
            data: formData,
            dataType: 'json',
            success: function(response) {
                var timer = 2500; // Timer duration in milliseconds
                if (response.status === 'success') {
                    showAlert('success', response.message, timer, true); // Set reload flag to true
                } else {
                    showAlert('error', response.message, timer, false); // Set reload flag to false
                }
            },
            error: function() {
                var timer = 2500; // Timer duration in milliseconds
                showAlert('error', 'An unexpected error occurred.', timer, false); // Set reload flag to false
            }
        });
    });

    function showAlert(type, message, timer, shouldReload) {
        Swal.fire({
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            didClose: () => {
                // Reload the page only if shouldReload is true
                if (shouldReload) {
                    location.reload();
                }
            }
        });
    }
});*/


</script>
