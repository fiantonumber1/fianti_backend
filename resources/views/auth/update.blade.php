@extends('layouts.universal')

@php
    $categoryprojectbaru= json_decode($category, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
    $listpic = json_decode($categoryproject, true); // $listpic[0] adalah "Unit1"
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="">Update Informasi</a></li>
                    <li class="breadcrumb-item"><a href="">List Dokumen</a></li>
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
            <h3 class="card-title">Update Informasi</h3>
        </div>

        <div class="card-body">
            <form id="updateInfoForm" action="{{ route('updateInformasi') }}" method="POST">
                @csrf
                @method('PUT') <!-- Tambahkan ini untuk mengirimkan metode PUT -->
                <div class="form-group">
                    <label for="name">Nama:</label>
                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required class="form-control">
                </div>
                

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="waphonenumber">Wa Number:</label>
                    <input type="text" id="waphonenumber" name="waphonenumber" value="{{ Auth::user()->waphonenumber ??""}}" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="rule">Rule:</label>
                    <select id="rule" name="rule" required class="form-control">
                        @foreach($listpic as $rule)
                            <option value="{{ $rule }}" {{ Auth::user()->rule == $rule ? 'selected' : '' }}>{{ $rule }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Informasi</button>
            </form>
            <form id="updatePasswordForm" action="{{ route('updatePassword') }}" method="POST">
                @csrf
                @method('PUT') <!-- Tambahkan ini untuk mengirimkan metode PUT -->
                <div class="form-group">
                    <label for="current_password">Password Saat Ini:</label>
                    <input type="password" id="current_password" name="current_password" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="password">Password Baru:</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>


            <!-- Display the current TTD file or prompt for upload -->
            <div class="form-group mt-4">
                <label for="file">Upload Tanda Tangan (TTD):</label>
                @if ($existingFile)
                    <div>
                        <p>Tanda Tangan Terdaftar:</p>
                        <a href="{{ asset('storage/' . $existingFile->link) }}" target="_blank">
                            <img src="{{ asset('storage/' . $existingFile->link) }}" alt="TTD" width="100">
                        </a>
                    </div>
                @else
                    <div>
                        <p>Anda belum mengunggah tanda tangan. Silakan unggah file tanda tangan.</p>
                        <form action="{{ route('updatettd') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file">
                            <button type="submit" class="btn btn-success">Upload TTD</button>
                        </form>
                    </div>
                @endif
            </div>
           



        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    

</div>
</div>
</div>
@endsection

@push('script') 
<!-- Include SweetAlert script -->
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Menangani submission form updateInfoForm
            const updateInfoForm = document.getElementById('updateInfoForm');
            updateInfoForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Mencegah form dari submit secara default

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan memperbarui informasi pengguna.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, perbarui!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Lanjutkan dengan pengiriman form jika dikonfirmasi
                        Swal.fire({
                        title: "Updated!",
                        text: "Your information has been updated.",
                        icon: "success"
                        });
                        updateInfoForm.submit();
                    }
                });
            });

            // Menangani submission form updatePasswordForm
            const updatePasswordForm = document.getElementById('updatePasswordForm');
            updatePasswordForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Mencegah form dari submit secara default

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan memperbarui password pengguna.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, perbarui!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Lanjutkan dengan pengiriman form jika dikonfirmasi
                        Swal.fire({
                        title: "Updated!",
                        text: "Your information has been updated.",
                        icon: "success"
                        });
                        updatePasswordForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
