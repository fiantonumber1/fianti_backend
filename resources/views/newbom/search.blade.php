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
                    <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="">Search Results</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')  
    <div class="container">
        <div class="card card-primary">
            <div class="card-header">Search Documents</div>
            <div class="card-body">
                <form action="{{ route('newprogressreports.search') }}" method="GET">
                    <div class="form-group">
                        <label for="query">Search:</label>
                        <input type="text" name="query" id="query" class="form-control" placeholder="Enter document name, nodokumen, etc.">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>


        
    </div>
    

@endsection



