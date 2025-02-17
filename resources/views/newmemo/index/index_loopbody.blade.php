@php
    $counterdokumen = 1; // Inisialisasi variabel counter
@endphp

@foreach($documents as $document)
    @php
        $key = key($documents);
        next($documents);
        $projectpics = json_decode($document->project_pic, true);
        $unitpicvalidation = $document->unitpicvalidation;

    @endphp

    <tr>
        <!-- <td>
                                <div class="icheck-primary">
                                    <input type="checkbox" value="{{ $document->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                    <label for="checkbox{{ $key }}"></label>
                                </div>
                            </td> -->
        <td>{{ $counterdokumen++ }}</td>
        <td>
            @php
                // Mengumpulkan semua timeline dengan status "documentopened"
                $timeline = collect($document->timelines)->where('infostatus', 'documentopened')->first();

                // Memeriksa apakah timeline dengan status "documentopened" ditemukan
                if ($timeline) {
                    // Mengubah string waktu ke objek Carbon dan menambahkan 5 hari
                    $deadline = \Carbon\Carbon::parse($timeline->entertime)->addDays(5);
                } else {
                    $deadline = null; // Atur null jika timeline tidak ditemukan
                }
            @endphp

            <span class="" style="padding: 3px;">
                {{$deadline->format('d/m/Y')}}
            </span>
        </td>

        <td>

            <span class="" style="padding: 3px;">
                {{ $document->documentnumber }}
            </span>
        </td>
        <td>
            <!-- @php
                                        $maxCharacters = 25; // Jumlah maksimum karakter sebelum teks dipotong
                                        $documentName = $document->documentname;
                                        $shortDocumentName = strlen($documentName) > $maxCharacters ? substr($documentName, 0, $maxCharacters) . '...' : $documentName;
                                    @endphp

                                    <span class="short-text" data-toggle="tooltip" title="{{ $documentName }}">{{ $shortDocumentName }}</span>
                                    @if (strlen($documentName) > $maxCharacters)
                                        <button class="btn btn-sm btn-info btn-toggle" data-toggle="collapse" data-target="#longText{{$document->id}}">Selengkapnya</button>
                                    @endif

                                    <div id="longText{{$document->id}}" class="collapse">
                                        {{ $documentName }}
                                    </div> -->
            {{$document->documentname}}
        </td>
        <!-- <td>
                                    @if (!empty($projectpics))
                                        @foreach($projectpics as $projectpic)
                                            <a class="dropdown-item" href="#">{{$unitsingkatan[$projectpic]}}</a>
                                        @endforeach
                                    @else
                                        <p>Tidak ada data unit</p>
                                    @endif
                                </td> -->
        <style>
            /* Gaya untuk tombol status dokumen */
            .document-status-button {
                /* Atur gaya umum untuk tombol */
                padding: 2px 5px;
                /* Padding tombol */
                border-radius: 3px;
                /* Sudut bulat tombol */
                font-size: 14px;
                /* Ukuran teks */
            }

            /* Gaya untuk tombol status "Terbuka" */
            .document-status-button-open {
                background-color: #dc3545;
                /* Warna latar merah */
                color: #fff;
                /* Warna teks putih */
            }

            /* Gaya untuk tombol status selain "Terbuka" */
            .document-status-button-closed {
                background-color: #28a745;
                /* Warna latar hijau */
                color: #fff;
                /* Warna teks putih */
            }
        </style>
        <td>
            <!-- Tombol untuk mengubah status dokumen -->
            @if($authuser->rule == $document->operator || $authuser->rule == "superuser" || $authuser->id == 9)
                <button type="button"
                    class="btn document-status-button document-status-button-{{ $document->documentstatus == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success' }}"
                    title="{{ $document->documentstatus }}" onclick="toggleDocumentStatus(this)"
                    data-document-status="{{ $document->documentstatus }}" data-document-id="{{ $document->id }}">
                    <i class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                    <span>{{ $document->documentstatus }}</span> <!-- Menampilkan status -->
                </button>
            @else
                <button type="button"
                    class="btn document-status-button document-status-button-{{ $document->documentstatus == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success' }}"
                    title="{{ $document->documentstatus }}" data-document-status="{{ $document->documentstatus }}"
                    data-document-id="{{ $document->id }}">
                    <i class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                    <span>{{ $document->documentstatus }}</span> <!-- Menampilkan status -->
                </button>
            @endif
        </td>
        <!-- Status dokumen -->
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f0f2f5;
                /* Warna latar belakang yang lembut */
            }

            .project-actionkus {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .action-group {
                display: flex;
                align-items: center;
                margin: 0 10px;
            }

            .arrow {
                margin: 0 5px;
                font-size: 24px;
                color: #00b0ff;
                /* Warna biru yang futuristik */
            }

            .container {
                display: flex;
                align-items: center;
            }

            .boxblue {
                margin-right: 5px;
                border: 1px solid #00b0ff;
                border-radius: 10px;
                padding: 10px;
                /* Tambahkan sedikit padding */
                background-color: #e1f5fe;
                /* Warna biru muda */
                box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
            }

            .box {
                margin-right: 5px;
                border: 1px solid #ccc;
                border-radius: 10px;
                padding: 10px;
                /* Tambahkan sedikit padding */
                background-color: #ffffff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            h2 {
                font-size: 20px;
                margin-bottom: 10px;
                color: #333;
            }

            ul {
                list-style-type: none;
                margin: 0;
                padding: 0;
            }

            li {
                margin-bottom: 10px;
            }

            .keterangan {
                margin-left: 5px;
                font-size: 16px;
                color: #555;
            }

            .indicator {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                margin-right: 5px;
            }

            .green {
                background-color: #4caf50;
                /* Warna hijau */
            }

            .red {
                background-color: #f44336;
                /* Warna merah */
            }

            .yellow {
                background-color: #ffeb3b;
                /* Warna kuning */
            }

            .blue {
                background-color: #2196f3;
                /* Warna biru */
            }

            .orange {
                background-color: #ff9800;
                /* Warna orange */
            }

            .black {
                background-color: #212121;
                /* Warna hitam */
            }
        </style>

        <td class="project-actionkus text-right">
            <div style="position: relative;">
                <div class="container">
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    @php

                        if ($document->posisi1 == "on") {
                            $classbox1 = "boxblue";
                        } else {
                            $classbox1 = "box";
                        }

                        if ($document->posisi2 == "on") {
                            $classbox2 = "boxblue";
                        } else {
                            $classbox2 = "box";
                        }

                        if ($document->posisi3 == "on") {
                            $classbox3 = "boxblue";
                        } else {
                            $classbox3 = "box";
                        }

                        if ($document->MTPRvalidation == "Aktif") {
                            $classbox1 = "boxblue";
                            $classbox2 = "boxblue";
                            $classbox3 = "boxblue";
                        }

                    @endphp
                    <a class="{{$classbox1}}" href="#">

                        @if($document->withMTPR == "Yes")
                            <div class="container">
                                <div class="indicator 
                                                                                {{ $document->MTPRsend == 'Aktif' ? 'green' : 'red' }}"
                                    title="{{ $document->MTPRsend == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                </div>
                                <span class="keterangan">MTPR</span>
                                @if(isset($waktudokumen["MTPR" . '_read']))
                                    @if($waktudokumen["MTPR" . '_read']['status'] == 'sudah dibaca')
                                        <span class="keterangan">{{$waktudokumen["MTPR" . '_read']['waktu'] ?? "23/04/2022"}}</span>
                                    @endif
                                @endif
                            </div>
                            <div class="container">
                                <span class="arrow">↓</span>
                            </div>
                        @endif


                        <div class="container">
                            <div class="indicator 
                                                    {{ $document->operatorshare == 'Aktif' ? 'green'
            : ($document->operatorshare == 'Ongoing' ? 'orange'
                : ($document->operatorshare == 'Belum dibaca' ? 'yellow'
                    : 'red')) }}" title="{{ $document->operatorshare == 'Aktif' ? 'Dokumen sudah dibagikan ke unit'
            : ($document->operatorshare == 'Ongoing' ? 'Dokumen sedang dibagikan ke unit'
                : ($document->operatorshare == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit'
                    : 'Dokumen belum dibagikan ke unit')) }}">
                            </div>
                            <span class="keterangan">{{$unitsingkatan[$document->operator]}}</span>
                        </div>
                    </a>

                    <span class="arrow">→</span>

                    <div class="{{$classbox2}}" style="height: 300px;">
                        <h2>Eng</h2>
                        <ul>
                            @foreach(['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'RAMS'] as $projectpic)
                                    <li>

                                        @if(isset($projectpics))
                                                    @if(in_array($projectpic, $projectpics))

                                                                <div class="container">
                                                                    <div class="indicator 
                                                                                                                                                                                                                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                    : 'red'))) }}" title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                    : $projectpic . ' belum dikerjakan'))) }}">
                                                                    </div>
                                                                </div>
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                    @endif
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                        @endif
                                        @if($projectpic != "RAMS")
                                            <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                        @else
                                            <span class="keterangan">{{$projectpic}}</span>
                                        @endif
                                    </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="{{$classbox2}}" style="height: 300px;">
                        <h2>Des</h2>
                        <ul>
                            @foreach(['Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik'] as $projectpic)
                                    <li>
                                        @if(isset($projectpics))
                                                    @if(in_array($projectpic, $projectpics))
                                                                <div class="indicator 
                                                                                                                                                                                                                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                    : 'red'))) }}" title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                    : $projectpic . ' belum dikerjakan'))) }}">
                                                                </div>
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>

                                                    @endif
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>

                                        @endif
                                        <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                    </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="{{$classbox2}}" style="height: 300px;">
                        <h2>TP</h2>
                        <ul>
                            @foreach(['Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'] as $projectpic)
                                    <li>
                                        @if(isset($projectpics))
                                                    @if(in_array($projectpic, $projectpics))
                                                                <div class="indicator 
                                                                                                                                                                                                                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                    : 'red'))) }}" title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                            : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                    : $projectpic . ' belum dikerjakan'))) }}">
                                                                </div>
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                    @endif
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                        @endif
                                        <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                    </li>
                            @endforeach
                        </ul>
                    </div>

                    <span class="arrow">→</span>

                    <a class="{{$classbox3}}" href="#">
                        @if($document->operator == "Product Engineering")
                                    <div class="container">
                                        <div class="indicator 
                                                                                                                                {{ $document->operatorcombinevalidation == 'Aktif' ? 'green'
                            : ($document->operatorcombinevalidation == 'Ongoing' ? 'orange'
                                : ($document->operatorcombinevalidation == 'Sudah dibaca' ? 'blue'
                                    : ($document->operatorcombinevalidation == 'Belum dibaca' ? 'yellow'
                                        : 'red'))) }}" title="{{ $document->operatorcombinevalidation == 'Aktif' ? 'PE sudah melakukan review dan penggabungan'
                            : ($document->operatorcombinevalidation == 'Ongoing' ? 'PE sedang melakukan review dan penggabungan'
                                : ($document->operatorcombinevalidation == 'Sudah dibaca' ? 'PE sudah dibaca'
                                    : ($document->operatorcombinevalidation == 'Belum dibaca' ? 'PE belum dibaca'
                                        : 'PE belum melakukan review dan penggabungan'))) }}">
                                        </div>
                                        <span class="keterangan">{{$unitsingkatan[$document->operator]}}</span>
                                    </div>
                                    <div class="container">
                                        <span class="arrow">↓</span>
                                    </div>


                                    @if($document->manageroperatorvalidation != "Tidak Terlibat")
                                            <div class="container">
                                                <div class="indicator 
                                                                                                                                                                                    {{ $document->manageroperatorvalidation == 'Aktif' ? 'green'
                                        : ($document->manageroperatorvalidation == 'Ongoing' ? 'orange'
                                            : ($document->manageroperatorvalidation == 'Sudah dibaca' ? 'blue'
                                                : ($document->manageroperatorvalidation == 'Belum dibaca' ? 'yellow'
                                                    : 'red'))) }}" title="{{ $document->manageroperatorvalidation == 'Aktif' ? 'Manager PE sudah melakukan review dan penggabungan'
                                        : ($document->manageroperatorvalidation == 'Ongoing' ? 'PE sedang melakukan review dan penggabungan'
                                            : ($document->manageroperatorvalidation == 'Sudah dibaca' ? 'PE sudah dibaca'
                                                : ($document->manageroperatorvalidation == 'Belum dibaca' ? 'PE belum dibaca'
                                                    : 'Manager PE belum melakukan review dan penggabungan'))) }}">
                                                </div>
                                                <span class="keterangan">Manager {{$unitsingkatan[$document->operator]}}</span>
                                            </div>
                                            <div class="container">
                                                <span class="arrow">↓</span>
                                            </div>
                                    @endif

                        @endif


                        <div class="container">
                            <div class="indicator 
                                                {{ $document->seniormanagervalidation == 'Aktif' ? 'green'
            : ($document->seniormanagervalidation == 'Ongoing' ? 'orange'
                : ($document->seniormanagervalidation == 'Sudah dibaca' ? 'blue'
                    : ($document->seniormanagervalidation == 'Belum dibaca' ? 'yellow'
                        : 'red'))) }}" title="{{ $document->seniormanagervalidation == 'Aktif' ? 'Senior manager sudah melakukan review'
            : ($document->seniormanagervalidation == 'Ongoing' ? 'Senior manager sedang melakukan review'
                : ($document->seniormanagervalidation == 'Sudah dibaca' ? 'Senior manager sudah membaca'
                    : ($document->seniormanagervalidation == 'Belum dibaca' ? 'Senior manager belum membaca'
                        : 'Senior manager belum melakukan review'))) }}">
                            </div>
                            @if($document->SMname == "Belum ditentukan")
                                <span class="keterangan">SM</span>
                            @else
                                <span class="keterangan">{{$unitsingkatan[$document->SMname]}}</span>
                            @endif

                        </div>
                        <div class="container">
                            <span class="arrow">↓</span>
                        </div>
                        <div class="container">
                            <div class="indicator 
                                                    {{ $document->MTPRvalidation == 'Aktif' ? 'green'
            : ($document->MTPRvalidation == 'Ongoing' ? 'orange'
                : ($document->MTPRvalidation == 'Sudah dibaca' ? 'blue'
                    : ($document->MTPRvalidation == 'Belum dibaca' ? 'yellow'
                        : 'red'))) }}" title="{{ $document->MTPRvalidation == 'Aktif' ? 'MTPR sudah menutup dokumen'
            : ($document->MTPRvalidation == 'Ongoing' ? 'MTPR sedang menutup dokumen'
                : ($document->MTPRvalidation == 'Sudah dibaca' ? 'MTPR sudah dibaca'
                    : ($document->MTPRvalidation == 'Belum dibaca' ? 'MTPR belum dibaca'
                        : 'MTPR belum menutup dokumen'))) }}">
                            </div>
                            <span class="keterangan">MTPR</span>
                        </div>
                    </a>

                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>

                    <!-- Bagian yang akan diletakkan di pojok kanan atas -->
                    <!-- Bagian yang akan diletakkan di pojok kanan atas -->
                    <div class="box"
                        style="position: absolute; top: 0; right: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                        @php
                            $timelines = collect($document->timelines); // Menggunakan collect untuk $timelines
                        @endphp
                        @php
                            $badgeClass = '';
                            $message = '';

                            // Pastikan $deadline terdefinisi
                            if (isset($deadline)) {
                                if ($document->documentstatus == 'Terbuka') {
                                    $now = \Carbon\Carbon::now();
                                    $differenceInDays = $now->diffInDays($deadline, false);

                                    if ($differenceInDays < 0) {
                                        $badgeClass = 'badge-danger';
                                        $message = "Telat " . abs($differenceInDays) . " hari";
                                    } else {
                                        $badgeClass = 'badge-success';
                                        $message = "Tersisa " . abs($differenceInDays) . " hari";
                                    }
                                } elseif ($document->documentstatus == 'Tertutup') {

                                    // Mencari timeline yang menandakan dokumen ditutup
                                    $documentclosed = $timelines->firstWhere('infostatus', 'documentclosed');

                                    if ($documentclosed) {
                                        $closed = \Carbon\Carbon::parse($documentclosed->createdat);
                                        $differenceInDays = $closed->diffInDays($deadline, false);

                                        if ($differenceInDays < 0) {
                                            $badgeClass = 'badge-danger';
                                            $message = "Telat " . abs($differenceInDays) . " hari";
                                        } else {
                                            $badgeClass = 'badge-success';
                                            $message = "Diupload " . abs($differenceInDays) . " hari sebelum deadline";
                                        }
                                    } else {
                                        $message = "Closed tanpa mengikuti alur";
                                    }
                                }
                            } else {
                                $message = "Deadline tidak tersedia";
                            }
                        @endphp


                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <span class="badge {{$badgeClass}}" style="padding: 3px;">
                                {{$message}}
                            </span>
                            <span class="badge bg-info" style="padding: 3px;">
                                Estimasi: 5 hari
                            </span>
                        </div>

                    </div>





                    <!-- Bagian yang akan diletakkan di pojok kiri bawah -->
                    <a class="box" href="#"
                        style="position: absolute; bottom: 0; left: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                        <div class="container">
                            <span class="badge bg-{{$document->positionPercentage == 100 ? 'success' : 'warning'}}"
                                style="padding: 5px;">
                                {{$document->positionPercentage}}% Completed
                            </span>
                        </div>
                    </a>

                    <!-- Bagian yang akan diletakkan di pojok kanan bawah -->
                    <!-- <a class="box" href="#" style="position: absolute; bottom: 0; right: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                                            <div class="container">
                                                <span class="badge bg-{{$document->positionPercentage == 100 ? 'success' : 'warning'}}" style="padding: 5px;">
                                                    {{$document->positionPercentage}}% Completed
                                                </span>
                                            </div>
                                        </a> -->

                </div>
            </div>
        </td>


        <td class="project-actions text-right">

            @if(auth()->user()->rule != "Logistik")
                <div class="col-md-12 text-right column-layout">
                    <a class="btn btn-primary btn-sm"
                        href="{{ route('new-memo.show', ['memoId' => $document->id, 'rule' => auth()->user()->rule]) }}"
                        style="width: 100px;">
                        <i class="fas fa-folder"></i> Detail
                    </a>
                </div>
                <div class="col-md-12 text-right column-layout">
                    <a class="btn bg-maroon btn-sm" href="{{ route('new-memo.timelinetracking', ['memoId' => $document->id]) }}"
                        style="width: 100px;">
                        <i class="fas fa-flag"></i> Milestone
                    </a>
                </div>



                @if(auth()->user()->rule == "superuser")
                    <div class="col-md-12 text-right column-layout">
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $document->id }}')"
                            style="width: 100px;">
                            <i class="fas fa-eraser"></i> Hapus
                        </button>
                    </div>
                @endif

            @endif
        </td>



    </tr>
@endforeach