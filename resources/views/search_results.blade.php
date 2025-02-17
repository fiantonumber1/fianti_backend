@extends('layouts.main')

@section('container3')
    <title>Search Results for {{ $query }}</title>
@endsection

@section('container1')
    <h1>Search Results</h1>
@endsection

@section('container2')
    <h1>Search Results</h1>
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
                {{ $file->metadata }}
            @endforeach
        </ul>
    @endif
@endsection
