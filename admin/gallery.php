<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

// Fetch gallery data from Firebase
$galleryData = $firebase->retrieve("gallery");

// Decode JSON data into associative arrays
$galleries = json_decode($galleryData, true) ?: [];
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header box-header-background">
                <h1>
                    Content <i class="fa fa-angle-right"></i> Gallery
                </h1>
                <div class="box-inline">
                    <a href="#addnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat">
                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp; New
                    </a>

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

            <section class="content">



                <div class="row">
                    <div class="col-xs-12">
                        <div class="box" style="min-height:20px">
                            <div class="box-header with-border">
                                <!-- Any header content you might want to add -->
                            </div>
                            <div class="box-body" style="padding:30px">
                                <div class="album album-container">
                                    <?php foreach ($galleries as $id => $gallery): ?>
                                        <?php if (isset($gallery['gallery_name'], $gallery['image_url'])): ?>
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
                                                            data-id="<?php echo htmlspecialchars($id); ?>"
                                                            onclick="event.stopPropagation(); openEditModal('<?php echo $id; ?>')">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M12 20h9" />
                                                                <path
                                                                    d="M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                            </svg>
                                                            Change Cover
                                                        </div>
                                                        <div class="dropdown-item open-delete"
                                                            data-id="<?php echo htmlspecialchars($id); ?>"
                                                            onclick="event.stopPropagation(); openDeleteModal('<?php echo $id; ?>')">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M3 6h18M3 6l1 16H2l1-16zM8 6v12M16 6v12" />
                                                            </svg>
                                                            Delete
                                                        </div>
                                                    </div>
                                                    <div class="overlay" style="background-color: rgba(0, 0, 0, 0.5);">
                                                        <a href="gallery_view.php?id=<?php echo urlencode($id); ?>"
                                                            class='btn btn-default btn-class'
                                                            style="opacity: 1; background:linear-gradient(to right, #90caf9, #047edf 99%); color:white; width: 100px; border: none;">View
                                                            Album</a>
                                                    </div>
                                                </div>
                                                <div class="album-title">
                                                    <?php echo htmlspecialchars($gallery['gallery_name']); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <!-- Your delete modal content here -->
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/album_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
</body>


<script>
    $(document).ready(function () {
        // Function to fetch content from the server
        function fetcheventData(id, successCallback, errorCallback) {
            $.ajax({
                url: 'gallery_row.php',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: successCallback,
                error: errorCallback
            });
        }

        $('.open-modal').click(function () {
            var id = $(this).data('id');

            // Fetch event data via AJAX
            fetcheventData(id, function (response) {
                $('#editId').val(id);
                $('#edit_album_name').val(response.gallery_name);
                $('#original_album_name').val(response.gallery_name);
                $('#original_image_url').val(response.image_url);

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

        $('#saveChangesButton').click(function (event) {
            event.preventDefault(); // Prevent form submission

            var id = $('#editId').val();
            var newName = $('#edit_album_name').val();
            var originalName = $('#original_album_name').val();
            var originalImageUrl = $('#original_image_url').val();

            // Check if data has changed
            if (newName === originalName && $('#imageUpload').val() === '') {
                // No data changed
                Swal.fire({
                    icon: 'info',
                    title: 'Oops...',
                    text: 'You have not made any changes',
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'swal-title',
                        htmlContainer: 'swal-text',
                        confirmButton: 'swal-button'
                    }
                });
            } else {
                // Data changed, proceed with form submission
                $('form').submit(); // Submit the form
            }
        });

        $('#addAlbumForm').on('submit', function (event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: 'gallery_add.php',
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



        // Open delete modal when delete button is clicked
        $('.open-delete').click(function () {
            var id = $(this).data('id');
            $('#deleteAlbumId').val(id);
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('.btn-confirm-delete').click(function () {
            var id = $('#deleteAlbumId').val();
            $.ajax({
                url: 'gallery_delete.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    if (response.status === 'success') {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
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
                    console.error('AJAX Error: ' + status + ' ' + error);
                    Swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: 'An unexpected error occurred.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

    });

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

    function filterGallery() {
        const input = document.getElementById('search-input').value.toLowerCase();
        const albums = document.querySelectorAll('.album-container .album-item');

        albums.forEach(album => {
            const title = album.querySelector('.album-title').textContent.toLowerCase();
            if (title.includes(input)) {
                album.style.display = 'block';
            } else {
                album.style.display = 'none';
            }
        });
    }

    // Optional: Filter as you type
    document.getElementById('search-input').addEventListener('input', filterGallery);

    function showAlert(type, message) {
        Swal.fire({
            position: "top-end",
            icon: type === 'error' ? 'error' : 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    }




</script>