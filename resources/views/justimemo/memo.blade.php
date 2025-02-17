@php
    $statussetujulist=[];
    $yourrule=auth()->user()->rule;
@endphp
<style>
    .feedback-container {
        border: 1px solid #ccc;
        padding: 15px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .feedback-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .feedback-item a {
        color: #007bff;
        text-decoration: none;
    }

    .feedback-section {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
    }

    .btn-warning,
    .btn-success {
        color: #fff;
    }
    .card-text {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .feedback-info {
        margin-bottom: 10px;
    }

    .timestamp-badge {
        background-color: #007bff; /* Warna latar belakang badge */
        color: #fff; /* Warna teks badge */
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 80%; /* Ukuran font yang lebih kecil */
        margin-top: 5px; /* Jarak antara teks dan waktu */
    }
    .card-badge {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .status-text {
        margin-right: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .badge-terbuka {
        background-color: #dc3545;
        color: #fff;
    }

    .badge-tertutup {
        background-color: #28a745;
        color: #fff;
    }
</style>


@extends('layouts.split3')

@section('container1')
    {{--Dokumen informasi Awal--}}
    
    <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <div class="info-box-content">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Informasi Dokumen:</h1>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                <p class="card-text"><strong>Nomor Dokumen:</strong> {{ $document->documentnumber }}</p>
                <p class="card-text"><strong>Nama Dokumen:</strong> {{ $document->documentname }}</p>
                <p class="card-text"><strong>Kategori:</strong> {{ $document->category }}</p>
                <p class="card-text"><strong>Tipe Proyek:</strong> {{ $projectname }}</p>
                <p>
                    <div class="card-badge mt-2">
                        <span class="status-text"><strong>Status Dokumen:</strong></span>
                        <span class="status-badge" id="statusBadge"></span>
                    </div>
                </p>
                <script>
                    const statusBadge = document.getElementById('statusBadge');
                    const documentStatus = '{{ $document->documentstatus }}';

                    // Atur warna dan isi teks pada badge berdasarkan status dokumen
                    if (documentStatus.toLowerCase() === 'terbuka') {
                        statusBadge.textContent = 'Terbuka';
                        statusBadge.classList.add('badge-terbuka');
                    } else {
                        statusBadge.textContent = 'Tertutup';
                        statusBadge.classList.add('badge-tertutup');
                    }
                </script>
                @if ($document->memoorigin)
                    <p class="card-text"><strong>Asal Memo:</strong> {{ $document->memoorigin }}</p>
                @else
                    <p class="card-text"><strong>Asal Memo:</strong> MTPR belum menentukan asal memo</p>
                @endif
                @php
                    $dasarinformasi = $document->feedbacks;
                @endphp
                @foreach ($dasarinformasi as $i => $anakinformasi)
                    @if ($anakinformasi != "" && $operatorsignature!="Aktif")
                        @if ($anakinformasi->pic == "MTPR" && $anakinformasi->level == "pembukadokumen")
                            {{-- Bagian Loop File --}}
                            @php
                                $files = $anakinformasi->files;
                                $jumlahLampiran = count($files); // Menghitung jumlah lampiran
                            @endphp
                        @endif
                    @else
                        @php
                            $files = $anakinformasi->files
                        @endphp
                        @if ($files)
                            @php
                                $jumlahLampiran = count($files); // Menghitung jumlah lampiran
                            @endphp
                        @endif 
                    @endif
                @endforeach
                @if(isset($jumlahLampiran))
                    <p class="card-text"><strong>Jumlah Lampiran:</strong> {{ $jumlahLampiran }}</p>
                @endif
                @if (isset(json_decode($document->timeline)->documentopened))
                    @php
                        $sendtime=$userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <p class="card-text"><strong>Tanggal Terbit Memo:</strong> {{$formattedTime}}</p> 
                @else
                    <p class="card-text"><strong>Tanggal Terbit Memo:</strong> Belum Terbit</p>
                @endif
                @if ($document->memokind)
                    <p class="card-text"><strong>Kategori Memo:</strong> {{ $document->memokind }}</p>
                @else
                    <p class="card-text"><strong>Kategori Memo:</strong> PE belum menentukan kategori memo</p>
                @endif
                @php
                    $komats = $document->komats;
                @endphp
                @if (isset($komats))
                    <p class="card-text">
                        <strong>Informasi Komat:</strong>
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th>Kode Material</th>
                                    <th>Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($komats as $komat)
                                    @php
                                        $komponen = $komat->material;
                                        $kodematerial = $komat->kodematerial;
                                        $supplier = $komat->supplier;
                                    @endphp
                                    <tr>
                                        <td>{{ $komponen }}</td>
                                        <td>{{ $kodematerial }}</td>
                                        <td>{{ $supplier }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </p> 
                @else
                    <p class="card-text"><strong>Supplier:</strong> PE Belum menentukan supplier</p>
                @endif
                @php
                    $timeline = json_decode($document->timeline, true);
                @endphp
                
                @if (isset($timeline['documentshared']))
                    @php
                        $sendtime=$userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> {{$formattedTime}}</p> 
                @else
                    <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> Belum disebarkan</p>
                @endif
                
                @if (isset($timeline['documentclosed']))
                    @php
                        $sendtime=$userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> {{$formattedTime}}</p> 
                @else
                    <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> Belum ditutup</p>
                @endif
                @if($yourrule === $document->operator)
                    <p class="card-text"><strong>PIC Proyek:</strong>
                        @if (isset($document->project_pic))
                            @foreach (json_decode($document->project_pic) as $pic)
                                <a href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi={{ $pic }}&notificationcategory={{ $document->category }}">{{ $pic }}</a>
                            @endforeach    
                        @else
                            Tidak ada PIC proyek tersedia
                        @endif    
                    </p>
                @endif
                @foreach ($dasarinformasi as $anakinformasi)
                    @if ($anakinformasi != "")
                        @if ($anakinformasi->pic == "MTPR"&&$anakinformasi->level == "pembukadokumen")
                            {{-- Bagian Loop File --}}
                            @php
                                $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                $files = $anakinformasi->files;
                            @endphp
                            @if ($files && $operatorsignature!="Aktif")
                                <p class="card-text"><strong>File dengan Kolom TTD:</strong>
                                @foreach ($files as $file)
                                    @php
                                        $newLinkFile = str_replace('uploads/', '', $file->link);
                                    @endphp
                                    <div class="card-text mt-2">
                                    <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                    </div>
                                @endforeach
                                </p>
                            @endif 
                            @php
                                $files = $anakinformasi->files;
                            @endphp
                            
                        @endif
                    @endif
                @endforeach
                @foreach ($dasarinformasi as $anakinformasi)
                    @if ($anakinformasi != "")
                        @if ($anakinformasi->pic == $document->operator&&$anakinformasi->level == "signature")
                            {{-- Bagian Loop File --}}
                            @php
                                $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                $files = $anakinformasi->files
                            @endphp
                            @if ($files)
                                <p class="card-text"><strong>File dengan Kolom TTD:</strong>
                                @foreach ($files as $file)
                                    @php
                                        $newLinkFile = str_replace('uploads/', '', $file->link);
                                    @endphp
                                    <div class="card-text mt-2">
                                    <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                    </div>
                                @endforeach
                                </p>
                            @endif 
                        @endif
                    @endif
                @endforeach
                
                
                <div>
                    @if($yourrule === $document->operator && $document->documentstatus === "Terbuka")
                        @if ($operatorsignature=="Aktif")
                            <a href="{{ route('new-memo.edit', $document->id) }}" class="btn btn-warning btn-sm">Edit Dokumen</a>
                        @else
                            <a href="{{ route('new-memo.uploadsignature', $document->id) }}" class="btn btn-success btn-sm feedback-upload-btn">Upload Signature</a>
                        @endif  
                    @endif
                    <a class="btn btn-primary btn-sm" href="{{ route('document.report', ['id' => $document->id, 'rule' => $yourrule]) }}">
                        <i class="fas fa-folder"></i> Progress
                    </a>
                </div>
            </div>
                        </div>
                    </div>
    </div>
    </div>
    {{--Dokumen informasi Akhir--}}
@endsection



@section('container2')
        
        {{-- Feedback Unit Awal --}}
        @if (json_decode($document->project_pic))
            @php
                $project_pic=json_decode($document->project_pic);
            @endphp
            @if (in_array($yourrule, $project_pic) || in_array($yourrule, ["superuser","MTPR"]))   
                @foreach ($project_pic as $unit)
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <div class="info-box-content">
                                <!-- MULTI CHARTS -->
                                <div class="card">
                                    <div class="card-header">
                                        <h1 class="card-title">Feedback {{ $unit }}</h1>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @php
                                        $userinformations = $document->feedbacks;
                                    @endphp
                                    <div class="card-body">
                                        @foreach ($userinformations as $userinformation)
                                            @if ($userinformation->level == $unit && $userinformation->conditionoffile2 == "feedback")
                                                <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                <p>
                                                <div class="card mt-3">
                                                    <div class="info-container mt-2" style="display: none;">
                                                        <div class="card-body">
                                                            <h5 class="card-title"></h5>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">
                                                                    @if ($userinformation->level == $yourrule)
                                                                        <button class="btn" style="background-color: orange;">
                                                                            <strong>Status: Penerima dari:</strong> {{ $userinformation->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @elseif ($userinformation->level == "")
                                                                        <button class="btn" style="background-color: yellow;">
                                                                            <strong>Upload Pribadi</strong>
                                                                        </button>
                                                                    @else
                                                                        <button class="btn" style="background-color: red;">
                                                                            <strong>Status: Terkirim ke:</strong> {{ $userinformation->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @endif  
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Apakah anda sudah melakukan review atas dokumen approval?</strong>
                                                                    {{ $userinformation->review ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Nama Penulis:</strong>
                                                                    {{ $userinformation->author ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Email:</strong>
                                                                    {{ $userinformation->email ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Apakah dokumen sudah dibaca?</strong>
                                                                    {{ $userinformation->sudahdibaca ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Jenis Comment:</strong>
                                                                    {{ $userinformation->conditionoffile2 ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>ID Comment:</strong>
                                                                    {{ $userinformation->id ?: 'Kosong' }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                </p>
                                                <li class="list-group-item">
                                                    <strong>Waktu:</strong>
                                                    @php
                                                        $sendtime=$userinformation->created_at;
                                                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                                    @endphp
                                                    {!! $formattedTime ?? 'Kosong' !!}
                                                    @if ($yourrule == $document->operator)   
                                                        <div class="col-md-6">
                                                            <form id="deleteFeedbackForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.deletedfeedbackdecision', ['memoId' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                                <button type="button" class="btn btn-warning mt-2" onclick="confirmDecision('deleteFeedbackForm{{ $document->id }}{{$sendtime}}')">Delete Feedback</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status Dokumen:</strong> {{ $userinformation->hasilreview ?: 'Kosong' }}
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status:</strong> {{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}
                                                    @php
                                                        $statussetuju = $userinformation->conditionoffile;
                                                        $statussetujulist[$unit] = $statussetuju;
                                                    @endphp
                                                </li>
                                                @if ($files)
                                                    <div class="card feedback-item">
                                                        <div class="card-text-item">
                                                            <strong>File:</strong>
                                                        </div>
                                                        <p class="card-text"><strong>File:</strong>
                                                            @foreach ($userinformation->files as $file)
                                                                @php
                                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                @endphp
                                                                <div class="card-text mt-2">
                                                                    <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                                                </div>
                                                            @endforeach
                                                        </p>
                                                    </div>
                                                @endif
                                                <li class="list-group-item">
                                                    <strong>Komentar:</strong>
                                                    @if (!empty($userinformation->comment))
                                                        {{ $userinformation->comment }} <span style="color: blue;">@</span><span style="color: blue;">{{ $userinformation->pic }}</span>
                                                    @else
                                                        Kosong
                                                    @endif
                                                </li>
                                                @if (str_contains($yourrule, "Manager") && $statussetuju != "Approved" && $statussetuju != "Approved by Manager" && $statussetuju != "Rejected by Manager")    
                                                    <div class="card-text">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <form id="approveForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision" value="Approved by Manager">
                                                                    <button type="button" class="btn btn-success mt-2" onclick="confirmDecision('approveForm{{ $document->id }}{{$sendtime}}')">Terima</button>
                                                                </form>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <form id="approveDirectForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision" value="Approved">
                                                                    <button type="button" class="btn btn-success mt-2" onclick="confirmDecision('approveDirectForm{{ $document->id }}{{$sendtime}}')">Terima Langsung</button>
                                                                </form>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <form id="rejectForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision" value="Rejected by Manager">
                                                                    <button type="button" class="btn btn-danger mt-2" onclick="confirmDecision('rejectForm{{ $document->id }}{{$sendtime}}')">Tolak</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                        @if ($yourrule == "Manager " . $unit) 
                                            <p class="mt-3">
                                                <a href="{{ route('new-memo.uploadmanagerfeedback', $document->id) }}" class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback Manager {{ $unit }}</a>
                                            </p>
                                            <p class="mt-2"><strong>STATUS</strong></p>
                                        @endif
                                        @if ($unitpicvalidation[$unit] != "Aktif" && !in_array($yourrule, ["MTPR"]) && $yourrule == $unit)
                                            <p class="mt-3">
                                                <a href="{{ route('new-memo.uploadfeedback', $document->id) }}" class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback {{ $unit }}</a>
                                            </p>
                                            <p class="mt-2"><strong>STATUS</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            
            @endif
        @endif
        {{-- Feedback Unit Akhir --}}



        {{-- Validasi Awal --}}
        @if (in_array($yourrule, ["MTPR","superuser"]))
        
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <div class="info-box-content">
                        {{-- Awalan --}}
                        {{-- VALIDASI AWAL --}} 
                        <!-- MULTI CHARTS -->
                        <div class="card">
                        <div class="card-header">
                                <h1 class="card-title">Validasi:</h1>
                                <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                                </div>
                            </div>
                                <div class="card-body">
                                    @php
                                        $userinformations=$document->feedbacks;
                                    @endphp
                                    @foreach ($userinformations as $userinformation)
                                        @if ($userinformation != "")
                                            @if (in_array($userinformation->level, ["MTPR","superuser"]) )
                                                <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                <p>
                                                    <div class="card mt-3">
                                                        <div class="info-container mt-2" style="display: none;">
                                                            <div class="card-body">
                                                                <h5 class="card-title"></h5>
                                                            
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item">
                                                                        @if ($userinformation->level == $yourrule)
                                                                            <button class="btn" style="background-color: orange;">
                                                                                <strong>Status: Penerima dari:</strong> {{ $userinformation->level ?? "hanya upload & tidak dikirim" }}
                                                                            </button>
                                                                        @elseif ($userinformation->level == "")
                                                                            <button class="btn" style="background-color: yellow;">
                                                                                <strong>Upload Pribadi</strong>
                                                                            </button>
                                                                        @else
                                                                            <button class="btn" style="background-color: red;">
                                                                                <strong>Status: Terkirim ke:</strong> {{ $userinformation->level ?? "hanya upload & tidak dikirim" }}
                                                                            </button>
                                                                        @endif  
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Apakah anda sudah melakukan review atas dokumen approval?</strong>
                                                                        {{ $userinformation->review ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Nama Penulis:</strong>
                                                                        {{ $userinformation->author ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Email:</strong>
                                                                        {{ $userinformation->email ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Apakah dokumen sudah dibaca?</strong>
                                                                        {{ $userinformation->sudahdibaca ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Jenis Comment:</strong>
                                                                        {{ $userinformation->conditionoffile2 ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>ID Comment:</strong>
                                                                        {{ $userinformation->id ?: 'Kosong' }}
                                                                    </li>
                                                                </ul>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </p>
                                                
                                                
                                                <li class="list-group-item">
                                                    <strong>Waktu:</strong>
                                                    @php
                                                        $sendtime=$userinformation->created_at;
                                                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                                    @endphp
                                                    {!! $formattedTime ?? 'Kosong' !!}
                                                    @if ($yourrule==$document->operator && $sendtime!="tidakada")   
                                                        <div class="col-md-6">
                                                            <form id="UnsendForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.unsenddecision', ['memoId' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT') <!-- Menyertakan metode PUT -->
                                                                <input type="hidden" name="_method" value="PUT"> <!-- Menambahkan input _method untuk menyatakan PUT -->
                                                                <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                                                <button type="button" class="btn btn-warning mt-2" onclick="confirmDecision('UnsendForm{{ $document->id }}{{$sendtime}}')">Unsend Semua</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status Dokumen:</strong>
                                                    {{ $userinformation->hasilreview ?: 'Kosong' }}
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status:</strong>
                                                    {{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}
                                                </li>
                                                @if ($files)
                                                    <div class="card feedback-item">
                                                        <div class="card-text-item">
                                                            <strong>File:</strong>
                                                        </div>
                                                        
                                                        @foreach ($files as $file)
                                                            @php
                                                                $newLinkFile = str_replace('uploads/', '', $file->link);
                                                            @endphp
                                                            <div class="card-text mt-2">
                                                            <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                <li class="list-group-item">
                                                <strong>Komentar:</strong>
                                                @if(!empty($userinformation->comment))
                                                    {{ $userinformation->comment }} <span style="color: blue;">@</span><span style="color: blue;">{{ $userinformation->pic }}</span>
                                                @else
                                                    Kosong
                                                @endif
                                            </li>
                                                @if ($unitvalidation=="Aktif" && $operatorcombinevalidation == "Aktif" && $seniormanagervalidation=="Aktif" && $MTPRvalidation != "Aktif")                                    
                                                    <div class="col-md-4">

                                                        <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST" action="{{ route('new-memo.sendfoward', ['memoId' => $document->id]) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="_method" value="PUT"> <!-- Menyertakan _method untuk metode PUT -->
                                                            <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                                            <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                            <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                            <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                            <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                            <input type="hidden" name="decision" value="Dokumen ditutup">
                                                            <div class="form-group">
                                                                <label for="level">Send:</label>
                                                                <select name="level" id="level" class="form-control">
                                                                    <option value="selesai">selesai</option>
                                                                </select>
                                                            </div>
                                                            <button type="button" class="btn btn-success mt-2" onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung Kirim</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            
                        </div>
                        <!-- END MULTI CHARTS -->
                        
                        {{-- VALIDASI Penutup--}}
                        {{--Akhiran--}}
                    </div>
                </div>
            </div>
          {{-- Validasi Akhir --}}
        @endif


@endsection

@section('container3')
    <a href="{{ route('mapping.all') }}" class="btn btn-primary">Back</a>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('.toggle-info').click(function(){
            $(this).closest('.info-box-content').find('.info-container').toggle();
        });
    });
</script>




@section('rightsidebar') 
    <p class="card">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>
                            <div class="card card-primary collapsed-card">
                                <div class="card-header">
                                    <h1 class="card-title">{{ $log->aksi }}</h1>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div>
                                        <span class="text-muted ml-2">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <p class="mb-0"><strong>{{ json_decode($log->message)->pesan }}</strong></p>
                                        <p class="text-muted mb-0">Jenis Data: {{ $log->jenisdata }}</p>
                                        <p class="text-muted mb-0">Pengguna Aksi: {{ $log->user }}</p>
                                    </div>
                                </div>
                            </div>
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </p>  
@endsection











