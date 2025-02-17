@extends('layouts.main')

@section('container1')
<div class="container">
    <a href="{{ route('events.all') }}" class="btn btn-primary">Kembali ke jadwal</a>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Edit Jadwal Baru (Link Eksternal sudah ada)</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('events.update', $event->id) }}" method="POST" id="edit-event-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="title">Judul Rapat</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $event->title }}">
                </div>
                <div class="form-group">
                    <label for="pic">PIC</label>
                    <input type="text" class="form-control" id="pic" name="pic" value="{{ $event->pic }}" required>
                </div>
                <div class="form-group">
                    <label for="agenda_desc">Agenda Rapat</label>
                    <input type="text" class="form-control" id="agenda_desc" name="agenda_desc" value="{{ $event->agenda_desc }}" required>
                </div>
                <div class="form-group">
                    <label for="start">Tanggal dan Waktu Mulai</label>
                    <input type="datetime-local" class="form-control" id="start" name="start" value="{{ $event->start }}" required>
                </div>
                <div class="form-group">
                    <label for="end">Tanggal dan Waktu Selesai</label>
                    <input type="datetime-local" class="form-control" id="end" name="end" value="{{ $event->end }}">
                </div>
                <div class="form-group">
                    <label for="room">Ruangan</label>
                    <select class="form-control" id="room" name="room" required>
                        <option value="Tidak Menggunakan Ruangan" {{ $event->room == 'Tidak Menggunakan Ruangan' ? 'selected' : '' }}>Tidak Menggunakan Ruangan</option>
                        @foreach($ruangrapat as $room)
                            <option value="{{ $room }}" {{ $event->room == $room ? 'selected' : '' }}>{{ $room }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="use_zoom">Gunakan Zoom</label>
                    <select class="form-control" id="use_zoom" name="use_zoom" required>
                        <option value="yes" {{ $event->use_zoom == 'yes' ? 'selected' : '' }}>Ya</option>
                        <option value="no" {{ $event->use_zoom == 'no' ? 'selected' : '' }}>Tidak</option>
                        <option value="linkeksternal" {{ $event->use_zoom == 'linkeksternal' ? 'selected' : '' }}>Link Eksternal</option>
                    </select>
                </div>

                <div class="form-group" id="zoom_link_container" style="display: {{ $event->use_zoom == 'linkeksternal' ? 'block' : 'none' }};">
                    <label for="zoom_link">Link Zoom Eksternal</label>
                    <input type="text" class="form-control" id="zoom_link" name="zoom_link" value="{{ $event->zoom_link }}">
                </div>

                <button type="button" class="btn btn-primary" id="check-room-availability">Cek Ketersediaan</button>
                <input type="hidden" class="form-control" id="backgroundColor" name="backgroundColor" value="#0073b7">

                <div class="form-group">
                    <label>Unit yang terlibat:</label><br>
                    @foreach($telegramaccounts as $pic)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="agenda_unit[]" value="{{ $pic->account }}" {{ in_array($pic->account, json_decode($event->agenda_unit,true)) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $pic->account }}</label>
                        </div>
                    @endforeach
                    <div id="custom-units-container" class="mt-2"></div>
                    <button type="button" class="btn btn-primary" id="add-custom-unit">Tambahkan Unit Baru</button>
                </div>

                <button type="submit" class="btn btn-success" id="submit-event">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-event-form');
        const useZoomSelect = document.getElementById('use_zoom');
        const zoomLinkContainer = document.getElementById('zoom_link_container');

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
                text: 'Please wait while your event is being updated.',
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
                    text: 'Event updated successfully!',
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
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                });
            } else {
                Swal.fire('Error', 'Please fill in all required fields.', 'error');
            }
        });

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
    });
</script>

@endsection
