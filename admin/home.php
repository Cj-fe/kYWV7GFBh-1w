<?php include 'includes/session.php'; ?>
<?php
include 'includes/timezone.php';
require_once 'includes/config.php';
require_once 'includes/firebaseRDB.php';

$firebase = new firebaseRDB($databaseURL);

$current_year = date("Y");
$default_year = 2020;
$dynamic_year = $default_year + (intval($current_year) - 2024);
$year = isset($_GET['year']) ? $_GET['year'] : $dynamic_year;

// Fetch data from Firebase
$courseData = $firebase->retrieve("course");
$alumniData = $firebase->retrieve("alumni");
$batchYrData = $firebase->retrieve("batch_yr");
$forumData = $firebase->retrieve("forum");
$jobData = $firebase->retrieve("job");
$eventData = $firebase->retrieve("event");
$trackVisitors = $firebase->retrieve("track_visitors");
$newsData = $firebase->retrieve("news");
$newsCommentsData = $firebase->retrieve("news_comments");
$eventCommentsData = $firebase->retrieve("event_comments");
$jobCommentsData = $firebase->retrieve("job_comments");

// Decode JSON data into associative arrays
$courses = json_decode($courseData, true) ?: [];
$alumni = json_decode($alumniData, true) ?: [];
$batchYr = json_decode($batchYrData, true) ?: [];
$forum = json_decode($forumData, true) ?: [];
$jobs = json_decode($jobData, true) ?: [];
$events = json_decode($eventData, true) ?: [];
$trackVisitors = json_decode($trackVisitors, true) ?: [];
$news = json_decode($newsData, true) ?: [];
$newsComments = json_decode($newsCommentsData, true) ?: [];
$eventComments = json_decode($eventCommentsData, true) ?: [];
$jobComments = json_decode($jobCommentsData, true) ?: [];

// Count alumni
$alumnidataCount = is_array($alumni) ? count($alumni) : 0;

// Count active jobs
$jobdataCount = 0;
if (is_array($jobs)) {
  foreach ($jobs as $job) {
    if (isset($job['status']) && $job['status'] === 'Active') {
      $jobdataCount++;
    }
  }
}

// Count events
$eventdataCount = is_array($events) ? count($events) : 0;

// Count forums
$forumCount = is_array($forum) ? count($forum) : 0;

// Filter alumni data for the selected batch year
$filteredAlumni = [];
if (is_array($alumni)) {
  foreach ($alumni as $alumId => $alum) {
    if (isset($alum['batch']) && isset($batchYr[$alum['batch']]['batch_yrs']) && $batchYr[$alum['batch']]['batch_yrs'] == $year) {
      $filteredAlumni[$alumId] = $alum;
    }
  }
}

// Count the number of alumni for each course
$courseMap = [];
if (is_array($courses)) {
  foreach ($courses as $courseId => $course) {
    if (isset($course['courCode'])) {
      $courseMap[$courseId] = $course['courCode'];
    }
  }
}

$courseCounts = array_fill_keys(array_values($courseMap), 0);
if (is_array($filteredAlumni)) {
  foreach ($filteredAlumni as $alum) {
    if (isset($alum['course']) && isset($courseMap[$alum['course']])) {
      $courseCode = $courseMap[$alum['course']];
      $courseCounts[$courseCode]++;
    }
  }
}

// Count employed and unemployed alumni
$employedCount = 0;
$unemployedCount = 0;

if (is_array($alumni)) {
  foreach ($alumni as $alum) {
    if (isset($alum['work_status'])) {
      if ($alum['work_status'] === 'Employed') {
        $employedCount++;
      } elseif ($alum['work_status'] === 'Unemployed') {
        $unemployedCount++;
      }
    }
  }
}

// Convert the course counts array to a JSON string for JavaScript
$courseCountsJson = json_encode(array_values($courseCounts));
$courseCodesJson = json_encode(array_keys($courseCounts));

// Count visitors per month
$visitorCounts = array_fill(0, 12, 0); // Initialize all months with 0

if (is_array($trackVisitors)) {
  foreach ($trackVisitors as $entry) {
    foreach ($entry as $date => $info) {
      if (isset($info['count'])) {
        $month = date('n', strtotime($date)) - 1; // 0-based month index
        $visitorCounts[$month] += $info['count'];
      }
    }
  }
}

$allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$categories = $allMonths;
$visitors = array_values($visitorCounts);

// Count Verified Alumni
$totalAlumni = count($alumni);
$verifiedAlumni = 0;

foreach ($alumni as $alumnus) {
  if (isset($alumnus['status']) && $alumnus['status'] === 'verified') {
    $verifiedAlumni++;
  }
}

$percentVerified = ($totalAlumni > 0) ? round(($verifiedAlumni / $totalAlumni) * 100, 1) : 0;

// Function to get alumni name
function getAlumniName($alumni_id, $alumni)
{
  if (isset($alumni[$alumni_id])) {
    return $alumni[$alumni_id]['firstname'] . ' ' . $alumni[$alumni_id]['lastname'];
  }
  return "Unknown";
}

// Function to get item title
function getItemTitle($item_id, $items)
{
  if (isset($items[$item_id])) {
    return isset($items[$item_id]['news_author']) ? $items[$item_id]['news_author'] :
      (isset($items[$item_id]['event_author']) ? $items[$item_id]['event_author'] :
        (isset($items[$item_id]['job_title']) ? $items[$item_id]['job_title'] : "Unknown"));
  }
  return "Unknown";
}

// Combine all comments
$all_comments = [];
foreach ($newsComments as $id => $comment) {
  $all_comments[] = [
    'type' => 'news',
    'alumni_id' => $comment['alumni_id'],
    'comment' => $comment['comment'],
    'item_id' => $comment['news_id'],
    'date' => strtotime($comment['date_commented'])
  ];
}
foreach ($eventComments as $id => $comment) {
  $all_comments[] = [
    'type' => 'event',
    'alumni_id' => $comment['alumni_id'],
    'comment' => $comment['comment'],
    'item_id' => $comment['event_id'],
    'date' => strtotime($comment['date_commented'])
  ];
}
foreach ($jobComments as $id => $comment) {
  $all_comments[] = [
    'type' => 'job',
    'alumni_id' => $comment['alumni_id'],
    'comment' => $comment['comment'],
    'item_id' => $comment['job_id'],
    'date' => strtotime($comment['date_commented'])
  ];
}

// Sort comments by date, newest first
usort($all_comments, function ($a, $b) {
  return $b['date'] - $a['date'];
});

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <?php include 'includes/header.php'; ?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <!-- Left side column. contains the logo and sidebar -->

    <?php include 'includes/menubar.php'; ?>



    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header box-header-background box-padding">
        <h1>
          Dashboard
        </h1>


        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Dashboard</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Reminder</h4>
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
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <!-- Left Column (70% width) -->
          <div class="col-lg-9 col-md-9 col-sm-12">

            <div class="row">
              <div class="col-lg-3 col-xs-6">
                <div class="small-box" style="background: linear-gradient(to right, #ffbf96, #fe7096) !important;">
                  <div class="inner">
                    <h3><?php echo $alumnidataCount; ?></h3>
                    <p>Total Alumni</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-graduation-cap"></i>
                  </div>
                  <a href="alumni.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <div class="small-box" style="background: linear-gradient(to right, #90caf9, #047edf 99%) !important;">
                  <div class="inner">
                    <h3><?php  echo $forumCount; ?></h3>
                    <p>Forum Topic</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-weixin"></i>
                  </div>
                  <a class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <div class="small-box" style="background: linear-gradient(to right, #84d9d2, #07cdae) !important">
                  <div class="inner">
                    <h3><?php echo $jobdataCount; ?></h3>
                    <p>Active Job</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-tasks"></i>
                  </div>
                  <a href="job.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <div class="small-box" style="background: linear-gradient(to right, #f6e384, #ffd500) !important;">
                  <div class="inner">
                    <h3><?php echo $eventdataCount; ?></h3>
                    <p>Event</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-clock-o"></i>
                  </div>
                  <a href="event.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-6">
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title" style="color:white;">Alumni Report</h3>
                    <div class="box-tools pull-right">
                      <form class="form-inline">
                        <div class="form-group">
                          <label style="color:white;">Select Year: </label>
                          <select class="form-control input-sm" style="height:15px; font-size:10px" id="select_year">
                            <?php
                            for ($i = 2001; $i <= 2065; $i++) {
                              $selected = ($i == $year) ? 'selected' : '';
                              echo "<option value='" . $i . "' " . $selected . ">" . $i . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </form>
                      <script>
                        $(function () {
                          $('#select_year').change(function () {
                            window.location.href = 'home.php?year=' + $(this).val();
                          });
                        });
                      </script>
                    </div>
                  </div>
                  <div class="box-body" style="height: 355px; display: flex; flex-direction: column;">
                    <div id="legend" class="text-center"></div>
                    <div class="chart" style="flex-grow: 1;">
                      <canvas id="myBarChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xs-12 col-md-6">
                <div class="box">
                  <div class="with-border">
                    <div class="box-tools pull-right"></div>
                  </div>
                  <div class="box-body">
                    <div class="card-header"></div>
                    <div class="card-body">
                      <div id="line-chart-1"></div>

                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column (30% width, Empty) -->
          <div class="col-lg-3 col-md-3 col-sm-12">
            <!-- Add content here if needed -->

            <div class="row">
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title" style="color:white;">Alumni Active User</h3>
                    <div class="box-tools pull-right">
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="card" style="background:white; min-height:100px">
                      <div class="card-header">
                      </div>
                      <div class="card-body">
                        <div id="radialBar-chart-1"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title" style="color:white;">Alumni Status</h3>
                    <div class="box-tools pull-right">
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="card" style="background:white;">
                      <div class="card-header">
                      </div>
                      <div class="card-body">
                        <div id="pie-chart-2" style="width:100%"></div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!--   <div class="recent-comments-wrapper">
              <div class="recent-comments-header">
                <h2>Recent Notification
                  <span class="notification-count">0</span>
                </h2>
                <a href="#" class="read-all-btn">Read All</a>
              </div>
              <div id="recent-comments-list" class="recent-comments-list">
                <?php
                $count = 0;
                foreach ($all_comments as $comment):
                  if ($count >= 5)
                    break; // Limit to 5 comments
                  $alumni_name = getAlumniName($comment['alumni_id'], $alumni);
                  $item_title = getItemTitle($comment['item_id'], $comment['type'] == 'news' ? $news : ($comment['type'] == 'event' ? $events : $jobs));
                  ?>
                  <div class="recent-comment-item">
                    <a href="#">
                      <div class="comment-flex">
                        <div class="comment-img">
                          <img
                            src="../use rpage/<?php echo $alumni[$comment['alumni_id']]['profile_url'] ?? 'img/default-avatar.jpg'; ?>"
                            alt="<?php echo $alumni_name; ?>" />
                        </div>
                        <div class="comment-content">
                          <div class="comment-header">
                            <h3><?php echo $alumni_name; ?></h3>
                            <span>on <?php echo $item_title; ?></span>
                          </div>
                          <div class="comment-text">
                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <?php
                  $count++;
                endforeach;
                ?>
                <div class="recent-comment-item view-all">
                  <a href="#">
                    <p>View All</p>
                  </a>
                </div>
              </div>
            </div>-->

          </div>
        </div>
    </div>

  </div>
  </div>


  </section>
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

  </div>
  <?php include 'includes/scripts.php'; ?>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('myBarChart').getContext('2d');

  const courseCodes = <?php echo $courseCodesJson; ?>;
  const courseCounts = <?php echo $courseCountsJson; ?>;

  const myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: courseCodes,
      datasets: [{
        label: 'Number of Alumni',
        data: courseCounts,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false // Hide default legend if you're using a custom one
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            callback: function (value) {
              return Number.isInteger(value) ? value : '';
            }
          }
        }
      }
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var options = {
      chart: {
        height: 355,
        type: 'line',
        zoom: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false,
        width: 2,
      },
      stroke: {
        curve: 'straight',
      },
      colors: ["#4680ff"],
      series: [{
        name: "Visitors",
        data: <?php echo json_encode($visitors); ?>
      }],
      title: {
        text: 'Web Visitor By Month',
        align: 'left'
      },
      grid: {
        row: {
          colors: ['#f3f6ff', 'transparent'],
          opacity: 0.5
        },
      },
      xaxis: {
        categories: <?php echo json_encode($categories); ?>,
      },
      yaxis: {
        labels: {
          formatter: function (value) {
            return Math.round(value);
          }
        }
      }
    };

    var chart = new ApexCharts(
      document.querySelector("#line-chart-1"),
      options
    );
    chart.render();
  });


  $(function () {
    var options = {
      chart: {
        height: 220,
        type: 'radialBar',
      },
      plotOptions: {
        radialBar: {
          hollow: {
            size: '70%',
          },
          dataLabels: {
            name: {
              fontSize: '22px',
            },
            value: {
              fontSize: '16px',
            },
            total: {
              show: true,
              label: 'Verified Alumni',
              formatter: function (w) {
                return '<?php echo $verifiedAlumni; ?> out of <?php echo $totalAlumni; ?>';
              }
            }
          }
        },
      },
      colors: ["#4680ff"],
      series: [<?php echo $percentVerified; ?>],
      labels: ['Verified Alumni'],
    }
    var chart = new ApexCharts(
      document.querySelector("#radialBar-chart-1"),
      options
    );
    chart.render();
  });
  $(function () {
    var options = {
      chart: {
        height: 220,
        type: 'donut',
      },
      series: [<?php echo $employedCount; ?>, <?php echo $unemployedCount; ?>],
      labels: ['Employed', 'Unemployed'],
      colors: ["#4680ff", "#ff5252"],
      legend: {
        show: true,
        position: 'bottom',
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                show: true
              },
              value: {
                show: true,
                formatter: function (val) {
                  return Math.round(val);
                }
              },
              total: {
                show: false
              }
            }
          }
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function (val, opts) {
          return opts.w.config.labels[opts.seriesIndex];
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom'
          }
        }
      }]
    }
    var chart = new ApexCharts(
      document.querySelector("#pie-chart-2"),
      options
    );
    chart.render();
  });
</script>
