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
<h1>WhatsApp Groups</h1>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<a href="{{ route('wagroupnumbers.index') }}">Home</a>
<a href="{{ route('wagroupnumbers.create') }}">Add Group</a>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Group Name</th>
            <th>Number</th>
            <th>Verified</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groups as $group)
            <tr>
                <td>{{ $group->id }}</td>
                <td>{{ $group->groupname }}</td>
                <td>{{ $group->number }}</td>
                <td>{{ $group->isverified ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('wagroupnumbers.edit', $group) }}">Edit</a>
                    <form action="{{ route('wagroupnumbers.destroy', $group) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                    @if (!$group->isverified)
                        <form action="{{ route('wagroupnumbers.verify', $group) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Verify</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection