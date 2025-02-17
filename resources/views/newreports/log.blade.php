@extends('layouts.table1showprogressreport')

@section('container2')
    @php
        $message = json_decode($log->message, true);
    @endphp

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="dataupdate-tab" data-toggle="tab" href="#dataupdate" role="tab" aria-controls="dataupdate" aria-selected="true">Data Update</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="databaru-tab" data-toggle="tab" href="#databaru" role="tab" aria-controls="databaru" aria-selected="false">Data Baru</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!-- Data Update Tab Content -->
        <div class="tab-pane fade show active" id="dataupdate" role="tabpanel" aria-labelledby="dataupdate-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h1 class="mt-0 mb-1">{{ $log->level }} - {{ $log->user }}</h1>
                            <h2 class="mt-0 mb-1">Pesan: {{ $message['message'] }}</h2>
                            <h3 class="mt-0 mb-1">Upload pada waktu: {{ $log->created_at->format('d-m-Y H:i') }}</h3>
                        </div>
                        <div class="card-body">
                            @if(isset($message['updatedata']) && is_array($message['updatedata']))
                                <table id="example5" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">No Dokumen</th>
                                            <th scope="col">Nama Dokumen</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Drafter</th>
                                            <th scope="col">Checker</th>
                                            <th scope="col">Deadline Release</th>
                                            <th scope="col">Realisasi</th>
                                            <th scope="col">Jenis Dokumen</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($message['updatedata'] as $index => $progressReport)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $progressReport['nodokumen'] }}</td>
                                                <td>{{ $progressReport['namadokumen'] }}</td>
                                                <td>{{ $progressReport['level'] }}</td>
                                                <td>{{ $progressReport['drafter'] }}</td>
                                                <td>{{ $progressReport['checker'] }}</td>
                                                <td>{{ $progressReport['deadlinerelease'] }}</td>
                                                <td>{{ $progressReport['realisasi'] }}</td>
                                                <td>{{ $progressReport['documentkind'] }}</td>
                                                <td>{{ $progressReport['status'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No data available for update.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Baru Tab Content -->
        <div class="tab-pane fade" id="databaru" role="tabpanel" aria-labelledby="databaru-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h1 class="mt-0 mb-1">{{ $log->level }} - {{ $log->user }}</h1>
                            <h2 class="mt-0 mb-1">Pesan: {{ $message['message'] }}</h2>
                            <h3 class="mt-0 mb-1">Upload pada waktu: {{ $log->created_at->format('d-m-Y H:i') }}</h3>
                        </div>
                        <div class="card-body">
                            @if(isset($message['databaru']) && is_array($message['databaru']))
                                <table id="example5" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">No Dokumen</th>
                                        <th scope="col">Nama Dokumen</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Drafter</th>
                                        <th scope="col">Checker</th>
                                        <th scope="col">Deadline Release</th>
                                        <th scope="col">Realisasi</th>
                                        <th scope="col">Jenis Dokumen</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($message['databaru'] as $index => $progressReport)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $progressReport['nodokumen'] }}</td>
                                                <td>{{ $progressReport['namadokumen'] }}</td>
                                                <td>{{ $progressReport['level']??"" }}</td>
                                                <td>{{ $progressReport['drafter'] ??""}}</td>
                                                <td>{{ $progressReport['checker'] ??""}}</td>
                                                <td>{{ $progressReport['deadlinerelease']??"" }}</td>
                                                <td>{{ $progressReport['realisasi'] ??""}}</td>
                                                <td>{{ $progressReport['documentkind']??"" }}</td>
                                                <td>{{ $progressReport['status']??"" }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No new data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
