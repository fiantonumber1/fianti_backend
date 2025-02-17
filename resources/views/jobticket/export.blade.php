<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold;">Laporan WLA {{ $name }} ({{ $kind }})</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">
                Periode: {{ $startDate }} s/d {{ $endDate }}
            </th>
        </tr>
        <tr>
            <th>ID</th>
            <th>No Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Rev</th>
            <th>Status</th>
            <th>Dokumen Ditutup</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach ($docLists as $doc)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $doc->jobticketIdentity->documentnumber }}</td>
                <td>{{ $doc->documentname }}</td>
                <td>{{ $doc->rev }}</td>
                <td>{{ $doc->status }}</td>
                <td>{{ $doc->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>