<h1>Laporan Update Dokumen Expedisi</h1>
<h1>(24 jam terakhir)</h1>
<h3>Periode : {{ $startTime }} - {{ $endTime }}</h3>

<ul>
    @foreach ($documentview as $documentKind => $documents)
        <h2>{{ $documentKind }}</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Expedisi</th>
                    <th>Proyek</th>
                    <th>No Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Revisi</th>
                    <th>DCR</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $document->created_at->format('d-m-Y') }}</td>
                        <td>{{ $document->newProgressReport->newreport->projectType->title }}</td>
                        <td>{{ $document->nodokumen }}</td>
                        <td>{{ $document->namadokumen }}</td>
                        <td>{{ $document->rev }}</td>
                        <td>{{ $document->dcr ?? "" }}</td>
                        <td>{{ $document->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br> <!-- Add some space between tables -->
    @endforeach
</ul>