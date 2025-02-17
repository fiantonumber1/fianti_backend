<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Technology Office</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">

  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
  <!-- Sweetalert2 (include theme bootstrap) -->
  <link rel="stylesheet" type="text/css" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') }}">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* CSS */
    .wrapper {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .content-wrapper {
      flex: 1;
    }

    .main-footer {
      margin-top: auto;
    }
  </style>
  @stack('css')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  @include('partials.navbaradminlte3')
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" id="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      @yield("container3")
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <!-- <h1 class="m-0">Starter Page</h1> -->
          </div><!-- /.col -->
          <div class="col-sm-6">
            
            <!-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol> -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    
    <div class="content">
    
      <div class="container-fluid">
        <!-- <h5 class="mb-2">Info Box</h5> -->
        <!-- Start Loop -->
        <div class="row">  
          @yield("container1")
          @yield("container2")
        </div>
        <!-- End Loop -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>

  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>&copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
<!-- Page specific script -->
<script>
    $(function () {
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
        });
    });
</script>

<script>
  // Fungsi untuk menyesuaikan tinggi konten dengan kontennya
  function adjustContentHeight() {
    var headerHeight = document.querySelector('.content-header').offsetHeight;
    var footerHeight = document.querySelector('.main-footer').offsetHeight;
    var windowHeight = window.innerHeight;

    var contentWrapper = document.getElementById('content-wrapper');
    var contentWrapperStyle = window.getComputedStyle(contentWrapper);
    var paddingTop = parseFloat(contentWrapperStyle.paddingTop);
    var paddingBottom = parseFloat(contentWrapperStyle.paddingBottom);
    
    var contentHeight = windowHeight - headerHeight - footerHeight - paddingTop - paddingBottom;
    contentWrapper.style.minHeight = contentHeight + 'px';
  }

  // Panggil fungsi setelah konten dimuat atau diubah
  document.addEventListener('DOMContentLoaded', adjustContentHeight);
  window.addEventListener('resize', adjustContentHeight);
</script>

@stack('scripts')
</body>
</html>
