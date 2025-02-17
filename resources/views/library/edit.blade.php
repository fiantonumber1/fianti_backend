@extends('layouts.universal')




@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{route('library.index')}}">Manajemen File</a></li>
                    <li class="breadcrumb-item"><a href="#">Edit File</a></li>
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
            <div class="card card-danger card-outline">


                <div class="card-header">
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <h3 class="card-title text-bold">Edit File <span class="badge badge-info ml-1"></span></h3>
                </div>  
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('library.update', $file->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label for="file_name">Nama File</label>
                            <input type="text" name="file_name" class="form-control" value="{{ $file->file_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="file_code">Kode File</label>
                            <input type="text" name="file_code" class="form-control" value="{{ $file->file_code }}" required>
                        </div>

                        <div class="form-group">
                            <label for="project_id">Pilih Proyek</label>
                            <select name="project_id" class="form-control" required>
                                <option value="">-- Pilih Proyek --</option>
                                @foreach ($projects as $id => $title)
                                    <option value="{{ $id }}" {{ $file->project_id == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="path_file">Pilih File Baru (Opsional)</label>
                            <input type="file" name="path_file" class="form-control-file">
                        </div>

                        <button type="submit" class="btn btn-success">Perbarui</button>
                    </form>
                </div>  
                

                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts') 

@endpush