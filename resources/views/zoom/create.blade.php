@extends('layouts.main')

@section('container2')
<div class="container">
    <h1>Create Zoom Access Token</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('zoom.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="account_name">Account Name:</label>
                    <input type="text" id="account_name" name="account_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="zoom_clientid">Zoom Client ID:</label>
                    <input type="text" id="zoom_clientid" name="zoom_clientid" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="zoom_clientsecret">Zoom Client Secret:</label>
                    <input type="text" id="zoom_clientsecret" name="zoom_clientsecret" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="zoom_redirecturl">Zoom Redirect URL:</label>
                    <input type="text" id="zoom_redirecturl" name="zoom_redirecturl" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="zoom_hotkey">Zoom Hotkey:</label>
                    <input type="text" id="zoom_hotkey" name="zoom_hotkey" class="form-control">
                </div>
                <div class="form-group">
                    <label for="jenis">Jenis:</label>
                    <input type="text" id="jenis" name="jenis" class="form-control">
                </div>
                <div class="form-group">
                    <label for="account_expired">Account Expired:</label>
                    <input type="datetime-local" id="account_expired" name="account_expired" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Create</button>
            </form>
            <a href="{{ route('zoom.index') }}" class="btn btn-secondary mt-3">Back to List</a>
        </div>
    </div>
</div>
@endsection
