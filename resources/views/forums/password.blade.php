@extends('layouts.split3')

@section('container2')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $forum->topic }}</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('forums.show', $forum->id) }}">
                        <div class="form-group">
                            <label for="password">Enter Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Enter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
