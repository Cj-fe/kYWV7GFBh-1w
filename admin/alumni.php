<?php include 'includes/session.php'; ?>

<head>
<?php include 'includes/header.php'; ?>

  <?php
  $coursesData = $firebase->retrieve("course");
  $departmentsData = $firebase->retrieve("departments");
  $batchYearsData = $firebase->retrieve("batch_yr");
  $alumniData = $firebase->retrieve("alumni");

  // Decode JSON data into associative arrays
  $courses = json_decode($coursesData, true) ?: [];
  $departments = json_decode($departmentsData, true) ?: [];
  $batchYears = json_decode($batchYearsData, true) ?: [];
  $alumni = json_decode($alumniData, true) ?: [];

  // Prepare an array to store courses filtered by department ID
  $filteredCourses = [];

  // Count the total number of alumni
  $totalAlumniCount = count($alumni);

  // Iterate through courses and filter by department ID
  foreach ($courses as $courseId => $course) {
    $departmentId = isset($course['department']) ? $course['department'] : null;

    if ($departmentId && isset($departments[$departmentId])) {
      $departmentName = isset($departments[$departmentId]['Department Name']) ? $departments[$departmentId]['Department Name'] : 'Unknown';

      // Initialize array to store batch years
      $courseBatchYears = [];

      // Find batch years for current course
      foreach ($batchYears as $batchId => $batch) {
        $courseBatchYears[$batchId] = $batch['batch_yrs'];
      }

      // Sort batch years in ascending order
      asort($courseBatchYears);

      // Build the filtered course data
      $filteredCourses[$departmentName][] = [
        'courseId' => $courseId,
        'courseCode' => isset($course['courCode']) ? $course['courCode'] : 'Unknown',
        'courseName' => isset($course['course_name']) ? $course['course_name'] : 'Unknown',
        'batchYears' => $courseBatchYears
      ];
    }
  }
  ?>
  <style>
    .box-header {
      position: relative;
      padding-top: 10px;
      border-radius: 0px !important;
      /* Adjust padding to accommodate the pseudo-element */
    }


    .table-responsive {
      overflow-x: auto;
      scrollbar-width: thin;
      /* For Firefox */
      scrollbar-color: #3c8dbc #f0f0f0;
      /* For Firefox */
    }

    .table-responsive {
      overflow-x: auto;
      scrollbar-width: thin;
      /* For Firefox */
      scrollbar-color: #3c8dbc #f0f0f0;
      /* For Firefox */
    }

    .table-responsive::-webkit-scrollbar {
      height: 8px;
      /* Height of the scrollbar */
    }

    .table-responsive::-webkit-scrollbar-track {
      background: #f0f0f0;
      /* Track color */
    }

    .table-responsive::-webkit-scrollbar-thumb {
      background-color: #3c8dbc;
      /* Thumb color */
      border-radius: 10px;
      /* Rounded corners */
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
      background-color: #2a6b8c;
      /* Darker shade for hover effect */
    }

    .table-bordered tbody tr:hover {
      background-color: #3c8dbc;
      /* Background color on hover */
      color: white;
      /* Text color on hover */
    }

    .table-bordered tbody tr:hover td {
      color: white;
      /* Ensure text color changes for all cells */
    }
  </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper content-flex">
      <!-- Main container -->
      <div class="main-container">
        <!-- Content Header (Page header) -->
        <section class="content-header box-header-background">
          <h1>
            Alumni List
          </h1>
          <div class="box-inline">
            <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-plus-circle"></i>&nbsp;&nbsp; New
            </a>

            <a href="#exportnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Import
            </a>

            <!-- <a href="#print" data-toggle="modal" id="showModalButton"
              class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-print"></i>&nbsp;&nbsp; Print
            </a>-->

            <div class="search-container">
              <input type="text" class="search-input" id="search-input" placeholder="Search...">
              <button class="search-button" onclick="filterTable()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="feather feather-search">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </button>
            </div>
          </div>

          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Alumni</li>
            <li class="active" style="color:white">Alumni List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <?php
          if (isset($_SESSION['error'])) {
            $errorMessage = addslashes($_SESSION['error']);
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showAlert('error', '{$errorMessage}');
                    });
                    </script>";
            unset($_SESSION['error']);
          }
          if (isset($_SESSION['success'])) {
            $successMessage = addslashes($_SESSION['success']);
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showAlert('success', '{$successMessage}');
                    });
                    </script>";
            unset($_SESSION['success']);
          }
          ?>

          <div class="row">
            <!-- Manage Alumni Section -->
            <div class="col-md-3">
              <div class="box box-solid" style="border-radius: 0% !important; ">
                <div class="box-header with-border"
                  style="background:white !important; border-top: 5px solid #3c8dbc !important;">
                  <h3 class="box-title">Manage Alumni</h3>
                  <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body no-padding">
                  <div class="manage-alumni">
                    <button type="button" class="btn-gradient-info btn-fw" id="toggle-button">
                      <span class="menu-title">Manage Alumni</span>
                      <i class="fa fa-cog menu-icon"></i>
                    </button>
                    <center>
                      <ul id="dropdown-menu">
                        <br>
                        <li><a class="sub-side-modal" data-toggle="modal" data-target="#addDepartment"><i
                              class="fa fa-building"></i>&nbsp; Add Department</a></li>
                        <li><a class="sub-side-modal" data-toggle="modal" data-target="#course-modal"><i
                              class="fa fa-laptop"></i>&nbsp; Add Course</a></li>
                        <li><a class="sub-side-modal" data-toggle="modal" data-target="#batch-modal"><i
                              class="fa fa-laptop"></i>&nbsp; Add Batch</a></li>
                      </ul>
                    </center>
                  </div>
                  <div>
                    <div class="alumni-count-container">
                      <span class="all-alumni"><a href="alumni.php">All Alumni</a></span>
                      <div class="count"><?php echo $totalAlumniCount; ?></div>
                    </div>
                  </div>
                  <hr>
                  <?php
                  // Output courses grouped by department
                  foreach ($filteredCourses as $departmentName => $courses) {
                    echo '<button class="collapsible transparent">' . htmlspecialchars($departmentName) . '<i class="fa fa-angle-right arrow"></i></button>';
                    echo '<div class="contents">';
                    foreach ($courses as $course) {
                      echo '<button class="collaps-department transparent" data-course-id="' . htmlspecialchars($course['courseId']) . '">' . htmlspecialchars($course['courseCode']) . '<i class="fa fa-angle-right arrow"></i></button>';
                      echo '<div class="content-batch">';
                      foreach ($course['batchYears'] as $batchId => $batchYear) {
                        echo '<button class="collaps-year transparent" data-course-id="' . htmlspecialchars($course['courseId']) . '" data-batch-id="' . htmlspecialchars($batchId) . '">' . htmlspecialchars($batchYear) . '</button>';
                      }
                      echo '</div>';
                    }
                    echo '</div>';
                  }
                  ?>
                </div>

              </div>

            </div>


            <!-- Table Section -->
            <div class="col-md-9">
              <div class="table-container">
                <div class="box" style="border-radius: 0px !important; ">

                  <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                    <div class="table-responsive">
                      <table id="example1" class="table table-bordered printable-table">
                        <thead>
                          <tr>
                            <th style="display:none;"></th>
                            <th>Alumni ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Tools</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php include 'fetch_data/fetch_dataAlumni.php' ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <script>
            document.querySelectorAll('.collaps-year').forEach(button => {
              button.addEventListener('click', function () {
                const courseId = this.getAttribute('data-course-id');
                const batchId = this.getAttribute('data-batch-id');
                window.location.href = `alumni.php?course=${courseId}&batch=${batchId}`;
              });
            });
          </script>
        </section>
      </div>

      <!-- Sidebar -->

    </div>


    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/alumniAddModal.php'; ?>
    <?php include 'includes/addDepartmentModal.php' ?>
    <?php include 'includes/addCourseModal.php' ?>
    <?php include 'includes/addBatchModal.php' ?>
    <?php include 'includes/import_excelModal.php' ?>


  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>

    $(document).ready(function () {
      // Use event delegation to handle edit modal
      $(document).on('click', '.open-modal', function () {
        var id = $(this).data('id');
        $.ajax({
          url: 'alumni_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            $('#editId').val(id);
            $('#editFirstname').val(response.firstname);
            $('#editLastname').val(response.lastname);
            $('#editMiddlename').val(response.middlename);
            $('#editAuxiliaryname').val(response.auxiliaryname);
            $('#editBirthdate').val(response.birthdate);
            $('#editCivilstatus').val(response.civilstatus);
            $('#editMale').prop('checked', response.gender === 'Male');
            $('#editMemale').prop('checked', response.gender === 'Female');
            $('#editAddressline1').val(response.addressline1);
            $('#editCity').val(response.city);
            $('#editState').val(response.state);
            $('#editZipcode').val(response.zipcode);
            $('#editContactnumber').val(response.contactnumber);
            $('#editEmail').val(response.email);
            $('#editCourse').val(response.course);
            $('#editBatch').val(response.batch);
            $('#editStudentid').val(response.studentid);

            // Show the edit modal
            $('#editModal').modal('show');
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
          }
        });
      });

      // Use event delegation to handle delete modal
      $(document).on('click', '.open-delete', function () {
        var id = $(this).data('id');
        $.ajax({
          url: 'alumni_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            // Populate modal with alumni name
            $('.deleteId').val(id);
            var fullName = response.firstname + ' ' + response.middlename + ' ' + response.lastname;
            $('.editFirstname, .editMiddlename, .editLastname').text(fullName);
            $('.editStudentid').text(response.studentid);

            // Show the delete confirmation modal
            $('#deleteModal').modal('show');

            // Store the ID in a data attribute of the delete button
            $('.btn-confirm-delete').data('id', id);
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
          }
        });
      });
    });


    // Toggle dropdown menu
    document.getElementById('toggle-button').addEventListener('click', function () {
      var dropdownMenu = document.getElementById('dropdown-menu');
      dropdownMenu.classList.toggle('show');
    });

    document.addEventListener("DOMContentLoaded", function () {
      function closeAllExcept(current, className) {
        var coll = document.getElementsByClassName(className);
        for (var i = 0; i < coll.length; i++) {
          if (coll[i] !== current) {
            coll[i].classList.remove("active");
            var content = coll[i].nextElementSibling;
            if (content) {
              content.classList.remove("active");
            }
          }
        }
      }

      function toggleContent(className) {
        var coll = document.getElementsByClassName(className);
        for (var i = 0; i < coll.length; i++) {
          coll[i].addEventListener("click", function () {
            // Close all other collapsibles of the same class
            closeAllExcept(this, className);

            // Toggle the clicked collapsible
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content) {
              content.classList.toggle("active");
            }
          });
        }
      }

      toggleContent("collapsible");
      toggleContent("collaps-department");
      toggleContent("collaps-year");
    });




  </script>
  <script>
    $(document).ready(function () {
      // Handle alumni delete form submission
      $('#deleteModal form').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
          type: 'POST',
          url: 'alumni_delete.php', // The URL of your PHP script for deleting alumni
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
            $('#deleteModal').modal('hide'); // Hide the modal after the operation
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      // Function to display SweetAlert messages
      function showAlert(type, message) {
        Swal.fire({
          position: 'top-end',
          icon: type,
          title: message,
          showConfirmButton: false,
          timer: 2500,
          willClose: () => {
            if (type === 'success') {
              location.reload(); // Reload the page after the success message
            }
          }
        });
      }

      // Existing form submissions (for add operations)
      $('#addAlumniForm').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
          type: 'POST',
          url: 'alumni_listadd.php', // The URL of your PHP script for alumni
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#editAlumniForm').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
          type: 'POST',
          url: 'alumni_edit.php', // The URL of your PHP script for editing alumni
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else if (response.status === 'info') {
              showAlertEdit('info', response.message);
            } else {
              showAlert('error', response.message);
            }
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#addDepartmentForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'department_add.php',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#addCourseForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'course_add.php',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#addBatchForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'batch_add.php',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
          },
          error: function () {
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#importFileForm').on('submit', function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        var uploadStatus = $('#uploadStatus');
        var progressBar = $('.progress-bar');
        var progressContainer = $('.progress-container');

        uploadStatus.text('');
        progressContainer.show();
        progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');

        var progress = 0;
        var intervalId = setInterval(function () {
          if (progress < 90) {
            progress += 1;
            progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
          }
        }, 100);

        $.ajax({
          type: 'POST',
          url: 'import_file.php',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function (response) {
            clearInterval(intervalId);
            if (response.status === 'success') {
              var completeProgress = setInterval(function () {
                if (progress < 100) {
                  progress += 1;
                  progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
                } else {
                  clearInterval(completeProgress);
                  progressBar.removeClass('progress-bar-animated').addClass('bg-success');
                  uploadStatus.text('Upload Completed');
                  progressContainer.hide();
                  showAlert('success', response.message);
                }
              }, 50);
            } else {
              progressContainer.hide();
              uploadStatus.text('Upload Failed');
              showAlertEdit('info', response.message);
            }
          },
          error: function () {
            clearInterval(intervalId);
            progressContainer.hide();
            uploadStatus.text('Upload Failed');
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
        let title = 'Import Results';

        // Split the message into an array if it contains <br> tags
        let messageArray = message.split('<br>').filter(msg => msg.trim() !== '');

        let messageHtml = '<ul style="text-align: left; list-style-position: inside; max-height: 300px; overflow-y: auto;">';
        messageArray.forEach(msg => {
          messageHtml += `<li>${msg}</li>`;
        });
        messageHtml += '</ul>';

        Swal.fire({
          icon: iconType,
          title: title,
          html: messageHtml,
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