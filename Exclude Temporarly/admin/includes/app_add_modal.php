<!-- Modal -->
<div class="modal fade" id="mobile_app_addCategory">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Add New Version</b></h4>
      </div>
      <div class="modal-body">
        <form id="mobile_app_addCategoryForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="version" class="col-sm-3 control-label">Version</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="version" name="version" required>
            </div>
          </div>
          <div class="form-group">
            <label for="apkFile" class="col-sm-3 control-label">APK File</label>
            <div class="col-sm-9">
              <input type="file" class="form-control" id="apkFile" name="apkFile" accept=".apk" required>
            </div>
          </div>
          <!-- Progress Bar Container -->
          <div class="form-group">
            <div class="col-sm-12">
              <div id="upload-progress-container" class="progress" style="display:none;">
                <div id="upload-progress-bar" class="progress-bar progress-bar-primary progress-bar-striped active"
                  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                  0%
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-flat pull-right btn-class" name="add"
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

<!-- Edit (app_add_modal.php) -->
<div class='modal fade' id='editModal' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Mobile Application Version</b></h4>
      </div>
      <div class="modal-body">
        <form id="mobile_app_editVersionForm" class="form-horizontal" method="POST" action="apk_version_edit.php"
          enctype="multipart/form-data">
          <input type="hidden" id="versionId" name="versionId">
          <div class="form-group">
            <label for="edit_version" class="col-sm-3 control-label">Version</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_version" name="edit_version" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_apkFile" class="col-sm-3 control-label">APK File</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="text" class="form-control" id="edit_apkFileName" placeholder="Current APK Filename"
                  readonly>
                <span class="input-group-btn">
                  <a href="#" id="download_apk_btn" class="btn btn-primary" target="_blank">
                    <i class="fa fa-download"></i> Download
                  </a>
                  <button class="btn btn-default" type="button"
                    onclick="document.getElementById('edit_apkFile').click()">
                    <i class="fa fa-folder-open"></i> Browse
                  </button>
                </span>
                <input type="file" class="form-control" id="edit_apkFile" name="edit_apkFile" accept=".apk"
                  style="display:none;"
                  onchange="document.getElementById('edit_apkFileName').value = this.files[0] ? this.files[0].name : '';">
              </div>
              <small class="help-block">Click 'Browse' to replace the current APK file</small>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-12">
              <div id="edit-upload-progress-container" class="progress" style="display:none;">
                <div id="edit-upload-progress-bar" class="progress-bar progress-bar-primary progress-bar-striped active"
                  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                  0%
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-flat pull-right btn-class" name="edit"
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


<!-- Delete Modal (app_add_modal.php) -->
<div class="modal fade" id="mobile_app_deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting Mobile Application Version...</b></h4>
            </div>
            <div class="modal-body">
                <form id="deleteForm" class="form-horizontal" method="POST" action="apk_version_delete.php">
                    <input type="hidden" class="mobile_app_catid" name="id"> <!-- Hidden input to store the version ID -->
                    <div class="text-center">
                        <p>DELETE MOBILE APPLICATION VERSION</p>
                        <h2 id="mobile_app_del_cat" class="bold"></h2> <!-- Display the version name -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                <button type="button" class="btn btn-danger btn-flat" id="confirmDelete"><i class="fa fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>
</div>