<style>
  .scrollable-menu {
    max-height: 350px;
    /* Set maximum height */
    overflow-y: auto;
    /* Enable vertical scrolling */
  }
</style>

<header class="main-header">
  <!-- Logo -->
  <a href="index2.html" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>A</b>LT</span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>Admin</b>LTE</span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a class="sidebar-toggle" data-toggle="push-menu" role="button">
      <i class="fa fa-bars"></i>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">

    <!--    <li class="dropdown messages-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success"></span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 0 messages</li>
            <li>
              <ul class="menu">
              </ul>
            </li>
            <li class="footer"><a href="#">See All Messages</a></li>
          </ul>
        </li>

-->
    <!-- Notifications: style can be found in dropdown.less -->
    <li class="dropdown notifications-menu">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning"></span>
      </a>
      <ul class="dropdown-menu">
        <li class="header">Loading...</li>
        <li>
          <!-- inner menu: contains the actual data -->
          <div id="recent-comments-list" class="recent-comments-list scrollable-menu">
            <!-- Fetched notifications will be inserted here dynamically -->
          </div>
        </li>

    </li>
    </ul>
    </li>
    <!-- Tasks: style can be found in dropdown.less -->

    <!-- User Account: style can be found in dropdown.less -->
    <li class="dropdown user user-menu">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="<?php echo (!empty($user['image_url'])) ? $user['image_url'] : 'uploads/profile.png'; ?>"
          class="user-image" alt="User Image">
        <span class="hidden-xs"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></span>
      </a>
      <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
          <img src="<?php echo (!empty($user['image_url'])) ? $user['image_url'] : 'uploads/profile.png'; ?>"
            class="img-circle" alt="User Image">


          <p>
            <?php echo $user['firstname'] . ' ' . $user['lastname']; ?>
            <!-- Assuming 'created_on' is part of the admin data -->
            <small>Member since <?php echo date('M. Y', strtotime($user['created_on'])); ?></small>
          </p>
        </li>

        <!-- Menu Footer-->
        <li class="user-footer">
          <div class="pull-left">
            <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" id="admin_profile">Update</a>
          </div>
          <div class="pull-right">
            <a href="#" class="btn btn-default btn-flat" id="signout_button">Sign out</a>
          </div>
        </li>
      </ul>
    </li>
    <!-- Control Sidebar Toggle Button -->
    <li>
      <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
    </li>
    </ul>
    </div>
  </nav>
</header>

<?php include 'profile_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Your existing script for the signout button
  document.getElementById('signout_button').addEventListener('click', function (e) {
    e.preventDefault();
    Swal.fire({
      title: 'Are you sure you want to leave this page?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, sign out',
      cancelButtonText: 'No, stay here'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  });
  // Your existing script
  document.getElementById('signout_button').addEventListener('click', function (e) {
    e.preventDefault();
    Swal.fire({
      title: 'Are you sure you want to leave this page?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, sign out',
      cancelButtonText: 'No, stay here'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  });
</script>


<script>
  let lastReadTimestamp = parseInt(localStorage.getItem('lastReadTimestamp')) || 0;

  function updateActivities() {
    $.ajax({
      url: 'get_recent_notification.php',
      type: 'GET',
      data: { last_read_timestamp: lastReadTimestamp },
      dataType: 'json',
      success: function (data) {
        let activitiesHtml = '';

        // Display all recent activities
        data.activities.forEach(function (activity) {
          let newClass = activity.timestamp > lastReadTimestamp ? 'new-comment' : '';

          let activityContent = activity.action === 'commented on'
            ? `<div class="comment-text"><p><i class="fa fa-wechat"></i> ${activity.comment}</p></div>`
            : `<div class="comment-text"><p><i class="fa fa-thumbs-o-up"></i> Liked this ${activity.item_type}</p></div>`;

          activitiesHtml += `
        <div class="recent-comment-item ${newClass}">
          <a href="#">
            <div class="comment-flex">
              <div class="comment-img">
                <img src="../userpage/${activity.profile_url}" alt="${activity.alumni_name}" />
              </div>
              <div class="comment-content">
                <div class="comment-header">
                  <h3>${activity.alumni_name} <span class="comment-time">${activity.time_elapsed}</span></h3>
                  <span>on ${activity.item_title}</span>
                </div>
                ${activityContent}
              </div>
            </div>
          </a>
        </div>
      `;
        });

        $('#recent-comments-list').html(activitiesHtml);
        updateNotificationCount(data.new_activity_count);
        updateNotificationHeader(data.new_activity_count);
      },
      error: function (xhr, status, error) {
        console.error("Error fetching activities:", error);
      }
    });
  }

  function updateNotificationCount(count) {
    const notificationCount = $('.label.label-warning');
    if (count > 0) {
      notificationCount.text(count).show();
    } else {
      notificationCount.text(0).hide();
    }
  }

  function updateNotificationHeader(count) {
    const header = $('.dropdown.notifications-menu .header');
    header.html(`You have ${count} notifications`);
  }

  // When the bell icon is clicked, reset the notification count
  $('.dropdown-toggle').on('click', function (e) {
    lastReadTimestamp = Math.floor(Date.now() / 1000); // Current timestamp in seconds
    localStorage.setItem('lastReadTimestamp', lastReadTimestamp);

    // Reset the notification count to 0 in the UI
    updateNotificationCount(0);
    $('.new-comment').removeClass('new-comment');
  });

  // Update activities immediately and then every 5 seconds
  updateActivities();
  setInterval(updateActivities, 5000);

</script>
<script>
  $(document).ready(function () {
    function updateContactQueries() {
      $.ajax({
        url: 'get_contact_queries.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          let queriesHtml = '';
          data.queries.forEach(function (query) {
            queriesHtml += `
                        <li>
                            <a href="#" data-query-id="${query.id}">
                                <div class="pull-left">
                                    <img src="../images/profile.jpg" class="img-circle" alt="User Image">
                                </div>
                                <h4>
                                    ${query.name}
                                    <small><i class="fa fa-clock-o"></i> ${query.time_elapsed}</small>
                                </h4>
                                <p>${query.subject}</p>
                            </a>
                        </li>
                    `;
          });

          $('.dropdown-menu .menu').html(queriesHtml);
          $('.dropdown-menu .header').text(`You have ${data.total_count} messages`);
          updateNotificationCount(data.total_count);
        },
        error: function (xhr, status, error) {
          console.error("Error fetching contact queries:", error);
        }
      });
    }

    function updateNotificationCount(count) {
      const notificationCount = $('.messages-menu .label-success');
      if (count > 0) {
        notificationCount.text(count).show();
      } else {
        notificationCount.hide();
      }
    }

    // Update contact queries immediately and then every 30 seconds
    updateContactQueries();
    setInterval(updateContactQueries, 30000);

    // Handle click on a contact query
    $(document).on('click', '.dropdown-menu .menu a', function (e) {
      e.preventDefault();
      const queryId = $(this).data('query-id');
      // Here you can implement the logic to show the full message
      // For example, open a modal with the full message details
      console.log("Clicked query ID:", queryId);
    });
  });
</script>