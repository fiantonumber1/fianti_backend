@extends('layouts.main')

@section('container2')
    <h1>Edit Document</h1>
    <form action="{{ route('documents.update', $document) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="documentname">Document Name</label>
            <input type="text" id="documentname" name="documentname" value="{{ $document->documentname }}" required>
        </div>
        <div>
            <label for="documentnumber">Document Number</label>
            <input type="text" id="documentnumber" name="documentnumber" value="{{ $document->documentnumber }}" required>
        </div>
        <button type="submit">Update</button>
    </form>
@endsection
