@extends('layouts.universal')

@php
    $categoryprojectbaru = json_decode($categoryproject, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
    $listproject = json_decode($categoryproject, true);

    $categoryunit_for_progres_dokumen = json_decode($unit_for_progres_dokumen, true)[0];
    $categoryunit_for_progres_dokumen_1 = trim($categoryunit_for_progres_dokumen, '"'); // Remove the extra double quotes
    $unitforprogresdokumen = json_decode($categoryunit_for_progres_dokumen_1, true);
@endphp
@php  
    $useronly = auth()->user();
@endphp

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="">Upload Excel</a></li>
                </ol>
            </div>
        </div>
    </div>
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
                    <h3 class="card-title">Unggah File Excel</h3>
                </div>
                <div class="row">


                </div>
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('jobticket.updateexcel') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="jenisupload">Jenis Upload:</label>
                            <select name="jenisupload" id="jenisupload" class="form-control" required>
                                <option value="formatprogress">Format_Progress</option>
                            </select>
                        </div>
                        <div class="form-group" id="unitField">
                            <label for="progressreportname">Unit:</label>
                            <select name="progressreportname" id="progressreportname" class="form-control" required>
                                @foreach($unitforprogresdokumen as $memberunitforprogresdokumen)
                                    <option value="{{$memberunitforprogresdokumen}}">{{$memberunitforprogresdokumen}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file">Pilih File Excel:</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Unggah</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const jenisUploadSelect = document.getElementById('jenisupload');
            const unitField = document.getElementById('unitField');
            const projectTypeField = document.getElementById('projectTypeField');

            function toggleFields() {
                if (jenisUploadSelect.value === 'formatprogress' || jenisUploadSelect.value === 'formatrencana') {
                    unitField.style.display = 'block';
                    projectTypeField.style.display = 'none';
                } else {
                    unitField.style.display = 'block';
                    projectTypeField.style.display = 'block';
                }
            }

            toggleFields();

            jenisUploadSelect.addEventListener('change', toggleFields);

            form.addEventListener('submit', function (event) {
                event.preventDefault();

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
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush