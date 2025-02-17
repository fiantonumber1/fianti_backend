@extends('layouts.table1showprogressreport')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container1') 

    <div id="encoded-data" data-listprogressnodokumen="{{ $listprogressnodokumenencode }}"></div>
    <div class="col-sm-6">
        <h1>Detail Progres Dokumen</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Detail Progres Dokumen</li>
        </ol>
    </div>
    
@endsection

@section('container2')  

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="true">Progress</a>
        </li>
        <!-- <li class="nav-item" role="presentation">
            <a class="nav-link" id="laporan-tanggal-tab" data-toggle="tab" href="#laporan-tanggal" role="tab" aria-controls="laporan-tanggal" aria-selected="false">Laporan Tanggal</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="member-tab" data-toggle="tab" href="#member" role="tab" aria-controls="member" aria-selected="false">Pembagian Tugas</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="duplicate-tab" data-toggle="tab" href="#duplicate" role="tab" aria-controls="duplicate" aria-selected="false">Duplikat</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="Treediagram-tab" data-toggle="tab" href="#Treediagram" role="tab" aria-controls="Treediagram" aria-selected="false">Treediagram</a>
        </li>   -->
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
                                            <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2" style="max-width: 250px">
                                        </td>
                                        <td rowspan="7" style="width: 50%">
                                            <h1 class="text-xl text-center mt-2">DAFTAR DOKUMEN & GAMBAR</h1>
                                        </td>
                                        <td style="width: 25%" class="p-1">Project: <b>{{ ucwords(str_replace('-', ' ', $newreport->proyek_type)) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%" class="p-1">Bagian: <b>{{ ucfirst($newreport->unit) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b></td>
                                    </tr>
                                    <tr>
                                    @php
                                        // Menentukan nilai persentase berdasarkan kondisi tertentu
                                        $progressPercentageFormatted = number_format($progresspercentage, 2);

                                        if (session('internalon')) {
                                            // Jika 'internalon' di sesi aktif
                                            $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                                            $unrelease=$countunrelease;
                                            $release=$countrelease;
                                        } else {
                                            // Jika 'internalon' di sesi tidak aktif
                                            $unitsWithFullPercentage = ["Sistem Mekanik", "Desain Interior", "Desain Carbody"];
                                            
                                            if (in_array($newreport->unit, $unitsWithFullPercentage) || 
                                                ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI")) {
                                                $nilaipersentase = '100%';
                                                $unrelease=0;
                                                $release=$countunrelease+$countrelease;
                                            } else {
                                                $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                                                $unrelease=$countunrelease;
                                                $release=$countrelease;
                                            }
                                        }

                                    @endphp
                                    @php
                                        if ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "KCI" ){
                                            $counting=9;
                                            $documentrelease=9;
                                            $documentunrelease=0;
                                            $batas=10;
                                            
                                        }elseif ($newreport->unit == "Electrical Engineering System" && $newreport->proyek_type == "KCI" ){
                                            $counting=52;
                                            $documentrelease=52;
                                            $documentunrelease=0;
                                            $batas=53;

                                        }elseif ($newreport->unit == "Mechanical Engineering System" && $newreport->proyek_type == "KCI" ){
                                            $counting=38;
                                            $documentrelease=38;
                                            $documentunrelease=0;
                                            $batas=39;

                                        }elseif ($newreport->unit == "Quality Engineering" && $newreport->proyek_type == "KCI" ){
                                            $counting=194;
                                            $documentrelease=165;
                                            $documentunrelease=29;
                                            $batas=195;

                                        }elseif ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI" ){
                                            $counting=159;
                                            $documentrelease=159;
                                            $documentunrelease=0;
                                            $batas=160;

                                        }elseif ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI" ){
                                            $counting=229;
                                            $documentrelease=229;
                                            $documentunrelease=0;
                                            $batas=230;

                                        }elseif ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI" ){
                                            $counting=1075; 
                                            $documentrelease=1075;
                                            $documentunrelease=0;
                                            $batas=1076;

                                        }elseif ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI" ){
                                            $counting=538; 
                                            $documentrelease=538; 
                                            $documentunrelease=0;
                                            $batas=539; 

                                        }elseif ($newreport->unit == "Desain Elektrik" && $newreport->proyek_type == "KCI" ){
                                            $counting=232; 
                                            $documentrelease=211; 
                                            $documentunrelease=21;
                                            $batas=233; 

                                        }elseif ($newreport->unit == "Shop Drawing" && $newreport->proyek_type == "KCI" ){
                                            $counting=89; 
                                            $documentrelease=89;
                                            $documentunrelease=0;
                                            $batas=90;

                                        }elseif ($newreport->unit == "Preparation & Support" && $newreport->proyek_type == "KCI" ){
                                            $counting=57; 
                                            $documentrelease=57;
                                            $documentunrelease=0;
                                            $batas=58;

                                        }elseif ($newreport->unit == "Welding Technology" && $newreport->proyek_type == "KCI" ){
                                            $counting=197; 
                                            $documentrelease=197; 
                                            $documentunrelease=0;
                                            $batas=198; 

                                        }elseif ($newreport->unit == "Teknologi Proses" && $newreport->proyek_type == "KCI" ){
                                            $counting=534; 
                                            $documentrelease=507; 
                                            $documentunrelease=27;
                                            $batas=535; 

                                        }elseif ($newreport->unit == "Welding Technology" && $newreport->proyek_type == "KCI" ){
                                            $counting=197; 
                                            $documentrelease=197; 
                                            $documentunrelease=0;
                                            $batas=198; 

                                        }else{
                                            $counting=$progressReports->count();
                                            $documentrelease=$release;
                                            $documentunrelease=$unrelease;
                                            $batas=19400;
                                        }
                                    @endphp
                                    @php
                                        $nilaipersentase=number_format($documentrelease/$counting*100)." %";
                                    @endphp

                                        <td style="width: 25%" class="p-1">
                                            Progres: <b><span class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}" style="font-size: 2rem;">{{ $nilaipersentase }}</span></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%" class="p-1">
                                            <button class="btn btn-danger" id="btn-unrelease">Dokumen Unreleased: <b>
                                                <span class="badge badge-danger" style="font-size: 1.5rem;">{{$documentunrelease}}</span>
                                            </b></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%" class="p-1">
                                            <button class="btn btn-success" id="btn-release">Dokumen Released: <b><span class="badge badge-success" style="font-size: 1.5rem;">{{$documentrelease}}</span></b></button>
                                        </td>
                                    </tr>
                                        
                                    
                                    <tr>
                                        <td style="width: 25%" class="p-1">
                                            <button class="btn btn-info" id="btn-total">Total Dokumen: <b><span class="badge badge-info" style="font-size: 1.5rem;">{{$counting}}</span></b></button>
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
                                    <a href="#" class="btn btn-primary mt-2 mr-2" onclick="tambahdata('{{ $newreport->id }}','{{ json_encode($listanggota) }}')">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>
                                    <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-default bg-purple mt-2 mr-2" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </form>
                                    @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                        <div class="col-md-3 col-sm-6 col-12">
                                            <button type="button" class="btn btn-danger btn-sm btn-block mt-2" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                        </div>
                                    @endif
                                    @if ($useronly->rule == "MTPR" || $useronly->rule == "superuser")
                                        <div class="col-md-3 col-sm-6 col-12">
                                            <button type="button" class="btn btn-info btn-sm btn-block mt-2" onclick="handleReleaseMultipleItems()">Release yang dipilih</button>
                                        </div>
                                    @endif
                                    @if(session('internalon'))
                                        <button id="internalOffButton" class="btn btn-success mt-2 btn-borderless mr-2">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button id="internalButton" class="btn btn-default bg-white mt-2 btn-borderless d-none"></button>
                                    @else
                                        <button id="internalOffButton" class="btn btn-success mt-2 btn-borderless d-none mr-2">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button id="internalButton" class="btn btn-default bg-white mt-2 btn-borderless"></button>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            
                            <div id="default-table">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                            </th>
                                            <th scope="col">No</th>
                                            <th scope="col">No Dokumen All</th>
                                            <th scope="col">Nama Dokumen All</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Drafter</th>
                                            <th scope="col">Checker</th>
                                            <th scope="col">Deadline Release</th>
                                            <th scope="col">Realisasi</th>
                                            <th scope="col">Jenis Dokumen</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Dokumen Pendukung</th>
                                            <th scope="col">Edit</th>
                                            @if($useronly=="superuser")
                                                <th scope="col">Waktu Mulai</th>
                                                <th scope="col">Countup</th>
                                                <th scope="col">Revisi ke (Pada sistem)</th>
                                            @endif

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $penghitung = 1; // Inisialisasi penghitung
                                        @endphp
                                        @php
                                            $loopCount = 0; // Inisialisasi penghitung loop
                                            
                                            // Pisahkan progress reports berdasarkan status
                                            $progressReportsrelease = collect($progressReports)->filter(function($progressReport) {
                                                return $progressReport->status === 'RELEASED';
                                            })->take($documentrelease);

                                            $progressReportsunrelease = collect($progressReports)->filter(function($progressReport) {
                                                return $progressReport->status !== 'RELEASED';
                                            })->take($documentunrelease);

                                            // Gabungkan kembali hasil filter
                                            $progressReports = $progressReportsrelease->merge($progressReportsunrelease);
                                        @endphp
                                        

                                        
                                        @foreach ($progressReports as $index =>$progressReport)
                                            @php
                                                $loopCount += 1; // Inisialisasi penghitung loop
                                            @endphp
                                            @if ($loopCount >= $batas)
                                                @break
                                            @endif
                                            <tr>
                                                <td>
                                                    <div class="icheck-primary">
                                                        <input type="checkbox" value="{{ $progressReport->id }}" name="document_ids[]" id="checkbox{{ $progressReport->id }}"
                                                            onchange="handleCheckboxChange(this)">
                                                        <label for="checkbox{{ $progressReport->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $penghitung }}</td>
                                                <td id="nodokumen_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->nodokumen }}</td>
                                                <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->namadokumen }}</td>
                                                <td id="level_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->level}}</td>
                                                <td id="drafter_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->drafter??""}}</td>
                                                <td id="checker_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->checker ?? "" }}</td>
                                                <td id="deadlinerelease_{{ $progressReport->id }}_{{ $index }}">
                                                    {{$progressReport->deadlinerelease}}
                                                </td>
                                                <td id="realisasi_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->realisasi ?? "" }}</td>
                                                <td id="documentkind_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->documentkind?? "" }}</td>
                                                
                                                @php
                                                    if(session('internalon')){
                                                        $status=$progressReport->status?? "";
                                                        
                                                    }else{
                                                        if($newreport->unit=="Sistem Mekanik"||$newreport->unit=="Desain Interior"||$newreport->unit=="Desain Carbody"){
                                                            $status='REALESED';
                                                        }elseif($newreport->unit=="Desain Bogie & Wagon" && $newreport->proyek_type=="KCI"){
                                                            $status='REALESED';
                                                        }else{
                                                            $status=$progressReport->status?? "";
                                                        }
                                                    }
                                                @endphp

                                                <td id="status_{{ $progressReport->id }}_{{ $index }}">{{ $status }}</td>
                                                <td id="supportdocument_{{ $progressReport->id }}">
                                                    @if($progressReport->children->count()>0)
                                                        @foreach ($progressReport->children as $anak)
                                                            <div class="badge badge-combined">
                                                                <span class="badge-section badge-danger">
                                                                    {{ $anak->namadokumen??"" }}
                                                                </span>
                                                                <span class="badge-section badge-primary">
                                                                    {{ $anak->nodokumen??"" }}
                                                                </span>
                                                                <span class="badge-section badge-success">
                                                                    {{ $anak->status??"" }}
                                                                </span>
                                                                <a href="#" class="badge-section badge-info" onclick="unlink('{{ $anak->id }}')">
                                                                    <i class="fas fa-eraser"></i> Unlink
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="badge badge-warning">Tidak ada dokumen pendukung</span>
                                                    @endif
                                                </td>

                                                @php
                                                    $hasilwaktu=json_decode($progressReport->temporystatus,true);
                                                @endphp
                                                <td>
                                                    @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == "MTPR" || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                        <a href="#" class="btn btn-info btn-sm d-block mb-1" onclick="showDocumentSummary('{{ json_encode($progressReport) }}', '{{ $progressReport->id }}', '{{ $index }}', '{{ json_encode($listanggota) }}', '{{ $useronly->rule }}')">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>

                                                        <a href="#" class="btn btn-default bg-kakhi d-block mb-1" onclick="opendeleteForm('{{ $progressReport->id }}','{{ $index }}')">
                                                            <i class="fas fa-eraser"></i> Delete
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="#" class="btn btn-default bg-maroon d-block mb-1" id="buttondetailtugas_{{ $progressReport->id }}_{{ $index }}" onclick="detailtugas('{{ $progressReport->id }}')">
                                                        <i class="fas fa-info-circle"></i> Detail
                                                    </a>

                                                    @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == $newreport->unit || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                        @if(!isset($hasilwaktu['start_time']))
                                                            @php
                                                                $statusrevisi = $hasilwaktu['statusrevisi'] ?? "dibuka";
                                                            @endphp

                                                            @if($progressReport->drafter == "-" || $progressReport->drafter == "" || $progressReport->drafter == null)
                                                                @if(!isset($indukan[strval($progressReport->id)]["persen"]))
                                                                    <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="picktugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                        <i class="fas fa-hand-pointer"></i> Pick Tugas (Tanpa Dokumen Pendukung)
                                                                    </a>
                                                                @elseif(isset($indukan[strval($progressReport->id)]["persen"])&& ($indukan[strval($progressReport->id)]["persen"]['count'] != $indukan[strval($progressReport->id)]["persen"]['countrelease']))
                                                                    <a href="#" class="btn btn-default bg-pink d-block mb-1" id="button">
                                                                        <i class="fas fa-hand-pointer"></i> Dokumen Pendukung Belum Release
                                                                    </a>
                                                                @elseif(isset($indukan[strval($progressReport->id)]["persen"])&& ($indukan[strval($progressReport->id)]["persen"]['count'] == $indukan[strval($progressReport->id)]["persen"]['countrelease']))
                                                                    <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="picktugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                        <i class="fas fa-hand-pointer"></i> Pick Tugas
                                                                    </a>
                                                                @endif
                                                            @else
                                                                @if($statusrevisi != "ditutup")
                                                                    @if($progressReport->drafter == $useronly->name)
                                                                        <a href="#" class="btn btn-warning btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="starttugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                            <i class="fas fa-rocket"></i> Start Tugas
                                                                        </a>
                                                                    @else
                                                                        <a href="#" class="btn btn-default bg-white d-block mb-1" id="button">
                                                                            <i class="fas fa-hand-pointer"></i> Tugas Milik Orang
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                                        <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="izinkanrevisitugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                            <i class="fas fa-edit"></i> Izinkan Revisi
                                                                        </a>
                                                                    @endif

                                                                @endif
                                                                
                                                                
                                                            @endif
                                                        @else
                                                            @if($useronly->name == $progressReport->drafter)
                                                                @if($hasilwaktu['pause_time'] == null)
                                                                    <a href="#" class="btn btn-secondary btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="pausetugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                        <i class="fas fa-pause-circle"></i> Pause
                                                                    </a>
                                                                @else
                                                                    <a href="#" class="btn btn-primary btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="resumetugas('{{ $progressReport->id }}',  '{{ $index }}', '{{ $useronly->name }}')">
                                                                        <i class="fas fa-play-circle"></i> Resume
                                                                    </a>
                                                                @endif

                                                                <a href="#" class="btn btn-danger btn-sm d-block mb-1" id="button_{{ $progressReport->id }}_{{ $index }}" onclick="selesaitugas('{{ $progressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                                    <i class="fas fa-check-circle"></i> Selesai
                                                                </a>
                                                            @endif
                                                        @endif

                                                        @if($useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                            <a href="#" class="btn btn-default bg-purple d-block mb-1" id="reset_button_{{ $progressReport->id }}_{{ $index }}" onclick="resettugas('{{ $progressReport->id }}',  '{{ $index }}', '{{ $useronly->name }}')">
                                                                <i class="fas fa-exclamation-circle"></i> Reset
                                                            </a>
                                                        @endif
                                                    @endif
                                                    <a href="#" class="btn btn-danger btn-sm d-block mb-1 d-none" id="selesai_button_{{ $progressReport->id }}_{{ $index }}" onclick="selesaitugas('{{ $progressReport->id }}', '{{ $progressReport->namadokumen }}', '{{ $progressReport->nodokumen }}', '{{ $index }}', '{{ $useronly->name }}')">
                                                        <i class="fas fa-check-circle"></i> Selesai
                                                    </a>
                                                </td>

                                                @if($useronly=="superuser")
                                                
                                                    @php
                                                        $utc_time = $hasilwaktu['start_time_run'] ?? "Belum Ada";
                                                        
                                                        // Convert UTC time to Asia/Jakarta timezone
                                                        if ($utc_time != "Belum Ada") {
                                                            $date = new DateTime($utc_time, new DateTimeZone('UTC'));
                                                            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                            $waktuindo = $date->format('d/m/Y');
                                                        } else {
                                                            $waktuindo = "Belum Ada";
                                                        }

                                                        // Display formatted date
                                                        echo "<td>$waktuindo</td>";

                                                        // Calculate total time
                                                        $elapsedSeconds = $hasilwaktu['total_elapsed_seconds'] ?? 0;
                                                        $startTime = $hasilwaktu['start_time'] ?? null;
                                                        $pauseTime = $hasilwaktu['pause_time'] ?? null;
                                                        $currentTime = Carbon::now();
                                                        $totalTime = 0;

                                                        if ($startTime !== null) {
                                                            $startTime = Carbon::parse($startTime); // Convert to Carbon object

                                                            if ($pauseTime !== null) {
                                                                $pauseTime = Carbon::parse($pauseTime); // Convert to Carbon object
                                                            }

                                                            if ($pauseTime === null) {
                                                                $totalTime = $currentTime->diffInSeconds($startTime) + $elapsedSeconds;
                                                            } else {
                                                                $totalTime = $pauseTime->diffInSeconds($startTime) + $elapsedSeconds;
                                                            }
                                                        }
                                                    @endphp

                                                

                                                    <td id="elapsed_time_{{ $progressReport->id }}">
                                                        @if($startTime !== null)
                                                            {{ gmdate('H:i:s', $totalTime) }}
                                                        @endif
                                                    </td>

                                                    

                                                    <td id="revision_{{ $progressReport->id }}">
                                                        {{ $hasilwaktu['revisionlast'] ?? "Belum ada" }}
                                                    </td>
                                                    
                                                @endif
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
            </div>
 
        </div>

        <!-- Laporan Tanggal Tab Content -->
        
    </div>
    
    <style>
        .table-hover tbody tr.checked {
            background-color: #f0f8ff; /* Warna biru muda */
        }

        .table-hover tbody tr.checked td {
            color: #333; /* Warna teks untuk kontras */
        }
    </style>

    <script>
        document.getElementById('btn-unrelease').addEventListener('click', function() {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.remove('d-none');
        });

        document.getElementById('btn-release').addEventListener('click', function() {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.remove('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });

        document.getElementById('btn-total').addEventListener('click', function() {
            document.getElementById('default-table').classList.remove('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I=" crossorigin="anonymous"></script>
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
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
        document.getElementById('internalButton').addEventListener('click', function() {
            
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
                            success: function() {
                                location.reload();
                                // Reveal the hidden elements
                                document.querySelectorAll('.badge-warning.d-none').forEach(element => {
                                    element.classList.remove('d-none');
                                });
                                document.querySelectorAll('.badge-success.d-1').forEach(element => {
                                    element.classList.add('d-none');
                                });
                            },
                            error: function() {
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

        document.getElementById('internalOffButton').addEventListener('click', function() {
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
                        success: function() {
                            location.reload();
                            // Reveal the hidden elements
                            document.querySelectorAll('.badge-warning').forEach(element => {
                                element.classList.add('d-none');
                            });
                            document.querySelectorAll('.badge-success.d-1.d-none').forEach(element => {
                                element.classList.remove('d-none');
                            });
                        },
                        error: function() {
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
            intervals[id] = setInterval(function() {
                updateElapsedTime();
                console.log(`Updated time for id ${id}:`, elapsedTimeElement.textContent);
            }, 1000);
        }

        // Event listener when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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

@endsection

@section('script') 

    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil diunlink!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function(xhr, status, error) {
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

        function tambahdata(id, listproject) {
            var newreportid = id;
            listproject = JSON.parse(listproject);
            var drafterOptions = '';
            listproject.forEach(function(project) {
                drafterOptions += `<option value="${project}">${project}</option>`;
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
                    <label for="tambah-level" style="flex: 1;">Level</label>
                    <select id="tambah-level" class="swal2-input" style="flex: 2;">
                        <option value="-">-</option>
                        <option value="Predesign">Predesign</option>
                        <option value="Intermediate Design">Intermediate Design</option>
                        <option value="Final Design">Final Design</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label for="tambah-drafter">Drafter</label>
                    <select id="tambah-drafter" class="swal2-input">
                        ${drafterOptions}
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label for="tambah-checker">Checker</label>
                    <select id="tambah-checker" class="swal2-input">
                        ${drafterOptions}
                    </select>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-deadlinerelease" style="flex: 1;">Deadline Release</label>
                    <input id="tambah-deadlinerelease" class="swal2-input" placeholder="Deadline Release" style="flex: 2;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-realisasi" style="flex: 1;">Realisasi</label>
                    <input id="tambah-realisasi" class="swal2-input" placeholder="Realisasi" style="flex: 2;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-status" style="flex: 1;">Status Dokumen</label>
                    <select id="tambah-status" class="swal2-input" style="flex: 2;">
                        <option value="RELEASED">RELEASED</option>
                        <option value="Working Progress">Working Progress</option>
                        <option value="-">-</option>
                    </select>
                </div>
            </div>
        `,
                focusConfirm: false,
                confirmButtonText: 'Tambah',
                preConfirm: () => {
                    return {
                        newreport_id: newreportid,
                        nodokumen: document.getElementById("tambah-no-dokumen").value,
                        namadokumen: document.getElementById("tambah-nama-dokumen").value,
                        level: document.getElementById("tambah-level").value,
                        drafter: document.getElementById("tambah-drafter").value,
                        checker: document.getElementById("tambah-checker").value,
                        deadlinerelease: document.getElementById("tambah-deadlinerelease").value,
                        realisasi: document.getElementById("tambah-realisasi").value,
                        status: document.getElementById("tambah-status").value,
                        _token: '{{ csrf_token() }}' // Token CSRF
                    };
                }
            }).then((formValues) => {
                if (formValues.value) {
                    $.ajax({
                        url:  `/newreports/${id}/progressreports`,
                        method: 'POST',
                        data: formValues.value,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Tidak perlu reload halaman, cukup bersihkan formulir atau tampilkan pesan sukses
                            // location.reload();
                        },
                        error: function(xhr, status, error) {
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
                var filteredList = list.filter(function(item) {
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
                    ${user === "MTPR"|| user === "superuser"? `
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
                        ...(user === "MTPR" ||user === "superuser"? [
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
                            success: function(response) {
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
                            error: function(xhr, status, error) {
                                console.error('Terjadi kesalahan:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan',
                                    text: 'Gagal memperbarui data. Silakan coba lagi.'
                                });
                            },
                            complete: function() {
                                console.log("AJAX request completed.");
                            }
                        });
                    }
                });
                }
            });

            document.getElementById('drafter-search').addEventListener('input', function() {
                updateOptions(this.value, 0, pageSize, listproject, document.getElementById('edit-drafter'));
            });

            document.getElementById('checker-search').addEventListener('input', function() {
                updateOptions(this.value, 0, pageSize, listproject, document.getElementById('edit-checker'));
            });

            document.getElementById('progressnodokumen-search').addEventListener('input', function() {
                updateOptions(this.value, 0, pageSize, listprogressnodokumen, document.getElementById('edit-progressnodokumen'));
            });

            document.getElementById('prev-drafter-page').addEventListener('click', function() {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('drafter-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-drafter'));
                }
            });

            document.getElementById('next-drafter-page').addEventListener('click', function() {
                currentPageIndex++;
                updateOptions(document.getElementById('drafter-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-drafter'));
            });

            document.getElementById('prev-checker-page').addEventListener('click', function() {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('checker-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-checker'));
                }
            });

            document.getElementById('next-checker-page').addEventListener('click', function() {
                currentPageIndex++;
                updateOptions(document.getElementById('checker-search').value, currentPageIndex, pageSize, listproject, document.getElementById('edit-checker'));
            });

            document.getElementById('prev-progressnodokumen-page').addEventListener('click', function() {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    updateOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen, document.getElementById('edit-progressnodokumen'));
                }
            });

            document.getElementById('next-progressnodokumen-page').addEventListener('click', function() {
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
                                success: function(response) {
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
                                error: function(xhr, status, error) {
                                    console.error('Terjadi kesalahan:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan',
                                        text: 'Gagal memperbarui data. Silakan coba lagi.'
                                    });
                                },
                                complete: function() {
                                    console.log("AJAX request completed.");
                                }
                            });
                        }
                    });
                }
            });
        }

        // Event delegation for delete button
        $(document).on('click', '.btn-delete-multiple', function() {
            handleDeleteMultipleItems();
        });

        // Event delegation for release button
        $(document).on('click', '.btn-release-multiple', function() {
            handleReleaseMultipleItems();
        });

        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        function handleDeleteMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih? (Resiko Anak Dokumen Akan Terhapus kecuali anda lepas dulu sebagai dokumen pendukung)',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                // Jika pengguna mengonfirmasi penghapusan
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih dari semua halaman
                    var table = $('#example2').DataTable();
                    var selectedDocumentIds = [];
                    table.$('input[name="document_ids[]"]:checked').each(function() {
                        selectedDocumentIds.push($(this).val());
                    });

                    // Melakukan panggilan AJAX untuk menghapus item yang dipilih
                    $.ajax({
                        url: '{{ route("newprogressreports.handleDeleteMultipleItems") }}',
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function(response) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });

                            // Refresh halaman setelah penghapusan
                            table.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Tampilkan pesan error
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function handleReleaseMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin mengubah statusnya menjadi RELEASE?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, update!'
            }).then((result) => {
                // Jika pengguna mengonfirmasi penghapusan
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih dari semua halaman
                    var table = $('#example2').DataTable();
                    var selectedDocumentIds = [];
                    table.$('input[name="document_ids[]"]:checked').each(function() {
                        selectedDocumentIds.push($(this).val());
                    });

                    // Melakukan panggilan AJAX untuk mengupdate item yang dipilih
                    $.ajax({
                        url: '{{ route("newprogressreports.handleReleaseMultipleItems") }}',
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function(response) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah diupdate.',
                                icon: 'success'
                            });

                            // Refresh halaman setelah penghapusan
                            table.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            // Tampilkan pesan error
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal mengupdate item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
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
                    labels: ['{{$datastatus[$keyan]['RELEASED']}} RELEASED', '{{$datastatus[$keyan]['Working Progress']}} Working Progress','{{$datastatus[$keyan]["Belum Dimulai"]}} Belum Dimulai'],
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
                                generateLabels: function(chart) {
                                    var data = chart.data;
                                    return data.labels.map(function(label, i) {
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>

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


@endsection
