@php
    $statussetujulist = [];
    $yourrule = auth()->user()->rule;
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
        background-color: #007bff;
        /* Warna latar belakang badge */
        color: #fff;
        /* Warna teks badge */
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 80%;
        /* Ukuran font yang lebih kecil */
        margin-top: 5px;
        /* Jarak antara teks dan waktu */
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
                    <p class="card-text"><strong>Tipe Proyek:</strong> {{ $document->project_type }}</p>
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
                        $dasarinformasi = json_decode($document->userinformations);
                    @endphp
                    @foreach ($dasarinformasi as $i => $anakinformasi)
                                @if ($anakinformasi != "" && $PEsignature != "Aktif")
                                            @if (json_decode($anakinformasi)->pic == "MTPR" && json_decode($anakinformasi)->level == "pembukadokumen")
                                                        {{-- Bagian Loop File --}}
                                                        @php
                                                            $filenames = json_decode($anakinformasi)->listfilenames;
                                                            $linkfiles = json_decode($anakinformasi)->listlinkfiles;
                                                            $jumlahLampiran = count($filenames); // Menghitung jumlah lampiran
                                                        @endphp
                                            @endif
                                @else
                                            @if (json_decode($anakinformasi)->pic == "Product Engineering" && json_decode($anakinformasi)->level == "signature")
                                                        {{-- Bagian Loop File --}}
                                                        @php
                                                            $filenames = json_decode($anakinformasi)->listfilenames;
                                                            $linkfiles = json_decode($anakinformasi)->listlinkfiles;
                                                            $jumlahLampiran = count($filenames); // Menghitung jumlah lampiran
                                                        @endphp
                                            @endif
                                @endif
                    @endforeach
                    @if(isset($jumlahLampiran))
                        <p class="card-text"><strong>Jumlah Lampiran:</strong> {{ $jumlahLampiran }}</p>
                    @endif
                    @if (isset(json_decode($document->timeline)->documentopened))
                                        @php
                                            $datetime = new DateTime(json_decode($document->timeline)->documentopened, new DateTimeZone('UTC'));
                                            $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                            $formattedTime = $datetime->format('Y-m-d H:i:s');
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
                        $komats = json_decode(json_decode($document->remaininformation)->komat);
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
                                                                        $komponen = json_decode($komat)->komponen;
                                                                        $kodematerial = json_decode($komat)->kodematerial;
                                                                        $supplier = json_decode($komat)->supplier;
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
                                            $datetime = new DateTime(json_decode($document->timeline)->documentshared, new DateTimeZone('UTC'));
                                            $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                            $formattedTime = $datetime->format('Y-m-d H:i:s');
                                        @endphp
                                        <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> {{$formattedTime}}</p>
                    @else
                        <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> Belum disebarkan</p>
                    @endif

                    @if (isset($timeline['documentclosed']))
                                        @php
                                            $datetime = new DateTime(json_decode($document->timeline)->documentclosed, new DateTimeZone('UTC'));
                                            $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                            $formattedTime = $datetime->format('Y-m-d H:i:s');
                                        @endphp
                                        <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> {{$formattedTime}}</p>
                    @else
                        <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> Belum ditutup</p>
                    @endif
                    @if($yourrule === "Product Engineering")
                        <p class="card-text"><strong>PIC Proyek:</strong>
                            @if (isset($document->project_pic))
                                @foreach (json_decode($document->project_pic) as $pic)
                                    <a
                                        href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi={{ $pic }}&notificationcategory={{ $document->category }}">{{ $pic }}</a>
                                @endforeach
                            @else
                                Tidak ada PIC proyek tersedia
                            @endif
                        </p>
                    @endif
                    @foreach ($dasarinformasi as $i => $anakinformasi)
                                @if ($anakinformasi != "")
                                            @if (json_decode($anakinformasi)->pic == "MTPR" && json_decode($anakinformasi)->level == "pembukadokumen")
                                                        {{-- Bagian Loop File --}}
                                                        @php
                                                            $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                                            $filenames = json_decode($anakinformasi)->listfilenames;
                                                            $linkfiles = json_decode($anakinformasi)->listlinkfiles;
                                                        @endphp
                                                        @if ($filenames && $PEsignature != "Aktif")
                                                            <p class="card-text"><strong>File:</strong>
                                                                @foreach ($filenames as $index => $filename)
                                                                        @php
                                                                            $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                                        @endphp
                                                                        {{-- Deduplikasi file --}}
                                                                        @if (!in_array($filename, $uniqueFiles))
                                                                            @php
                                                                                $uniqueFiles[] = $filename; // Tambahkan file ke dalam array unik
                                                                            @endphp
                                                                            <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                                        @endif
                                                                @endforeach
                                                            </p>
                                                        @endif
                                            @endif
                                @endif
                    @endforeach
                    @foreach ($dasarinformasi as $i => $anakinformasi)
                                @if ($anakinformasi != "")
                                            @if (json_decode($anakinformasi)->pic == "Product Engineering" && json_decode($anakinformasi)->level == "signature")
                                                        {{-- Bagian Loop File --}}
                                                        @php
                                                            $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                                            $filenames = json_decode($anakinformasi)->listfilenames;
                                                            $linkfiles = json_decode($anakinformasi)->listlinkfiles;
                                                        @endphp
                                                        @if ($filenames)
                                                            <p class="card-text"><strong>File dengan Kolom TTD:</strong>
                                                                @foreach ($filenames as $index => $filename)
                                                                        @php
                                                                            $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                                        @endphp
                                                                        {{-- Deduplikasi file --}}
                                                                        @if (!in_array($filename, $uniqueFiles))
                                                                            @php
                                                                                $uniqueFiles[] = $filename; // Tambahkan file ke dalam array unik
                                                                            @endphp
                                                                            <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                                        @endif
                                                                @endforeach
                                                            </p>
                                                        @endif
                                            @endif
                                @endif
                    @endforeach


                    <div>
                        @if($yourrule === "Product Engineering" && $document->documentstatus === "Terbuka")
                            @if ($PEsignature == "Aktif")
                                <a href="{{ route('document.edit', $document->id) }}" class="btn btn-warning btn-sm">Edit
                                    Dokumen</a>
                            @endif
                            <a href="{{ route('document.uploadsignature', $document->id) }}"
                                class="btn btn-success btn-sm feedback-upload-btn">Upload Signature</a>
                        @endif
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('document.report', ['id' => $document->id, 'rule' => $yourrule]) }}">
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
{{--Feedback Unit Awal--}}
@if (!in_array($yourrule, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]))
    @if(json_decode($document->project_pic))
        @foreach(json_decode($document->project_pic) as $unit)

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
                            <div class="card-body">
                                @foreach ($userinformations as $i => $userinformation)
                                    @if ($userinformation != "")
                                        @if (json_decode($userinformation)->pic == $unit && json_decode($userinformation)->level == "" && json_decode(json_decode($userinformation)->userinformations)->conditionoffile2 == "feedback")
                                            <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                            <p>
                                                @php
                                                    $sumberinformasi = json_decode($userinformation)->userinformations;
                                                @endphp
                                                @if ($userinformations)

                                                    <div class="card mt-3">
                                                        <div class="info-container mt-2" style="display: none;">
                                                            <div class="card-body">


                                                                <h5 class="card-title"></h5>

                                                                <ul class="list-group list-group-flush">

                                                                    @php
                                                                        try {
                                                                            $userInfo = json_decode($sumberinformasi, true);
                                                                            if (json_last_error() != JSON_ERROR_NONE) {
                                                                                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                                                            }
                                                                        } catch (\Exception $e) {
                                                                            \Log::error('Error decoding JSON: ' . $e->getMessage());
                                                                            $userInfo = [];
                                                                        }
                                                                    @endphp
                                                                    <li class="list-group-item">
                                                                        @if(json_decode($userinformation)->level == $yourrule)
                                                                            <button class="btn" style="background-color: orange;">
                                                                                <strong>Status: Penerima dari:</strong>
                                                                                {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                            </button>
                                                                        @elseif(json_decode($userinformation)->level == "")
                                                                            <button class="btn" style="background-color: yellow;">
                                                                                <strong>Upload Pribadi</strong>
                                                                            </button>
                                                                        @else
                                                                            <button class="btn" style="background-color: red;">
                                                                                <strong>Status: Terkirim ke:</strong>
                                                                                {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                            </button>
                                                                        @endif
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Apakah anda sudah melakukan review atas dokumen approval?</strong>
                                                                        {{ $userInfo['sudahdibaca'] ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Nama Penulis:</strong>
                                                                        {{ $userInfo['nama penulis'] ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Email:</strong>
                                                                        {{ $userInfo['email'] ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Apakah dokumen sudah dibaca?</strong>
                                                                        {{ $userInfo['sudahdibaca'] ?: 'Kosong' }}
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <strong>Jenis Comment:</strong>
                                                                        {{ $userInfo['conditionoffile2'] ?: 'Kosong' }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </p>
                                            @php
                                                $filenames = json_decode($userinformation)->listfilenames;
                                                $metadatas = json_decode($userinformation)->listmetadatas;
                                                $linkfiles = json_decode($userinformation)->listlinkfiles;
                                            @endphp
                                            <li class="list-group-item">
                                                <strong>Waktu:</strong>
                                                @php
                                                    if (isset($userInfo['time'])) {
                                                        try {
                                                            $sendtime = $userInfo['time'];
                                                            // Buat objek DateTime dengan timezone 'Asia/Jakarta'
                                                            $datetime = new DateTime($userInfo['time'], new DateTimeZone('Asia/Jakarta'));
                                                            $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                        } catch (Exception $e) {
                                                            $sendtime = "tidakada";
                                                            // Tangani kesalahan jika parsing waktu gagal
                                                            $formattedTime = 'Kesalahan: ' . $e->getMessage();
                                                        }
                                                    } else {
                                                        // Jika $userInfo['time'] tidak terdefinisi, gunakan waktu sekarang
                                                        $datetime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                    }    
                                                @endphp
                                                {!! $formattedTime ?? 'Kosong' !!}

                                                @if ($yourrule == "Product Engineering" && $sendtime != "tidakada")
                                                    <div class="col-md-6">
                                                        <form id="deleteFeedbackForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                            action="{{ route('deletedfeedbackdecision.Document', ['id' => $document->id, 'sendtime' => $sendtime]) }}">
                                                            @csrf
                                                            @method('PUT') <!-- Menyertakan metode PUT -->
                                                            <input type="hidden" name="_method" value="PUT">
                                                            <!-- Menambahkan input _method untuk menyatakan PUT -->
                                                            <button type="button" class="btn btn-warning mt-2"
                                                                onclick="confirmDecision('deleteFeedbackForm{{ $document->id }}{{$sendtime}}')">Delete
                                                                Feedback</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Status Dokumen</strong>
                                                {{ $userInfo['hasilreview'] ?: 'Kosong' }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Status:</strong>
                                                {{ ucfirst($userInfo['conditionoffile'] ?: 'Kosong') }}
                                                @php
                                                    $statussetuju = $userInfo['conditionoffile'];
                                                    $statussetujulist[$unit] = $statussetuju
                                                @endphp
                                            </li>
                                            @if ($filenames)
                                                <div class="card feedback-item">
                                                    <div class="card-text-item">
                                                        <strong>File:</strong>
                                                    </div>

                                                    @foreach ($filenames as $index => $filename)
                                                        @php
                                                            $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                        @endphp
                                                        <div class="card-text mt-2">
                                                            <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <li class="list-group-item">
                                                <strong>Komentar:</strong>
                                                @if(!empty($userInfo['comment']))
                                                    {{ $userInfo['comment'] }} <span style="color: blue;">@</span><span
                                                        style="color: blue;">{{ json_decode($userinformation)->pic }}</span>
                                                @else
                                                    Kosong
                                                @endif
                                            </li>
                                            @if (str_contains($yourrule, "Manager") && $statussetuju != "Approved" && $statussetuju != "Approved by Manager" && $statussetuju != "Rejected by Manager")
                                                <div class="card-text">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <form id="approveForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                action="{{ route('senddecision.Document', ['id' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                <input type="hidden" name="decision" value="Approved by Manager">
                                                                <button type="button" class="btn btn-success mt-2"
                                                                    onclick="confirmDecision('approveForm{{ $document->id }}{{$sendtime}}')">Terima</button>
                                                            </form>
                                                        </div>

                                                        <!-- Formulir untuk menyetujui langsung -->
                                                        <div class="col-md-6">
                                                            <form id="approveDirectForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                action="{{ route('senddecision.Document', ['id' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                <input type="hidden" name="decision" value="Approved">
                                                                <button type="button" class="btn btn-success mt-2"
                                                                    onclick="confirmDecision('approveDirectForm{{ $document->id }}{{$sendtime}}')">Terima
                                                                    Langsung</button>
                                                            </form>
                                                        </div>

                                                        <!-- Formulir untuk menolak -->
                                                        <div class="col-md-6">
                                                            <form id="rejectForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                action="{{ route('senddecision.Document', ['id' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                <input type="hidden" name="decision" value="Rejected by Manager">
                                                                <button type="button" class="btn btn-danger mt-2"
                                                                    onclick="confirmDecision('rejectForm{{ $document->id }}{{$sendtime}}')">Tolak</button>
                                                            </form>
                                                        </div>


                                                    </div>
                                                </div>
                                            @endif




                                        @endif
                                    @endif
                                @endforeach

                                {{-- Manager Feedback --}}

                                <!-- && $statussetujulist[$unit] !="Approved" -->
                                @if ($yourrule == "Manager " . $unit)
                                    <p class="mt-3">
                                        <a href="{{ route('document.uploadmanagerfeedback', $document->id) }}"
                                            class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback Manager {{$unit}}</a>
                                    </p>
                                    <p class="mt-2"><strong>{{ $status }}</strong></p>
                                @endif
                                {{-- NON Manager Feedback --}}
                                @if (($unitpicvalidation[$unit] != "Aktif" && !in_array($yourrule, ["MTPR"]) && $yourrule == $unit))
                                    <p class="mt-3">
                                        <a href="{{ route('document.uploadfeedback', $document->id) }}"
                                            class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback {{$unit}}</a>
                                    </p>

                                    <p class="mt-2"><strong>{{ $status }}</strong></p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endif
{{--Feedback Unit Akhir--}}


{{--Combine--}}
@if (!in_array($yourrule, ["Senior Manager Engineering", "Manager Product Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]))
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Finalisasi Unit</h1>
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
                        @foreach ($userinformations as $i => $userinformation)
                                @if ($userinformation != "")
                                        @if (json_decode($userinformation)->pic == "Product Engineering" && json_decode($userinformation)->level == "" && json_decode(json_decode($userinformation)->userinformations)->conditionoffile2 == "combine")
                                                <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                <p>
                                                    @php
                                                        $sumberinformasi = json_decode($userinformation)->userinformations;
                                                    @endphp
                                                    @if ($userinformations)

                                                        <div class="card mt-3">
                                                            <div class="info-container mt-2" style="display: none;">
                                                                <div class="card-body">


                                                                    <h5 class="card-title"></h5>

                                                                    <ul class="list-group list-group-flush">

                                                                        @php
                                                                            try {
                                                                                $userInfo = json_decode($sumberinformasi, true);
                                                                                if (json_last_error() != JSON_ERROR_NONE) {
                                                                                    throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                                                                }
                                                                            } catch (\Exception $e) {
                                                                                \Log::error('Error decoding JSON: ' . $e->getMessage());
                                                                                $userInfo = [];
                                                                            }
                                                                        @endphp
                                                                        <li class="list-group-item">
                                                                            @if(json_decode($userinformation)->level == $yourrule)
                                                                                <button class="btn" style="background-color: orange;">
                                                                                    <strong>Status: Penerima dari:</strong>
                                                                                    {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                                </button>
                                                                            @elseif(json_decode($userinformation)->level == "")
                                                                                <button class="btn" style="background-color: yellow;">
                                                                                    <strong>Upload Pribadi</strong>
                                                                                </button>
                                                                            @else
                                                                                <button class="btn" style="background-color: red;">
                                                                                    <strong>Status: Terkirim ke:</strong>
                                                                                    {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                                </button>
                                                                            @endif



                                                                        </li>
                                                                        @foreach ($userInfo as $key => $value)
                                                                            @if ($key == 'sudahdibaca')
                                                                                <li class="list-group-item">
                                                                                    <strong>Apakah anda sudah melakukan review atas dokumen approval?</strong>
                                                                                    {{ $value ?: 'Kosong' }}
                                                                                </li>
                                                                            @elseif ($key == 'comment')
                                                                            @elseif ($key == 'time')
                                                                            @elseif ($key == 'conditionoffile')
                                                                            @elseif ($key == 'hasilreview')
                                                                            @else
                                                                                <li class="list-group-item">
                                                                                    <strong>{{ $key }}:</strong>
                                                                                    {{ $value ?: 'Kosong' }}
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </p>
                                                @php
                                                    $filenames = json_decode($userinformation)->listfilenames;
                                                    $metadatas = json_decode($userinformation)->listmetadatas;
                                                    $linkfiles = json_decode($userinformation)->listlinkfiles;
                                                @endphp
                                                @foreach ($userInfo as $key => $value)
                                                    @if ($key == 'time')
                                                        <li class="list-group-item">
                                                            <strong>Waktu:</strong>
                                                            @php
                                                                $sendtime = $value;
                                                                $datetime = new DateTime($value, new DateTimeZone('UTC'));
                                                                $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                                $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                            @endphp
                                                            {!! $formattedTime ?? 'Kosong' !!}
                                                            @if ($yourrule == "Product Engineering" && $sendtime != "tidakada")
                                                                <div class="col-md-6">
                                                                    <form id="UnsendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                        action="{{ route('unsenddecision.Document', ['id' => $document->id, 'sendtime' => $sendtime]) }}">
                                                                        @csrf
                                                                        @method('PUT') <!-- Menyertakan metode PUT -->
                                                                        <input type="hidden" name="_method" value="PUT">
                                                                        <!-- Menambahkan input _method untuk menyatakan PUT -->
                                                                        <button type="button" class="btn btn-warning mt-2"
                                                                            onclick="confirmDecision('UnsendForm{{ $document->id }}{{$sendtime}}')">Unsend
                                                                            Semua</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @elseif ($key == 'hasilreview')
                                                        <li class="list-group-item">
                                                            <strong>Status Dokumen</strong>
                                                            {{ $value ?: 'Kosong' }}
                                                        </li>
                                                    @elseif ($key == 'conditionoffile')
                                                        <li class="list-group-item">
                                                            <strong>Status:</strong>
                                                            {{ ucfirst($value ?: 'Kosong') }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                                @if ($filenames)
                                                    <div class="card feedback-item">
                                                        <div class="card-text-item">
                                                            <strong>File:</strong>
                                                        </div>

                                                        @foreach ($filenames as $index => $filename)
                                                            @php
                                                                $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                            @endphp
                                                            <div class="card-text mt-2">
                                                                <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @foreach ($userInfo as $key => $value)
                                                    @if ($key == 'comment')
                                                        <li class="list-group-item">
                                                            <strong>Komentar:</strong>
                                                            @if(!empty($value))
                                                                {{ $value }} <span style="color: blue;">@</span><span
                                                                    style="color: blue;">{{ json_decode($userinformation)->pic }}</span>
                                                            @else
                                                                Kosong
                                                            @endif
                                                    @endif
                                                @endforeach
                                        @endif
                                @endif

                                    @if ($selfunitvalidation == "Aktif" && $unitvalidation == "Aktif" && $PEmanagervalidation != "Aktif" && $yourrule == "Product Engineering" && json_decode($userinformation)->pic == "Product Engineering" && json_decode($userinformation)->level != "signature" && json_decode(json_decode($userinformation)->userinformations)->conditionoffile2 == "combine")
                                        <div class="card-text">
                                            <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                <input type="hidden" name="decision" value="Terkirim">
                                                <div class="form-group">
                                                    <label for="level">Send:</label>
                                                    <select name="level" id="level_{{ $document->id }}{{$sendtime}}"
                                                        class="form-control">
                                                        <option value="Senior Manager Teknologi Produksi">Senior Manager Teknologi
                                                            Produksi</option>
                                                        <option value="Manager Product Engineering">Manager Product Engineering</option>
                                                        <option value="Senior Manager Desain">Senior Manager Desain</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-success mt-2"
                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung
                                                    Kirim</button>
                                            </form>
                                        </div>
                                    @endif
                        @endforeach
                            @if ($unitvalidation == "Aktif" && $PEmanagervalidation != "Aktif" && $yourrule == "Product Engineering")
                                <p class="mt-3">
                                    <a href="{{ route('document.uploadcombine', $document->id) }}"
                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Combine</a>
                                </p>
                                <p class="mt-2"><strong>{{ $status }}</strong></p>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{--Combine Akhir--}}



@if($MPEvalidation != "Tidak Terlibat")
    {{-- Manager Product Engineering Awal --}}
    @if (in_array($yourrule, ["Manager Product Engineering", "MTPR", "superuser"]))
        @php
            $statusmanagerproductengineering = "Nonaktif";
            foreach ($userinformations as $i => $userinformation) {
                $level = json_decode($userinformation)->level;
                $pic = json_decode($userinformation)->pic;
                if ($level == "Manager Product Engineering" || $pic == "Manager Product Engineering") {
                    $statusmanagerproductengineering = "Aktif";
                }
            }
        @endphp
        @if ($statusmanagerproductengineering == "Aktif")
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <div class="info-box-content">
                        {{-- Awalan --}}
                        {{-- "Manager Product Engineering","Senior Manager Engineering", "Senior Manager Desain", "Senior Manager
                        Teknologi Produksi" is in section --}}
                        <!-- MULTI CHARTS -->
                        <div class="card">
                            <div class="card-header">
                                <h1 class="card-title">Review Manager Product Engineering:</h1>
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
                                @foreach ($userinformations as $i => $userinformation)
                                        @php
                                            $level = json_decode($userinformation)->level;
                                            $pic = json_decode($userinformation)->pic;
                                            $sumberinformasi = json_decode($userinformation)->userinformations;
                                            $filenames = json_decode($userinformation)->listfilenames;
                                            $metadatas = json_decode($userinformation)->listmetadatas;
                                            $linkfiles = json_decode($userinformation)->listlinkfiles;
                                        @endphp
                                        @if ($userinformation != "")
                                            @if (!in_array(json_decode($userinformation)->level, ["MTPR", "Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) && (in_array(json_decode($userinformation)->level, ["Manager Product Engineering"]) || in_array(json_decode($userinformation)->pic, ["Manager Product Engineering"])))
                                                {{--Mulai123--}}
                                                @if(json_decode($userinformation)->level == $yourrule || json_decode($userinformation)->pic == $yourrule || $yourrule == "MTPR" || $yourrule == "superuser")
                                                    @if (($level && !in_array($level, ["MTPR"])) || ($pic && in_array($pic, ["Manager Product Engineering"])))
                                                        <!-- Selengkapnya -->
                                                        <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                        <p>
                                                            @if ($userinformations)
                                                                <div class="card mt-3">
                                                                    <div class="info-container mt-2" style="display: none;"> <!-- hide by default -->
                                                                        <div class="card-body">
                                                                            <h5 class="card-title"></h5>
                                                                            <ul class="list-group list-group-flush">
                                                                                @php
                                                                                    try {
                                                                                        $userInfo = json_decode($sumberinformasi, true);

                                                                                        // Check for JSON decoding errors
                                                                                        if (json_last_error() != JSON_ERROR_NONE) {
                                                                                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                                                                        }
                                                                                    } catch (\Exception $e) {
                                                                                        // Log the error message or throw an exception
                                                                                        \Log::error('Error decoding JSON: ' . $e->getMessage());

                                                                                        // Handle decoding error, for example:
                                                                                        $userInfo = [];
                                                                                    }
                                                                                @endphp
                                                                                <li class="list-group-item">
                                                                                    <strong>Penerima:</strong>
                                                                                    {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                                </li>
                                                                                @foreach ($userInfo as $key => $value)
                                                                                    @if ($key != 'sudahdibaca' && $key != 'comment' && $key != 'time' && $key != 'conditionoffile' && $key != 'hasilreview')
                                                                                        <li class="list-group-item">
                                                                                            <strong>{{ $key }}:</strong>
                                                                                            {{ $value ?: 'Kosong' }}
                                                                                        </li>
                                                                                    @endif
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </p>
                                                        @foreach ($userInfo as $key => $value)
                                                            @if ($key == 'time')
                                                                <li class="list-group-item">
                                                                    <strong>Waktu:</strong>
                                                                    @php
                                                                        $sendtime = $value;
                                                                        $datetime = new DateTime($value, new DateTimeZone('UTC'));
                                                                        $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                                    @endphp
                                                                    {!! $formattedTime ?? 'Kosong' !!}
                                                                </li>
                                                            @elseif ($key == 'hasilreview')
                                                                <li class="list-group-item">
                                                                    <strong>Status Dokumen</strong>
                                                                    {{ $value ?: 'Kosong' }}
                                                                </li>
                                                            @elseif ($key == 'conditionoffile')
                                                                <li class="list-group-item">
                                                                    <strong>Status:</strong>
                                                                    {{ ucfirst($value ?: 'Kosong') }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                        @if ($filenames)
                                                            <div class="card feedback-item">
                                                                <div class="card-text-item">
                                                                    <strong>File:</strong>
                                                                </div>

                                                                @foreach ($filenames as $index => $filename)
                                                                    @php
                                                                        $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                                    @endphp
                                                                    <div class="card-text mt-2">
                                                                        <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @foreach ($userInfo as $key => $value)
                                                            @if ($key == 'comment')
                                                                <li class="list-group-item">
                                                                    <strong>Komentar:</strong>
                                                                    @if(!empty($value))
                                                                        {{ $value }} <span style="color: blue;">@</span><span
                                                                            style="color: blue;">{{ json_decode($userinformation)->pic }}</span>
                                                                    @else
                                                                        Tidak ada
                                                                    @endif
                                                            @endif
                                                        @endforeach

                                                            <!-- Form untuk Mengirim -->
                                                            @if ($unitvalidation == "Aktif" && $PEmanagervalidation == "Aktif" && $seniormanagervalidation != "Aktif" && $yourrule != "MTPR")
                                                                @if($yourrule != "Manager Product Engineering")
                                                                    <div class="card-text">
                                                                        <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                            action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                            <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                            <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                                            <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                                            <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                                            <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                            <input type="hidden" name="decision" value="Terkirim">
                                                                            <div class="form-group">
                                                                                <label for="level">Send:</label>
                                                                                <select name="level" id="level" class="form-control">
                                                                                    <option value="Senior Manager Engineering">Senior Manager Engineering</option>
                                                                                </select>
                                                                            </div>
                                                                            <button type="button" class="btn btn-success mt-2"
                                                                                onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung
                                                                                Kirim</button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    @if($MPEvalidation != "Aktif")
                                                                        <div class="card-text">
                                                                            <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                                action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                                <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                                                <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                                <input type="hidden" name="decision" value="Terkirim">
                                                                                <div class="form-group">
                                                                                    <label for="level">Send:</label>
                                                                                    <select name="level" id="level" class="form-control">
                                                                                        <option value="Senior Manager Engineering">Senior Manager Engineering</option>
                                                                                    </select>
                                                                                </div>
                                                                                <button type="button" class="btn btn-success mt-2"
                                                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung
                                                                                    Kirim</button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                    @endif
                                                @endif
                                                    {{--Akhir123--}}
                                            @endif
                                        @endif
                                @endforeach
                                    <!-- Tombol Upload -->
                                    @if ($unitvalidation == "Aktif" && $PEmanagervalidation == "Aktif" && $seniormanagervalidation != "Aktif" && !in_array($yourrule, ["MTPR"]) && !in_array(json_decode($userinformation)->level, ["MTPR", "Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]))
                                        <p class="mt-3">
                                            <a href="{{ route('document.uploadfeedback', $document->id) }}"
                                                class="btn btn-success btn-sm feedback-upload-btn">Upload Review</a>
                                        </p>
                                        <p class="mt-2"><strong>{{ $status }}</strong></p>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
    {{-- Manager Product Engineering Akhir --}}
@endif







{{-- Senior Manager Aawal --}}
@if (in_array($yourrule, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi", "MTPR", "superuser"]))
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                {{-- Awalan --}}
                {{-- "Manager Product Engineering","Senior Manager Engineering", "Senior Manager Desain", "Senior Manager
                Teknologi Produksi" is in section --}}
                <!-- MULTI CHARTS -->
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Review Senior Manager:</h1>
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
                        @foreach ($userinformations as $i => $userinformation)
                                        @php
                                            $level = json_decode($userinformation)->level;
                                            $pic = json_decode($userinformation)->pic;
                                            $sumberinformasi = json_decode($userinformation)->userinformations;
                                            $filenames = json_decode($userinformation)->listfilenames;
                                            $metadatas = json_decode($userinformation)->listmetadatas;
                                            $linkfiles = json_decode($userinformation)->listlinkfiles;
                                        @endphp
                                        @if ($userinformation != "")
                                            @if (!in_array(json_decode($userinformation)->level, ["MTPR"]) && (in_array(json_decode($userinformation)->level, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) || in_array(json_decode($userinformation)->pic, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])))
                                                {{--Mulai123--}}
                                                @if(json_decode($userinformation)->level == $yourrule || json_decode($userinformation)->pic == $yourrule || $yourrule == "MTPR" || $yourrule == "superuser")
                                                    @if (($level && !in_array($level, ["MTPR"])) || ($pic && in_array($pic, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])))
                                                        <!-- Selengkapnya -->
                                                        <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                        <p>
                                                            @if ($userinformations)
                                                                <div class="card mt-3">
                                                                    <div class="info-container mt-2" style="display: none;"> <!-- hide by default -->
                                                                        <div class="card-body">
                                                                            <h5 class="card-title"></h5>
                                                                            <ul class="list-group list-group-flush">
                                                                                @php
                                                                                    try {
                                                                                        $userInfo = json_decode($sumberinformasi, true);

                                                                                        // Check for JSON decoding errors
                                                                                        if (json_last_error() != JSON_ERROR_NONE) {
                                                                                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                                                                        }
                                                                                    } catch (\Exception $e) {
                                                                                        // Log the error message or throw an exception
                                                                                        \Log::error('Error decoding JSON: ' . $e->getMessage());

                                                                                        // Handle decoding error, for example:
                                                                                        $userInfo = [];
                                                                                    }
                                                                                @endphp
                                                                                <li class="list-group-item">
                                                                                    <strong>Penerima:</strong>
                                                                                    {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                                </li>
                                                                                @foreach ($userInfo as $key => $value)
                                                                                    @if ($key != 'sudahdibaca' && $key != 'comment' && $key != 'time' && $key != 'conditionoffile' && $key != 'hasilreview')
                                                                                        <li class="list-group-item">
                                                                                            <strong>{{ $key }}:</strong>
                                                                                            {{ $value ?: 'Kosong' }}
                                                                                        </li>
                                                                                    @endif
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </p>
                                                        @foreach ($userInfo as $key => $value)
                                                            @if ($key == 'time')
                                                                <li class="list-group-item">
                                                                    <strong>Waktu:</strong>
                                                                    @php
                                                                        $sendtime = $value;
                                                                        $datetime = new DateTime($value, new DateTimeZone('UTC'));
                                                                        $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                                    @endphp
                                                                    {!! $formattedTime ?? 'Kosong' !!}
                                                                </li>
                                                            @elseif ($key == 'hasilreview')
                                                                <li class="list-group-item">
                                                                    <strong>Status Dokumen</strong>
                                                                    {{ $value ?: 'Kosong' }}
                                                                </li>
                                                            @elseif ($key == 'conditionoffile')
                                                                <li class="list-group-item">
                                                                    <strong>Status:</strong>
                                                                    {{ ucfirst($value ?: 'Kosong') }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                        @if ($filenames)
                                                            <div class="card feedback-item">
                                                                <div class="card-text-item">
                                                                    <strong>File:</strong>
                                                                </div>

                                                                @foreach ($filenames as $index => $filename)
                                                                    @php
                                                                        $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                                    @endphp
                                                                    <div class="card-text mt-2">
                                                                        <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @foreach ($userInfo as $key => $value)
                                                            @if ($key == 'comment')
                                                                <li class="list-group-item">
                                                                    <strong>Komentar:</strong>
                                                                    @if(!empty($value))
                                                                        {{ $value }} <span style="color: blue;">@</span><span
                                                                            style="color: blue;">{{ json_decode($userinformation)->pic }}</span>
                                                                    @else
                                                                        Tidak ada
                                                                    @endif
                                                            @endif
                                                        @endforeach
                                                            <!-- Form untuk Mengirim -->
                                                            @if ($unitvalidation == "Aktif" && $PEmanagervalidation == "Aktif" && $seniormanagervalidation != "Aktif" && $yourrule != "MTPR")
                                                                @if($yourrule != "Manager Product Engineering")
                                                                    <div class="card-text">
                                                                        <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                            action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                            <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                            <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                                            <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                                            <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                                            <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                            <input type="hidden" name="decision" value="Terkirim">
                                                                            <div class="form-group">
                                                                                <label for="level">Send:</label>
                                                                                <select name="level" id="level" class="form-control">
                                                                                    <option value="MTPR">MTPR</option>
                                                                                </select>
                                                                            </div>
                                                                            <button type="button" class="btn btn-success mt-2"
                                                                                onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung
                                                                                Kirim</button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    @if($MPEvalidation != "Aktif")
                                                                        <div class="card-text">
                                                                            <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                                action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                                <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                                                <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                                <input type="hidden" name="decision" value="Terkirim">
                                                                                <div class="form-group">
                                                                                    <label for="level">Send:</label>
                                                                                    <select name="level" id="level" class="form-control">
                                                                                        <option value="MTPR">MTPR</option>
                                                                                    </select>
                                                                                </div>
                                                                                <button type="button" class="btn btn-success mt-2"
                                                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Langsung
                                                                                    Kirim</button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                    @endif
                                                @endif
                                                    {{--Akhir123--}}
                                            @endif
                                        @endif
                        @endforeach
                            <!-- Tombol Upload -->
                            @if ($unitvalidation == "Aktif" && $PEmanagervalidation == "Aktif" && $seniormanagervalidation != "Aktif" && !in_array($yourrule, ["MTPR"]))
                                <p class="mt-3">
                                    <a href="{{ route('document.uploadfeedback', $document->id) }}"
                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Review</a>
                                </p>
                                <p class="mt-2"><strong>{{ $status }}</strong></p>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- Senior Manager Akhir --}}




{{-- Validasi Awal --}}
@if (in_array($yourrule, ["MTPR", "superuser"]))
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
                        @foreach ($userinformations as $i => $userinformation)
                                @if ($userinformation != "")
                                        @if (in_array(json_decode($userinformation)->level, ["MTPR", "superuser"]))
                                                <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                <p>
                                                    @php
                                                        $sumberinformasi = json_decode($userinformation)->userinformations;
                                                        $filenames = (json_decode($userinformation)->listfilenames);
                                                        $metadatas = (json_decode($userinformation)->listmetadatas);
                                                        $linkfiles = (json_decode($userinformation)->listlinkfiles);
                                                      @endphp
                                                    @if ($userinformations)
                                                        <div class="card mt-3">
                                                            <div class="info-container mt-2" style="display: none;"> <!-- hide by default -->
                                                                <div class="card-body">
                                                                    <h5 class="card-title"></h5>

                                                                    <ul class="list-group list-group-flush">
                                                                        @php
                                                                            try {
                                                                                $userInfo = json_decode($sumberinformasi, true);
                                                                                if (json_last_error() != JSON_ERROR_NONE) {
                                                                                    throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                                                                }
                                                                            } catch (\Exception $e) {
                                                                                \Log::error('Error decoding JSON: ' . $e->getMessage());
                                                                                $userInfo = [];
                                                                            }
                                                                          @endphp
                                                                        <li class="list-group-item">
                                                                            <strong>Penerima:</strong>
                                                                            {{ json_decode($userinformation)->level ?? "hanya upload & tidak dikirim" }}
                                                                        </li>
                                                                        @foreach ($userInfo as $key => $value)
                                                                            @if ($key == 'sudahdibaca')
                                                                                <li class="list-group-item">
                                                                                    <strong>Apakah anda sudah melakukan review atas dokumen approval?</strong>
                                                                                    {{ $value ?: 'Kosong' }}
                                                                                </li>
                                                                            @elseif ($key != 'comment' && $key != 'time' && $key != 'conditionoffile' && $key != 'hasilreview')
                                                                                <li class="list-group-item">
                                                                                    <strong>{{ $key }}:</strong>
                                                                                    {{ $value ?: 'Kosong' }}
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </p>
                                                @foreach ($userInfo as $key => $value)
                                                    @if ($key == 'time')
                                                        <li class="list-group-item">
                                                            <strong>Waktu:</strong>
                                                            @php
                                                                $sendtime = $value;
                                                                $datetime = new DateTime($value, new DateTimeZone('UTC'));
                                                                $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                                $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                              @endphp
                                                            {!! $formattedTime ?? 'Kosong' !!}
                                                        </li>
                                                    @elseif ($key == 'hasilreview')
                                                        <li class="list-group-item">
                                                            <strong>Status Dokumen</strong>
                                                            {{ $value ?: 'Kosong' }}
                                                        </li>
                                                    @elseif ($key == 'conditionoffile')
                                                        <li class="list-group-item">
                                                            <strong>Status:</strong>
                                                            {{ ucfirst($value ?: 'Kosong') }}
                                                            @php
                                                                $statussetuju = $value;
                                                            @endphp
                                                        </li>
                                                    @endif
                                                @endforeach
                                                @if ($filenames)
                                                    <div class="card feedback-item">
                                                        <div class="card-text-item">
                                                            <strong>File:</strong>
                                                        </div>
                                                        @foreach ($filenames as $index => $filename)
                                                            @php
                                                                $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                                                              @endphp
                                                            <div class="card-text mt-2">
                                                                <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $filename }}</a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @foreach ($userInfo as $key => $value)
                                                    @if ($key == 'comment')
                                                        <li class="list-group-item">
                                                            <strong>Komentar:</strong>
                                                            @if(!empty($value))
                                                                {{ $value }} <span style="color: blue;">@</span><span
                                                                    style="color: blue;">{{ json_decode($userinformation)->pic }}</span>
                                                            @else
                                                                Tidak ada
                                                            @endif
                                                    @endif
                                                @endforeach
                                                    @if ($unitvalidation == "Aktif" && $PEmanagervalidation == "Aktif" && $seniormanagervalidation == "Aktif" && $MTPRvalidation != "Aktif")
                                                        <div class="col-md-4">
                                                            <form id="sendForm{{ $document->id }}{{$sendtime}}" method="POST"
                                                                action="{{ route('sendfoward.Document', ['id' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                                <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                                <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                                <input type="hidden" name="level" value="selesai">
                                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                                <input type="hidden" name="decision" value="Dokumen ditutup">
                                                                <button type="button" class="btn btn-success mt-2"
                                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{$sendtime}}')">Setujui</button>
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
    $(document).ready(function () {
        $('.toggle-info').click(function () {
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