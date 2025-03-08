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
          Content <i class="fa fa-angle-right"></i> News
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
          <li class="active" style="color:white">News</li>
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
              <div class=" with-border">
                <!-- Add any additional header content here -->
              </div>
              <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered">
                    <thead>
                      <th>Thumbnails</th>
                      <th>Title</th>
                      <th>Author</th>
                      <th width="30%">Description</th>
                      <th width="10%">Date Posted</th>
                      <th width="10%">Tools</th>
                    </thead>
                    <tbody>
                      <?php include 'fetch_data/fetch_dataNews.php'; ?>
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
    <?php include 'includes/news_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(document).ready(function () {
     

      // Function to fetch content from the server
      function fetchNewsData(id, successCallback, errorCallback) {
        $.ajax({
          url: 'news_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: successCallback,
          error: errorCallback
        });
      }

      // Function to initialize CKEditor
      function initializeCKEditor(elementId) {
        if (window.CKEDITOR && CKEDITOR.instances[elementId]) {
          CKEDITOR.instances[elementId].destroy(true);
        }
        if (window.CKEDITOR && CKEDITOR.replace) {
          CKEDITOR.replace(elementId);
        } else {
          console.error('CKEditor is not defined or replace method is missing.');
        }
      }

      let originalValues = {};

      // Function to handle modal display and data population for edit
      function openEditModal(response, id) {
        $('#editId').val(id);
        $('#editTitle').val(response.news_title);
        $('#editAuthor').val(response.news_author);
        $('#editDesc').val(response.news_description);

        if (response.image_url && response.image_url.trim() !== '') {
          $('#imagePreviewImg2').attr('src', response.image_url).show();
        } else {
          $('#imagePreviewImg2').hide();
        }

        // Capture original values
        captureOriginalValues();
        $('#editModal').modal('show');

        initializeCKEditor('editDesc');

        $('#editTitle').focus();
      }

      function captureOriginalValues() {
        originalValues = {};
        $('#editNewsForm').find('input, textarea').each(function () {
          var $input = $(this);
          originalValues[$input.attr('name')] = $input.val();
        });
      }

      function hasChanges() {
        var hasChanges = false;
        $('#editNewsForm').find('input, textarea').each(function () {
          var $input = $(this);
          if ($input.val() !== originalValues[$input.attr('name')]) {
            hasChanges = true;
            return false; // Break out of the loop
          }
        });
        return hasChanges;
      }

      // Use event delegation to handle edit modal
      $(document).on('click', '.open-modal', function () {
        var id = $(this).data('id');
        fetchNewsData(id, function (response) {
          openEditModal(response, id);
        }, function (xhr, status, error) {
          console.error('AJAX Error: ' + status + ' ' + error);
          alert('Failed to fetch news data. Please try again later.');
        });
      });

      $('#editNewsForm').on('submit', function (event) {
        event.preventDefault();

        if (!hasChanges()) {
          showAlertEdit('info', 'You have not made any changes');
          return; // Prevent form submission
        }

        var formData = new FormData(this);

        $.ajax({
          type: 'POST',
          url: 'news_edit.php',
          data: formData,
          contentType: false,
          processData: false,
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

      $('#addNewsForm').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this); // Create FormData object

        $.ajax({
          type: 'POST',
          url: 'news_add.php', // The URL of your PHP script for adding news
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


      $('#deleteNewsForm').on('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this); // Create FormData object

        $.ajax({
          type: 'POST',
          url: 'news_delete.php', // The URL of your PHP script for adding news
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

      // Use event delegation to handle delete modal
      $(document).on('click', '.open-delete', function () {
        var id = $(this).data('id');

        $.ajax({
          url: 'news_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            $('.description-container').html(response.news_description);
            $('.deleteId').val(id);
            $('.title').text(response.news_title);

            if (response.image_url) {
              $('#imagePreviewImg3').attr('src', response.image_url).show();
            } else {
              $('#imagePreviewImg3').hide();
            }

            $('#deleteModal').modal('show');
            $('.btn-confirm-delete').data('id', id);
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
          }
        });
      });

      // Confirm delete action
      $(document).on('click', '.btn-confirm-delete', function () {
        var id = $(this).data('id');

        $.ajax({
          url: 'news_delete.php',
          type: 'POST',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              showAlert('success', response.message);
            } else {
              showAlert('error', response.message);
            }
            $('#deleteModal').modal('hide');
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
          icon: 'info',
          title: 'Oops...',
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