<!-- resources/views/project_types/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Project Types</title>
</head>
<body>
    <h1>Project Types</h1>
    
    <a href="{{ route('project_types.create') }}">Create New Project Type</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <ul>
        @foreach ($projectTypes as $projectType)
            <li>
                {{ $projectType->title }}
                <a href="{{ route('project_types.show', $projectType) }}">View</a>
                <a href="{{ route('project_types.edit', $projectType) }}">Edit</a>
                <!-- <form action="{{ route('project_types.destroy', $projectType) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                </form> -->
            </li>
        @endforeach
    </ul>
</body>
</html>
