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
    <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.js') }}" type="text/javascript"></script>

    <style>
        .scrollable-checkboxes .form-check {
            margin-bottom: 10px; /* Adjust spacing between checkboxes */
        }
    </style>
    <style type="text/css">
        .scheduler_default_rowheader {
            background: -webkit-gradient(linear, left top, left bottom, from(#eeeeee), to(#dddddd));
            background: -moz-linear-gradient(top, #eeeeee 0%, #dddddd);
            background: -ms-linear-gradient(top, #eeeeee 0%, #dddddd);
            background: -webkit-linear-gradient(top, #eeeeee 0%, #dddddd);
            background: linear-gradient(top, #eeeeee 0%, #dddddd);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorStr="#eeeeee", endColorStr="#dddddd");
        }

        .scheduler_default_rowheader_inner {
            border-right: 1px solid #ccc;
        }

        .scheduler_default_rowheadercol2 {
            background: White;
        }

        .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            top: 2px;
            bottom: 2px;
            left: 2px;
            background-color: transparent;
            border-left: 5px solid #1a9d13;
            border-right: 0px none;
        }

        .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #ea3624;
        }

        .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #f9ba25;
        }

        .full-loaded {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #4caf50;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            z-index: 9999;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
    </style>
</head>

<body class="hold-transition">
    <div class="wrapper">
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                
                    <div class="col-lg-7">
                        <h3>Jadwal pada: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $thisday)->format('d-m-Y') }}</h3>
                            
                            
                    </div>
                    <div class="col-lg-7">
                        <!-- Menampilkan tanggal yang dipilih atau defaultnya hari ini -->
                        <div id="scheduler" style="height: 600px; width: 100%;"></div>
                        
                        <script>
                            var dp = new DayPilot.Scheduler("scheduler");

                            // Set start date sesuai dengan tanggal yang dipilih dari controller
                            dp.startDate = "{{ \Carbon\Carbon::createFromFormat('Y-m-d', $thisday)->format('Y-m-d') }}";

                            dp.days = 1;
                            dp.businessBeginsHour = 6;
                            dp.businessEndsHour = 23;
                            dp.businessWeekends = true;
                            dp.showNonBusiness = false;

                            dp.timeHeaders = [
                                { groupBy: "Month", format: "dd/MM/yyyy", height: 40 },
                                { groupBy: "Day", format: "dd/MM/yyyy", height: 40 },
                                { groupBy: "Hour", format: "H:mm", height: 40 }
                            ];

                            dp.eventHeight = 100;
                            dp.cellWidth = 120;
                            dp.cellWidthMin = 120;
                            dp.cellHeight = 100;

                            dp.resources = [
                                @foreach($ruangrapat as $room)
                                    @if($room != "All")
                                        { name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}" },
                                    @endif
                                @endforeach
                            ];

                            dp.events.list = @json($eventsdaypilot);

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

                            dp.onBeforeEventRender = function(args) {
                                var start = new DayPilot.Date(args.e.start);
                                var end = new DayPilot.Date(args.e.end);
                                var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', args.e.id);

                                args.e.html = `
                                    <div class='calendar_white_event_inner' style='background-color: #e1f5fe; padding: 5px; border-radius: 5px;'>
                                        <div style='font-weight:bold; color: #333;'>${args.e.text}</div>
                                        <div style='color: #777;'>${start.toString("HH:mm")} - ${end.toString("HH:mm")}</div>
                                        <div style='color: #777;'>Pic: ${args.e.pic}</div>
                                        <div><a href='${eventUrl}' target='_blank'>View Event</a></div>
                                    </div>
                                `;

                                args.e.barColor = "#e1f5fe";
                                args.e.toolTip = "Event from " + start.toString("HH:mm") + " to " + end.toString("HH:mm");
                            };

                            dp.init();
                        </script>
                        
                    </div>
                    <div class="col-lg-7">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Buat Jadwal (Pastikan Tidak boleh ada kesalahan)</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('events.store') }}" method="POST" id="create-event-form">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="title">Judul Rapat</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="pic">PIC</label>
                                            <input type="text" class="form-control" id="pic" name="pic" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="agenda_desc">Agenda Rapat</label>
                                        <input type="text" class="form-control" id="agenda_desc" name="agenda_desc" required>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="start">Tanggal dan Waktu Mulai</label>
                                            <input type="datetime-local" class="form-control" id="start" name="start" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="end">Tanggal dan Waktu Selesai</label>
                                            <input type="datetime-local" class="form-control" id="end" name="end" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="room">Ruangan</label>
                                        <select class="form-control" id="room" name="room" required>
                                            <option value="Tidak Menggunakan Ruangan">Tidak Menggunakan Ruangan</option>
                                            @foreach($ruangrapat as $room)
                                                <option value="{{ $room }}">{{ $room }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="use_zoom">Gunakan Zoom</label>
                                        <select class="form-control" id="use_zoom" name="use_zoom" required>
                                            <option value="yes">Ya</option>
                                            <option value="no">Tidak</option>
                                            <option value="linkeksternal">Link Eksternal</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="zoom_link_container" style="display: none;">
                                        <label for="zoom_link">Link Zoom Eksternal</label>
                                        <input type="text" class="form-control" id="zoom_link" name="zoom_link">
                                    </div>

                                    <button type="button" class="btn btn-primary" id="check-room-availability">Cek Ketersediaan</button>
                                    <input type="hidden" class="form-control" id="backgroundColor" name="backgroundColor" value="#0073b7">

                                    <div class="form-group">
                                        <label>Pilih Tipe Peserta:</label>
                                        <select class="form-control" id="participant-type" required>
                                            <option value="">Pilih...</option>
                                            <option value="user">Kirim Pengguna</option>
                                            <option value="unit">Kirim Unit</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="participants-container" style="display: none;">
                                        <div id="unit-participants" style="display: none;">
                                            <label>Unit yang terlibat:</label><br>
                                            <input type="text" id="unit-search" class="form-control" placeholder="Cari Unit...">
                                            <div class="scrollable-checkboxes" style="max-height: 150px; overflow-y: auto;">
                                                @foreach($telegramaccounts as $pic)
                                                    <div class="form-check unit-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="agenda_unit[]" value="{{ $pic->account }}">
                                                        <label class="form-check-label">{{ $pic->account }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div id="custom-units-container" class="mt-2"></div>
                                            <button type="button" class="btn btn-primary" id="add-custom-unit">Tambahkan Unit Baru</button>
                                        </div>

                                        <div id="user-participants" style="display: none;">
                                            <label>Pengguna yang terlibat:</label><br>
                                            <input type="text" id="user-search" class="form-control" placeholder="Cari Pengguna...">
                                            <div class="scrollable-checkboxes" style="max-height: 150px; overflow-y: auto;">
                                                @foreach($availableUsers as $user)
                                                    <div class="form-check user-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="personal_users_id[]" value="{{ $user->id }}">
                                                        <label class="form-check-label">{{ $user->name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>



                                    </div>

                                    <button type="submit" class="btn btn-success" id="submit-event">Buat Jadwal</button>
                                </form>
                            </div>
                        </div>
                    </div>
                
            </section>
        </div>

        <!-- Footer -->
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
    <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>

    <script>
        // Display "full-loaded" message once the page has fully loaded
        window.addEventListener('load', function() {
            var fullLoadedElement = document.createElement('div');
            fullLoadedElement.className = 'full-loaded';
            fullLoadedElement.innerText = 'Full Loaded';
            document.body.appendChild(fullLoadedElement);

            // Fade in the message
            setTimeout(function() {
                fullLoadedElement.style.visibility = 'visible';
                fullLoadedElement.style.opacity = 1;
            }, 100);

            // Fade out the message after 3 seconds
            setTimeout(function() {
                fullLoadedElement.style.opacity = 0;
                setTimeout(function() {
                    fullLoadedElement.style.visibility = 'hidden';
                }, 300);
            }, 3000);
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('create-event-form');
            const participantTypeSelect = document.getElementById('participant-type');
            const participantsContainer = document.getElementById('participants-container');
            const unitParticipants = document.getElementById('unit-participants');
            const userParticipants = document.getElementById('user-participants');
            const useZoomSelect = document.getElementById('use_zoom');
            const zoomLinkContainer = document.getElementById('zoom_link_container');

            participantTypeSelect.addEventListener('change', function() {
                participantsContainer.style.display = 'block';
                if (this.value === 'unit') {
                    unitParticipants.style.display = 'block';
                    userParticipants.style.display = 'none';
                } else if (this.value === 'user') {
                    unitParticipants.style.display = 'none';
                    userParticipants.style.display = 'block';
                } else {
                    participantsContainer.style.display = 'none';
                }
            });

            useZoomSelect.addEventListener('change', function() {
                zoomLinkContainer.style.display = this.value === 'linkeksternal' ? 'block' : 'none';
            });

            document.getElementById('add-custom-unit').addEventListener('click', function() {
                const container = document.getElementById('custom-units-container');
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'custom_units[]';
                input.classList.add('form-control', 'mt-2');
                input.placeholder = 'Nama Unit Baru';
                container.appendChild(input);
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while your event is being created.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(this.action, {
                    method: this.method,
                    body: new FormData(this)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Room sudah dipesan atau mungkin anda tidak memilih opsi apapun');
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Event created successfully!',
                    }).then(() => {
                        window.location.href = `/events/show/${data.message}`;
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message,
                    });
                });
            });

            const checkRoomAvailabilityBtn = document.getElementById('check-room-availability');
            checkRoomAvailabilityBtn.addEventListener('click', function () {
                const start = document.getElementById('start').value;
                const end = document.getElementById('end').value;
                const room = document.getElementById('room').value;
                const useZoom = document.getElementById('use_zoom').value;

                if (start && end) {
                    Swal.fire({
                        title: 'Checking availability...',
                        text: 'Please wait while we check room and zoom availability.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const data = {
                        start,
                        end,
                        room,
                        use_zoom: useZoom
                    };

                    fetch("{{ route('checkRoomAvailability') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        handleAvailabilityResponse(data.roomavailable);
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Error:', error);
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Harap isi tanggal dan waktu sebelum memeriksa ketersediaan.',
                    });
                }
            });

        });
        
        
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitSearchInput = document.getElementById('unit-search');
            const userSearchInput = document.getElementById('user-search');

            // Filter function for units
            unitSearchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const checkboxes = document.querySelectorAll('#unit-participants .unit-checkbox');

                checkboxes.forEach(checkbox => {
                    const label = checkbox.querySelector('.form-check-label').textContent.toLowerCase();
                    checkbox.style.display = label.includes(filter) ? '' : 'none';
                });
            });

            // Filter function for users
            userSearchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const checkboxes = document.querySelectorAll('#user-participants .user-checkbox');

                checkboxes.forEach(checkbox => {
                    const label = checkbox.querySelector('.form-check-label').textContent.toLowerCase();
                    checkbox.style.display = label.includes(filter) ? '' : 'none';
                });
            });
        });
    </script>
    <script>
        function handleAvailabilityResponse(roomAvailable) {
            switch (roomAvailable) {
                case "truetrue":
                    Swal.fire('Success', 'Ruangan tersedia dan zoom tersedia!', 'success');
                    break;
                case "truefalse":
                    Swal.fire('Error', 'Ruangan tersedia tetapi zoom tidak tersedia.', 'warning');
                    break;
                case "falsetrue":
                    Swal.fire('Error', 'Ruangan tidak tersedia tetapi zoom tersedia.', 'warning');
                    break;
                case "falsefalse":
                    Swal.fire('Error', 'Ruangan dan zoom tidak tersedia.', 'error');
                    break;
                case "zerotrue":
                    Swal.fire('Success', 'Zoom tersedia.', 'success');
                    break;
                case "zerofalse":
                    Swal.fire('Error', 'Zoom tidak tersedia.', 'error');
                    break;
                case "truezero":
                    Swal.fire('Success', 'Ruangan tersedia.', 'success');
                    break;
                case "falsezero":
                    Swal.fire('Error', 'Ruangan tidak tersedia.', 'error');
                    break;
                case "endearlierstart":
                    Swal.fire('Error', 'Waktu mulai harus lebih awal dari waktu akhir', 'error');
                    break;
                default:
                    Swal.fire('Error', 'Data tidak valid.', 'error');
                    break;
            }
        }
    </script>
</body>

</html>
