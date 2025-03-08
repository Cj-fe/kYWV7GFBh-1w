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
$filterStatus = isset($_GET['work_status']) ? sanitizeInput($_GET['work_status']) : '';
$filterWorkClassification = isset($_GET['work_classification']) ? sanitizeInput($_GET['work_classification']) : '';

// Initialize counters
$employedCount = 0;
$unemployedCount = 0;
$totalCount = 0;


?>

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
            Alumni Status
          </h1>
          <div class="box-inline">

            <!--  <a href="#print" data-toggle="modal" id="showModalButton"
              class="btn-add-class btn btn-primary btn-sm btn-flat">
              <i class="fa fa-print"></i>&nbsp;&nbsp; Print
            </a>-->

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
            <li class="active">Field of Work</li>
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
            <!-- Folders Box -->
            <div class="col-md-3"> <!-- Adjust the column size as needed -->
              <div class="box box-solid" style="border-radius: 0% !important; ">
                <div class="box-header with-border" style="background:white !important; border-top: 5px solid #3c8dbc !important;">
                  <h3 class="box-title">Folders</h3>
                  <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body no-padding">
                
                <?php include 'includes/submenufieldofwork.php'; ?>
                </div>
                <!-- /.box-body -->
              </div>
            </div>

            <!-- Table Container -->
            <div class="col-md-9"> <!-- Adjust the column size as neeed -->
              <div class="table-container">
                <div class="box" style="border-radius: 0px !important; border-top: 5px solid #3c8dbc !important">
                  <div class="box-header" >
                    <div class="box-tools pull-right">
                      <!-- Existing form and dropdown -->
                      <form class="form-inline">
                        <div class="form-group">
                          <label style="color:white;">Select Status: </label>
                          <select class="form-control input-sm" style="height:25px; font-size:10px"
                            id="select_work_classification">
                            <option value="">All</option>
                            <?php
                            $categoryData = getFirebaseData($firebase, "category");
                            if (!empty($categoryData) && is_array($categoryData)) {
                              foreach ($categoryData as $categoryId => $categoryDetails) {
                                $categoryName = isset($categoryDetails['category_name']) ? htmlspecialchars($categoryDetails['category_name']) : 'Unknown';
                                echo "<option value=\"" . htmlspecialchars($categoryId) . "\">" . $categoryName . "</option>";
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="box-body" >
                    <div class="table-responsive">
                      <table id="example1" class="table table-bordered">
                        <thead>
                          <tr>
                            <th style="display:none;"></th>
                            <th>Profile</th>
                            <th>Alumni ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th width="30%">Work</th>
                            <th>Tools</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php include 'fetch_data/fetch_dataFieldOfWork.php' ?>
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



      <?php include 'includes/footer.php'; ?>



    </div>
    <?php include 'includes/scripts.php'; ?>
    
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function collapseOthers(currentElement, elementClass) {
            const elements = document.getElementsByClassName(elementClass);
            Array.from(elements).forEach(element => {
                if (element !== currentElement) {
                    element.classList.remove("active");
                    const associatedContent = element.nextElementSibling;
                    if (associatedContent) {
                        associatedContent.classList.remove("active");
                    }
                }
            });
        }

        function initializeCollapsible(elementClass) {
            const collapsibleElements = document.getElementsByClassName(elementClass);
            Array.from(collapsibleElements).forEach(element => {
                element.addEventListener("click", function () {
                    collapseOthers(this, elementClass);
                    this.classList.toggle("active");
                    const associatedContent = this.nextElementSibling;
                    if (associatedContent) {
                        associatedContent.classList.toggle("active");
                    }
                });
            });
        }

        initializeCollapsible("collapsible");

        // Program selection handlers
        document.querySelectorAll('.collaps-department').forEach(programBtn => {
            programBtn.addEventListener('click', function (evt) {
                evt.preventDefault();
                const programId = this.getAttribute('data-program-id');
                const classYearId = this.getAttribute('data-class-year-id');
                const statusSelect = this.nextElementSibling;

                statusSelect.style.display =
                    statusSelect.style.display === 'none' ? 'inline-block' : 'none';

                if (statusSelect.style.display === 'none') {
                    window.location.href =
                        `field_of_work.php?course=${programId}&batch=${classYearId}`;
                }
            });
        });

        // Employment status selection handlers
        document.querySelectorAll('.employment-status-select').forEach(statusSelect => {
            statusSelect.addEventListener('change', function () {
                const programBtn = this.previousElementSibling;
                const programId = programBtn.getAttribute('data-program-id');
                const classYearId = programBtn.getAttribute('data-class-year-id');
                const employmentStatus = this.value;

                if (employmentStatus) {
                    window.location.href =
                        `field_of_work.php?course=${programId}&batch=${classYearId}&work_status=${employmentStatus}`;
                }
            });
        });
    });
</script>
</body>

</html>