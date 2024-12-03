

<!-- Add -->

<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Gallery <i
                            class="fa fa-angle-right"></i> Add</b></h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="uploadForm" method="POST" action="gallery_view_add.php"
                    enctype="multipart/form-data" onsubmit="return validateAddGalleryForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <input type="hidden" name="gallery_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <div class="form-group">
                        <label for="album_images" class="col-sm-3 control-label">Select Images</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="album_images" name="album_images[]" multiple
                                accept="image/*">
                            <small class="error-message" id="add_gallery_image_error" style="color:red; display:none;">
                                <i class="fa fa-info-circle"></i> This field is required.
                            </small>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div id="progress-container" class="form-control" style="display:none;">
                                <div id="progress-bar" class="form-control progress-bar" role="progressbar"
                                    style="width: 0%;"></div>
                                <span id="progress-text">0% Complete</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-flat pull-right btn-class" id="uploadButton"
                            style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
                            <i class="fa fa-save"></i> Save
                        </button>
                        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal">
                            <i class="fa fa-close"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Edit -->

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Gallery Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for editing gallery item -->
                <form id="editForm" action="gallery_view_edit.php" method="post" enctype="multipart/form-data"
                    onsubmit="return validateEditGalleryForm()">
                    <input type="hidden" id="editId" name="id">

                    <div class="form-group">
                        <label for="edit_album_image_url">Current Image URL</label>
                        <input type="text" class="form-control" id="edit_album_image_url" name="image_url" readonly>
                    </div>

                    <div class="form-group">
                        <label for="imageFile">Change Image</label>
                        <input type="file" class="form-control" id="imageFile" name="imageFile">
                        <small class="error-message" id="edit_image_file_error" style="color:red; display:none;"><i
                                class="fa fa-info-circle"></i> Please select a valid image file.</small>
                    </div>

                    <div class="form-group">
                        <label for="newFileName">New File Name</label>
                        <input type="text" class="form-control" id="newFileName" name="newFileName"
                            placeholder="Enter new file name">
                        <small class="error-message" id="edit_new_file_name_error" style="color:red; display:none;"><i
                                class="fa fa-info-circle"></i> This field is required.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-flat pull-right btn-class" id="saveChanges"
                    style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                        class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>



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
            <form accept="gallery_view_delete.php" method="post" onsubmit="return validateDeleteGalleryForm()">
            <div class="modal-body">
                    <input type="hidden" id="deleteAlbumId" value="">
                    <div class="text-center">
                        <p>Are you sure you want to delete this Photo?</p>
                        <h2 class="deleteTitle"></h2>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton" class="btn btn-default pull-right btn-flat btn-class btn-confirm-delete"
                    style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;">
                    <i class="fa fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-flat  btn-class" data-dismiss="modal">
                    <i class="fa fa-close"></i> Close
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
