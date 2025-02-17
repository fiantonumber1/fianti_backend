@extends('layouts.main')


@section('container1') 
    <div class="col-sm-6">
        <h1>Upload Feedback</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Feedback</li>
        </ol>
    </div>
@endsection
@section('container2')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Upload Feedback</h3>
    </div>

    <div class="card-body">
    <form id="uploadForm" action="{{ route('upload.Feedback', ['id' => $document->id]) }}" method="POST"
                enctype="multipart/form-data" onsubmit="return validateForm()">
                @csrf
                @method('PUT')
                <!-- Hidden Input Fields -->
                <input type="hidden" name="aksi" value="uploaddocument">
                <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
                <!-- Upload file and comment -->
                <div class="form-group">
                    <label for="review">Apakah anda sudah melakukan review atas dokumen approval?</label>
                    <select id="review" name="review" class="form-control" required>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hasil_review">Dari hasil review atas dokumen approval tersebut, apakah dapat diterima?</label>
                    <select id="hasil_review" name="hasil_review" class="form-control" required>
                        <option value="Ya, dapat diterima">Ya, dapat diterima</option>
                        <option value="Ya, dapat diterima dengan catatan">Ya, dapat diterima dengan catatan</option>
                        <option value="Ada catatan">Ada catatan</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment (optional):</label>
                    <textarea class="form-control" id="comment" name="comment" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <label for="file">Choose File:</label>
                    <div id="file-input-container">
                        <div class="file-input-group">
                            <input type="file" name="file[]" onchange="handleFileChange(this)">
                            <span class="remove-file-btn" onclick="removeFileInput(this)">Remove</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-info mb-3" onclick="addFileInput()">Add Another File</button>
                <input type="hidden" name="picrule" value="{{ auth()->user()->rule }}">
                <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                <input type="hidden" name="time" value="">
                <input type="hidden" name="level" value="">
                <input type="hidden" name="conditionoffile" value="draft">
                <input type="hidden" name="conditionoffile2" value="feedback">
                <div>
                    <button type="button" class="btn btn-success" onclick="confirmUpload()">Upload</button>
                </div>
            </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

    <!-- Load CKEditor from CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#commentku'))
            .catch(error => {
                console.error(error);
            });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert script -->
    <script>
        function addFileInput() {
            const fileInputContainer = document.getElementById('file-input-container');
            const newFileInputGroup = document.createElement('div');
            newFileInputGroup.classList.add('file-input-group');

            newFileInputGroup.innerHTML = `
                <input type="file" name="file[]" onchange="handleFileChange(this)" required>
                <span class="remove-file-btn" onclick="removeFileInput(this)">Remove</span>
            `;

            fileInputContainer.appendChild(newFileInputGroup);
        }

        function removeFileInput(element) {
            const fileInputContainer = document.getElementById('file-input-container');
            fileInputContainer.removeChild(element.parentNode);
        }

        function handleFileChange(input) {
            const removeBtn = input.nextElementSibling.nextElementSibling;
            if (input.files.length > 0) {
                removeBtn.style.display = 'inline';
            } else {
                removeBtn.style.display = 'none';
            }
        }

        function validateForm() {
            // Your validation logic here
            return true; // Return true if the form is valid, false otherwise
        }

        function confirmUpload() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan mengunggah feedback ini.',
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
@endsection
@section('container3')
    <title>Upload Feedback</title>
    <style>
        .file-input-group {
            margin-bottom: 10px;
        }

        .remove-file-btn {
            cursor: pointer;
            color: red;
        }
    </style>
@endsection