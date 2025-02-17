@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('newreports.show', ['newreport' => $idprogress]) }}">List Dokumen</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('newreports.showrev', ['newreport' => $idprogress, 'id' => $newprogressreport->id]) }}">List
                            Revisi</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-14">

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h1>Revisi Dokumen</h1>
                </div>

                <div class="card-body">
                    <!-- Cek apakah ada revisi yang ditemukan -->
                    @if($newreporthistorys->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada revisi untuk laporan ini.
                        </div>
                    @else
                        <!-- Daftar revisi -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th>Paper Size</th>
                                        <th>Sheet</th>
                                        <th>Rev</th>
                                        <th>DCR</th>
                                        <th>Level</th>
                                        <th>Drafter</th>
                                        <th>Checker</th>
                                        <th>Deadline Release</th>
                                        <th>Realisasi</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($newreporthistorys as $history)
                                        <tr>
                                            <td>{{ $history->nodokumen }}</td>
                                            <td>{{ $history->namadokumen }}</td>
                                            <td>{{ $history->papersize }}</td>
                                            <td>{{ $history->sheet }}</td>
                                            <td>{{ $history->rev }}</td>
                                            <td>{{ $history->dcr }}</td>
                                            <td>{{ $history->level }}</td>
                                            <td>{{ $history->drafter}}</td>
                                            <td>{{ $history->checker }}</td>
                                            <td>{{ $history->deadlinerelease }}</td>
                                            <td>{{ $history->realisasi }}</td>
                                            <td>{{ $history->documentKind->name ?? "" }}</td>
                                            <td>{{ $history->status }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

@endpush