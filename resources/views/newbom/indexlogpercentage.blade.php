@extends('layouts.table1showprogressreport')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2') 
    <div class="card card-outline card-danger">
        <div class="card-header">History</div>
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Status</th>
                        <th scope="col">Nama Uploader</th>
                        <th scope="col">Waktu Upload</th>
                        <th scope="col">Aksi</th>
                        <th scope="col">Persentase Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $penghitung = 1;
                    @endphp
                    @foreach ($logs as $riwayat)
                        <tr>
                            <td>{{ $penghitung++ }}</td>
                            <td>{{ $riwayat->level }}</td>
                            <td>{{ $riwayat->user }}</td>
                            <td>{{ $riwayat->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $riwayat->aksi }}</td>
                            <td class="project-actions text-left">
                                @php
                                    $message = json_decode($riwayat->message, true);
                                @endphp
                                @if (isset($message['persentase']) && is_array($message['persentase']))
                                    @foreach ($message['persentase'] as $key => $value)
                                        <div class="col-md-12 text-left column-layout">
                                            <div class="badge badge-combined">
                                                <span class="badge-section badge-danger">
                                                    {{ $key ?? "" }}
                                                </span>
                                                <span class="badge-section badge-primary">
                                                    {{ $value ?? "" }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @section('script') 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>
    <script>
        $.fn.dataTable.moment('DD-MM-YYYY'); // Tentukan format tanggal yang digunakan dalam tabel Anda
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[3, 'desc']], // Order by the fourth column (created_at) in descending order
            "columnDefs": [
                {
                    "targets": 3, // Kolom yang berisi tanggal (Waktu Upload)
                    "type": 'date',
                    "render": function (data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return moment(data, 'DD-MM-YYYY HH:mm').format('YYYY-MM-DD HH:mm');
                        }
                        return data;
                    }
                }
            ]
        });
    </script>
    @endsection
@endsection
