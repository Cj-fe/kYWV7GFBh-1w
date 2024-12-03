<?php
// Include the FirebaseRDB class file
require_once 'includes/firebaseRDB.php';

require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Fetch data from Firebase
$courseKey = "course"; // Replace with your actual Firebase path or key for courses

$data = $firebase->retrieve($courseKey);
$data = json_decode($data, true); // Decode JSON data into associative arrays

$batchYears = $firebase->retrieve("batch_yr");
$batchYears = json_decode($batchYears, true); // Decode JSON data into associative arrays
?>


<style>
    .modal-dialog-centered {

        display: flex;
        align-items: center;
    }

    .modal-lg {
        max-width: 800px;
        /* Adjust for A4 size approximation */
    }

    .modal-content {
        width: 100%;
    }

    .modal-header {

        justify-content: space-between;
        align-items: center;
    }

    .view-report-details {
        margin-right: 10px;
        /* Adjust as needed for spacing */
    }


    .information-section {
        margin-bottom: 20px;
        border: 3px solid silver;
        padding: 45px;
        border-radius: 8px;
        /* Rounded corners for a card-like appearance */

    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 25px;
        color: #f85252;


    }

    .info-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;

        border-bottom: 2px solid silver;
        /* Horizontal line below each label */
        padding-bottom: 8px;
        padding-top: 8px;
        padding-right: 10px;
    }

    .info-label {
        flex: 1;
        font-weight: bold;
        padding-right: 20px;
        /* Ensure at least 20px space between label and content */
    }

    .info-content {
        flex: 2;
        padding-left: 20px;
        /* Ensure at least 20px space between label and content */
    }

    /* Add padding and other styles as needed */
    .view-report-details {
        font-size: 20px;
    }
</style>
<div class='modal fade' id='reportModal' tabindex='-1' role='dialog' aria-labelledby='editModalLabel'
    aria-hidden='true'>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">

                <a class="btn view-report-details">View Report Details <h5>Date Responded: <span
                            id="date_responded"></span></h5></a>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">



                <div class="information-section">
                    <div class="section-title">Personal Information</div>
                    <div class="info-row">
                        <div class="info-label">Alumni ID</div>
                        <div class="info-content"><span id="displayStudentid"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Alumni Name</div>
                        <div class="info-content"><span id="displayAuxiliaryname"></span> <span
                                id="displayFirstname"></span> <span id="displayMiddlename"></span> <span
                                id="displayLastname"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Birthdate</div>
                        <div class="info-content"><span id="displayBirthdate"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Civil Status</div>
                        <div class="info-content"><span id="displayCivilstatus"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Sex</div>
                        <div class="info-content"><span id="displayMale"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address</div>
                        <div class="info-content"><span id="displayAddressline1"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">City</div>
                        <div class="info-content"><span id="displayCity"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">State</div>
                        <div class="info-content"><span id="displayState"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Zip Code</div>
                        <div class="info-content"><span id="displayZipcode"></span></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Contact Number</div>
                        <div class="info-content"><span id="displayContactnumber"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-content"><span id="displayEmail"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Course</div>
                        <div class="info-content"><span id="displayCourse"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Batch</div>
                        <div class="info-content"><span id="displayBatch"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Employment Status</div>
                        <div class="info-content"><span id="work_status"></span></div>
                    </div>
                    <!-- Add more personal information rows as needed -->
                </div>
                <div class="information-section">
                    <div class="section-title">Employment Information</div>

                    <!-- Employment History -->
                    <div class="info-row">
                        <div class="info-label">First Employment</div>
                        <div class="info-content"><span id="first_employment_date"></span></div>
                    </div>

                    <!-- Current Employment -->
                    <div class="info-row">
                        <div class="info-label">Current Employment</div>
                        <div class="info-content"><span id="date_for_current_employment"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Name of the Organization/Company</div>
                        <div class="info-content"><span id="name_company"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Current Employment Status</div>
                        <div class="info-content"><span id="work_employment_status"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Location of Employment</div>
                        <div class="info-content"><span id="employment_location"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Work Position</div>
                        <div class="info-content"><span id="work_position"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Type Of Work</div>
                        <div class="info-content"><span id="type_of_work"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Monthly Income</div>
                        <div class="info-content"><span id="current_monthly_income"></span></div>
                    </div>

                    <!-- Job Satisfaction -->
                    <div class="info-row">
                        <div class="info-label">Job Satisfaction</div>
                        <div class="info-content"><span id="job_satisfaction"></span></div>
                    </div>

                    <!-- Relation to Education -->
                    <div class="info-row">
                        <div class="info-label">Is work related to course?</div>
                        <div class="info-content"><span id="work_related"></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">General Classification of the Type of Job</div>
                        <div class="info-content"><span id="work_classification"></span></div>
                    </div>

                    <!-- Add more employment information rows as needed -->
                </div>

            </div>
        </div>
    </div>
</div>



<!--==============Print File=================-->

<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Alumni Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="printTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th> <!-- Checkbox column -->
                           
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Date Responded</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody">
                        <!-- Data will be copied here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger mb-2" id="removeSelectedButton">Remove Selected</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printModalButton">Print</button>
            </div>
        </div>
    </div>
</div>