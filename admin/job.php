<?php include 'includes/session.php'; ?>

<head>
<?php include 'includes/header.php'; ?>

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
<html>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header box-header-background">
        <h1>
          Content <i class="fa fa-angle-right"></i> Job
        </h1>
        <div class="box-inline">
          <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat"><i
              class="fa fa-plus-circle"></i>&nbsp;&nbsp; New</a>
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
          <li>Content</li>
          <li class="active" style="color:white">Job</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "<script>
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '" . $_SESSION['error'] . "',
                  });
                </script>";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "<script>
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '" . $_SESSION['success'] . "',
                  });
                </script>";
          unset($_SESSION['success']);
        }
        ?>
        <div class="row">
          <div class="col-xs-12">
            <div class="box" style="border-radius: 0px !important; ">
            <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered">
                    <thead>
                      <th>Status</th>
                      <th width="15%">Work Type</th>
                      <th>Job Title</th>
                      <th>Company</th>
                      <th>Description</th>
                      <th>Date Posted</th>
                      <th width="10%">Tools</th>
                    </thead>
                    <tbody>
                      <?php include 'fetch_data/fetch_dataJob.php'; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/job_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script>
$(document).ready(function () {
  var originalData = {};
  var currentEditors = {};

  function fetcheventData(id, successCallback, errorCallback) {
    $.ajax({
      url: 'job_row.php',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: successCallback,
      error: errorCallback
    });
  }

  function initializeCKEditor(elementId) {
    if (window.CKEDITOR && CKEDITOR.replace) {
      // Destroy existing instance if it exists
      if (currentEditors[elementId]) {
        currentEditors[elementId].destroy();
      }
      // Create new instance
      currentEditors[elementId] = CKEDITOR.replace(elementId);
    } else {
      console.error('CKEditor is not defined or replace method is missing.');
    }
  }

  $('.open-modal').click(function () {
    var id = $(this).data('id');
    fetcheventData(id, function (response) {
      $('#editId').val(id);
      $('#edit_job_title').val(response.job_title);
      $('#edit_company_name').val(response.company_name);
      $('#edit_location').val(response.location);
      $('#edit_salary_range').val(response.salary_range);
      $('#edit_contact_email').val(response.contact_email);

      $('#edit_parttime').prop('checked', response.work_time === 'Part-Time');
      $('#edit_fulltime').prop('checked', response.work_time === 'Full-Time');
      $('#edit_statusActive').prop('checked', response.status === 'Active');
      $('#edit_statusArchive').prop('checked', response.status === 'Archive');

      // Initialize Select2
      $('#edit_job_categories').select2();

      // Populate job categories
      if (response.job_categories && Array.isArray(response.job_categories)) {
        $('#edit_job_categories').val(response.job_categories).trigger('change');
      }

      if (response.image_path && response.image_path.trim() !== '') {
        $('#imagePreviewImg2').attr('src', response.image_path).show();
      } else {
        $('#imagePreviewImg2').hide();
      }

      if (response.logo_path && response.logo_path.trim() !== '') {
        $('#imagePreviewImgLogo2').attr('src', response.logo_path).show();
      } else {
        $('#imagePreviewImgLogo2').hide();
      }

      // Set the textarea values before initializing CKEditor
      $('#edit_description').val(response.job_description);
      $('#edit_expertise_specification').val(response.expertise_specification);
      $('#edit_about_the_role').val(response.about_the_role);

      originalData = {
        job_title: response.job_title,
        company_name: response.company_name,
        work_time: response.work_time,
        status: response.status,
        job_description: response.job_description,
        expertise_specification: response.expertise_specification,
        about_the_role: response.about_the_role,
        image_path: response.image_path
      };

      $('#editModal').modal('show');

      // Use a setTimeout to ensure the modal is fully shown before initializing CKEditor
      setTimeout(function () {
        initializeCKEditor('edit_description');
        initializeCKEditor('edit_expertise_specification');
        initializeCKEditor('edit_about_the_role');
      }, 100);
    }, function (xhr, status, error) {
      console.error('AJAX Error: ' + status + ' ' + error);
    });
  });

  $('#editModal').on('hidden.bs.modal', function () {
    // Destroy all CKEditor instances when the modal is closed
    for (var elementId in currentEditors) {
      if (currentEditors.hasOwnProperty(elementId)) {
        currentEditors[elementId].destroy();
        delete currentEditors[elementId];
      }
    }
  });

  // Open delete modal when delete button is clicked
  $('.open-delete').click(function () {
    var id = $(this).data('id');

    // Make an AJAX request to fetch job details
    $.ajax({
      url: 'job_row.php',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function (response) {
        // Populate modal with alumni name
        $('.deleteId').val(id); // Update value of deleteId input field
        $('.deleteTitle').text(response.job_title);

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

  $('#addJobForm').on('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    var formData = new FormData(this); // Create FormData object

    // Manually handle multiple select if needed
    var jobCategories = $('#job_categories').val(); // Get the selected categories
    formData.append('job_categories', JSON.stringify(jobCategories)); // Convert to JSON string

    $.ajax({
      type: 'POST',
      url: 'job_add.php', // The URL of your PHP script for adding the job
      data: formData,
      contentType: false, // Important for file upload
      processData: false, // Important for file upload
      dataType: 'json', // Expect JSON response from server
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

  $('#deleteJobForm').on('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    var formData = new FormData(this); // Create FormData object

    $.ajax({
      type: 'POST',
      url: 'job_delete.php', // The URL of your PHP script for adding news
      data: formData,
      contentType: false, // Important for file upload
      processData: false, // Important for file upload
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

  $('#editJobForm').on('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    
    // Get the updated content from CKEditor instances
    formData.set('edit_description', currentEditors['edit_description'].getData());
    formData.set('edit_expertise_specification', currentEditors['edit_expertise_specification'].getData());
    formData.set('edit_about_the_role', currentEditors['edit_about_the_role'].getData());

    $.ajax({
      type: 'POST',
      url: 'job_edit.php',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          showAlert('success', response.message);
        } else if (response.status === 'info') {
          showAlertEdit(response.status, response.message);
        } else {
          showAlert('error', response.message);
        }
      },
      error: function () {
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
    Swal.fire({
      icon: type,
      title: type === 'info' ? 'Information' : 'Error',
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