@php
    $statussetujulist = [];
    $yourrule = auth()->user()->rule;
@endphp

@extends('layouts.split3')

@section('container1')

{{-- Dokumen informasi Awal --}}
<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <div class="info-box-content">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Informasi Dokumen RAMS:</h1>
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

                    <p class="card-text"><strong>Nama Dokumen:</strong> {{ $document->documentname }}</p>

                    <p class="card-text"><strong>No Dokumen:</strong> {{ $document->documentnumber }}</p>

                    <p class="card-text"><strong>Project:</strong> {{ $document->proyek_type }}</p>

                    <!-- Add more fields as needed -->
                    @if ($files)
                                    <div class="card feedback-item">
                                        <div class="card-text-item">
                                            <strong>File:</strong>
                                        </div>
                                        <p class="card-text"><strong>File:</strong>
                                            @foreach ($files as $file)
                                                                    @php
                                                                        $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                    @endphp
                                                                <div class="card-text mt-2">
                                                                    @include('rams.fileinfo', ['file' => $file])
                                                                </div>
                                            @endforeach
                                        </p>
                                    </div>
                    @endif
                    <!-- Add more fields as needed -->

                    <!-- Add more fields as needed -->
                    @if($ramsUnit)
                        <div class="form-group">
                            <label for="hazard_unit"><strong>Unit Terlibat:</strong></label>
                            <ul>

                                @foreach ($ramsUnit as $unit)
                                    <li>{{ $unit }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Add more fields as needed -->

                </div>
            </div>
        </div>
    </div>
</div>
{{-- Dokumen informasi Akhir --}}

@endsection

@section('container2')


{{--Feedback Unit Awal--}}
@foreach($ramsUnit as $unit)   
                        @if (str_contains($yourrule, $unit) || str_contains($yourrule, "RAMS"))
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
                                                @php
                                                    $listcount = 1;
                                                @endphp
                                                @foreach ($feedbacks as $ramsdocumentfeedback)
                                                        @if ((str_contains($yourrule, $unit) || str_contains($yourrule, "RAMS")) && ($ramsdocumentfeedback->conditionoffile2 == 'feedback') && ($ramsdocumentfeedback->level == $unit))
                                                                <div class="card mt-3">
                                                                    <div class="info-container mt-2" style="display: none;">
                                                                        <div class="card-body">
                                                                            <h5 class="card-title"></h5>
                                                                            <ul class="list-group list-group-flush">
                                                                                <li class="list-group-item">
                                                                                    @if($ramsdocumentfeedback->level == $yourrule)
                                                                                        <button class="btn" style="background-color: orange;">
                                                                                            <strong>Status: Penerima dari:</strong>
                                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                                        </button>
                                                                                    @elseif($ramsdocumentfeedback->level == "")
                                                                                        <button class="btn" style="background-color: yellow;">
                                                                                            <strong>Upload Pribadi</strong>
                                                                                        </button>
                                                                                    @else
                                                                                        <button class="btn" style="background-color: red;">
                                                                                            <strong>Status: Terkirim ke:</strong>
                                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                                        </button>
                                                                                    @endif
                                                                                </li>
                                                                                <li class="list-group-item">
                                                                                    <strong>Nama Penulis:</strong>
                                                                                    {{ $ramsdocumentfeedback->author ?: 'Kosong' }}
                                                                                </li>
                                                                                <li class="list-group-item">
                                                                                    <strong>Email:</strong>
                                                                                    {{ $ramsdocumentfeedback->email ?: 'Kosong' }}
                                                                                </li>
                                                                                <li class="list-group-item">
                                                                                    <strong>Jenis:</strong>
                                                                                    {{ $ramsdocumentfeedback->conditionoffile2 ?: 'Kosong' }}
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <li class="list-group-item">
                                                                    <strong>Feedback {{$listcount++}}:</strong>





                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Waktu:</strong>
                                                                    @php
                                                                        $datetime = new DateTime($ramsdocumentfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                                        $sendtime = $formattedTime;
                                                                    @endphp

                                                                    {!! $formattedTime ?? 'Kosong' !!}


                                                                </li>

                                                                <li class="list-group-item">
                                                                    <strong>Status Feedback:</strong>
                                                                    @php
                                                                        $status = ucfirst($ramsdocumentfeedback->conditionoffile ?: 'Kosong');
                                                                        $statussetujulist[$unit] = $status;
                                                                        $badgeClass = '';

                                                                        // Mengatur kelas badge berdasarkan kondisi
                                                                        switch ($ramsdocumentfeedback->conditionoffile) {
                                                                            case 'respond':
                                                                                $badgeClass = 'badge badge-primary';
                                                                                break;
                                                                            case 'approve':
                                                                                $badgeClass = 'badge badge-success';
                                                                                break;
                                                                            case 'incomplete':
                                                                                $badgeClass = 'badge badge-warning';
                                                                                break;
                                                                            case 'wrong':
                                                                                $badgeClass = 'badge badge-danger';
                                                                                break;
                                                                            default:
                                                                                $badgeClass = 'badge badge-secondary';
                                                                                break;
                                                                        }
                                                                    @endphp

                                                                    <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                                </li>


                                                                @if ($ramsdocumentfeedback->feedbackfiles->isNotEmpty())
                                                                    <div class="card feedback-item">
                                                                        <li class="list-group-item">
                                                                            <div class="card-text-item">
                                                                                <strong>Files:</strong>
                                                                            </div>



                                                                            @foreach ($ramsdocumentfeedback->feedbackfiles as $file)
                                                                                    @php
                                                                                        $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                                    @endphp
                                                                                    <div class="card-text mt-2">
                                                                                        @include('rams.fileinfo', ['file' => $file])
                                                                                    </div>
                                                                            @endforeach
                                                                        </li>
                                                                    </div>
                                                                @endif

                                                                <li class="list-group-item">
                                                                    <strong>Komentar:</strong>
                                                                    @if(!empty($ramsdocumentfeedback->comment))
                                                                        {{ $ramsdocumentfeedback->comment}} <span style="color: blue;">@</span><span
                                                                            style="color: blue;">{{ $ramsdocumentfeedback->author }}</span>
                                                                    @else
                                                                        Kosong
                                                                    @endif
                                                                </li>

                                                                <li class="list-group-item">
                                                                    <div class="row">
                                                                        @if (str_contains($yourrule, "RAMS"))
                                                                            <form id="deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                                action="{{ route('ramsdocuments.feedback.destroy', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="button" class="btn btn-warning mt-2"
                                                                                    onclick="confirmDecision('deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                                            </form>
                                                                        @endif
                                                                        @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit)) && ($ramsdocumentfeedback->conditionoffile == "draft"))
                                                                            <form id="approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                                action="{{ route('ramsdocuments.feedback.approve', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                                @csrf
                                                                                <button type="button" class="btn btn-success mt-2"
                                                                                    onclick="confirmDecision('approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                                            </form>
                                                                        @endif
                                                                        <!-- @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit))&&($ramsdocumentfeedback->conditionoffile=="draft"))
                                                                                                                                                                                                                                                                                                                                                                                                                    <form id="rejectFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('ramsdocuments.feedback.reject', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                                                                                                                                                                                                                                                                                                                                                                        @csrf
                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-danger mt-2" onclick="confirmDecision('rejectFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Tolak</button>
                                                                                                                                                                                                                                                                                                                                                                                                                    </form>
                                                                                                                                                                                                                                                                                                                                                                                                                @endif -->

                                                                        <button class="btn btn-sm mt-2 btn-info toggle-info">Selengkapnya</button>
                                                                    </div>
                                                                </li>
                                                        @endif
                                                @endforeach
                                                @php
                                                    $status = $unitpicvalidation[$unit];    
                                                @endphp
                                                @if($status != "Aktif")
                                                    @if(str_contains($yourrule, "RAMS"))
                                                        <p class="mt-3">
                                                            <a href="{{ route('ramsdocuments.feedback', ['id' => $document->id, 'level' => $unit]) }}"
                                                                class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback RAMS</a>
                                                        </p>
                                                    @elseif(str_contains($yourrule, $unit))
                                                        <p class="mt-3">
                                                            <a href="{{ route('ramsdocuments.feedback', ['id' => $document->id, 'level' => $unit]) }}"
                                                                class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback {{ $unit }}</a>
                                                        </p>
                                                    @endif
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
@endforeach
{{--Feedback Unit Akhir--}}



{{--Combine--}}
@if (in_array($yourrule, ["RAMS"]))
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Finalisasi Feedback Unit</h1>
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


                        @foreach ($feedbacks as $ramsdocumentfeedback)
                                    @if ($ramsdocumentfeedback->conditionoffile2 == "combine" && $ramsdocumentfeedback->conditionoffile == "approve")
                                                <div class="card mt-3">
                                                    <div class="info-container mt-2" style="display: none;">
                                                        <div class="card-body">
                                                            <h5 class="card-title"></h5>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">
                                                                    @if($ramsdocumentfeedback->level == $yourrule)
                                                                        <button class="btn" style="background-color: orange;">
                                                                            <strong>Status: Penerima dari:</strong>
                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @elseif($ramsdocumentfeedback->level == "")
                                                                        <button class="btn" style="background-color: yellow;">
                                                                            <strong>Upload Pribadi</strong>
                                                                        </button>
                                                                    @else
                                                                        <button class="btn" style="background-color: red;">
                                                                            <strong>Status: Terkirim ke:</strong>
                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @endif
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Nama Penulis:</strong>
                                                                    {{ $ramsdocumentfeedback->author ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Email:</strong>
                                                                    {{ $ramsdocumentfeedback->email ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Jenis:</strong>
                                                                    {{ $ramsdocumentfeedback->conditionoffile2 ?: 'Kosong' }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <li class="list-group-item">
                                                    <strong>Combine</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Waktu:</strong>
                                                    @php
                                                        $datetime = new DateTime($ramsdocumentfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                        $sendtime = $formattedTime;
                                                    @endphp

                                                    {!! $formattedTime ?? 'Kosong' !!}


                                                </li>

                                                <li class="list-group-item">
                                                    <strong>Status Feedback:</strong>
                                                    @php
                                                        $status = ucfirst($ramsdocumentfeedback->conditionoffile ?: 'Kosong');
                                                        $statussetujulist[$unit] = $status;
                                                        $badgeClass = '';

                                                        // Mengatur kelas badge berdasarkan kondisi
                                                        switch ($ramsdocumentfeedback->conditionoffile) {
                                                            case 'respond':
                                                                $badgeClass = 'badge badge-primary';
                                                                break;
                                                            case 'approve':
                                                                $badgeClass = 'badge badge-success';
                                                                break;
                                                            case 'incomplete':
                                                                $badgeClass = 'badge badge-warning';
                                                                break;
                                                            case 'wrong':
                                                                $badgeClass = 'badge badge-danger';
                                                                break;
                                                            default:
                                                                $badgeClass = 'badge badge-secondary';
                                                                break;
                                                        }
                                                    @endphp

                                                    <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                </li>


                                                @if ($ramsdocumentfeedback->feedbackfiles->isNotEmpty())
                                                    <div class="card feedback-item">
                                                        <li class="list-group-item">
                                                            <div class="card-text-item">
                                                                <strong>Files:</strong>
                                                            </div>


                                                            @foreach ($ramsdocumentfeedback->feedbackfiles as $file)
                                                                        @php
                                                                            $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                        @endphp
                                                                        <div class="card-text mt-2">
                                                                            @include('rams.fileinfo', ['file' => $file])
                                                                        </div>
                                                            @endforeach
                                                        </li>
                                                    </div>
                                                @endif

                                                <li class="list-group-item">
                                                    <strong>Komentar:</strong>
                                                    @if(!empty($ramsdocumentfeedback->comment))
                                                        {{ $ramsdocumentfeedback->comment}} <span style="color: blue;">@</span><span
                                                            style="color: blue;">{{ $ramsdocumentfeedback->author }}</span>
                                                    @else
                                                        Kosong
                                                    @endif
                                                </li>

                                                <li class="list-group-item">
                                                    <div class="row">
                                                        @if (str_contains($yourrule, "RAMS"))
                                                            <form id="deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                action="{{ route('ramsdocuments.feedback.destroy', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-warning mt-2"
                                                                    onclick="confirmDecision('deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                            </form>
                                                        @endif
                                                        @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit)) && ($ramsdocumentfeedback->conditionoffile == "draft"))
                                                            <form id="approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                action="{{ route('ramsdocuments.feedback.approve', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                @csrf
                                                                <button type="button" class="btn btn-success mt-2"
                                                                    onclick="confirmDecision('approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                            </form>
                                                        @endif

                                                        <button class="btn btn-sm mt-2 btn-info toggle-info">Selengkapnya</button>
                                                    </div>
                                                </li>
                                                @php
                                                    $listpic = ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"];
                                                @endphp
                                                @if($ramscombinesendvalidation != "Aktif")
                                                    <div class="card-text">
                                                        <form id="sendForm{{ $ramsdocumentfeedback->id }}" method="GET"
                                                            action="{{ route('ramsdocuments.sendSM', ['id' => $ramsdocumentfeedback->id]) }}">
                                                            <div class="form-group">
                                                                <label>SM yang dikirim:</label><br>
                                                                @foreach($listpic as $pic)
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="checkbox" name="sm_unit[]" value="{{ $pic }}">
                                                                        <label class="form-check-label">{{ $pic }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="submit" class="btn btn-success mt-2">Langsung Kirim</button>
                                                        </form>
                                                    </div>
                                                @endif

                                    @endif

                        @endforeach

                        @if($ramscombinevalidation != "Aktif" && $unitvalidation == "Aktif")
                            @if(str_contains($yourrule, "RAMS"))
                                <p class="mt-3">
                                    <a href="{{ route('ramsdocuments.combine', ['id' => $document->id, 'level' => 'RAMS']) }}"
                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Rangkuman Feedback RAMS</a>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{--Combine Akhir--}}



{{--Feedback Senior Manager--}}
@php
    $SMunits = [];
    foreach ($feedbacks as $ramsdocumentfeedback) {
        $conditionoffile = $ramsdocumentfeedback->conditionoffile;
        $level = $ramsdocumentfeedback->level;

        if ($conditionoffile === 'filesend' && !in_array($level, $SMunits)) {
            $SMunits[] = $level;
        }
    }
@endphp


@if (in_array($yourrule, $SMunits) || str_contains($yourrule, "RAMS"))
    {{--Feedback Unit Awal--}}
    @foreach($SMunits as $SMunit)   
                                                @if($yourrule == "RAMS" || $yourrule == $SMunit)
                                                    <div class="col-md-3 col-sm-6 col-12">
                                                        <div class="info-box">
                                                            <div class="info-box-content">
                                                                <!-- MULTI CHARTS -->
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <h1 class="card-title">Approve {{ $SMunit }}</h1>
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
                                                                            $listcount = 1;
                                                                        @endphp
                                                                        @foreach ($feedbacks as $ramsdocumentfeedback)
                                                                            @if (($SMunit == $yourrule || $yourrule == "RAMS") && $ramsdocumentfeedback->conditionoffile2 == "smfeedback" && $ramsdocumentfeedback->level == $SMunit && ($ramsdocumentfeedback->pic == $SMunit || $ramsdocumentfeedback->pic == "RAMS"))
                                                                                <div class="card mt-3">
                                                                                    <div class="info-container mt-2" style="display: none;">
                                                                                        <div class="card-body">
                                                                                            <h5 class="card-title"></h5>
                                                                                            <ul class="list-group list-group-flush">
                                                                                                <li class="list-group-item">
                                                                                                    @if($ramsdocumentfeedback->level == $yourrule)
                                                                                                        <button class="btn" style="background-color: orange;">
                                                                                                            <strong>Status: Penerima dari:</strong>
                                                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                                                        </button>
                                                                                                    @elseif($ramsdocumentfeedback->level == "")
                                                                                                        <button class="btn" style="background-color: yellow;">
                                                                                                            <strong>Upload Pribadi</strong>
                                                                                                        </button>
                                                                                                    @else
                                                                                                        <button class="btn" style="background-color: red;">
                                                                                                            <strong>Status: Terkirim ke:</strong>
                                                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                                                        </button>
                                                                                                    @endif
                                                                                                </li>
                                                                                                <li class="list-group-item">
                                                                                                    <strong>Nama Penulis:</strong>
                                                                                                    {{ $ramsdocumentfeedback->author ?: 'Kosong' }}
                                                                                                </li>
                                                                                                <li class="list-group-item">
                                                                                                    <strong>Email:</strong>
                                                                                                    {{ $ramsdocumentfeedback->email ?: 'Kosong' }}
                                                                                                </li>
                                                                                                <li class="list-group-item">
                                                                                                    <strong>Jenis:</strong>
                                                                                                    {{ $ramsdocumentfeedback->conditionoffile2 ?: 'Kosong' }}
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <li class="list-group-item">
                                                                                    @php
                                                                                        $status = ucfirst($ramsdocumentfeedback->conditionoffile ?: 'Kosong');
                                                                                    @endphp
                                                                                    @if ($status != 'Filesend')
                                                                                        <strong>Feedback {{$listcount++}}:</strong>
                                                                                    @endif
                                                                                </li>
                                                                                <li class="list-group-item">
                                                                                    <strong>Waktu:</strong>
                                                                                    @php
                                                                                        $datetime = new DateTime($ramsdocumentfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                                                        $sendtime = $formattedTime;
                                                                                    @endphp

                                                                                    {!! $formattedTime ?? 'Kosong' !!}


                                                                                </li>

                                                                                <li class="list-group-item">
                                                                                    <strong></strong>
                                                                                    @php
                                                                                        $status = ucfirst($ramsdocumentfeedback->conditionoffile ?: 'Kosong');
                                                                                        $statussetujulist[$SMunit] = $status;
                                                                                        $badgeClass = '';

                                                                                        // Mengatur kelas badge berdasarkan kondisi
                                                                                        switch ($ramsdocumentfeedback->conditionoffile) {
                                                                                            case 'respond':
                                                                                                $badgeClass = 'badge badge-primary';
                                                                                                break;
                                                                                            case 'approve':
                                                                                                $badgeClass = 'badge badge-success';
                                                                                                break;
                                                                                            case 'incomplete':
                                                                                                $badgeClass = 'badge badge-warning';
                                                                                                break;
                                                                                            case 'wrong':
                                                                                                $badgeClass = 'badge badge-danger';
                                                                                                break;
                                                                                            default:
                                                                                                $badgeClass = 'badge badge-secondary';
                                                                                                break;
                                                                                        }
                                                                                    @endphp

                                                                                    <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                                                </li>

                                                                                @if ($ramsdocumentfeedback->feedbackfiles->isNotEmpty())
                                                                                    <div class="card feedback-item">
                                                                                        <li class="list-group-item">
                                                                                            <div class="card-text-item">
                                                                                                <strong>Files:</strong>
                                                                                            </div>
                                                                                            @foreach ($ramsdocumentfeedback->feedbackfiles as $file)
                                                                                                @php
                                                                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                                                @endphp
                                                                                                <div class="card-text mt-2">
                                                                                                    @include('rams.fileinfo', ['file' => $file])
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </li>
                                                                                    </div>
                                                                                @endif

                                                                                <li class="list-group-item">
                                                                                    <strong>Komentar:</strong>
                                                                                    @if(!empty($ramsdocumentfeedback->comment))
                                                                                        {{ $ramsdocumentfeedback->comment}} <span style="color: blue;">@</span><span
                                                                                            style="color: blue;">{{ $ramsdocumentfeedback->author }}</span>
                                                                                    @else
                                                                                        Kosong
                                                                                    @endif
                                                                                </li>

                                                                                <li class="list-group-item">
                                                                                    <div class="row">
                                                                                        @if (str_contains($yourrule, "RAMS"))
                                                                                            <form id="deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                                                action="{{ route('ramsdocuments.feedback.destroy', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="button" class="btn btn-warning mt-2"
                                                                                                    onclick="confirmDecision('deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                                                            </form>
                                                                                        @endif
                                                                                        @if ((str_contains($yourrule, "Senior Manager") && str_contains($yourrule, $SMunit)) && ($ramsdocumentfeedback->conditionoffile == "draft"))
                                                                                            <form id="approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                                                action="{{ route('ramsdocuments.feedback.approve', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                                                @csrf
                                                                                                <button type="button" class="btn btn-success mt-2"
                                                                                                    onclick="confirmDecision('approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                                                            </form>
                                                                                        @endif

                                                                                        <button class="btn btn-sm mt-2 btn-info toggle-info">Selengkapnya</button>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                        @php
                                                                            $status = $smunitpicvalidation[$SMunit];    
                                                                        @endphp
                                                                        @if($status != "Aktif")
                                                                            @if(str_contains($yourrule, "RAMS"))
                                                                                <p class="mt-3">
                                                                                    <a href="{{ route('ramsdocuments.smfeedback', ['id' => $document->id, 'level' => $SMunit]) }}"
                                                                                        class="btn btn-success btn-sm feedback-upload-btn">Respon Approve {{ $SMunit }}</a>
                                                                                </p>
                                                                            @elseif(str_contains($yourrule, $SMunit))
                                                                                <p class="mt-3">
                                                                                    <a href="{{ route('ramsdocuments.smfeedback', ['id' => $document->id, 'level' => $SMunit]) }}"
                                                                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Approve {{ $SMunit }}</a>
                                                                                </p>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
    @endforeach
    {{--Feedback Unit Akhir--}}
@endif
{{--Feedback Senior Manager Akhir--}}



{{--Finalisasi--}}
@if (in_array($yourrule, ["RAMS"]))
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Finalisasi Feedback Unit</h1>
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


                        @foreach ($feedbacks as $ramsdocumentfeedback)
                                    @if ($ramsdocumentfeedback->conditionoffile2 == "finalisasi" && $ramsdocumentfeedback->conditionoffile == "approve")
                                                <div class="card mt-3">
                                                    <div class="info-container mt-2" style="display: none;">
                                                        <div class="card-body">
                                                            <h5 class="card-title"></h5>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">
                                                                    @if($ramsdocumentfeedback->level == $yourrule)
                                                                        <button class="btn" style="background-color: orange;">
                                                                            <strong>Status: Penerima dari:</strong>
                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @elseif($ramsdocumentfeedback->level == "")
                                                                        <button class="btn" style="background-color: yellow;">
                                                                            <strong>Upload Pribadi</strong>
                                                                        </button>
                                                                    @else
                                                                        <button class="btn" style="background-color: red;">
                                                                            <strong>Status: Terkirim ke:</strong>
                                                                            {{ $ramsdocumentfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                        </button>
                                                                    @endif
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Nama Penulis:</strong>
                                                                    {{ $ramsdocumentfeedback->author ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Email:</strong>
                                                                    {{ $ramsdocumentfeedback->email ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Jenis:</strong>
                                                                    {{ $ramsdocumentfeedback->conditionoffile2 ?: 'Kosong' }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <li class="list-group-item">
                                                    <strong>Combine</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Waktu:</strong>
                                                    @php
                                                        $datetime = new DateTime($ramsdocumentfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                        $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                        $sendtime = $formattedTime;
                                                    @endphp

                                                    {!! $formattedTime ?? 'Kosong' !!}


                                                </li>

                                                <li class="list-group-item">
                                                    <strong>Status Feedback:</strong>
                                                    @php
                                                        $status = ucfirst($ramsdocumentfeedback->conditionoffile ?: 'Kosong');
                                                        $statussetujulist[$unit] = $status;
                                                        $badgeClass = '';

                                                        // Mengatur kelas badge berdasarkan kondisi
                                                        switch ($ramsdocumentfeedback->conditionoffile) {
                                                            case 'respond':
                                                                $badgeClass = 'badge badge-primary';
                                                                break;
                                                            case 'approve':
                                                                $badgeClass = 'badge badge-success';
                                                                break;
                                                            case 'incomplete':
                                                                $badgeClass = 'badge badge-warning';
                                                                break;
                                                            case 'wrong':
                                                                $badgeClass = 'badge badge-danger';
                                                                break;
                                                            default:
                                                                $badgeClass = 'badge badge-secondary';
                                                                break;
                                                        }
                                                    @endphp

                                                    <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                </li>


                                                @if ($ramsdocumentfeedback->feedbackfiles->isNotEmpty())
                                                    <div class="card feedback-item">
                                                        <li class="list-group-item">
                                                            <div class="card-text-item">
                                                                <strong>Files:</strong>
                                                            </div>
                                                            @foreach ($ramsdocumentfeedback->feedbackfiles as $file)
                                                                        @php
                                                                            $newLinkFile = str_replace('uploads/', '', $file->link);
                                                                        @endphp
                                                                        <div class="card-text mt-2">
                                                                            @include('rams.fileinfo', ['file' => $file])
                                                                        </div>
                                                            @endforeach
                                                        </li>
                                                    </div>
                                                @endif

                                                <li class="list-group-item">
                                                    <strong>Komentar:</strong>
                                                    @if(!empty($ramsdocumentfeedback->comment))
                                                        {{ $ramsdocumentfeedback->comment}} <span style="color: blue;">@</span><span
                                                            style="color: blue;">{{ $ramsdocumentfeedback->author }}</span>
                                                    @else
                                                        Kosong
                                                    @endif
                                                </li>

                                                <li class="list-group-item">
                                                    <div class="row">
                                                        @if (str_contains($yourrule, "RAMS"))
                                                            <form id="deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                action="{{ route('ramsdocuments.feedback.destroy', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-warning mt-2"
                                                                    onclick="confirmDecision('deleteFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                            </form>
                                                        @endif
                                                        @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit)) && ($ramsdocumentfeedback->conditionoffile == "draft"))
                                                            <form id="approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{ $sendtime }}" method="POST"
                                                                action="{{ route('ramsdocuments.feedback.approve', ['documentId' => $document->id, 'feedbackId' => $ramsdocumentfeedback->id]) }}">
                                                                @csrf
                                                                <button type="button" class="btn btn-success mt-2"
                                                                    onclick="confirmDecision('approveFeedbackForm{{ $ramsdocumentfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                            </form>
                                                        @endif

                                                        <button class="btn btn-sm mt-2 btn-info toggle-info">Selengkapnya</button>
                                                    </div>
                                                </li>
                                    @endif
                        @endforeach

                        @if($ramsfinalisasivalidation != "Aktif" && $smunitvalidation == "Aktif")
                            @if(str_contains($yourrule, "RAMS"))
                                <p class="mt-3">
                                    <a href="{{ route('ramsdocuments.finalisasi', ['id' => $document->id, 'level' => 'RAMS']) }}"
                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Rangkuman Feedback SM</a>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{--Finalisasi Akhir--}}




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


@endsection

@section('container3')
<a href="{{ route('ramsdocuments.index') }}" class="btn btn-primary">Back</a>
@endsection