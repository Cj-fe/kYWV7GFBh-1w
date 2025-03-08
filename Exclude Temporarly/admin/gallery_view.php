<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Retrieve the unique ID from the query string
$uniqueId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch gallery data from Firebase
$galleryData = $firebase->retrieve("gallery_view");
$galleries = json_decode($galleryData, true) ?: [];

// Fetch the specific album data
$albumData = $firebase->retrieve("gallery/$uniqueId");
$album = json_decode($albumData, true) ?: [];
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header box-header-background">
                <h1>
                    Content <i class="fa fa-angle-right"></i> Gallery <i class="fa fa-angle-right"></i>
                    <?php echo htmlspecialchars($album['gallery_name']); ?>
                </h1>
                <div class="box-inline ">

                    <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat"><i
                            class="fa fa-plus-circle"></i>&nbsp;&nbsp; New</a>


                    <div class="search-container">
                        <input type="text" class="search-input" id="search-input"
                            placeholder="Search by album title...">
                        <button class="search-button" onclick="filterGallery()">
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
                    <li class="active" style="color:white">Gallery</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_SESSION['error'])) {
                    $errorMessage = $_SESSION['error'];
                    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('error', '" . addslashes($errorMessage) . "');
    });
    </script>";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    $successMessage = $_SESSION['success'];
                    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('success', '" . addslashes($successMessage) . "');
    });
    </script>";
                    unset($_SESSION['success']);
                }
                ?>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box" style="min-height:20px">
                            <div class="box-header with-border">

                            </div>
                            <div class="box-body" style="padding:30px">
                                <div class="album" id="album-container">
                                    <?php foreach ($galleries as $id => $gallery): ?>
                                        <?php if ($gallery['gallery_id'] === $_GET['id']): ?>
                                            <div class="album-item">
                                                <div class="album-box"
                                                    style="background-image: url('<?php echo htmlspecialchars($gallery['image_url']); ?>'); background-size: cover; background-position: center;">
                                                    <div class="circle"
                                                        onclick="toggleDropdown(event, 'album<?php echo $id; ?>')">
                                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                                            <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" />
                                                        </svg>
                                                    </div>
                                                    <div class="dropdown-album-menu" id="album<?php echo $id; ?>">

                                                        <div class="dropdown-item open-modal"
                                                            data-id="<?php echo htmlspecialchars($id); ?>" data-toggle="modal"
                                                            data-target="#editModal">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M12 20h9" />
                                                                <path
                                                                    d="M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                            </svg>
                                                            Edit
                                                        </div>
                                                        <div class="dropdown-item open-delete"
                                                            data-id="<?php echo htmlspecialchars($id); ?>">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M3 6h18M3 6l1 16H2l1-16zM8 6v12M16 6v12" />
                                                            </svg>
                                                            Remove
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="album-title">
                                                    <?php echo htmlspecialchars(pathinfo($gallery['image_url'], PATHINFO_FILENAME)); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Edit Modal -->

    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/gallery_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>

</body>



<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>

<script>
    function toggleDropdown(event, dropdownId) {
        event.stopPropagation();
        const dropdown = document.getElementById(dropdownId);
        const otherDropdowns = document.querySelectorAll('.dropdown-album-menu');

        // Hide all other dropdowns except the one clicked
        otherDropdowns.forEach(dropdown => {
            if (dropdown.id !== dropdownId) {
                dropdown.style.display = 'none';
            }
        });

        // Toggle the display of the clicked dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', () => {
        const dropdowns = document.querySelectorAll('.dropdown-album-menu');
        dropdowns.forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    });

    $(document).ready(function () {
        // Function to fetch content from the server
        function fetchGalleryData(id, successCallback, errorCallback) {
            $.ajax({
                url: 'gallery_view_row.php',
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

            // Fetch gallery data via AJAX
            fetchGalleryData(id, function (response) {
                $('#editId').val(id);
                $('#edit_album_image_url').val(response.image_url);
                // Display the fetched image in the edit modal
                if (response.image_url) {
                    $('#edit_album_image').attr('src', response.image_url);
                    $('#edit_album_image').css('display', 'block'); // Ensure the image is displayed
                } else {
                    $('#edit_album_image').attr('src', ''); // Clear the image src if no image URL is returned
                    $('#edit_album_image').css('display', 'none'); // Hide the image if no image URL is returned
                }

                // Show the edit modal after setting the form fields
                $('#editModal').modal('show');

            }, function (xhr, status, error) {
                console.error('AJAX Error: ' + status + ' ' + error);
            });
        });

        // Open delete modal when delete button is clicked
        $('.open-delete').click(function () {
            var id = $(this).data('id');
            $('#deleteAlbumId').val(id);
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('.btn-confirm-delete').off('click').on('click', function () {
            var id = $('#deleteAlbumId').val();
            $.ajax({
                url: 'gallery_view_delete.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    if (response.status === 'success') {
                        // Show SweetAlert success message
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Optional: Refresh the page or perform additional actions
                            window.location.reload();
                        });
                    } else {
                        // Show SweetAlert error message
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $('#deleteModal').modal('hide');
                    // Display session error message if any
                    var errorMessage = xhr.status === 400 ? 'ID is required.' : xhr.status === 500 ? 'Failed to delete gallery data in Firebase.' : 'Invalid request method.';
                    console.error('AJAX Error: ' + errorMessage);
                    $('#errorMessage').text(errorMessage).show();
                }
            });
        });


        // Save changes when the save button is clicked in the edit modal
        $('#saveChanges').click(function () {
            var formData = new FormData($('#editForm')[0]);
            $.ajax({
                url: 'gallery_view_edit.php',
                type: 'POST',
                data: formData,
                processData: false, // Tell jQuery not to process the data
                contentType: false, // Tell jQuery not to set content type
                success: function (response) {
                    if (response.status === 'success') {
                        $('#editModal').modal('hide');
                        location.reload(); // Reload the page to see the changes
                    } else {
                        console.error('AJAX Error: ' + response.message);
                        // Show SweetAlert error dialog
                        Swal.fire({
                            icon: 'info',
                            title: 'Oops...',
                            text: response.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                title: 'swal-title',
                                htmlContainer: 'swal-text',
                                confirmButton: 'swal-button'
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    // Show SweetAlert error dialog
                    Swal.fire({
                        icon: 'error',
                        title: 'AJAX Error',
                        text: 'Status: ' + status + ' Error: ' + error,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });




        // Filter function to filter gallery items based on input value
        function filterGallery() {
            var searchTerm = $('#search-input').val().toLowerCase();
            $('#album-container').children('.album-item').each(function () {
                var title = $(this).find('.album-title').text().toLowerCase();
                if (title.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Attach input event listener to search input for live filtering
        $('#search-input').on('input', filterGallery);

        // Initial filtering on page load (in case there's an initial value in the search input)
        filterGallery();




    });

    function showAlert(type, message) {
        Swal.fire({
            position: "top-end",
            icon: type === 'error' ? 'error' : 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    }


    /*======================Progress Bar=============--*/

    document.getElementById('uploadButton').addEventListener('click', function (event) {
        event.preventDefault();

        // Validate gallery images
        const galleryImages = document.getElementById("album_images");
        const galleryImagesError = document.getElementById("add_gallery_image_error");
        let isValid = true;

        if (galleryImages.files.length === 0) {
            galleryImagesError.style.display = "block";
            showAlert('error', 'Please select at least one image.');
            isValid = false;
        } else {
            galleryImagesError.style.display = "none";
        }

        if (!isValid) {
            return; // Exit if validation fails
        }

        var form = document.getElementById('uploadForm');
        var formData = new FormData(form);

        var progressContainer = document.getElementById('progress-container');
        var progressBar = document.getElementById('progress-bar');
        var progressText = document.getElementById('progress-text');

        // Show progress container
        progressContainer.style.display = 'block';

        var xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                var percentComplete = (e.loaded / e.total) * 100;
                var slowProgress = percentComplete * 0.5; // Adjust slow down factor if needed
                progressBar.style.width = slowProgress + '%';
                progressText.textContent = Math.round(slowProgress) + '% Complete';
            }
        }, false);

        xhr.addEventListener('load', function () {
            if (xhr.status === 200) {
                progressBar.style.width = '100%';
                progressText.textContent = 'Upload Complete';

                // Parse the response to get the message
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showAlert('success', response.success);
                        setTimeout(() => window.location.reload(), 2500); // Reload after 2.5 seconds
                    } else {
                        showAlert('error', response.error);
                    }
                } catch (e) {
                    showAlert('error', 'Unexpected response format.');
                }
            } else {
                progressText.textContent = 'Upload Failed: ' + xhr.responseText;
                showAlert('error', 'Upload Failed: ' + xhr.responseText);
            }
        });

        xhr.addEventListener('error', function () {
            progressText.textContent = 'Upload Error';
            showAlert('error', 'Upload Error');
        });

        xhr.open('POST', 'gallery_view_add.php', true);
        xhr.send(formData);
    });





    /*===============Script Validation=====================--*/

    function validateAddGalleryForm() {
        let isValid = true;

        // Gallery Image validation
        const galleryImages = document.getElementById("album_images");
        const galleryImagesError = document.getElementById("add_gallery_image_error");
        if (galleryImages.files.length === 0) {
            galleryImagesError.style.display = "block";
            isValid = false;
        } else {
            galleryImagesError.style.display = "none";
        }

        return isValid;
    }
    function validateEditGalleryForm() {
        let isValid = true;

        // Validate Image File
        const imageFile = document.getElementById("imageFile");
        const imageFileError = document.getElementById("edit_image_file_error");
        if (imageFile.files.length > 0 && !imageFile.files[0].type.startsWith("image/")) {
            imageFileError.style.display = "block";
            isValid = false;
        } else {
            imageFileError.style.display = "none";
        }

        // Validate New File Name
        const newFileName = document.getElementById("newFileName");
        const newFileNameError = document.getElementById("edit_new_file_name_error");
        if (newFileName.value.trim() === "") {
            newFileNameError.style.display = "block";
            isValid = false;
        } else {
            newFileNameError.style.display = "none";
        }



        return isValid;
    }

    function validateDeleteGalleryForm() {
        let isValid = true;

        // Validate Image File


        if (isValid) {
            document.getElementById('submitButton').disabled = true;
        }

        return isValid;
    }

</script>