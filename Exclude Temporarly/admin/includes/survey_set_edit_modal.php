<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size as needed -->
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"><b>Survey <i class="fa fa-angle-right"></i> Question <i
                            class="fa fa-angle-right"></i> Edit</b></h3>
                <hr>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="editQuestionForm" method="POST" action="survey_set_edit.php">
                        <input type="hidden" id="question_id" name="question_id">
                        <input type="hidden" id="editId" name="survey_set_unique_id">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label for="edit_question" class="control-label">Question</label>
                                    <textarea name="question" id="edit_question" cols="30" rows="4" style="height: 90px"
                                        class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="edit_type" class="control-label">Question Answer Type</label>
                                    <select name="type" id="edit_type" class="custom-select custom-select-sm">
                                        <option value="" disabled="" selected>Please Select here</option>
                                        <option value="radio_opt">Single Answer/Radio Button</option>
                                        <option value="check_opt">Multiple Answer/Check Boxes</option>
                                        <option value="textfield_s">Text Field/Text Area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <b>Preview</b>
                                <div id="edit_preview" class="preview" style="width: 100%;"><!-- Adjust width as needed -->
                                    <center><b>Select Question Answer type first.</b></center>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-flat pull-right btn-class"
                                style="background: linear-gradient(to right, #90caf9, #047edf 99%); color: white;">
                                <i class="fa fa-save"></i> Save changes
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

<div id="check_opt_clone" style="display: none">
    <div class="callout callout-info">
        <table width="100%" class="table">
            <colgroup>
                <col width="10%">
                <col width="80%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr class="">
                    <th class="text-center"></th>
                    <th class="text-center">
                        <label for="" class="control-label" style="color:black;">Label</label>
                    </th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="1">
                            <input type="checkbox" id="checkboxPrimary1" checked="">
                            <label for="checkboxPrimary1"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]">
                    </td>
                    <td class="text-center"></td>
                </tr>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="2">
                            <input type="checkbox" id="checkboxPrimary2">
                            <label for="checkboxPrimary2"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]">
                    </td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-sm-12 text-center">
                <button class="btn btn-sm btn-flat btn-default" type="button" onclick="new_check($(this))"><i
                        class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </div>
</div>

<div id="radio_opt_clone" style="display: none">
    <div class="callout callout-info">
        <table width="100%" class="table">
            <colgroup>
                <col width="10%">
                <col width="80%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr class="">
                    <th class="text-center"></th>
                    <th class="text-center">
                        <label for="" class="control-label" style="color:black;">Label</label>
                    </th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="1">
                            <input type="radio" id="radioPrimary1" name="radio" checked="">
                            <label for="radioPrimary1"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]">
                    </td>
                    <td class="text-center"></td>
                </tr>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="2">
                            <input type="radio" id="radioPrimary2" name="radio">
                            <label for="radioPrimary2"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]">
                    </td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-sm-12 text-center">
                <button class="btn btn-sm btn-flat btn-default" type="button" onclick="new_radio($(this))"><i
                        class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </div>
</div>

<div id="textfield_s_clone" style="display: none">
    <div class="callout callout-info">
        <textarea name="frm_opt" id="" cols="30" rows="10" class="form-control" disabled=""
            placeholder="Write Something here..."></textarea>
    </div>
</div>

<script>
    $(document).ready(function () {
    // Function to fetch question data from the server
    function fetchQuestionData(id, successCallback, errorCallback) {
        $.ajax({
            url: 'question_row.php',
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

        // Fetch question data via AJAX
        fetchQuestionData(id, function (response) {
            $('#editId').val(response.survey_set_unique_id);
            $('#question_id').val(id);
            $('#edit_question').val(response.question);
            $('#edit_type').val(response.type);
            $('#edit_type').change(); // Trigger the change event to load the preview

            // Load options for check_opt or radio_opt
            if (response.type === 'check_opt' || response.type === 'radio_opt') {
                var options = JSON.parse(response.frm_option);
                var html = '';
                for (var key in options) {
                    if (options.hasOwnProperty(key)) {
                        html += `
                            <tr>
                                <td class="text-center pt-1">
                                    <div class="icheck-primary d-inline" data-count="${key}">
                                        <input type="${response.type === 'check_opt' ? 'checkbox' : 'radio'}" id="${key}">
                                        <label for="${key}"></label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control form-control-sm check_inp" name="label[]" value="${options[key]}">
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="$(this).closest('tr').remove()">
                                        <span class="fa fa-times"></span>
                                    </a>
                                </td>
                            </tr>
                        `;
                    }
                }
                $('.preview tbody').html(html);
            } else {
                $('.preview').html('<center><b>Select Question Answer type first.</b></center>');
            }

            $('#editModal').modal('show');
        }, function (xhr, status, error) {
            console.error('Error fetching question data:', error);
            alert('An error occurred while fetching question data.');
        });
    });

    // Load preview based on selected type
    $('[name="type"]').change(function () {
        var type = $(this).val();
        if (type === 'check_opt') {
            check_opt();
        } else if (type === 'radio_opt') {
            radio_opt();
        } else if (type === 'textfield_s') {
            textfield_s();
        }
    });
});

// Function to add a new checkbox option
function new_check(_this) {
    var tbody = _this.closest('.row').siblings('table').find('tbody');
    var count = tbody.find('tr').length + 1;
    var opt = `
        <tr>
            <td class="text-center pt-1">
                <div class="icheck-primary d-inline" data-count="${count}">
                    <input type="checkbox" id="checkboxPrimary${count}">
                    <label for="checkboxPrimary${count}"></label>
                </div>
            </td>
            <td class="text-center">
                <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
            </td>
            <td class="text-center">
                <a href="javascript:void(0)" onclick="$(this).closest('tr').remove()">
                    <span class="fa fa-times"></span>
                </a>
            </td>
        </tr>
    `;
    tbody.append(opt);
}

// Function to add a new radio option
function new_radio(_this) {
    var tbody = _this.closest('.row').siblings('table').find('tbody');
    var count = tbody.find('tr').length + 1;
    var opt = `
        <tr>
            <td class="text-center pt-1">
                <div class="icheck-primary d-inline" data-count="${count}">
                    <input type="radio" id="radioPrimary${count}" name="radio">
                    <label for="radioPrimary${count}"></label>
                </div>
            </td>
            <td class="text-center">
                <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
            </td>
            <td class="text-center">
                <a href="javascript:void(0)" onclick="$(this).closest('tr').remove()">
                    <span class="fa fa-times"></span>
                </a>
            </td>
        </tr>
    `;
    tbody.append(opt);
}

// Function to load checkbox options
function check_opt() {
    var check_opt_clone = $('#check_opt_clone').clone();
    $('.preview').html(check_opt_clone.html());
}

// Function to load radio options
function radio_opt() {
    var radio_opt_clone = $('#radio_opt_clone').clone();
    $('.preview').html(radio_opt_clone.html());
}

// Function to load textfield options
function textfield_s() {
    var textfield_s_clone = $('#textfield_s_clone').clone();
    $('.preview').html(textfield_s_clone.html());
}

</script>