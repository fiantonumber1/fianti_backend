@extends('layouts.main')

@section('container1') 
    <div class="col-sm-6">
        <h1>Upload Masal Memo</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Masal Memo</li>
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
            <h3 class="card-title">Upload Massal Memo</h3>
        </div>

        <div class="card-body">
            <!-- Tambahkan tombol upload di sini -->
            <a href="{{ url('/previewdocument/massupload_arijayadigitalprinting.xlsx') }}" class="btn btn-primary mb-3">Download Contoh Excel</a>
           <form action="{{ url('/document/memo/massupload') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="excel_file">
                <button type="submit">Upload</button>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection

