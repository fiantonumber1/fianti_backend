@extends('layouts.main')

@section('container2')
<div class="container">
    <h1>Zoom Details</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('zoom.update', $zoomaccess->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="account_name">Account Name:</label>
                    <input type="text" id="account_name" name="account_name" class="form-control" value="{{ $zoomaccess->account_name }}">
                </div>
                <div class="form-group">
                    <label for="zoom_clientid">Zoom Client ID:</label>
                    <input type="text" id="zoom_clientid" name="zoom_clientid" class="form-control" value="{{ $zoomaccess->zoom_clientid }}">
                </div>
                <div class="form-group">
                    <label for="zoom_clientsecret">Zoom Client Secret:</label>
                    <input type="text" id="zoom_clientsecret" name="zoom_clientsecret" class="form-control" value="{{ $zoomaccess->zoom_clientsecret }}">
                </div>
                <div class="form-group">
                    <label for="zoom_redirecturl">Zoom Redirect URL:</label>
                    <input type="url" id="zoom_redirecturl" name="zoom_redirecturl" class="form-control" value="{{ $zoomaccess->zoom_redirecturl }}">
                </div>
                <div class="form-group">
                    <label for="zoom_hotkey">Zoom Hotkey:</label>
                    <input type="text" id="zoom_hotkey" name="zoom_hotkey" class="form-control" value="{{ $zoomaccess->zoom_hotkey ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="jenis">Jenis Zoom:</label>
                    <input type="text" id="jenis" name="jenis" class="form-control" value="{{ $zoomaccess->jenis ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="account_expired">Account Expired:</label>
                    <input type="datetime-local" id="account_expired" name="account_expired" class="form-control" value="{{ $zoomaccess->account_expired ? \Carbon\Carbon::parse($zoomaccess->account_expired)->format('Y-m-d\TH:i') : '' }}">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update</button>
            </form>
            <a href="{{ route('zoom.index') }}" class="btn btn-secondary mt-3">Back to List</a>
        </div>
    </div>
</div>
@endsection
