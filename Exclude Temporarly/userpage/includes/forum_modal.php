<div class="modal fade" id="forumModal" tabindex="-1" aria-labelledby="forumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary ">
            <h5 class="modal-title"><b>Add New Forum</b></h5>
              

            </div>
            <div class="modal-body">
                <form id="addForumForm" method="POST" action="forum_add.php" onsubmit="return submitForm();">
                    <div class="mb-3">
                        <label for="forumName" class="form-label">Forum Topic</label>
                        <input type="text" class="form-control shadow-sm rounded-pill" id="forumName" name="forumName" required autocomplete="off">
                        <div id="forumErrorMessage" class="form-text text-danger mt-1 d-none">
                            
                        </div>
                    </div>
                    <br><br>
                    <div class="mb-3">
                        <label for="forumDescription" class="form-label">Description</label>
                        <textarea id="editor1" name="editor1" rows="10" cols="80">
                                 
                    </textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
            <button type="submit" form="addForumForm" style="background:red; !important" class="btn btn-primary shadow-sm rounded-pill px-4" data-dismiss="modal" > Cancel
                </button>
                <button type="submit" form="addForumForm" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="fa fa-save me-2"></i>  Save
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteForumModal" tabindex="-1" aria-labelledby="deleteForumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><b>Delete Forum</b></h5>
            </div>
            <div class="modal-body">
                <form id="deleteForumForm">
                    <input type="hidden" id="forum_id" name="forum_id">
                    <p>Are you sure you want to delete this forum? This action cannot be undone.</p>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary shadow-sm rounded-pill px-4" data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" id="confirmDelete" class="btn btn-danger shadow-sm rounded-pill px-4">
                    <i class="fa fa-trash me-2"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>