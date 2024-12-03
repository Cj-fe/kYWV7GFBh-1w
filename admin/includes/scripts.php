<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Moment JS -->
<script src="../bower_components/moment/moment.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/filterTable.js"></script>
<!-- ChartJS -->
<script src="../bower_components/chart.js/Chart.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="../plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- SlimScroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="../bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- iCheck -->
<script src="../plugins/iCheck/icheck.min.js"></script>
<!-- iCheck -->
<script src="../../plugins/iCheck/icheck.min.js"></script>

<!-- Additional Chart Libraries -->
<script src="../plugins/chart/Chart.min.js"></script>
<script src="../plugins/chart/chart.js"></script>
<script src="../plugins/chart/apexcharts.min.js"></script>
<script src="../plugins/chart/chart-apex.js"></script>

<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Page Script -->

<!-- Active Script -->
<script>
  $(function () {
    var url = window.location.href;

    // Close all menu items initially
    $('.treeview-menu').hide();

    // For sidebar menu entirely but not cover treeview
    $('ul.sidebar-menu a').filter(function () {
      return this.href == url;
    }).parent().addClass('active');

    // For treeview
    $('ul.treeview-menu a').filter(function () {
      return this.href == url;
    }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

    // Expand only the active treeview menu
    $('.sidebar-menu .active').parents('.treeview').addClass('menu-open');
    $('.sidebar-menu .active').parents('.treeview-menu').show();

    // Toggle submenu on click
    $('.treeview > a').on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $parent = $this.parent();
      var $submenu = $this.next('.treeview-menu');

      if ($parent.hasClass('active')) {
        $submenu.slideUp('fast', function () {
          $parent.removeClass('active menu-open');
        });
      } else {
        // Close other open menus
        $('.treeview.active').not($parent).removeClass('active menu-open').find('.treeview-menu').slideUp('fast');

        $submenu.slideDown('fast', function () {
          $parent.addClass('active menu-open');
        });
      }
    });
  });
</script>

<!-- Data Table Initialize -->
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true
    })
    $('#example2').DataTable({
      'paging': true,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': true,
      'autoWidth': false
    })
  })
</script>

<!-- Date and Timepicker -->
<script>
  //Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })
  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  }) 
</script>


<script>
  // Embed PHP session data into JavaScript
  var userData = <?php echo json_encode($user); ?>;
</script>