<table>
    <thead>
        <tr>
            <th>Proyek: {{ $projectName }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Dokumen</th>
            <th>No Dokumen</th>
            <th>Status</th>
            <th>Komat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($newmemos as $newmemo)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $newmemo->documentname }}</td>
                <td>{{ $newmemo->documentnumber }}</td>
                <td>{{ $newmemo->documentstatus }}</td>
                <td>
                    @foreach($newmemo->komats as $komat)
                        {{ $komat->kodematerial }} - {{ $komat->material }}
                        @if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>