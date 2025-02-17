@extends('layouts.universal')

@section('container2') 
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="">Upload Excel</a></li>
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
                <div class="card-header">Unggah File Excel</div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <a href="https://drive.google.com/drive/folders/1qL-MQCbp67ndb8U_K0TLC1gmBSdLzisk?usp=sharing"
                            class="btn btn-success btn-sm btn-block" target="_blank">
                            Download format
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('importnewbom.excel') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div id="formContent">
                            <div class="form-group" id="bomnumberGroup">
                                <label for="bomnumber">Nomor BOM:</label>
                                <input type="text" class="form-control" id="bomnumber" name="bomnumber"
                                    value="{{ old('bomnumber') }}">
                            </div>
                            <div class="form-group" id="projectTypeGroup">
                                <label for="project_type_id">Select Project Type:</label>
                                <select name="project_type_id" id="project_type_id" class="form-control" required>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}">{{$project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="unitGroup">
                                <label for="unit">Select Unit:</label>
                                <select name="unit" id="unit" class="form-control" required>
                                    @foreach($units as $unit)
                                        <option value="{{$unit}}">{{$unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="file">Pilih File Excel (.xlsx, .xls):</label>
                            <input type="file" class="form-control-file" id="file" name="file" accept=".xlsx, .xls"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="jenisupload">Jenis Upload:</label>
                            <select name="jenisupload" id="jenisupload" class="form-control" required>
                                <option value="formatprogress">Format_Progress</option>
                                <option value="formatupdateprogress">Format_Relasi_Dokumen</option>
                                <option value="formatrencana">Format_Rencana</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Unggah</button>
                    </form>
                </div>


                <!-- /.card-body -->
            </div>
            <!-- /.card -->



        </div>
    </div>
</div>





























@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const jenisUpload = document.getElementById('jenisupload');
            const formContent = document.getElementById('formContent');

            // Function to toggle visibility of form fields
            function toggleFormFields() {
                if (jenisUpload.value === 'formatprogress' || jenisUpload.value === 'formatupdateprogress') {
                    formContent.style.display = 'none'; // Hide other fields
                } else {
                    formContent.style.display = 'block'; // Show all fields
                }
            }

            // Initialize form fields visibility
            toggleFormFields();

            // Event listener for changes on the jenis upload dropdown
            jenisUpload.addEventListener('change', toggleFormFields);

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting normally

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
                            title: 'File Excel Berhasil Diunggah!',
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