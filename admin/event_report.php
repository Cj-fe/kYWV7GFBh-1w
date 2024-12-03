<?php include 'includes/session.php'; ?>


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
<html>

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
            Event Responses Report
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
            <li>Report </li>
            <li class="active" style="color:white">Event Participation</li>
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
            <!-- Sidebar -->
            <div class="col-md-3">
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
                  <?php include 'includes/subeventreport.php'; ?>
                </div>
                <!-- /.box-body -->
              </div>
            </div>

            <!-- Main Content (Table) -->
            <div class="col-md-9">
              <div class="table-container">
                <div class="box" style="border-radius: 0px !important; ">

                  <div class="box-body" style="border-top: 5px solid #3c8dbc !important">
                    <div class="box-tools pull-right">
                      <form class="form-inline">
                        <!-- Form content here -->
                      </form>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table id="example1" class="table table-bordered printable-table">
                        <thead>
                          <tr>
                            <th style="display:none;"></th>
                            <th>Alumni ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Date Responded</th>
                            <th>Event Participated</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php include 'fetch_data/fetch_dataEventReport.php' ?>
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



    </div>


    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/event_modal.php'; ?>



  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
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
          // Clone the row
          const clonedRow = row.cloneNode(true);
          // Remove any existing checkbox if mistakenly added
          const existingCheckbox = clonedRow.querySelector('td:first-child input[type="checkbox"]');
          if (existingCheckbox) {
            existingCheckbox.remove();
          }
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
          const batchYear = row.cells[4].textContent.trim(); // Assuming the batch year is in the 5th cell
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
        window.open(`event_print.php?data=${encodedData}`, '_blank');

        // Show checkboxes again after the print dialog is opened (optional)
        checkboxes.forEach(checkbox => {
          checkbox.style.display = ''; // Restore default display (could be 'block', 'inline', etc.)
        });
      });
    });

  </script>
</body>

</html>