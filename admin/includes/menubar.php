<?php
// Function to count unique keys in a node
function countUniqueKeys($firebase, $node)
{
  $data = $firebase->retrieve($node);
  $data = json_decode($data, true);

  return is_array($data) ? count(array_keys($data)) : 0;
}

// Function to check if there are new log entries
function checkLogNotifications($firebase, $adminNotificationTimestamp) {
    // Retrieve logs from Firebase
    $logsData = $firebase->retrieve("logs");
    $logs = json_decode($logsData, true) ?: [];
    
    // Convert admin's notification timestamp to Unix timestamp
    $notificationTime = strtotime($adminNotificationTimestamp);
    
    // Check if any log entries are newer than the notification timestamp
    $newLogEntries = array_filter($logs, function($log) use ($notificationTime) {
        return strtotime($log['timestamp']) > $notificationTime;
    });
    
    // Return the count of new log entries
    return count($newLogEntries);
}

// Count unique keys in each node
$newsCount = countUniqueKeys($firebase, "deleted_news");
$alumniCount = countUniqueKeys($firebase, "deleted_alumni");
$jobCount = countUniqueKeys($firebase, "deleted_job");
$galleryCount = countUniqueKeys($firebase, "deleted_gallery");
$surveyCount = countUniqueKeys($firebase, "deleted_survey_set");
$eventCount = countUniqueKeys($firebase, "deleted_event");

// Sum the counts
$totalCount = $newsCount + $alumniCount + $jobCount + $galleryCount + $surveyCount + $eventCount;

// Check for new log entries
$newLogCount = checkLogNotifications($firebase, $adminData['notification_timestamp']);
?>

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?php echo (!empty($user['image_url'])) ? $user['image_url'] : 'uploads/profile.png'; ?>"
          class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></p>
        <a><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">REPORTS</li>
      <li class=""><a href="home.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li class="header">MANAGE</li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-graduation-cap "></i>
          <span>Alumni</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="alumni.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Manage Alumni</a></li>
          <li><a href="field_of_work.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Alumni Status</a></li>
          <li><a href="category.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Category</a></li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-newspaper-o "></i>
          <span>Contents</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right "></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="news.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa fa-angle-right"></i> News</a></li>
          <li><a href="event.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Events</a></li>
          <li><a href="gallery.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Gallery</a></li>
          <li><a href="job.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Job Offer</a></li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-check-square-o "></i>
          <span>Survey</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="survey.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Survey List</a></li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-book"></i>
          <span>Report</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="survey_report.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Survey Report</a></li>
          <li><a href="alumni_report.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Alumni Report</a></li>
          <li><a href="event_report.php?token_url=<?php echo urlencode($adminData['token_url']); ?>"><i class="fa fa-angle-right"></i> Event Report</a></li>
        </ul>
      </li>
       <li>
       <a href="timeline.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
            <i class="fa fa-th"></i> 
            <span>Logs</span>
            <span class="pull-right-container">
              <?php if ($newLogCount > 0): ?>
                <small class="label pull-right bg-green">new</small>
              <?php endif; ?>
            </span>
          </a>
        </li>
      
      <li class="treeview">
        <a href="#">
          <i class="fa fa-trash"></i>
          <span>Trash</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i> &nbsp; &nbsp; &nbsp;
            <span class="pull-right-container">
              <small class="label pull-right bg-red"><?php echo $totalCount; ?></small>
            </span>
          </span>
        </a>
        <ul class="treeview-menu">
          <li>
            <a href="deleted_alumni.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $alumniCount; ?></small> Alumni
            </a>
          </li>
          <li>
            <a href="deleted_news.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $newsCount; ?></small> News
            </a>
          </li>
          <li>
            <a href="deleted_event.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $eventCount; ?></small> Event
            </a>
          </li>
          <li>
            <a href="deleted_gallery.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $galleryCount; ?></small> Gallery
            </a>
          </li>
          <li>
            <a href="deleted_job.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $jobCount; ?></small> Job
            </a>
          </li>
          <li>
            <a href="deleted_survey.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
              <i class="fa fa-angle-right"></i> <small class="label bg-red"><?php echo $surveyCount; ?></small> Survey
            </a>
          </li>
        </ul>
      </li>
      <li>
       <a href="apk_version.php?token_url=<?php echo urlencode($adminData['token_url']); ?>">
            <i class="fa fa-th"></i> 
            <span>Apk Version</span>
          </a>
        </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Create the tabs -->
  <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
   <!-- <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-gear"></i></a></li> -->
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <!-- Home tab content -->
    <div class="tab-pane" id="control-sidebar-home-tab">
      <div class="tab-pane" id="control-sidebar-settings-tab"></div>
    </div>
  </div>
</aside>