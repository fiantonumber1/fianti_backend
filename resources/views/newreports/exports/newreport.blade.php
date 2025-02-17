<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Report Export</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-responsive- {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .text-xl {
            font-size: 1.25rem;
        }
        body {
            border: 4px solid black; /* Add thick black border */
            padding: 20px; /* Add some padding to prevent content from touching the border */
            margin: 20px; /* Add some margin to prevent the border from touching the edges of the viewport */
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container my-4">
    @php
        $data=$hasil[0]['Exporteddata'];
        $project=$hasil[0]['Project'];
        $unit=$hasil[0]['Unit'];
    @endphp
    <table id="example2" class="table table-bordered table-hover">
        <thead>
            @php
                $total = 0;
                foreach ($data as $week => $row){
                    $total += 1;
                }
                $middleIndex = floor(($total - 1) / 2); // Calculate the middle index
            @endphp

            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    @if ($loop->index == $middleIndex)
                        <th rowspan="3" colspan="2" class="text-center">Progress Teknologi</th>
                    @else
                        <th colspan="2"></th>
                    @endif
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    <th colspan="2"></th>
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    <th colspan="2"></th>
                @endforeach
            </tr>
            <tr>
                <th class="text-center">Proyek: {{$project}}</th>
                @foreach ($data as $week => $row)
                    @if ($loop->last)
                        <th rowspan="2" colspan="2" class="text-center">Gambar Inka</th>
                    @else
                        <th colspan="2"></th>
                    @endif
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    <th colspan="2"></th>
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    @if ($loop->last)
                        <th colspan="2" class="text-center">{{ date('d F Y') }}</th>
                    @else
                        <th colspan="2"></th>
                    @endif
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach ($data as $week => $row)
                    <th colspan="2"></th>  
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <th rowspan="3" class="text-center">Unit</th>
                @php
                    $weekcount=0;
                @endphp
                @foreach ($data as $week => $row)
                    <th colspan="2" class="text-center">Week {{ $weekcount++ }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($data as $row)
                    <td class="text-center">{{ $row['Start Date'] }}</td>
                    <td class="text-center">{{ $row['End Date'] }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($data as $row)
                    <td class="text-center">Target</td>
                    <td class="text-center">Realisasi</td>
                @endforeach
            </tr>
            @foreach ($hasil as $data)
                <tr>
                    <td class="text-center">{{$data['Unit']}}</td> 
                    @foreach ($data['Exporteddata'] as $row1)
                        <td class="text-center">{{ number_format($row1['Total Percentage (Plan)'], 2) }}%</td>
                        <td class="text-center">{{ number_format($row1['Total Percentage (Realisasi)'], 2) }}%</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
