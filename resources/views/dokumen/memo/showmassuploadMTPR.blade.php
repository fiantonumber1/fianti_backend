<!-- view-excel.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>View Excel</title>
</head>
<body>
    <h1>Data from Excel</h1>
    <table border="1">
        <tr>
            <th>Component</th>
            <th>Supplier</th>
            <th>Memo No</th>
            <!-- Sesuaikan dengan kolom-kolom Excel Anda -->
        </tr>
        @foreach($excelData as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td>{{ $row[1] }}</td>
                <td>{{ $row[2] }}</td>
                <!-- Sesuaikan dengan posisi kolom dalam file Excel Anda -->
            </tr>
        @endforeach
    </table>
</body>
</html>
