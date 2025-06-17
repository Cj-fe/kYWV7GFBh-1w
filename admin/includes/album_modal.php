<!--Script Validation-->

<script>
  function validateGalleryForm() {
    let isValid = true;

    // Album Name validation
    const albumName = document.getElementById("album_name");
    const albumNameError = document.getElementById("add_album_name_error");
    if (albumName.value.trim() === "") {
        albumNameError.style.display = "block";
        isValid = false;
    } else {
        albumNameError.style.display = "none";
    }

    // Album Image validation
    const albumImage = document.getElementById("album_image");
    const albumImageError = document.getElementById("add_album_image_error");
    if (albumImage.files.length === 0) {
        albumImageError.style.display = "block";
        isValid = false;
    } else {
        albumImageError.style.display = "none";
    }

    if (isValid) {
        document.getElementById('submitButton').disabled = true;
    }

    return isValid;
}
function validateEditGalleryForm() {
    let isValid = true;

    // Album Name validation
    const albumName = document.getElementById("edit_album_name");
    const albumNameError = document.getElementById("edit_album_name_error");
    if (albumName.value.trim() === "") {
        albumNameError.style.display = "block";
        isValid = false;
    } else {
        albumNameError.style.display = "none";
    }

    // Album Image validation
    const albumImage = document.getElementById("edit_album_image");
    const albumImageError = document.getElementById("edit_album_image_error");
    if (albumImage.src.trim() === "" && document.getElementById("imageUpload").files.length === 0) {
        albumImageError.style.display = "block";
        isValid = false;
    } else {
        albumImageError.style.display = "none";
    }

    return isValid;
}

</script>

<!-- Add -->

<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Gallery <i class="fa fa-angle-right"></i> Add</b></h3>
            </div>
            <div class="modal-body">
                <form id="addAlbumForm" class="form-horizontal" method="POST" action="gallery_add.php" enctype="multipart/form-data" onsubmit="return validateGalleryForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <div class="form-group">
                        <label for="album_name" class="col-sm-3 control-label">Album Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="album_name" name="album_name">
                            <small class="error-message" id="add_album_name_error" style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is required.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="album_image" class="col-sm-3 control-label">Album Cover</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="album_image" name="album_image" accept="image/*">
                            <small class="error-message" id="add_album_image_error" style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is required.</small>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton"  class="btn btn-flat pull-right btn-class" name="add" style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit -->

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 30%">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <h3 class="modal-title" id="editModalLabel">Edit Album</h3>
            </div>
            <div class="modal-body">
                <form method="POST" action="gallery_edit.php" enctype="multipart/form-data" onsubmit="return validateEditGalleryForm()">
                    <input type="hidden" id="editId" name="id">
                    <input type="hidden" id="original_album_name">
                    <input type="hidden" id="original_image_url">
                    <center>
                        <div class="form-group" style="background: silver; width:50%; min-height: 0; position: relative;">
                            <img id="edit_album_image" src="" alt="Album Image" style="display: none; max-width: 100%; height: auto; top: 0; left: 0; right: 0; bottom: 0; margin: auto;">
                            <small class="error-message" id="edit_album_image_error" style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is required.</small>
                        </div>
                    </center>
                    <div class="form-group">
                        <label for="edit_album_name">Gallery Name</label>
                        <input type="text" class="form-control" id="edit_album_name" name="edit_album_name">
                        <small class="error-message" id="edit_album_name_error" style="color:red; display:none;"><i class="fa fa-info-circle"></i> This field is required.</small>
                    </div>
                    <div class="form-group">
                        <label for="imageUpload">Upload Image</label>
                        <input type="file" class="form-control" id="imageUpload" name="imageUpload" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveChangesButton" class="btn btn-flat pull-right btn-class" style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body">

        <input type="hidden" id="deleteAlbumId" value="">
        <div class="text-center">
          <p>Are you sure you want to delete this Album?</p>
          <h2 class="deleteTitle"></h2>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default pull-right btn-flat btn-class btn-confirm-delete"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
          <i class="fa fa-trash"></i> Delete
        </button>
        <button type="button" class="btn btn-flat  btn-class" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>