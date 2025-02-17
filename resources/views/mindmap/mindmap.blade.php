@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div id="jsmind_container"></div>

    <div class="mt-4">
        <h4>Tambah Node Baru</h4>
        <form action="{{ route('mindmap.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Node:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="parent_id">Parent Node:</label>
                <select name="parent_id" id="parent_id" class="form-control">
                    <option value="">Tanpa Parent (Root)</option>
                    @foreach ($mindMapNodes as $node)
                        <option value="{{ $node->id }}">{{ $node->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="level">Jenis Level:</label>
                <select name="level" id="level" class="form-control">
                    <option value="">Pilih Level</option>
                    @foreach ($kinds as $kind)
                        <option value="{{ $kind->id }}">{{ $kind->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Tambah Node</button>
        </form>

        <form action="{{ route('mindmap-kind.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Tambah Jenis</button>
        </form>
    </div>
@endsection

@push('css')
    <link type="text/css" rel="stylesheet" href="//cdn.jsdelivr.net/npm/jsmind@0.8.6/style/jsmind.css" />
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/jsmind@0.8.6/es6/jsmind.js"></script>

    <style>
        #jsmind_container {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript">
        const mindMapData = @json($mindMapData);
        const options = {
            container: 'jsmind_container',
            theme: 'orange',
            editable: true
        };

        const mind = {
            meta: mindMapData.meta,
            format: mindMapData.format,
            data: mindMapData.data
        };

        const jm = new jsMind(options);
        jm.show(mind);
    </script>
@endpush
