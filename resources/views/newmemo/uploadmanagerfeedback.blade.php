@extends('layouts.universal')


@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.show',['memoId'=>$document->id]) }}">{{$document->documentnumber}}</a></li>
                    <li class="breadcrumb-item"><a href="">Upload Feedback by Manager</a></li>
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
            <h3 class="card-title">Upload Feedback by Manager</h3>
        </div>
        <div class="card-body">
            <form id="uploadForm" action="{{ route('new-memo.allfeedback', ['memoId' => $document->id]) }}" method="POST"
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
                @if (config('app.url') === 'https://inka.goovicess.com')
                    <div class="form-group">
                        <label for="filecount">Jumlah File (Sementara File Kosong):</label>
                        <input type="number" id="filecount" name="filecount" class="form-control" min="0" max="100" step="1" value="0">
                    </div>
                @else
                    <div class="form-group">
                        <label for="file">Choose File:</label>
                        <div id="file-input-container">
                            <div class="file-input-group">
                                <input type="file" name="file[]" onchange="handleFileChange(this)">
                                <span class="remove-file-btn" onclick="removeFileInput(this)">Remove</span>
                            </div>
                        </div>
                    </div>
                @endif

                
                <button type="button" class="btn btn-info mb-3" onclick="addFileInput()">Add Another File</button>
                <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                <input type="hidden" name="time" value="">
                <input type="hidden" name="level" value="{{ str_replace("Manager ","",auth()->user()->rule) }}">
                <input type="hidden" name="conditionoffile" value="Draft">
                <input type="hidden" name="conditionoffile2" value="feedback">
                <input type="hidden" name="feedbacklevel" value="feedbackmanager">
                <div>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
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
    <!-- Load CKEditor from CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#commentku'))
            .catch(error => {
                console.error(error);
            });
    </script>
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
    </script>
    
@endpush