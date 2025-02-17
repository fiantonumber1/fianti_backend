@extends('layouts.universal')

@section('container2')
<div class="container">
    <a href="{{ route('events.all') }}" class="btn btn-primary">Kembali ke jadwal</a>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Buat Jadwal Baru</h3>
        </div>

        <div class="card-body">
            <h2>Inka Technology Office mengundang Anda untuk bergabung ke rapat Zoom yang terjadwal.</h2>

            <p><strong>Topik:</strong> {{ $event->title }}</p>
            <p><strong>Waktu Awal:</strong> {{ $event->start }} Jakarta</p>
            <p><strong>Waktu Akhir:</strong> {{ $event->end }} Jakarta</p>

            <p><strong>Bergabung Zoom Rapat</strong></p>
            <p>
                <a id="join-url" href="{{ $event->join_url }}">{{ $event->join_url }}</a>
                <button class="btn btn-sm btn-primary copy-btn"
                    onclick="copyToClipboard('{{ $event->join_url }}')">Salin</button>
            </p>

            <p><strong>Host Key:</strong> {{$zoomaccount->zoom_hotkey}}</p>
            <p><strong>ID Rapat:</strong> {{ $event->idrapat }}</p>
            <p><strong>Kode Sandi:</strong> {{ $event->password }}</p>

            <p>
                1. Langsung masuk ke zoom meeting menggunakan ID dan Passcode atau klik tautan link zoom meeting.<br>
                2. Klik participant.<br>
                3. Klik claim host di ujung kanan bawah.<br>
                4. Masukkan host key.<br>
                5. Selamat Anda telah berhasil jadi host meeting yeyy🎉
            </p>

            <p><strong>TIPS :</strong></p>
            <ul>
                <li>Setelah jadi host, disarankan buka Security > Enable waiting room, agar tidak sembarang orang bisa
                    masuk</li>
                <li>Disarankan mengangkat teman menjadi co-host agar ketika Anda tanpa sengaja keluar zoom, maka host
                    akan teralihkan ke co-host. Jika tidak ada co-host, maka host akan teralihkan ke peserta zoom secara
                    acak</li>
            </ul>

            <!-- Delete Button -->
            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus acara ini?');">
                @csrf
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>

            <button id="showParticipants" class="btn btn-success mt-3">Show Participant</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyToClipboard(text) {
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        Swal.fire({
            icon: 'success',
            title: 'Disalin!',
            text: 'URL telah disalin ke papan klip.',
        });
    }
</script>
<script>
    document.querySelector('#showParticipants').addEventListener('click', function () {
        const eventId = {{ $event->id }};

        fetch(`{{ route('events.listMeetingParticipants', '') }}/${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const participants = data.participants.map(p => `
                            <tr>
                                <td>${p.name || 'N/A'}</td>
                                <td>${p.user_email || 'N/A'}</td>
                                <td>${p.join_time}</td>
                                <td>${p.leave_time}</td>
                                <td>${p.duration} seconds</td>
                            </tr>
                        `).join('');

                    Swal.fire({
                        title: 'Participants',
                        html: `
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Join Time</th>
                                            <th>Leave Time</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${participants}
                                    </tbody>
                                </table>
                            `,
                        width: '800px',
                        showCloseButton: true,
                        focusConfirm: false,
                    });
                } else {
                    Swal.fire('Error', 'Failed to fetch participants.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            });
    });
</script>
@endsection