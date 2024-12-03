<!-- Add -->


<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.replace) {
            CKEDITOR.replace('job_description');
        } else {
            console.error('CKEditor is not defined or replace method is missing.');
        }
    });


    document.addEventListener("DOMContentLoaded", function () {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.replace) {
            CKEDITOR.replace('expertise_specification');
        } else {
            console.error('CKEditor is not defined or replace method is missing.');
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.replace) {
            CKEDITOR.replace('about_the_role');
        } else {
            console.error('CKEditor is not defined or replace method is missing.');
        }
    });
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }



    function validateAddForm() {
        var isValid = true;

        // Synchronize CKEditor content with the textarea
        if (typeof CKEDITOR !== 'undefined') {
            if (CKEDITOR.instances['job_description']) {
                CKEDITOR.instances['job_description'].updateElement();
            }
            if (CKEDITOR.instances['expertise_specification']) {
                CKEDITOR.instances['expertise_specification'].updateElement();
            }
            if (CKEDITOR.instances['about_the_role']) {
                CKEDITOR.instances['about_the_role'].updateElement();
            }
        }

        // Validate job_title
        var jobTitle = document.getElementById('job_title').value;
        var jobTitleError = document.getElementById('job_title_error');
        if (jobTitle.trim() === "") {
            jobTitleError.style.display = 'block';
            isValid = false;
        } else {
            jobTitleError.style.display = 'none';
        }

        // Validate company_name
        var companyName = document.getElementById('company_name').value;
        var companyNameError = document.getElementById('company_error');
        if (companyName.trim() === "") {
            companyNameError.style.display = 'block';
            isValid = false;
        } else {
            companyNameError.style.display = 'none';
        }

        // Validate work_time (radio buttons)
        var workTimeChecked = document.querySelector('input[name="work_status"]:checked');
        var workTimeError = document.getElementById('job_worktime_error');
        if (!workTimeChecked) {
            workTimeError.style.display = 'block';
            isValid = false;
        } else {
            workTimeError.style.display = 'none';
        }

        // Validate status (radio buttons)
        var statusChecked = document.querySelector('input[name="status"]:checked');
        var statusError = document.getElementById('status_error');
        if (!statusChecked) {
            statusError.style.display = 'block';
            isValid = false;
        } else {
            statusError.style.display = 'none';
        }

        // Validate job_description
        var jobDescription = document.getElementById('job_description').value;
        var jobDescriptionError = document.getElementById('desc_error');
        if (jobDescription.trim() === "") {
            jobDescriptionError.style.display = 'block';
            isValid = false;
        } else {
            jobDescriptionError.style.display = 'none';
        }

        if (isValid) {
            document.getElementById('submitButton').disabled = true;
        }

        return isValid;
    }


    function validateEditForm() {
        var isValid = true;

        // Validate edit_job_title
        var editJobTitle = document.getElementById('edit_job_title').value;
        var editJobTitleError = document.getElementById('edit_title_error');
        if (editJobTitle.trim() === "") {
            editJobTitleError.style.display = 'block';
            isValid = false;
        } else {
            editJobTitleError.style.display = 'none';
        }

        // Validate edit_company_name
        var editCompanyName = document.getElementById('edit_company_name').value;
        var editCompanyNameError = document.getElementById('edit_company_error');
        if (editCompanyName.trim() === "") {
            editCompanyNameError.style.display = 'block';
            isValid = false;
        } else {
            editCompanyNameError.style.display = 'none';
        }

        // Validate edit_work_status (radio buttons)
        var editWorkStatusChecked = document.querySelector('input[name="edit_work_status"]:checked');
        var editWorkStatusError = document.getElementById('edit_worktime_error');
        if (!editWorkStatusChecked) {
            editWorkStatusError.style.display = 'block';
            isValid = false;
        } else {
            editWorkStatusError.style.display = 'none';
        }

        // Validate edit_status (radio buttons)
        var editStatusChecked = document.querySelector('input[name="edit_status"]:checked');
        var editStatusError = document.getElementById('edit_status_error');
        if (!editStatusChecked) {
            editStatusError.style.display = 'block';
            isValid = false;
        } else {
            editStatusError.style.display = 'none';
        }

        // Validate edit_job_description
        var editJobDescription = document.getElementById('edit_description').value;
        var editJobDescriptionError = document.getElementById('edit_desc_error');
        if (editJobDescription.trim() === "") {
            editJobDescriptionError.style.display = 'block';
            isValid = false;
        } else {
            editJobDescriptionError.style.display = 'none';
        }

        return isValid;
    }



</script>
<script>
    $(document).ready(function () {
        $('#job_categories').select2({
            placeholder: "Select job categories",
            allowClear: true
        });

        // Optional: Show error message if required
        $('#job_categories').on('change', function () {
            if ($(this).val() == null) {
                $('#categories_error').show();
            } else {
                $('#categories_error').hide();
            }
        });
    });

    $(document).ready(function () {
        $('#edit_job_categories').select2({
            placeholder: "Select job categories",
            allowClear: true
        });

        // Optional: Show error message if required
        $('#edit_job_categories').on('change', function () {
            if ($(this).val() == null) {
                $('#categories_error').show();
            } else {
                $('#categories_error').hide();
            }
        });
    });
</script>


<div class="modal fade" id="addnew">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Job <i
                            class="fa fa-angle-right"></i> Add</b></h2>
                <hr>
            </div>
            <div class="modal-body">
                <form id="addJobForm" class="form-horizontal" method="POST" action="job_add.php"
                    enctype="multipart/form-data" onsubmit="return validateAddForm()">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <div class="personal_information">
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Primary Details</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="job_title" class="col-form-label">Job Title</label>
                                        <input type="text" class="form-control" id="job_title" name="job_title"
                                            required>
                                        <small class="error-message" id="job_title_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="company_name" class="col-form-label">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                            required>
                                        <small class="error-message" id="company_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="location" class="col-form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" required>
                                        <small class="error-message" id="location_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="salary_range" class="col-form-label">Salary Range</label>
                                        <input type="text" class="form-control" id="salary_range" name="salary_range"
                                            placeholder="e.g., $50,000 - $70,000">
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="work_time" class="col-form-label">Work Time</label><br>
                                        <input type="radio" id="part_time" name="work_status" value="Part-Time"
                                            class="partime-radio" required>
                                        <label for="part_time" class="radio-label">Part Time</label>
                                        <input type="radio" id="full_time" name="work_status" value="Full-Time"
                                            class="partime-radio" required>
                                        <label for="full_time" class="radio-label">Full Time</label>
                                        <small class="error-message" id="job_worktime_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="status" class="col-form-label">Status</label><br>
                                        <input type="radio" id="archive" name="status" value="Archive"
                                            class="status-radio" required>
                                        <label for="archive" class="radio-label">Archive</label>
                                        <input type="radio" id="active" name="status" value="Active"
                                            class="status-radio" required>
                                        <label for="active" class="radio-label">Active</label>
                                        <small class="error-message" id="status_error"
                                            style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                            field is required.</small>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="job_categories" class="col-form-label">Job Categories</label>
                                        <select class="form-control" id="job_categories" name="job_categories[]"
                                            multiple required style="width: 100%; box-sizing: border-box;">
                                            <option value="Engineering">Engineering</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Human Resources">Human Resources</option>
                                            <option value="IT">Information Technology</option>
                                            <option value="Customer Service">Customer Service</option>
                                            <option value="Administration">Administration</option>
                                            <option value="Education">Education</option>
                                            <option value="Healthcare">Healthcare</option>
                                            <option value="Legal">Legal</option>
                                            <option value="Operations">Operations</option>
                                            <option value="Product Management">Product Management</option>
                                            <option value="Project Management">Project Management</option>
                                            <option value="Research and_Development">Research and Development</option>
                                            <option value="Supply_chain">Supply Chain</option>
                                            <option value="Design">Design</option>
                                            <option value="Quality_assurance">Quality Assurance</option>
                                            <option value="Manufacturing">Manufacturing</option>
                                            <option value="Logistics">Logistics</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                        <small class="error-message" id="categories_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.
                                        </small>
                                    </div>
                                    <div class="col-sm-1 mb-3 d-flex justify-content-between">

                                        <div class="image-upload-container">
                                            <label for="picture" class="col-form-label">Banner</label><br>
                                            <div class="image-preview" id="imagePreviewAdd">
                                                <img id="imagePreviewImg" src="" alt="Image Preview"
                                                    style="display:none;">
                                                <div class="upload-controls">
                                                    <label for="imageUploadAdd" class="image-upload-label">Edit</label>
                                                    <input type="file" id="imageUploadAdd" name="imageUpload"
                                                        accept="image/*"
                                                        onchange="previewImage(event, 'imagePreviewImg')"
                                                        style="display:none;">
                                                </div>
                                            </div>
                                            <h5 class="h5">Thumbnail</h5>
                                            <small class="error-message" id="imageUploadAdd_error"
                                                style="color:red; display:none;"><i class="fa fa-info-circle"></i> This
                                                field is required.</small>
                                        </div>

                                        <div class="image-upload-container">
                                            <label for="picture" class="col-form-label">Company Logo</label><br>
                                            <div class="image-preview" id="imagePreviewAddLogo">
                                                <img id="imagePreviewImgLogo" src="" alt="Image Preview"
                                                    style="display:none;">
                                                <div class="upload-controls">
                                                    <label for="imageUploadAddLogo"
                                                        class="image-upload-label">Edit</label>
                                                    <input type="file" id="imageUploadAddLogo" name="imageUploadlogo"
                                                        accept="image/*"
                                                        onchange="previewImage(event, 'imagePreviewImgLogo')"
                                                        style="display:none;">
                                                </div>
                                            </div>
                                            <h5 class="h5">Thumbnail</h5>
                                            <small class="error-message" id="imageUploadAdd_error"
                                                style="color:red; display:none;">
                                                <i class="fa fa-info-circle"></i> This field is required.
                                            </small>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="job_description" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Job Description</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="job_description" class="col-form-label">Description</label>
                                        <textarea class="form-control" id="job_description" name="job_description"
                                            required></textarea>
                                        <small class="error-message" id="desc_error" style="color:red; display:none;"><i
                                                class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="expertise_specification" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Expertise Specification</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="expertise_specification" class="col-form-label">Description</label>
                                        <textarea class="form-control" id="expertise_specification"
                                            name="expertise_specification" required></textarea>
                                        <small class="error-message" id="desc_error" style="color:red; display:none;"><i
                                                class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="about_the_role" class="col-sm-3 col-form-label text-sm-end">
                                <h4>About the Role</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label for="about_the_role" class="col-form-label">Description</label>
                                        <textarea class="form-control" id="about_the_role" name="about_the_role"
                                            required></textarea>
                                        <small class="error-message" id="desc_error" style="color:red; display:none;"><i
                                                class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton" class="btn btn-flat pull-right btn-class" name="add"
                    style="background:linear-gradient(to right, #90caf9, #047edf 99%); color:white;"><i
                        class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-default btn-flat btn-class" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit -->


<div class='modal fade' id='editModal' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="box-headerModal"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title"><b>Content <i class="fa fa-angle-right"></i> Job <i
                            class="fa fa-angle-right"></i> Update</b></h2>
                <hr>
            </div>
            <div class="modal-body">
                <form id="editJobForm" class="form-horizontal" method="POST" action="job_edit.php"
                    enctype="multipart/form-data" onsubmit="return validateEditForm()">
                    <input type="hidden" id="editId" name="id">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <div class="personal_information">
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="firstname" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Primary Details</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <!-- Job Title -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_job_title" class="col-form-label">Job Title</label>
                                        <input type="text" class="form-control" id="edit_job_title"
                                            name="edit_job_title">
                                        <small class="error-message" id="edit_title_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <!-- Company Name -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_company_name" class="col-form-label">Company Name</label>
                                        <input type="text" class="form-control" id="edit_company_name"
                                            name="edit_company_name">
                                        <small class="error-message" id="edit_company_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_location" class="col-form-label">Location</label>
                                        <input type="text" class="form-control" id="edit_location" name="edit_location">
                                    </div>

                                    <!-- Salary -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_salary_range" class="col-form-label">Salary Range</label>
                                        <input type="text" class="form-control" id="edit_salary_range"
                                            name="edit_salary_range" placeholder="e.g., $50,000 - $70,000">
                                    </div>

                                    <!-- Work Time -->
                                    <div class="col-sm-6 mb-3">
                                        <label for="work_time" class="col-form-label">Work Time</label><br>
                                        <input type="radio" id="edit_parttime" name="edit_work_status" value="Part-Time"
                                            class="partime-radio">
                                        <label for="edit_parttime" class="radio-label">Part Time</label>
                                        <input type="radio" id="edit_fulltime" name="edit_work_status" value="Full-Time"
                                            class="partime-radio">
                                        <label for="edit_fulltime" class="radio-label">Full Time</label>
                                        <small class="error-message" id="edit_worktime_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-sm-6 mb-3">
                                        <label for="status" class="col-form-label">Status</label><br>
                                        <input type="radio" id="edit_statusArchive" name="edit_status" value="Archive"
                                            class="status-radio">
                                        <label for="edit_statusArchive" class="radio-label">Archive</label>
                                        <input type="radio" id="edit_statusActive" name="edit_status" value="Active"
                                            class="status-radio">
                                        <label for="edit_statusActive" class="radio-label">Active</label>
                                        <small class="error-message" id="edit_status_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <!-- Job Categories -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_job_categories" class="col-form-label">Job Categories</label>
                                        <select class="form-control" id="edit_job_categories"
                                            name="edit_job_categories[]" multiple required
                                            style="width: 100%; box-sizing: border-box;">
                                            <option value="Engineering">Engineering</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Human Resources">Human Resources</option>
                                            <option value="Information Technology">Information Technology</option>
                                            <option value="Customer Service">Customer Service</option>
                                            <option value="Administration">Administration</option>
                                            <option value="Education">Education</option>
                                            <option value="Healthcare">Healthcare</option>
                                            <option value="Legal">Legal</option>
                                            <option value="Operations">Operations</option>
                                            <option value="Product Management">Product Management</option>
                                            <option value="Project Management">Project Management</option>
                                            <option value="Research and_Development">Research and Development</option>
                                            <option value="Supply Chain">Supply Chain</option>
                                            <option value="Design">Design</option>
                                            <option value="Quality_assurance">Quality Assurance</option>
                                            <option value="Manufacturing">Manufacturing</option>
                                            <option value="Logistics">Logistics</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                        <small class="error-message" id="categories_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.
                                        </small>
                                    </div>


                                    <!-- Picture -->
                                    <div class="col-sm-1 mb-3 d-flex justify-content-between">
                                        <div class="image-upload-container">
                                            <label for="imageUploadEdit" class="col-form-label">Picture</label><br>
                                            <div class="image-preview" id="imagePreviewEdit">
                                                <img id="imagePreviewImg2" src="" alt="Image Preview"
                                                    style="display:none;">
                                                <div class="upload-controls">
                                                    <label for="imageUploadEdit" class="image-upload-label">Edit</label>
                                                    <input type="file" id="imageUploadEdit" name="imageUpload"
                                                        accept="image/*"
                                                        onchange="previewImage(event, 'imagePreviewImg2')"
                                                        style="display:none;">
                                                </div>
                                            </div>
                                            <h5 class="h5">Thumbnail</h5>
                                            <small class="error-message" id="imageUploadEdit_error"
                                                style="color:red; display:none;">
                                                <i class="fa fa-info-circle"></i> This field is required.</small>
                                        </div>

                                        <div class="image-upload-container">
                                            <label for="imageUploadEditLogo" class="col-form-label">Company
                                                Logo</label><br>
                                            <div class="image-preview" id="imagePreviewAddLogo">
                                                <img id="imagePreviewImgLogo2" src="" alt="Image Preview"
                                                    style="display:none;">
                                                <div class="upload-controls">
                                                    <label for="imageUploadEditLogo"
                                                        class="image-upload-label">Edit</label>
                                                    <input type="file" id="imageUploadEditLogo" name="imageUploadlogo"
                                                        accept="image/*"
                                                        onchange="previewImage(event, 'imagePreviewImgLogo2')"
                                                        style="display:none;">
                                                </div>
                                            </div>
                                            <h5 class="h5">Thumbnail</h5>
                                            <small class="error-message" id="imageUploadAdd_error"
                                                style="color:red; display:none;">
                                                <i class="fa fa-info-circle"></i> This field is required.</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Other Details -->
                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="address" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Job Description</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_description" class="col-form-label">Description</label>
                                        <textarea class="form-control" id="edit_description"
                                            name="edit_job_description"></textarea>
                                        <small class="error-message" id="edit_desc_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <div class="col-sm-6"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="address" class="col-sm-3 col-form-label text-sm-end">
                                <h4>Expertise Specification</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_expertise_specification"
                                            class="col-form-label">Description</label>
                                        <textarea class="form-control" id="edit_expertise_specification"
                                            name="edit_expertise_specification"></textarea>
                                        <small class="error-message" id="edit_edit_expertise_specification_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <div class="col-sm-6"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" style="border-bottom: 1px solid silver !important;">
                            <label for="address" class="col-sm-3 col-form-label text-sm-end">
                                <h4>About The Role</h4>
                            </label>
                            <div class="col-sm-7">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-sm-12 mb-3">
                                        <label for="edit_about_the_role" class="col-form-label">Description</label>
                                        <textarea class="form-control" id="edit_about_the_role"
                                            name="edit_about_the_role"></textarea>
                                        <small class="error-message" id="edit_about_the_role_error"
                                            style="color:red; display:none;">
                                            <i class="fa fa-info-circle"></i> This field is required.</small>
                                    </div>

                                    <div class="col-sm-6"></div>
                                </div>
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
                <form id="deleteJobForm" class="form-horizontal" method="POST" action="job_delete.php">
                    <input type="hidden" class="deleteId" name="id">
                    <div class="text-center">
                        <p>Are you sure you want to delete this Job?</p>
                        <h2 class="deleteTitle"></h2>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default pull-right btn-flat btn-class" name="delete"
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