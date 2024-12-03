

<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

// Fetch contact query data from Firebase
$contact_query = json_decode($firebase->retrieve("contact_query"), true);

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/flat/blue.css" />



  <style>
    .box-header {
      width: 100%;
      height: 35px;
      background: white !important;
      border-top-left-radius: 0 !important;
      border-top-right-radius: 0 !important;
    }
  </style>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header box-header-background">
        <h1>
          Mailbox <i class="fa fa-angle-right"></i> Inbox
        </h1>
        <div class="box-inline">

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
          <li>Mailbox</li>
          <li class="active">Inbox</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-md-3">
            <a href="compose.html" class="btn btn-primary btn-block margin-bottom">Compose</a>

            <div class="box box-solid" style="border-radius: none !important;">
              <div class="box-header with-border">
                <h3 class="box-title">Folders</h3>

                <div class="box-tools">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                  <li class="active"><a href="#"><i class="fa fa-inbox"></i> Inbox
                      <span class="label label-primary pull-right">12</span></a></li>
                  <li><a href="#"><i class="fa fa-envelope-o"></i> Sent</a></li>
                  <li><a href="#"><i class="fa fa-file-text-o"></i> Drafts</a></li>
                  <li><a href="#"><i class="fa fa-filter"></i> Junk <span
                        class="label label-warning pull-right">65</span></a>
                  </li>
                  <li><a href="#"><i class="fa fa-trash-o"></i> Trash</a></li>
                </ul>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /. box -->
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Labels</h3>

                <div class="box-tools">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                  <li><a href="#"><i class="fa fa-circle-o text-red"></i> Important</a></li>
                  <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> Promotions</a></li>
                  <li><a href="#"><i class="fa fa-circle-o text-light-blue"></i> Social</a></li>
                </ul>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Inbox</h3>


                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body no-padding">
                <div class="mailbox-controls">
                  <!-- Check all button -->
                  <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                  </button>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                  </div>
                  <!-- /.btn-group -->
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                  <div class="pull-right">
                    1-50/200
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                      <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <!-- /.btn-group -->
                  </div>
                  <!-- /.pull-right -->
                </div>
                <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <tbody>
                      <?php if (!empty($contact_query)): ?>
                        <?php foreach ($contact_query as $key => $contact): ?>
                          <tr>
                            <td><input type="checkbox"></td>
                            <td class="mailbox-name"><a
                                href="read-mail.php?id=<?php echo $key; ?>"><?php echo $contact['name']; ?></a></td>
                            <td class="mailbox-subject"><b><?php echo $contact['subject']; ?></b> -
                              <?php echo $contact['message']; ?></td>
                            <td class="mailbox-email"><?php echo $contact['email']; ?></td>
                            <td class="mailbox-date"><?php echo $contact['date']; ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="5">No messages found.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                  <!-- /.table -->
                </div>
                <!-- /.mail-box-messages -->
              </div>
              <!-- /.box-body -->
              <div class="box-footer no-padding">
                <div class="mailbox-controls">
                  <!-- Check all button -->
                  <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                  </button>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                  </div>
                  <!-- /.btn-group -->
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                  <div class="pull-right">
                    1-50/200
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                      <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <!-- /.btn-group -->
                  </div>
                  <!-- /.pull-right -->
                </div>
              </div>
            </div>
            <!-- /. box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/news_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>

 
<script>
  $(function () {
    // Initialize iCheck for all checkboxes
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });

    // Check/uncheck all checkboxes functionality
    $('.checkbox-toggle').click(function () {
      var $this = $(this);
      var isChecked = $this.data('clicks');
      if (isChecked) {
        // Uncheck all checkboxes
        $('.mailbox-messages input[type="checkbox"]').iCheck('uncheck');
        $this.find('i').removeClass('fa-check-square-o').addClass('fa-square-o');
      } else {
        // Check all checkboxes
        $('.mailbox-messages input[type="checkbox"]').iCheck('check');
        $this.find('i').removeClass('fa-square-o').addClass('fa-check-square-o');
      }
      $this.data('clicks', !isChecked);
    });

    // Handle starring for glyphicon and font awesome
    $(".mailbox-star").click(function (e) {
      e.preventDefault();
      var $this = $(this).find("a > i");
      $this.toggleClass("glyphicon-star glyphicon-star-empty fa-star fa-star-o");
    });
  });
</script>

</body>

</html>