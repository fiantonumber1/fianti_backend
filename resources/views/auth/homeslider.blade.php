@php
    use Illuminate\Support\Collection;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Technology Office</title>
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">




        <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
        <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
        <script src="{{ asset('adminlte3/plugins/chart.js/Chart.min.js') }}"></script>
        <script src="{{ asset('adminlte3/plugins/chartjs-plugin-datalabels/chartjs-plugin-datalabels.min.js') }}"></script>
        <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.css') }}" type="text/javascript"></script>

        <!-- helper libraries -->
        <script src="{{ asset('schedulerdaypilot/js/jquery/jquery-1.9.1.min.js') }}" type="text/javascript"></script>

        <!-- daypilot libraries -->
        <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.js') }}" type="text/javascript"></script>



        <style type="text/css">
            .scheduler_default_rowheader 
            {
                background: -webkit-gradient(linear, left top, left bottom, from(#eeeeee), to(#dddddd));
                background: -moz-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: -ms-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: -webkit-linear-gradient(top, #eeeeee 0%, #dddddd);
                background: linear-gradient(top, #eeeeee 0%, #dddddd);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorStr="#eeeeee", endColorStr="#dddddd");
            }
            .scheduler_default_rowheader_inner 
            {
                border-right: 1px solid #ccc;
            }
            .scheduler_default_rowheadercol2
            {
                background: White;
            }
            .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner 
            {
                top: 2px;
                bottom: 2px;
                left: 2px;
                background-color: transparent;
                border-left: 5px solid #1a9d13; /* green */
                border-right: 0px none;
            }
            .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                border-left: 5px solid #ea3624; /* red */
            }
            .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                border-left: 5px solid #f9ba25; /* orange */
            }
        </style>
    </head>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            @include('partials.navbaradminlte3')
            <div class="content-wrapper">  
                <div class="content-header">
                    
                </div>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-danger card-outline"> 
                                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                        <ol class="carousel-indicators">
                                            @foreach ($revisiall as $keyan => $revisi)
                                                <li data-target="#carouselExampleIndicators" data-slide-to="{{ $keyan }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                                            @endforeach
                                        </ol>
                                        <div class="carousel-inner">
                                            @foreach ($revisiall as $keyan => $revisi)
                                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                    @include('auth.homeinduk')
                                                </div>
                                            @endforeach
                                        </div>
                                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    </div>         
                                </div>
                            </div>
                        <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                <!-- /.container-fluid -->
                </section>

                
            </div>
            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.2.0
                </div>
                <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
            </footer>
            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
        <script>
            setInterval(function() {
                location.reload();
            }, 3600000); // 60000 milidetik = 3600 detik
        </script>

    </body>
    
   
    @include('auth.homeindukscript')
</html>