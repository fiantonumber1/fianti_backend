<!-- resources/views/project_types/create.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Project Type</title>
</head>
<body>
    <h1>Create New Project Type</h1>
    
    <form action="{{ route('project_types.store') }}" method="POST">
        @csrf
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <button type="submit">Create</button>
    </form>
</body>
</html>
