<!-- resources/views/project_types/edit.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Unit</title>
</head>
<body>
    <h1>Edit Unit</h1>
    
    <form action="{{ route('unit.update', $unit) }}" method="POST">
        @csrf
        <label for="name">Title:</label>
        <input type="text" name="name" id="name" value="{{ $unit->name }}" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>
