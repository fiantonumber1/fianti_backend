@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="my-4">Ranking Drafters (Mingguan)</h1>

            <!-- Form untuk memilih periode -->
            <form method="GET" action="{{ route('jobticket.rank') }}" class="mb-4">
                <label>Periode Mingguan:</label>
                <div class="input-group">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <label>Unit:</label>
                <div class="input-group mb-3">
                    <select name="unit" class="form-control">
                        <option value="">-- Semua Unit --</option>
                        @foreach($unit as $unitName)
                            <option value="{{ $unitName }}" {{ $selectedUnit === $unitName ? 'selected' : '' }}>
                                {{ $unitName }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-danger" type="submit">Tampilkan</button>
                    </div>
                </div>
            </form>

            <!-- Tombol untuk berpindah minggu -->
            <div class="input-group mt-2">
                <a href="{{ route('jobticket.rank', ['start_date' => \Carbon\Carbon::parse($startDate)->subWeek()->toDateString(), 'end_date' => \Carbon\Carbon::parse($endDate)->subWeek()->toDateString()]) }}"
                    class="btn btn-secondary">Minggu Sebelumnya</a>
                <a href="{{ route('jobticket.rank', ['start_date' => \Carbon\Carbon::parse($startDate)->addWeek()->toDateString(), 'end_date' => \Carbon\Carbon::parse($endDate)->addWeek()->toDateString()]) }}"
                    class="btn btn-secondary ml-2">Minggu Berikutnya</a>
            </div>

            <!-- Table untuk menampilkan ranking drafter -->
            <div class="card card-outline card-danger mt-2">

                <!-- Tabs for Drafter and Checker -->
                <ul class="nav nav-tabs" id="rankingTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="drafter-tab" data-toggle="tab" href="#drafter" role="tab"
                            aria-controls="drafter" aria-selected="true">Drafter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="checker-tab" data-toggle="tab" href="#checker" role="tab"
                            aria-controls="checker" aria-selected="false">Checker</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="unfinishedCheckerDocuments-tab" data-toggle="tab"
                            href="#unfinishedCheckerDocuments" role="tab" aria-controls="unfinishedCheckerDocuments"
                            aria-selected="false">Tanggungan Checker</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="unfinishedApproverDocuments-tab" data-toggle="tab"
                            href="#unfinishedApproverDocuments" role="tab" aria-controls="unfinishedApproverDocuments"
                            aria-selected="false">Tanggungan Approver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab"
                            aria-controls="report" aria-selected="false">Report</a>
                    </li>

                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="rankingTabContent">
                    <!-- Drafter Tab -->
                    <div class="tab-pane fade show active" id="drafter" role="tabpanel" aria-labelledby="drafter-tab">
                        <div class="card card-outline card-danger mt-2">
                            <div class="card-header">
                                <h3 class="card-title">Top Drafters (Closed)</h3>
                            </div>
                            <div class="card-header">
                                <h3>Daily Progress Drafter</h3>
                                <canvas id="dailyProgressChartDrafters"></canvas>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered table-hover mt-4">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Drafter</th>
                                            <th>Closed Count</th>
                                            <th>Cek Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rankedDrafters as $index => $drafter)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $drafter['drafter_name'] }}</td>

                                                <td>{{ $drafter['closed_count'] }}
                                                    @if($drafter['status'] === 'Meningkat')
                                                        <i class="fas fa-arrow-up text-success"></i>
                                                    @elseif($drafter['status'] === 'Menurun')
                                                        <i class="fas fa-arrow-down text-danger"></i>
                                                    @else
                                                        <i class="fas fa-circle text-secondary"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('jobticket.showdocumentmember', ['id' => $drafter['id'], 'status' => 'closed']) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-check"></i> Done
                                                    </a>
                                                    <a href="{{ route('jobticket.showdocumentmember', ['id' => $drafter['id'], 'status' => 'opened']) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-undo"></i> Undone
                                                    </a>



                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Checker Tab -->
                    <div class="tab-pane fade" id="checker" role="tabpanel" aria-labelledby="checker-tab">
                        <div class="card card-outline card-danger mt-2">
                            <div class="card-header">
                                <h3 class="card-title">Top Checkers (Checked)</h3>
                            </div>
                            <div class="card-header">
                                <h3>Daily Progress Checker</h3>
                                <canvas id="dailyProgressChartCheckers"></canvas>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered table-hover mt-4">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Checker</th>
                                            <th>Checked Count</th>
                                            <th>Cek Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rankedCheckers as $index => $checker)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $checker['checker_name'] }}</td>
                                                <td>{{ $checker['closed_count'] }}
                                                    @if($checker['status'] === 'Meningkat')
                                                        <i class="fas fa-arrow-up text-success"></i>
                                                    @elseif($checker['status'] === 'Menurun')
                                                        <i class="fas fa-arrow-down text-danger"></i>
                                                    @else
                                                        <i class="fas fa-circle text-secondary"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('jobticket.showdocumentmember', ['id' => $checker['id'], 'status' => 'closed']) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-check"></i> Done
                                                    </a>
                                                    <a href="{{ route('jobticket.showdocumentmember', ['id' => $checker['id'], 'status' => 'opened']) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-folder-open"></i> Open
                                                    </a>





                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="unfinishedCheckerDocuments" role="tabpanel"
                        aria-labelledby="unfinishedCheckerDocuments-tab">
                        <div class="card card-outline card-danger mt-2">
                            <div class="card-header">
                                <h3>Selesaikan Tanggungan Anda</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Belum Terselesaikan</th>
                                            <th>Cek</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unfinishedCheckerDocuments as $document)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $document['name'] }}</td>
                                                <td>{{ $document['unfinished_count'] }}</td>
                                                <td><a href="{{ route('jobticket.showdocumentmember', ['id' => $document['id'], 'status' => 'opened']) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-undo"></i> Check
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="unfinishedApproverDocuments" role="tabpanel"
                        aria-labelledby="unfinishedApproverDocuments-tab">
                        <div class="card card-outline card-danger mt-2">
                            <div class="card-header">
                                <h3>Selesaikan Tanggungan Anda</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Belum Terselesaikan</th>
                                            <th>Cek</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unfinishedApproverDocuments as $document)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $document['name'] }}</td>
                                                <td>{{ $document['unfinished_count'] }}</td>
                                                <td><a href="{{ route('jobticket.showdocumentmember', ['id' => $document['id'], 'status' => 'opened']) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-undo"></i> Check
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                        <div class="card card-outline card-danger mt-2">
                            <div class="card-header">
                                <h3>Raport Gabungan Drafter dan Checker</h3>
                                <h3>Nilai Poin Tiap Job</h3>
                                <h3>Drafter : 1 Poin || Checker : 0.5 Poin</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Skor</th>
                                            <th>Laporan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData as $report)
                                                                                <tr>
                                                                                    <td>{{ $loop->iteration }}</td>
                                                                                    <td>{{ $report['name'] }}</td>
                                                                                    <td>{{ $report['score'] }}</td>
                                                                                    <td>
                                                                                        <a href="{{ route('jobticket.downloadWLA', [
                                                'startDate' => $startDate,
                                                'endDate' => $endDate,
                                                'id' => $report['id']
                                            ]) }}" class="btn bg-maroon btn-sm">
                                                                                            <i class="fas fa-download"></i> Download
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                </div>


            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data untuk Drafter
            const chartDataDrafters = @json($chartDataDrafters);
            const drafterCtx = document.getElementById('dailyProgressChartDrafters').getContext('2d');

            // Chart Drafter
            const datesDrafters = chartDataDrafters[0].dates;
            new Chart(drafterCtx, {
                type: 'line',
                data: {
                    labels: datesDrafters,
                    datasets: chartDataDrafters.map(data => ({
                        label: data.label,
                        data: data.data,
                        borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                        fill: false,
                        tension: 0.2
                    }))
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'category',
                            title: { display: true, text: 'Tanggal' }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Closed' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });

            // Data untuk Checker
            const chartDataCheckers = @json($chartDataCheckers);
            const checkerCtx = document.getElementById('dailyProgressChartCheckers').getContext('2d');

            // Chart Checker
            const datesCheckers = chartDataCheckers[0].dates;
            new Chart(checkerCtx, {
                type: 'line',
                data: {
                    labels: datesCheckers,
                    datasets: chartDataCheckers.map(data => ({
                        label: data.label,
                        data: data.data,
                        borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                        fill: false,
                        tension: 0.2
                    }))
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'category',
                            title: { display: true, text: 'Tanggal' }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Checked' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        });
    </script>
@endpush