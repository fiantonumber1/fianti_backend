@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newbom.searchkomat') }}">Search Results</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')  
<div class="container mt-5">
    <h1 class="mb-4">Pencarian Material</h1>

    <form action="{{ route('newbom.searchkomat') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="query" class="form-control" placeholder="Cari material atau kode material"
                value="{{ request('query') }}" required>
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    @if(isset($query))
        <p><strong>Query:</strong> {{ $query }}</p>

        @if($results->count() > 0)
            <div class="list-group">
                @foreach($results as $result)
                    <div class="list-group-item list-group-item-action mb-3">
                        <h5 class="mb-1">Material: {{ $result->material ?? '-' }}</h5>
                        <p class="mb-1"><strong>Kode Material:</strong> {{ $result->kodematerial ?? '-' }}</p>
                        <p class="mb-1"><strong>Unit:</strong> {{ $result->newbom->unit ?? 'Tidak Diketahui' }}</p>
                        <p class="mb-1"><strong>Project:</strong> {{ $result->newbom->projectType->title ?? 'Tidak Diketahui' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning">⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: <strong>{{ $query }}</strong>
            </div>
        @endif
    @endif
</div>
@endsection