@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')  
<h1>{{ isset($wagroupnumber) ? 'Edit Group' : 'Add Group' }}</h1>

<form
    action="{{ isset($wagroupnumber) ? route('wagroupnumbers.update', $wagroupnumber) : route('wagroupnumbers.store') }}"
    method="POST">
    @csrf
    @if (isset($wagroupnumber))
        @method('PUT')
    @endif

    <label for="groupname">Group Name</label>
    <input type="text" name="groupname" id="groupname" value="{{ $wagroupnumber->groupname ?? '' }}" required>

    <label for="number">Number</label>
    <input type="text" name="number" id="number" value="{{ $wagroupnumber->number ?? '' }}" required>

    <button type="submit">{{ isset($wagroupnumber) ? 'Update' : 'Save' }}</button>
</form>
@endsection