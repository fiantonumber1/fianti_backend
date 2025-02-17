<!-- resources/views/newbom/index.blade.php -->

@extends('layouts.universal')

@section('container2') 
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="/">BOM</a></li>
                    <li class="breadcrumb-item active text-bold">Tracking BOM</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')

<div class="card card-danger card-outline">
    <div class="card-header">
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <h3 class="card-title text-bold">Page monitoring BOM <span class="badge badge-info ml-1"></span></h3>
    </div>
    <div class="card-body">
        <!-- Dropdown for revisions -->
        <h1 class="mb-4">Daily Notifications</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Day</th>
                    <th>Read Status</th>
                    <th>Action</th> <!-- Kolom baru untuk tombol Show -->
                </tr>
            </thead>
            <tbody>
                @foreach ($dailyNotifications as $notification)
                    <tr>
                        <td>{{ $notification->id }}</td>
                        <td>{{ $notification->name }}</td>
                        <td>{{ $notification->day }}</td>
                        <td>{{ $notification->read_status }}</td>
                        <td>
                            <!-- Tombol Show -->
                            <a href="{{ route('daily-notifications.show', $notification->id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Show
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection