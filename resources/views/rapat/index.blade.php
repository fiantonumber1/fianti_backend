<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Technology Office</title>
  <!-- Styles and scripts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fullcalendar/main.css') }}">

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
    @php
        use Carbon\Carbon;
    @endphp


    <!-- Modal -->
    <div class="modal fade" id="updateInfoModal" tabindex="-1" aria-labelledby="updateInfoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateInfoModalLabel">Update Informasi</h5>
                    <!-- Hapus tombol close -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Nomor WhatsApp Anda belum terdaftar. Silakan perbarui informasi Anda dengan mengklik tombol di bawah ini.
                </div>
                <div class="modal-footer">
                    <a href="{{ route('updateInformasiForm') }}" class="btn btn-primary">Perbarui Informasi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <!-- Navbar -->
        @include('partials.navbaradminlte3')
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">Meeting</a></li>
                        <li class="breadcrumb-item active text-bold">Mapping</li>
                    </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>


            <!-- Main content -->
            <section class="content">


                <div class="container-fluid">
                    <div class="row">
                    <div class="col-12">
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <h3 class="card-title text-bold">Mapping Meeting <span class="badge badge-info ml-1"></span></h3>
                            </div>  
                            <div class="card-header">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    @foreach ($revisiall as $key => $revisi)
                                        <li class="nav-item">
                                            <a class="nav-link @if($loop->first) active @endif" id="custom-tabs-one-{{ $key }}-tab" data-toggle="pill" href="#custom-tabs-one-{{ $key }}" role="tab" aria-controls="custom-tabs-one-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $key }}</a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    @foreach ($revisiall as $key => $revisi)
                                        <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $key }}" role="tabpanel" aria-labelledby="custom-tabs-one-{{ $key }}-tab">
                                            
                                        
                                            <ul class="nav nav-tabs" id="nested-tabs-one-tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="nested-tabs-one-schedule-tab" data-toggle="pill" href="#nested-tabs-one-schedule-{{ $key }}" role="tab" aria-controls="nested-tabs-one-schedule-{{ $key }}" aria-selected="true">Jadwal</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="nested-tabs-one-display-tab" data-toggle="pill" href="#nested-tabs-one-display-{{ $key }}" role="tab" aria-controls="nested-tabs-one-display-{{ $key }}" aria-selected="false">Tampilan</a>
                                                </li>
                                            </ul>


                                            <div class="tab-content" id="nested-tabs-one-tabContent">
                                                <div class="tab-pane fade show active" id="nested-tabs-one-schedule-{{ $key }}" role="tabpanel" aria-labelledby="nested-tabs-one-schedule-tab">
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <!-- <div class="col-md-1">
                                                                <div class="sticky-top mb-3">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h4 class="card-title">{{ $key }}</h4>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div id="external-events-{{ $key }}">
                                                                                <!-- External events can be dynamically added here
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->

                                                            
                                                            
                                                                
                                                            <div class="col-md-3 col-sm-6 col-12">
                                                                <!-- Tambahkan tombol upload di sini -->
                                                                <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm btn-block mb-3">Buat Jadwal</a>
                                                            </div>
                                                            
                                                            
                                                            <div class="col-md-12">
                                                                <div class="card card-primary">
                                                                    <div class="card-body">
                                                                        @if($key != "All")
                                                                            <div id="calendar-{{ $key }}" data-calendar-key="{{ $key }}"></div>
                                                                        @else
                                                                        <div id="calendar-{{ $key }}" data-calendar-key="{{ $key }}" style="display: none;"></div>
                                                                        <div id="scheduler" style="height: 600px; width: 100%;"></div>

                                                                        <script>
                                                                            // Initialize DayPilot Scheduler
                                                                            var dp = new DayPilot.Scheduler("scheduler");

                                                                            // Set the start date to today
                                                                            dp.startDate = DayPilot.Date.today();

                                                                            // Define the number of days to display (1 day in this case)
                                                                            dp.days = 1;

                                                                            // Define business hours from 06:00 AM to 11:00 PM
                                                                            dp.businessBeginsHour = 6;
                                                                            dp.businessEndsHour = 23;
                                                                            dp.businessWeekends = true;
                                                                            dp.showNonBusiness = false;

                                                                            // Define time headers with custom date format
                                                                            dp.timeHeaders = [
                                                                                { groupBy: "Month", format: "dd/MM/yyyy", height: 40 },
                                                                                { groupBy: "Day", format: "dd/MM/yyyy", height: 40 },
                                                                                { groupBy: "Hour", format: "H:mm", height: 40 }
                                                                            ];

                                                                            // Set event height
                                                                            dp.eventHeight = 100;

                                                                            // Set cell dimensions
                                                                            dp.cellWidth = 120;
                                                                            dp.cellWidthMin = 120;
                                                                            dp.cellHeight = 100;

                                                                            // Load resources (e.g., rooms)
                                                                            dp.resources = [
                                                                                @foreach($ruangrapat as $room)
                                                                                    @if($room != "All")
                                                                                        { name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}" },
                                                                                    @endif
                                                                                @endforeach
                                                                            ];

                                                                            // Load events
                                                                            dp.events.list = @json($eventsdaypilot);

                                                                            // Event handler for creating new events
                                                                            dp.onTimeRangeSelected = function(args) {
                                                                                var name = prompt("New event name:", "Event");
                                                                                dp.clearSelection();
                                                                                if (!name) return;

                                                                                var e = {
                                                                                    start: args.start,
                                                                                    end: args.end,
                                                                                    id: DayPilot.guid(),
                                                                                    text: name,
                                                                                    resource: args.resource
                                                                                };

                                                                                dp.events.add(e);

                                                                                // Send data to the server to save the event
                                                                                DayPilot.Http.ajax({
                                                                                    url: "/events/create",
                                                                                    data: e,
                                                                                    success: function(ajax) {
                                                                                        var response = ajax.data;
                                                                                        if (response && response.result) {
                                                                                            e.id = response.id; // Update id with server response
                                                                                            dp.message("Created: " + response.message);
                                                                                        }
                                                                                    },
                                                                                    error: function(ajax) {
                                                                                        dp.message("Saving failed");
                                                                                    }
                                                                                });
                                                                            };

                                                                            // Event handler for clicking on an event
                                                                            dp.onEventClick = function(args) {
                                                                                var eventId = args.e.id;
                                                                                var url = "{{ route('events.show', ':id') }}".replace(':id', eventId);
                                                                                window.location.href = url;
                                                                            };

                                                                            // Configure the bubble to display event details on hover
                                                                            dp.bubble = new DayPilot.Bubble({
                                                                                onLoad: function(args) {
                                                                                    var ev = args.source;
                                                                                    args.async = true;

                                                                                    var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', ev.id());

                                                                                    setTimeout(function() {
                                                                                        args.html = `
                                                                                            <div style='font-weight:bold'>${ev.text()}</div>
                                                                                            <div>Start: ${ev.start().toString("MM/dd/yyyy HH:mm")}</div>
                                                                                            <div>End: ${ev.end().toString("MM/dd/yyyy HH:mm")}</div>
                                                                                            <div><a href='${eventUrl}' target='_blank'>View Event</a></div>`;
                                                                                        args.loaded();
                                                                                    }, 500);
                                                                                }
                                                                            });

                                                                            // Customize event rendering
                                                                            dp.onBeforeEventRender = function(args) {
                                                                                var start = new DayPilot.Date(args.e.start);
                                                                                var end = new DayPilot.Date(args.e.end);
                                                                                var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', args.e.id);

                                                                                // Define the HTML content for the event
                                                                                args.e.html = `
                                                                                    <div class='calendar_white_event_inner' style='background-color: #e1f5fe; padding: 5px; border-radius: 5px;'>
                                                                                        <div style='font-weight:bold; color: #333;'>${args.e.text}</div>
                                                                                        <div style='color: #777;'>${start.toString("HH:mm")} - ${end.toString("HH:mm")}</div>
                                                                                        <div style='color: #777;'>Pic: ${args.e.pic}</div>
                                                                                        <div><a href='${eventUrl}' target='_blank'>View Event</a></div>
                                                                                    </div>
                                                                                `;

                                                                                // Set the event bar color
                                                                                args.e.barColor = "#e1f5fe";

                                                                                // Set the tooltip text
                                                                                args.e.toolTip = "Event from " + start.toString("HH:mm") + " to " + end.toString("HH:mm");
                                                                            };

                                                                            // Initialize the scheduler
                                                                            dp.init();
                                                                        </script>


                                                                            

                                                                            

                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($key!="All")
                                                    <div class="tab-pane fade" id="nested-tabs-one-display-{{ $key }}" role="tabpanel" aria-labelledby="nested-tabs-one-display-tab">  
                                                    
                                                        <div class="container-fluid">

                                                            <div class="row">
                                                                <div class="col-12">

                                                                <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Page Monitoring Meeting</h3> 
                                                                        </div>
                                                                        <!-- /.card-header -->
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                @if(in_array(auth()->user()->rule, ["Product Engineering","superuser"]))
                                                                                    <div class="col-md-3 col-sm-6 col-12">
                                                                                        <!-- Tombol untuk menghapus yang dipilih -->
                                                                                        <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                                                                    </div>
                                                                                @endif
                                                                                <div class="col-md-3 col-sm-6 col-12">
                                                                                    <!-- Tambahkan tombol upload di sini -->
                                                                                    <a href="{{ url('calendar/events/create') }}" class="btn btn-primary btn-sm btn-block mb-3">Buat Jadwal</a>
                                                                                </div>
                                                                            </div>
                                                                            <table id="example2-{{ $key }}" class="table table-bordered table-hover">
                                                                                @php
                                                                                    $keyanku1 = str_replace('.', '-', $key);
                                                                                    $keyanku = str_replace(' ', '_', $keyanku1);
                                                                                    $events = $revisiall[$keyanku]['events'];
                                                                                @endphp
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>
                                                                                            <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                                                                        </th>
                                                                                        <th scope="col">No</th>
                                                                                        <th scope="col">Nama Rapat</th>
                                                                                        <th scope="col">Waktu Awal</th>
                                                                                        <th scope="col">Waktu Akhir</th>
                                                                                        <th scope="col">Ruang Rapat</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php
                                                                                        $counter = 1;
                                                                                    @endphp
                                                                                    @foreach ($events as $event)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="icheck-primary">
                                                                                                    <input type="checkbox" value="{{ $event->id }}" name="document_ids[]" id="checkbox{{ $event->id }}">
                                                                                                    <label for="checkbox{{ $event->id }}"></label>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>{{ $counter++ }}</td>
                                                                                            <td>{{ $event->title }}</td>
                                                                                            <td>{{ Carbon::parse($event->start)->format('d-m-Y H:i') }}</td>
                                                                                            <td>{{ Carbon::parse($event->end)->format('d-m-Y H:i') }}</td>
                                                                                            <td>
                                                                                                <a href='{{ route('events.show', $event->id) }}' class="btn btn-primary">Detail</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <!-- /.card-body -->
                                                                    </div>


                                                                </div>
                                                            </div>
                                                        
                                                        </div>
                                        
                                                    </div>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    @endforeach
                                </div> 
                            </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                    
            </section>
            

        
        </div>


        <!-- Include your footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.2.0
            </div>
            <strong>Copyright &copy; 2024 <a href="https://adminlte.io">Technology Office</a>.</strong>
            All rights reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
    </div>

    

    <!-- Add your JS scripts here -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    <!-- jQuery UI -->
    <script src="{{ asset('adminlte3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('adminlte3/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/fullcalendar/main.js') }}"></script>
    <!-- Page specific script -->

    <script>
        $(document).ready(function() {
            // Cek apakah alert bernilai "yes"
            @if($alert === "yes")
                // Tampilkan modal
                $('#updateInfoModal').modal('show');
            @endif
        });
    </script>

    <script>
        $(function () {
            function ini_events(ele) {
                ele.each(function () {
                    var eventObject = {
                        title: $.trim($(this).text())
                    };
                    $(this).data('eventObject', eventObject);
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true,
                        revertDuration: 0
                    });
                });
            }

            function initializeCalendar(key, events) {
                var calendarEl = document.querySelector(`#calendar-${key}`);
                var calendarKey = calendarEl.getAttribute('data-calendar-key');

                if (!calendarEl) {
                    console.error('Calendar element not found for key:', key);
                    return;
                }

                // Destroy any existing calendar instance to prevent duplication
                if (calendarEl._fullCalendar) {
                    calendarEl._fullCalendar.destroy();
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    themeSystem: 'bootstrap',
                    events: events,
                    editable: true,
                    droppable: true,
                    drop: function(info) {
                        var checkbox = document.getElementById('drop-remove');
                        if (checkbox && checkbox.checked) {
                            info.draggedEl.parentNode.removeChild(info.draggedEl);
                        }
                    },
                    slotLabelFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false // Use 24-hour format
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false // Use 24-hour format
                    },
                    eventContent: function(arg) {
                        return {
                            html: `<button class="btn btn-primary btn-block text-left">
                                    <div>${arg.event.extendedProps.starttime} - ${arg.event.extendedProps.endtime ? arg.event.extendedProps.endtime : ''}</div>
                                    <div>${arg.event.extendedProps.unit}</div>
                                    <div>${arg.event.extendedProps.pic}</div>
                                    <div>${arg.event.title}</div>
                                    <div>${arg.event.extendedProps.room}</div>
                                </button>`
                        }
                    }
                });

                // Store the calendar instance on the DOM element for later access
                calendarEl._fullCalendar = calendar;

                calendar.render();
            }
            // Function to format event time to HH:mm format
            function formatEventTime(dateTimeStr) {
                var date = new Date(dateTimeStr);
                var hours = date.getHours().toString().padStart(2, '0');
                var minutes = date.getMinutes().toString().padStart(2, '0');
                return hours + ':' + minutes;
            }
            
            function convertListToString(agenda_unit) {
                try {
                    // Parse the agenda_unit JSON string
                    var list = JSON.parse(agenda_unit);

                    // Function to abbreviate a phrase
                    function abbreviate(phrase) {
                        return phrase.split(' ').map(word => word.charAt(0)).join('').toUpperCase();
                    }

                    // Convert each item in the list to its abbreviation if necessary
                    var abbreviatedList = list.map(function(item) {
                        return item.length > 10 ? abbreviate(item) : item;
                    });

                    // Join the abbreviated list into a string
                    var resultString = abbreviatedList.join(', ');

                    // Check if the resulting string exceeds 10 characters
                    if (resultString.length > 10) {
                        resultString = resultString.substring(0, 8) + '....';
                    }

                    return resultString;
                } catch (error) {
                    // If any error occurs, return "...."
                    return "...";
                }
            }

            // Function to format event time to HH:mm format
            function formatEventTime(dateTimeStr) {
                // Split the dateTimeStr into date and time parts
                var parts = dateTimeStr.split(' ');
                var timePart = parts[1]; // Time part is at index 1 after splitting by space

                // Split the time part into hours, minutes, and seconds
                var timeParts = timePart.split(':');
                var hours = timeParts[0];
                var minutes = timeParts[1];

                // Return formatted time as HH:mm
                return hours + ':' + minutes;
            }

            function renderCalendarAndTable(key) {
                var events = @json($revisiall)[key].events.map(function(event) {
                    return {
                        title: event.title,
                        start: event.start,
                        starttime: formatEventTime(event.start),
                        endtime: formatEventTime(event.end),
                        end: event.end ? event.end : null,
                        color: event.color,
                        unit: convertListToString(event.agenda_unit),
                        pic: event.pic,
                        room: event.room,
                        url: '{{ route('events.show', ':id') }}'.replace(':id', event.id)  // Dynamically generate URL
                    };
                });

                initializeCalendar(key, events);
                ini_events($(`#external-events-${key} div.external-event`));

                // Initialize DataTable
                if ($.fn.DataTable.isDataTable(`#example2-${key}`)) {
                    $(`#example2-${key}`).DataTable().destroy();
                }

                $(`#example2-${key}`).DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });
            }

            // Initial rendering for the first tab
            renderCalendarAndTable("{{ array_key_first($revisiall) }}");

            // Re-render calendar and table when tab is shown
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                var key = $(e.target).attr('aria-controls').replace('custom-tabs-one-', '');
                // Use setTimeout to ensure the tab content is fully visible
                setTimeout(function() {
                    renderCalendarAndTable(key);
                }, 50);
            });
        });

    </script>




</body>
</html>