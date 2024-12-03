<div class="modal fade" id="addnew">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size as needed -->
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"><b>Survey <i class="fa fa-angle-right"></i> Question <i
                            class="fa fa-angle-right"></i>
                        Add</b></h3>
                <hr>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="addSurveySetForm" action="survey_set_add.php" id="manage-question" method="POST">
                        <input type="hidden" name="survey_set_id" value="<?= $id; ?>">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label for="question" class="control-label">Question</label>
                                    <textarea name="question" id="question" cols="30" rows="4" style="height: 90px"
                                        class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="type" class="control-label">Question Answer Type</label>
                                    <select name="type" id="type" class="custom-select custom-select-sm">
                                        <option value="" disabled="" selected>Please Select here</option>
                                        <option value="radio_opt">Single Answer/Radio Button</option>
                                        <option value="check_opt">Multiple Answer/Check Boxes</option>
                                        <option value="textfield_s">Text Field/Text Area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <b>Preview</b>
                                <div class="preview" style="width: 100%;"><!-- Adjust width as needed -->
                                    <center><b>Select Question Answer type first.</b></center>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-flat pull-right btn-class"
                                style="background: linear-gradient(to right, #90caf9, #047edf 99%); color: white;">
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
                            <input type="checkbox" id="checkboxPrimary1" checked="" required>
                            <label for="checkboxPrimary1">
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
                    </td>
                    <td class="text-center"></td>
                </tr>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="2">
                            <input type="checkbox" id="checkboxPrimary2">
                            <label for="checkboxPrimary2">
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
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
                            <input type="radio" id="radioPrimary1" name="radio" checked="" required>
                            <label for="radioPrimary1">
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
                    </td>
                    <td class="text-center"></td>
                </tr>
                <tr class="">
                    <td class="text-center">
                        <div class="icheck-primary d-inline" data-count="2">
                            <input type="radio" id="radioPrimary2" name="radio">
                            <label for="radioPrimary2">
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="text" class="form-control form-control-sm check_inp" name="label[]" required>
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
    function new_check(_this) {
        var tbody = _this.closest('.row').siblings('table').find('tbody');
        var count = tbody.find('tr').length + 1;
        var opt = `
        <tr>
            <td class="text-center pt-1">
                <div class="icheck-primary d-inline" data-count="${count}">
                    <input type="checkbox" id="checkboxPrimary${count}" required>
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

    function new_radio(_this) {
        var tbody = _this.closest('.row').siblings('table').find('tbody');
        var count = tbody.find('tr').length + 1;
        var opt = `
        <tr>
            <td class="text-center pt-1">
                <div class="icheck-primary d-inline" data-count="${count}">
                    <input type="radio" id="radioPrimary${count}" name="radio" required>
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

    function check_opt() {
        var check_opt_clone = $('#check_opt_clone').clone();
        $('.preview').html(check_opt_clone.html());
    }

    function radio_opt() {
        var radio_opt_clone = $('#radio_opt_clone').clone();
        $('.preview').html(radio_opt_clone.html());
    }

    function textfield_s() {
        var textfield_s_clone = $('#textfield_s_clone').clone();
        $('.preview').html(textfield_s_clone.html());
    }

    $('[name="type"]').change(function () {
        window[$(this).val()]();
    });


</script>



<style>
    .mb-3 {
        margin-bottom: 30px;
    }

    .partime-radio,
    .status-radio {
        display: none;
        /* Hide the actual radio buttons */
    }

    .radio-label {
        display: inline-block;
        cursor: pointer;
        padding: 8px 20px;
        margin-right: 10px;
        border-radius: 5px;
        background-color: #e0e0e0;
        /* Default background color */
    }

    /* Styling when radio button is checked */
    .partime-radio:checked+.radio-label,
    .status-radio:checked+.radio-label {
        background-color: #ff5252 !important;
        /* Change background color when checked */
        color: white;
        /* Change text color when checked */
    }

    .right-div {
        float: right;
        /* Float the div to the right */
        width: 30%;
        /* Adjust width as needed */
        padding: 10px;
        /* Add padding for spacing */
        border-left: 1px solid #ccc;
        /* Example border */
        height: 100%;
        /* Ensure it covers the full height if needed */
    }
</style>