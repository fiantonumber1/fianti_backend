<!-- resources/views/project_types/edit.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Project Type</title>
</head>
<body>
    <h1>Edit Project Type</h1>
    
    <form action="{{ route('project_types.update', $projectType) }}" method="POST">
        @csrf
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="{{ $projectType->title }}" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>
