@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp


@section('container2') 
    <div id="encoded-data" data-listprogressnodokumen="{{ json_encode($listdocuments) }}"></div>
<div id="encoded-data-memo" data-listprogressnodokumen="{{ json_encode($newmemo) }}"></div>

<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('jobticket.show', ['id' => $jobticketpart->id]) }}">List Dokumen</a></li>
                    <li class="breadcrumb-item active text-bold">List Revisi (Ambil Jobticket)</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection


@section('container3')
<div align="center">
    <div class="col-9">

        <div class="card card-outline card-danger">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab"
                        aria-controls="progress" aria-selected="true">Jobticket</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="laporan-tanggal-tab" data-toggle="tab" href="#laporan-tanggal" role="tab"
                        aria-controls="laporan-tanggal" aria-selected="false">Notification</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <!-- Progress Tab Content -->
                <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                    <div class="card-header">
                        <table class="table table-bordered table-hover mt-4">
                            <tbody>
                                <tr>
                                    <td rowspan="7" class="text-center" style="width: 25%;">
                                        <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                            style="max-width: 250px;">
                                    </td>
                                    <td rowspan="7" style="width: 50%;">
                                        <div class="text-center mt-2" style="font-size: 2rem; font-weight: bold;">List
                                            Revisi</div>
                                    </td>
                                    <td style="width: 25%;" class="p-1">
                                        Project:
                                        <b>{{ ucwords(str_replace('-', ' ', $jobticketpart->projectType->title)) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;" class="p-1">
                                        Bagian: <b>{{ ucfirst($jobticketpart->unit->name) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;" class="p-1">
                                        Tanggal: <b>{{ date('d F Y') }}</b>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                    <div class="card-body">
                        <!-- Container for Document Information -->
                        <div class="text-center mb-4">
                            <h2 class="mb-3">Informasi Dokumen {{ $jobticketidentitys->jobticketDocumentkind->name }}
                            </h2>
                            <div class="card card-body">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Nomor Dokumen:</strong></td>
                                            <td>{{ $jobticketidentitys->documentnumber }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Dokumen:</strong></td>
                                            <td>{{ $jobtickets[0]->documentname }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Dokumen:</strong></td>
                                            <td>{{ $jobticketidentitys->jobticketDocumentkind->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Level:</strong></td>
                                            <td>{{ $jobtickets[0]->level }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rev Terakhir:</strong></td>
                                            <td>{{  $jobtickets->last()->rev }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Main Table for Job Tickets -->
                        <table class="table table-bordered table-hover mt-4">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Rev ke</th>
                                    <th>Status</th>
                                    <th>Informasi User</th>
                                    <th>Dokumen pendukung (Vault)</th>
                                    <th>Catatan</th>
                                    <th>Waktu Mulai</th>
                                    <th>Countup</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $urutan = 1;



                                @endphp
                                @foreach($jobtickets as $index => $jobticket)
                                                                @php
                                                                    $documenref = $jobticket->getDocumentSupport;
                                                                    $startcolor = "bg-gray";
                                                                    $continuecolor = "";
                                                                    $donecolor = "";
                                                                    $pausecolor = "";
                                                                    $draftername = $jobticket->drafter_name ?? null;
                                                                    $checkername = $jobticket->checker_name ?? null;
                                                                    $approvername = $jobticket->approver_name ?? null;
                                                                    $jobticket_started = $jobticket->jobticketStarted;
                                                                    $utc_time = $jobticket_started->start_time_first ?? 'Belum Ada';

                                                                    // Convert UTC time to Asia/Jakarta timezone
                                                                    $waktuindo = $utc_time !== 'Belum Ada'
                                                                        ? (new DateTime($utc_time, new DateTimeZone('UTC')))
                                                                            ->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d/m/Y')
                                                                        : 'Belum Ada';

                                                                    // Calculate total time
                                                                    $elapsedSeconds = $jobticket_started->total_elapsed_seconds ?? 0;
                                                                    $startTime = $jobticket_started->start_time_run ?? null;
                                                                    $pauseTime = $jobticket_started->pause_time_run ?? null;
                                                                    $currentTime = Carbon::now();
                                                                    $totalTime = 0;

                                                                    if ($startTime !== null) {
                                                                        $startTime = Carbon::parse($startTime);
                                                                        if ($pauseTime !== null) {
                                                                            $pauseTime = Carbon::parse($pauseTime);
                                                                        }
                                                                        $totalTime = $pauseTime === null
                                                                            ? $currentTime->diffInSeconds($startTime) + $elapsedSeconds
                                                                            : $pauseTime->diffInSeconds($startTime) + $elapsedSeconds;
                                                                    }
                                                                @endphp

                                                                <tr>
                                                                    <td>{{ $urutan++ }}</td>
                                                                    <td>{{ Carbon::parse($jobticket->created_at)->format('d/m/Y') }}</td>
                                                                    <td>{{ $jobticket->rev }}</td>
                                                                    <td>
                                                                        <p>
                                                                            @if($jobticket->status === 'closed')
                                                                                <span class="badge badge-success">Closed</span>
                                                                            @else
                                                                                <span class="badge badge-info">{{ $jobticket->status ?? "Open" }}</span>
                                                                            @endif
                                                                        </p>
                                                                    </td>

                                                                    <td>
                                                                        <div class="user-info">
                                                                            <p id="drafter_{{ $jobticket->id}}"><strong>Drafter:</strong>
                                                                                <span
                                                                                    class="badge badge-secondary">{{ $jobticket->drafter_name ?? "Belum ada" }}</span>
                                                                            </p>
                                                                            <p id="checker_{{ $jobticket->id}}"><strong>Checker:</strong>
                                                                                <span
                                                                                    class="badge badge-secondary">{{ $jobticket->checker_name ?? "Belum ada" }}</span>
                                                                            </p>
                                                                            <p id="approver_{{ $jobticket->id}}"><strong>Approver:</strong>
                                                                                <span
                                                                                    class="badge badge-secondary">{{ $jobticket->approver_name ?? "Belum ada" }}</span>
                                                                            </p>
                                                                        </div>
                                                                    </td>

                                                                    <td id="document-support-container-{{ $index }}">

                                                                        @if ($documenref->count() > 0)
                                                                            @foreach ($documenref as $document)
                                                                                <span class="badge bg-info">{{ $document->id }} - {{ $document->namadokumen }} -
                                                                                    {{ $document->nodokumen }} - {{ $document->rev }}</span><br>
                                                                            @endforeach
                                                                        @else
                                                                            <span class="text-muted">Dokumen pendukung belum ada</span>
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        <p id="note_{{ $jobticket->id }}">
                                                                            <span
                                                                                class="badge badge-secondary">{{ $jobticket->note ?? "Belum ada catatan" }}</span>
                                                                        </p>
                                                                    </td>

                                                                    <td>{{ $waktuindo }}</td>
                                                                    <td>
                                                                        <div id="elapsed_time_{{ $jobticket->id }}" style="position: relative;">
                                                                            <span
                                                                                class="time-display badge {{ $pauseTime ? 'badge-warning' : 'badge-info' }}">
                                                                                @if($pauseTime === null)
                                                                                    {{ gmdate('H:i:s', $totalTime) }}
                                                                                @else
                                                                                    Jeda
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            if (isset($jobticket->jobticketStarted->revisions) && count($jobticket->jobticketStarted->revisions) > 0) {
                                                                                $detailcolor = "bg-maroon"; // Warna jika revisi ada dan lebih dari 0
                                                                            } else {
                                                                                $detailcolor = "bg-gray";   // Warna jika revisi tidak ada atau 0
                                                                            }
                                                                        @endphp

                                                                        <a href="#" class="btn btn-default {{ $detailcolor }} d-block mb-1"
                                                                            id="buttondetailtugas_{{ $jobticket->id }}_{{ $index }}"
                                                                            onclick="detailtugas('{{ $jobticket->jobticketIdentity->jobticket_part_id }}', '{{ $jobticket->jobticket_identity_id }}', '{{ $jobticket->id }}')">
                                                                            <i class="fas fa-info-circle"></i> Detail
                                                                        </a>






                                                                        @if($useronly->rule == "superuser")
                                                                            <a href="#" class="btn btn-default bg-maroon d-block mb-1"
                                                                                id="buttondeletetugas_{{ $jobticket->id }}_{{ $index }}"
                                                                                onclick="deletetugas('{{ $jobticket->jobticketIdentity->jobticket_part_id }}','{{ $jobticket->jobticket_identity_id }}','{{ $jobticket->id }}')">
                                                                                <i class="fas fa-info-circle"></i> Delete Jobticket
                                                                            </a>
                                                                        @endif



                                                                        @if($jobticket->status == null)
                                                                                                            @if (
                                                                                                                    (
                                                                                                                        str_contains($useronly->rule, 'Quality Engineering') ||
                                                                                                                        str_contains($useronly->rule, 'Electrical Engineering System') ||
                                                                                                                        $useronly->rule == "Manager " . $jobticketpart->unit->name ||
                                                                                                                        $selectusercontroller == true
                                                                                                                    ) &&
                                                                                                                    (
                                                                                                                        $jobticket->checker_id == null ||
                                                                                                                        $jobticket->approver_id == null
                                                                                                                    )
                                                                                                                )
                                                                                                                                                <a href="#" class="btn btn-default bg-pink d-block mb-1"
                                                                                                                                                    id="selectuserbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                    onclick="pickposition('{{ $jobticket->id }}', {{ json_encode($jobticket) }}, '{{ $index }}', '{{ $useronly->rule }}')">
                                                                                                                                                    <i class="fas fa-hand-pointer"></i> Select Pic
                                                                                                                                                </a>
                                                                                                            @endif


                                                                                                            @if($useronly->rule == "Manager " . $jobticketpart->unit->name || $useronly->rule == $useronly->rule || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                                                                                                        @if(!isset($jobticket_started->start_time_first))
                                                                                                                                                                    @php
                                                                                                                                                                        $statusrevisi = $jobticket_started->statusrevisi ?? "dibuka";
                                                                                                                                                                    @endphp

                                                                                                                                                                    @if($draftername == "-" || $draftername == "" || $draftername == null)
                                                                                                                                                                        @if($draftername == "-" || $draftername == "" || $draftername == null)
                                                                                                                                                                            @if(!isset($indukan[strval($jobticket->id)]["persen"]))
                                                                                                                                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                                                                                                                                    id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                    onclick="picktugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}','drafter')">
                                                                                                                                                                                    <i class="fas fa-hand-pointer"></i> Pick Drafter
                                                                                                                                                                                </a>
                                                                                                                                                                            @elseif(isset($indukan[strval($jobticket->id)]["persen"]) && ($indukan[strval($jobticket->id)]["persen"]['count'] != $indukan[strval($jobticket->id)]["persen"]['countrelease']))
                                                                                                                                                                                <a href="#" class="btn btn-default bg-pink d-block mb-1" id="button">
                                                                                                                                                                                    <i class="fas fa-hand-pointer"></i> Dokumen Pendukung Belum Release
                                                                                                                                                                                </a>
                                                                                                                                                                            @elseif(isset($indukan[strval($jobticket->id)]["persen"]) && ($indukan[strval($jobticket->id)]["persen"]['count'] == $indukan[strval($jobticket->id)]["persen"]['countrelease']))
                                                                                                                                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                                                                                                                                    id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                    onclick="picktugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                    <i class="fas fa-hand-pointer"></i> Pick Tugas
                                                                                                                                                                                </a>
                                                                                                                                                                            @endif
                                                                                                                                                                        @endif



                                                                                                                                                                    @else

                                                                                                                                                                                    @if(!isset($jobticket_started) || $jobticket_started->statusrevisi != "ditutup")

                                                                                                                                                                                                    @if($draftername == $useronly->name)
                                                                                                                                                                                                                    @php
                                                                                                                                                                                                                        $startcolor = ($jobticket->documentsupport !== null || $jobticket->note !== null) ? 'btn-warning' : 'bg-gray';
                                                                                                                                                                                                                    @endphp

                                                                                                                                                                                                                    <a href="#" class="btn {{$startcolor}} btn-sm d-block mb-1"
                                                                                                                                                                                                                        id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                                                        onclick="starttugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                                                        <i class="fas fa-rocket"></i> Start Tugas
                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                    @else
                                                                                                                                                                                                        <a href="#" class="btn btn-default bg-white d-block mb-1" id="button">
                                                                                                                                                                                                            <i class="fas fa-hand-pointer"></i> Tugas Milik Orang
                                                                                                                                                                                                        </a>
                                                                                                                                                                                                    @endif
                                                                                                                                                                                    @else
                                                                                                                                                                                        @if($useronly->rule == "Manager " . $jobticketpart->unit->name || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                                                                                                                                                            <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                                                                                                                                                id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                                onclick="izinkanrevisitugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                                <i class="fas fa-edit"></i> Izinkan Perbaikan
                                                                                                                                                                                            </a>

                                                                                                                                                                                        @endif

                                                                                                                                                                                    @endif


                                                                                                                                                                    @endif
                                                                                                                                        @else
                                                                                                                                                                @if($useronly->name == $jobticket->drafter_name)
                                                                                                                                                                                        @if($jobticket_started->pause_time_run == null)
                                                                                                                                                                                                            @php
                                                                                                                                                                                                                $pausecolor = "btn-secondary";    
                                                                                                                                                                                                            @endphp
                                                                                                                                                                                                            <a href="#" class="btn btn-secondary btn-sm d-block mb-1"
                                                                                                                                                                                                                id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                                                onclick="pausetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                                                <i class="fas fa-pause-circle"></i> Jeda
                                                                                                                                                                                                            </a>
                                                                                                                                                                                        @else
                                                                                                                                                                                                            @php
                                                                                                                                                                                                                $continuecolor = "btn-primary";
                                                                                                                                                                                                            @endphp
                                                                                                                                                                                                            <a href="#" class="btn {{$continuecolor}} btn-sm d-block mb-1"
                                                                                                                                                                                                                id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                                                onclick="resumetugas('{{ $jobticket->id }}',  '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                                                <i class="fas fa-play-circle"></i> Lanjutkan
                                                                                                                                                                                                            </a>
                                                                                                                                                                                        @endif
                                                                                                                                                                                        @php
                                                                                                                                                                                            if ($continuecolor == "btn-primary") {
                                                                                                                                                                                                $donecolor = "btn-danger";
                                                                                                                                                                                            } elseif ($detailcolor == "bg-maroon") {
                                                                                                                                                                                                $donecolor = "bg-gray";
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $donecolor = "btn-danger";
                                                                                                                                                                                            }
                                                                                                                                                                                        @endphp

                                                                                                                                                                                        <a href="#" class="btn {{$donecolor}} btn-sm d-block mb-1"
                                                                                                                                                                                            id="button_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                                                                                            onclick="selesaitugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                            <i class="fas fa-check-circle"></i> Selesai
                                                                                                                                                                                        </a>
                                                                                                                                                                @endif
                                                                                                                                        @endif

                                                                                                                                        <!-- @if($useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <a href="#" class="btn btn-default bg-purple d-block mb-1" id="reset_button_{{ $jobticket->id }}_{{ $index }}" onclick="resettugas('{{ $jobticket->id }}',  '{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-exclamation-circle"></i> Reset
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        @endif -->
                                                                                                            @endif


                                                                                                            @php
                                                                                                                if ($draftername == "-" || $draftername == "" || $draftername == null) {
                                                                                                                    $documentcolor = "bg-gray";
                                                                                                                    $notecolor = "bg-gray";
                                                                                                                } elseif ($detailcolor == "bg-maroon" || $continuecolor == "btn-primary" || $pausecolor == "btn-secondary" || $startcolor == "btn-warning") {
                                                                                                                    $documentcolor = "bg-gray";
                                                                                                                    $notecolor = "bg-gray";
                                                                                                                } else {
                                                                                                                    $documentcolor = "bg-purple";
                                                                                                                    $notecolor = "bg-orange";
                                                                                                                }

                                                                                                                // Memastikan $documenref adalah koleksi yang valid
                                                                                                                $documenref = collect($jobticket->getDocumentSupport)->map(function ($document) {
                                                                                                                    return [
                                                                                                                        'id' => $document->id,
                                                                                                                        'namadokumen' => $document->namadokumen,
                                                                                                                        'nodokumen' => $document->nodokumen,
                                                                                                                        'rev' => $document->rev,
                                                                                                                    ];
                                                                                                                })->values()->all(); // Memastikan hasil adalah array numerik
                                                                                                            @endphp

                                                                                                            <a href="#" class="btn btn-default {{$documentcolor}} d-block mb-1"
                                                                                                                id="documentpickerbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                onclick="pickdocument('{{ $jobticket->id }}','{{ json_encode($documenref) }}','{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                <i class="fas fa-hand-pointer"></i> Pick Dokumen
                                                                                                            </a>

                                                                                                            <a href="#" class="btn btn-default {{$notecolor}} d-block mb-1"
                                                                                                                id="notebutton_{{ $jobticket->id }}_{{ $index }}"
                                                                                                                onclick="picknote('{{ $jobticket->id }}','{{ json_encode($documenref) }}','{{ $index }}', '{{ $useronly->name }}')">
                                                                                                                <i class="fas fa-hand-pointer"></i> Tambahkan Note
                                                                                                            </a>
                                                                        @endif

                                                                    </td>
                                                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="tab-pane fade" id="laporan-tanggal" role="tabpanel" aria-labelledby="laporan-tanggal-tab">
                    <div class="card-header">
                        <table class="table table-bordered table-hover mt-4">
                            <tbody>
                                <tr>
                                    <td rowspan="7" class="text-center" style="width: 25%;">
                                        <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                            style="max-width: 250px;">
                                    </td>
                                    <td rowspan="7" style="width: 50%;">
                                        <div class="text-center mt-2" style="font-size: 2rem; font-weight: bold;">List
                                            Update Dokumen Pendukung</div>
                                    </td>
                                    <td style="width: 25%;" class="p-1">
                                        Project:
                                        <b>{{ ucwords(str_replace('-', ' ', $jobticketpart->projectType->title)) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;" class="p-1">
                                        Bagian: <b>{{ ucfirst($jobticketpart->unit->name) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;" class="p-1">
                                        Tanggal: <b>{{ date('d F Y') }}</b>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="mb-3">Informasi Dokumen {{ $jobticketidentitys->jobticketDocumentkind->name }}
                            </h2>
                            <div class="card card-body">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Nomor Dokumen:</strong></td>
                                            <td>{{ $jobticketidentitys->documentnumber }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Dokumen:</strong></td>
                                            <td>{{ $jobtickets[0]->documentname }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Dokumen:</strong></td>
                                            <td>{{ $jobticketidentitys->jobticketDocumentkind->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Level:</strong></td>
                                            <td>{{ $jobtickets[0]->level }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rev Terakhir:</strong></td>
                                            <td>{{  $jobtickets->last()->rev }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tingkatkan:</strong></td>
                                            <td>
                                                @if($jobtickets->isNotEmpty() && $jobtickets->last()->status == 'closed')
                                                    <a href="#" class="btn btn-default bg-teal"
                                                        id="buttondetailtugas_{{$jobtickets->last()->id }}_{{ $index }}"
                                                        onclick="updateJobticketRevision('{{ $jobtickets->last()->id }}', '{{ $uprevision }}')">
                                                        <i class="fas fa-arrow-up"></i> {{ $uprevision }}
                                                    </a>
                                                @else
                                                    Tidak bisa, pastikan jobticket terakhir sudah closed
                                                @endif
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No Dokumen</th>
                                    <th>Nama Dokumen</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Jenis Notifikasi</th>
                                    <th>Deskripsi Revisi</th>
                                    <th>Rev</th>
                                    <th>Status</th>
                                    <th>Waktu informasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobtickethistories as $history)
                                    <tr>
                                        <td>{{ $history->newprogressreport->nodokumen ?? 'Tidak ada nama dokumen' }}</td>
                                        <td>{{ $history->newprogressreport->namadokumen ?? 'Tidak ada no dokumen' }}</td>
                                        <td>{{ $history->newprogressreport->documentKind->name ?? 'Tidak ada jenis dokumen' }}
                                        </td>

                                        <td>{{ $history->historykind }}</td>
                                        <td>{{ $history->description }}</td>
                                        <td>{{ $history->newprogressreporthistory->rev ?? 'No rev' }}</td>
                                        <td>
                                            @if($history->status == 'read')
                                                <span class="text-success" title="Terbuka">
                                                    <i class="fas fa-envelope-open"></i>
                                                </span>
                                            @else
                                                <a href="javascript:void(0);" class="change-status"
                                                    data-id="{{ $history->id }}">
                                                    <span class="text-info" title="Tertutup" id="status-{{ $history->id }}">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $history->created_at ? $history->created_at->format('d-m-Y H:i') : 'No date available' }}
                                        </td>




                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No revision history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection


@push('css')
    <!-- CSS Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <!-- Custom Styles -->
    <style>
        .large-swal {
            width: 800px !important;
            /* Lebar yang lebih besar */
            height: auto !important;
            /* Tinggi menyesuaikan isi */
            max-height: 90vh !important;
            /* Batasi agar tidak terlalu tinggi */
            overflow-y: auto !important;
            /* Tambahkan scroll jika isinya terlalu banyak */
        }

        .super-large-swal {
            width: 1200px !important;
            /* Lebar yang lebih besar */
            height: auto !important;
            /* Tinggi menyesuaikan isi */
            max-height: 90vh !important;
            /* Batasi agar tidak terlalu tinggi */
            overflow-y: auto !important;
            /* Tambahkan scroll jika isinya terlalu banyak */
        }

        .d-none {
            display: none !important;
        }

        .table-hover tbody tr.checked {
            background-color: #f0f8ff;
        }

        .table-hover tbody tr.checked td {
            color: #333;
        }

        .badgekhusus {
            display: inline-block;
            padding: 5px 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #28a745;
            border-radius: 20px;
            transition: background-color 0.5s ease, transform 0.3s ease;
        }

        .badgekhusus.paused {
            background-color: #dc3545;
        }

        .badgekhusus.critical {
            background-color: #ffc107;
            color: #000;
        }

        @foreach($jobtickets as $index => $jobticket)
            #elapsed_time_{{ $jobticket->id }} {
                text-align: center;
            }

        @endforeach .badge:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@push('scripts')
    <!-- JavaScript Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>
    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>



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
            @foreach ($jobtickets as $index => $item)
                    @php
                        $id = $item->id;
                        $temporystatus = $item->jobticketStarted;
                        $elapsedSeconds = $temporystatus->total_elapsed_seconds ?? 0;
                        $startTime = $temporystatus->start_time_run ?? null;
                        $pauseTime = $temporystatus->pause_time_run ?? null;
                    @endphp
                    @if($startTime != null && $pauseTime == null)
                        var elapsedTimeElement = document.getElementById('elapsed_time_{{ $id }}');
                        var kondisional = elapsedTimeElement ? elapsedTimeElement.textContent : '';
                        if (kondisional !== "Jeda" && kondisional !== "Completed" && kondisional !== "Time up tidak berjalan") {
                            updateElapsedTime1('{{ $id }}', '{{ $startTime }}', {{ $elapsedSeconds }});
                        }
                    @endif
            @endforeach
        });
    </script>
    <script>




        var encodedDataElement = document.getElementById('encoded-data');
        var listprogressnodokumenDecoded = JSON.parse(encodedDataElement.dataset.listprogressnodokumen);
        listprogressnodokumenDecoded.unshift('');

        var selectedDocuments = {}; // Menggunakan objek untuk menyimpan dokumen yang dipilih

        @foreach($jobtickets as $jobticket)
            selectedDocuments['{{ $jobticket->id }}'] = new Set(); // Set untuk setiap jobticket
        @endforeach

        function updateJobticketRevision(jobticketId, uprevision) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan memperbarui revisi jobticket ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, perbarui!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/jobticket/update-revision/' + jobticketId,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rev: uprevision
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.success,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.error || 'Terjadi kesalahan saat memperbarui revisi.',
                            });
                        }
                    });
                }
            });
        }


        function picknote(jobticketId, documentSupport, urutan, name) {
            var picknoteUrl = "{{ route('jobticket.picknote', ['id' => ':id']) }}".replace(':id', jobticketId);

            // Fungsi untuk memperbarui warna dan teks tombol
            function updateButtonColor() {
                const button = $(`#button_${jobticketId}_${urutan}`);
                button.attr("onclick", `starttugas('${jobticketId}', '${urutan}', '${name}')`)
                    .html('<i class="fas fa-rocket"></i> Start Tugas')
                    .removeClass('bg-gray')
                    .addClass('btn-warning');

                // Ubah tombol Pick Document menjadi abu-abu
                $(`#documentpickerbutton_${jobticketId}_${urutan}`).attr("onclick", `pickdocument('${jobticketId}', '${documentSupport}', '${urutan}', '${name}')`)
                    .removeClass('bg-purple')
                    .addClass('bg-gray');

                // Ubah tombol Pick Note menjadi abu-abu
                $(`#notebutton_${jobticketId}_${urutan}`).removeClass('bg-orange').addClass('bg-gray');
            }
            Swal.fire({
                title: 'Tambahkan Catatan',
                html: '<textarea id="note" class="swal2-textarea" placeholder="Tambahkan catatan (opsional)" rows="10" style="width: 75%;"></textarea>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Simpan catatan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'large-swal'  // Kelas CSS khusus untuk mengatur ukuran
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var note = $('#note').val();  // Ambil input catatan dari Swal

                    $.ajax({
                        url: picknoteUrl,  // Gunakan URL yang benar
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",  // Token CSRF dari meta tag di header
                            note: note  // Kirim catatan
                        },
                        success: function (response) {
                            updateButtonColor();
                            Swal.fire({
                                icon: 'success',
                                title: 'Catatan berhasil ditambahkan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#note_${jobticketId}`).text(note);  // Update catatan di UI
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);  // Log detail kesalahan
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menambahkan catatan: ' + error,
                            });
                        }
                    });
                }
            });
        }


        function pickdocument(jobticketId, documentSupport, urutan, name) {
            // Ambil dokumen dari parameter documentSupport
            var existingDocuments = JSON.parse(documentSupport);

            // Membuat array baru berisi format yang diinginkan
            var existingDocumentValues = existingDocuments.map(doc => {
                return `${doc.id}@${doc.namadokumen}@${doc.nodokumen}@${doc.rev}`; // Format sesuai permintaan
            });

            // Mengisi selectedDocuments dengan existingDocumentValues
            existingDocumentValues.forEach(doc => {
                if (!selectedDocuments[jobticketId]) {
                    selectedDocuments[jobticketId] = new Set(); // Inisialisasi Set jika belum ada
                }
                selectedDocuments[jobticketId].add(doc); // Tambahkan dokumen ke Set
            });

            var listprogressnodokumen = listprogressnodokumenDecoded;

            // Fungsi untuk memperbarui warna dan teks tombol
            function updateButtonColor() {

                const button = $(`#button_${jobticketId}_${urutan}`);
                if (selectedDocuments[jobticketId].size > 0) {
                    // Update tombol menjadi tombol Start Tugas
                    button.attr("onclick", `starttugas('${jobticketId}', '${urutan}', '${name}')`)
                        .html('<i class="fas fa-rocket"></i> Start Tugas')
                        .removeClass('bg-gray')
                        .addClass('btn-warning');

                    // Setelah tombol Start Tugas diaktifkan, ubah Pick Document menjadi abu-abu
                    $(`#documentpickerbutton_${jobticketId}_${urutan}`).attr("onclick", `pickdocument('${jobticketId}', '${documentSupport}', '${urutan}', '${name}')`)
                        .removeClass('bg-purple')
                        .addClass('bg-gray');
                    // Setelah tombol Start Tugas diaktifkan, ubah Pick Document menjadi abu-abu
                    $(`#notebutton_${jobticketId}_${urutan}`).attr("onclick", `pickdocument('${jobticketId}', '${documentSupport}', '${urutan}', '${name}')`)
                        .removeClass('bg-orange')
                        .addClass('bg-gray');
                }


            }



            function loadOptions(searchTerm, pageIndex, pageSize, list) {
                searchTerm = searchTerm.toLowerCase();
                var startIndex = pageIndex * pageSize;
                var endIndex = startIndex + pageSize;
                var filteredList = list.filter(item => item.toLowerCase().includes(searchTerm));
                var optionsHtml = '';

                for (var i = startIndex; i < endIndex && i < filteredList.length; i++) {
                    var listItem = filteredList[i].split('@'); // Memecah string menjadi array
                    var namadoc = listItem[1] || ''; // Ambil nama dokumen dari bagian kedua
                    var nodoc = listItem[2] || ''; // Ambil nama dokumen dari bagian kedua
                    var rev = listItem[3] || ''; // Ambil versi dari bagian ketiga
                    var checked = selectedDocuments[jobticketId]?.has(filteredList[i]) ? 'checked' : ''; // Cek jika dokumen sudah ada

                    optionsHtml += `<label><input type="checkbox" value="${filteredList[i]}" class="progress-checkbox" ${checked}> ${namadoc} - ${nodoc}- ${rev}</label><br>`;
                }
                return optionsHtml;
            }

            var currentPageIndex = 0;
            var pageSize = 5;
            var progressnodokumenOptionsHtml = loadOptions('', currentPageIndex, pageSize, listprogressnodokumen);

            var html = `
                                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                                        <label for="edit-progressnodokumen">Tambahkan Dokumen Referensi</label>
                                                        <div id="checkbox-list" style="max-height: 200px; overflow-y: auto;">
                                                            ${progressnodokumenOptionsHtml}
                                                        </div>
                                                        <input type="text" id="progressnodokumen-search" class="swal2-input" placeholder="Search progress...">
                                                        <div id="selected-documents" style="margin-top: 10px;"></div>
                                                        <div id="progressnodokumen-pagination" style="margin-top: 10px;">
                                                            <button id="prev-progressnodokumen-page" ${currentPageIndex === 0 ? 'disabled' : ''}>Previous</button>
                                                            <button id="next-progressnodokumen-page" ${currentPageIndex * pageSize + pageSize >= listprogressnodokumen.length ? 'disabled' : ''}>Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;

            function updateSelectedDocuments() {
                var selectedHtml = Array.from(selectedDocuments[jobticketId] || []).map(doc => `<li>${doc}</li>`).join('');
                document.getElementById('selected-documents').innerHTML = selectedHtml ? `<ul>${selectedHtml}</ul>` : '';
            }

            Swal.fire({
                title: "Dokumen Referensi",
                html: html,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                customClass: {
                    popup: 'large-swal'  // Kelas CSS khusus untuk mengatur ukuran
                },
                preConfirm: () => {
                    return Array.from(selectedDocuments[jobticketId] || []);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var newDocument = result.value;
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
                            var updateUrl = `/jobticket/updatesupportdocument/${jobticketId}/`;
                            $.ajax({
                                url: updateUrl,
                                method: 'PUT',
                                data: {
                                    documentsupport: newDocument,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data berhasil diperbarui!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });

                                    // Kosongkan selectedDocuments dan perbarui dengan dokumen baru dari server
                                    selectedDocuments[jobticketId].clear();
                                    response.data.forEach(function (doc) {
                                        var formattedDoc = `${doc.id}@${doc.namadokumen}@${doc.nodokumen}@${doc.rev}`;
                                        selectedDocuments[jobticketId].add(formattedDoc);
                                    });

                                    // Update bagian dokumen tanpa reload
                                    var container = $('#document-support-container-' + urutan);
                                    container.html('');
                                    response.data.forEach(function (doc) {
                                        container.append(`<span class="badge bg-info">${doc.namadokumen} - ${doc.nodokumen} - ${doc.rev}</span><br>`);
                                    });

                                    // Update tombol warna setelah dokumen diperbarui
                                    updateButtonColor();
                                    updateSelectedDocuments();
                                },
                                error: function (xhr, status, error) {
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

            document.getElementById('progressnodokumen-search').addEventListener('input', function () {
                var searchTerm = this.value;
                var filteredOptionsHtml = loadOptions(searchTerm, currentPageIndex, pageSize, listprogressnodokumen);
                document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                updateSelectedDocuments(); // Update selected documents list
            });

            document.getElementById('prev-progressnodokumen-page').addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    var filteredOptionsHtml = loadOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen);
                    document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                    updateSelectedDocuments();
                    document.getElementById('prev-progressnodokumen-page').disabled = currentPageIndex === 0; // Disable button if on first page
                    document.getElementById('next-progressnodokumen-page').disabled = (currentPageIndex * pageSize + pageSize) >= listprogressnodokumen.length; // Disable if last page
                }
            });

            document.getElementById('next-progressnodokumen-page').addEventListener('click', function () {
                if ((currentPageIndex + 1) * pageSize < listprogressnodokumen.length) {
                    currentPageIndex++;
                    var filteredOptionsHtml = loadOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen);
                    document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                    updateSelectedDocuments();
                    document.getElementById('prev-progressnodokumen-page').disabled = currentPageIndex === 0; // Disable button if on first page
                    document.getElementById('next-progressnodokumen-page').disabled = ((currentPageIndex + 1) * pageSize) >= listprogressnodokumen.length; // Disable if last page
                }
            });

            document.getElementById('checkbox-list').addEventListener('change', function (event) {
                if (event.target.classList.contains('progress-checkbox')) {
                    var checkboxValue = event.target.value;
                    if (!selectedDocuments[jobticketId]) {
                        selectedDocuments[jobticketId] = new Set(); // Inisialisasi Set jika belum ada
                    }
                    if (event.target.checked) {
                        selectedDocuments[jobticketId].add(checkboxValue);
                    } else {
                        selectedDocuments[jobticketId].delete(checkboxValue);
                    }
                    updateSelectedDocuments();

                }
            });


        }

        function pickposition(id, jobticket, posisitable, rule) {
            const users = @json($availableUsers);

            const positionOptions = rule.includes('Quality Engineering')
                ? ['checker', 'approver']
                : rule.includes('Electrical Engineering System')
                    ? ['drafter', 'checker', 'approver']
                    : ['checker', 'approver'];

            const userOptionsHtml = (position) => users
                .filter(user => {
                    if (rule.includes('Quality Engineering') && position === 'checker') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [149, 137, 178, 139].includes(user.id);
                    } else if (rule.includes('Quality Engineering') && position === 'approver') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [94].includes(user.id);
                    } else if (rule.includes('Electrical Engineering System') && position === 'approver') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [27].includes(user.id);
                    }
                    return true; // Semua pengguna untuk peran lainnya
                })
                .reduce((html, user) => html + `<option value="${user.id}">${user.name}</option>`, '');

            const selectedIds = {
                checker: jobticket.checker_id ?? '',
                approver: jobticket.approver_id ?? '',
                drafter: jobticket.drafter_id ?? '',
            };

            const htmlContent = positionOptions.map(position => `
                                                <div style="margin-top: 10px;">
                                                    <label for="${position}Select">Pilih ${position.charAt(0).toUpperCase() + position.slice(1)}:</label>
                                                    <select id="${position}Select" class="swal2-select" value="${selectedIds[position]}">
                                                        ${userOptionsHtml(position)}
                                                    </select>
                                                </div>
                                            `).join('');

            Swal.fire({
                title: 'Pilih PIC',
                html: htmlContent,
                didOpen: () => positionOptions.forEach(position => {
                    document.getElementById(`${position}Select`).value = selectedIds[position];
                }),
                showCancelButton: true,
                confirmButtonText: 'Pilih',
                cancelButtonText: 'Batal',
                customClass: { popup: 'large-swal' },
                preConfirm: () => positionOptions.reduce((data, position) => {
                    data[`${position}Id`] = document.getElementById(`${position}Select`)?.value;
                    return data;
                }, {}),
            }).then(result => {
                if (result.isConfirmed) {
                    const { checkerId, approverId, drafterId } = result.value;
                    const selectedNames = {
                        checker: users.find(user => user.id == checkerId)?.name,
                        approver: users.find(user => user.id == approverId)?.name,
                        drafter: users.find(user => user.id == drafterId)?.name,
                    };

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin mengambil pekerjaan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, ambil job ini!',
                        cancelButtonText: 'Batal',
                    }).then(confirmResult => {
                        if (confirmResult.isConfirmed) {
                            $.ajax({
                                url: `/jobticket/pickdraftercheckerapprover/${id}`,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    checker_id: checkerId,
                                    approver_id: approverId,
                                    drafter_id: drafterId,
                                },
                                success: response => {
                                    $(`#checker_${id} .badge`).text(selectedNames.checker);
                                    $(`#approver_${id} .badge`).text(selectedNames.approver);
                                    if (drafterId) {
                                        $(`#drafter_${id} .badge`).text(selectedNames.drafter);
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pekerjaan berhasil diambil!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                error: xhr => {
                                    const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil pekerjaan ini.';
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: errorMessage,
                                    });
                                }
                            });

                        }
                    });
                }
            });
        }


        function picktugas(id, posisitable, name, kindposition) {
            var picktugasUrl = `/jobticket/picktugas/${id}/${name}/${kindposition}`;

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
                    $.ajax({
                        url: picktugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (kindposition === "drafter") {
                                // Update nama drafter dan sesuaikan tombolnya
                                $(`#drafter_${id} .badge`).text(name);
                                $(`#button_${id}_${posisitable}`)
                                    .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                    .html('<i class="fas fa-rocket"></i> Start Tugas')
                                    .removeClass('btn-success')
                                    .addClass('bg-gray');

                                // Ubah warna document picker menjadi maroon
                                $(`#documentpickerbutton_${id}_${posisitable}`)
                                    .removeClass('bg-gray')
                                    .addClass('bg-purple');
                                $(`#notebutton_${id}_${posisitable}`)
                                    .removeClass('bg-gray')
                                    .addClass('bg-orange');
                            } else if (kindposition === "checker") {
                                // Update nama checker dan hapus tombol checker
                                $(`#checker_${id} .badge`).text(name);
                                $(`#checkerbutton_${id}_${posisitable}`).remove();
                            } else if (kindposition === "approver") {
                                // Update nama approver dan hapus tombol approver
                                $(`#approver_${id} .badge`).text(name);
                                $(`#approverbutton_${id}_${posisitable}`).remove();
                            }


                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil diambil!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function (xhr, status, error) {
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
            var starttugasUrl = `/jobticket/starttugas/${id}/${name}`;

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
                                .html('<i class="fas fa-pause-circle"></i> Jeda')
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


        var encodeddatamemo = JSON.parse(document.getElementById('encoded-data-memo').dataset.listprogressnodokumen);

        function pausetugas(id, posisitable, name) {
            var pausetugasUrl = `/jobticket/pausetugas/${id}/${name}`;

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
                    Swal.fire({
                        title: 'Masukkan Alasan, Kind, dan Upload Bukti',
                        html:
                            '<div id="reason-container"><input id="reason" class="swal2-input" placeholder="Masukkan alasan"></div>' +
                            '<select id="kind" class="swal2-input">' +
                            '<option value="">Pilih Jenis Izin (Opsional)</option>' +
                            '<option value="memo">Memo</option>' +
                            '<option value="dinas">Dinas</option>' +
                            '<option value="support">Support</option>' +
                            '</select>' +
                            '<select id="kind_id" class="swal2-input" style="display: none;"></select>' +
                            '<div id="file-container"><input type="file" id="file" class="swal2-input" multiple></div>',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Jeda Tugas',
                        customClass: {
                            popup: 'super-large-swal'
                        },
                        preConfirm: () => {
                            const reason = document.getElementById('reason').value;
                            const kind = document.getElementById('kind').value;
                            const kind_id = document.getElementById('kind_id').value;
                            const files = document.getElementById('file').files;

                            if (kind !== 'memo' && !reason) {
                                Swal.showValidationMessage('Alasan harus diisi!');
                                return false;
                            }

                            return { reason, kind, kind_id, files };
                        }
                    }).then((inputResult) => {
                        if (inputResult.isConfirmed) {
                            const { reason, kind, kind_id, files } = inputResult.value;

                            var formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            formData.append('jobticket_id', id);
                            formData.append('reason', reason);
                            formData.append('kind', kind);
                            formData.append('kind_id', kind_id);

                            if (kind !== 'memo') {
                                for (let i = 0; i < files.length; i++) {
                                    formData.append('file[]', files[i]);
                                }
                            }

                            $.ajax({
                                url: pausetugasUrl,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pekerjaan berhasil dijeda!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $(`#button_${id}_${posisitable}`)
                                        .attr("onclick", `resumetugas('${id}','${posisitable}', '${name}')`)
                                        .html('<i class="fas fa-play-circle"></i> Lanjutkan')
                                        .removeClass('btn-secondary')
                                        .addClass('btn-primary');

                                    var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                                    elapsedTimeElement.textContent = "Jeda";

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

                    document.getElementById('kind').addEventListener('change', function () {
                        const kind = this.value;
                        const kind_id_select = document.getElementById('kind_id');
                        const reasonContainer = document.getElementById('reason-container');
                        const fileContainer = document.getElementById('file-container');

                        if (kind === 'memo') {
                            kind_id_select.style.display = 'block';
                            reasonContainer.style.display = 'none';
                            fileContainer.style.display = 'none';
                            kind_id_select.innerHTML = '';

                            for (const [id, documentname] of Object.entries(encodeddatamemo)) {
                                kind_id_select.innerHTML += `<option value="${id}">${documentname}</option>`;
                            }
                        } else {
                            kind_id_select.style.display = 'none';
                            reasonContainer.style.display = 'block';
                            fileContainer.style.display = 'block';
                            kind_id_select.innerHTML = '';
                        }
                    });
                }
            });
        }

        function resumetugas(id, posisitable, name) {
            var resumetugasUrl = `/jobticket/resumetugas/${id}/${name}`;

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
                                .html('<i class="fas fa-pause-circle"></i> Jeda')
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
            var selesaitugasUrl = `/jobticket/selesaitugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyelesaikan pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, selesaikan pekerjaan ini!',
                cancelButtonText: 'Batal',
                html: `
                                                    <p>Upload file (Wajib):</p>
                                                    <input type="file" id="fileInput" class="swal2-file" accept=".pdf,.doc,.docx,.jpg,.png">
                                                `
            }).then((result) => {
                if (result.isConfirmed) {
                    var fileInput = document.getElementById("fileInput");
                    var file = fileInput.files[0];

                    // Validasi file tidak boleh kosong atau 0 byte
                    if (!file || file.size === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File tidak valid',
                            text: 'Harap unggah file yang tidak kosong.',
                        });
                        return; // Menghentikan proses jika file tidak valid
                    }

                    var formData = new FormData();
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("file", file);

                    $.ajax({
                        url: selesaitugasUrl,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan',
                                    text: response.error,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pekerjaan berhasil diselesaikan!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                $(`#button_${id}_${posisitable}`).remove();

                                $(`#button_${id}_${posisitable}`).removeClass('btn-danger').addClass('bg-gray');

                                var revisionElement = document.getElementById('revision_' + id);
                                if (revisionElement) {
                                    revisionElement.textContent = response.lastKey || "update";
                                }
                                var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                                elapsedTimeElement.textContent = "Selesai";

                                var detailButton = $(`#buttondetailtugas_${id}_${posisitable}`);
                                detailButton.removeClass('bg-gray').addClass('bg-maroon');

                                if (intervals[id]) {
                                    clearInterval(intervals[id]);
                                }
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function izinkanrevisitugas(id, posisitable, name) {
            var resetTugasUrl = `/jobticket/izinkanrevisitugas/${id}/${name}`;

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

        function detailtugas(jobticket_identity_part, jobticket_identity_id, jobticket_id) {
            var detailUrl = `/jobticket/show/${jobticket_identity_part}/${jobticket_identity_id}/${jobticket_id}`;

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

        function deletetugas(jobticket_identity_part, jobticket_identity_id, jobticket_id) {
            var detailUrl = `/jobticket/deletejobticket/${jobticket_identity_part}/${jobticket_identity_id}/${jobticket_id}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus jobticket ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = detailUrl;
                }
            });
        }

    </script>

    <script>
        document.querySelectorAll('.change-status').forEach(function (element) {
            element.addEventListener('click', function () {
                var historyId = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Confirm',
                    text: "Sudahkah anda mengetahui hal ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, saya sudah membacanya',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim permintaan AJAX untuk mengubah status menjadi "read"
                        $.ajax({
                            url: `/jobticket/statushistory/${historyId}/mark-as-read`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Ubah ikon dan status menjadi "read" tanpa refresh
                                    var statusElement = document.getElementById('status-' + historyId);
                                    statusElement.innerHTML = '<i class="fas fa-envelope-open text-success"></i>';
                                    Swal.fire('Updated!', 'Dokumen telah ditandai sebagai sudah dibaca.', 'success');
                                } else {
                                    Swal.fire('Error', 'Terjadi kesalahan, coba lagi.', 'error');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush