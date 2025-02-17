@extends('layouts.universal')

@section('container2') 
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <!-- <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="">Upload Excel</a></li> -->
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
                    <div class="card-header">Unggah File Excel</div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <a href="https://drive.google.com/drive/folders/1qL-MQCbp67ndb8U_K0TLC1gmBSdLzisk?usp=sharing" class="btn btn-success btn-sm btn-block" target="_blank">
                            Download format
                        </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" action="{{ route('katalogkomat.excel') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Pilih File Excel (.xlsx, .xls):</label>
                                <input type="file" class="form-control-file" id="file" name="file" accept=".xlsx, .xls" required>
                            </div>
                            <div class="form-group">
                                <label for="jenisupload">Jenis Upload:</label>
                                <select name="jenisupload" id="jenisupload" class="form-control" required>
                                    <option value="formatprogress">Format_Progress</option>
                                    <option value="formatrencana">Format_Rencana</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Unggah</button>
                        </form>
                    </div>

                    
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                

            </div>
        </div>
    </div>

    
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
@endpush
