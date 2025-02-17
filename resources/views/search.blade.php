@extends('layouts.main')

@section('container3')
    <title>Pencarian</title>
@endsection

@section('container1')
    <h1>Pencarian</h1>
@endsection

@section('container2')
<h1>Pencarian File</h1>
    <form action="{{ route('searchresult') }}" method="GET">
        @csrf
        <label for="query">Masukkan query:</label>
        <input type="text" id="query" name="query" required>
        <button type="submit">Cari</button>


    <label for="search_type">Select Search Type:</label><br>
    <select name="search_type" id="search_type">
        <option value="metadata">Metadata</option>
        <option value="project_type">Project</option>
    </select><br><br>

    </form>
@endsection
