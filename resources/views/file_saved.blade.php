<!-- resources/views/file_saved.blade.php -->
@extends('layouts.main')

@section('container3')
    <title>File Successfully Send</title>
@endsection

@section('container1')
    <div class="container text-center py-5">
        <h1 class="display-4">File Successfully Send</h1>
    </div>
@endsection

@section('container2')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <strong>Congratulations!</strong> Your file has been successfully saved.
                </div>
                <p><strong>File Name:</strong> {{ $file->nama_file }}</p>
                <p><strong>Project Name:</strong> {{ $file->nama_project }}</p>
                <a href="{{ route('memo.show', ['id' =>$file->iddocument, 'rule' => auth()->user()->rule]) }}" class="btn btn-sm btn-primary mr-2">Buka File</a>
                <p><strong>Division:</strong> <a href="{{ route('divisi.show', ['namadivisi' => $file->nama_divisi]) }}">{{ $file->nama_divisi }}</a></p>
                <!-- resources/views/file_saved.blade.php -->                
            </div>
        </div>
    </div>
@endsection
