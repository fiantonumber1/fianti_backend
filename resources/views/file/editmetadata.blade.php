@extends('layouts.main')

@php
$listproject = ["KCI", "Retrofit"];
$listpic = ["MES", "PE"];
@endphp

@section('container3')
    <title>Edit Metadata File PDF</title>
@endsection

@section('container1')
    <div class="container text-center py-5">
        <h1 class="display-4">Edit Metadata File PDF</h1>
    </div>
@endsection

@section('container2')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('file.update', ['id' => $file->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="filename">Filename:</label>
                        <input type="text" class="form-control" id="filename" name="filename" value="{{ $file->filename }}">
                        <p><strong>Link:</strong> <a href="{{ route('file.download', ['id' => $file->id]) }}">Download File</a></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="metadata">Metadata:</label>
                        <textarea class="form-control" name="metadata" id="metadata" rows="5">{{ $file->metadata }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="project_type">Project:</label>
                        <select class="form-control" name="project_type" id="project_type">
                            @foreach($listproject as $project)
                                <option value="{{ $project }}" {{ $file->project_type == $project ? 'selected' : '' }}>{{ $project }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- <div class="form-group">
                        <label>Project PIC:</label><br>
                        @foreach($listpic as $pic)
                            @php
                                $picArray = $file->project_pic ? json_decode($file->project_pic) : []; // Mengonversi JSON menjadi array jika tidak kosong, jika kosong maka array kosong
                                $isChecked = in_array($pic, $picArray); // Memeriksa apakah $pic ada di $picArray
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="project_pic[]" value="{{ $pic }}" {{ $isChecked ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $pic }}</label>
                            </div>
                        @endforeach
                    </div> -->

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
