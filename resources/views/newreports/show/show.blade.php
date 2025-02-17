@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2') 

    <div id="encoded-datajenisdokumen" data-listjenisdokumen="{{ $jenisdokumen }}"></div>
<div id="encoded-data" data-listprogressnodokumen="{{ $listprogressnodokumenencode }}"></div>
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('newreports.show', ['newreport' => $newreport->id]) }}">List Dokumen</a></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

@endsection

@section('container3')  
<div class="row justify-content-center">



    <div class="col-10">
        <div class="card card-danger card-outline">





            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab"
                        aria-controls="progress" aria-selected="true">Progress</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="laporan-tanggal-tab" data-toggle="tab" href="#laporan-tanggal" role="tab"
                        aria-controls="laporan-tanggal" aria-selected="false">Laporan Tanggal</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="member-tab" data-toggle="tab" href="#member" role="tab"
                        aria-controls="member" aria-selected="false">Pembagian Tugas</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="duplicate-tab" data-toggle="tab" href="#duplicate" role="tab"
                        aria-controls="duplicate" aria-selected="false">Duplikat</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="Treediagram-tab" data-toggle="tab" href="#Treediagram" role="tab"
                        aria-controls="Treediagram" aria-selected="false">Treediagram</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"
                        aria-controls="history" aria-selected="false">History</a>
                </li>

            </ul>

            <div class="tab-content" id="myTabContent">
                <!-- Progress Tab Content -->
                <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <table class="table table-bordered my-2 table-responsive-">
                                        <tbody>
                                            <tr>
                                                <td rowspan="7" style="width: 25%" class="text-center">
                                                    <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo"
                                                        class="p-2" style="max-width: 250px">
                                                </td>
                                                <td rowspan="7" style="width: 50%">
                                                    <h1 class="text-xl text-center mt-2">DAFTAR DOKUMEN & GAMBAR</h1>
                                                </td>
                                                <td style="width: 25%" class="p-1">Project:
                                                    <b>{{ ucwords(str_replace('-', ' ', $newreport->proyek_type)) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Bagian:
                                                    <b>{{ ucfirst($newreport->unit) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Progres: <b><span
                                                            class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}"
                                                            style="font-size: 2rem;">{{ $newreport->nilaipersentase }}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    <button class="btn btn-danger" id="btn-unrelease">Dokumen
                                                        Unreleased: <b>
                                                            <span class="badge badge-danger"
                                                                style="font-size: 1.5rem;">{{$newreport->unrelease}}</span>
                                                        </b></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    <button class="btn btn-success" id="btn-release">Dokumen Released:
                                                        <b><span class="badge badge-success"
                                                                style="font-size: 1.5rem;">{{$newreport->release}}</span></b></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    <button class="btn btn-info" id="btn-total">Total Dokumen: <b><span
                                                                class="badge badge-info"
                                                                style="font-size: 1.5rem;">{{$progressReports->count()}}</span></b></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-header">
                                    <h3 class="card-title">Progres Dokumen</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>


                                <div class="card-header">
                                    <div class="row">
                                        <style>
                                            .btn-borderless {
                                                border: none;
                                            }
                                        </style>
                                        <div class="col-md-12 d-flex justify-content-start align-items-center">
                                            <a href="#" class="btn btn-primary mt-2 mr-2"
                                                onclick="tambahdata('{{ $newreport->id }}')">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-default bg-purple mt-2 mr-2"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            </form>
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-danger btn-sm btn-block mt-2"
                                                        onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                                </div>
                                            @endif
                                            @if ($useronly->rule == "superuser")

                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleReleaseMultipleItems()">Release yang dipilih</button>
                                                </div>

                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleUnreleaseMultipleItems()">Unrelease yang
                                                        dipilih</button>
                                                </div>

                                            @endif
                                            @if(session('internalon'))
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless d-none"></button>
                                            @else
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless d-none mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless"></button>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="card-body">
                                    @php
                                        $penghitung = 0;
                                    @endphp
                                    <div id="default-table">
                                        @component('newreports.show.componentstable', [
    'newreport_id' => $newreport_id,
    'id' => 'example2',
    'useronly' => $useronly,
    'newreport' => $newreport,
    'listanggota' => $listanggota,
    'penghitung' => $penghitung,
    'documentNoHeader' => 'No Dokumen All',
    'documentNameHeader' => 'Nama Dokumen All',
    'progressReports' => $progressReports,
    'checklist' => 'checkAll',
    'name' => 'document_ids[]',
    'jenisdokumen' => $jenisdokumen,
])
                                        @endcomponent
                                    </div>

                                    <div id="table-release" class="d-none">
                                        @component('newreports.show.componentstable', [
    'newreport_id' => $newreport_id,
    'id' => 'example2-release',
    'useronly' => $useronly,
    'newreport' => $newreport,
    'listanggota' => $listanggota,
    'penghitung' => $penghitung,
    'documentNoHeader' => 'No Dokumen Release',
    'documentNameHeader' => 'Nama Dokumen Release',
    'progressReports' => $revisiall['RELEASED']['progressReports'],
    'checklist' => 'checkAllrelease',
    'name' => 'document_ids_release[]',
    'jenisdokumen' => $jenisdokumen,
])
                                        @endcomponent
                                    </div>

                                    <div id="table-unrelease" class="d-none">
                                        @component('newreports.show.componentstable', [
    'newreport_id' => $newreport_id,
    'id' => 'example2-unrelease',
    'useronly' => $useronly,
    'newreport' => $newreport,
    'listanggota' => $listanggota,
    'penghitung' => $penghitung,
    'documentNoHeader' => 'No Dokumen Unrelease',
    'documentNameHeader' => 'Nama Dokumen Unrelease',
    'progressReports' => $revisiall['UNRELEASED']['progressReports'],
    'checklist' => 'checkAllunrelease',
    'name' => 'document_ids_unrelease[]',
    'jenisdokumen' => $jenisdokumen,
])
                                        @endcomponent
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Laporan Tanggal Tab Content -->
                <div class="tab-pane fade" id="laporan-tanggal" role="tabpanel" aria-labelledby="laporan-tanggal-tab">
                    <!-- Laporan Tanggal content goes here -->
                    <!-- Progress content goes here -->

                    <div class="card card-outline card-danger">
                        <div class="card-header">

                            <p><strong>Nama Unit:</strong> {{ $newreport->unit }}</p>
                            <p><strong>Proyek:</strong> {{ $newreport->proyek_type }}</p>
                            <p><strong>Tingkat Penyelesaian:</strong> {{ number_format($progresspercentage, 2) }} %</p>
                            <p><strong>Total Dokumen:</strong> {{ count($progressReports) }}</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card card-danger">

                                        <div class="card-header">
                                            <h3 class="card-title">Progress Status</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <form
                                                action="{{ route('newreports.download', ['newreport' => $newreport->id]) }}"
                                                method="POST">
                                                @csrf
                                                <div class="form-row align-items-center">
                                                    <div class="col-auto">
                                                        <label for="start_date" class="sr-only">Start Date</label>
                                                        <input type="date" id="start_date" name="start_date"
                                                            class="form-control mb-2" required>
                                                    </div>
                                                    <div class="col-auto">
                                                        <label for="end_date" class="sr-only">End Date</label>
                                                        <input type="date" id="end_date" name="end_date"
                                                            class="form-control mb-2" required>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="submit" class="btn btn-primary mb-2">Download
                                                            Report</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <table id="example3" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th>Week</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Total Revisions (Plan)</th>
                                                        <th>Total Revisions (Realisasi)</th>
                                                        <th>Total Presentase (Plan)</th>
                                                        <th>Total Presentase (Realisasi)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data as $week => $item): ?>
                                                    <tr>
                                                        <td>{{ $penghitung++ }}</td>
                                                        <td><?php    echo $week; ?></td>
                                                        <td><?php    echo $data[$week]['start']; ?></td>
                                                        <td><?php    echo $data[$week]['end']; ?></td>

                                                        <td><?php    echo $weekData[$week]['value']; ?></td>
                                                        <td><?php    echo $data[$week]['nilai']; ?></td>
                                                        <td>{{number_format($weekData[$week]['percentage'], 2)}} %</td>
                                                        <td>{{number_format($data[$week]['nilaipresentase'], 2)}} %</td>


                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card card-danger">
                                        <div class="card-header">
                                            <h3 class="card-title">Progress Status</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="sCurveChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <script>
                        // Prepare data for S Curve
                        var labels = {!! json_encode(array_keys($data)) !!};
                        var plannedData = {!! json_encode(array_column($weekData, 'percentage')) !!};
                        var actualData = {!! json_encode(array_column($data, 'nilaipresentase')) !!};

                        var ctx = document.getElementById('sCurveChart').getContext('2d');
                        var sCurveChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Planned',
                                        data: plannedData,
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 2,
                                        fill: false
                                    },
                                    {
                                        label: 'Actual',
                                        data: actualData,
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 2,
                                        fill: false
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                title: {
                                    display: true,
                                    text: 'S Curve of Project Revisions'
                                },
                                scales: {
                                    x: {
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Week'
                                        }
                                    },
                                    y: {
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Percentage'
                                        },
                                        min: 0,
                                        max: 100
                                    }
                                }
                            }
                        });
                    </script>

                </div>

                <div class="tab-pane fade" id="member" role="tabpanel" aria-labelledby="member-tab">

                    <div class="card card-outline card-danger">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                @foreach ($datastatus as $keyan => $revisi)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif"
                                            id="custom-tabs-one-{{ $keyan }}-tab" data-toggle="pill"
                                            href="#custom-tabs-one-{{ $keyan }}" role="tab"
                                            aria-controls="custom-tabs-one-{{ $keyan }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $keyan }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                @foreach ($datastatus as $keyan => $revisi)
                                    <div class="tab-pane fade @if($loop->first) show active @endif"
                                        id="custom-tabs-one-{{ $keyan }}" role="tabpanel"
                                        aria-labelledby="custom-tabs-one-{{ $keyan }}-tab">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card card-danger">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Progress Level -
                                                            {{ str_replace('_', ' ', $keyan) }}
                                                        </h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool"
                                                                data-card-widget="collapse">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-tool"
                                                                data-card-widget="remove">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="canvas-level-detailed-{{ $keyan }}"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card card-danger">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Progress Status -
                                                            {{ str_replace('_', ' ', $keyan) }}
                                                        </h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool"
                                                                data-card-widget="collapse">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-tool"
                                                                data-card-widget="remove">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="canvas-status-detailed-{{ $keyan }}"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="duplicate" role="tabpanel" aria-labelledby="duplicate-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <table class="table table-bordered my-2 table-responsive-">
                                        <tbody>
                                            <tr>
                                                <td rowspan="7" style="width: 25%" class="text-center">
                                                    <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo"
                                                        class="p-2" style="max-width: 250px">
                                                </td>
                                                <td rowspan="7" style="width: 50%">
                                                    <h1 class="text-xl text-center mt-2">DAFTAR DOKUMEN & GAMBAR</h1>
                                                </td>
                                                <td style="width: 25%" class="p-1">Project:
                                                    <b>{{ ucwords(str_replace('-', ' ', $newreport->proyek_type)) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Bagian:
                                                    <b>{{ ucfirst($newreport->unit) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Progres: <b><span
                                                            class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}"
                                                            style="font-size: 2rem;">{{ $newreport->nilaipersentase }}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Dokumen Unreleased: <b>
                                                        <span class="badge badge-danger"
                                                            style="font-size: 1.5rem;">{{$newreport->unrelease}}</span>
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Dokumen Released: <b><span class="badge badge-success"
                                                            style="font-size: 1.5rem;">{{$newreport->release}}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Total Dokumen: <b><span class="badge badge-info"
                                                            style="font-size: 1.5rem;">{{$progressReports->count()}}</span></b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-header">
                                    <h3 class="card-title">Progres Dokumen</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <div class="row">
                                        <style>
                                            .btn-borderless {
                                                border: none;
                                            }
                                        </style>
                                        <div class="col-md-12 d-flex justify-content-start align-items-center">
                                            <a href="#" class="btn btn-primary mt-2 mr-2"
                                                onclick="tambahdata('{{ $newreport->id }}')">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-default bg-purple mt-2 mr-2"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            </form>
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-danger btn-sm btn-block mt-2"
                                                        onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                                </div>
                                            @endif
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleReleaseMultipleItems()">Release yang dipilih</button>
                                                </div>
                                            @endif
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleUnreleaseMultipleItems()">Unrelease yang
                                                        dipilih</button>
                                                </div>
                                            @endif
                                            @if(session('internalon'))
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless d-none"></button>
                                            @else
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless d-none mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless"></button>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    <table id="example4" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <span class="checkbox-toggle" id="checkAll"><i
                                                            class="far fa-square"></i></span>
                                                </th>
                                                <th scope="col">No</th>
                                                <th scope="col">No Dokumen</th>
                                                <th scope="col">Nama Dokumen</th>
                                                <th scope="col">Rev</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Dokumen Pendukung</th>
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $penghitung = 1; // Inisialisasi penghitung
                                                $sortedReports = $progressReports->sortBy('nodokumen'); // Sort the collection by nodokumen
                                            @endphp

                                            @foreach ($sortedReports as $index => $progressReport)
                                                                                    @if (in_array(trim($progressReport->nodokumen), $duplicates))
                                                                                                                            <tr>
                                                                                                                                <td>
                                                                                                                                    <div class="icheck-primary">
                                                                                                                                        <input type="checkbox" value="{{ $progressReport->id }}"
                                                                                                                                            name="document_ids[]"
                                                                                                                                            id="checkbox{{ $progressReport->id }}">
                                                                                                                                        <label for="checkbox{{ $progressReport->id }}"></label>
                                                                                                                                    </div>
                                                                                                                                </td>
                                                                                                                                <td>{{ $penghitung }}</td>
                                                                                                                                <td id="nodokumen_{{ $progressReport->id }}_{{ $index }}">
                                                                                                                                    {{ $progressReport->nodokumen }}
                                                                                                                                </td>
                                                                                                                                <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">
                                                                                                                                    {{ $progressReport->namadokumen }}
                                                                                                                                </td>
                                                                                                                                <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">
                                                                                                                                    {{ $progressReport->rev }}
                                                                                                                                </td>



                                                                                                                                <td id="status_{{ $progressReport->id }}_{{ $index }}">
                                                                                                                                    {{ $progressReport->statusterbaru }}
                                                                                                                                </td>
                                                                                                                                <td id="supportdocument_{{ $progressReport->id }}">
                                                                                                                                    @if($progressReport->children->count() > 0)
                                                                                                                                        @foreach ($progressReport->children as $anak)
                                                                                                                                            <div class="badge badge-combined">
                                                                                                                                                <span class="badge-section badge-danger">
                                                                                                                                                    {{ $anak->namadokumen ?? "" }}
                                                                                                                                                </span>
                                                                                                                                                <span class="badge-section badge-primary">
                                                                                                                                                    {{ $anak->nodokumen ?? "" }}
                                                                                                                                                </span>
                                                                                                                                                <span class="badge-section badge-success">
                                                                                                                                                    {{ $anak->status ?? "" }}
                                                                                                                                                </span>
                                                                                                                                                <a href="#" class="badge-section badge-info"
                                                                                                                                                    onclick="unlink('{{ $anak->id }}')">
                                                                                                                                                    <i class="fas fa-eraser"></i> Unlink
                                                                                                                                                </a>
                                                                                                                                            </div>
                                                                                                                                        @endforeach
                                                                                                                                    @else
                                                                                                                                        <span class="badge badge-warning">Tidak ada dokumen pendukung</span>
                                                                                                                                    @endif
                                                                                                                                </td>

                                                                                                                                @php
                                                                                                                                    $hasilwaktu = json_decode($progressReport->temporystatus, true);
                                                                                                                                @endphp
                                                                                                                                <td>
                                                                                                                                    <a href="#" class="btn btn-default bg-maroon d-block mb-1"
                                                                                                                                        onclick="opendeleteForm('{{ $progressReport->id }}', '{{ $index }}')">
                                                                                                                                        <i class="fas fa-eraser"></i> Delete
                                                                                                                                    </a>
                                                                                                                                    <a href="#" class="btn btn-info btn-sm d-block mb-1"
                                                                                                                                        onclick="showDocumentSummaryduplicate('{{ json_encode($progressReport) }}', '{{ $progressReport->id }}', '{{ $index }}', '{{ json_encode($listanggota) }}', '{{ $useronly->rule }}')">
                                                                                                                                        <i class="fas fa-edit"></i> Edit
                                                                                                                                    </a>

                                                                                                                                </td>
                                                                                                                            </tr>
                                                                                                                            @php
                                                                                                                                $penghitung++;
                                                                                                                            @endphp
                                                                                    @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="Treediagram" role="tabpanel" aria-labelledby="Treediagram-tab">

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <table class="table table-bordered my-2 table-responsive-">
                                        <tbody>
                                            <tr>
                                                <td rowspan="7" style="width: 25%" class="text-center">
                                                    <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo"
                                                        class="p-2" style="max-width: 250px">
                                                </td>
                                                <td rowspan="7" style="width: 50%">
                                                    <h1 class="text-xl text-center mt-2">DAFTAR DOKUMEN & GAMBAR</h1>
                                                </td>
                                                <td style="width: 25%" class="p-1">Project:
                                                    <b>{{ ucwords(str_replace('-', ' ', $newreport->proyek_type)) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Bagian:
                                                    <b>{{ ucfirst($newreport->unit) }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b>
                                                </td>
                                            </tr>
                                            <tr>


                                                <td style="width: 25%" class="p-1">
                                                    Progres: <b><span
                                                            class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}"
                                                            style="font-size: 2rem;">{{ $newreport->nilaipersentase }}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Dokumen Unreleased: <b>
                                                        <span class="badge badge-danger"
                                                            style="font-size: 1.5rem;">{{$newreport->unrelease}}</span>
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Dokumen Released: <b><span class="badge badge-success"
                                                            style="font-size: 1.5rem;">{{$newreport->release}}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%" class="p-1">
                                                    Total Dokumen: <b><span class="badge badge-info"
                                                            style="font-size: 1.5rem;">{{$progressReports->count()}}</span></b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-header">
                                    <h3 class="card-title">Progres Dokumen</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <div class="row">
                                        <style>
                                            .btn-borderless {
                                                border: none;
                                            }
                                        </style>
                                        <div class="col-md-12 d-flex justify-content-start align-items-center">
                                            <a href="#" class="btn btn-primary mt-2 mr-2"
                                                onclick="tambahdata('{{ $newreport->id }}')">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-default bg-purple mt-2 mr-2"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            </form>
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-danger btn-sm btn-block mt-2"
                                                        onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                                </div>
                                            @endif
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleReleaseMultipleItems()">Release yang dipilih</button>
                                                </div>
                                            @endif
                                            @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <button type="button" class="btn btn-info btn-sm btn-block mt-2"
                                                        onclick="handleUnreleaseMultipleItems()">Unrelease yang
                                                        dipilih</button>
                                                </div>
                                            @endif
                                            @if(session('internalon'))
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless d-none"></button>
                                            @else
                                                <button id="internalOffButton"
                                                    class="btn btn-success mt-2 btn-borderless d-none mr-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                </button>
                                                <button id="internalButton"
                                                    class="btn btn-default bg-white mt-2 btn-borderless"></button>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    <table id="example5" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">Expand</th>
                                                <th scope="col">No</th>
                                                <th scope="col">No Dokumen</th>
                                                <th scope="col">Nama Dokumen</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $penghitung = 1; // Inisialisasi penghitung
                                                $sortedReports = $progressReports->sortBy('nodokumen'); // Sort the collection by nodokumen
                                            @endphp
                                            @foreach ($sortedReports as $index => $progressReport)
                                                @if ($progressReport->parent_revision_id == null)
                                                    <tr>
                                                        <td>
                                                            @if ($generasi[$progressReport->id]['count'] > 0)
                                                                <button class="btn btn-primary toggle-children"
                                                                    data-id="{{ $progressReport->id }}">+</button>
                                                            @else
                                                                <button class="btn btn-secondary" disabled>[]</button>
                                                            @endif
                                                        </td>
                                                        <td>{{ $penghitung++ }}</td>
                                                        <td>{{ $progressReport->nodokumen }}</td>
                                                        <td>{{ $progressReport->namadokumen }}</td>
                                                        <td>{{ $progressReport->status }}</td>
                                                    </tr>
                                                    @if ($generasi[$progressReport->id]['count'] > 0)
                                                        <tr class="child-rows" data-parent-id="{{ $progressReport->id }}"
                                                            style="display: none;">
                                                            <td colspan="5">
                                                                @include('newreports.child', ['progressReports' => $generasi[$progressReport->id]['childreen'], 'generasi' => $generasi])
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        document.querySelectorAll('.toggle-children').forEach(function (button) {
                                            button.addEventListener('click', function () {
                                                var parentId = this.getAttribute('data-id');
                                                var childRows = document.querySelectorAll('.child-rows[data-parent-id="' + parentId + '"]');
                                                childRows.forEach(function (row) {
                                                    row.style.display = row.style.display === 'none' ? '' : 'none';
                                                });
                                                this.textContent = this.textContent === '+' ? '-' : '+';
                                            });
                                        });
                                    });
                                </script>




                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">History</div>


                                <div class="card-body">
                                    @if($newreport->systemLogs && $newreport->systemLogs->isEmpty())
                                        <p>No history available for project {{ $newreport->id }}.</p>
                                    @else
                                                                    <table id="example6" class="table table-bordered table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">No</th>
                                                                                <th scope="col">Status</th>
                                                                                <th scope="col">Nama Uploader</th>
                                                                                <th scope="col">Waktu Upload</th>
                                                                                <th scope="col">Status</th>
                                                                                <th scope="col">Persentase Terakhir External</th>
                                                                                <th scope="col">Persentase Terakhir Internal</th>
                                                                                <th scope="col">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($newreport->systemLogs as $riwayat)
                                                                                                                    <tr>
                                                                                                                        <td>{{ $penghitung++ }}</td>
                                                                                                                        <td>{{ $riwayat->level }}</td>
                                                                                                                        <td>{{ $riwayat->user }}</td>
                                                                                                                        <td>{{ $riwayat->created_at->format('d/m/Y H:i') }}</td>
                                                                                                                        <td>{{ $riwayat->aksi }}</td>

                                                                                                                        <td class="project-actions text-left">
                                                                                                                            @php
                                                                                                                                $message = json_decode($riwayat->message, true);
                                                                                                                            @endphp
                                                                                                                            @if(isset($message['persentase']) && is_array($message['persentase']))
                                                                                                                                @foreach($message['persentase'] as $key => $value)

                                                                                                                                    <div class="col-md-12 text-left column-layout">
                                                                                                                                        <div class="badge badge-combined">
                                                                                                                                            <span class="badge-section badge-danger">
                                                                                                                                                {{ $key ?? "" }}
                                                                                                                                            </span>
                                                                                                                                            <span class="badge-section badge-primary">
                                                                                                                                                {{ $value ?? "" }}
                                                                                                                                            </span>
                                                                                                                                        </div>
                                                                                                                                    </div>


                                                                                                                                @endforeach
                                                                                                                            @else
                                                                                                                                N/A
                                                                                                                            @endif
                                                                                                                        </td>
                                                                                                                        <td class="project-actions text-left">
                                                                                                                            @php
                                                                                                                                $message = json_decode($riwayat->message, true);
                                                                                                                            @endphp
                                                                                                                            @if(isset($message['persentase_internal']) && is_array($message['persentase_internal']))
                                                                                                                                @foreach($message['persentase_internal'] as $key => $value)

                                                                                                                                    <div class="col-md-12 text-left column-layout">
                                                                                                                                        <div class="badge badge-combined">
                                                                                                                                            <span class="badge-section badge-danger">
                                                                                                                                                {{ $key ?? "" }}
                                                                                                                                            </span>
                                                                                                                                            <span class="badge-section badge-primary">
                                                                                                                                                {{ $value ?? "" }}
                                                                                                                                            </span>
                                                                                                                                        </div>
                                                                                                                                    </div>


                                                                                                                                @endforeach
                                                                                                                            @else
                                                                                                                                N/A
                                                                                                                            @endif
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            @if($riwayat->aksi == 'progressaddition')
                                                                                                                                <a href="{{ route('newreports.showlog', ['newreport' => $newreport->id, 'logid' => $riwayat->id]) }}"
                                                                                                                                    class="btn btn-primary">View Log</a>

                                                                                                                            @endif
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                            @endforeach
                                                                        </tbody>

                                                                    </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>


        </div>
    </div>

</div>
@endsection

@push('css') 

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables & Plugins -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tambahkan ini ke dalam <head> di file HTML Anda -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
    <!-- Sweetalert2 (include theme bootstrap) -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Donut Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>
    <style>
        /* Tambahkan ini ke dalam file CSS utama proyek Anda */
        .d-none {
            display: none !important;
        }
    </style>
    <style>
        .table-hover tbody tr.checked {
            background-color: #f0f8ff;
            /* Warna biru muda */
        }

        .table-hover tbody tr.checked td {
            color: #333;
            /* Warna teks untuk kontras */
        }
    </style>
@endpush

@push('scripts')

    <!-- jQuery -->
    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables & Plugins -->
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
    <script src="https://code.y.com/jquery-3.6.0.min.js" integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I="
        crossorigin="anonymous"></script>
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>

    <script>
        $(document).ready(function () {
            $('.select-documentkind').change(function () {
                var documentKindId = $(this).val();
                var progressReportId = $(this).data('id');
                var index = $(this).data('index');
                var token = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ route('newprogressreports.updateDocumentKind') }}",
                    type: "POST",
                    data: {
                        _token: token,
                        documentkind_id: documentKindId,
                        progressreport_id: progressReportId
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            // Jika berhasil, update tampilan untuk user non-MTPR
                            $('#documentkind_' + progressReportId + '_' + index).text(response.documentkind_name);
                        } else {
                            alert(response.documentkind_name);
                        }
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON.message);
                    }
                });
            });
        });

    </script>



    <script>
        document.getElementById('btn-unrelease').addEventListener('click', function () {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.remove('d-none');
        });

        document.getElementById('btn-release').addEventListener('click', function () {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.remove('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });

        document.getElementById('btn-total').addEventListener('click', function () {
            document.getElementById('default-table').classList.remove('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });
    </script>


    <script>
        // Fungsi untuk menangani perubahan status checkbox
        function handleCheckboxChange(checkbox) {
            // Dapatkan baris tabel terkait dengan checkbox
            var row = checkbox.closest('tr');

            // Periksa apakah checkbox dicentang atau tidak
            if (checkbox.checked) {
                // Jika dicentang, tambahkan kelas 'checked' pada baris tabel
                row.classList.add('checked');
            } else {
                // Jika tidak dicentang, hapus kelas 'checked' dari baris tabel
                row.classList.remove('checked');
            }
        }
    </script>

    <script>
        document.getElementById('internalButton').addEventListener('click', function () {

            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (password === '12345') {
                        // Save the status to the session
                        return $.ajax({
                            url: '{{ route("set.internalon") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function () {
                                location.reload();
                                // Reveal the hidden elements
                                document.querySelectorAll('.badge-warning.d-none').forEach(element => {
                                    element.classList.remove('d-none');
                                });
                                document.querySelectorAll('.badge-success.d-1').forEach(element => {
                                    element.classList.add('d-none');
                                });
                            },
                            error: function () {
                                Swal.showValidationMessage('Failed to set session');
                            }
                        });
                    } else {
                        Swal.showValidationMessage('Incorrect password');
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password correct, internal details revealed.',
                        icon: 'success'
                    });
                }
            });
        });

        document.getElementById('internalOffButton').addEventListener('click', function () {
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to turn off internal details?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, turn off',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Save the status to the session
                    return $.ajax({
                        url: '{{ route("set.internaloff") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function () {
                            location.reload();
                            // Reveal the hidden elements
                            document.querySelectorAll('.badge-warning').forEach(element => {
                                element.classList.add('d-none');
                            });
                            document.querySelectorAll('.badge-success.d-1.d-none').forEach(element => {
                                element.classList.remove('d-none');
                            });
                        },
                        error: function () {
                            Swal.showValidationMessage('Failed to set session');
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Internal details turned off.',
                        icon: 'success'
                    });
                }
            });
        });

    </script>

    <script>
        // Object to store intervals
        var intervals = {};

        // Function to update elapsed time
        function updateElapsedTime1(id, startTime, initialSeconds) {
            var elapsedTimeElement = document.getElementById('elapsed_time_' + id);

            // Function to format elapsed time
            function formatElapsedTime(seconds) {
                var hours = Math.floor(seconds / 3600);
                var minutes = Math.floor((seconds % 3600) / 60);
                var remainingSeconds = seconds % 60;
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
            }

            // Function to calculate elapsed time
            function calculateElapsedTime() {
                var now = new Date();
                var start = new Date(startTime);
                var elapsed = Math.floor((now - start) / 1000) + initialSeconds;
                return elapsed;
            }

            // Update elapsed time element
            function updateElapsedTime() {
                var elapsedSeconds = calculateElapsedTime();
                elapsedTimeElement.textContent = formatElapsedTime(elapsedSeconds);
            }

            // Initial update and log the initial state
            updateElapsedTime();
            console.log(`Initial update for id ${id}:`, elapsedTimeElement.textContent);

            // Clear existing interval if it exists
            if (intervals[id]) {
                clearInterval(intervals[id]);
            }

            // Update elapsed time periodically and store the interval
            intervals[id] = setInterval(function () {
                updateElapsedTime();
                console.log(`Updated time for id ${id}:`, elapsedTimeElement.textContent);
            }, 1000);
        }

        // Event listener when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($progressReports as $item)
                    @php
                        $id = $item->id;
                        $temporystatus = json_decode($item->temporystatus, true) ?? [];
                        $elapsedSeconds = $temporystatus['total_elapsed_seconds'] ?? 0;
                        $startTime = $temporystatus['start_time'] ?? null;
                        $pauseTime = $temporystatus['pause_time'] ?? null;
                    @endphp
                    @if($startTime != null && $pauseTime == null)
                        var elapsedTimeElement = document.getElementById('elapsed_time_{{ $id }}');
                        var kondisional = elapsedTimeElement ? elapsedTimeElement.textContent : '';
                        if (kondisional !== "Paused" && kondisional !== "Completed" && kondisional !== "Time up tidak berjalan") {
                            updateElapsedTime1('{{ $id }}', '{{ $startTime }}', {{ $elapsedSeconds }});
                        }
                    @endif
            @endforeach
        });
    </script>

    <script>
        function picktugas(id, posisitable, name) {
            var picktugasUrl = `/newprogressreports/picktugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengambil pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ambil job ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Sending AJAX request to:", picktugasUrl);
                    $.ajax({
                        url: picktugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            console.log("Server response:", response);
                            $(`#drafter_${id}_${posisitable}`).text(name);
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil didapat!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-rocket"></i> Start Tugas')
                                .removeClass('btn-success')
                                .addClass('btn-warning');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengambil pekerjaan ini.',
                            });
                        }
                    });
                }
            });
        }

        function starttugas(id, posisitable, name) {
            var starttugasUrl = `/newprogressreports/starttugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin memulai pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, mulai job ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: starttugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil dimulai!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `pausetugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-pause-circle"></i> Pause')
                                .removeClass('btn-warning')
                                .addClass('btn-secondary');
                            var startTime = new Date().toISOString();
                            var elapsedSeconds = response.elapsedSeconds || 0;
                            updateElapsedTime1(id, startTime, elapsedSeconds);

                            $(`#selesai_button_${id}_${posisitable}`).removeClass('d-none');
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function pausetugas(id, posisitable, name) {
            var pausetugasUrl = `/newprogressreports/pausetugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menjeda pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, jeda pekerjaan ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: pausetugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil dijeda!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `resumetugas('${id}','${posisitable}', '${name}')`)
                                .html('<i class="fas fa-play-circle"></i> Resume')
                                .removeClass('btn-secondary')
                                .addClass('btn-primary');

                            var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                            elapsedTimeElement.textContent = "Paused";

                            // Clear the interval for this task
                            if (intervals[id]) {
                                clearInterval(intervals[id]);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function resumetugas(id, posisitable, name) {
            var resumetugasUrl = `/newprogressreports/resumetugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melanjutkan pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan pekerjaan ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: resumetugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil dilanjutkan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `pausetugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-pause-circle"></i> Pause')
                                .removeClass('btn-primary')
                                .addClass('btn-secondary');

                            var startTime = response.startTime;
                            var elapsedSeconds = response.elapsedSeconds;
                            updateElapsedTime1(id, startTime, elapsedSeconds);
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function selesaitugas(id, posisitable, name) {
            var selesaitugasUrl = `/newprogressreports/selesaitugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyelesaikan pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33', confirmButtonText: 'Ya, selesaikan pekerjaan ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: selesaitugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil diselesaikan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`).remove();
                            var revisionElement = document.getElementById('revision_' + id);
                            if (revisionElement) {
                                revisionElement.textContent = response.lastKey || "update";
                            }
                            var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                            elapsedTimeElement.textContent = "Selesai";

                            // Clear the interval for this task
                            if (intervals[id]) {
                                clearInterval(intervals[id]);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function resettugas(id, posisitable, name) {
            var resetTugasUrl = `/newprogressreports/resettugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mereset tugas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, reset tugas ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: resetTugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tugas berhasil direset!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `picktugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-hand-pointer"></i> Pick Tugas')
                                .removeClass('btn-danger')
                                .addClass('btn-success');
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan',
                                text: 'Gagal mereset tugas. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        }

        function izinkanrevisitugas(id, posisitable, name) {
            var resetTugasUrl = `/newprogressreports/izinkanrevisitugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin merevisi tugas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, revisi dan buka tugas ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: resetTugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tugas berhasil direvisi dan terbuka!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Update the button to reflect the task can now be started
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-rocket"></i> Start Tugas')
                                .removeClass('btn-success')
                                .addClass('btn-warning');
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan',
                                text: 'Gagal mereset tugas. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        }


    </script>


    <script>
        function opendeleteForm(id, index) {
            var deleteUrl = `/newprogressreports/${id}/delete`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data ini? (Resiko Anak Dokumen Akan Terhapus kecuali anda lepas dulu sebagai dokumen pendukung)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            // Remove the entire row containing the deleted data
                            $(`#nodokumen_${id}_${index}`).closest('tr').remove();
                            $(`#namadokumen_${id}_${index}`).closest('tr').remove();
                            $(`#level_${id}_${index}`).closest('tr').remove();
                            $(`#drafter_${id}_${index}`).closest('tr').remove();
                            $(`#deadlinerelease_${id}_${index}`).closest('tr').remove();
                            $(`#realisasi_${id}_${index}`).closest('tr').remove();
                            $(`#status_${id}_${index}`).closest('tr').remove();

                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }


        function unlink(id) {
            var deleteUrl = `/newprogressreports/unlinkparent/${id}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melakukan unlink?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil diunlink!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function detailtugas(id) {
            var detailUrl = `/newprogressreports/${id}/detail`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melihat detail?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, detail!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = detailUrl;
                }
            });
        }

        function tambahdata(reportId) {
            // Mengambil data dari elemen div
            var listjenisdokumen = JSON.parse(document.getElementById("encoded-datajenisdokumen").getAttribute("data-listjenisdokumen"));
            var jenisdokumenOptions = '';

            listjenisdokumen.forEach(function (jenisdokumen) {
                jenisdokumenOptions += `<option value="${jenisdokumen.id}">${jenisdokumen.name}</option>`;
            });

            Swal.fire({
                title: "Tambah No Dokumen dan Nama Dokumen",
                html: `
                                                                                                                                                                                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="tambah-no-dokumen" style="flex: 1;">No Dokumen</label>
                                                                                                                                                                                                                    <input id="tambah-no-dokumen" class="swal2-input" placeholder="No Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="tambah-nama-dokumen" style="flex: 1;">Nama Dokumen</label>
                                                                                                                                                                                                                    <input id="tambah-nama-dokumen" class="swal2-input" placeholder="Nama Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="jenis-dokumen" style="flex: 1;">Jenis Dokumen</label>
                                                                                                                                                                                                                    <select id="jenis-dokumen" class="swal2-input" style="flex: 2;">
                                                                                                                                                                                                                        ${jenisdokumenOptions}
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                            `,
                focusConfirm: false,
                confirmButtonText: 'Tambah',
                preConfirm: () => {
                    return {
                        newreport_id: reportId,
                        nodokumen: document.getElementById("tambah-no-dokumen").value,
                        namadokumen: document.getElementById("tambah-nama-dokumen").value,
                        jenisdokumen: document.getElementById("jenis-dokumen").value,
                        _token: '{{ csrf_token() }}' // Token CSRF
                    };
                }
            }).then((formValues) => {
                if (formValues.value) {
                    $.ajax({
                        url: `/newreports/${reportId}/progressreports`,
                        method: 'POST',
                        data: formValues.value,
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan',
                                text: 'Gagal menambahkan data. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        }


        var encodedDataElement = document.getElementById('encoded-data');
        var listprogressnodokumenDecoded = JSON.parse(encodedDataElement.dataset.listprogressnodokumen);
        // Tambahkan tanda kutip ganda sebagai elemen pertama dalam array
        listprogressnodokumenDecoded.unshift('');

        function showDocumentSummary(item, id, index, listproject, user) {
            var listproject = JSON.parse(listproject);
            var listprogressnodokumen = listprogressnodokumenDecoded;
            var nodokumen = document.getElementById(`nodokumen_${id}_${index}`).innerText;
            var namadokumen = document.getElementById(`namadokumen_${id}_${index}`).innerText;
            var level = document.getElementById(`level_${id}_${index}`).innerText;
            var drafter = document.getElementById(`drafter_${id}_${index}`).innerText;
            var checker = document.getElementById(`checker_${id}_${index}`).innerText;
            var deadlinerelease = document.getElementById(`deadlinerelease_${id}_${index}`).innerText;
            var documentkind = document.getElementById(`documentkind_${id}_${index}`).innerText;
            var realisasi = document.getElementById(`realisasi_${id}_${index}`).innerText;
            var status = document.getElementById(`status_${id}_${index}`).innerText;

            function loadOptions(searchTerm, pageIndex, pageSize, list) {
                searchTerm = searchTerm.toLowerCase();
                var startIndex = pageIndex * pageSize;
                var endIndex = startIndex + pageSize;
                var filteredList = list.filter(function (item) {
                    return item.toLowerCase().includes(searchTerm);
                });
                var optionsHtml = '';
                for (var i = startIndex; i < endIndex && i < filteredList.length; i++) {
                    var listItem = filteredList[i];
                    optionsHtml += `<option value="${listItem}">${listItem}</option>`;
                }
                return optionsHtml;
            }

            var currentPageIndex = 0;
            var pageSize = 5;
            var drafterOptionsHtml = loadOptions('', currentPageIndex, pageSize, listproject);
            var checkerOptionsHtml = loadOptions('', currentPageIndex, pageSize, listproject);
            var progressnodokumenOptionsHtml = loadOptions('', currentPageIndex, pageSize, listprogressnodokumen);

            var html = `
                                                                                                                                                                                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-no-dokumen" style="flex: 1;">No Dokumen</label>
                                                                                                                                                                                                                    <input id="edit-no-dokumen" class="swal2-input" value="${nodokumen}" placeholder="No Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-nama-dokumen" style="flex: 1;">Nama Dokumen</label>
                                                                                                                                                                                                                    <input id="edit-nama-dokumen" class="swal2-input" value="${namadokumen}" placeholder="Nama Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-level" style="flex: 1;">Level</label>
                                                                                                                                                                                                                    <select id="edit-level" class="swal2-input" style="flex: 2;">
                                                                                                                                                                                                                        <option value="-" ${level === '-' ? 'selected' : ''}>-</option>
                                                                                                                                                                                                                        <option value="Predesign" ${level === 'Predesign' ? 'selected' : ''}>Predesign</option>
                                                                                                                                                                                                                        <option value="Intermediate Design" ${level === 'Intermediate Design' ? 'selected' : ''}>Intermediate Design</option>
                                                                                                                                                                                                                        <option value="Final Design" ${level === 'Final Design' ? 'selected' : ''}>Final Design</option>
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                ${(!drafter || drafter === "" || drafter === "-" || user === "MTPR" || user === "superuser" || user === "Senior Manager Engineering") ? `
                                                                                                                                                                                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                    <label for="edit-drafter">Drafter</label>
                                                                                                                                                                                                                    <select id="edit-drafter" class="swal2-input">
                                                                                                                                                                                                                        ${drafterOptionsHtml}
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                    <input type="text" id="drafter-search" class="swal2-input" placeholder="Search drafter...">
                                                                                                                                                                                                                    <div id="drafter-pagination" style="margin-top: 10px;">
                                                                                                                                                                                                                        <button id="prev-drafter-page">Previous</button>
                                                                                                                                                                                                                        <button id="next-drafter-page">Next</button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>` : ''}
                                                                                                                                                                                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                    <label for="edit-checker">Checker</label>
                                                                                                                                                                                                                    <select id="edit-checker" class="swal2-input">
                                                                                                                                                                                                                        ${checkerOptionsHtml}
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                    <input type="text" id="checker-search" class="swal2-input" placeholder="Search checker...">
                                                                                                                                                                                                                    <div id="checker-pagination" style="margin-top: 10px;">
                                                                                                                                                                                                                        <button id="prev-checker-page">Previous</button>
                                                                                                                                                                                                                        <button id="next-checker-page">Next</button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                    <label for="edit-progressnodokumen">Tambahkan Dokumen Turunanannya</label>
                                                                                                                                                                                                                    <select id="edit-progressnodokumen" class="swal2-input">
                                                                                                                                                                                                                        ${progressnodokumenOptionsHtml}
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                    <input type="text" id="progressnodokumen-search" class="swal2-input" placeholder="Search progress...">
                                                                                                                                                                                                                    <div id="progressnodokumen-pagination" style="margin-top: 10px;">
                                                                                                                                                                                                                        <button id="prev-progressnodokumen-page">Previous</button>
                                                                                                                                                                                                                        <button id="next-progressnodokumen-page">Next</button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-deadlinerelease" style="flex: 1;">Deadline Release</label>
                                                                                                                                                                                                                    <input id="edit-deadlinerelease" class="swal2-input" value="${deadlinerelease}" placeholder="Deadline Release" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-documentkind" style="flex: 1;">Jenis Dokumen</label>
                                                                                                                                                                                                                    <input id="edit-documentkind" class="swal2-input" value="${documentkind}" placeholder="Deadline Release" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                ${user === "MTPR" || user === "superuser" ? `
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-realisasi" style="flex: 1;">Realisasi</label>
                                                                                                                                                                                                                    <input id="edit-realisasi" class="swal2-input" value="${realisasi}" placeholder="Realisasi" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-status" style="flex: 1;">Status Dokumen</label>
                                                                                                                                                                                                                    <select id="edit-status" class="swal2-input" style="flex: 2;">
                                                                                                                                                                                                                        <option value="RELEASED" ${status === 'RELEASED' ? 'selected' : ''}>RELEASED</option>
                                                                                                                                                                                                                        <option value="Working Progress" ${status === 'Working Progress' ? 'selected' : ''}>Working Progress</option>
                                                                                                                                                                                                                        <option value="-" ${status === '-' ? 'selected' : ''}>-</option>
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                </div>` : ''}
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        `;

            function updateOptions(searchTerm, pageIndex, pageSize, list, targetSelect) {
                var optionsHtml = loadOptions(searchTerm, pageIndex, pageSize, list);
                targetSelect.innerHTML = optionsHtml;
            }

            Swal.fire({
                title: "Edit Dokumen",
                html: html,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return [
                        document.getElementById("edit-no-dokumen").value,
                        document.getElementById("edit-nama-dokumen").value,
                        document.getElementById("edit-level").value,
                        document.getElementById("edit-drafter").value,
                        document.getElementById("edit-checker").value,
                        document.getElementById("edit-progressnodokumen").value,
                        document.getElementById("edit-deadlinerelease").value,
                        document.getElementById("edit-documentkind").value,
                        ...(user === "MTPR" || user === "superuser" ? [
                            document.getElementById("edit-realisasi").value,
                            document.getElementById("edit-status").value
                        ] : [status])
                    ];
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var newNoDokumen = result.value[0];
                    var newNamaDokumen = result.value[1];
                    var newLevel = result.value[2];
                    var newDrafter = result.value[3];
                    var newChecker = result.value[4];
                    var newProgressnodokumen = result.value[5];
                    var newDeadlinerelease = result.value[6];
                    var newDocumentkind = result.value[7];
                    var newRealisasi = user === "MTPR" ? result.value[8] : realisasi;
                    var newStatus = user === "MTPR" ? result.value[9] : (deadlinerelease !== "" ? "RELEASED" : (status !== "" ? status : "Working Progress"));

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin memperbarui data ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, perbarui!',
                        cancelButtonText: 'Batal'
                    }).then((updateConfirmation) => {
                        if (updateConfirmation.isConfirmed) {
                            var updateUrl = `/newprogressreports/updateprogressreport/${id}/`;
                            console.log("Sending AJAX request to: ", updateUrl);
                            $.ajax({
                                url: updateUrl,
                                method: 'POST',
                                data: {
                                    nodokumen: newNoDokumen,
                                    namadokumen: newNamaDokumen,
                                    level: newLevel,
                                    drafter: newDrafter,
                                    checker: newChecker,
                                    progressnodokumen: newProgressnodokumen,
                                    //deadlinerelease: newDeadlinerelease,
                                    documentkind: newDocumentkind,
                                    realisasi: newRealisasi,
                                    status: newStatus,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    console.log("Update successful:", response);
                                    $(`#nodokumen_${id}_${index}`).text(newNoDokumen);
                                    $(`#namadokumen_${id}_${index}`).text(newNamaDokumen);
                                    $(`#level_${id}_${index}`).text(newLevel);
                                    $(`#drafter_${id}_${index}`).text(newDrafter);
                                    $(`#checker_${id}_${index}`).text(newChecker);
                                    $(`#deadlinerelease_${id}_${index}`).text(newDeadlinerelease);
                                    $(`#documentkind_${id}_${index}`).text(newDocumentkind);
                                    $(`#status_${id}_${index}`).text(newStatus);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data berhasil diperbarui!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error('Terjadi kesalahan:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan',
                                        text: 'Gagal memperbarui data. Silakan coba lagi.'
                                    });
                                },
                                complete: function () {
                                    console.log("AJAX request completed.");
                                }
                            });
                        }
                    });
                }
            });

            document.getElementById('drafter-search').addEventListener('input', function () {
                updateOptions(this.value, 0, pageSize, listproject, document.getElementById('edit-drafter'));
            });

            document.getElementById('checker-search').addEventListener('input', function () {
                updateOptions(this.value, 0, pageSize, listproject, document.getElementById('edit-checker'));
            });

            document.getElementById('progressnodokumen-search').addEventListener('input', function () {
                updateOptions(this.value, 0, pageSize, listprogressnodokumen, document.getElementById('edit-progressnodokumen'));
            });

            document.getElementById('prev-drafter-page').addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('drafter-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-drafter'));
                }
            });

            document.getElementById('next-drafter-page').addEventListener('click', function () {
                currentPageIndex++;
                updateOptions(document.getElementById('drafter-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-drafter'));
            });

            document.getElementById('prev-checker-page').addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('checker-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-checker'));
                }
            });

            document.getElementById('next-checker-page').addEventListener('click', function () {
                currentPageIndex++;
                updateOptions(document.getElementById('checker-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-checker'));
            });

            document.getElementById('prev-progressnodokumen-page').addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen, document.getElementById('edit-progressnodokumen'));
                }
            });

            document.getElementById('next-progressnodokumen-page').addEventListener('click', function () {
                currentPageIndex++;
                updateOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen, document.getElementById('edit-progressnodokumen'));
            });
        }


        function showDocumentSummaryduplicate(item, id, index) {
            var nodokumen = document.getElementById(`nodokumen_${id}_${index}`).innerText;
            var namadokumen = document.getElementById(`namadokumen_${id}_${index}`).innerText;
            var status = document.getElementById(`status_${id}_${index}`).innerText;

            var html = `
                                                                                                                                                                                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-no-dokumen" style="flex: 1;">No Dokumen</label>
                                                                                                                                                                                                                    <input id="edit-no-dokumen" class="swal2-input" value="${nodokumen}" placeholder="No Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-nama-dokumen" style="flex: 1;">Nama Dokumen</label>
                                                                                                                                                                                                                    <input id="edit-nama-dokumen" class="swal2-input" value="${namadokumen}" placeholder="Nama Dokumen" style="flex: 2;">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                                                                                                                                                                    <label for="edit-status" style="flex: 1;">Status</label>
                                                                                                                                                                                                                    <select id="edit-status" class="swal2-input" style="flex: 2;">
                                                                                                                                                                                                                        <option value="RELEASED" ${status === 'RELEASED' ? 'selected' : ''}>RELEASED</option>
                                                                                                                                                                                                                        <option value="Working Progress" ${status === 'Working Progress' ? 'selected' : ''}>Working Progress</option>
                                                                                                                                                                                                                        <option value="-" ${status === '-' ? 'selected' : ''}>-</option>
                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        `;

            Swal.fire({
                title: "Edit Dokumen",
                html: html,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return [
                        document.getElementById("edit-no-dokumen").value,
                        document.getElementById("edit-nama-dokumen").value,
                        document.getElementById("edit-status").value
                    ];
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var newNoDokumen = result.value[0];
                    var newNamaDokumen = result.value[1];
                    var newStatus = result.value[2];

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin memperbarui data ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, perbarui!',
                        cancelButtonText: 'Batal'
                    }).then((updateConfirmation) => {
                        if (updateConfirmation.isConfirmed) {
                            // Lakukan update data menggunakan AJAX
                            var updateUrl = `/newprogressreports/updateprogressreport/${id}/`; // Ganti dengan URL yang sesuai
                            console.log("Sending AJAX request to: ", updateUrl);
                            $.ajax({
                                url: updateUrl,
                                method: 'POST',
                                data: {
                                    nodokumen: newNoDokumen,
                                    namadokumen: newNamaDokumen,
                                    status: newStatus,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    console.log("Update successful:", response);
                                    $(`#nodokumen_${id}_${index}`).text(newNoDokumen);
                                    $(`#namadokumen_${id}_${index}`).text(newNamaDokumen);
                                    $(`#status_${id}_${index}`).text(newStatus);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data berhasil diperbarui!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error('Terjadi kesalahan:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan',
                                        text: 'Gagal memperbarui data. Silakan coba lagi.'
                                    });
                                },
                                complete: function () {
                                    console.log("AJAX request completed.");
                                }
                            });
                        }
                    });
                }
            });
        }

        // Event delegation for delete button
        $(document).on('click', '.btn-delete-multiple', function () {
            handleDeleteMultipleItems();
        });

        // Event delegation for release button
        $(document).on('click', '.btn-release-multiple', function () {
            handleReleaseMultipleItems();
        });

        // Event delegation for release button
        $(document).on('click', '.btn-unrelease-multiple', function () {
            handleUnreleaseMultipleItems();
        });


        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        function handleDeleteMultipleItems() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih? (Resiko Anak Dokumen Akan Terhapus kecuali anda lepas dulu sebagai dokumen pendukung)',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedDocumentIds = [];

                    // Iterasi untuk mengambil semua checkbox yang dipilih
                    var tableIds = ['example2', 'example2-release', 'example2-unrelease'];
                    var checkboxNames = ['document_ids[]', 'document_ids_release[]', 'document_ids_unrelease[]'];

                    tableIds.forEach(function (tableId, index) {
                        var table = $('#' + tableId).DataTable();
                        table.$(`input[name="${checkboxNames[index]}"]:checked`).each(function () {
                            selectedDocumentIds.push($(this).val());
                        });
                    });

                    if (selectedDocumentIds.length === 0) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Tidak ada item yang dipilih.',
                            icon: 'warning'
                        });
                        return;
                    }

                    // Kirim data ke server menggunakan AJAX
                    $.ajax({
                        url: '{{ route("newprogressreports.handleDeleteMultipleItems") }}',
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });

                            // Reload semua tabel setelah penghapusan sukses
                            tableIds.forEach(function (tableId) {
                                $('#' + tableId).DataTable().ajax.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus item yang dipilih. Silakan coba lagi.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }


        // function handleReleaseMultipleItems() {
        //     // Menampilkan SweetAlert konfirmasi
        //     Swal.fire({
        //         title: 'Konfirmasi',
        //         text: 'Anda yakin ingin mengubah statusnya menjadi RELEASE?',
        //         icon: 'question',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Ya, update!'
        //     }).then((result) => {
        //         // Jika pengguna mengonfirmasi penghapusan
        //         if (result.isConfirmed) {
        //             // Mengambil daftar ID dokumen yang dipilih dari semua halaman
        //             var table = $('#example2').DataTable();
        //             var selectedDocumentIds = [];
        //             table.$('input[name="document_ids[]"]:checked').each(function() {
        //                 selectedDocumentIds.push($(this).val());
        //             });

        //             // Melakukan panggilan AJAX untuk mengupdate item yang dipilih
        //             $.ajax({
        //                 url: '{{ route("newprogressreports.handleReleaseMultipleItems") }}',
        //                 type: "POST",
        //                 data: {
        //                     _token: '{{ csrf_token() }}',
        //                     document_ids: selectedDocumentIds
        //                 },
        //                 success: function(response) {
        //                     // Tampilkan pesan sukses
        //                     Swal.fire({
        //                         title: 'Berhasil!',
        //                         text: 'Item yang dipilih telah diupdate.',
        //                         icon: 'success'
        //                     });

        //                     // Refresh halaman setelah penghapusan
        //                     table.ajax.reload();
        //                 },
        //                 error: function(xhr, status, error) {
        //                     // Tampilkan pesan error
        //                     Swal.fire({
        //                         title: 'Gagal!',
        //                         text: 'Gagal mengupdate item yang dipilih.',
        //                         icon: 'error'
        //                     });
        //                 }
        //             });
        //         }
        //     });
        // }

        // function handleUnreleaseMultipleItems() {
        //     // Menampilkan SweetAlert konfirmasi
        //     Swal.fire({
        //         title: 'Konfirmasi',
        //         text: 'Anda yakin ingin mengubah statusnya menjadi RELEASE?',
        //         icon: 'question',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#3085d6',
        //         confirmButtonText: 'Ya, update!'
        //     }).then((result) => {
        //         // Jika pengguna mengonfirmasi penghapusan
        //         if (result.isConfirmed) {
        //             // Mengambil daftar ID dokumen yang dipilih dari semua halaman
        //             var table = $('#example2').DataTable();
        //             var selectedDocumentIds = [];
        //             table.$('input[name="document_ids[]"]:checked').each(function() {
        //                 selectedDocumentIds.push($(this).val());
        //             });

        //             // Melakukan panggilan AJAX untuk mengupdate item yang dipilih
        //             $.ajax({
        //                 url: '{{ route("newprogressreports.handleUnreleaseMultipleItems") }}',
        //                 type: "POST",
        //                 data: {
        //                     _token: '{{ csrf_token() }}',
        //                     document_ids: selectedDocumentIds
        //                 },
        //                 success: function(response) {
        //                     // Tampilkan pesan sukses
        //                     Swal.fire({
        //                         title: 'Berhasil!',
        //                         text: 'Item yang dipilih telah diupdate.',
        //                         icon: 'success'
        //                     });

        //                     // Refresh halaman setelah penghapusan
        //                     table.ajax.reload();
        //                 },
        //                 error: function(xhr, status, error) {
        //                     // Tampilkan pesan error
        //                     Swal.fire({
        //                         title: 'Gagal!',
        //                         text: 'Gagal mengupdate item yang dipilih.',
        //                         icon: 'error'
        //                     });
        //                 }
        //             });
        //         }
        //     });
        // }

    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            const CHART_COLORS = {
                red: 'rgb(255, 99, 132)',
                orange: 'rgb(255, 159, 64)',
                yellow: 'rgb(255, 205, 86)',
                green: 'rgb(75, 192, 192)',
                blue: 'rgb(54, 162, 235)',
                purple: 'rgb(153, 102, 255)',
                grey: 'rgb(201, 203, 207)'
            };

            @foreach ($datastatus as $keyan => $revisi)
                var levelData = {
                    labels: ['Predesign', 'Intermediate Design', 'Final Design', 'Belum Diidentifikasi'],
                    datasets: [
                        {
                            label: 'RELEASED',
                            data: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Predesign']['RELEASED'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Intermediate Design']['RELEASED'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Final Design']['RELEASED'] }},
                                {{ $percentageLevel[$keyan]['Belum Diidentifikasi']['RELEASED'] }}
                            ],
                            absoluteValues: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Predesign']['RELEASED'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Intermediate Design']['RELEASED'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Final Design']['RELEASED'] }},
                                {{ $datalevel[$keyan]['Belum Diidentifikasi']['RELEASED'] }}
                            ],
                            backgroundColor: CHART_COLORS.red,
                        },
                        {
                            label: 'Working Progress',
                            data: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Predesign']['Working Progress'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Intermediate Design']['Working Progress'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Final Design']['Working Progress'] }},
                                {{ $percentageLevel[$keyan]['Belum Diidentifikasi']['Working Progress'] }}
                            ],
                            absoluteValues: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Predesign']['Working Progress'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Intermediate Design']['Working Progress'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Final Design']['Working Progress'] }},
                                {{ $datalevel[$keyan]['Belum Diidentifikasi']['Working Progress'] }}
                            ],
                            backgroundColor: CHART_COLORS.blue,
                        },
                        {
                            label: 'Belum Dimulai',
                            data: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Predesign']['Belum Dimulai'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Intermediate Design']['Belum Dimulai'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $percentageLevel[$keyan]['Final Design']['Belum Dimulai'] }},
                                {{ $percentageLevel[$keyan]['Belum Diidentifikasi']['Belum Dimulai'] }}
                            ],
                            absoluteValues: [
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Predesign']['Belum Dimulai'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Intermediate Design']['Belum Dimulai'] }},
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $datalevel[$keyan]['Final Design']['Belum Dimulai'] }},
                                {{ $datalevel[$keyan]['Belum Diidentifikasi']['Belum Dimulai'] }}
                            ],
                            backgroundColor: CHART_COLORS.green,
                        }
                    ]
                };

                var levelOptions = {
                    plugins: {
                        title: {
                            display: true,
                            text: "Progress Level - {{ str_replace('_', ' ', $keyan) }}",
                            color: "#D6001C",
                            font: { family: "AvenirNextLTW01-Regular", size: 25, style: 'normal' }
                        },
                        datalabels: {
                            color: 'white',
                            font: { size: 12 },
                            formatter: function (value, context) {
                                var dataset = context.dataset;
                                var absoluteValue = dataset.absoluteValues[context.dataIndex];
                                var percentage = value.toFixed(2);
                                return `${absoluteValue} (${percentage}%)`;
                            },
                        },
                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            min: -15,
                            max: 115
                        }
                    }
                }

                var ctxLevel = document.getElementById("canvas-level-detailed-{{ $keyan }}").getContext("2d");
                window["myBarLevel{{ $keyan }}"] = new Chart(ctxLevel, {
                    plugins: [ChartDataLabels],
                    type: "bar",
                    data: levelData,
                    options: levelOptions
                });

                var statusData = {
                    labels: ['{{$datastatus[$keyan]['RELEASED']}} RELEASED', '{{$datastatus[$keyan]['Working Progress']}} Working Progress', '{{$datastatus[$keyan]["Belum Dimulai"]}} Belum Dimulai'],
                    datasets: [{
                        data: [{{ $percentageStatus[$keyan]['RELEASED'] }}, {{ $percentageStatus[$keyan]['Working Progress'] }}, {{ $percentageStatus[$keyan]["Belum Dimulai"] }}],
                        backgroundColor: ['#00a65a', '#f39c12', '#d2d6de'],
                        borderColor: '#fff'
                    }]
                };

                var statusOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'white',
                            font: { size: 12 },
                            formatter: function (value) {
                                return value.toFixed(2) + '%';
                            },
                        },
                        title: {
                            display: true,
                            text: "Progress Status - {{ str_replace('_', ' ', $keyan) }}",
                            color: "#D6001C",
                            font: { family: "AvenirNextLTW01-Regular", size: 25, style: 'normal' }
                        },
                        legend: {
                            display: true,
                            labels: {
                                font: { size: 16 },
                                generateLabels: function (chart) {
                                    var data = chart.data;
                                    return data.labels.map(function (label, i) {
                                        return {
                                            text: label + ' (' + data.datasets[0].data[i].toFixed(2) + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i]
                                        };
                                    });
                                }
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return value.toFixed(2) + '%';
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: true } },
                        y: { grid: { display: true, drawBorder: true } },
                    },
                    elements: { point: { radius: 0 } },
                };

                var ctxStatus = document.getElementById("canvas-status-detailed-{{ $keyan }}").getContext("2d");
                window["myDoughnutStatus{{ $keyan }}"] = new Chart(ctxStatus, {
                    plugins: [ChartDataLabels],
                    type: "doughnut",
                    data: statusData,
                    options: statusOptions
                });

            @endforeach
        });
    </script>


    <script>


        $.fn.dataTable.moment('DD-MM-YYYY'); // Tentukan format tanggal yang digunakan dalam tabel Anda

        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                {
                    "targets": 7, // Kolom yang berisi tanggal (Deadlines Release)
                    "type": 'date',
                    "render": function (data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return moment(data, 'DD-MM-YYYY').format('YYYY-MM-DD');
                        }
                        return data;
                    }
                }
            ]
        });
        $('#example2-release').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                {
                    "targets": 7, // Kolom yang berisi tanggal (Deadlines Release)
                    "type": 'date',
                    "render": function (data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return moment(data, 'DD-MM-YYYY').format('YYYY-MM-DD');
                        }
                        return data;
                    }
                }
            ]
        });
        $('#example2-unrelease').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                {
                    "targets": 7, // Kolom yang berisi tanggal (Deadlines Release)
                    "type": 'date',
                    "render": function (data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return moment(data, 'DD-MM-YYYY').format('YYYY-MM-DD');
                        }
                        return data;
                    }
                }
            ]
        });

    </script>

    <script>

        $(function () {
            $('#example3').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

    <script>

        $(function () {
            $('#example4').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
        $(function () {
            $('#example5').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>

    <script>
        $(function () {
            // Generic function to toggle checkboxes
            function toggleCheckbox(button, selector) {
                var clicks = $(button).data('clicks');
                if (clicks) {
                    // Uncheck all checkboxes
                    $(selector).prop('checked', false);
                    $(button).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    // Check first 10 checkboxes
                    $(selector + ':lt(10)').prop('checked', true);
                    $(button).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(button).data('clicks', !clicks);
            }

            // Bind checkAll
            $('#checkAll').click(function () {
                toggleCheckbox(this, 'input[name="document_ids[]"]');
            });

            // Bind checkAllrelease
            $('#checkAllrelease').click(function () {
                toggleCheckbox(this, 'input[name="document_ids_release[]"]');
            });

            // Bind checkAllunrelease
            $('#checkAllunrelease').click(function () {
                toggleCheckbox(this, 'input[name="document_ids_unrelease[]"]');
            });
        });
    </script>


    <script>

        function confirmDecision(formId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan mengambil keputusan ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Updated!",
                        text: "Your information has been uploaded.",
                        icon: "success"
                    });
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>



    <script>
        function enableEdit(id) {
            document.getElementById('documentNumberDisplay' + id).style.display = 'none';
            document.getElementById('editDocumentForm' + id).style.display = 'inline-block';
        }

        function cancelEdit(id) {
            document.getElementById('documentNumberDisplay' + id).style.display = 'inline';
            document.getElementById('editDocumentForm' + id).style.display = 'none';
        }

        function updateDocumentNumber(id, newreport_id, nodokumenlama) {
            const nodokumen = document.getElementById('nodokumen' + id).value;
            const saveButton = document.getElementById('saveButton' + id);
            const originalButtonText = saveButton.innerHTML;

            // Add loading spinner
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            saveButton.disabled = true;

            $.ajax({
                url: '/newreports/update-documentnumber',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nodokumen: nodokumen,
                    nodokumenlama: nodokumenlama,
                    newreport_id: newreport_id
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        });
                        document.getElementById('documentNumberDisplay' + id).innerText = nodokumen;
                        cancelEdit(id);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#d33'
                    });
                },
                complete: function () {
                    // Remove loading spinner and re-enable button
                    saveButton.innerHTML = originalButtonText;
                    saveButton.disabled = false;
                }
            });
        }




    </script>


@endpush