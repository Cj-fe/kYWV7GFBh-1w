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
                    Content <i class="fa fa-angle-right"></i> Event
                </h1>
                <div class="box-inline ">

                    <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat"><i
                            class="fa fa-plus-circle"></i>&nbsp;&nbsp; New</a>

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
                    <li>Content</li>
                    <li class="active" style="color:white">Event</li>
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
                            <div class="with-border">

                            </div>
                            <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                                <div class="table-responsive"> <!-- Add this div for responsive behavior -->
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <th>Thumnails</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th width="30%">Description</th>
                                            <th width="10%">Date Posted</th>

                                            <th width="10%">Tools</th>
                                        </thead>
                                        <tbody>
                                            <?php include 'fetch_data/fetch_dataEvent.php'; ?>
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
        <?php include 'includes/event_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <script>
        $(document).ready(function () {

            // Function to fetch content from the server
            function fetchEventData(id, successCallback, errorCallback) {
                $.ajax({
                    url: 'event_row.php',
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

            let originalEventData = {};

            function openEditModal(response, id) {
                $('#editId').val(id);
                $('#editTitle').val(response.event_title);
                $('#editEventDate').val(response.event_date);
                $('#editEventVenue').val(response.event_venue);
                $('#editAuthor').val(response.event_author);
                $('#editDesc').val(response.event_description);

                // Handle course_invited
                if (response.course_invited) {
                    try {
                        const courseInvited = JSON.parse(response.course_invited);
                        $('#edit_course_invited').val(courseInvited);
                        $('#edit_course_invited').selectpicker('refresh');
                    } catch (e) {
                        console.error('Error parsing course_invited:', e);
                    }
                } else {
                    $('#edit_course_invited').val([]);
                    $('#edit_course_invited').selectpicker('refresh');
                }

                // Handle event_invited
                if (response.event_invited) {
                    try {
                        const eventInvited = JSON.parse(response.event_invited);
                        $('#edit_event_invited').val(eventInvited);
                        $('#edit_event_invited').selectpicker('refresh');
                    } catch (e) {
                        console.error('Error parsing event_invited:', e);
                    }
                } else {
                    $('#edit_event_invited').val([]);
                    $('#edit_event_invited').selectpicker('refresh');
                }

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
                originalEventData = {};
                $('#editEventForm').find('input, textarea, select').each(function () {
                    var $input = $(this);
                    if ($input.is(':checkbox')) {
                        originalEventData[$input.attr('name')] = $input.prop('checked');
                    } else if ($input.is(':radio')) {
                        if ($input.is(':checked')) {
                            originalEventData[$input.attr('name')] = $input.val();
                        }
                    } else if ($input.is('select')) {
                        originalEventData[$input.attr('name')] = $input.val() ? $input.val().toString() : '';
                    } else {
                        originalEventData[$input.attr('name')] = $input.val();
                    }
                });
                console.log('Original values:', originalEventData); // Debugging log
            }

            function hasChanges() {
                var hasChanges = false;
                $('#editEventForm').find('input, textarea, select').each(function () {
                    var $input = $(this);
                    var currentValue;
                    if ($input.is(':checkbox')) {
                        currentValue = $input.prop('checked');
                    } else if ($input.is(':radio')) {
                        if ($input.is(':checked')) {
                            currentValue = $input.val();
                        }
                    } else if ($input.is('select')) {
                        currentValue = $input.val() ? $input.val().toString() : '';
                    } else {
                        currentValue = $input.val();
                    }

                    var originalValue = originalEventData[$input.attr('name')];
                    console.log('Comparing', $input.attr('name'), ':', currentValue, 'with', originalValue); // Debugging log

                    if (currentValue !== originalValue) {
                        hasChanges = true;
                        return false; // Break out of the loop
                    }
                });
                return hasChanges;
            }

            // Use event delegation to handle edit modal
            $(document).on('click', '.open-modal', function () {
                var id = $(this).data('id');
                fetchEventData(id, function (response) {
                    openEditModal(response, id);
                }, function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    alert('Failed to fetch event data. Please try again later.');
                });
            });

            $('#editEventForm').on('submit', function (event) {
                event.preventDefault();

                if (!hasChanges()) {
                    showAlertEdit('info', 'You have not made any changes');
                    return; // Prevent form submission
                }

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: 'event_edit.php',
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


            $('#addEventForm').on('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: 'event_add.php',
                    data: formData,
                    contentType: false,
                    processData: false,
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

            $('#deleteEventForm').on('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: 'event_delete.php',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                    url: 'event_row.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        $('.description-container').html(response.event_description);
                        $('.deleteId').val(id);
                        $('.title').text(response.event_title);

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
                    url: 'event_delete.php',
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
                    icon: type,
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