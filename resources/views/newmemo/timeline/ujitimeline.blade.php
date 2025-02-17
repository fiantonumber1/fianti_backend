@extends('layouts.universal')

@php
    use Carbon\Carbon;
@endphp

@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
            @if ($document->documentstatus=="Tertutup")
                <li class="breadcrumb-item"><a href="{{ route('new-memo.indextertutup') }}">List Memo Tertutup</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('new-memo.indextertutup') }}">List Memo Terbuka</a></li>
            @endif
          
          <li class="breadcrumb-item"><a href="{{ route('new-memo.show', ['memoId' => $document->id, 'rule' => auth()->user()->rule]) }}">{{$document->documentnumber}}</a></li>
          <li class="breadcrumb-item"><a href="{{ route('new-memo.timelinetracking', ['memoId' => $document->id]) }}">Milestone</a></li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection



@section('container3')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">

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
                        
                        <div id="timeline-1" class>
                            <div class="row">
                                <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                                    <div class="timeline-container">
                                        <div class="timeline-label">
                                            <span class="label label-primary arrowed-in-right label-lg" style="font-size: 1.25em;">
                                                @php
                                                    if($document->operator == "Product Engineering"){
                                                        $tipealurmemo = "Paralel";
                                                    } else {
                                                        $tipealurmemo = "Seri";
                                                    }
                                                @endphp

                                                <b>Alur Memo {{$tipealurmemo}}</b>
                                            </span>

                                        </div>
                                        <div class="timeline-items">

                                            @if($document->MTPRsend)
                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                    <div class="timeline-info">
                                                        <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                                        <span class="label label-info label-sm">MTPR</span>
                                                    </div>
                                                
                                                

                                                    <div class="widget-box transparent">

                                                        
                                                        <div class="widget-header widget-header-small">
                                                            <h5 class="widget-title smaller">
                                                                <a href="#" class="blue">{{($document->MTPRsend_array)['author']}}</a>
                                                                <span class="grey"></span>
                                                            </h5>
                                                            <span class="widget-toolbar no-border">
                                                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                {{ \Carbon\Carbon::parse(($document->MTPRsend_array)['time'])->format('d/m/Y | H:i') }}
                                                            </span>



                                                            
                                                        </div>


                                                        <div class="widget-body">
                                                            <div class="widget-main">
                                                                Memo Telah dibuka
                                                                <span class="red"></span>
                                                                <div class="space-6"></div>
                                                            </div>
                                                        </div>




                                                    </div>

                                                </div>
                                            @endif


                                            @if($document->operatorshare_array)
                                                <div class="timeline-item clearfix">
                                                    <div class="timeline-info">
                                                        <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                        <span class="label label-danger label-sm">
                                                            @php
                                                                $start = Carbon::parse(($document->operatorshare_array)['time']);
                                                                $end = Carbon::parse(($document->MTPRsend_array)['time']);
                                                                $differenceInHours = $start->diffInHours($end);
                                                            @endphp
                                                            {{ $differenceInHours }} Jam
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif


                                            @if($document->operatorshare_array)

                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                    <div class="timeline-info">
                                                        <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar2.png">
                                                        <span class="label label-info label-sm">{{$unitsingkatan[$document->operator]}}</span>
                                                    </div>
                                                
                                                

                                                    <div class="widget-box transparent">

                                                        
                                                        <div class="widget-header widget-header-small">
                                                            <h5 class="widget-title smaller">
                                                                <a href="#" class="blue">{{($document->operatorshare_array)['author']}}</a>
                                                                <span class="grey"></span>
                                                            </h5>
                                                            <span class="widget-toolbar no-border">
                                                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                {{ \Carbon\Carbon::parse(($document->operatorshare_array)['time'])->format('d/m/Y | H:i') }}
                                                            </span>


                                                            
                                                        </div>


                                                        <div class="widget-body">
                                                            <div class="widget-main">
                                                                Dokumen telah disebarkan ke unit
                                                                <span class="red"></span>
                                                                <div class="space-6"></div>
                                                                
                                                            </div>
                                                        </div>




                                                    </div>

                                                </div>

                                            @endif


            
                                            @php
                                                // Mengambil array unitpicvalidation_array dari document
                                                $unitTimes = $document->unitpicvalidation_array;

                                                $unitfeedbackongoing=false;
                                                // Variabel untuk menyimpan nilai terbesar
                                                $longestDifference = 0;
                                                $longestUnit = '';
                                                $longestTime = '';
                                                $longestAuthor='';
                                                $longestUnit="";

                                                // Iterasi array untuk mencari selisih waktu terbesar
                                                foreach ($unitTimes as $unit => $data) {
                                                    // Pastikan key 'time' ada di dalam $data
                                                    // Konversi waktu menjadi objek Carbon
                                                    $start = Carbon::parse($data['time'] ?? now());

                                                    if (is_null($data['time'])) {
                                                        $unitfeedbackongoing = true;
                                                    } else {
                                                        $unitfeedbackongoing = false;
                                                    }

                                                    $end = Carbon::parse(($document->operatorshare_array)['time']);

                                                    // Hitung selisih waktu dalam jam
                                                    $differenceInHours = $start->diffInHours($end);

                                                    // Simpan unit dengan selisih waktu terbesar
                                                    if ($differenceInHours > $longestDifference) {
                                                        $longestDifference = $differenceInHours;
                                                        $longestUnit = $unit;  // Menggunakan kunci $unit
                                                        $longestTime = $data['time']; // Mengambil 'time'
                                                        $longestAuthor = $data['author']; // Mengambil 'time'
                                                        $longestUnit = $unit; // Mengambil 'time'
                                                        
                                                    }
                                                }
                                            @endphp

                                            @if($longestDifference>0)
                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                
                                                    <div class="timeline-info">
                                                        <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                        @if($unitfeedbackongoing ==false)
                                                            <span class="label label-danger label-sm"> {{$longestDifference}} Jam</span>
                                                        @else
                                                            <span class="label label-danger label-sm"> {{$longestDifference}} Jam (Ongoing)</span>
                                                        @endif
                                                    </div>
                                                
                                                

                                                    

                                                </div>
                                            @endif


                                            @if($longestDifference>0)
                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                    <div class="timeline-info">
                                                        <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar3.png">
                                                        <span class="label label-info label-sm">Units</span>
                                                    </div>
                                                
                                                

                                                    <div class="widget-box transparent">

                                                        
                                                        <div class="widget-header widget-header-small">
                                                            <h5 class="widget-title smaller">
                                                                <a href="#" class="blue">{{$longestAuthor}}</a>
                                                                <span class="grey">Menyelesaikan unit paling akhir</span>
                                                            </h5>
                                                            <span class="widget-toolbar no-border">
                                                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                {{ \Carbon\Carbon::parse(($longestTime))->format('d/m/Y | H:i') }}
                                                            </span>

                                                        


                                                            
                                                        </div>


                                                        <div class="widget-body">
                                                            <div class="widget-main">
                                                                <div class="clearfix">
                                                                    @php
                                                                        $unitTimes = $document->unitpicvalidation_array;
                                                                        $unitscount=0;
                                                                    @endphp

                                                                    <div class="pull-left">
                                                                        Unit :
                                                                        <br>
                                                                        @if($document->operator =="Product Engineering")
                                                                            @php
                                                                                $unitscount = 0; // Misalnya, jumlah unit
                                                                            @endphp
                                                                            @foreach ($unitTimes as $unit => $data)
                                                                                @php
                                                                                    
                                                                                    if($data['time']!=null){
                                                                                        $start = Carbon::parse(($data['time']));
                                                                                        $end = Carbon::parse(($document->operatorshare_array)['time']);
                                                                                        $differenceInHours = $start->diffInHours($end);
                                                                                    }else{
                                                                                        $start = Carbon::parse(now() );
                                                                                        $end = Carbon::parse(($document->operatorshare_array)['time']);
                                                                                        $differenceInHours = $start->diffInHours($end);
                                                                                    }
                                                                                   
                                                                                    $unitscount += 1; // Misalnya, jumlah unit
                                                                                @endphp
                                                                                <span class="label label-info label-sm">{{ $unit }}</span>: Menyelesaikan laporan dalam <span class="label label-danger label-sm">{{ $differenceInHours }} Jam</span> 
                                                                                <br>
                                                                            @endforeach
                                                                        @else
                                                                            @php
                                                                                $unitscount = 0; // Misalnya, jumlah unit
                                                                                $end = Carbon::parse(($document->operatorshare_array)['time']);
                                                                                $break=false;
                                                                            @endphp
                                                                            @foreach ($unitTimes as $unit => $data)
                                                                                @php
                                                                                    if($data['time']!=null){
                                                                                        $start = Carbon::parse(($data['time']));
                                                                                        $differenceInHours = $start->diffInHours($end);
                                                                                        $end=$start ;
                                                                                    }else{
                                                                                        if($break==false){
                                                                                            $start = Carbon::parse((now()));
                                                                                            $differenceInHours = $start->diffInHours($end);
                                                                                            $end=$start ;
                                                                                            $break==true;
                                                                                        }else{
                                                                                            $differenceInHours = 'Wait';
                                                                                        }
                                                                                        
                                                                                    }
                                                                                    $unitscount += 1; // Misalnya, jumlah unit
                                                                                @endphp
                                                                                <span class="label label-info label-sm">{{ $unit }}</span>: Menyelesaikan laporan dalam <span class="label label-danger label-sm">{{ $differenceInHours }} Jam</span> 
                                                                                <br>
                                                                            @endforeach

                                                                        @endif
                                                                    </div>

                                                                    <div class="pull-right">
                                                                        <i class="ace-icon fa fa-chevron-left blue bigger-110"></i>
                                                                        &nbsp;
                                                                        @foreach (range(1, $unitscount) as $i)
                                                                            <img alt="Image {{ $i }}" width="36" src="https://bootdey.com/img/Content/avatar/avatar{{ $i }}.png">
                                                                        @endforeach
                                                                        &nbsp;
                                                                        <i class="ace-icon fa fa-chevron-right blue bigger-110"></i>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>




                                                    </div>

                                                </div>
                                            @endif



                                            @if($document->operator =="Product Engineering")
                                                @if($document->operatorcombinevalidation_array)
                                                    <div class="timeline-item clearfix">
                                                        <div class="timeline-info">
                                                            <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                            <span class="label label-danger label-sm">
                                                                @php
                                                                    $start = Carbon::parse(($document->operatorcombinevalidation_array)['time']);
                                                                    $end = Carbon::parse($longestTime);
                                                                    $differenceInHours = $start->diffInHours($end);
                                                                @endphp
                                                                {{ $differenceInHours }} Jam
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($document->operatorcombinevalidation_array)
                                                    <div class="timeline-item clearfix">
                                                        
                                                    
                                                        <div class="timeline-info">
                                                            <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar4.png">
                                                            <span class="label label-info label-sm">{{$unitsingkatan[$document->operator]}}</span>
                                                        </div>
                                                    
                                                    

                                                        <div class="widget-box transparent">

                                                            
                                                            <div class="widget-header widget-header-small">
                                                                <h5 class="widget-title smaller">
                                                                    <a href="#" class="blue">{{($document->operatorcombinevalidation_array)['author']}}</a>
                                                                    <span class="grey"></span>
                                                                </h5>
                                                                <span class="widget-toolbar no-border">
                                                                    <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                    {{ \Carbon\Carbon::parse(($document->operatorcombinevalidation_array)['time'])->format('d/m/Y | H:i') }}
                                                                </span>


                                                                
                                                            </div>


                                                            <div class="widget-body">
                                                                <div class="widget-main">
                                                                    PE melakukan penggabungan feedback
                                                                    <span class="red"></span>
                                                                    <div class="space-6"></div>
                                                                    
                                                                </div>
                                                            </div>




                                                        </div>

                                                    </div>
                                                @endif

                                                @if($document->manageroperatorvalidation_array )
                                                    <div class="timeline-item clearfix">
                                                        <div class="timeline-info">
                                                            <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                            <span class="label label-danger label-sm">
                                                                @php
                                                                    $start = Carbon::parse(($document->manageroperatorvalidation_array )['time']);
                                                                    $end = Carbon::parse(($document->operatorcombinevalidation_array)['time']);
                                                                    $differenceInHours = $start->diffInHours($end);
                                                                @endphp
                                                                {{ $differenceInHours }} Jam
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($document->manageroperatorvalidation_array)
                                                    <div class="timeline-item clearfix">
                                                        
                                                    
                                                        <div class="timeline-info">
                                                            <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar4.png">
                                                            <span class="label label-info label-sm">{{$unitsingkatan['Manager '.$document->operator]}}</span>
                                                        </div>
                                                    
                                                    

                                                        <div class="widget-box transparent">

                                                            
                                                            <div class="widget-header widget-header-small">
                                                                <h5 class="widget-title smaller">
                                                                    <a href="#" class="blue">{{($document->manageroperatorvalidation_array)['author']}}</a>
                                                                    <span class="grey"></span>
                                                                </h5>
                                                                <span class="widget-toolbar no-border">
                                                                    <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                    {{ \Carbon\Carbon::parse(($document->manageroperatorvalidation_array)['time'])->format('d/m/Y | H:i') }}
                                                                </span>


                                                                
                                                            </div>


                                                            <div class="widget-body">
                                                                <div class="widget-main">
                                                                    Manager PE melakukan verifikasi
                                                                    <span class="red"></span>
                                                                    <div class="space-6"></div>
                                                                    
                                                                </div>
                                                            </div>




                                                        </div>

                                                    </div>
                                                @endif

                                                @if($document->seniormanagervalidation_array )
                                                    <div class="timeline-item clearfix">
                                                        <div class="timeline-info">
                                                            <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                            <span class="label label-danger label-sm">
                                                                @php
                                                                    $start = Carbon::parse(($document->seniormanagervalidation_array )['time']);
                                                                    $end = Carbon::parse(($document->manageroperatorvalidation_array)['time']);
                                                                    $differenceInHours = $start->diffInHours($end);
                                                                @endphp
                                                                {{ $differenceInHours }} Jam
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif


                                            @else
                                                @if($document->seniormanagervalidation_array)
                                                    <div class="timeline-item clearfix">
                                                        <div class="timeline-info">
                                                            <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                            <span class="label label-danger label-sm">
                                                                @php
                                                                    $start = Carbon::parse(($document->seniormanagervalidation_array)['time']);
                                                                    $end = Carbon::parse($longestTime);
                                                                    $differenceInHours = $start->diffInHours($end);
                                                                @endphp
                                                                {{ $differenceInHours }} Jam
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($document->seniormanagervalidation_array)
                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                    <div class="timeline-info">
                                                        <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar4.png">
                                                        <span class="label label-info label-sm">{{$unitsingkatan[$document->SMname??"SM"]}}</span>
                                                    </div>
                                                
                                                

                                                    <div class="widget-box transparent">

                                                        
                                                        <div class="widget-header widget-header-small">
                                                            <h5 class="widget-title smaller">
                                                                <a href="#" class="blue">{{($document->seniormanagervalidation_array)['author']}}</a>
                                                                <span class="grey"></span>
                                                            </h5>
                                                            <span class="widget-toolbar no-border">
                                                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                {{ \Carbon\Carbon::parse(($document->seniormanagervalidation_array)['time'])->format('d/m/Y | H:i') }}
                                                            </span>


                                                            
                                                        </div>


                                                        <div class="widget-body">
                                                            <div class="widget-main">
                                                                Senior Manager melakukan Approve
                                                                <span class="red"></span>
                                                                <div class="space-6"></div>
                                                                
                                                            </div>
                                                        </div>




                                                    </div>

                                                </div>
                                            @endif

                                            @if($document->MTPRvalidation_array )
                                                <div class="timeline-item clearfix">
                                                    <div class="timeline-info">
                                                        <i class="timeline-indicator ace-icon fa fa-clock-o btn btn-success no-hover"></i>
                                                        <span class="label label-danger label-sm">
                                                            @php
                                                                $start = Carbon::parse(($document->MTPRvalidation_array)['time']);
                                                                $end = Carbon::parse(($document->seniormanagervalidation_array)['time']);
                                                                $differenceInHours = $start->diffInHours($end);
                                                            @endphp
                                                            {{ $differenceInHours }} Jam
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($document->MTPRvalidation_array)
                                                <div class="timeline-item clearfix">
                                                    
                                                
                                                    <div class="timeline-info">
                                                        <img alt="Avatar" src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                                        <span class="label label-info label-sm">MTPR</span>
                                                    </div>
                                                
                                                

                                                    <div class="widget-box transparent">

                                                        
                                                        <div class="widget-header widget-header-small">
                                                            <h5 class="widget-title smaller">
                                                                <a href="#" class="blue">{{($document->MTPRsend_array)['author']}}</a>
                                                                <span class="grey"></span>
                                                            </h5>
                                                            <span class="widget-toolbar no-border">
                                                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                                                {{ \Carbon\Carbon::parse(($document->MTPRvalidation_array)['time'])->format('d/m/Y | H:i') }}
                                                            </span>


                                                            
                                                        </div>


                                                        <div class="widget-body">
                                                            <div class="widget-main">
                                                                Memo ditutup
                                                                <span class="red"></span>
                                                                <div class="space-6"></div>
                                                                
                                                            </div>
                                                        </div>




                                                    </div>

                                                </div>
                                            @endif

                                            
                                            
                                            <div class="timeline-item clearfix">
                                            
                                                <div class="timeline-info">
                                                    <i class="timeline-indicator ace-icon fa fa-star btn btn-warning no-hover green"></i>
                                                    @if($document->documentstatus!="Tertutup")
                                                        <span class="label label-info label-sm"> Memo Terbuka</span>
                                                    @elseif($document->MTPRvalidation_array)
                                                        <span class="label label-info label-sm"> Memo Tertutup</span>
                                                    @else
                                                        <span class="label label-info label-sm"> Memo Tertutup (Paksa)</span>
                                                    @endif
                                                </div>
                                                

                                                <div class="widget-box transparent">
                                                    <div class="widget-body">
                                                        
                                                    </div>
                                                </div>

                                            </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    
                
                    </div>
                </div>

            </div>
        </div>
    </div>
    
@endsection

@push('css')

    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">
        body{margin-top:20px;}


        .timeline-container {
            position: relative;
            padding-top: 4px;
            margin-bottom: 32px
        }

        .timeline-container:last-child {
            margin-bottom: 0
        }

        .timeline-container:before {
            content: "";
            display: block;
            position: absolute;
            left: 28px;
            top: 0;
            bottom: 0;
            border: 1px solid #E2E3E7;
            background-color: #E7EAEF;
            width: 4px;
            border-width: 0 1px
        }

        .timeline-container:first-child:before {
            border-top-width: 1px
        }

        .timeline-container:last-child:before {
            border-bottom-width: 1px
        }

        .timeline-item {
            position: relative;
            margin-bottom: 8px
        }

        .timeline-item .widget-box {
            background-color: #F2F6F9;
            color: #595C66
        }

        .timeline-item .transparent.widget-box {
            border-left: 3px solid #DAE1E5
        }

        .timeline-item .transparent .widget-header {
            background-color: #ECF1F4;
            border-bottom-width: 0
        }

        .timeline-item .transparent .widget-header>.widget-title {
            margin-left: 8px
        }

        .timeline-item:nth-child(even) .widget-box {
            background-color: #F3F3F3;
            color: #616161
        }

        .timeline-item:nth-child(even) .widget-box.transparent {
            border-left-color: #DBDBDB!important
        }

        .timeline-item:nth-child(even) .widget-box.transparent .widget-header {
            background-color: #EEE!important
        }

        .timeline-item .widget-box {
            margin: 0 0 0 60px;
            position: relative;
            max-width: none
        }

        .timeline-item .widget-main {
            margin: 0;
            position: relative;
            max-width: none;
            border-bottom-width: 0
        }

        .timeline-item .widget-body {
            background-color: transparent
        }

        .timeline-item .widget-toolbox {
            padding: 4px 8px 0!important;
            background-color: transparent!important;
            border-width: 0!important;
            margin: 0!important
        }

        .timeline-info {
            float: left;
            width: 60px;
            text-align: center;
            position: relative
        }

        .timeline-info img {
            border-radius: 100%;
            max-width: 42px
        }

        .timeline-info .badge,
        .timeline-info .label {
            font-size: 12px
        }

        .timeline-container:not(.timeline-style2) .timeline-indicator {
            opacity: 1;
            border-radius: 100%;
            display: inline-block;
            font-size: 16px;
            height: 36px;
            line-height: 30px;
            width: 36px;
            text-align: center;
            text-shadow: none!important;
            padding: 0;
            cursor: default;
            border: 3px solid #FFF!important
        }

        .timeline-label {
            display: block;
            clear: both;
            margin: 0 0 18px 34px
        }

        .timeline-item img {
            border: 1px solid #AAA;
            padding: 2px;
            background-color: #FFF
        }

        .timeline-style2:before {
            display: none
        }

        .timeline-style2 .timeline-item {
            padding-bottom: 22px;
            margin-bottom: 0
        }

        .timeline-style2 .timeline-item:last-child {
            padding-bottom: 0
        }

        .timeline-style2 .timeline-item:before {
            content: "";
            display: block;
            position: absolute;
            left: 90px;
            top: 5px;
            bottom: -5px;
            border-width: 0;
            background-color: #DDD;
            width: 2px;
            max-width: 2px
        }

        .timeline-style2 .timeline-item:last-child:before {
            display: none
        }

        .timeline-style2 .timeline-item:first-child:before {
            display: block
        }

        .timeline-style2 .timeline-item .transparent .widget-header {
            background-color: transparent!important
        }

        .timeline-style2 .timeline-item .transparent.widget-box {
            background-color: transparent!important;
            border-left: none!important
        }

        .timeline-style2 .timeline-info {
            width: 100px
        }

        .timeline-style2 .timeline-indicator {
            font-size: 0;
            height: 12px;
            line-height: 12px;
            width: 12px;
            border-width: 1px!important;
            background-color: #FFF!important;
            position: absolute;
            left: 85px;
            top: 3px;
            opacity: 1;
            border-radius: 100%;
            display: inline-block;
            padding: 0
        }

        .timeline-style2 .timeline-date {
            display: inline-block;
            width: 72px;
            text-align: right;
            margin-right: 25px;
            color: #777
        }

        .timeline-style2 .timeline-item .widget-box {
            margin-left: 112px
        }

        .timeline-style2 .timeline-label {
            width: 75px;
            margin-left: 0;
            margin-bottom: 10px;
            text-align: right;
            color: #666;
            font-size: 14px
        }

        .timeline-time {
            text-align: center;
            position: static
        }


        .widget-box {
            padding: 0;
            box-shadow: none;
            margin: 3px 0;
            border: 1px solid #CCC
        }

        @media only screen and (max-width:767px) {
            .widget-box {
                margin-top: 7px;
                margin-bottom: 7px
            }
        }

        .widget-header {
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
            position: relative;
            min-height: 38px;
            background: repeat-x #f7f7f7;
            background-image: -webkit-linear-gradient(top, #FFF 0, #EEE 100%);
            background-image: -o-linear-gradient(top, #FFF 0, #EEE 100%);
            background-image: linear-gradient(to bottom, #FFF 0, #EEE 100%);
            filter: progid: DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffeeeeee', GradientType=0);
            color: #669FC7;
            border-bottom: 1px solid #DDD;
            padding-left: 12px
        }

        .widget-box.transparent>.widget-header,
        .widget-header-flat {
            filter: progid: DXImageTransform.Microsoft.gradient(enabled=false)
        }

        .widget-header:after,
        .widget-header:before {
            content: "";
            display: table;
            line-height: 0
        }

        .widget-header:after {
            clear: right
        }

        .widget-box.collapsed>.widget-header {
            border-bottom-width: 0
        }

        .collapsed.fullscreen>.widget-header {
            border-bottom-width: 1px
        }

        .collapsed>.widget-body {
            display: none
        }

        .widget-header-flat {
            background: #F7F7F7
        }

        .widget-header-large {
            min-height: 49px;
            padding-left: 18px
        }

        .widget-header-small {
            min-height: 31px;
            padding-left: 10px
        }

        .widget-header>.widget-title {
            line-height: 36px;
            padding: 0;
            margin: 0;
            display: inline
        }

        .widget-header>.widget-title>.ace-icon {
            margin-right: 5px;
            font-weight: 400;
            display: inline-block
        }

        .infobox .infobox-content:first-child,
        .infobox>.badge,
        .infobox>.stat,
        .percentage {
            font-weight: 700
        }

        .widget-header-large>.widget-title {
            line-height: 48px
        }

        .widget-header-small>.widget-title {
            line-height: 30px
        }

        .widget-toolbar {
            display: inline-block;
            padding: 0 10px;
            line-height: 37px;
            float: right;
            position: relative
        }

        .widget-header-large>.widget-toolbar {
            line-height: 48px
        }

        .widget-header-small>.widget-toolbar {
            line-height: 29px
        }

        .widget-toolbar.no-padding {
            padding: 0
        }

        .widget-toolbar.padding-5 {
            padding: 0 5px
        }

        .widget-toolbar:before {
            display: inline-block;
            content: "";
            position: absolute;
            top: 3px;
            bottom: 3px;
            left: -1px;
            border: 1px solid #D9D9D9;
            border-width: 0 1px 0 0
        }

        .popover-notitle+.popover .popover-title,
        .popover.popover-notitle .popover-title,
        .widget-toolbar.no-border:before {
            display: none
        }

        .widget-header-large>.widget-toolbar:before {
            top: 6px;
            bottom: 6px
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar:before {
            border-color: #EEE
        }

        .widget-color-orange>.widget-header>.widget-toolbar:before {
            border-color: #FEA
        }

        .widget-color-dark>.widget-header>.widget-toolbar:before {
            border-color: #222;
            box-shadow: -1px 0 0 rgba(255, 255, 255, .2), inset 1px 0 0 rgba(255, 255, 255, .1)
        }

        .widget-toolbar label {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 0
        }

        .widget-toolbar>.widget-menu>a,
        .widget-toolbar>a {
            font-size: 14px;
            margin: 0 1px;
            display: inline-block;
            padding: 0;
            line-height: 24px
        }

        .widget-toolbar>.widget-menu>a:hover,
        .widget-toolbar>a:hover {
            text-decoration: none
        }

        .widget-header-large>.widget-toolbar>.widget-menu>a,
        .widget-header-large>.widget-toolbar>a {
            font-size: 15px;
            margin: 0 1px
        }

        .widget-toolbar>.btn {
            line-height: 27px;
            margin-top: -2px
        }

        .widget-toolbar>.btn.smaller {
            line-height: 26px
        }

        .widget-toolbar>.btn.bigger {
            line-height: 28px
        }

        .widget-toolbar>.btn-sm {
            line-height: 24px
        }

        .widget-toolbar>.btn-sm.smaller {
            line-height: 23px
        }

        .widget-toolbar>.btn-sm.bigger {
            line-height: 25px
        }

        .widget-toolbar>.btn-xs {
            line-height: 22px
        }

        .widget-toolbar>.btn-xs.smaller {
            line-height: 21px
        }

        .widget-toolbar>.btn-xs.bigger {
            line-height: 23px
        }

        .widget-toolbar>.btn-minier {
            line-height: 18px
        }

        .widget-toolbar>.btn-minier.smaller {
            line-height: 17px
        }

        .widget-toolbar>.btn-minier.bigger {
            line-height: 19px
        }

        .widget-toolbar>.btn-lg {
            line-height: 36px
        }

        .widget-toolbar>.btn-lg.smaller {
            line-height: 34px
        }

        .widget-toolbar>.btn-lg.bigger {
            line-height: 38px
        }

        .widget-toolbar-dark {
            background: #444
        }

        .widget-toolbar-light {
            background: rgba(255, 255, 255, .85)
        }

        .widget-toolbar>.widget-menu {
            display: inline-block;
            position: relative
        }

        .widget-toolbar>.widget-menu>a[data-action],
        .widget-toolbar>a[data-action] {
            -webkit-transition: transform .1s;
            -o-transition: transform .1s;
            transition: transform .1s
        }

        .widget-toolbar>.widget-menu>a[data-action]>.ace-icon,
        .widget-toolbar>a[data-action]>.ace-icon {
            margin-right: 0
        }

        .widget-toolbar>.widget-menu>a[data-action]:focus,
        .widget-toolbar>a[data-action]:focus {
            text-decoration: none;
            outline: 0
        }

        .widget-toolbar>.widget-menu>a[data-action]:hover,
        .widget-toolbar>a[data-action]:hover {
            -moz-transform: scale(1.2);
            -webkit-transform: scale(1.2);
            -o-transform: scale(1.2);
            -ms-transform: scale(1.2);
            transform: scale(1.2)
        }

        .widget-body {
            background-color: #FFF
        }

        .widget-main {
            padding: 12px
        }

        .widget-main.padding-32 {
            padding: 32px
        }

        .widget-main.padding-30 {
            padding: 30px
        }

        .widget-main.padding-28 {
            padding: 28px
        }

        .widget-main.padding-26 {
            padding: 26px
        }

        .widget-main.padding-24 {
            padding: 24px
        }

        .widget-main.padding-22 {
            padding: 22px
        }

        .widget-main.padding-20 {
            padding: 20px
        }

        .widget-main.padding-18 {
            padding: 18px
        }

        .widget-main.padding-16 {
            padding: 16px
        }

        .widget-main.padding-14 {
            padding: 14px
        }

        .widget-main.padding-12 {
            padding: 12px
        }

        .widget-main.padding-10 {
            padding: 10px
        }

        .widget-main.padding-8 {
            padding: 8px
        }

        .widget-main.padding-6 {
            padding: 6px
        }

        .widget-main.padding-4 {
            padding: 4px
        }

        .widget-main.padding-2 {
            padding: 2px
        }

        .widget-main.no-padding,
        .widget-main.padding-0 {
            padding: 0
        }

        .widget-toolbar .progress {
            vertical-align: middle;
            display: inline-block;
            margin: 0
        }

        .widget-toolbar>.dropdown,
        .widget-toolbar>.dropup {
            display: inline-block
        }

        .widget-toolbox.toolbox-vertical,
        .widget-toolbox.toolbox-vertical+.widget-main {
            display: table-cell;
            vertical-align: top
        }

        .widget-box>.widget-header>.widget-toolbar>.widget-menu>[data-action=settings],
        .widget-box>.widget-header>.widget-toolbar>[data-action=settings],
        .widget-color-dark>.widget-header>.widget-toolbar>.widget-menu>[data-action=settings],
        .widget-color-dark>.widget-header>.widget-toolbar>[data-action=settings] {
            color: #99CADB
        }

        .widget-box>.widget-header>.widget-toolbar>.widget-menu>[data-action=reload],
        .widget-box>.widget-header>.widget-toolbar>[data-action=reload],
        .widget-color-dark>.widget-header>.widget-toolbar>.widget-menu>[data-action=reload],
        .widget-color-dark>.widget-header>.widget-toolbar>[data-action=reload] {
            color: #ACD392
        }

        .widget-box>.widget-header>.widget-toolbar>.widget-menu>[data-action=collapse],
        .widget-box>.widget-header>.widget-toolbar>[data-action=collapse],
        .widget-color-dark>.widget-header>.widget-toolbar>.widget-menu>[data-action=collapse],
        .widget-color-dark>.widget-header>.widget-toolbar>[data-action=collapse] {
            color: #AAA
        }

        .widget-box>.widget-header>.widget-toolbar>.widget-menu>[data-action=close],
        .widget-box>.widget-header>.widget-toolbar>[data-action=close],
        .widget-color-dark>.widget-header>.widget-toolbar>.widget-menu>[data-action=close],
        .widget-color-dark>.widget-header>.widget-toolbar>[data-action=close] {
            color: #E09E96
        }

        .widget-box[class*=widget-color-]>.widget-header {
            color: #FFF;
            filter: progid: DXImageTransform.Microsoft.gradient(enabled=false)
        }

        .widget-color-blue {
            border-color: #307ECC
        }

        .widget-color-blue>.widget-header {
            background: #307ECC;
            border-color: #307ECC
        }

        .widget-color-blue2 {
            border-color: #5090C1
        }

        .widget-color-blue2>.widget-header {
            background: #5090C1;
            border-color: #5090C1
        }

        .widget-color-blue3 {
            border-color: #6379AA
        }

        .widget-color-blue3>.widget-header {
            background: #6379AA;
            border-color: #6379AA
        }

        .widget-color-green {
            border-color: #82AF6F
        }

        .widget-color-green>.widget-header {
            background: #82AF6F;
            border-color: #82AF6F
        }

        .widget-color-green2 {
            border-color: #2E8965
        }

        .widget-color-green2>.widget-header {
            background: #2E8965;
            border-color: #2E8965
        }

        .widget-color-green3 {
            border-color: #4EBC30
        }

        .widget-color-green3>.widget-header {
            background: #4EBC30;
            border-color: #4EBC30
        }

        .widget-color-red {
            border-color: #E2755F
        }

        .widget-color-red>.widget-header {
            background: #E2755F;
            border-color: #E2755F
        }

        .widget-color-red2 {
            border-color: #E04141
        }

        .widget-color-red2>.widget-header {
            background: #E04141;
            border-color: #E04141
        }

        .widget-color-red3 {
            border-color: #D15B47
        }

        .widget-color-red3>.widget-header {
            background: #D15B47;
            border-color: #D15B47
        }

        .widget-color-purple {
            border-color: #7E6EB0
        }

        .widget-color-purple>.widget-header {
            background: #7E6EB0;
            border-color: #7E6EB0
        }

        .widget-color-pink {
            border-color: #CE6F9E
        }

        .widget-color-pink>.widget-header {
            background: #CE6F9E;
            border-color: #CE6F9E
        }

        .widget-color-orange {
            border-color: #E8B10D
        }

        .widget-color-orange>.widget-header {
            color: #855D10!important;
            border-color: #E8B10D;
            background: #FFC657
        }

        .widget-color-dark {
            border-color: #5a5a5a
        }

        .widget-color-dark>.widget-header {
            border-color: #666;
            background: #404040
        }

        .widget-color-grey {
            border-color: #9e9e9e
        }

        .widget-color-grey>.widget-header {
            border-color: #aaa;
            background: #848484
        }

        .widget-box.transparent {
            border-width: 0
        }

        .widget-box.transparent>.widget-header {
            background: 0 0;
            border-width: 0;
            border-bottom: 1px solid #DCE8F1;
            color: #4383B4;
            padding-left: 3px
        }

        .widget-box.transparent>.widget-header-large {
            padding-left: 5px
        }

        .widget-box.transparent>.widget-header-small {
            padding-left: 1px
        }

        .widget-box.transparent>.widget-body {
            border-width: 0;
            background-color: transparent
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar>.widget-menu>[data-action],
        [class*=widget-color-]>.widget-header>.widget-toolbar>[data-action] {
            text-shadow: 0 1px 1px rgba(0, 0, 0, .2)
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar>.widget-menu>[data-action=settings],
        [class*=widget-color-]>.widget-header>.widget-toolbar>[data-action=settings] {
            color: #D3E4ED
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar>.widget-menu>[data-action=reload],
        [class*=widget-color-]>.widget-header>.widget-toolbar>[data-action=reload] {
            color: #DEEAD3
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar>.widget-menu>[data-action=collapse],
        [class*=widget-color-]>.widget-header>.widget-toolbar>[data-action=collapse] {
            color: #E2E2E2
        }

        [class*=widget-color-]>.widget-header>.widget-toolbar>.widget-menu>[data-action=close],
        [class*=widget-color-]>.widget-header>.widget-toolbar>[data-action=close] {
            color: #FFD9D5
        }

        .widget-color-orange>.widget-header>.widget-toolbar>.widget-menu>[data-action],
        .widget-color-orange>.widget-header>.widget-toolbar>[data-action] {
            text-shadow: none
        }

        .widget-color-orange>.widget-header>.widget-toolbar>.widget-menu>[data-action=settings],
        .widget-color-orange>.widget-header>.widget-toolbar>[data-action=settings] {
            color: #559AAB
        }

        .widget-color-orange>.widget-header>.widget-toolbar>.widget-menu>[data-action=reload],
        .widget-color-orange>.widget-header>.widget-toolbar>[data-action=reload] {
            color: #7CA362
        }

        .widget-color-orange>.widget-header>.widget-toolbar>.widget-menu>[data-action=collapse],
        .widget-color-orange>.widget-header>.widget-toolbar>[data-action=collapse] {
            color: #777
        }

        .widget-color-orange>.widget-header>.widget-toolbar>.widget-menu>[data-action=close],
        .widget-color-orange>.widget-header>.widget-toolbar>[data-action=close] {
            color: #A05656
        }

        .widget-box.light-border[class*=widget-color-]:not(.fullscreen) {
            border-width: 0
        }

        .widget-box.light-border[class*=widget-color-]:not(.fullscreen)>.widget-header {
            border: 1px solid;
            border-color: inherit
        }

        .widget-box.light-border[class*=widget-color-]:not(.fullscreen)>.widget-body {
            border: 1px solid #D6D6D6;
            border-width: 0 1px 1px
        }

        .widget-box.no-border {
            border-width: 0
        }

        .widget-box.fullscreen {
            position: fixed;
            margin: 0;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #FFF;
            border-width: 3px;
            z-index: 1040!important
        }

        .widget-box.fullscreen:not([class*=widget-color-]) {
            border-color: #AAA
        }

        .widget-body .table {
            border-top: 1px solid #E5E5E5
        }

        .widget-body .table thead:first-child tr {
            background: #FFF
        }

    </style>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

@endpush
@push('scripts')

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript"></script>

@endpush