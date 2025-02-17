
@extends('layouts.mail')

@section('container2')
    <title>Undangan Tekirim</title>
@endsection


@section('container3')
          <table id="example2" class="table table-bordered table-hover">
                <thead>
                        <tr>
                        <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Nama Surat</th>
                            <th>Status Dokumen</th>
                            <th>Unit</th>
                            <th>Jenis Notifikasi</th>
                            <th>Id Notifikasi</th>
                            <th>Status Unit</th>
                            <th>Alasan Penolakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedData = [];
                        @endphp

                        @foreach($notifs as $notif)
                            @php
                                $idnotifikasi = $notif->id;
                                $Dokumen = NewMemo::findOrFail($notif->iddocument);
                                $documentnumber = $Dokumen->documentnumber;
                                $documentname = $Dokumen->documentname;
                                $documentstatus = $Dokumen->documentstatus;
                                $key = $notif->nama_divisi . '_' . $notif->status; // Membuat kunci unik berdasarkan nama divisi dan status
                                
                            @endphp

                            @if($notif->nama_divisi != "selesai")
                                @if(!isset($groupedData[$documentnumber]))
                                    @php
                                        $groupedData[$documentnumber] = [
                                            'nama_divisis' => [$notif->nama_divisi],
                                            'status' => [$notif->status],
                                            'alasan' => [$notif->alasan],
                                            'idnotifikasi' => [$notif->id],
                                            'jenisnotifikasi' => [$notif->notificationcategory],
                                            'documents' => $documentnumber,
                                            'documentname' => $documentname,
                                            'documentstatus' => $documentstatus
                                        ];
                                    @endphp
                                @else
                                    @php
                                        $groupedData[$documentnumber]['nama_divisis'][] = $notif->nama_divisi;
                                        $groupedData[$documentnumber]['status'][] = $notif->status;
                                        $groupedData[$documentnumber]['alasan'][] = $notif->alasan;
                                        $groupedData[$documentnumber]['idnotifikasi'][] = $notif->id;
                                        $groupedData[$documentnumber]['jenisnotifikasi'][] = $notif->notificationcategory;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                @php
                    $counter = 1; // Inisialisasi variabel counter
                @endphp
                        @foreach($groupedData as $documentnumber => $data)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $data['documents'] }}</td>
                                <td>{{ $data['documentname'] }}</td>
                                <td>{{ $data['documentstatus'] }}</td>
                                <td>
                                    @foreach($data['nama_divisis'] as $index => $nama_divisi)
                                        {{ $nama_divisi }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data['jenisnotifikasi'] as $index => $jenisnotifikasi)
                                        {{ $jenisnotifikasi }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data['idnotifikasi'] as $index => $idnotifikasi)
                                        {{ $idnotifikasi }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data['nama_divisis'] as $index => $nama_divisi)
                                        {{ $data['status'][$index] }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data['nama_divisis'] as $index => $nama_divisi)
                                        {{ $data['alasan'][$index] }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data['idnotifikasi'] as $index => $idnotifikasi)
                                          @if(auth()->user()->rule=="superuser" ||auth()->user()->rule=="Product Engineering")
                                            <div class="col-md-12 text-right">
                                                  <form id="deleteForm" action="{{ route('notifications.destroy', $idnotifikasi) }}" method="POST">
                                                      @csrf
                                                      @method('PUT')
                                                      <button type="submit" class="btn btn-secondary btn-sm">Hapus
                                                      </button>
                                                  </form>
                                              </div>
                                          @endif
                                        <br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
    <!-- Modal -->
    <div class="modal fade" id="documentSummaryModal" tabindex="-1" role="dialog" aria-labelledby="documentSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentSummaryModalLabel">Ringkasan Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="documentSummaryContent"></div> <!-- Konten untuk dicetak -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printDocumentSummary()">Cetak</button> <!-- Tombol cetak -->
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Add any custom styles here */
        .badge {
            font-size: 0.8rem;
            margin-right: 5px;
        }
    </style>
    <!-- Include the existing styles and scripts from the original code -->

    <style>
        /* Add any custom styles here */
        .badge {
            font-size: 0.8rem;
            margin-right: 5px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var countupElements = document.querySelectorAll('.countup');

            countupElements.forEach(function (element) {
                var startTime = new Date(element.getAttribute('data-time')).getTime();

                function updateCountup() {
                    var currentTime = new Date().getTime();
                    var timeDifference = currentTime - startTime;

                    var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);
                    var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                    var hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

                    element.textContent = days + " hari, " + hours + " jam, " + minutes + " menit, " + seconds + " detik";
                }

                updateCountup();
                setInterval(updateCountup, 1000);
            });

            countupElements[0].style.display = 'inline';
        });


        document.getElementById('status').addEventListener('change', function () {
            var alasanContainer = document.getElementById('alasan-container');
            if (this.value === 'Tolak') {
                alasanContainer.style.display = 'block';
            } else {
                alasanContainer.style.display = 'none';
            }
        });
    </script>
@endsection
