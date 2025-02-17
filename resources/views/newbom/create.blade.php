@extends('layouts.upload')


@php  
    $useronly = auth()->user();
@endphp
@section('container1') 
    <div class="col-sm-6">
        <h1>Create New BOM</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Create New BOM</li>
        </ol>
    </div>
@endsection

@section('container2')
    <div class="card-header">Create New BOM</div>
    
    <div class="card-body">
        <form id="uploadForm" action="{{ route('newbom.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <label for="BOMnumber">BOM Number:</label>
            <input type="text" name="BOMnumber" required><br>

            <label for="proyek_type">Proyek Type:</label>
            <select name="proyek_type" required>
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select><br>
            <select name="unit" required>
                @foreach($units as $unit)
                    <option value="{{ $unit }}">{{ $unit }}</option>
                @endforeach
            </select><br>

            <button type="submit">Create</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const jenisUploadSelect = document.getElementById('jenisupload');
            const unitField = document.getElementById('unitField');
            const projectTypeField = document.getElementById('projectTypeField');

            function toggleFields() {
                if (jenisUploadSelect.value === 'formatprogress' || jenisUploadSelect.value === 'formatrencana') {
                    unitField.style.display = 'none';
                    projectTypeField.style.display = 'none';
                } else {
                    unitField.style.display = 'block';
                    projectTypeField.style.display = 'block';
                }
            }

            // Initial call to set the correct visibility
            toggleFields();

            // Add event listener for changes
            jenisUploadSelect.addEventListener('change', toggleFields);

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
