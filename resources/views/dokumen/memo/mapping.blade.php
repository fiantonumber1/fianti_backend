@extends('layouts.universal')



@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="/">Memo</a></li>
            <li class="breadcrumb-item active text-bold">Tracking Memo</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
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
            <h3 class="card-title text-bold">Page monitoring memo <span class="badge badge-info ml-1"></span></h3>
        </div>  
        <div class="card-body">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                @foreach ($revisiall as $keyan => $revisi)
                    <li class="nav-item">
                        <a class="nav-link @if($loop->first) active @endif" id="custom-tabs-one-{{ $keyan }}-tab" data-toggle="pill" href="#custom-tabs-one-{{ $keyan }}" role="tab" aria-controls="custom-tabs-one-{{ $keyan }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $keyan }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="custom-tabs-one-tabContent">
                @foreach ($revisiall as $keyan => $revisi)
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $keyan }}" role="tabpanel" aria-labelledby="custom-tabs-one-{{ $keyan }}-tab">
                        <div class="row">
                            @if(auth()->user()->rule=="superuser")
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Tombol untuk menghapus yang dipilih -->
                                    <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                </div>

                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Tambahkan tombol upload di sini -->
                                    <a href="" class="btn btn-primary btn-sm btn-block mb-3">Upload Dokumen</a>
                                </div>
                            @endif
                            <div class="col-md-3 col-sm-6 col-12">
                                <!-- Tombol untuk menghapus yang dipilih -->
                                <button type="button" class="btn btn-success btn-sm btn-block" onclick="handleReportMultipleItems()">Report yang dipilih</button>
                            </div>
                        </div>

                        <!-- Button trigger modal -->
                        <table id="example2-{{ $keyan }}" class="table table-bordered table-hover table-striped">
                            @php
                                if($keyan !== 'All'){
                                    $documents = $revisi['documents'];
                                }
                            @endphp
                            <thead>
                                <tr>
                                    <th>
                                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                    </th>
                                    <th>No</th>
                                    <th>Deadline</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Nama Dokumen</th>
                                    <!-- <th>Anggota Project</th> -->
                                    <th>Progress</th>
                                    <th>Posisi Memo</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @php
                                    $counterdokumen = 1; // Inisialisasi variabel counter
                                @endphp
                                
                                
                                
                                @foreach($documents as $document)
                                @php
                                    $key = key($documents);
                                    next($documents);
                                @endphp
                                @php
                                    $sumberinformasi= json_decode(json_decode($listdatadocumentencode,true)[$document->id],true);
                                    $projectpics=$sumberinformasi['projectpics'];
                                    $unitpicvalidation=$sumberinformasi['unitpicvalidation'];
                                    $parameterlain=$sumberinformasi['parameterlain'];
                                    $posisi=$parameterlain['posisi'];
                                    $posisi1=$posisi['posisi1'];
                                    $posisi2=$posisi['posisi2'];
                                    $posisi3=$posisi['posisi3'];
                                    $MTPRvalidation=$sumberinformasi['MTPRvalidation'];
                                    $MTPRsend=$sumberinformasi['MTPRsend'];
                                    $PEshare=$sumberinformasi['PEshare'];
                                    $MPEvalidation=$sumberinformasi['MPEvalidation'];
                                    $SMname=$sumberinformasi['SMname'];
                                    $PEmanagervalidation=$sumberinformasi['PEmanagervalidation'];
                                    $seniormanagervalidation=$sumberinformasi['seniormanagervalidation'];
                                    $selfunitvalidation=$sumberinformasi['selfunitvalidation'];
                                    $unitvalidation=$sumberinformasi['unitvalidation'];
                                    $positionPercentage=$sumberinformasi['positionPercentage'];
                                    $datadikirimencoded=$sumberinformasi['datadikirimencoded'];
                                    $informasidokumenencoded=$sumberinformasi['informasidokumenencoded'];
                                    $timeline = $sumberinformasi['timeline'];
                                    $document=json_decode($sumberinformasi['document']);
                                    
                                    
                                @endphp
                                <tr>
                                    <td>
                                        <div class="icheck-primary">
                                            <!-- Tambahkan name dan ID unik -->
                                            <input type="checkbox" value="{{ $document->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                            <label for="checkbox{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $counterdokumen++ }}</td>
                                    <td>
                                        @php
                                            $date = \Carbon\Carbon::parse($timeline["documentopened"]);
                                            $date = $date->addDays(3);
                                        @endphp
                                        <span class="" style="padding: 3px;">
                                            {{$date->format('d/m/Y')}}
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
                                            padding: 2px 5px; /* Padding tombol */
                                            border-radius: 3px; /* Sudut bulat tombol */
                                            font-size: 14px; /* Ukuran teks */
                                        }

                                        /* Gaya untuk tombol status "Terbuka" */
                                        .document-status-button-open {
                                            background-color: #dc3545; /* Warna latar merah */
                                            color: #fff; /* Warna teks putih */
                                        }

                                        /* Gaya untuk tombol status selain "Terbuka" */
                                        .document-status-button-closed {
                                            background-color: #28a745; /* Warna latar hijau */
                                            color: #fff; /* Warna teks putih */
                                        }
                                    </style>
                                    <td>
                                        <!-- Tombol untuk mengubah status dokumen -->
                                        <button type="button" class="btn document-status-button document-status-button-{{ $document->documentstatus == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success' }}" title="{{ $document->documentstatus }}" onclick="toggleDocumentStatus(this)" data-document-status="{{ $document->documentstatus }}" data-document-id="{{ $document->id }}">
                                            <i class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                                            <span>{{ $document->documentstatus }}</span> <!-- Menampilkan status -->
                                        </button>
                                    </td>
                                    <!-- Status dokumen -->
                                    <style>
                                        body {
                                            font-family: Arial, sans-serif;
                                            margin: 0;
                                            padding: 0;
                                            background-color: #f0f2f5; /* Warna latar belakang yang lembut */
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
                                            color: #00b0ff; /* Warna biru yang futuristik */
                                        }
                                        .container {
                                            display: flex;
                                            align-items: center;
                                        }
                                        .boxblue {
                                            margin-right: 5px;
                                            border: 1px solid #00b0ff;
                                            border-radius: 10px;
                                            padding: 10px; /* Tambahkan sedikit padding */
                                            background-color: #e1f5fe; /* Warna biru muda */
                                            box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
                                        }
                                        .box {
                                            margin-right: 5px;
                                            border: 1px solid #ccc;
                                            border-radius: 10px;
                                            padding: 10px; /* Tambahkan sedikit padding */
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
                                            background-color: #4caf50; /* Warna hijau */
                                        }
                                        .red {
                                            background-color: #f44336; /* Warna merah */
                                        }
                                        .yellow {
                                            background-color: #ffeb3b; /* Warna kuning */
                                        }
                                        .blue {
                                            background-color: #2196f3; /* Warna biru */
                                        }
                                        .orange {
                                            background-color: #ff9800; /* Warna orange */
                                        }
                                        .black {
                                            background-color: #212121; /* Warna hitam */
                                        }
                                    </style>

                                    <td class="project-actionkus text-right">
                                        <div style="position: relative;">
                                        <div class="container">
                                            <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                                            <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                                            @php
                                            
                                                if($posisi1=="on"){
                                                    $classbox1="boxblue";
                                                }else{
                                                    $classbox1="box";
                                                }

                                                if($posisi2=="on"){
                                                    $classbox2="boxblue";
                                                }else{
                                                    $classbox2="box";
                                                }

                                                if($posisi3=="on"){
                                                    $classbox3="boxblue";
                                                }else{
                                                    $classbox3="box";
                                                }

                                                if($MTPRvalidation=="Aktif"){
                                                    $classbox1="boxblue";
                                                    $classbox2="boxblue";
                                                    $classbox3="boxblue";
                                                }
                                                
                                            @endphp
                                            <a class="{{$classbox1}}" href="#">
                                                <div class="container">
                                                    <div class="indicator 
                                                        {{ $MTPRsend == 'Aktif' ? 'green' : 'red' }}" 
                                                        title="{{ $MTPRsend == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                                    </div>
                                                    <span class="keterangan">MTPR</span>
                                                    @if(isset($waktudokumen["MTPR".'_read']))
                                                        @if($waktudokumen["MTPR".'_read']['status']=='sudah dibaca')
                                                            <span class="keterangan">{{$waktudokumen["MTPR".'_read']['waktu']??"23/04/2022"}}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="container">
                                                    <span class="arrow">↓</span>
                                                </div>
                                                <div class="container">
                                                    <div class="indicator 
                                                        {{ $PEshare == 'Aktif' ? 'green' 
                                                        : ($PEshare == 'Ongoing' ? 'orange' 
                                                        : ($PEshare == 'Belum dibaca' ? 'yellow' 
                                                        : 'red')) }}" 
                                                        title="{{ $PEshare == 'Aktif' ? 'Dokumen sudah dibagikan ke unit' 
                                                        : ($PEshare == 'Ongoing' ? 'Dokumen sedang dibagikan ke unit' 
                                                        : ($PEshare == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' 
                                                        : 'Dokumen belum dibagikan ke unit')) }}">
                                                    </div>
                                                    <span class="keterangan">PE</span>
                                                </div>
                                            </a>
                                            
                                            <span class="arrow">→</span>
                                            
                                            <div class="{{$classbox2}}" style="height: 300px;">
                                                <h2>Eng</h2>
                                                <ul>
                                                    @foreach(['Product Engineering','Mechanical Engineering System','Electrical Engineering System','Quality Engineering','RAMS'] as $projectpic)
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
                                                                <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                            @endif
                                                        @else
                                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        @if($projectpic!="RAMS")
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
                                                @foreach(['Desain Mekanik & Interior','Desain Bogie & Wagon','Desain Carbody','Desain Elektrik'] as $projectpic)
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
                                                @foreach(['Preparation & Support','Welding Technology','Shop Drawing','Teknologi Proses'] as $projectpic)
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
                                                <div class="container">
                                                    <div class="indicator 
                                                        {{ $PEmanagervalidation == 'Aktif' ? 'green' 
                                                        : ($PEmanagervalidation == 'Ongoing' ? 'orange' 
                                                        : ($PEmanagervalidation == 'Sudah dibaca' ? 'blue' 
                                                        : ($PEmanagervalidation == 'Belum dibaca' ? 'yellow' 
                                                        : 'red'))) }}" 
                                                        title="{{ $PEmanagervalidation == 'Aktif' ? 'PE sudah melakukan review dan penggabungan' 
                                                        : ($PEmanagervalidation == 'Ongoing' ? 'PE sedang melakukan review dan penggabungan' 
                                                        : ($PEmanagervalidation == 'Sudah dibaca' ? 'PE sudah dibaca' 
                                                        : ($PEmanagervalidation == 'Belum dibaca' ? 'PE belum dibaca' 
                                                        : 'PE belum melakukan review dan penggabungan'))) }}">
                                                    </div>
                                                    <span class="keterangan">PE</span>
                                                </div>
                                                <div class="container">
                                                    <span class="arrow">↓</span>
                                                </div>

                                                @if($MPEvalidation!="Tidak Terlibat")
                                                <div class="container">
                                                    <div class="indicator 
                                                        {{ $MPEvalidation == 'Aktif' ? 'green' 
                                                        : ($MPEvalidation == 'Ongoing' ? 'orange' 
                                                        : ($MPEvalidation == 'Sudah dibaca' ? 'blue' 
                                                        : ($MPEvalidation == 'Belum dibaca' ? 'yellow' 
                                                        : 'red'))) }}" 
                                                        title="{{ $MPEvalidation == 'Aktif' ? 'PE sudah melakukan review dan penggabungan' 
                                                        : ($MPEvalidation == 'Ongoing' ? 'PE sedang melakukan review dan penggabungan' 
                                                        : ($MPEvalidation == 'Sudah dibaca' ? 'PE sudah dibaca' 
                                                        : ($MPEvalidation == 'Belum dibaca' ? 'PE belum dibaca' 
                                                        : 'PE belum melakukan review dan penggabungan'))) }}">
                                                    </div>
                                                    <span class="keterangan">MPE</span>
                                                </div>
                                                <div class="container">
                                                    <span class="arrow">↓</span>
                                                </div>
                                                @endif


                                                <div class="container">
                                                    <div class="indicator 
                                                    {{ $seniormanagervalidation == 'Aktif' ? 'green' 
                                                    : ($seniormanagervalidation == 'Ongoing' ? 'orange' 
                                                    : ($seniormanagervalidation == 'Sudah dibaca' ? 'blue' 
                                                    : ($seniormanagervalidation == 'Belum dibaca' ? 'yellow' 
                                                    : 'red'))) }}" 
                                                    title="{{ $seniormanagervalidation == 'Aktif' ? 'Senior manager sudah melakukan review' 
                                                    : ($seniormanagervalidation == 'Ongoing' ? 'Senior manager sedang melakukan review' 
                                                    : ($seniormanagervalidation == 'Sudah dibaca' ? 'Senior manager sudah membaca' 
                                                    : ($seniormanagervalidation == 'Belum dibaca' ? 'Senior manager belum membaca' 
                                                    : 'Senior manager belum melakukan review'))) }}">
                                                    </div>
                                                    @if($SMname=="Belum ditentukan")
                                                        <span class="keterangan">SM</span>
                                                    @else
                                                        <span class="keterangan">{{$unitsingkatan[$SMname]}}</span>
                                                    @endif
                                                    
                                                </div>
                                                <div class="container">
                                                    <span class="arrow">↓</span>
                                                </div>
                                                <div class="container">
                                                    <div class="indicator 
                                                        {{ $MTPRvalidation == 'Aktif' ? 'green' 
                                                        : ($MTPRvalidation == 'Ongoing' ? 'orange' 
                                                        : ($MTPRvalidation == 'Sudah dibaca' ? 'blue' 
                                                        : ($MTPRvalidation == 'Belum dibaca' ? 'yellow' 
                                                        : 'red'))) }}" 
                                                        title="{{ $MTPRvalidation == 'Aktif' ? 'MTPR sudah menutup dokumen' 
                                                        : ($MTPRvalidation == 'Ongoing' ? 'MTPR sedang menutup dokumen' 
                                                        : ($MTPRvalidation == 'Sudah dibaca' ? 'MTPR sudah dibaca' 
                                                        : ($MTPRvalidation == 'Belum dibaca' ? 'MTPR belum dibaca' 
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
                                            <div class="box" style="position: absolute; top: 0; right: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                                                @php
                                                    $badgeClass = '';
                                                    $message = '';
                                                    $date = \Carbon\Carbon::parse($timeline["documentopened"]);
                                                    $deadline = $date->addDays(3);

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
                                                    } else {
                                                        if(isset($timeline["documentclosed"]))
                                                        {
                                                            $closed=\Carbon\Carbon::parse($timeline["documentclosed"]);
                                                            $differenceInDays = $closed->diffInDays($deadline, false);
                                                            if ($differenceInDays < 0) {
                                                                $badgeClass = 'badge-danger';
                                                                $message = "Telat " . abs($differenceInDays) . " hari";
                                                            } else {
                                                                $badgeClass = 'badge-success';
                                                                $message = "Diupload " . abs($differenceInDays) . " hari sebelum deadline";
                                                            }
                                                        }else{
                                                            $message = "Closed tanpa mengikuti alur";
                                                        }
                                                        
                                                    }
                                                    

                                                    echo "<span style='padding: 3px;' class='badge " . $badgeClass . "'>" . $message . "</span>";
                                                @endphp
                                            </div>





                                            <!-- Bagian yang akan diletakkan di pojok kiri bawah -->
                                            <a class="box" href="#" style="position: absolute; bottom: 0; left: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                                                <div class="container">
                                                    <span class="badge bg-{{$positionPercentage == 100 ? 'success' : 'warning'}}" style="padding: 5px;">
                                                        {{$positionPercentage}}% Completed
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- Bagian yang akan diletakkan di pojok kanan bawah -->
                                            <!-- <a class="box" href="#" style="position: absolute; bottom: 0; right: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                                                <div class="container">
                                                    <span class="badge bg-{{$positionPercentage == 100 ? 'success' : 'warning'}}" style="padding: 5px;">
                                                        {{$positionPercentage}}% Completed
                                                    </span>
                                                </div>
                                            </a> -->


                                            </div>
                                        </div>
                                    </td>        
                                    
                                    <td class="project-actions text-right">

                                        @if(auth()->user()->rule!="Logistik")
                                            
                                            <div class="col-md-12 text-right column-layout">
                                                <a class="btn btn-primary btn-sm" href="{{ route('memo.show', ['id' => $document->id, 'rule' => auth()->user()->rule]) }}"style="width: 100px;">
                                                    <i class="fas fa-folder"></i> Detail
                                                </a>
                                            </div>

                                            <div class="col-md-12 text-right column-layout">
                                                <!-- Pemanggilan modal -->
                                                <a href="#" class="btn btn-success btn-sm" onclick="showDocumentSummary('{{$informasidokumenencoded}}','{{$datadikirimencoded}}',{{$document->id}})"style="width: 100px;">
                                                    <i class="fas fa-print"></i> View
                                                </a>
                                                
                                            </div>

                                            <div class="col-md-12 text-right column-layout">
                                                <a class="btn btn-info btn-sm" href="{{ route('document.report', ['id' => $document->id, 'rule' => auth()->user()->rule]) }}"style="width: 100px;">
                                                    <i class="fas fa-chart-line"></i> Progress
                                                </a>
                                            </div>

                                            @if(auth()->user()->rule=="superuser")
                                                <div class="col-md-12 text-right column-layout">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $document->id }}')"style="width: 100px;">
                                                        <i class="fas fa-eraser"></i> Hapus
                                                    </button>
                                                </div>
                                            @endif

                                        @endif
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        

                    </div>
                @endforeach
            </div>
        </div> 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        function handleDeleteMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                // Jika pengguna mengonfirmasi penghapusan
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function(checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    // Melakukan panggilan AJAX untuk menghapus item yang dipilih
                    $.ajax({
                        url: "{{ route('document.deleteMultiple') }}",
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
                            location.reload();
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
    
    </script>



    <script>
    function handleReportMultipleItems() {
        // Menampilkan SweetAlert konfirmasi
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda yakin ingin melakukan report item yang dipilih?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, report!'
        }).then((result) => {
            // Jika pengguna mengonfirmasi
            if (result.isConfirmed) {
                // Mengambil daftar ID dokumen yang dipilih
                var selectedDocumentIds = [];
                var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                checkboxes.forEach(function(checkbox) {
                    selectedDocumentIds.push(checkbox.value);
                });

                // Melakukan panggilan AJAX untuk melakukan report pada item yang dipilih
                $.ajax({
                    url: "{{ route('document.reportMultiple') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        document_ids: selectedDocumentIds
                    },
                    xhrFields: {
                        responseType: 'blob' // Set responseType ke 'blob' untuk menerima file blob
                    },
                    success: function(response, status, xhr) {
                        // Membuat blob URL untuk unduh file
                        var blob = new Blob([response]);
                        var url = window.URL.createObjectURL(blob);
                        
                        // Membuat anchor untuk men-download file
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'document_report.xlsx'; // Nama file yang ingin diunduh
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        
                        // Hapus objek URL yang sudah tidak diperlukan
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.error;
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
    </script>




    <script>
        function showDocumentSummary(informasidokumenencoded, ringkasan, documentId) {
            // Parse JSON data
            var documentData = JSON.parse(ringkasan);
            var documentInfo = JSON.parse(informasidokumenencoded);

            // Construct document information section
            var documentInfoHTML = `
                <div style="text-align: center;">
                    <p style="font-weight: bold; font-size: 24px;">INFORMASI MEMO</p>
                </div>
                <div style="padding: 20px; font-size: 16px;">
                    <p style="font-weight: bold;">Kepada Yth,</p>
                    <ol>
            `;

            // Construct list of PICs
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                documentInfoHTML += `<li>${pic}</li>`;
            }

            // Add closing tags for list
            documentInfoHTML += `
                    </ol>
                    <hr style="margin-top: 20px;">
                    <div style="padding: 20px;">
                        <p style="font-size: 16px;">Kami sampaikan informasi dokumen berikut:</p>
                        <table style="width: 100%; margin-bottom: 20px;">
                            <tr>
                                <td style="font-weight: bold; width: 30%">Jenis Dokumen:</td>
                                <td>${documentInfo['category']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nama Memo:</td>
                                <td>${documentInfo['documentname']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nomor Memo:</td>
                                <td>${documentInfo['documentnumber']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Jenis Memo:</td>
                                <td>${documentInfo['memokind']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Asal Memo:</td>
                                <td>${documentInfo['memoorigin']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Status Dokumen:</td>
                                <td>${documentInfo['documentstatus']}</td>
                            </tr>
                        </table>
                    </div>
                `;

            // Construct table header
            var tableHeaderHTML = `
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 8px;">Pic</th>
                        <th style="border: 1px solid #000; padding: 8px;">Nama Penulis</th>
                        <th style="border: 1px solid #000; padding: 8px;">Email</th>
                        <th style="border: 1px solid #000; padding: 8px;">Status Feedback</th>
                        <th style="border: 1px solid #000; padding: 8px;">Kategori</th>
                        <th style="border: 1px solid #000; padding: 8px;">Sudah Dibaca</th>
                        <th style="border: 1px solid #000; padding: 8px;">Hasil Review</th>
                    </tr>
                </thead>`;

            // Construct table body
            var tableBodyHTML = '<tbody>';
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                var level = documentData[i].level;
                var userInformation = documentData[i].userinformations;

                // Filter out specific conditions
                if ((pic !== "MTPR" || level !== "pembukadokumen") && (pic !== "Product Engineering" || level !== "signature")) {
                    // Construct table row
                    var tableRowHTML = `
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">${pic}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['nama penulis']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['email']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile2']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['sudahdibaca']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['hasilreview']}</td>
                        </tr>`;
                    
                    tableBodyHTML += tableRowHTML;
                }
            }
            tableBodyHTML += '</tbody>';

            // Construct the complete HTML content
            var htmlContent = `
                <div style="padding: 20px;">
                    ${documentInfoHTML}
                    <div style="overflow-x: auto;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 16px;">
                            <caption style="caption-side: top; text-align: center; font-weight: bold; font-size: 20px; margin-bottom: 10px;">Feedback</caption>
                            ${tableHeaderHTML}
                            ${tableBodyHTML}
                        </table>
                    </div>
                </div>
                <img src="{{ asset('images/INKAICON.png') }}" alt="Company Logo" class="company-logo" style="position: absolute; top: 10px; right: 10px; width: 80px; height: 80px; object-fit: contain;">`;

            // Show SweetAlert2 modal with close and print buttons
            Swal.fire({
                html: htmlContent,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, print it!",
                cancelButtonText: "Close",
                width: '90%', // Lebar modal 90%
                padding: '2rem', // Padding konten modal
                customClass: {
                    image: 'img-fluid rounded-circle' // Menggunakan kelas Bootstrap untuk memastikan gambar perusahaan responsif
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Printed!",
                        text: "Your file has been printed.",
                        icon: "success"
                    });
                    printDocumentSummary(documentId);
                }
            });
        }

        function printDocumentSummary(documentId) {
            // Get the URL for the PDF
            var pdfUrl = "{{ url('document/memo') }}/" + documentId + "/pdf";

            // Open the PDF URL in a new window/tab for printing
            window.open(pdfUrl, '_blank');
        }
        
        function toggleDocumentStatus(button) {
            var documentId = $(button).data('document-id');
            var currentStatus = $(button).data('document-status');
            var newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';

            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah status dokumen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah status!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengirim permintaan AJAX untuk mengubah status dokumen
                    $.ajax({
                        url: "{{ url('document/memo') }}/" + documentId + "/update-document-status",
                        type: "PUT", // Menggunakan metode PUT karena mengubah data
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            // Update tampilan tombol
                            $(button).removeClass('document-status-button-' + currentStatus.toLowerCase()).addClass('document-status-button-' + newStatus.toLowerCase());
                            $(button).data('document-status', newStatus);
                            $(button).attr('title', newStatus);

                            // Update ikon di dalam tombol
                            var iconClass = newStatus === 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle';
                            $(button).find('i').removeClass().addClass(iconClass);
                            
                            // Update teks status
                            $(button).find('span').text(newStatus);

                            // Perubahan warna tombol sesuai dengan status baru
                            if (newStatus === 'Terbuka') {
                                $(button).removeClass('btn-success').addClass('btn-danger');
                            } else {
                                $(button).removeClass('btn-danger').addClass('btn-success');
                            }

                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Status dokumen berhasil diubah.",
                                icon: "success"
                            });
                        },
                        error: function(xhr, status, error) {
                            // Tampilkan pesan error
                            Swal.fire({
                                title: "Gagal!",
                                text: "Gagal mengubah status dokumen.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }

        function confirmDelete(documentId) {
        // Konfirmasi SweetAlert
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus dokumen ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL hapus dengan mengganti {id} dengan id dokumen yang sesuai
                Swal.fire({
                        title: "Berhasil!",
                        text: "Status Anda berhasil diubah.",
                        icon: "success"
                    });
                var Url = "{{ url('document/memo') }}/" + documentId + "/destroyget";

                // Redirect ke URL untuk mengubah status dokumen
                window.location.href = Url;
            }
        });
    }
    </script>
    
    <script>
        // Function to handle form submission with SweetAlert confirmation
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForm = document.getElementById('deleteForm');
            const submitBtn = document.getElementById('submitBtn');

            deleteForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Confirmation',
                    text: 'Do you want to submit the form?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                        title: "Updated!",
                        text: "Your information has been uploaded.",
                        icon: "success"
                        });
                        deleteForm.submit(); // Submit the form if user confirms
                    }
                });
            });
        });
    </script>
@endsection

@section('script')
    <script>
        $(function () {
            @foreach ($revisiall as $key => $revisi)
            $('#example2-{{ $key }}').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
            @endforeach
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
@endsection
