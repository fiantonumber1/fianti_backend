@extends('layouts.universal')


@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
                    <li class="breadcrumb-item active">Unggah RAMS Document</li>
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

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Upload RAMS</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('ramsdocuments.store') }}" method="POST" enctype="multipart/form-data"
                        id="uploadForm">
                        @csrf
                        <div class="form-group">
                            <label for="documentname">Nama Dokumen</label>
                            <input type="text" class="form-control" id="documentname" name="documentname" required>
                        </div>
                        <div class="form-group">
                            <label for="documentnumber">No Dokumen</label>
                            <input type="text" class="form-control" id="documentnumber" name="documentnumber" required>
                        </div>
                        <div class="form-group">
                            <label for="filenames">Pilih File</label>
                            <input type="file" class="form-control-file" id="filenames" name="filenames[]" multiple
                                required>
                        </div>
                        <div id="fileList"></div>

                        <div class="form-group">
                            <label for="proyek_type">Project:</label>
                            <select class="form-control" name="proyek_type" id="proyek_type">
                                @foreach($listproject as $project)
                                    <option value="{{ $project }}">{{ $project }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Project PIC:</label><br>
                            @foreach($listpic as $pic)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="ramsdocument_unit[]"
                                        value="{{ $pic }}">
                                    <label class="form-check-label">{{ $pic }}</label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-success">Buat</button>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->



        </div>
    </div>
</div>
@endsection

@push('css') 

    <style>
        .file-input-group {
            margin-bottom: 10px;
        }

        .remove-file-btn {
            cursor: pointer;
            color: red;
        }
    </style>
@endpush

@push('scripts')
    <!-- Include SweetAlert script -->
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('filenames');
            const fileList = document.getElementById('fileList');

            fileInput.addEventListener('change', function () {
                fileList.innerHTML = '';
                Array.from(fileInput.files).forEach(file => {
                    const listItem = document.createElement('div');
                    listItem.textContent = file.name;
                    fileList.appendChild(listItem);
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting normally

                // You can add additional validation logic here if needed

                // Show SweetAlert with confirmation message
                Swal.fire({
                    title: 'Yakin ingin unggah file?',
                    text: 'Pilih "Ya" untuk mengunggah file.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'File Berhasil Diunggah!',
                            text: 'Tindakan selanjutnya di sini...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        form.submit(); // Submit the form
                    }
                });
            });
        });
    </script>


@endpush