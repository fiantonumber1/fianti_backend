<h1>Laporan Keseluruhan</h1>
<ul>
    <h2>Memo</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nama Project</th>
                <th>Memo Terbuka (satuan)</th>
                <th>Memo Tertutup (satuan)</th>
                <th>Memo Terbuka (%)</th>
                <th>Memo Tertutup (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revisiall as $keyan => $revisi)
            <tr>
                <td>{{ $keyan }}</td>
                <td>{{ number_format($revisi['jumlah']['terbuka'], 2)  }}</td>
                <td>{{ number_format($revisi['jumlah']['tertutup'], 2) }}</td>
                <td>{{ number_format($revisi['persentase']['terbuka'], 2) }}%</td>
                <td>{{ number_format($revisi['persentase']['tertutup'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>BOM</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nama Project</th>
                <th>Komponen Terselesaikan (satuan)</th>
                <th>Komponen Belum Terselesaikan  (satuan)</th>
                <th>Komponen Terselesaikan (%)</th>
                <th>Komponen Belum Terselesaikan  (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revisiall as $keyan => $revisi)
            <tr>
                <td>{{ $keyan }}</td>
                <td>{{ $revisi['jumlahbom']['terselesaikan'] }}</td>
                <td>{{ $revisi['jumlahbom']['tidak terselesaikan'] }}</td>
                <td>{{ number_format($revisi['persentasebom']['terselesaikan'],2) }}%</td>
                <td>{{ number_format($revisi['persentasebom']['tidak terselesaikan'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @foreach ($unitstatusterakhir as $keyan => $nilaiprojectpic)
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
        
    @endforeach
      
    <h2>{{ $judul }}</h2>
    <h2>Memo belum selesai berjumlah : {{ count($documentterlambat) }}</h2>
    @foreach($documentterlambat as $docMessage)
        <li>{{ $docMessage }}</li>
    @endforeach


    
</ul>

