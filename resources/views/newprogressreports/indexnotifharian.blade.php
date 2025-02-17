@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2') 
    <div class="content-header mt-2">
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
            <ol class="breadcrumb bg-white px-2 float-left mt-1">
            <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
            <li class="breadcrumb-item active text-bold"><a href="">Notif Harian Unit</a></li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')
    <div class="container mt-2">
        <div class="card card-danger card-outline mt-2">
            <!-- Display success message -->
            @if(session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-header mt-1">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <h3 class="card-title text-bold">Notif Harian Unit <span class="badge badge-info ml-1"></span></h3>
            </div>

            <div class="card-body mt-1">
                <h2>List Notif Harian Unit</h2>
                <!-- Form to add new Newprogressreport Document Kind -->
                <form action="{{ route('newprogressreports.store-notif-harian-units') }}" method="POST">
                    @csrf
                    <label for="title">Title:</label><br>
                    <input type="text" id="title" name="title" required><br><br>

                    <label for="documentkind">Select Document Kinds:</label><br>
                    <select id="documentkind" name="documentkind[]" multiple required>
                        @foreach ($documentKinds as $documentKind)
                            <option value="{{ $documentKind->id }}">{{ $documentKind->name }}</option>
                        @endforeach
                    </select><br><br>

                    <label for="telegrammessagesaccount_id">Select Telegram Account:</label><br>
                    <select id="telegrammessagesaccount_id" name="telegrammessagesaccount_id">
                        <option value="">None</option>
                        @foreach ($telegrammessagesaccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account }}</option>
                        @endforeach
                    </select><br><br>

                    <button type="submit">Create Notif Harian Unit</button>
                </form>

                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Document Kinds</th>
                            <th>Telegram Messages Account</th>
                            <th>Actions</th> <!-- New Column -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifHarianUnits as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>{{ $unit->title }}</td>
                                <td>{{ implode(', ', $unit->documentkind_names) }}</td>
                                <td>{{ $unit->telegrammessagesaccount->account ?? 'None' }}</td>
                                <td>
                                    <a href="{{ route('newprogressreports.edit-notif-harian-unit', $unit->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('newprogressreports.delete-notif-harian-unit', $unit->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>
@endsection
