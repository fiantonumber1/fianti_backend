<h1>Laporan Keseluruhan {{ $unit }}</h1>
<ul>
    <h2>Memo</h2>
    <!-- <p>Ranking (Kecepatan): {{  $unitstatusterakhir["rank"] }} dari  {{  $unitstatusterakhir["unitcount"] }} unit</p> -->
    <p>Rata-rata waktu: {{ $unitstatusterakhir['leadtimeaverage'] }} jam / memo</p>
    <p>Total Memo terselesaikan: {{ $unitstatusterakhir['memocount'] }}</p>

    <p>List Memo yang perlu diselesaikan:</p>
    @if(count($unitstatusterakhir['document']) > 0)
        <table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Nama Memo</th>
                    <th>No Memo</th>
                    <th>Status</th>
                    <th>Status Memo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unitstatusterakhir['document'] as $docMessage)
                    <tr>
                        <td>{{ $docMessage->documentname }}</td>
                        <td>{{ $docMessage->documentnumber }}</td>
                        <td>{{ $docMessage->documentstatus }}</td>
                        <td>{{ $docMessage->statusunit ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada memo yang perlu diselesaikan.</p>
    @endif


    <h2>Tanggungan Checker (bagi unit yang sudah berpartisipasi)</h2>
    @if(count($jobtickets['checker']) > 0)
        <table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jumlah Tidak Terselesaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobtickets['checker'] as $checker)
                    <tr>
                        <td>{{ $checker['name'] }}</td>
                        <td>{{ $checker['unfinished_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada tanggungan untuk checker.</p>
    @endif


    <h2>Tanggungan Approver (bagi unit yang sudah berpartisipasi)</h2>
    @if(count($jobtickets['approver']) > 0)
        <table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Nama Approver</th>
                    <th>Jumlah Tidak Terselesaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobtickets['approver'] as $approver)
                    <tr>
                        <td>{{ $approver['name'] ?? '' }}</td>
                        <td>{{ $approver['unfinished_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada tanggungan untuk approver.</p>
    @endif
</ul>