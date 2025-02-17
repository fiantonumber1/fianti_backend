@extends('layouts.table1')

@section('container1')
    <h1>Database Zoom Access Tokens</h1>
@endsection

@section('container2')
    <h3 class="card-title">Database Zoom Access Tokens</h3>
@endsection

@section('container3')
    <div class="row mb-3">
        @if(auth()->user()->rule == "superuser")
            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <!-- Button to delete selected items -->
                <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
            </div>
        @endif
        <div class="col-md-3 col-sm-6 col-12 mb-3">
            <a href="{{ route('zoom.create') }}" class="btn btn-primary">Create New Zoom Access Token</a>
        </div>
    </div>

    <table id="example2" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all">
                </th>
                <th>ID</th>
                <th>Account Name</th>
                <th>Jenis</th>
                <th>Client ID</th>
                <th>Redirect URL</th>
                <th>Hotkey</th>
                <th>Access Token</th>
                <th>Refresh Token</th>
                <th>Expires At</th>
                <th>Account Expired</th>
                <th>Countdown Account Expired</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allzoomaccess as $zoomaccess)
                <tr>
                    <td>
                        <input type="checkbox" name="zoom_access_ids[]" value="{{ $zoomaccess->id }}">
                    </td>
                    <td>{{ $zoomaccess->id }}</td>
                    <td>{{ $zoomaccess->account_name }}</td>
                    <td>{{ $zoomaccess->jenis }}</td>
                    <td>{{ $zoomaccess->zoom_clientid }}</td>
                    <td>{{ $zoomaccess->zoom_redirecturl }}</td>
                    <td>{{ $zoomaccess->zoom_hotkey ?? "" }}</td>
                    <td>{{ Str::limit($zoomaccess->access_token, 20) }}</td>
                    <td>{{ Str::limit($zoomaccess->refresh_token, 20) }}</td>
                    <td>{{ \Carbon\Carbon::parse($zoomaccess->expires_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($zoomaccess->account_expired)->format('d/m/Y H:i') }}</td>
                    <td class="countdown" data-expiry="{{ \Carbon\Carbon::parse($zoomaccess->account_expired)->toIso8601String() }}"></td>
                    <td>
                        <a href="{{ route('zoom.auth', $zoomaccess->account_name) }}" class="btn btn-info btn-sm">Auth</a>
                        <a href="{{ route('zoom.show', $zoomaccess->id) }}" class="btn btn-info btn-sm">View</a>
                        <form action="{{ route('zoom.destroy', $zoomaccess->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Select all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="zoom_access_ids[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        function handleDeleteMultipleItems() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedZoomAccessIds = [];
                    var checkboxes = document.querySelectorAll('input[name="zoom_access_ids[]"]:checked');
                    checkboxes.forEach(function(checkbox) {
                        selectedZoomAccessIds.push(checkbox.value);
                    });

                    $.ajax({
                        url: "{{ route('zoom.deleteMultiple') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            zoom_access_ids: selectedZoomAccessIds
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        // Countdown script
        document.addEventListener('DOMContentLoaded', function() {
            var countdownElements = document.querySelectorAll('.countdown');
            countdownElements.forEach(function(countdownElement) {
                var expiryDate = new Date(countdownElement.getAttribute('data-expiry')).getTime();
                var interval = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = expiryDate - now;

                    if (distance < 0) {
                        clearInterval(interval);
                        countdownElement.innerHTML = "Expired";
                    } else {
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownElement.innerHTML = days + "d " + hours + "h "
                            + minutes + "m " + seconds + "s ";
                    }
                }, 1000);
            });
        });
    </script>
@endsection
