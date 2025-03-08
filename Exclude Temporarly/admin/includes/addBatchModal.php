<?php
require_once 'firebaseRDB.php';

// Initialize Firebase URL
require_once 'config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

$batchData = $firebase->retrieve("batch_yr"); // Fetch batch data
$batchData = json_decode($batchData, true);

echo '<script>';
echo 'var existingBatches = ' . json_encode($batchData) . ';';
echo '</script>';
?>
<!-- Modal -->
<div class="modal fade" id="batch-modal">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="box-headerModal"></div>
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>New Batch</b></h4>
            </div>
            <div class="modal-body">
              <form id="addBatchForm" class="form-horizontal" method="POST" action="batch_add.php">
                <div class="form-group">
                    <label for="bacthName" class="col-sm-3 control-label">Batch Yr</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control datepicker-year" id="bacthName" name="bacthName" required autocomplete="off">
                      <small id="batchErrorMessage" style="display:none; color:red;"><i class="fa fa-info-circle"></i> This Batch Year Already Exists</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-flat pull-right btn-class" name="add"
          style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i class="fa fa-save"></i>
          Save</button>
        <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
            class="fa fa-close"></i> Close</button>
              </form>
            </div>
        </div>
    </div>
</div>

<script>
  $(document).ready(function () {
    $('.datepicker-year').datepicker({
      format: 'yyyy',
      viewMode: 'years',
      minViewMode: 'years',
      autoclose: true
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('addBatchForm').addEventListener('submit', function(event) {
        var batchYear = document.getElementById('bacthName').value.trim();
        var isBatchExisting = false;

        // Check if batch year exists
        for (var key in existingBatches) {
            if (existingBatches.hasOwnProperty(key)) {
                if (existingBatches[key]['batch_yrs'].toLowerCase() === batchYear.toLowerCase()) {
                    isBatchExisting = true;
                    break;
                }
            }
        }

        // Show or hide error message based on batch existence
        if (isBatchExisting) {
            event.preventDefault();
            document.getElementById('batchErrorMessage').style.display = 'block';
        } else {
            document.getElementById('batchErrorMessage').style.display = 'none';
        }
    });

    // Hide error message on input
    document.getElementById('bacthName').addEventListener('input', function() {
        var batchYear = this.value.trim();
        var isBatchExisting = false;

        // Check if batch year exists
        for (var key in existingBatches) {
            if (existingBatches.hasOwnProperty(key)) {
                if (existingBatches[key]['batch_yrs'].toLowerCase() === batchYear.toLowerCase()) {
                    isBatchExisting = true;
                    break;
                }
            }
        }

        // Hide or show error message based on current input
        if (isBatchExisting) {
            document.getElementById('batchErrorMessage').style.display = 'block';
        } else {
            document.getElementById('batchErrorMessage').style.display = 'none';
        }
    });
});

</script>
