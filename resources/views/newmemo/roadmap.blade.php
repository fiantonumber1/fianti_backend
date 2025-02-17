@extends('layouts.universal')

@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
          <li class="breadcrumb-item"><a href="{{ route('new-memo.indextertutup') }}">List Memo</a></li>
          <li class="breadcrumb-item"><a href="">{{$document->documentnumber}}</a></li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection



@section('container3')
    @php
        $projectpics=json_decode($document->project_pic,true);
        $unitpicvalidation=$document->unitpicvalidation;
    @endphp
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
           
            <p class="card-text"><strong>Nomor Dokumen:</strong> {{ $document->documentnumber }}</p>
            <p class="card-text"><strong>Nama Dokumen:</strong> {{ $document->documentname }}</p>
            <p class="card-text"><strong>Kategori:</strong> {{ $document->category }}</p>
                
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

<           <p>
                <div class="container">
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                    @php
                    
                        if($document->posisi1=="on"){
                            $classbox1="boxblue";
                        }else{
                            $classbox1="box";
                        }

                        if($document->posisi2=="on"){
                            $classbox2="boxblue";
                        }else{
                            $classbox2="box";
                        }

                        if($document->posisi3=="on"){
                            $classbox3="boxblue";
                        }else{
                            $classbox3="box";
                        }

                        if($document->MTPRvalidation=="Aktif"){
                            $classbox1="boxblue";
                            $classbox2="boxblue";
                            $classbox3="boxblue";
                        }
                        
                    @endphp
                    <a class="{{$classbox1}}" href="#">

                        @if($document->withMTPR=="Yes")
                            <div class="container">
                                <div class="indicator 
                                    {{ $document->MTPRsend == 'Aktif' ? 'green' : 'red' }}" 
                                    title="{{ $document->MTPRsend == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
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
                        @endif


                        <div class="container">
                            <div class="indicator 
                                {{ $document->operatorshare == 'Aktif' ? 'green' 
                                : ($document->operatorshare == 'Ongoing' ? 'orange' 
                                : ($document->operatorshare == 'Belum dibaca' ? 'yellow' 
                                : 'red')) }}" 
                                title="{{ $document->operatorshare == 'Aktif' ? 'Dokumen sudah dibagikan ke unit' 
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
                        @if($document->operator=="Product Engineering")
                            <div class="container">
                                <div class="indicator 
                                    {{ $document->operatorcombinevalidation == 'Aktif' ? 'green' 
                                    : ($document->operatorcombinevalidation == 'Ongoing' ? 'orange' 
                                    : ($document->operatorcombinevalidation == 'Sudah dibaca' ? 'blue' 
                                    : ($document->operatorcombinevalidation == 'Belum dibaca' ? 'yellow' 
                                    : 'red'))) }}" 
                                    title="{{ $document->operatorcombinevalidation == 'Aktif' ? 'PE sudah melakukan review dan penggabungan' 
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


                            @if($document->manageroperatorvalidation!="Tidak Terlibat")
                                <div class="container">
                                    <div class="indicator 
                                        {{ $document->manageroperatorvalidation == 'Aktif' ? 'green' 
                                        : ($document->manageroperatorvalidation == 'Ongoing' ? 'orange' 
                                        : ($document->manageroperatorvalidation == 'Sudah dibaca' ? 'blue' 
                                        : ($document->manageroperatorvalidation == 'Belum dibaca' ? 'yellow' 
                                        : 'red'))) }}" 
                                        title="{{ $document->manageroperatorvalidation == 'Aktif' ? 'PE sudah melakukan review dan penggabungan' 
                                        : ($document->manageroperatorvalidation == 'Ongoing' ? 'PE sedang melakukan review dan penggabungan' 
                                        : ($document->manageroperatorvalidation == 'Sudah dibaca' ? 'PE sudah dibaca' 
                                        : ($document->manageroperatorvalidation == 'Belum dibaca' ? 'PE belum dibaca' 
                                        : 'PE belum melakukan review dan penggabungan'))) }}">
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
                            : 'red'))) }}" 
                            title="{{ $document->seniormanagervalidation == 'Aktif' ? 'Senior manager sudah melakukan review' 
                            : ($document->seniormanagervalidation == 'Ongoing' ? 'Senior manager sedang melakukan review' 
                            : ($document->seniormanagervalidation == 'Sudah dibaca' ? 'Senior manager sudah membaca' 
                            : ($document->seniormanagervalidation == 'Belum dibaca' ? 'Senior manager belum membaca' 
                            : 'Senior manager belum melakukan review'))) }}">
                            </div>
                            @if($document->SMname=="Belum ditentukan")
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
                                : 'red'))) }}" 
                                title="{{ $document->MTPRvalidation == 'Aktif' ? 'MTPR sudah menutup dokumen' 
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
                    <div class="box" style="position: absolute; top: 0; right: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
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
                    <a class="box" href="#" style="position: absolute; bottom: 0; left: 0; background-color: rgba(255, 255, 255, 0); border: rgba(255, 255, 255, 0); z-index: 10;">
                        <div class="container">
                            <span class="badge bg-{{$document->positionPercentage == 100 ? 'success' : 'warning'}}" style="padding: 5px;">
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
            </p>


        </div> 
    </div> 
@endsection

