@extends('layouts.universal')

@section('container2') 
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="/">Ekpedisi</a></li>
                    <li class="breadcrumb-item active text-bold">Upload Ekpedisi</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')

<div class="card card-danger card-outline shadow-sm">
    <div class="card-header bg-gradient-info">
        <h3 class="card-title text-bold text-white">Upload Ekpedisi</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Informational Heading -->
        <div class="alert alert-info">
            <h5 class="text-center font-weight-bold">Silakan Upload File PDF Ekpedisi Anda</h5>
            <p class="text-center">Anda bisa memilih lebih dari satu file sekaligus.</p>
        </div>

        <!-- Upload Form with Multiple Files -->
        <div class="d-flex justify-content-center">
            <form action="{{ route('ekpedisi.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group text-center">
                    <label for="file" class="font-weight-bold">Pilih File PDF</label>
                    <input type="file" name="file[]" accept=".pdf" multiple required class="form-control">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection