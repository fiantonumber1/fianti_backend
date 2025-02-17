@extends('layouts.main')

@section('container1') 
    <div class="col-sm-6">
        <h1>Upload Dokumen berTTD</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Dokumen berTTD</li>
        </ol>
    </div>
@endsection

@section('container2')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Upload Signature</h3>
    </div>

    <div class="card-body">
        <form id="uploadForm" action="{{ route('upload.Signature', ['id' => $document->id]) }}" method="POST"
            enctype="multipart/form-data">
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
            
            <input type="hidden" name="aksi" value="uploaddocument">
            <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
            <input type="hidden" name="picrule" value="{{ auth()->user()->rule }}">
            <input type="hidden" name="review" value="sudah">
            <input type="hidden" name="hasil_review" value="signature">
            <input type="hidden" name="comment" value="">
            <input type="hidden" name="author" value="{{ auth()->user()->name }}">
            <input type="hidden" name="time" value="">
            <input type="hidden" name="level" value="signature">
            <input type="hidden" name="conditionoffile" value="signature">
            <input type="hidden" name="conditionoffile2" value="signature">
            <div>
                <button type="button" onclick="confirmUpload()" class="btn btn-success">Upload</button>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
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
@endsection

@section('container3')
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
@endsection
