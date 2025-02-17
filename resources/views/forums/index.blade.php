{{-- resources/views/forums/index.blade.php --}}

@extends('layouts.main')

@section('container1')
    <div class="col-sm-6">
        <h1>Semua Forum</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('') }}">Home</a></li>
            <li class="breadcrumb-item active">Semua Forum</li>
        </ol>
    </div>
@endsection



@section('container3')
    <div class="row">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Forum Topics</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('forums.create') }}" class="btn btn-primary btn-sm btn-block">Buat Forum</a>
                        </div>
                    </div>
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topic</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forums as $forum)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $forum->topic }}</td>
                                    <td>{{ $forum->description }}</td>
                                    <td>
                                        <a href="{{ route('forums.show', $forum->id) }}" class="btn btn-primary">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
