<!-- resources/views/upload_success.blade.php -->

@extends('layouts.main')

@section('title', 'Upload Success')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Upload Success</div>

                    <div class="card-body">
                        <p>File successfully uploaded!</p>
                        <p><strong>Filename:</strong> {{ $filename }}</p>
                        <p><strong>Metadata:</strong></p>
                        <pre>{{ $metadata }}</pre>
                        <a href="{{ route('file.download', ['id' => $file->id]) }}">Download File</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
