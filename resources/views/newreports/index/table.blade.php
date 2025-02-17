<table id="example2-{{ $keyan }}" class="table table-bordered table-hover table-striped">
    <thead>
        <tr>
            <th>
                <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
            </th>
            <th>No</th>
            <th scope="col">Unit</th>
            <th scope="col">Nama Proyek</th>
            <th scope="col" style="width: 15%; text-align: center;">Persentase</th>
            <th scope="col">Jumlah Dokumen</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $counterdokumen = 1; // Inisialisasi variabel counter

        @endphp
        @foreach ($newreports as $newreport)
                @php
                    $key = key($newreports);
                @endphp
                <tr>
                    <td>
                        <div class="icheck-primary">
                            <!-- Tambahkan name dan ID unik -->
                            <input type="checkbox" value="{{ $newreport->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                            <label for="checkbox{{ $key }}"></label>
                        </div>
                    </td>
                    <td>{{ $counterdokumen++ }}</td>
                    <td>{{ $newreport->unit }}</td>
                    <td>{{ $newreport->proyek_type }}</td>

                    <td style="width: 15%; text-align: center;" class="p-1">
                        @php
                            if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                                $totalpersentaseeksternal = number_format(100, 2);
                            } else {
                                $totalpersentaseeksternal = number_format($newreport->seniorpercentage, 2);
                            }
                            $totalpersentaseinternal = number_format($newreport->seniorpercentage, 2);
                        @endphp
                        @if(session('internalon'))
                            <span class="badge badge-warning" style="font-size: 2rem;">{{ $totalpersentaseinternal}} %</span>
                        @else
                            <span class="badge badge-success d-1" style="font-size: 2rem;">{{ $totalpersentaseeksternal}} %</span>
                            <span class="badge badge-warning d-none" style="font-size: 2rem;">{{ $totalpersentaseinternal}} %</span>
                        @endif
                    </td>
                    <td>
                        <style>
                            .badge-fffd19 {
                                background-color: #fffd19;
                                /* Warna latar belakang khusus */
                                color: #000;
                                /* Warna teks */
                            }
                        </style>
                        <span class="badge badge-danger" style="font-size: 1.5rem;">Total Dokumen:
                            {{ $newreport->documentcount }}</span>
                        <span class="badge badge-fffd19" style="font-size: 1.5rem;">Dokumen Release:
                            {{ $newreport->release }}</span>
                    </td>
                    <td>
                        <a href="{{ route('newreports.show', $newreport->id) }}" class="btn btn-primary">View</a>
                        <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-default bg-purple"
                                onclick="return confirm('Are you sure?')">Download</button>
                        </form>

                        @if (auth()->user()->rule == "superuser" || auth()->user()->rule == "MTPR" || auth()->user()->id != 202)
                            <form action="{{ route('newreports.destroy', $newreport->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        @endif
                        @if (auth()->user()->name == "Dian Pertiwi")
                            <form action="{{ route('newreports.destroydian', $newreport->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-default bg-pink"
                                    onclick="return confirm('Are you sure?')">Hapus Rencana saja (Khusus Dian)</button>
                            </form>
                        @endif

                        @if (auth()->user()->rule == "superuser" || auth()->user()->rule == "MTPR")
                            <a href="{{ route('newreports.doubledetector', $newreport->id) }}"
                                class="btn btn-default bg-kakhi">double Detector: {{$newreport->doubledetectorcount()}}</a>

                        @endif
                    </td>
                </tr>
        @endforeach
    </tbody>
</table>