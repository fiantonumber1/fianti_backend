@extends('layouts.universal')

@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="/">Progress</a></li>
            <li class="breadcrumb-item active text-bold">Tracking Progress</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page monitoring memo <span class="badge badge-info ml-1"></span></h3>
        </div>  
        <div class="card-body">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">New Report ID</th>
                        <th scope="col">Nodokumen</th>
                        <th scope="col">Namadokumen</th>
                        <th scope="col">Level</th>
                        <!-- Tambahkan kolom lainnya sesuai kebutuhan -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($newprogressreports as $progressreport)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $progressreport->newreport_id }}</td>
                            <td>{{ $progressreport->nodokumen }}</td>
                            <td>{{ $progressreport->namadokumen }}</td>
                            <td>{{ $progressreport->level }}</td>
                            <!-- Tambahkan kolom lainnya sesuai kebutuhan -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
