<!-- resources/views/unit/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>View Project Type</title>
</head>
<body>
    <h1>{{ $unit->name }}</h1>
    
    <a href="{{ route('unit.index') }}">Back to List</a>
    <a href="{{ route('unit.edit', $unit) }}">Edit</a>
    <form action="{{ route('unit.destroy', $unit) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
</body>
</html>
