@extends('layouts.main')

@section('container1')
<div class="container">
    <a href="{{ route('events.all') }}" class="btn btn-primary">Kembali ke jadwal</a>
    <div class="card mt-3">
        <div class="card-header">
            <p><strong>Judul Rapat:</strong> {{ $event->title }}</p>
        </div>
        <div class="card-body">

            <p><strong>Penanggung Jawab:</strong> {{ $event->pic }}</p>
            <p><strong>Agenda:</strong> {{ $event->agenda_desc }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $event->start }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $event->end }}</p>
            <p><strong>Ruangan:</strong> {{ $event->room }}</p>
            <p><strong>Detail Zoom (Jika Ada):</strong>
                @if($event->children)
                    <ul>
                        @foreach($event->children as $child)
                            <li>
                                <a href="{{ route('events.show', $child->id) }}">Klik</a>
                            </li>
                        @endforeach
                    </ul>

                @endif
            @if($event->join_url)
                <p>
                    <a href="{{ $event->join_url }}" target="_blank">{{ $event->join_url }}</a>
                </p>
            @endif
            </p>
            <p><strong>Unit Terlibat:</strong> {{ $event->convertListToString() }}</p>
            <!-- <p><strong>Unit Terlibat:</strong>
                <ul>
                    @foreach(json_decode($event->agenda_unit) as $unit)
                        <li>{{ $unit }}</li>
                    @endforeach
                </ul>
            </p> -->
            <!-- Delete Button -->
            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus acara ini?');">
                @csrf
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>

            <!-- <form action="{{ route('events.edit', $event->id) }}" method="GET" onsubmit="return confirm('Apakah Anda yakin ingin mengedit?');">
                <button type="submit" class="btn btn-success">Edit</button>
            </form> -->
        </div>

    </div>
</div>
@endsection