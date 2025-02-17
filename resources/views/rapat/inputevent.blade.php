@extends('layouts.universal')

@section('container2') 
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('events.all') }}">Jadwal</a>
                        </li>
                        <li class="breadcrumb-item active text-bold">Buat Jadwal</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('container3')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Buat Jadwal Baru (Link Eksternal sudah ada)</h3>
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
        </div>
    </>

@endsection

@push('css')
    <style>
        .scrollable-checkboxes .form-check {
            margin-bottom: 10px; /* Adjust spacing between checkboxes */
        }


    </style>
@endpush
@push('scripts')
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
@endpush

@push('scripts')
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
@endpush
