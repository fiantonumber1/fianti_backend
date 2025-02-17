@php


    $userinformations = json_decode($document->userinformations);
    $timeline = json_decode($document->timeline, true);
    $projectpicscount = [];
    $projectpics = json_decode($document->project_pic);

    function decodeUserInformation($userInfo)
    {
        try {
            $decodedInfo = json_decode($userInfo, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
            }
            return $decodedInfo;
        } catch (\Exception $e) {
            \Log::error('Error decoding JSON: ' . $e->getMessage());
            return [];
        }
    }

    $data = [
        'Dokumendibuka' => null,
        'kotakTTD' => null,
        'Dokumen dicombine' => null,
        'Dokumen dikirim ke MTPR' => null,
        'Dokumen ditutup' => null,
    ];

    foreach ($userinformations as $userInfo) {
        $decodedInfo = decodeUserInformation($userInfo);
        if (!$decodedInfo)
            continue;
        $picname = $decodedInfo['pic'] ?? null;
        $levelname = $decodedInfo['level'] ?? null;
        $waktu = json_decode($decodedInfo['userinformations'])->time;
        $datetime = isset($waktu) ? new DateTime($waktu, new DateTimeZone('UTC')) : null;

        if ($datetime) {
            $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $formattedDateTime = $datetime->format('Y-m-d H:i:s');
            if ($projectpics) {
                for ($i = 0; $i < count($projectpics); $i++) {
                    if ($projectpics[$i]) {
                        if ($picname == $projectpics[$i]) {
                            $data[$picname] = $formattedDateTime;
                            $data['waktuterakhirunit'] = $formattedDateTime;
                        }
                    }
                }
            }
            if ($picname == "MTPR" && $levelname == "pembukadokumen") {
                $data['Dokumendibuka'] = $formattedDateTime;
            } elseif ($picname == "Product Engineering" && $levelname == "signature") {
                $data['kotakTTD'] = $formattedDateTime;
            } elseif ($picname == "Product Engineering" && in_array($levelname, ["Manager Product Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                $data['Dokumen dicombine'] = $formattedDateTime;
            } elseif ($levelname == "MTPR") {
                $data['Dokumen dikirim ke MTPR'] = $formattedDateTime;
            } elseif ($levelname == "selesai") {
                $data['Dokumen ditutup'] = "$formattedDateTime";
            }
        }
    }

    if (isset(json_decode($document->timeline)->documentshared)) {
        $waktu = json_decode($document->timeline)->documentshared;
        $datetime = isset($waktu) ? new DateTime($waktu, new DateTimeZone('UTC')) : null;
    }

    if ($datetime) {
        $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        $formattedDateTime = $datetime->format('Y-m-d H:i:s');
        $data['sharetounit'] = $formattedDateTime;
    }

    $waktusekarang = null;
    if (isset($waktusekarang)) {
        $datetime = new DateTime($waktusekarang, new DateTimeZone('UTC'));
    } else {
        $datetime = new DateTime('now', new DateTimeZone('UTC'));
    }

    $datetime->setTimezone(new DateTimeZone('Asia/Jakarta'));
    $formattedDateTime = $datetime->format('Y-m-d H:i:s');
    $data['waktusekarang'] = $formattedDateTime;

    $dokumenDibuka = null;
    $dokumenDitutup = null;
    $waktuDibubuhkan = null;
    $waktuDishare = null;
    $waktuPalingAkhir = null;
    $waktudicombine = null;
    $dikirimMTRP = null;
    $finalisasi = null;

    if (isset($data['waktusekarang'])) {
        $waktusekarang = new DateTime($data['waktusekarang']);
    }

    if (isset($data['Dokumendibuka'])) {
        $dokumenDibuka = new DateTime($data['Dokumendibuka']);
    }
    if (isset($data['Dokumen ditutup'])) {
        $dokumenDitutup = new DateTime($data['Dokumen ditutup']);
    }

    if (isset($data['kotakTTD'])) {
        $waktuDibubuhkan = new DateTime($data['kotakTTD']);
    }

    if (isset($data['sharetounit'])) {
        $waktuDishare = new DateTime($data['sharetounit']);
    }


    if (isset($data['Dokumen dicombine'])) {
        $waktudicombine = new DateTime($data['Dokumen dicombine']);
    }

    if (isset($data['Dokumen dikirim ke MTPR'])) {
        $dikirimMTRP = new DateTime($data['Dokumen dikirim ke MTPR']);
    }

    if (isset($data['Dokumen dikirim ke MTPR']) && isset($data['Dokumen ditutup'])) {
        $finalisasi = new DateTime($data['Dokumen ditutup']);
    }

    $selisihDibukaDitutup = isset($dokumenDibuka) && isset($dokumenDitutup) ? $dokumenDibuka->diff($dokumenDitutup)->format('%R%a hari %H jam %I menit %S detik') : null;
    $selisihDibukaSekarang = null;
    if (isset($dokumenDibuka) && isset($waktusekarang)) {
        $tugasdivisi = TugasDivisi::where('dokumen_id', $document->id)
            ->where(function ($query) {
                $query->where('notificationcategory', "allert1")
                    ->orWhere('notificationcategory', "allert2");
            })
            ->first();
        $selisih = $dokumenDibuka->diff($waktusekarang);
        $selisihHari = $selisih->days;
        if ($selisihHari > 3) {
            $jenisallert = "allert1";
            if ($selisihHari > 5) {
                $jenisallert = "allert2";
            }
            if (isset($tugasdivisi->nama_divisi)) {
            } else {
                $file = TugasDivisi::create([
                    'nama_file' => $document->documentname,
                    'nama_project' => $document->project_type,
                    'iddocument' => $document->id,
                    'nama_divisi' => "Senior Manager Engineering",
                    'status' => "Terima",
                    'alasan' => "",
                    'sudahdibaca' => "belum dibaca",
                    'notificationcategory' => $jenisallert,
                ]);
            }
        }
        $selisihDibukaSekarang = $selisih->format('%R%a hari %H jam %I menit %S detik');
    }

    $selisihDibukaDibubuhkan = isset($dokumenDibuka) && isset($waktuDibubuhkan) ? $dokumenDibuka->diff($waktuDibubuhkan)->format('%R%a hari %H jam %I menit %S detik') : null;
    $selisihDibubuhkanDishare = isset($waktuDibubuhkan) && isset($waktuDishare) ? $waktuDibubuhkan->diff($waktuDishare)->format('%R%a hari %H jam %I menit %S detik') : null;
    $selisihProjectPicDibubuhkan = [];
    if ($projectpics) {
        foreach ($projectpics as $projectpic) {
            if (isset($data[$projectpic])) {
                $waktuProjectPic = new DateTime($data[$projectpic]);
                if (isset($waktuDibubuhkan)) {
                    $selisihProjectPicDibubuhkan[$projectpic] = $waktuDishare->diff($waktuProjectPic)->format('%R%a hari %H jam %I menit %S detik');
                }
                $selisihUnitCombine = $waktuDishare->diff($waktuProjectPic)->format('%R%a hari %H jam %I menit %S detik');
            }
        }
    }
    $selisihCombineSendMTPR = isset($waktudicombine) && isset($dikirimMTRP) ? $waktudicombine->diff($dikirimMTRP)->format('%R%a hari %H jam %I menit %S detik') : null;
    $selisihKirimSelesai = isset($dikirimMTRP) && isset($finalisasi) ? $dikirimMTRP->diff($finalisasi)->format('%R%a hari %H jam %I menit %S detik') : null;


@endphp



@extends('layouts.split3')

@section('container1')
@endsection



@section('container2')
<div class="row">
    <section>
        <div class="card bg-light d-flex flex-fill">
            <div class="timeline">
                <!-- Dokumen dibuka -->
                <div class="time-label">
                    <span class="bg-red">Start</span>
                </div>

                @if(isset($data['Dokumendibuka']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['Dokumendibuka'] }}</span>
                            <h3 class="timeline-header"><a href="#">MTPR</a> membuka dokumen</h3>
                            <div class="timeline-body">Waktu dibuka: {{ $data['Dokumendibuka'] }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['Dokumendibuka']) && isset($data['kotakTTD']))
                    <div>
                        <i class="fas fa-clock"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">{{ $selisihDibukaDibubuhkan }}</div>
                        </div>
                    </div>
                @endif
                <!-- Dokumen dibubuhkan -->
                @if(isset($data['kotakTTD']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['kotakTTD'] }}</span>
                            <h3 class="timeline-header"><a href="#">Product Engineering</a> menambahkan kolom TTD</h3>
                            <div class="timeline-body">Waktu ditambahkan: {{ $data['kotakTTD'] }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['kotakTTD']) && isset($timeline['documentshared']))
                    <div>
                        <i class="fas fa-clock"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">{{ $selisihDibubuhkanDishare }}</div>
                        </div>
                    </div>
                @endif
                @if (isset($timeline['documentshared']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['sharetounit'] }}</span>
                            <h3 class="timeline-header"><a href="#">Product Engineering</a> membagikan memo</h3>
                            <div class="timeline-body">Waktu dibagikan: {{ $data['sharetounit'] }}</div>
                        </div>
                    </div>
                @endif


                <!-- Loop through projectpics -->
                @if($projectpics)
                    @foreach ($projectpics as $projectpic)
                        @if(isset($data[$projectpic]))
                            <div>
                                <i class="fas fa-envelope bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $data[$projectpic] }}</span>
                                    <div class="timeline-header">
                                        <i class="fas fa-clock"></i>
                                        {{ $selisihProjectPicDibubuhkan[$projectpic] }}
                                    </div>
                                    <h3 class="timeline-header"><a href="#">{{ $projectpic }}</a> Melakukan Approved</h3>
                                    <div class="timeline-body">Waktu Approved: {{ $data[$projectpic] }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
                @if(isset($data['kotakTTD']) && isset($timeline['Dokumen dicombine']))
                    <div>
                        <i class="fas fa-clock"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">{{ $selisihUnitCombine }}</div>
                        </div>
                    </div>
                @endif

                @if(isset($data['Dokumen dicombine']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['Dokumen dicombine'] }}</span>
                            <h3 class="timeline-header"><a href="#">Product Engineering</a>mengirimkan memo ke SM</h3>
                            <div class="timeline-body">Waktu dikirimkan: {{ $data['Dokumen dicombine'] }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['Dokumen dicombine']) && isset($data['Dokumen dikirim ke MTPR']))
                    <div>
                        <i class="fas fa-clock"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">{{ $selisihCombineSendMTPR }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['Dokumen dikirim ke MTPR']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['Dokumen dikirim ke MTPR'] }}</span>
                            <h3 class="timeline-header"><a href="#">Senior Manager</a>mengirimkan memo ke MTPR</h3>
                            <div class="timeline-body">Waktu dikirimkan: {{ $data['Dokumen dikirim ke MTPR'] }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['Dokumen dikirim ke MTPR']) && isset($data['Dokumen ditutup']))
                    <div>
                        <i class="fas fa-clock"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">{{ $selisihKirimSelesai }}</div>
                        </div>
                    </div>
                @endif
                @if(isset($data['Dokumen ditutup']))
                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $data['Dokumen ditutup'] }}</span>
                            <h3 class="timeline-header"><a href="#">MTPR</a> menutup dokumen</h3>
                            <div class="timeline-body">Waktu ditutup: {{ $data['Dokumen ditutup'] }}</div>
                        </div>
                    </div>
                @endif
                <div>
                    <i class="fas fa-clock bg-gray"></i>
                </div>
    </section>
    <section>
        <div class="card">
            <!-- Waktu Sekarang -->
            @if(isset($data['waktusekarang']))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Waktu Sekarang</h5>
                    </div>
                    <div class="card-text">
                        {{ $data['waktusekarang'] }}
                    </div>
                </div>
            @endif

            <!-- Perlu Allert? -->
            @if(isset($allertsm))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Perlukah Allert? {{ ucfirst($allertsm) }} </h5>
                        <a class="btn btn-secondary btn-sm" <a
                            href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi=Senior Manager Engineering&notificationcategory=Allert">
                            <i class="fas fa-warning"></i> Kirim Allert ke SME
                        </a>
                    </div>
                </div>
            @endif

            <!-- Dokumen dibuka - Waktu Sekarang -->
            @if(isset($selisihDibukaSekarang))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Dokumen dibuka - Waktu Sekarang</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihDibukaSekarang }}
                    </div>
                </div>
            @endif



            <!-- MTPR - Pe Uploadsignature -->
            @if(isset($selisihDibukaDibubuhkan))
                <div class="card-body">
                    <div class="card-title">
                        <h5>MTPR - Pe Uploadsignature</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihDibukaDibubuhkan }}
                    </div>
                </div>
            @endif

            <!-- Pe Uploadsignature - Pe share -->
            @if(isset($selisihDibubuhkanDishare))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Pe Uploadsignature - Pe share</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihDibubuhkanDishare }}
                    </div>
                </div>
            @endif

            <!-- Selisih waktu antara unit terakhir kirim dengan Pe melakukan kombinasi dan mengirim ke SM -->
            @if(isset($projectpics))
                @foreach ($projectpics as $projectpic)
                    @if(isset($data[$projectpic]) && isset($selisihProjectPicDibubuhkan[$projectpic]))
                        <div class="card-body">
                            <div class="card-title">
                                <h5>PE Membagikan - {{$projectpic}}</h5>
                            </div>
                            <div class="card-text">
                                {{ $selisihProjectPicDibubuhkan[$projectpic] }}
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            <!-- Unit terakhir kirim - Pe kombinasi dan kirim ke SM -->
            @if(isset($selisihUnitCombine))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Unit terakhir kirim - Pe kombinasi dan kirim ke SM</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihUnitCombine }}
                    </div>
                </div>
            @endif

            <!-- Pe kombinasi ke SM - SM kirim ke MTPR -->
            @if(isset($selisihCombineSendMTPR))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Pe kombinasi ke SM - SM kirim ke MTPR</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihCombineSendMTPR }}
                    </div>
                </div>
            @endif

            <!-- SM kirim ke MTPR - MTPR penutupan dokumen -->
            @if(isset($selisihKirimSelesai))
                <div class="card-body">
                    <div class="card-title">
                        <h5>SM kirim ke MTPR - MTPR penutupan dokumen</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihKirimSelesai }}
                    </div>
                </div>
            @endif
            <!-- Dokumen dibuka - Dokumen ditutup -->
            @if(isset($selisihDibukaDitutup))
                <div class="card-body">
                    <div class="card-title">
                        <h5>Dokumen dibuka - Dokumen ditutup</h5>
                    </div>
                    <div class="card-text">
                        {{ $selisihDibukaDitutup }}
                    </div>
                </div>
            @endif
        </div>

    </section>
</div>
@endsection