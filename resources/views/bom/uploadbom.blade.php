@extends('layouts.upload')

@php
    $categoryprojectbaru = json_decode($categoryproject, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
    $listproject = json_decode($categoryproject, true);
@endphp

@section('container1') 
    <div class="col-sm-6">
        <h1>Upload Gabungan Feedback</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Gabungan Feedback</li>
        </ol>
    </div>
@endsection

@section('container2')
    <div class="card-header">Unggah File Excel</div>
    <div class="card-body">
        <form id="uploadForm" action="{{ route('importbom.excel') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="bomnumber">Nomor BOM:</label>
                <input type="text" class="form-control" id="bomnumber" name="bomnumber" value="{{ old('bomnumber') }}">
            </div>
            <div class="form-group">
                <label for="project_type">Select Project Type:</label>
                <select name="project_type" id="project_type" class="form-control" required>
                    @foreach($listproject as $memberlistproject)
                        <option value="{{$memberlistproject}}">{{$memberlistproject}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="file">Pilih File Excel (.xlsx, .xls):</label>
                <input type="file" class="form-control-file" id="file" name="file" accept=".xlsx, .xls">
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn">Unggah</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');

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
@endsection
