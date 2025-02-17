<!-- resources/views/project_types/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>View Project Type</title>
</head>
<body>
    <h1>{{ $projectType->title }}</h1>
    
    <a href="{{ route('project_types.index') }}">Back to List</a>
    <a href="{{ route('project_types.edit', $projectType) }}">Edit</a>
    <form action="{{ route('project_types.destroy', $projectType) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
</body>
</html>
