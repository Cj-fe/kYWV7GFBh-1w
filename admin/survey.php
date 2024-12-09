<?php include 'includes/session.php'; ?>

<head>
<?php include 'includes/header.php'; ?>

<style>
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
          Survey <i class="fa fa-angle-right"></i> Survey List
        </h1>
        <div class="box-inline ">

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
          <li>Survey</li>
          <li class="active" style="color:white">Survey List</li>
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
          <div class="col-xs-12">
            <div class="box" style="border-radius: 0px !important; ">

              <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                <div class="table-responsive"> <!-- Add this div for responsive behavior -->
                  <table id="example1" class="table table-bordered">
                    <thead>

                      <th>Title</th>
                      <th>Description</th>
                      <th width="15%">Start Date</th>
                      <th width="15%">End Date</th>
                      <th width="15%">Tools</th>
                    </thead>
                    <tbody>
                      <?php include 'fetch_data/fetch_dataSurvey.php'; ?>
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
    <?php include 'includes/survey_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(document).ready(function () {
      // Function to fetch content from the server
      function fetcheventData(id, successCallback, errorCallback) {
        $.ajax({
          url: 'survey_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: successCallback,
          error: errorCallback
        });
      }

      // Open edit modal when edit button is clicked
      $('.open-modal').click(function () {
        var id = $(this).data('id');
        console.log('Edit button clicked for ID:', id);

        // Fetch event data via AJAX
        fetcheventData(id, function (response) {
          console.log('Received data:', response);

          try {
            $('#editId').val(id);
            $('#edit_survey_title').val(response.survey_title);
            $('#edit_survey_desc').val(response.survey_desc);
            $('#edit_survey_start').val(response.survey_start);
            $('#edit_survey_end').val(response.survey_end);

            // Handle survey_batch multi-select
            if (response.survey_batch) {
              console.log('Setting survey_batch:', response.survey_batch);
              $('#edit_survey_batch').val(Array.isArray(response.survey_batch) ? response.survey_batch : [response.survey_batch]);
            } else {
              $('#edit_survey_batch').val([]);
            }

            // Handle survey_courses multi-select
            if (response.survey_courses) {
              console.log('Setting survey_courses:', response.survey_courses);
              $('#edit_survey_courses').val(Array.isArray(response.survey_courses) ? response.survey_courses : [response.survey_courses]);
            } else {
              $('#edit_survey_courses').val([]);
            }

            // Check if selectpicker is initialized
            if ($.fn.selectpicker) {
              $('#edit_survey_batch, #edit_survey_courses').selectpicker('refresh');
            } else {
              console.warn('Bootstrap Select not initialized');
            }

            // Show the edit modal after setting the form fields
            $('#editModal').modal('show');
          } catch (error) {
            console.error('Error while populating form:', error);
          }
        }, function (xhr, status, error) {
          console.error('AJAX Error:', status, error);
          console.log('Response Text:', xhr.responseText);
        });
      });

      // Open delete modal when delete button is clicked
      $('.open-delete').click(function () {
        var id = $(this).data('id');
        console.log('Delete button clicked for ID:', id);

        // Make an AJAX request to fetch job details
        $.ajax({
          url: 'survey_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            console.log('Received delete data:', response);
            // Populate modal with alumni name
            $('.deleteId').val(id); // Update value of deleteId input field
            $('.edit_survey_title').text(response.survey_title);

            // Show the delete confirmation modal
            $('#deleteModal').modal('show');

            // Store the ID in a data attribute of the delete button
            $('.btn-confirm-delete').data('id', id);
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.log('Response Text:', xhr.responseText);
          }
        });
      });
    });
  </script>
  <script>
    $(document).ready(function () {
      $('#addSurveyForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'survey_add.php',
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

      $('#editSurveyForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'survey_edit.php',
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
          error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error details:', textStatus, errorThrown);
            console.log('Response Text:', jqXHR.responseText);
            showAlert('error', 'An unexpected error occurred.');
          }
        });
      });

      $('#deleteSurveyForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'survey_delete.php',
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