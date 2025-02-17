@extends('layouts.main')

@php
$categoryprojectbaru = json_decode($categoryproject, true)[0];
$categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
$listproject = json_decode($categoryproject, true);

$categoryprojectbaru = json_decode($documentcategory, true)[0];
$categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
$listdocumentkind = json_decode($categoryproject, true);
@endphp
@section('container1') 
    <div class="col-sm-6">
        <h1>Upload Memo</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Memo</li>
        </ol>
    </div>
@endsection

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

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Upload Form</h3>
        </div>

        <div class="card-body">
        <form id="uploadForm" action="{{ route('documentMTPR.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="documentname">Nama Memo:</label>
                    <input type="text" id="documentname" name="documentname" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="documentnumber">Nomor Memo:</label>
                    <input type="text" id="documentnumber" name="documentnumber" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="memoorigin">Asal Memo: (misal logistik)</label>
                    <input type="text" id="memoorigin" name="memoorigin" class="form-control" required>
                </div>

                <input type="hidden" name="jenis_aksi" value="upload">
                <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
                
                <div class="">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="file">Choose File:</label>
                                <input type="file" id="file" name="file[]" class="form-control-file" required multiple>
                            </div>

                            <!-- Tombol "Tambah File" -->
                            <div class="form-group" id="additionalFileContainer">
                                <button type="button" class="btn btn-secondary" id="addFile">Tambah File Lain</button>
                            </div>
                        </div>
                        

                        <!-- Daftar file yang dipilih -->
                        <div id="selectedFiles"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="project_type">Select Project Type:</label>
                    <select name="project_type" id="project_type" class="form-control" required>
                        @foreach($listproject as $memberlistproject)
                            <option value="{{$memberlistproject}}">{{$memberlistproject}}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->rule=="superuser")
                    <div class="form-group">
                        <label for="asliordummy">Select Project Type:</label>
                        <select name="asliordummy" id="asliordummy" class="form-control" required>
                            <option value="asli">asli</option>
                            <option value="palsu">palsu</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="asliordummy" value="asli">
                @endif

                <input type="hidden" name="metadata" value="">
                <input type="hidden" name="category" value="memo">
                <button type="submit" class="btn btn-primary" id="submitBtn">Upload</button>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addFileButton = document.getElementById('addFile');
            const additionalFileContainer = document.getElementById('additionalFileContainer');
            const selectedFilesContainer = document.getElementById('selectedFiles');

            addFileButton.addEventListener('click', function () {
                const newFileInput = document.createElement('div');
                newFileInput.className = 'form-group mt-2'; 

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'file[]';
                fileInput.className = 'form-control-file';
                fileInput.required = true;

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Hapus';
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-danger mt-2';
                deleteButton.addEventListener('click', function () {
                    additionalFileContainer.removeChild(newFileInput); 
                    updateSelectedFiles(); // Update selected files list when a file is removed
                });

                newFileInput.appendChild(fileInput);
                newFileInput.appendChild(deleteButton);

                additionalFileContainer.appendChild(newFileInput);

                updateSelectedFiles(); // Update selected files list when a file is added
            });

            // Function to update selected files list
            function updateSelectedFiles() {
                const fileInputs = document.querySelectorAll('input[type="file"]');
                selectedFilesContainer.innerHTML = ''; // Clear the selected files container

                fileInputs.forEach(function(input) {
                    if (input.files.length > 0) {
                        const files = Array.from(input.files);
                        files.forEach(function(file) {
                            const fileNameElement = document.createElement('div');
                            fileNameElement.textContent = file.name;
                            selectedFilesContainer.appendChild(fileNameElement);
                        });
                    }
                });
            }
        });
    </script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Function to handle form submission with SweetAlert confirmation
        document.addEventListener('DOMContentLoaded', function () {
            const uploadForm = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');

            uploadForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Confirmation',
                    text: 'Do you want to submit the form?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                        title: "Updated!",
                        text: "Your information has been uploaded.",
                        icon: "success"
                        });
                        uploadForm.submit(); // Submit the form if user confirms
                    }
                });
            });
        });
    </script>
@endsection
