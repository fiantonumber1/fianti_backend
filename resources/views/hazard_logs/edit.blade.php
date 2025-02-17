<!-- resources/views/hazard_logs/edit.blade.php -->
@extends('layouts.table1')

@section('container2')
    <h1>Edit Hazard Log</h1>
    <form action="{{ url('hazard_logs/' . $hazardLog->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="hazard_ref">Hazard Ref</label>
            <input type="text" class="form-control" id="hazard_ref" name="hazard_ref" value="{{ $hazardLog->hazard_ref }}" required>
        </div>
        <!-- Tambahkan input fields untuk setiap kolom, isian awal menggunakan nilai dari $hazardLog -->
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
