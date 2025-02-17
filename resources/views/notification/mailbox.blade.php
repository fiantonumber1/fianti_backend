@php
    $unreadCount=0;
@endphp

@extends('layouts.mail')

@section('container2')
    <title>Mailbox</title>
@endsection

@section('container3')
    <table id="example2" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th class="text-center">Nomor Surat</th>
                <th class="text-center">Nama Dokumen</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Nama Project</th>
                <!-- <th class="text-center">Status</th> -->
                <th class="text-center">Status Dokumen</th>
                <th class="text-center">Buka Dokumen</th>
                <th class="text-center">Sudah Dibaca</th>
                <th class="text-center">Jenis Notifikasi</th>
                <th class="text-center">Id Notifikasi</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @foreach($notifs as $notif)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($notif->memo)->documentnumber ?? 'N/A' }}</td>
                    <td>{{ optional($notif->memo)->documentname ?? 'N/A' }}</td>
                    <td>{{ $notif->created_at->format('d-m-Y') }}</td>
                    <td>{{ optional($notif->memo)->documentnumber ?? 'N/A' }}</td>
                    <!-- <td>
                        @if($notif->memo && $notif->memo->documentstatus == "Tolak")
                            <span class="badge badge-danger">Ditolak</span>
                            <p class="mb-0">{{ $notif->alasan }}</p>
                        @elseif($notif->memo && $notif->memo->documentstatus == "Terima")
                            <span class="badge badge-success">Terima</span>
                        @else
                            <select id="status" name="status" class="form-control">
                                <option value="Terima">Terima</option>
                                <option value="Tolak">Tolak</option>
                            </select>
                            <div id="alasan-container" style="display: none;">
                                <label for="alasan">Alasan Tolak:</label>
                                <input type="text" id="alasan" name="alasan" class="form-control">
                            </div>
                            <br>
                            <button onclick="submitAnswer('{{ $notif->nama_file }}', '{{ $notif->nama_project }}', '{{ $notif->dokumen_id }}', '{{ $notif->nama_divisi }}', '{{ "Memo" }}')" class="btn btn-success btn-sm">Submit</button>
                        @endif
                    </td> -->
                    <td>
                        @if(optional($notif->memo)->documentstatus == 'Terbuka')
                            <span class="badge badge-success">{{ $notif->memo->documentstatus }}</span>
                        @else
                            <span class="badge badge-secondary">{{ optional($notif->memo)->documentstatus ?? 'N/A' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($notif->notifmessage_type == "App\Models\NewMemo")
                            <a href="{{ route('new-memo.show', ['memoId' => $notif->notifmessage_id, 'rule' => auth()->user()->rule]) }}"" class="btn btn-primary btn-sm mr-2">Detail</a>
                        @endif
                    </td>
                    <td>
                        @if($notif->status == 'read')
                            <span class="text-success" title="Terbuka">
                                <i class="fas fa-envelope-open"></i>
                            </span>
                        @else
                            <span class="text-info" title="Tertutup">
                                <i class="fas fa-envelope"></i>
                            </span>
                        @endif
                    </td>
                    <td>
                        <button class="btn" style="background-color: 
                            @if($notif->notifmessage_type == "App\Models\NewMemo") orange;
                            @elseif($notif->notifmessage_type == "App\Models\allert1") yellow;
                            @elseif($notif->notifmessage_type == "App\Models\allert2") red;
                            @else white;
                            @endif">
                            {{ ucfirst($notif->notificationcategory) }}
                        </button>
                    </td>
                    <td>{{ $notif->id }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.querySelectorAll('select[id^="status-"]').forEach(select => {
            select.addEventListener('change', function () {
                var notifId = this.id.split('-')[1];
                var alasanContainer = document.getElementById('alasan-container-' + notifId);
                if (this.value === 'Tolak') {
                    alasanContainer.style.display = 'block';
                } else {
                    alasanContainer.style.display = 'none';
                }
            });
        });

        function submitAnswer(namaFile, namaProject, idDocument, namaDivisi, notificationcategory, notifId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah status dokumen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah status!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var status = document.getElementById('status-' + notifId).value;
                    var alasan = document.getElementById('alasan-' + notifId).value;

                    var url = "{{ url('/mail') }}?namafile=" + encodeURIComponent(namaFile) +
                        "&namaproject=" + encodeURIComponent(namaProject) +
                        "&iddocument=" + encodeURIComponent(idDocument) +
                        "&namadivisi=" + encodeURIComponent(namaDivisi) +
                        "&status=" + encodeURIComponent(status) +
                        "&alasan=" + encodeURIComponent(alasan) +
                        "&notificationcategory=" + encodeURIComponent(notificationcategory);

                    window.location.href = url;
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Status Anda berhasil diubah.",
                        icon: "success"
                    });
                }
            });
        }
    </script>
@endsection
