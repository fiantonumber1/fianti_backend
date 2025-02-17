<h1>Laporan Keseluruhan Tiap Unit</h1>
<ul>
    @foreach ($unitstatusterakhir as $keyan => $nilaiprojectpic)
        @if($keyan==$unit)
        <h2>{{ $keyan }}</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Nama Memo</th>
                    <th>No Memo</th>
                    <th>Status</th>
                    <th>Status Memo</th>
                    <th>Deadline/Keterlambatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nilaiprojectpic as $docMessage)
                    <tr>
                        <td>{{ $docMessage["namadokumen"] }}</td>
                        <td>{{ $docMessage["nodokumen"] }}</td>
                        <td>{{ $docMessage["status"] }}</td>
                        <td>{{ $docMessage["statusmemo"] }}</td>
                        <td>{{ $docMessage["waktuterlambat"] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach
</ul>

