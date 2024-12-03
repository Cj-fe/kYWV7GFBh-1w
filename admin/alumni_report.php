<?php include 'includes/session.php'; ?>
<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

function getFirebaseData($firebase, $path)
{
  $data = $firebase->retrieve($path);
  return json_decode($data, true);
}

function sanitizeInput($data)
{
  return htmlspecialchars(strip_tags($data));
}

$alumniData = getFirebaseData($firebase, "alumni");
$batchData = getFirebaseData($firebase, "batch_yr");
$courseData = getFirebaseData($firebase, "course");
$categoryData = getFirebaseData($firebase, "category");

$filterCourse = isset($_GET['course']) ? sanitizeInput($_GET['course']) : '';
$filterBatch = isset($_GET['batch']) ? sanitizeInput($_GET['batch']) : '';
$filterStatus = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$filterWorkClassification = isset($_GET['work_classification']) ? sanitizeInput($_GET['work_classification']) : '';
?>

<html>

<head>
  <?php include 'includes/header.php'; ?>

  <style>
    .box-header {
      position: relative;
      padding-top: 10px;
      border-radius: 0px !important;
      /* Adjust padding to accommodate the pseudo-element */
    }


    .table-responsive {
      overflow-x: auto;
      scrollbar-width: thin;
      /* For Firefox */
      scrollbar-color: #3c8dbc #f0f0f0;
      /* For Firefox */
    }

    .table-responsive {
      overflow-x: auto;
      scrollbar-width: thin;
      /* For Firefox */
      scrollbar-color: #3c8dbc #f0f0f0;
      /* For Firefox */
    }

    .table-responsive::-webkit-scrollbar {
      height: 8px;
      /* Height of the scrollbar */
    }

    .table-responsive::-webkit-scrollbar-track {
      background: #f0f0f0;
      /* Track color */
    }

    .table-responsive::-webkit-scrollbar-thumb {
      background-color: #3c8dbc;
      /* Thumb color */
      border-radius: 10px;
      /* Rounded corners */
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
      background-color: #2a6b8c;
      /* Darker shade for hover effect */
    }

    .table-bordered tbody tr:hover {
      background-color: #3c8dbc;
      /* Background color on hover */
      color: white;
      /* Text color on hover */
    }

    .table-bordered tbody tr:hover td {
      color: white;
      /* Ensure text color changes for all cells */
    }
  </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper content-flex">
      <!-- Main container -->
      <div class="main-container">
        <!-- Content Header (Page header) -->
        <section class="content-header box-header-background">
          <h1>
            Alumni Report
          </h1>
          <div class="box-inline">


            <!-- <a href="#exportnew" data-toggle="modal" class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Import
            </a>

            <a href="#print" data-toggle="modal" id="showModalButton"
              class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-print"></i>&nbsp;&nbsp; Print
            </a>-->

            <a href="#print" data-toggle="modal" id="showModalButton"
              class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-print"></i>&nbsp;&nbsp; Print
            </a>

            <div class="search-container">
              <input type="text" class="search-input" id="search-input" placeholder="Search...">
              <button class="search-button" onclick="filterTable()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="feather feather-search">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </button>
            </div>
          </div>

          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Alumni</li>
            <li class="active" style="color:white">Alumni Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <?php
          if (isset($_SESSION['error'])) {
            echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-bell'></i> Reminder!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
            unset($_SESSION['error']);
          }
          if (isset($_SESSION['success'])) {
            echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
            unset($_SESSION['success']);
          }
          ?>
          <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3">
              <!-- Folders Box -->
              <div class="box box-solid" style="border-radius: 0% !important; ">
                <div class="box-header with-border"
                  style="background:white !important; border-top: 5px solid #3c8dbc !important;">
                  <h3 class="box-title">Folders</h3>
                  <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body no-padding">
                  <?php include 'includes/subalumnireport.php'; ?>
                </div>
                <!-- /.box-body -->
              </div>
            </div>

            <!-- Main Content (Table) -->
            <div class="col-md-9">
              <div class="table-container">
                <div class="box" style="border-radius: 0px !important; ">

                  <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                    <div class="table-responsive">
                      <table id="example1" class="table table-bordered printable-table">
                        <thead>
                          <tr>
                            <th style="display:none;"></th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Date Responded</th>
                            <th>Tools</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php include 'fetch_data/fetch_dataAlumniReport.php' ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>



        </section>
      </div>

      <!-- Sidebar -->

    </div>



    <?php include 'includes/alumni_report_modal.php'; ?>



  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function () {
      var params = new URLSearchParams(window.location.search);

      function updateFilters() {
        var url = new URL(window.location.href);
        url.searchParams.set('status', $('#select_status').val());
        window.location.href = url.toString();
      }

      $('#select_status').change(updateFilters);

      // Set initial values
      var status = params.get('status');
      if (status) {
        $('#select_status').val(status);
      }

    });
  </script>
  <script>
    function escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    }
    $(document).ready(function () {
      // Open edit modal when edit button is clicked
      $('.open-modal').click(function () {
        var id = $(this).data('id');
        $.ajax({
          url: 'alumni_report_row.php',
          type: 'GET',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            $('#reporttId').val(id);
            $('#displayFirstname').text(escapeHtml(response.firstname || 'N/A'));
            $('#displayLastname').text(escapeHtml(response.lastname || 'N/A'));
            $('#displayMiddlename').text(escapeHtml(response.middlename || 'N/A'));
            $('#displayAuxiliaryname').text(escapeHtml(response.auxiliaryname || 'N/A'));
            $('#displayBirthdate').text(escapeHtml(response.birthdate || 'N/A'));
            $('#displayCivilstatus').text(escapeHtml(response.civilstatus || 'N/A'));
            $('#displayMale').text(escapeHtml(response.gender || 'N/A'));
            $('#displayAddressline1').text(escapeHtml(response.addressline1 || 'N/A'));
            $('#displayCity').text(escapeHtml(response.city || 'N/A'));
            $('#displayState').text(escapeHtml(response.state || 'N/A'));
            $('#displayZipcode').text(escapeHtml(response.zipcode || 'N/A'));
            $('#displayContactnumber').text(escapeHtml(response.contactnumber || 'N/A'));
            $('#displayEmail').text(escapeHtml(response.email || 'N/A'));
            $('#date_responded').text(escapeHtml(response.date_responded || 'N/A'));
            $('#displayCourse').text(escapeHtml(response.course_name || 'N/A')); // Display course name
            $('#displayBatch').text(escapeHtml(response.batch_year || 'N/A'));
            $('#displayStudentid').text(escapeHtml(response.studentid || 'N/A'));
            $('#work_status').text(escapeHtml(response.work_status || 'N/A'));
            $('#first_employment_date').text(escapeHtml(response.first_employment_date || 'N/A'));
            $('#date_for_current_employment').text(escapeHtml(response.date_for_current_employment || 'N/A'));
            $('#type_of_work').text(escapeHtml(response.type_of_work || 'N/A'));
            $('#work_position').text(escapeHtml(response.work_position || 'N/A'));
            $('#current_monthly_income').text(escapeHtml(response.current_monthly_income || 'N/A'));
            $('#work_related').text(escapeHtml(response.work_related || 'N/A'));
            $('#work_classification').text(escapeHtml(response.work_classification || 'N/A'));
            $('#name_company').text(escapeHtml(response.name_company || 'N/A'));
            $('#work_employment_status').text(escapeHtml(response.work_employment_status || 'N/A'));
            $('#employment_location').text(escapeHtml(response.employment_location || 'N/A'));
            $('#job_satisfaction').text(escapeHtml(response.job_satisfaction || 'N/A'));

            // Show the edit modal
            $('#reportModal').modal('show');
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
          }
        });
      });
    });


    /*=========Table Modal=============*/

    document.addEventListener('DOMContentLoaded', function () {
      const modalTableBody = document.getElementById('modalTableBody');
      const outsideTableBody = document.querySelector('#example1 tbody');
      const showModalButton = document.getElementById('showModalButton');
      const printModalButton = document.getElementById('printModalButton');
      const removeSelectedButton = document.getElementById('removeSelectedButton');

      showModalButton.addEventListener('click', function () {
        // Clear previous data
        modalTableBody.innerHTML = '';
        // Clone rows from outside table and append to modal table
        Array.from(outsideTableBody.rows).forEach(row => {
          // Clone the row without the last cell (actions cell)
          const clonedRow = row.cloneNode(true);
          clonedRow.deleteCell(-1); // Remove the last cell (actions cell)
          // Remove any existing checkbox if mistakenly added
          clonedRow.querySelector('td:first-child input[type="checkbox"]').remove();
          // Add checkbox to the first cell
          const checkboxCell = document.createElement('td');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.className = 'modal-checkbox';
          checkbox.dataset.id = row.cells[1].textContent.trim(); // Assuming student ID is in the second cell
          checkboxCell.appendChild(checkbox);
          clonedRow.insertBefore(checkboxCell, clonedRow.cells[0]);
          // Append the modified row to modal table
          modalTableBody.appendChild(clonedRow);
        });
        // Show the modal
        $('#dataModal').modal('show');
      });

      removeSelectedButton.addEventListener('click', function () {
        // Remove selected rows from modal table
        const checkboxes = document.querySelectorAll('.modal-checkbox:checked');
        checkboxes.forEach(checkbox => {
          const row = checkbox.closest('tr');
          row.parentNode.removeChild(row);
        });
      });

      printModalButton.addEventListener('click', function () {
        // Temporarily hide checkboxes
        const checkboxes = modalTableBody.querySelectorAll('.modal-checkbox');
        checkboxes.forEach(checkbox => {
          checkbox.style.display = 'none';
        });

        // Collect data to be printed
        const dataToPrint = [];
        const batchYears = new Set();

        Array.from(modalTableBody.rows).forEach(row => {
          // Clone the row to manipulate without the first column
          const clonedRow = row.cloneNode(true);
          clonedRow.deleteCell(0); // Remove the first cell (first column)
          const batchYear = row.cells[5].textContent.trim(); // Assuming the batch year is in the 7th cell
          batchYears.add(batchYear);
          dataToPrint.push({
            content: clonedRow.innerHTML,
            batchYear: batchYear
          });
        });

        // Determine if there are mixed batches
        const isMixedBatch = batchYears.size > 1;

        // Encode the data to be sent via URL
        const encodedData = encodeURIComponent(JSON.stringify({
          dataToPrint: dataToPrint,
          isMixedBatch: isMixedBatch
        }));

        // Redirect to the print page with the encoded data
        window.open(`alumni_print.php?data=${encodedData}`, '_blank');

        // Show checkboxes again after the print dialog is opened (optional)
        checkboxes.forEach(checkbox => {
          checkbox.style.display = ''; // Restore default display (could be 'block', 'inline', etc.)
        });
      });
    });
  </script>
</body>

</html>