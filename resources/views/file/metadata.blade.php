@extends('layouts.main')

@section('container3')
    <title>Metadata File PDF</title>
@endsection

@section('container1')
    <div class="container text-center py-5">
        <h1 class="display-4">Metadata File PDF</h1>
    </div>
@endsection

@section('container2')
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <p><strong>Nama Dokumen:</strong> {{ $file->documentname }}</p>
                    <p><strong>Filename:</strong> {{ $file->filename }}</p>
                    <?php $newLinkFile = str_replace('uploads/', '', $file->linkfile); ?>

                    <p><strong>Link:</strong> <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">Download</a></p>
                    
                    <p><strong>Metadata:</strong></p>
                    <pre>{{ $file->metadata }}</pre>
                    <p><strong>Project:</strong></p>
                    <pre>{{ $file->project_type }}</pre>
                    <p><strong>Created at:</strong></p>
                    <p>Created at: {{ \Carbon\Carbon::parse($file->created_at)->addHours(0)->format('H:i:s') }}</p>
                </div>

                <!-- <div class="mb-4">
                    <p><strong>Project PIC:</strong></p>

                    @if ($file->project_pic)
                        <div class="d-flex flex-wrap">
                            @foreach (json_decode($file->project_pic) as $pic)
                                <div class="border rounded p-2 mr-3 mb-3">
                                    <a href="http://laravel_search.test/division?namafile={{ $file->filename }}&namaproject={{ $file->project_type }}&linkdownload=http://laravel_search.test/metadata/{{$file->id}}&namadivisi={{ $pic }}">{{ $pic }}</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No project PIC available.</p>
                    @endif
                </div> -->

                <div>
                    <p><strong>Edit:</strong> <a href="{{ route('metadata.edit', ['id' => $file->id]) }}">Edit File</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        pre {
            white-space: pre-wrap;
        }
    </style>
@endsection
