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
  </style>v
</head>
<html>
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
                    <h1>Deleted Job List</h1>
                    <div class="box-inline">
                        <a href="#" id="delete-all" class="btn-add-class btn btn-primary btn-sm btn-flat">
                            <i class="fa fa-trash"></i>&nbsp;&nbsp; Delete All
                        </a>
                        <a href="#" id="retrieve-all" class="btn-add-class btn btn-success btn-sm btn-flat">
                            <i class="fa fa-undo"></i>&nbsp;&nbsp; Retrieve All
                        </a>
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
                        <li>Archive</li>
                        <li class="active" style="color:white; !important">Deleted Job List</li>
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
                        <div class="table-container col-xs-12">
                            <div class="box" style="border-radius: 0px !important; ">
                                <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                                    <div class="table-responsive"> <!-- Add this div for responsive behavior -->
                                        <table id="example1" class="table table-bordered printable-table">
                                            <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th width="15%">Work Type</th>
                                                    <th>Job Title</th>
                                                    <th>Company</th>
                                                    <th>Description</th>
                                                    <th>Date Deleted</th>
                                                    <th width="10%">Tools</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php include 'fetch_data/fetch_deleteJob.php' ?>
                                            </tbody>
                                        </table>
                                        <!-- Modal -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <?php include 'includes/archive_modal.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>
<script>
    $(document).ready(function () {
        // Handle restore button click
        $(document).on('click', '.open-restore', function () {
            var id = $(this).data('id');
            var title = $(this).data('title');

            $('.retrieveId').val(id);
            $('.restoreJobTitle').text(title);
            $('#restoreJobModal').modal('show');
        });

        // Handle delete button click
        $(document).on('click', '.open-delete', function () {
            var id = $(this).data('id');
            var title = $(this).data('title');

            $('.deleteId').val(id);
            $('.deleteJobTitle').text(title);
            $('#deleteJobModal').modal('show');
        });

        // Handle restore form submission
        $('#restoreJobForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'deleted_job_retrieve.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    $('#restoreJobModal').modal('hide');
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function () {
                    showAlert('error', 'An unexpected error occurred');
                }
            });
        });

        // Handle delete form submission
        $('#deleteJobForm').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'deleted_job_delete.php',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $('#deleteJobModal').modal('hide');
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function () {
                    $('#deleteJobModal').modal('hide');
                    showAlert('error', 'An unexpected error occurred.');
                }
            });
        });

        // Handle "Delete All" button click
        $('#delete-all').on('click', function (event) {
            event.preventDefault(); // Prevent default action

            // Set a generic message or item name for the modal
            $('.deleteItemName').text('All Job Deleted Item Records');

            // Open the generic delete modal
            $('#genericDeleteModal').modal('show');

            // Optionally, you can set a special ID or flag to indicate "delete all"
            $('.deleteId').val('all'); // Use 'all' or another identifier to handle this case in your PHP script
        });

        // Handle form submission for deleting all
        $('#genericDeleteForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            $.ajax({
                type: 'POST',
                url: 'delete_all_archive_job_list.php', // The URL of your PHP script for deleting all alumni
                dataType: 'json',
                success: function (response) {
                    $('#genericDeleteModal').modal('hide'); // Hide the modal after the operation
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function () {
                    $('#genericDeleteModal').modal('hide'); // Hide the modal in case of error
                    showAlert('error', 'An unexpected error occurred.');
                }
            });
        });


        // Handle "Retrieve All" button click
        $('#retrieve-all').on('click', function (event) {
            event.preventDefault(); // Prevent default action

            // Set a generic message or item name for the modal
            $('.restoreItemName').text('Restore All Job Deleted Records');

            // Open the generic restore modal
            $('#genericRestoreModal').modal('show');

            // Optionally, you can set a special ID or flag to indicate "retrieve all"
            $('.retrieveId').val('all'); // Use 'all' or another identifier to handle this case in your PHP script
        });

        // Handle form submission for retrieving all
        $('#genericRestoreForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            $.ajax({
                type: 'POST',
                url: 'restore_all_archive_job_list.php', // The URL of your PHP script for retrieving all alumni
                dataType: 'json',
                success: function (response) {
                    $('#genericRestoreModal').modal('hide'); // Hide the modal after the operation
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function () {
                    $('#genericRestoreModal').modal('hide'); // Hide the modal in case of error
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
    });
</script>

</html>