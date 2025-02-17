@extends('layouts.table1')

@section('container1')
    <h1>File All</h1>
@endsection
@section('container2')
    <h3 class="card-title">Page: File All</h3>
@endsection

@section('container3')
        @if(count($files) > 0)
            <form action="{{ route('file.deleteMultiple') }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger mb-3">Hapus yang Dipilih</button>
                <!-- Tambahkan tombol upload di sini -->
                <a href="{{ url('/file/aksi/upload') }}" class="btn btn-primary mb-3">Upload File</a>
                <table id="example2" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Checkbox</th>
                            <th>Nama File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>
                                    <label>
                                        <input type="checkbox" name="fileIds[]" value="{{ $file->id }}">   
                                    </label>
                                </td>
                                <td>
                                    <a href="{{ route('metadata.show', $file->id) }}" style="font-size: 1.2em; font-weight: bold;">
                                        {{ $file->filename }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        @else
            <p>Tidak ada file yang ditemukan.</p>
        @endif
@endsection

@section('scripts')
    <script>
        // Untuk menambahkan fitur untuk memeriksa semua kotak centang
        $('#checkAll').click(function () {
            $('input:checkbox').prop('checked', this.checked);
        });

        // Untuk mencegah pengguna melakukan submit tanpa memilih setidaknya satu file
        $('#deleteForm').submit(function () {
            if ($('input:checked').length === 0) {
                alert('Pilih setidaknya satu file untuk dihapus.');
                return false;
            }
        });
    </script>
@endsection
