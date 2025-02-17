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
                    <div class="card-header">Katalog Komat</div>
                <div class="row">
                    
                    <div class="card-body">
                        <table class="table table-bordered" id="katalogKomatTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Material</th>
                                    <th>Deskripsi</th>
                                    <th>Spesifikasi</th>
                                    <th>UoM</th>
                                    <th>Stok UU Ekspedisi</th>
                                    <th>Stok UU Gudang</th>
                                    <th>Stok Project Ekspedisi</th>
                                    <th>Stok Project Gudang</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('#katalogKomatTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('katalogkomat.getData') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kodematerial', name: 'kodematerial' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'spesifikasi', name: 'spesifikasi' },
                    { data: 'UoM', name: 'UoM' },
                    { data: 'stokUUekpedisi', name: 'stokUUekpedisi' },
                    { data: 'stokUUgudang', name: 'stokUUgudang' },
                    { data: 'stokprojectekpedisi', name: 'stokprojectekpedisi' },
                    { data: 'stokprojectgudang', name: 'stokprojectgudang' },
                ]
            });
        });
    </script>
    
@endpush
