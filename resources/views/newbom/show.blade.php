@extends('layouts.universal')

@section('container2') 

<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="">List Material</a></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

@endsection

@section('container3')

<div class="row justify-content-center">
    <div class="col-10">
        <div class="card card-outline card-danger">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab"
                        aria-controls="progress" aria-selected="true">Progress</a>
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
                            <div class="card-body">
                                <p><strong>Nomor BOM:</strong> {{ $newbom->BOMnumber }}</p>
                                <p><strong>Proyek:</strong> {{ $newbom->projectType->title }}</p>
                                <p><strong>Unit:</strong> {{ $newbom->unit }}</p>
                                <p><strong>Tingkat Penyelesaian:</strong> {{ number_format($seniorpercentage, 2) }} %
                                </p>


                                <div class="row">
                                    <!-- <div class="col-md-3 col-sm-6 col-12">
                                        <button type="button" class="btn btn-success btn-sm btn-block"
                                            onclick="tambahdata('{{ $newbom->id }}')">
                                            Tambah Komat Bom
                                        </button>
                                    </div> -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <button type="button" class="btn btn-danger btn-sm btn-block"
                                            onclick="downloadbom('{{ $newbom->id }}')" aria-label="Export data BOM">
                                            Download BOM
                                        </button>
                                    </div>

                                </div>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="checkbox-toggle" id="checkAll"><i
                                                        class="far fa-square"></i></span>
                                            </th>
                                            <th scope="col">No</th>
                                            <th scope="col">Rev</th>
                                            <th scope="col">Kode Material</th>
                                            <th scope="col">Material</th>
                                            <th scope="col">Spesifikasi</th>
                                            <th scope="col">Memo Terkait</th>
                                            <th scope="col">Total Percentage</th>
                                            <th scope="col">Edit</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $penghitung = 1;
                                        @endphp
                                        @foreach ($newbomkomats as $index => $item)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        @php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            $key = key($newbomkomats);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            next($newbomkomats);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        @endphp
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div class="icheck-primary">
                                                                                                <input type="checkbox" value="{{ $index }}" name="document_ids[]"
                                                                                                    id="checkbox{{ $key }}">
                                                                                                <label for="checkbox{{ $key }}"></label>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>{{ $penghitung }}</td>
                                                                                        <td contenteditable id="rev_{{ $newbom->id }}_{{ $index }}">
                                                                                            {{ $item['rev'] }}
                                                                                        </td>
                                                                                        <td contenteditable id="kodematerial_{{ $newbom->id }}_{{ $index }}">
                                                                                            {{ $item['kodematerial'] }}
                                                                                        </td>
                                                                                        <td contenteditable id="material_{{ $newbom->id }}_{{ $index }}">
                                                                                            {{ $item['material'] }}
                                                                                        </td>
                                                                                        <td>
                                                                                            @if(isset($item->newprogressreports) && $item->newprogressreports->isNotEmpty())
                                                                                                                                        <div >
                                                                                                    @foreach ($item->newprogressreports as $progressReport)
                                                                                                            @php
                                                                                                                $documentnumber = $progressReport->nodokumen ?? 'Tidak ada dokumen';
                                                                                                                $spesifikasipic = $progressReport->drafter ?? 'Tidak ada drafter';
                                                                                                                $status = $progressReport->status ? 'Released' : 'Proses';
                                                                                                            @endphp
                                                                                                            <div style="margin-bottom: 5px;">
                                                                                                                <span class="badge bg-info">
                                                                                                                    {{ $documentnumber }}
                                                                                                                </span>
                                                                                                                <span class="badge bg-primary">
                                                                                                                    {{ $spesifikasipic }}
                                                                                                                </span>
                                                                                                                <span class="badge {{ $progressReport->released ? 'bg-success' : 'bg-warning' }}">
                                                                                                                    {{ $status }}
                                                                                                                </span>
                                                                                                            </div>
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            @else
                                                                                                <div>
                                                                                                    <div style="margin-bottom: 5px;">
                                                                                                        <span class="badge bg-warning">
                                                                                                            Tidak ada dokumen
                                                                                                        </span>
                                                                                                        <span class="badge bg-primary">
                                                                                                            Tidak ada drafter
                                                                                                        </span>
                                                                                                        <span class="badge bg-warning">
                                                                                                            Proses
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                            </td>




                                                                                                                                <td>
                                                                                                                                    @if(isset($groupedKomats[$item['kodematerial']]))
                                                                                                                                                                                                                                                                    <!-- @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listkomat = $groupedKomats[$item['kodematerial']]['memoname']; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listmemoid = $groupedKomats[$item['kodematerial']]['memoid']; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listkomatStatus = $groupedKomats[$item['kodematerial']]['memostatus'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listpercentage = $groupedKomats[$item['kodematerial']]['percentage'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listsupplier = $groupedKomats[$item['kodematerial']]['supplier'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $listPEcombineworkstatus= $groupedKomats[$item['kodematerial']]['PEcombineworkstatus'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    @endphp
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    @for ($i = 0; $i < count($listkomat); $i++)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div style="display: inline-block; margin-right: 10px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <a href="{{ route('memo.show', ['id' => $listmemoid[$i]]) }}" class="badge badge-success" style="margin-left: 5px;">{{ $listkomat[$i] }}</a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="badge badge-info" style="margin-left: 5px;">{{ $listkomatStatus[$i] }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="badge badge-primary" style="margin-left: 5px;">{{ $listpercentage[$i] }} %</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="badge badge-warning" style="margin-left: 5px;">{{ $listPEcombineworkstatus[$i] }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="badge badge-secondary" style="margin-left: 5px;">{{ $listsupplier[$i] }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    @endfor -->

                                                                                                                                                                                                                                                                    @php
                                                                                                                                                                                                                                                                        $sortedKomats = [];
                                                                                                                                                                                                                                                                        $komats = $groupedKomats[$item['kodematerial']];

                                                                                                                                                                                                                                                                        for ($i = 0; $i < count($komats['memoname']); $i++) {
                                                                                                                                                                                                                                                                            $sortedKomats[] = [
                                                                                                                                                                                                                                                                                'memoname' => $komats['memoname'][$i],
                                                                                                                                                                                                                                                                                'memoid' => $komats['memoid'][$i],
                                                                                                                                                                                                                                                                                'memostatus' => $komats['memostatus'][$i],
                                                                                                                                                                                                                                                                                'percentage' => $komats['percentage'][$i],
                                                                                                                                                                                                                                                                                'supplier' => strtoupper($komats['supplier'][$i]),
                                                                                                                                                                                                                                                                                'PEcombineworkstatus' => $komats['PEcombineworkstatus'][$i],
                                                                                                                                                                                                                                                                            ];
                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                        usort($sortedKomats, function ($a, $b) {
                                                                                                                                                                                                                                                                            return strcmp($a['supplier'], $b['supplier']);
                                                                                                                                                                                                                                                                        });
                                                                                                                                                                                                                                                                    @endphp

                                                                                                                                                                                                                                                                    @foreach ($sortedKomats as $komat)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="badge-combined">
                                                                                                                                                                                                                                                                            <a href="{{ route('memo.show', ['id' => $komat['memoid']]) }}"
                                                                                                                                                                                                                                                                                class="badge badge-success badge-section">{{ $komat['memoname'] }}</a>
                                                                                                                                                                                                                                                                            <span
                                                                                                                                                                                                                                                                                class="badge badge-info badge-section">{{ $komat['memostatus'] }}</span>
                                                                                                                                                                                                                                                                            <span
                                                                                                                                                                                                                                                                                class="badge badge-primary badge-section">{{ $komat['percentage'] }}
                                                                                                                                                                                                                                                                                %</span>
                                                                                                                                                                                                                                                                            <span
                                                                                                                                                                                                                                                                                class="badge badge-warning badge-section">{{ $komat['PEcombineworkstatus'] }}</span>
                                                                                                                                                                                                                                                                            <span
                                                                                                                                                                                                                                                                                class="badge badge-secondary badge-section">{{ $komat['supplier'] }}</span>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                    @endforeach



                                                                                                                                    @else
                                                                                                                                        <span class="badge bg-info" contenteditable
                                                                                                                                            id="yyy_{{ $newbom->id }}_{{ $index }}" style="padding: 2px 5px;">
                                                                                                                                            Tidak ada Memo Terkait
                                                                                                                                        </span>
                                                                                                                                    @endif
                                                                                                                                </td>

                                                                                                                                <td>
                                                                                                                                    @if(isset($groupedKomats[$item['kodematerial']]))
                                                                                                                                        @php 
                                                                                                                                            $totalpercentage = $groupedKomats[$item['kodematerial']]['totalpercentage']; 
                                                                                                                                        @endphp
                                                                                                                                    @else
                                                                                                                                        @php 
                                                                                                                                            $totalpercentage = 0; 
                                                                                                                                        @endphp
                                                                                                                                    @endif




                                                                                                                                    @if($totalpercentage == 100)
                                                                                                                                        <span class="badge bg-success" style="padding: 2px 5px;">
                                                                                                                                            Completed
                                                                                                                                        </span>
                                                                                                                                    @else
                                                                                                                                        <span class="badge bg-warning" style="padding: 2px 5px;">
                                                                                                                                            Incomplete
                                                                                                                                        </span>
                                                                                                                                    @endif
                                                                                                                                    <span class="badge bg-info" contenteditable
                                                                                                                                        id="status_{{ $newbom->id }}_{{ $index }}"
                                                                                                                                        style="padding: 2px 5px;">
                                                                                                                                        {{ $item['status'] }}
                                                                                                                                    </span>
                                                                                                                                </td>



                                                                                                                                @php
                                                                                                                                    $datarevisi2 = [];
                                                                                                                                    $datarevisi2['kodematerial'] = str_replace("\n", " ", $item['kodematerial']);
                                                                                                                                    $datarevisi2['material'] = str_replace("\n", " ", $item['material']);
                                                                                                                                    $datarevisi2['listspesifikasi'] = $item['spesifikasi'] ?? []; // Include listspesifikasi
                                                                                                                                    $datarevisi2['status'] = $item['status']; // Assuming you have status in the $item array

                                                                                                                                    // Convert the array to a JSON string directly
                                                                                                                                    $datarevisi = json_encode($datarevisi2, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG);
                                                                                                                                @endphp

                                                                                                                                <td>
                                                                                                                                    <!-- <a href="#" class="btn btn-success btn-sm" onclick="openUpdateForm('{{ $newbom->id }}', '{{ $index }}')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-edit"></i> Update
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </a> -->
                                                                                                                                    <a href="#" class="btn btn-info btn-sm"
                                                                                                                                        onclick="showDocumentSummary('{{ $datarevisi }}','{{ $newbom->id }}', '{{ $item['id'] }}', '{{ $index }}')">
                                                                                                                                        <i class="fas fa-edit"></i> Edit
                                                                                                                                    </a>
                                                                                                                                    <a href="#" class="btn btn-warning btn-sm"
                                                                                                                                        onclick="opendeleteForm('{{ $newbom->id }}', '{{ $item['id'] }}', '{{ $index }}')">
                                                                                                                                        <i class="fas fa-eraser"></i> Delete
                                                                                                                                    </a>
                                                                                                                                </td>
                                                                                                                            </tr>
                                                                                                                            @php
                                                                                                                                $penghitung++;
                                                                                                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>


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
                                    @if($newbom->systemLogs && $newbom->systemLogs->isEmpty())
                                        <p>No history available for project {{ $newbom->id }}.</p>
                                    @else
                                                                                                    <table id="example6" class="table table-bordered table-hover">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th scope="col">No</th>
                                                                                                                <th scope="col">Status</th>
                                                                                                                <th scope="col">Nama Uploader</th>
                                                                                                                <th scope="col">Waktu Upload</th>
                                                                                                                <th scope="col">Status</th>
                                                                                                                <th scope="col">Persentase Terakhir</th>
                                                                                                                <th scope="col">Aksi</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            @foreach ($newbom->systemLogs as $riwayat)
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

                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                    @if($riwayat->aksi == 'progressaddition')
                                                                                                                                                                                                                                        <a href="{{ route('newreports.showlog', ['newreport' => $newbom->id, 'logid' => $riwayat->id]) }}"
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




@section('rightsidebar') 
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Log Aktivitas</h5>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">

        </ul>
    </div>
</div>
@endsection


@push('scripts')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        const groupprogress = @json($newbom);

        function getSpesifikasiOptions(selectedSpesifikasi) {
            let options = '<option value="" ' + (selectedSpesifikasi === '' ? 'selected' : '') + '></option>';
            for (const key in groupprogress) {
                if (groupprogress.hasOwnProperty(key)) {
                    const selected = key === selectedSpesifikasi ? 'selected' : '';
                    options += `<option value="${key}" ${selected}>${key}</option>`;
                }
            }
            return options;
        }

        function openUpdateForm(id, index) {
            var kodematerial = document.getElementById(`kodematerial_${id}_${index}`).innerText;
            var material = document.getElementById(`material_${id}_${index}`).innerText;
            var updateUrl = `/bom/update/${id}/${index}`;

            // Menggunakan variabel yang benar saat mengirim data melalui AJAX
            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: {
                    kodematerial: kodematerial,
                    material: material,
                    _token: "{{ csrf_token() }}" // Sertakan token CSRF
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data berhasil diperbarui!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Tambahkan kode di sini jika diperlukan untuk memperbarui tampilan tanpa reload
                },
                error: function (xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        }





        function opendeleteForm(idbom, idkomat, index) {
            var deleteUrl = `/newboms/newbomkomats/${idbom}/${idkomat}/delete`;

            // Display confirmation before deletion
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request for deletion
                    $.ajax({
                        url: deleteUrl,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}" // Include CSRF token
                        },
                        success: function (response) {
                            // Remove row from DOM
                            $(`#kodematerial_${id}_${index}`).closest('tr').remove();

                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Add any additional code here to update the view without reload
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function tambahdata(id) {
            Swal.fire({
                title: "Tambah Kode Material dan Material",
                html: `
                                                                                        <p><b>Kode Material</b></p>
                                                                                        <input id="tambah-kode-material" class="swal2-input" value="" placeholder="Kode Material">
                                                                                        <p><b>Material</b></p>
                                                                                        <input id="tambah-material" class="swal2-input" value="" placeholder="Material">
                                                                                        <p><b>Spesifikasi</b></p>
                                                                                        <select id="tambah-spesifikasi" class="swal2-input">
                                                                                            ${getSpesifikasiOptions('')}
                                                                                        </select>
                                                                                        <p><b>Status</b></p>
                                                                                        <select id="tambah-status" class="swal2-input">
                                                                                            <option value="0">0</option>
                                                                                            <option value="N">N</option>
                                                                                            <option value="PR">PR</option>
                                                                                            <option value="PO">PO</option>
                                                                                        </select>
                                                                                    `,

                focusConfirm: false,
                showCancelButton: false, // Hilangkan tombol batal
                confirmButtonText: 'Update', // Mengubah teks tombol konfirmasi
                preConfirm: () => {
                    return [
                        document.getElementById("tambah-kode-material").value,
                        document.getElementById("tambah-material").value,
                        document.getElementById("tambah-spesifikasi").value,
                        document.getElementById("tambah-status").value,
                    ];
                }
            }).then((result) => {
                if (result.value) {
                    const [kodeMaterial, material, spesifikasi, status] = result.value;

                    Swal.fire(`Kode Material: ${kodeMaterial}, Material: ${material}, Spesifikasi: ${spesifikasi}, Status: ${status}`);

                    // Kirim request ke endpoint 'update' dengan menggunakan method 'GET'
                    var updateUrl = `/newboms/newbomkomats/${id}?kodematerial=${kodeMaterial}&material=${material}&spesifikasi=${spesifikasi}&status=${status}`;

                    // Redirect atau buka URL untuk update
                    window.location.href = updateUrl;
                }
            });
        }

        function showDocumentSummary(itemMaterial, idbom, idkomat, index) {
            itemMaterial = JSON.parse(itemMaterial);
            var kodematerial = itemMaterial['kodematerial'];
            var material = itemMaterial['material'];
            var spesifikasiList = itemMaterial['listspesifikasi']; // Get listspesifikasi
            var spesifikasi = "hi";
            var status = itemMaterial['status'];
            var spesifikasiText = spesifikasiList.join(', ');
            Swal.fire({
                title: "Input Kode Material dan Material",
                html: `
                                                                                    <p><b>Kode Material</b></p>
                                                                                    <input id="kode-material" class="swal2-input" value="${kodematerial}" placeholder="Kode Material">
                                                                                    <p><b>Material</b></p>
                                                                                    <input id="material" class="swal2-input" value="${material}" placeholder="Material">
                                                                                    <p><b>Spesifikasi Sudah Dipilih</b></p>
                                                                                    <p id="spesifikasishow" class="swal2-input">${spesifikasiText}</p>
                                                                                    <p><b>Spesifikasi Ditambahkan</b></p>
                                                                                    <select id="spesifikasi" class="swal2-input">
                                                                                        ${getSpesifikasiOptions(spesifikasi)}
                                                                                    </select>
                                                                                    <p><b>Status</b></p>
                                                                                    <select id="status" class="swal2-input">
                                                                                        <option value="0" ${status === '0' ? 'selected' : ''}>0</option>
                                                                                        <option value="N" ${status === 'N' ? 'selected' : ''}>N</option>
                                                                                        <option value="PR" ${status === 'PR' ? 'selected' : ''}>PR</option>
                                                                                        <option value="PO" ${status === 'PO' ? 'selected' : ''}>PO</option>
                                                                                    </select>
                                                                                `,

                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return [
                        document.getElementById("kode-material").value,
                        document.getElementById("material").value,
                        document.getElementById("spesifikasi").value,
                        document.getElementById("status").value
                    ];
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var newKodeMaterial = result.value[0];
                    var newMaterial = result.value[1];
                    var newSpesifikasi = result.value[2];
                    var newStatus = result.value[3];

                    // Menampilkan konfirmasi sebelum melakukan pembaruan
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
                            var updateUrl = `/newboms/newbomkomats/${idbom}/${idkomat}`;
                            $.ajax({
                                url: updateUrl,
                                method: 'POST',
                                data: {
                                    kodematerial: newKodeMaterial,
                                    material: newMaterial,
                                    spesifikasi: newSpesifikasi,
                                    status: newStatus,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    // Update data di dalam tabel
                                    $(`#kodematerial_${id}_${index}`).text(newKodeMaterial);
                                    $(`#material_${id}_${index}`).text(newMaterial);
                                    $(`#spesifikasi_${id}_${index}`).text(newSpesifikasi);
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
                                }
                            });
                        }
                    });
                }
            });
        }

        function handleExportMultipleItems(idbom) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin mengexport item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Export!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function (checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    $.ajax({
                        url: "",
                        type: "POST", // Mengubah metode menjadi POST
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah diexport.',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '/bom/download/' + response.file_name;
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal mengexport item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }



        function downloadbom(idbom) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin mengexport item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Export!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/newboms/download/${idbom}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        xhrFields: {
                            responseType: 'blob' // Mendukung unduhan file
                        },
                        success: function (response, status, xhr) {
                            const disposition = xhr.getResponseHeader('Content-Disposition');
                            const matches = /filename="([^"]*)"/.exec(disposition);
                            const filename = matches != null && matches[1] ? matches[1] : 'export.xlsx';

                            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'File telah berhasil diunduh.',
                                icon: 'success'
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengexport item.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }








    </script>
    <script>
        $(function () {
            $('#example2').DataTable({
                "paging": true,
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
            //Enable check and uncheck all functionality
            $('#checkAll').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('input[name="document_ids[]"]').prop('checked', false);
                    $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check first 10 checkboxes
                    $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                    $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks);
            });
        });
    </script>

@endpush