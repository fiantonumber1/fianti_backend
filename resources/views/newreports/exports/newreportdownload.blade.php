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
            border: 4px solid black;
            /* Add thick black border */
            padding: 20px;
            /* Add some padding to prevent content from touching the border */
            margin: 20px;
            /* Add some margin to prevent the border from touching the edges of the viewport */
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container my-4">
        <table id="example4" class="table table-bordered table-hover">
            <thead>
                <tr>

                    <th scope="col">No</th>
                    <th scope="col">Cabang</th>
                    <th scope="col">Proyek</th>
                    <th scope="col">Unit</th>
                    <th scope="col">No Dokumen</th>
                    <th scope="col">Nama Dokumen</th>
                    <th scope="col">Rev Terakhir</th>
                    <th scope="col">Level</th>
                    <th scope="col">Drafter</th>
                    <th scope="col">Checker</th>
                    <th scope="col">Start</th>
                    <th scope="col">Deadline Release</th>
                    <th scope="col">Realisasi</th>
                    <th scope="col">Jenis Dokumen</th>
                    <th scope="col">Status</th>
                    <th scope="col">Paper Size</th>
                    <th scope="col">Sheet</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $penghitung = 0; // Inisialisasi penghitung
                    $convertervalue = [];
                    foreach ($progressreport as $index => $progressReport) {
                        $penghitung++;
                        $convertervalue[(string) $progressReport->id] = (string) $penghitung;
                    }
                @endphp


                @foreach ($progressreport as $index => $progressReport)
                                <tr>
                                    <td>{{ $convertervalue[(string) $progressReport->id] }}</td>
                                    @php
                                        if ($progressReport->parent_revision_id) {
                                            $nilai = isset($convertervalue[(string) $progressReport->parent_revision_id])
                                                ? $convertervalue[(string) $progressReport->parent_revision_id]
                                                : "Nilai tidak ditemukan";
                                        } else {
                                            $nilai = "Tidak ada dokumen induk";
                                        }
                                    @endphp

                                    <td>{{ $nilai }}</td>
                                    <td id="project_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->projecttype }}</td>
                                    <td id="unit_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->unit }}</td>
                                    <td id="nodokumen_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->nodokumen }}</td>
                                    <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->namadokumen }}</td>
                                    <td id="rev_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->revisiTerakhir }}</td>
                                    <td id="level_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->level}}</td>
                                    <td id="drafter_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->drafter ?? ""}}</td>
                                    <td id="checker_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->checker ?? "" }}</td>
                                    <td id="startreleasedate_{{ $progressReport->id }}_{{ $index }}">
                                        {{ $progressReport->startreleasedate ? \Carbon\Carbon::parse($progressReport->startreleasedate)->format('d-m-Y') : '' }}
                                    </td>
                                    <td id="deadlinerelease_{{ $progressReport->id }}_{{ $index }}">
                                        {{ $progressReport->deadlinereleasedate ? \Carbon\Carbon::parse($progressReport->deadlinereleasedate)->format('d-m-Y') : '' }}
                                    </td>

                                    <td id="realisasi_{{ $progressReport->id }}_{{ $index }}">
                                        {{ \Carbon\Carbon::parse($progressReport->realisasidate)->format('d-m-Y') }}
                                    </td>
                                    <td id="documentkind_{{ $progressReport->id }}_{{ $index }}">
                                        {{ $progressReport->kindofdocument ?? "" }}
                                    </td>

                                    <td id="status_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->status }}</td>
                                    <td id="papersize_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->papersize }}</td>
                                    <td id="sheet_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->sheet }}</td>




                                </tr>
                                @php
                                    $penghitung++;
                                @endphp
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>