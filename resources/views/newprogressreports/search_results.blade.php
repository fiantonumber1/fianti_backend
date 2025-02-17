@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="">Search Results</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')  
    @if(isset($results) && count($results) > 0)
        <ul class="list-group">
            @foreach($results as $result)
                <li class="list-group-item">
                    <a href="#result-{{ $result->id }}" data-toggle="collapse">{{ $result->nodokumen }} - {{ $result->namadokumen }}</a>
                    <div id="result-{{ $result->id }}" class="collapse">
                        <p><strong>Level:</strong> {{ $result->level }}</p>
                        <p><strong>Drafter:</strong> {{ $result->drafter }}</p>
                        <p><strong>Checker:</strong> {{ $result->checker }}</p>
                        <p><strong>Deadline Release:</strong> {{ $result->deadlinerelease }}</p>
                        <p><strong>Jenis Dokumen:</strong> {{ $result->documentkind->name??"Belum ada jenis dokumennya" }}</p>
                        <p><strong>Realisasi:</strong> {{ $result->realisasi }}</p>
                        @php
                            if(isset($result->getLatestRevAttribute()->status)){
                                $status = $result->getLatestRevAttribute()->status;
                            } else{
                                $status = $result->status??"";
                            }

                            if(isset($result->getLatestRevAttribute()->status)){
                                $status = $result->getLatestRevAttribute()->status;
                            } else{
                                $status =  $result->status??"";
                            }


                        @endphp
                        <p><strong>Status:</strong> {{ $status }}</p>
                        <p><strong>Revisi Terakhir:</strong> {{ $result->getLatestRevAttribute()->rev ?? "Belum ada" }}</p>

                        <p><strong>Unit:</strong> {{ $result->newreport->unit ?? "Belum ada" }}</p>
                        <p><strong>Project:</strong> {{ $result->newreport->projectType->title ?? "Belum ada" }}</p>
                        
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p>No results found.</p>
    @endif
    

@endsection

@section('script') 

    


@endsection
