<!-- resources/views/project_types/create.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Unit</title>
</head>
<body>
    <h1>Create New Unit</h1>
    
    <form action="{{ route('unit.store') }}" method="POST">
        @csrf
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" required>
        <button type="submit">Create</button>
    </form>
</body>
</html>
