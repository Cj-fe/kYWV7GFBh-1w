<!-- Delete Modal -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title"><b>Survey <i class="fa fa-angle-right"></i> Question <i class="fa fa-angle-right"></i> Delete</b></h2>
                <hr>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="manage-delete-question" method="POST">
                        <input type="hidden" id="deleteId" name="id">
                        <p>Are you sure you want to delete this question?</p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-flat pull-right btn-class btn-danger">
                                <i class="fa fa-trash"></i> Delete
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
</div>

<!-- jQuery code for handling the delete modal -->
<script>
    $(document).ready(function () {
        // Open delete modal when delete button is clicked
        $('.delete-modal').click(function () {
            var id = $(this).data('id');
            $('#deleteId').val(id);
            $('#deleteModal').modal('show');
        });


    });
</script>
