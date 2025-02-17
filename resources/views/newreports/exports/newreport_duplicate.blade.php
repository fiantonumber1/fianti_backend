<!DOCTYPE html>
<html>
<head>
    <title>New Report Duplicate Export</title>
    <style>
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <td>Project</td>
                <td colspan="{{ max(count($informasi), 1) }}" class="centered">{{ $informasi[0]['Project'] }}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Unit</td>
                @foreach($informasi as $info)
                    <td>{{ $info['Unit'] }}</td>
                @endforeach
            </tr>
            @for ($i = 0; $i < max(array_map('count', array_column($informasi, 'Duplicate'))); $i++)
                <tr>
                    <td>Duplicate {{$i + 1}}</td>
                    @foreach($informasi as $info)
                        <td>{{ $info['Duplicate'][$i] ?? '' }}</td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
