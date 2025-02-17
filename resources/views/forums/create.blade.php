{{-- resources/views/forums/create.blade.php --}}

@extends('layouts.main')

@section('container1')
<div class="container">
    
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Buat Forum</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('forums.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="topic">Topic</label>
                        <input type="text" name="topic" class="form-control" id="topic" placeholder="Enter topic" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" id="description" placeholder="Enter description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection





