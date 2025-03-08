<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/session.php'; ?>
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

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    Alumni <i class="fa fa-angle-right"></i> Job Categories
                </h1>
                <div class="box-inline ">

                    <a href="#addCategory" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat"><i
                            class="fa fa-plus-circle"></i>&nbsp;&nbsp; Add New Category</a>

                    <div class="search-container">
                        <input type="text" class="search-input" id="search-input"
                            placeholder="Search Job Categories...">
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
                    <li>Content</li>
                    <li class="active" style="color:white">Job Categories</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error</h4>
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
                        <div class="box" style="border-radius: 0% !important; ">
                        
                            <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                                <div class="table-responsive"> <!-- Add this div for responsive behavior -->
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <th>Job Category</th>
                                            <th>Actions</th>
                                        </thead>
                                        <tbody>
                                            <?php include 'fetch_data/fetch_dataCategory.php'; ?>
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
        <?php include 'includes/category_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <script>
        $(document).ready(function () {
            // Use event delegation to handle edit modal
            $(document).on('click', '.open-modal', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'category_row.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        $('#categoryId').val(id);
                        $('#editCategoryName').val(response.category_name);

                        // Show the edit modal
                        $('#editModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' ' + error);
                    }
                });
            });

            // Separate event delegation to handle delete modal
            $(document).on('click', '.open-delete', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'category_row.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        // Populate modal with category name
                        $('.catid').val(id);
                        $('#del_cat').text(response.category_name);

                        // Show the delete confirmation modal
                        $('#deleteModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' ' + error);
                    }
                });
            });
        });

    </script>
    <script>
        $(document).ready(function () {

            $('#deleteModal form').on('submit', function (event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'category_delete.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                            $('#deleteModal').modal('hide');
                            // Optionally, you can remove the deleted category from the DOM here
                            // $('.category-item[data-id="' + categoryId + '"]').remove();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        showAlert('error', 'An unexpected error occurred. Please check the console for more details.');
                    },
                    complete: function () {
                        $('#deleteModal').modal('hide');
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

            $('#addCategoryForm').on('submit', function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'category_add.php',
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
                        console.error('AJAX Error:', textStatus, errorThrown);
                        showAlert('error', 'An unexpected error occurred. Please check the console for more details.');
                    }
                });
            });

            // Other existing code...
            $('#editCategoryForm').on('submit', function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'category_edit.php',
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
                        console.error('AJAX Error:', textStatus, errorThrown);
                        showAlert('error', 'An unexpected error occurred. Please check the console for more details.');
                    }
                });
            });




        });

    </script>

</body>

</html>