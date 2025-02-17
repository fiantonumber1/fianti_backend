@extends('layouts.universal')


@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
                    <li class="breadcrumb-item"><a href="">Upload Memo</a></li>
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
                    <h3 class="card-title">Upload Memo</h3>
                </div>
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('new-memo.upload') }}" method="POST"
                        enctype="multipart/form-data">
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
                        <div class="form-group">
                            <label for="komat">Masukan 1 komat (Untuk memunculkan operator):</label>
                            <input type="text" id="komat" name="komat" class="form-control" required>
                            <button type="button" class="btn btn-secondary mt-2" onclick="generateIR()">Operator
                                Generate</button>
                        </div>

                        <div class="form-group">
                            <label for="operator">Operator</label>
                            <input type="text" id="operator" name="operator" class="form-control" required>
                        </div>

                        <input type="hidden" name="jenis_aksi" value="upload">
                        <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">

                        <div class="">
                            <div class="row">
                                <div class="col">


                                    @if (config('app.url') === 'https://inka.goovicess.com')
                                        <div class="form-group">
                                            <label for="filecount">Jumlah File (Sementara File Kosong):</label>
                                            <input type="number" id="filecount" name="filecount" class="form-control"
                                                min="0" max="100" step="1" value="0">
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="file">Choose File:</label>
                                            <input type="file" id="file" name="file[]" class="form-control-file" required
                                                multiple>
                                        </div>
                                    @endif

                                    <!-- Tombol "Tambah File" -->
                                    <div class="form-group" id="additionalFileContainer">
                                        <button type="button" class="btn btn-secondary" id="addFile">Tambah File
                                            Lain</button>
                                    </div>
                                </div>


                                <!-- Daftar file yang dipilih -->
                                <div id="selectedFiles"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="proyek_type_id">Select Project Type:</label>
                            <select name="proyek_type_id" id="proyek_type_id" class="form-control" required>
                                @foreach($listproject as $memberlistproject)
                                    <option value="{{$memberlistproject->id}}">{{$memberlistproject->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(auth()->user()->rule == "superuser")
                            <div class="form-group">
                                <label for="asliordummy">Select Type:</label>
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
            const addFileButton = document.getElementById('addFile');
            const additionalFileContainer = document.getElementById('additionalFileContainer');
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const operatorField = document.getElementById('operator');
            const uploadForm = document.getElementById('uploadForm');

            const validOperators = [
                'Product Engineering',
                'Desain Mekanik & Interior',
                'Desain Bogie & Wagon',
                'Desain Carbody',
                'Desain Elektrik',
                'Preparation & Support',
                'Welding Technology',
                'Shop Drawing',
                'Teknologi Proses'
            ];

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
                    updateSelectedFiles();
                });

                newFileInput.appendChild(fileInput);
                newFileInput.appendChild(deleteButton);

                additionalFileContainer.appendChild(newFileInput);
                updateSelectedFiles();
            });

            function updateSelectedFiles() {
                const fileInputs = document.querySelectorAll('input[type="file"]');
                selectedFilesContainer.innerHTML = '';

                fileInputs.forEach(function (input) {
                    if (input.files.length > 0) {
                        const files = Array.from(input.files);
                        files.forEach(function (file) {
                            const fileNameElement = document.createElement('div');
                            fileNameElement.textContent = file.name;
                            selectedFilesContainer.appendChild(fileNameElement);
                        });
                    }
                });
            }

            uploadForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Mencegah form terkirim secara langsung

                const operatorValue = operatorField.value.trim();

                if (!validOperators.includes(operatorValue)) {
                    Swal.fire({
                        title: 'Invalid Operator',
                        text: 'Operator harus salah satu dari: ' + validOperators.join(', '),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

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

                        // Submit the form manually
                        uploadForm.submit();
                    }
                });
            });




        });
    </script>


    <script>
        function generateIR() {
            var komatValue = document.getElementById('komat').value;
            var operatorField = document.getElementById('operator');

            // Show SweetAlert loading spinner
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching operator...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request to fetch the operator based on 'komat'
            fetch('{{ route('newbom.operatorfindbykomat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                },
                body: JSON.stringify({ komat: komatValue })
            })
                .then(response => response.json())
                .then(data => {
                    Swal.close(); // Close the loading spinner

                    // Modify output if it matches specific values
                    if (data.operator === 'Sistem Mekanik' || data.operator === 'Desain Interior') {
                        operatorField.value = 'Desain Mekanik & Interior';
                    } else {
                        operatorField.value = data.operator; // Update the operator field normally
                    }
                })
                .catch(error => {
                    Swal.close(); // Close the loading spinner
                    console.error('Error:', error);
                });
        }
    </script>


@endpush