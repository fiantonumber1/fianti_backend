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
  <!-- fullCalendar -->
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fullcalendar/main.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">

</head>
<body class="hold-transition sidebar-mini">
@php
  $key="keyberagam";
  $revisiall=[];
  $revisiall['keyberagam']="";
@endphp
<div class="wrapper">
  <!-- Navbar -->
  @include('partials.navbaradminlte3')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Jadwal Rapat</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Jadwal Rapat</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <div class="sticky-top mb-3">


              <div class="card">
                <div class="card-header">
                  <h4 class="card-title"> {{$room}}</h4>
                </div>
                <div class="card-body">
                  <div id="external-events-{{$key}}">
                    <!-- <div class="external-event bg-success">Lunch</div> -->
                    <!-- <div class="external-event bg-warning">Go home</div>
                    <div class="external-event bg-info">Do homework</div>
                    <div class="external-event bg-primary">Work on UI design</div>
                    <div class="external-event bg-danger">Sleep tight</div> -->
                    <!-- <div class="checkbox">
                      <label for="drop-remove">
                        <input type="checkbox" id="drop-remove">
                        remove after drop
                      </label>
                    </div> -->
                  </div>
                </div>
              </div>


              <!-- /.card -->
              <!-- <div class="card"> -->
                <!-- <div class="card-header">
                  <h3 class="card-title">Create Event</h3>
                </div>
                <div class="card-body">
                  <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                    <ul class="fc-color-picker" id="color-chooser">
                      <li><a class="text-primary" href="#"><i class="fas fa-square"></i></a></li>
                      <li><a class="text-warning" href="#"><i class="fas fa-square"></i></a></li>
                      <li><a class="text-success" href="#"><i class="fas fa-square"></i></a></li>
                      <li><a class="text-danger" href="#"><i class="fas fa-square"></i></a></li>
                      <li><a class="text-muted" href="#"><i class="fas fa-square"></i></a></li>
                    </ul>
                  </div>
                  <div class="input-group">
                    <input id="new-event" type="text" class="form-control" placeholder="Event Title">

                    <div class="input-group-append">
                      <button id="add-new-event" type="button" class="btn btn-primary">Add</button>
                    </div>
                  </div>
                </div> -->
              <!-- </div> -->


              
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary">
              <div class="card-body p-0">
                <!-- THE CALENDAR -->
                
                <div id="calendar-{{$key}}"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2024 <a href="https://adminlte.io">Technology Office</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- jQuery UI -->
<script src="{{ asset('adminlte3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
<!-- fullCalendar 2.2.5 -->
<script src="{{ asset('adminlte3/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/fullcalendar/main.js') }}"></script>
<!-- Page specific script -->
<script>
    $(function () {
        function ini_events(ele) {
            ele.each(function () {
                var eventObject = {
                    title: $.trim($(this).text())
                }
                $(this).data('eventObject', eventObject)
                $(this).draggable({
                    zIndex: 1070,
                    revert: true,
                    revertDuration: 0
                })
            })
        }

        


        
        @foreach ($revisiall as $key => $revisi)
          ini_events($('#external-events-{{$key}} div.external-event'))

          var date = new Date()
          var d = date.getDate(),
              m = date.getMonth(),
              y = date.getFullYear()

          var Calendar = FullCalendar.Calendar;
          var Draggable = FullCalendar.Draggable;

          var containerEl = document.getElementById('external-events-{{$key}}');
          var checkbox = document.getElementById('drop-remove');
          var calendarEl = document.getElementById('calendar-{{$key}}');

          new Draggable(containerEl, {
              itemSelector: '.external-event',
              eventData: function(eventEl) {
                  return {
                      title: eventEl.innerText,
                      backgroundColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
                      borderColor: window.getComputedStyle(eventEl, null).getPropertyValue('background-color'),
                      textColor: window.getComputedStyle(eventEl, null).getPropertyValue('color'),
                  };
              }
          });

          var calendar = new Calendar(calendarEl, {
              headerToolbar: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'dayGridMonth,timeGridWeek,timeGridDay'
              },
              themeSystem: 'bootstrap',
              events: @json($events),
              editable: true,
              droppable: true,
              drop: function(info) {
                  if (checkbox.checked) {
                      info.draggedEl.parentNode.removeChild(info.draggedEl);
                  }
              }
          });

          calendar.render();

          var currColor = '#3c8dbc'
          $('#color-chooser > li > a').click(function (e) {
              e.preventDefault()
              currColor = $(this).css('color')
              $('#add-new-event').css({
                  'background-color': currColor,
                  'border-color': currColor
              })
          })

          $('#add-new-event').click(function (e) {
              e.preventDefault()
              var val = $('#new-event').val()
              if (val.length == 0) {
                  return
              }

              var event = $('<div />')
              event.css({
                  'background-color': currColor,
                  'border-color': currColor,
                  'color': '#fff'
              }).addClass('external-event')
              event.text(val)
              $('#external-events-{{$key}}').prepend(event)

              ini_events(event)

              $('#new-event').val('')

              $.ajax({
                  url: '{{ url('/events') }}',
                  type: 'POST',
                  data: {
                      title: val,
                      start: new Date().toISOString(),
                      backgroundColor: currColor,
                      borderColor: currColor,
                      _token: '{{ csrf_token() }}'
                  },
                  success: function(response) {
                      console.log('Event created:', response);
                  }
              });
          })
      
        @endforeach


        
    })
</script>
</body>
</html>
