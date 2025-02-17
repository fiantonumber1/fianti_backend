<!-- resources/views/unit/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Units</title>
</head>
<body>
    <h1>Units</h1>
    
    <a href="{{ route('unit.create') }}">Create New Unit</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <ul>
        @foreach ($units as $unit)
            <li>
                {{ $unit->name }}
                <a href="{{ route('unit.show', $unit) }}">View</a>
                <a href="{{ route('unit.edit', $unit) }}">Edit</a>
                <!-- <form action="{{ route('unit.destroy', $unit) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                </form> -->
            </li>
        @endforeach
    </ul>
</body>
</html>
