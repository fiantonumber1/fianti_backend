@extends('layouts.main')

@section('container2')
    <div class="error-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    {{--{{ route('documentMTPR.upload') }}--}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Upload File</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="uploadfile">Nama File:</label>
                    <input type="text" id="uploadfile" name="uploadfile" class="form-control" required>
                </div>
                <input type="hidden" name="jenis_aksi" value="upload">
                <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
                <div class="form-group">
                    <label for="file">Choose File:</label>
                    <input type="file" id="file" name="file[]" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addFileButton = document.getElementById('addFile');
            const additionalFileContainer = document.getElementById('additionalFileContainer');

            addFileButton.addEventListener('click', function () {
                const newFileInput = document.createElement('input');
                newFileInput.type = 'file';
                newFileInput.name = 'file[]';
                newFileInput.className = 'form-control-file mt-2'; // Adjust the margin as needed
                newFileInput.required = true; // Set the new input as required
                additionalFileContainer.appendChild(newFileInput);
            });
        });
    </script>
@endsection
