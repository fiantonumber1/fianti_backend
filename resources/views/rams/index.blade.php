@extends('layouts.universal')

@php
    $authuser = auth()->user();
@endphp

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List RAMS Dokumen</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="card card-danger card-outline">
    <div class="card-header">
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <h3 class="card-title text-bold">Page Monitoring Dokumen RAMS <span class="badge badge-info ml-1"></span></h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="revisiSelect">Pilih Project :</label>
            <select class="form-control" id="revisiSelect" onchange="showRevisiContent(this.value)">
                @foreach ($revisiall as $keyan => $revisi)
                    <option value="{{ $keyan }}" @if($loop->first) selected @endif>{{ $keyan }}</option>
                @endforeach
            </select>
        </div>

        @foreach ($revisiall as $keyan => $revisi)
                                        <div class="revisi-section" id="revisi-{{ $keyan }}"
                    style="display: @if($loop->first) block @else none @endif;">
                    <div class="row mb-3">
                        @if(in_array(auth()->user()->rule, ["superuser"]))
                            <div class="col-md-3 col-sm-6 col-12">
                                <button type="button" class="btn btn-danger btn-sm btn-block"
                                    onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                            </div>
                        @endif
                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('ramsdocuments.create') }}" class="btn btn-primary btn-sm btn-block">Buat
                                Dokumen</a>
                        </div>
                        <table id="example2-{{ $keyan }}" class="table table-bordered table-hover">
                            @php
                                $documents = $revisi['documents'];
                            @endphp
                            <thead>
                                <tr>
                                    <th>
                                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                    </th>
                                    <th scope="col">No</th>
                                    <th scope="col">Deadline</th>
                                    <th scope="col">Nama Dokumen</th>
                                    <th scope="col">No Dokumen</th>
                                    <th scope="col">Posisi Dokumen</th>
                                    <th scope="col">Status Dokumen</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counterdokumen = 1;
                                @endphp
                                @foreach ($documents as $document)
                                                                                                                                                                                                                                                                                                        @php
                                                                                                                                                                                                                                                                                                            $unitsingkatan = $document->unitsingkatan;
                                                                                                                                                                                                                                                                                                            $projectpics = $document->projectpics;
                                                                                                                                                                                                                                                                                                            $unitpicvalidation = $document->unitpicvalidation;

                                                                                                                                                                                                                                                                                                            $smunitpicvalidation = $document->smunitpicvalidation;


                                                                                                                                                                                                                                                                                                        @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="icheck-primary">
                                                                <input type="checkbox" value="{{ $document->id }}" name="document_ids[]"
                                                                    id="checkbox{{ $document->id }}">
                                                                <label for="checkbox{{ $document->id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $counterdokumen++ }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($document->due_date)->format('d/m/Y') }}</td>
                                                        <td>{{ $document->documentname }}</td>
                                                        <td>{{ $document->documentnumber }}</td>
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
                                                                        if ($document->posisi4 == "on") {
                                                                            $classbox4 = "boxblue";
                                                                        } else {
                                                                            $classbox4 = "box";
                                                                        }
                                                                        if ($document->posisi5 == "on") {
                                                                            $classbox5 = "boxblue";
                                                                        } else {
                                                                            $classbox5 = "box";
                                                                        }

                                                                    @endphp
                                                                    <a class="{{$classbox1}}" href="#">
                                                                        <div class="container">
                                                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                    {{ $document->ramsvalidation == 'Aktif' ? 'green' : 'red' }}"
                                                                                title="{{ $document->ramsvalidation == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                                                            </div>
                                                                            <span class="keterangan">RAMS</span>
                                                                        </div>
                                                                    </a>
                                                                    <span class="arrow">→</span>

                                                                    <div class="{{$classbox2}}" style="height: 300px;">
                                                                        <h2>Eng</h2>
                                                                        <ul>
                                                                            @foreach(['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering'] as $projectpic)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li>
                                                                                                            @if(isset($projectpics))
                                                                                                                                            @if(in_array($projectpic, $projectpics))
                                                                                                                                                                            <div class="container">
                                                                                                                                                                                <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green'
                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange'
                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow'
                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                                                                                                            : 'red'))) }}"
                                                                                                                                                                                    title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                                                                                                            : $projectpic . ' belum dikerjakan'))) }}">
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                            @else
                                                                                                                                                <div class="indicator black"
                                                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                                                </div>
                                                                                                                                            @endif
                                                                                                            @else
                                                                                                                <div class="indicator black"
                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                </div>
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
                                                                                                                                                            : 'red'))) }}"
                                                                                                                                                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                                                                                                            : $projectpic . ' belum dikerjakan'))) }}">
                                                                                                                                                                            </div>
                                                                                                                                            @else
                                                                                                                                                <div class="indicator black"
                                                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                                                </div>

                                                                                                                                            @endif
                                                                                                            @else
                                                                                                                <div class="indicator black"
                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                </div>

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
                                                                                                                                                            : 'red'))) }}"
                                                                                                                                                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve'
                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                                                                                                            : $projectpic . ' belum dikerjakan'))) }}">
                                                                                                                                                                            </div>
                                                                                                                                            @else
                                                                                                                                                <div class="indicator black"
                                                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                                                </div>
                                                                                                                                            @endif
                                                                                                            @else
                                                                                                                <div class="indicator black"
                                                                                                                    title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                </div>
                                                                                                            @endif
                                                                                                            <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                                                                                        </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>

                                                                    <span class="arrow">→</span>

                                                                    <a class="{{$classbox3}}" href="#">
                                                                        <div class="container">
                                                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                    {{ $document->ramscombinevalidation == 'Aktif' ? 'green' : 'red' }}"
                                                                                title="{{ $document->ramscombinevalidation == 'Aktif' ? 'Dokumen sudah difinalisasi' : 'Dokumen belum difinalisasi' }}">
                                                                            </div>
                                                                            <span class="keterangan">RAMS</span>
                                                                        </div>
                                                                    </a>

                                                                    <span class="arrow">→</span>

                                                                    <div class="{{$classbox4}}" style="height: 300px;">
                                                                        <h2>SM</h2>
                                                                        <ul>
                                                                            @php
                                                                                $allunitundersm = array_keys($smunitpicvalidation);
                                                                            @endphp
                                                                            @foreach(['Senior Manager Engineering', 'Senior Manager Teknologi Produksi', 'Senior Manager Desain'] as $projectpic)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li>
                                                                                                                        @php
                                                                                                                            $unitsminfo = $smunitpicvalidation[$projectpic] ?? 'Nonaktif';

                                                                                                                        @endphp
                                                                                                                        @if(isset($allunitundersm))
                                                                                                                                                        @if(in_array($projectpic, $allunitundersm))
                                                                                                                                                                                        <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $unitsminfo == 'Aktif' ? 'green'
                                                                                                                                                            : ($unitsminfo == 'Ongoing' ? 'orange'
                                                                                                                                                                : ($unitsminfo == 'Belum dibaca' ? 'yellow'
                                                                                                                                                                    : ($unitsminfo == 'Sudah dibaca' ? 'blue'
                                                                                                                                                                        : 'red'))) }}"
                                                                                                                                                                                            title="{{ $unitsminfo == 'Aktif' ? $projectpic . ' sudah approve'
                                                                                                                                                            : ($unitsminfo == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                                                                : ($unitsminfo == 'Belum dibaca' ? $projectpic . ' belum dibaca'
                                                                                                                                                                    : ($unitsminfo == 'Sudah dibaca' ? $projectpic . ' sudah dibaca'
                                                                                                                                                                        : $projectpic . ' belum dikerjakan'))) }}">
                                                                                                                                                                                        </div>
                                                                                                                                                        @else
                                                                                                                                                            <div class="indicator black"
                                                                                                                                                                title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                                                            </div>
                                                                                                                                                        @endif
                                                                                                                        @else
                                                                                                                            <div class="indicator black"
                                                                                                                                title="{{ $projectpic . ' tidak terlibat' }}">
                                                                                                                            </div>
                                                                                                                        @endif
                                                                                                                        <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                                                                                                    </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>

                                                                    <span class="arrow">→</span>

                                                                    <a class="{{$classbox5}}" href="#">
                                                                        <div class="container">
                                                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                    {{ $document->ramsfinalisasivalidation == 'Aktif' ? 'green' : 'red' }}"
                                                                                title="{{ $document->ramsfinalisasivalidation == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                                                            </div>
                                                                            <span class="keterangan">RAMS</span>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
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
                                                            <button type="button"
                                                                class="btn document-status-button document-status-button-{{$document->documentopenedclosed == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentopenedclosed == 'Terbuka' ? 'btn-danger' : 'btn-success' }}"
                                                                title="{{ $document->documentopenedclosed }}" onclick="toggleDocumentStatus(this)"
                                                                data-document-status="{{ $document->documentopenedclosed }}"
                                                                data-document-id="{{ $document->id }}">
                                                                <i
                                                                    class="{{ $document->documentopenedclosed == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                                                                <span>{{ $document->documentopenedclosed }}</span> <!-- Menampilkan status -->
                                                            </button>
                                                        </td>
                                                        <td class="project-actions text-right">



                                                            <div class="col-md-12 text-right column-layout">

                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('ramsdocuments.show', $document) }}" style="width: 100px;">
                                                                    <i class="fas fa-folder"></i> Detail
                                                                </a>
                                                            </div>

                                                            <!-- @if(auth()->user()->rule == "superuser" || auth()->user()->rule == "RAMS")
                                                                                                                        <div class="col-md-12 text-right column-layout">
                                                                                                                            <form action="{{ route('ramsdocuments.destroy', $document) }}" method="POST"
                                                                                                                                style="display:inline;">
                                                                                                                                @csrf
                                                                                                                                @method('DELETE')
                                                                                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                                                                                    style="width: 100px;">Delete</button>
                                                                                                                            </form>
                                                                                                                        </div>
                                                                                                                    @endif -->
                                                        </td>

                                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>

        function showRevisiContent(keyan) {
            document.querySelectorAll('.revisi-section').forEach(section => {
                section.style.display = 'none';
            });

            const selectedSection = document.getElementById('revisi-' + keyan);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        }
    </script>
@endpush