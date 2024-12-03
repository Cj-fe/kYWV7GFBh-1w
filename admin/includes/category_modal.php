<!-- Add -->
<div class="modal fade" id="addCategory">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Category</b></h4>
      </div>
      <div class="modal-body">
        <form id="addCategoryForm" class="form-horizontal" method="POST" action="category_add.php">
          <div class="form-group">
            <label for="categoryName" class="col-sm-3 control-label">Category Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="categoryName" name="categoryName" required>
              <small id="errorMessage" style="display:none; color:red;">
                <i class="fa fa-info-circle"></i> This Category Already Exists
              </small>
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


<!-- Edit -->
<div class='modal fade' id='editModal' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box-headerModal"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Category</b></h4>
      </div>
      <div class="modal-body">
        <form id="editCategoryForm" class="form-horizontal" method="POST" action="category_edit.php">
          <input type="hidden" id="categoryId" name="categoryId"> <!-- Hidden input to store the category ID -->
          <div class="form-group">
            <label for="categoryName" class="col-sm-3 control-label">Category Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="editCategoryName" name="categoryName" required>
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

<!-- Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Deleting Category...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="category_delete.php">
                <input type="hidden" class="catid" name="categoryId"> <!-- Hidden input to store the category ID -->
                <div class="text-center">
                    <p>DELETE CATEGORY</p>
                    <h2 id="del_cat" class="bold"></h2> <!-- Display the category name -->
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
        </div>
    </div>
</div>


     