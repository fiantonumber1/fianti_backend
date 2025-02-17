@php
    $statussetujulist=[];
    $yourrule=auth()->user()->rule;
@endphp

@extends('layouts.split3')

@section('container1')
    {{-- Dokumen informasi Awal --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Informasi Hazard Log:</h1>
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

                        <p class="card-text"><strong>Project Type:</strong> {{ $hazardLog->proyek_type }}</p>

                        <p class="card-text"><strong>Hazard Reference:</strong> {{ $hazardLog->hazard_ref }}</p>

                        <p class="card-text"><strong>Operating Mode:</strong> {{ $hazardLog->operating_mode }}</p>

                        <p class="card-text"><strong>System:</strong> {{ $hazardLog->system }}</p>

                        <p class="card-text"><strong>Hazard:</strong> {{ $hazardLog->hazard }}</p>

                        <p class="card-text"><strong>Hazard Cause:</strong> {{ $hazardLog->hazard_cause }}</p>

                        <p class="card-text"><strong>Accident:</strong> {{ $hazardLog->accident }}</p>

                        <p class="card-text"><strong>Verification Evidence Reference:</strong> {{ $hazardLog->verification_evidence_reference }}</p>

                        <p class="card-text"><strong>Validation Evidence Reference:</strong> {{ $hazardLog->validation_evidence_reference }}</p>

                        <p class="card-text"><strong>IF:</strong> {{ $hazardLog->IF }}</p>

                        <p class="card-text"><strong>IS:</strong> {{ $hazardLog->IS }}</p>

                        <p class="card-text"><strong>IR:</strong> {{ $hazardLog->IR ??"" }}</p>

                        <p class="card-text"><strong>Resolution Status:</strong> {{ $hazardLog->resolution_status }}</p>

                        <p class="card-text"><strong>Hazard Status:</strong> {{ $hazardLog->hazard_status }}</p>

                        <p class="card-text"><strong>Source:</strong> {{ $hazardLog->source }}</p>

                        <p class="card-text"><strong>Hazard Owner:</strong> {{ $hazardLog->haz_owner }}</p>

                        <p class="card-text"><strong>RF:</strong> {{ $hazardLog->RF }}</p>

                        <p class="card-text"><strong>RS:</strong> {{ $hazardLog->RS }}</p>

                        <p class="card-text"><strong>RR:</strong> {{ $hazardLog->RR }}</p>

                        <p class="card-text"><strong>Comments:</strong> {{ $hazardLog->comments }}</p>

                        <p class="card-text"><strong>Status:</strong> {{ $hazardLog->status }}</p>

                        <!-- Add more fields as needed -->
                        @if ($hazardUnit)
                            <div class="form-group">
                                <label for="hazard_unit"><strong>Hazard Unit:</strong></label>
                                <ul>
                                    @foreach ($hazardUnit as $unit)
                                        <li>{{ $unit }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Add more fields as needed -->

                        @if($hazardLog->status!="Terhapus")
                            <div class="col-6 mb-2">
                                <form id="closehazardlog{{ $hazardLog->id }}" method="POST" action="{{ route('hazard_logs.deletestatus', ['hazardLogId' => $hazardLog->id]) }}">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-block" onclick="confirmDecision('closehazardlog{{ $hazardLog->id  }}')">Hazard Log dihapus</button>
                                </form>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Dokumen informasi Akhir --}}

@endsection

@section('container2')

    {{--Feedback Unit Awal--}}
    @foreach($hazardUnit as $unit)
        @if (str_contains($yourrule, $unit)||str_contains($yourrule, "RAMS")||str_contains($yourrule, "superuser"))   
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
                                @foreach ($hazardLog->reductionMeasures as $reductionMeasure)
                                    @if($reductionMeasure->unit_name == $unit)
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <strong>Reduction measure:</strong>
                                                        {{ $reductionMeasure->reduction_measure ?: 'Kosong' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Status Reduction Measure:</strong>
                                                        @if(($reductionMeasure->status == "needanswer" && str_contains($yourrule, $unit) && str_contains($yourrule, "Manager")))
                                                            <div class="row">
                                                                <div class="col-6 mb-2">
                                                                    <form id="approveFeedbackForm{{ $reductionMeasure->id }}" method="POST" action="{{ route('hazard_logs.hazardlog.approve', ['hazardLogId' => $hazardLog->id, 'reductionMeasureId' => $reductionMeasure->id]) }}">
                                                                        @csrf
                                                                        <button type="button" class="btn btn-success btn-block" onclick="confirmDecision('approveFeedbackForm{{ $reductionMeasure->id }}')">Setujui</button>
                                                                    </form>
                                                                </div>
                                                                <div class="col-6 mb-2">
                                                                    <form id="rejectFeedbackForm{{ $reductionMeasure->id }}" method="POST" action="{{ route('hazard_logs.hazardlog.reject', ['hazardLogId' => $hazardLog->id, 'reductionMeasureId' => $reductionMeasure->id]) }}">
                                                                        @csrf
                                                                        <input type="hidden" name="reason" id="hiddenReason{{ $reductionMeasure->id }}">
                                                                        <button type="button" class="btn btn-danger btn-block" onclick="showRejectAlert({{ $reductionMeasure->id }})">Tolak</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="badge badge-primary">{{ $reductionMeasure->status }}</div>
                                                            @if($reductionMeasure->status == "reject")
                                                                <div class="mt-2">
                                                                    <strong>Alasan Penolakan:</strong>
                                                                    <span>{{ $reductionMeasure->reason }}</span>
                                                                </div>
                                                                @if($reductionMeasure->forums->isNotEmpty())
                                                                    @foreach ($reductionMeasure->forums as $forum)
                                                                        <a href="{{ route('forums.show', $forum->id) }}" class="btn btn-primary mb-2">Diskusi</a>
                                                                    @endforeach
                                                                    <div class="col-12">
                                                                        <form id="showmakereductionmeasureForm{{ $reductionMeasure->id }}" method="POST" action="{{ route('hazard_logs.hazardlog.add', ['hazardLogId' => $hazardLog->id, 'unit_name' => $unit]) }}">
                                                                            @csrf
                                                                            <input type="hidden" name="reason" id="hiddenReason{{ $reductionMeasure->id }}">
                                                                            <button type="button" class="btn btn-primary btn-block" onclick="showmakereductionmeasure({{ $reductionMeasure->id }})">Buat Reduction Measure</button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <div class="col-12">
                                                                        <form id="makeforumForm{{ $reductionMeasure->id }}" method="POST" action="{{ route('hazard_logs.hazardlog.makeforum', ['hazardLogId' => $hazardLog->id, 'reductionMeasureId' => $reductionMeasure->id]) }}">
                                                                            @csrf
                                                                            <input type="hidden" name="topic" value="{{ $reductionMeasure->id}}_{{$hazardLog->hazard_ref}}_{{$reductionMeasure->reduction_measure}}">
                                                                            <input type="hidden" name="description" value="Diskusi Hazard log">
                                                                            <button type="button" class="btn btn-primary btn-block" onclick="confirmDecision('makeforumForm{{ $reductionMeasure->id }}')">Buat Forum</button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach


                                <script>

                                    function showmakereductionmeasure(id) {
                                        Swal.fire({
                                            title: 'Masukkan Reduction Measure',
                                            input: 'textarea',
                                            inputPlaceholder: 'Ketikkan di sini...',
                                            inputAttributes: {
                                                'aria-label': 'Ketikkan di sini'
                                            },
                                            showCancelButton: true,
                                            confirmButtonText: 'Setujui',
                                            cancelButtonText: 'Batal',
                                            inputValidator: (value) => {
                                                if (!value) {
                                                    return 'Anda harus memasukkan informasi!'
                                                }
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('hiddenReason' + id).value = result.value;
                                                document.getElementById('showmakereductionmeasureForm' + id).submit();
                                            }
                                        });
                                    }

                                    
                                    function showRejectAlert(id) {
                                        Swal.fire({
                                            title: 'Masukkan Alasan Penolakan',
                                            input: 'textarea',
                                            inputLabel: 'Alasan Penolakan',
                                            inputPlaceholder: 'Ketikkan alasan penolakan di sini...',
                                            inputAttributes: {
                                                'aria-label': 'Ketikkan alasan penolakan di sini'
                                            },
                                            showCancelButton: true,
                                            confirmButtonText: 'Tolak',
                                            cancelButtonText: 'Batal',
                                            inputValidator: (value) => {
                                                if (!value) {
                                                    return 'Anda harus memasukkan alasan!'
                                                }
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('hiddenReason' + id).value = result.value;
                                                document.getElementById('rejectFeedbackForm' + id).submit();
                                            }
                                        })
                                    }


                                    function showMakeForum(id) {
                                        Swal.fire({
                                            title: 'Buat Forum Baru',
                                            html:
                                                '<input id="swal-input1" class="swal2-input" placeholder="Topik">' +
                                                '<textarea id="swal-input2" class="swal2-textarea" placeholder="Deskripsi"></textarea>',
                                            focusConfirm: false,
                                            preConfirm: () => {
                                                const topic = document.getElementById('swal-input1').value;
                                                const description = document.getElementById('swal-input2').value;
                                                if (!topic || !description) {
                                                    Swal.showValidationMessage('Topik dan deskripsi harus diisi!');
                                                    return false;
                                                }
                                                return { topic: topic, description: description };
                                            },
                                            showCancelButton: true,
                                            confirmButtonText: 'Buat',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('hiddenTopic' + id).value = result.value.topic;
                                                document.getElementById('hiddenDescription' + id).value = result.value.description;
                                                document.getElementById('makeforumForm' + id).submit();
                                            }
                                        })
                                    }


                                </script>



                                
                                @foreach ($hazardLog->hazardlogfeedback as $hazardlogfeedback)
                                    @if (str_contains($yourrule, $unit)||str_contains($yourrule, "RAMS"))
                                        @if ((str_contains($yourrule, $unit)||str_contains($yourrule, "RAMS"))&&($hazardlogfeedback->conditionoffile2=='feedback')&&($hazardlogfeedback->level==$unit))
                                            <div class="card mt-3">
                                                <div class="info-container mt-2" style="display: none;">
                                                    <div class="card-body">
                                                        <h5 class="card-title"></h5>
                                                        <ul class="list-group list-group-flush">        
                                                            <li class="list-group-item">
                                                                @if($hazardlogfeedback->level == $yourrule)
                                                                    <button class="btn" style="background-color: orange;">
                                                                        <strong>Status: Penerima dari:</strong>
                                                                        {{ $hazardlogfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                    </button>
                                                                @elseif($hazardlogfeedback->level == "")
                                                                    <button class="btn" style="background-color: yellow;">
                                                                        <strong>Upload Pribadi</strong>
                                                                    </button>
                                                                @else
                                                                    <button class="btn" style="background-color: red;">
                                                                        <strong>Status: Terkirim ke:</strong>
                                                                        {{ $hazardlogfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                    </button>
                                                                @endif  
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>Nama Penulis:</strong>
                                                                {{ $hazardlogfeedback->author ?: 'Kosong' }}
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>Email:</strong>
                                                                {{ $hazardlogfeedback->email ?: 'Kosong' }}
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>Jenis:</strong>
                                                                {{ $hazardlogfeedback->conditionoffile2 ?: 'Kosong' }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <li class="list-group-item">
                                                <strong>Waktu:</strong>
                                                @php
                                                    $datetime = new DateTime($hazardlogfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                    $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                    $sendtime = $formattedTime;
                                                @endphp
                                                
                                                {!! $formattedTime ?? 'Kosong' !!}
                                                


                                            </li>
                                            <li class="list-group-item">
                                                <strong>Status Feedback:</strong>
                                                {{ ucfirst($hazardlogfeedback->conditionoffile ?: 'Kosong') }}
                                                @php
                                                    $statussetujulist[$unit] = $hazardlogfeedback->conditionoffile;
                                                @endphp
                                            </li>
                                            
                                            @if ($hazardlogfeedback->hazardLogFiles->isNotEmpty())
                                                <div class="card feedback-item">
                                                    <li class="list-group-item">
                                                        <div class="card-text-item">
                                                            <strong>Files:</strong>
                                                        </div>
                                                        @foreach ($hazardlogfeedback->hazardLogFiles as $file)
                                                            <div class="card-text mt-2">
                                                                <a href="{{ route('document.preview', ['linkfile' => str_replace('uploads/', '', $file->link)]) }}">{{ $file->filename }}</a>
                                                            </div>
                                                        @endforeach
                                                    </li>
                                                </div>
                                            @endif

                                            <li class="list-group-item">
                                                <strong>Komentar:</strong>
                                                @if(!empty($hazardlogfeedback->comment))
                                                    {{ $hazardlogfeedback->comment}} <span style="color: blue;">@</span><span style="color: blue;">{{ $hazardlogfeedback->pic }}</span>
                                                @else
                                                    Kosong
                                                @endif
                                            </li>
                                            
                                            <li class="list-group-item">
                                                <div class="row">
                                                    @if (str_contains($yourrule, "RAMS"))
                                                        <form id="deleteFeedbackForm{{ $hazardlogfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('hazard_logs.feedback.destroy', ['hazardLogId' => $hazardLog->id, 'feedbackId' => $hazardlogfeedback->id]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-warning mt-2" onclick="confirmDecision('deleteFeedbackForm{{ $hazardlogfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                        </form>
                                                    @endif
                                                    @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit))&&($hazardlogfeedback->conditionoffile=="draft"))
                                                        <form id="approveFeedbackForm{{ $hazardlogfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('hazard_logs.feedback.approve', ['hazardLogId' => $hazardLog->id, 'feedbackId' => $hazardlogfeedback->id]) }}">
                                                            @csrf
                                                            <button type="button" class="btn btn-success mt-2" onclick="confirmDecision('approveFeedbackForm{{ $hazardlogfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                        </form>
                                                    @endif
                                                    @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit))&&($hazardlogfeedback->conditionoffile=="draft"))
                                                        <form id="rejectFeedbackForm{{ $hazardlogfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('hazard_logs.feedback.reject', ['hazardLogId' => $hazardLog->id, 'feedbackId' => $hazardlogfeedback->id]) }}">
                                                            @csrf
                                                            <button type="button" class="btn btn-danger mt-2" onclick="confirmDecision('rejectFeedbackForm{{ $hazardlogfeedback->id }}{{$sendtime}}')">Tolak</button>
                                                        </form>
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                </div>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                                
                                @if(str_contains($yourrule, $unit) && $unitpicvalidation[$unit]!="Aktif")
                                    <p class="mt-3">
                                        <a href="{{ route('hazard_logs.feedback', ['id'=>$hazardLog->id,'level'=>$unit]) }}" class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback {{ $unit }}</a>
                                    </p>
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
    @if (in_array($yourrule, ["RAMS",'superuser']))   
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
                                
                                
                                @foreach ($hazardLog->hazardlogfeedback as $hazardlogfeedback)
                                    @if ($hazardlogfeedback->conditionoffile2=="combine"&& $hazardlogfeedback->conditionoffile=="approve")
                                        <div class="card mt-3">
                                            <div class="info-container mt-2" style="display: none;">
                                                <div class="card-body">
                                                    <h5 class="card-title"></h5>
                                                    <ul class="list-group list-group-flush">        
                                                        <li class="list-group-item">
                                                            @if($hazardlogfeedback->level == $yourrule)
                                                                <button class="btn" style="background-color: orange;">
                                                                    <strong>Status: Penerima dari:</strong>
                                                                    {{ $hazardlogfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                </button>
                                                            @elseif($hazardlogfeedback->level == "")
                                                                <button class="btn" style="background-color: yellow;">
                                                                    <strong>Upload Pribadi</strong>
                                                                </button>
                                                            @else
                                                                <button class="btn" style="background-color: red;">
                                                                    <strong>Status: Terkirim ke:</strong>
                                                                    {{ $hazardlogfeedback->level ?? "hanya upload & tidak dikirim" }}
                                                                </button>
                                                            @endif  
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Nama Penulis:</strong>
                                                            {{ $hazardlogfeedback->author ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Email:</strong>
                                                            {{ $hazardlogfeedback->email ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Jenis:</strong>
                                                            {{ $hazardlogfeedback->conditionoffile2 ?: 'Kosong' }}
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
                                                $datetime = new DateTime($hazardlogfeedback->timestamp, new DateTimeZone('Asia/Jakarta'));
                                                $formattedTime = $datetime->format('Y-m-d H:i:s');
                                                $sendtime = $formattedTime;
                                            @endphp
                                            
                                            {!! $formattedTime ?? 'Kosong' !!}
                                            

                                        </li>
                                        @if ($hazardlogfeedback->hazardLogFiles->isNotEmpty())
                                            <div class="card feedback-item">
                                                <li class="list-group-item">
                                                    <div class="card-text-item">
                                                        <strong>Files:</strong>
                                                    </div>
                                                    @foreach ($hazardlogfeedback->hazardLogFiles as $file)
                                                        <div class="card-text mt-2">
                                                            <a href="{{ route('document.preview', ['linkfile' => str_replace('uploads/', '', $file->link)]) }}">{{ $file->filename }}</a>
                                                        </div>
                                                    @endforeach
                                                </li>
                                            </div>
                                        @endif
                                        <li class="list-group-item">
                                            <strong>Status Feedback:</strong>
                                            @php
                                                $status = ucfirst($hazardlogfeedback->conditionoffile ?: 'Kosong');
                                                $statussetujulist[$unit] = $status;
                                                $badgeClass = '';

                                                // Mengatur kelas badge berdasarkan kondisi
                                                switch ($hazardlogfeedback->conditionoffile) {
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

                                        
                                        @if (isset($hazardlogfeedback->feedbackfiles) && $hazardlogfeedback->feedbackfiles->isNotEmpty())
                                            <div class="card feedback-item">
                                                <li class="list-group-item">
                                                    <div class="card-text-item">
                                                        <strong>Files:</strong>
                                                    </div>
                                                    @foreach ($hazardlogfeedback->feedbackfiles as $file)
                                                        @php
                                                            $newLinkFile = str_replace('uploads/', '', $file->link);
                                                        @endphp
                                                        <div class="card-text mt-2">
                                                        <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                                        </div>
                                                    @endforeach
                                                </li>
                                            </div>
                                        @endif

                                        <li class="list-group-item">
                                            <strong>Komentar:</strong>
                                            @if(!empty($hazardlogfeedback->comment))
                                                {{ $hazardlogfeedback->comment}} <span style="color: blue;">@</span><span style="color: blue;">{{ $hazardlogfeedback->author }}</span>
                                            @else
                                                Kosong
                                            @endif
                                        </li>
                                        
                                        <li class="list-group-item">
                                            <div class="row">
                                                @if (str_contains($yourrule, "RAMS"))
                                                    <form id="deleteFeedbackForm{{ $hazardlogfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('hazard_logs.feedback.destroy', ['hazardLogId' => $hazardLog->id, 'feedbackId' => $hazardlogfeedback->id]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-warning mt-2" onclick="confirmDecision('deleteFeedbackForm{{ $hazardlogfeedback->id }}{{$sendtime}}')">Hapus</button>
                                                    </form>
                                                @endif
                                                @if ((str_contains($yourrule, "Manager") && str_contains($yourrule, $unit))&&($hazardlogfeedback->conditionoffile=="draft"))
                                                    <form id="approveFeedbackForm{{ $hazardlogfeedback->id }}{{ $sendtime }}" method="POST" action="{{ route('ramsdocuments.feedback.approve', ['documentId' => $document->id, 'feedbackId' => $hazardlogfeedback->id]) }}">
                                                        @csrf
                                                        <button type="button" class="btn btn-success mt-2" onclick="confirmDecision('approveFeedbackForm{{ $hazardlogfeedback->id }}{{$sendtime}}')">Setujui</button>
                                                    </form>
                                                @endif
                                                
                                                <button class="btn btn-sm mt-2 btn-info toggle-info">Selengkapnya</button>
                                            </div>
                                        </li>
                                        

                                    @endif
                                    
                                @endforeach

                                @if($ramscombinevalidation!="Aktif" && $unitvalidation=="Aktif")
                                    @if(str_contains($yourrule, "RAMS"))
                                        <p class="mt-3">
                                            <a href="{{ route('hazard_logs.combine', ['id'=>$hazardLog->id,'level'=>'combine']) }}" class="btn btn-success btn-sm feedback-upload-btn">Upload Rangkuman Feedback RAMS</a>
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
@endsection

