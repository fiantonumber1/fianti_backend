@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item active">Reset Password</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Reset Password User</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('auth.resetpassword') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="user_id">Pilih User:</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="" disabled selected>Pilih user yang ingin direset</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger mt-3">Reset Password ke "12345"</button>
                    </form>
                </div>
                @if(session('success'))
                    <div class="card-footer">
                        <div class="alert alert-success mb-0">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
