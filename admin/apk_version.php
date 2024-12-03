<!DOCTYPE html>
<!--apk_version.php-->
<html>

<head>
    <?php include 'includes/session.php'; ?>
    <?php include 'includes/header.php'; ?>
    <style>
        .box-header {
            position: relative;
            padding-top: 10px;
            border-radius: 0px !important;
        }

        .table-responsive {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #3c8dbc #f0f0f0;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f0f0f0;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #3c8dbc;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background-color: #2a6b8c;
        }

        .table-bordered tbody tr:hover {
            background-color: #3c8dbc;
            color: white;
        }

        .table-bordered tbody tr:hover td {
            color: white;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    App Versions
                </h1>
                <div class="box-inline ">

                    <a href="#mobile_app_addCategory" data-toggle="modal"
                        class="btn-add-class btn btn-primary btn-sm btn-flat"><i
                            class="fa fa-plus-circle"></i>&nbsp;&nbsp; Add New Version</a>

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
                    <li>App Version</li>
                    <li class="active" style="color:white">App Version</li>
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
                                            <th>Version</th>
                                            <th>Actions</th>
                                        </thead>
                                        <tbody>
                                            <?php include 'fetch_data/fetch_dataApkUpdate.php' ?>
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
        <?php include 'includes/app_add_modal.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script>
        $(document).ready(function () {
            const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
            let currentUpload = null;
            let currentEditUpload = null;

            function generateUniqueId() {
                return 'upload-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            }

            function initializeUpload(file) {
                return {
                    file: file,
                    totalChunks: Math.ceil(file.size / CHUNK_SIZE),
                    currentChunk: 0,
                    uploadedChunks: 0,
                    uniqueId: generateUniqueId()
                };
            }

            function uploadNextChunk() {
                if (!currentUpload) return;
                const start = currentUpload.currentChunk * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, currentUpload.file.size);
                const chunk = currentUpload.file.slice(start, end);
                const formData = new FormData();
                const $form = $('#mobile_app_addCategoryForm');
                const originalFormData = new FormData($form[0]);
                for (let [key, value] of originalFormData.entries()) {
                    formData.append(key, value);
                }
                formData.delete('apkFile');
                formData.append('file', chunk);
                formData.append('uniqueId', currentUpload.uniqueId);
                formData.append('totalChunks', currentUpload.totalChunks);
                formData.append('currentChunk', currentUpload.currentChunk);

                $.ajax({
                    type: 'POST',
                    url: 'chunked_upload.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = (currentUpload.uploadedChunks + (evt.loaded / evt.total)) / currentUpload.totalChunks * 100;
                                $('#upload-progress-bar')
                                    .css('width', percentComplete + '%')
                                    .attr('aria-valuenow', percentComplete)
                                    .text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        if (response.status === 'chunk_received') {
                            currentUpload.currentChunk++;
                            currentUpload.uploadedChunks++;
                            if (currentUpload.currentChunk < currentUpload.totalChunks) {
                                uploadNextChunk();
                            } else {
                                finalizeUpload();
                            }
                        } else {
                            handleUploadError(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        handleUploadError(textStatus);
                    }
                });
            }

            function finalizeUpload() {
                $.ajax({
                    type: 'POST',
                    url: 'finalize_upload.php',
                    data: {
                        uniqueId: currentUpload.uniqueId,
                        version: $('input[name="version"]').val(),
                        totalChunks: currentUpload.totalChunks
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#upload-progress-bar')
                                .css('width', '100%')
                                .attr('aria-valuenow', 100)
                                .text('100%')
                                .removeClass('progress-bar-primary progress-bar-striped active')
                                .addClass('progress-bar-success');
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            handleUploadError(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        handleUploadError(textStatus);
                    }
                });
            }

            function handleUploadError(message) {
                $('#upload-progress-bar')
                    .removeClass('progress-bar-primary progress-bar-striped active')
                    .addClass('progress-bar-danger');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                currentUpload = null;
            }

            $('#mobile_app_addCategoryForm').on('submit', function (event) {
                event.preventDefault();
                const fileInput = $('input[name="apkFile"]');
                const file = fileInput[0].files[0];
                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please choose an APK file to upload.'
                    });
                    return;
                }
                $('#upload-progress-container').show();
                $('#upload-progress-bar')
                    .css('width', '0%')
                    .attr('aria-valuenow', 0)
                    .text('0%')
                    .removeClass('progress-bar-success progress-bar-danger')
                    .addClass('progress-bar-primary progress-bar-striped active');
                currentUpload = initializeUpload(file);
                uploadNextChunk();
            });

            // Edit Modal Upload
            function uploadNextEditChunk() {
                if (!currentEditUpload) return;
                const start = currentEditUpload.currentChunk * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, currentEditUpload.file.size);
                const chunk = currentEditUpload.file.slice(start, end);
                const formData = new FormData();
                const $form = $('#mobile_app_editVersionForm');
                const originalFormData = new FormData($form[0]);
                for (let [key, value] of originalFormData.entries()) {
                    formData.append(key, value);
                }
                formData.delete('edit_apkFile');
                formData.append('file', chunk);
                formData.append('uniqueId', currentEditUpload.uniqueId);
                formData.append('totalChunks', currentEditUpload.totalChunks);
                formData.append('currentChunk', currentEditUpload.currentChunk);

                $.ajax({
                    type: 'POST',
                    url: 'chunked_upload.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = (currentEditUpload.uploadedChunks + (evt.loaded / evt.total)) / currentEditUpload.totalChunks * 100;
                                $('#edit-upload-progress-bar')
                                    .css('width', percentComplete + '%')
                                    .attr('aria-valuenow', percentComplete)
                                    .text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        if (response.status === 'chunk_received') {
                            currentEditUpload.currentChunk++;
                            currentEditUpload.uploadedChunks++;
                            if (currentEditUpload.currentChunk < currentEditUpload.totalChunks) {
                                uploadNextEditChunk();
                            } else {
                                finalizeEditUpload();
                            }
                        } else {
                            handleEditUploadError(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        handleEditUploadError(textStatus);
                    }
                });
            }

            function finalizeEditUpload() {
                $.ajax({
                    type: 'POST',
                    url: 'finalize_upload.php',
                    data: {
                        uniqueId: currentEditUpload.uniqueId,
                        version: $('input[name="edit_version"]').val(),
                        totalChunks: currentEditUpload.totalChunks
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#edit-upload-progress-bar')
                                .css('width', '100%')
                                .attr('aria-valuenow', 100)
                                .text('100%')
                                .removeClass('progress-bar-primary progress-bar-striped active')
                                .addClass('progress-bar-success');
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            handleEditUploadError(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        handleEditUploadError(textStatus);
                    }
                });
            }

            function handleEditUploadError(message) {
                $('#edit-upload-progress-bar')
                    .removeClass('progress-bar-primary progress-bar-striped active')
                    .addClass('progress-bar-danger');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                currentEditUpload = null;
            }

            $('#mobile_app_editVersionForm').on('submit', function (event) {
                event.preventDefault();
                const fileInput = $('input[name="edit_apkFile"]');
                const file = fileInput[0].files[0];
                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please choose an APK file to upload.'
                    });
                    return;
                }
                $('#edit-upload-progress-container').show();
                $('#edit-upload-progress-bar')
                    .css('width', '0%')
                    .attr('aria-valuenow', 0)
                    .text('0%')
                    .removeClass('progress-bar-success progress-bar-danger')
                    .addClass('progress-bar-primary progress-bar-striped active');
                currentEditUpload = initializeEditUpload(file);
                uploadNextEditChunk();
            });

            // Existing code for handling modals and other functionalities
            $(document).on('click', '.open-modal', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'app_row.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        $('#versionId').val(id);
                        $('#edit_version').val(response.version);
                        if (response.apk_file_path) {
                            $('#edit_apkFileName').val(response.apk_file_path);
                            $('#download_apk_btn').attr('href', 'https://mccalumnitracker.com/admin/' + encodeURIComponent(response.apk_file_path));
                            $('#download_apk_btn').show();
                        } else {
                            $('#edit_apkFileName').val('No file selected');
                            $('#download_apk_btn').hide();
                        }
                        $('#edit_apkFile').data('original-file', response.apk_file_path);
                        $('#editModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' ' + error);
                        alert('An error occurred while fetching version details.');
                    }
                });
            });

            // Optional: Handle form submission for edit
            $('#mobile_app_editVersionForm').on('submit', function (event) {
                event.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: 'apk_version_edit.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Full response text:', xhr.responseText);
                        console.error('Status:', status);
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: 'An unexpected error occurred. Check browser console for details.'
                        });
                    }
                });
            });

            $(document).on('click', '.open-delete', function () {
                var id = $(this).data('id');

                // Make an AJAX request to fetch the version details
                $.ajax({
                    url: 'app_row.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        // Set the hidden input value in the delete modal
                        $('.mobile_app_catid').val(id);

                        // Display the apk_file_path in the modal
                        if (response.apk_file_path) {
                            $('#mobile_app_del_cat').text('APK File Path: ' + response.apk_file_path);
                        } else {
                            $('#mobile_app_del_cat').text('No APK file path available.');
                        }

                        // Show the delete modal
                        $('#mobile_app_deleteModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' ' + error);
                        alert('An error occurred while fetching version details.');
                    }
                });
            });

            $('#confirmDelete').on('click', function () {
                var versionId = $('.mobile_app_catid').val();
                $.ajax({
                    type: 'POST',
                    url: 'apk_version_delete.php',
                    data: { versionId: versionId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' ' + error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Deletion Error',
                            text: 'An unexpected error occurred. Check browser console for details.'
                        });
                    }
                });
            });


        });
    </script>
</body>

</html>