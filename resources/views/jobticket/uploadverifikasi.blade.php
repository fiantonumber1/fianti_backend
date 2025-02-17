@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp


@section('container2') 
    <div class="content-header">
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
            <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
            <li class="breadcrumb-item"><a href="">Upload Dokumen</a></li>
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
                        <h3 class="card-title">Upload Bukti Verifikasi</h3>
                    </div>

                    <div class="card-body">
                        <form id="uploadForm" action="{{ route('jobticket.revisionapprovedoc', ['revision' => $revision]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="file" class="file-label">Choose File:</label>
                                <div id="file-input-container">
                                    <div class="file-input-group">
                                        <input type="file" name="file[]" onchange="handleFileChange(this)" class="form-control-file" multiple>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="button" onclick="confirmUpload()" class="btn btn-success">Upload</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>

            </div>
        </div>
    </div>
@endsection


@push('scripts')

    <!-- /.card -->

    <!-- Include SweetAlert script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function handleFileChange(input) {
            const removeBtn = input.nextElementSibling.nextElementSibling;
            if (input.files.length > 0) {
                removeBtn.style.display = 'inline';
            } else {
                removeBtn.style.display = 'none';
            }
        }

        function confirmUpload() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan mengunggah berkas ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, unggah!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form jika dikonfirmasi
                    Swal.fire({
                            title: "Updated!",
                            text: "Your information has been uploaded.",
                            icon: "success"
                            });
                    document.getElementById('uploadForm').submit();
                }
            });
        }
    </script>
@endpush

@push('css')
    <style>
        .file-input-group {
            margin-bottom: 10px;
        }

        .remove-file-btn {
            cursor: pointer;
            color: red;
            margin-left: 10px;
        }
    </style>
@endpush
