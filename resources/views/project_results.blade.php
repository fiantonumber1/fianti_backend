@extends('layouts.main')

@section('container3')
    <title>Project Results for {{ $query }}</title>
@endsection

@section('container1')
    <h1>Project Results for {{ $query }}</h1>
@endsection

@section('container2')
    <h1>Project Results for {{ $query }}</h1>
    @if ($files->isEmpty())
        <p>No results found for '{{ $query }}'</p>
    @else
        <ul>
            @foreach ($files as $file)
                <li>
                    <a href="{{ route('metadata.show', $file->id) }}">
                        {{ $file->filename }}
                    </a>
                </li>
                <p>Metadata: {{ $file->metadata }}</p>
                <p>Created at: {{ \Carbon\Carbon::parse($file->created_at)->addHours(7)->format('H:i:s') }}</p>
                <p><strong>Project PIC:</strong></p>

                @if ($file->project_pic)
                    <div style="display: flex;">
                        @foreach (json_decode($file->project_pic) as $pic)
                            <div style="border: 1px solid black; padding: 5px; margin-right: 10px;">
                                <a href="https://ptinka.com/{{ urlencode($pic) }}">{{ $pic }}</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No project PIC available.</p>
                @endif
            @endforeach
        </ul>
    @endif
@endsection
