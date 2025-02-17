@extends('layouts.mail')

@section('container2')
    <title>Mailbox</title>
@endsection

@section('container3')
    <table id="example2" class="table table-bordered table-hover">
        <thead>
            <tr>
                    <th>No</th>
                    <th class="text-center">Nomor Surat</th>
                    <th class="text-center">Nama Dokumen</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Nama Project</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Status Dokumen</th>
                    <th class="text-center">Buka Dokumen</th>
                    <th class="text-center">Sudah Dibaca</th>
                    <th class="text-center">Jenis Notifikasi</th>
                    <th class="text-center">Id Notifikasi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1; // Inisialisasi variabel counter
            @endphp
            @foreach($notifs as $notif)
                @php
                    $statusdibuka = $notif->status;
                    try {
                        $sumberinformasi = json_decode(json_decode($listdatadocumentencode, true)[(int)$notif->dokumen_id], true);
                        $projectpics=$sumberinformasi['projectpics'];
                        $unitpicvalidation=$sumberinformasi['unitpicvalidation'];
                        $MTPRvalidation=$sumberinformasi['MTPRvalidation'];
                        $MTPRsend=$sumberinformasi['MTPRsend'];
                        $PEshare=$sumberinformasi['PEshare'];
                        $PEmanagervalidation=$sumberinformasi['PEmanagervalidation'];
                        $seniormanagervalidation=$sumberinformasi['seniormanagervalidation'];
                        $selfunitvalidation=$sumberinformasi['selfunitvalidation'];
                        $unitvalidation=$sumberinformasi['unitvalidation'];
                        $positionPercentage=$sumberinformasi['positionPercentage'];
                        $datadikirimencoded=$sumberinformasi['datadikirimencoded'];
                        $informasidokumenencoded=$sumberinformasi['informasidokumenencoded'];
                        $document=json_decode($sumberinformasi['document']);
                        $documentnumber=$document->documentnumber;
                        $documentname=$document->documentname;
                        $documentstatus=$document->documentstatus;
                        $userinformations=$document->userinformations;
                        $documentcategory = strtolower($notif->notificationcategory);
                    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                        // Penanganan jika dokumen tidak ditemukan
                        $document = null; // Atau alternatif penanganan lainnya
                    }
                    
                @endphp
                @if($document)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $documentnumber}}</td>
                        <td>
                            <!-- @php
                                $maxCharacters = 50; // Jumlah maksimum karakter sebelum teks dipotong
                                $documentName = $documentname;
                                $shortDocumentName = strlen($documentName) > $maxCharacters ? substr($documentName, 0, $maxCharacters) . '...' : $documentName;
                            @endphp

                            <span class="short-text" data-toggle="tooltip" title="{{ $documentName }}">{{ $shortDocumentName }}</span>
                            @if (strlen($documentName) > $maxCharacters)
                                <button class="btn btn-sm btn-info btn-toggle" data-toggle="collapse" data-target="#longText{{$document->id}}">Selengkapnya</button>
                            @endif

                            <div id="longText{{$document->id}}" class="collapse">
                                {{ $documentName }}
                            </div> -->
                            {{$documentname}}
                        </td>
                        <td>{{ $notif->created_at->format('d-m-Y') }}</td>
                        <td>{{ $notif->nama_project }}</td>
                        <td>  
                            @if($notif->status == "Tolak")
                                <span class="badge badge-danger">Ditolak</span>
                                <p class="mb-0">{{ $notif->alasan }}</p>
                            @elseif($notif->status == "Terima")
                                <span class="badge badge-success">Terima</span>
                            @else
                                <select id="status" name="status" class="form-control">
                                    <option value="Terima">Terima</option>
                                    <option value="Tolak">Tolak</option>
                                </select>
                                <div id="alasan-container" style="display: none;">
                                    <label for="alasan">Alasan Tolak:</label>
                                    <input type="text" id="alasan" name="alasan" class="form-control">
                                </div>
                                <br>
                                <button onclick="submitAnswer('{{ $notif->nama_file }}', '{{ $notif->nama_project }}', '{{ $notif->dokumen_id }}', '{{ $notif->nama_divisi }}', '{{ $documentcategory }}')" class="btn btn-success btn-sm">Submit</button>
                            @endif
                        </td>
                        <td>
                        @if($documentstatus == 'Terbuka')
                            <span class="badge badge-success">{{ $documentstatus }}</span>
                        @else
                            <span class="badge badge-secondary">{{ $documentstatus }}</span>
                        @endif
                        </td>
                        <td>
                            @if($documentcategory=="memo")
                            <a href="{{ route($documentcategory . '.show', ['id' => $notif->dokumen_id, 'rule' => auth()->user()->rule]) }}" class="btn btn-primary btn-sm mr-2">Detail</a>
                            <a href="#" class="btn btn-secondary btn-sm mr-1" onclick="showDocumentSummary('{{$informasidokumenencoded}}','{{$datadikirimencoded}}',{{$document->id}})">Cetak</a>
                            @elseif($documentcategory=="allert1")
                            <a href="{{ route('allert' . '.show', ['idmemo' => $notif->dokumen_id, 'rule' => auth()->user()->rule]) }}" class="btn btn-primary btn-sm mr-2">Detail</a>
                            <a href="#" class="btn btn-secondary btn-sm mr-1" onclick="showDocumentSummary('{{$informasidokumenencoded}}','{{$datadikirimencoded}}',{{$document->id}})">Cetak</a>
                            @elseif($documentcategory=="allert2")
                            <a href="{{ route('allert' . '.show', ['idmemo' => $notif->dokumen_id, 'rule' => auth()->user()->rule]) }}" class="btn btn-primary btn-sm mr-2">Detail</a>
                            <a href="#" class="btn btn-secondary btn-sm mr-1" onclick="showDocumentSummary('{{$informasidokumenencoded}}','{{$datadikirimencoded}}',{{$document->id}})">Cetak</a>
                            @endif
                        </td>
                        <td>
                            @if($statusdibuka == 'read')
                                <span class="text-success" title="Terbuka">
                                    <i class="fas fa-envelope-open"></i>
                                </span>
                            @else
                                <span class="text-info" title="Tertutup">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <button class="btn" style="background-color: 
                                @if($notif->notificationcategory == 'memo') orange;
                                @elseif($notif->notificationcategory == 'allert1') yellow;
                                @elseif($notif->notificationcategory == 'allert2') red;
                                @else white;
                                @endif">
                                {{ ucfirst($notif->notificationcategory) }}
                            </button>
                        </td>
                        <td>{{ $notif->id }}</td>
                    </tr>
                @endif
            @endforeach                  
        </tbody>
    </table>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function showDocumentSummary(informasidokumenencoded, ringkasan, documentId) {
            // Parse JSON data
            var documentData = JSON.parse(ringkasan);
            var documentInfo = JSON.parse(informasidokumenencoded);

            // Construct document information section
            var documentInfoHTML = `
                <div style="text-align: center;">
                    <p style="font-weight: bold; font-size: 24px;">INFORMASI MEMO</p>
                </div>
                <div style="padding: 20px; font-size: 16px;">
                    <p style="font-weight: bold;">Kepada Yth,</p>
                    <ol>
            `;

            // Construct list of PICs
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                documentInfoHTML += `<li>${pic}</li>`;
            }

            // Add closing tags for list
            documentInfoHTML += `
                    </ol>
                    <hr style="margin-top: 20px;">
                    <div style="padding: 20px;">
                        <p style="font-size: 16px;">Kami sampaikan informasi dokumen berikut:</p>
                        <table style="width: 100%; margin-bottom: 20px;">
                            <tr>
                                <td style="font-weight: bold; width: 30%">Jenis Dokumen:</td>
                                <td>${documentInfo['category']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nama Memo:</td>
                                <td>${documentInfo['documentname']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nomor Memo:</td>
                                <td>${documentInfo['documentnumber']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Jenis Memo:</td>
                                <td>${documentInfo['memokind']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Asal Memo:</td>
                                <td>${documentInfo['memoorigin']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Status Dokumen:</td>
                                <td>${documentInfo['documentstatus']}</td>
                            </tr>
                        </table>
                    </div>
                `;

            // Construct table header
            var tableHeaderHTML = `
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 8px;">Pic</th>
                        <th style="border: 1px solid #000; padding: 8px;">Nama Penulis</th>
                        <th style="border: 1px solid #000; padding: 8px;">Email</th>
                        <th style="border: 1px solid #000; padding: 8px;">Status Feedback</th>
                        <th style="border: 1px solid #000; padding: 8px;">Kategori</th>
                        <th style="border: 1px solid #000; padding: 8px;">Sudah Dibaca</th>
                        <th style="border: 1px solid #000; padding: 8px;">Hasil Review</th>
                    </tr>
                </thead>`;

            // Construct table body
            var tableBodyHTML = '<tbody>';
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                var level = documentData[i].level;
                var userInformation = documentData[i].userinformations;

                // Filter out specific conditions
                if ((pic !== "MTPR" || level !== "pembukadokumen") && (pic !== "Product Engineering" || level !== "signature")) {
                    // Construct table row
                    var tableRowHTML = `
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">${pic}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['nama penulis']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['email']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile2']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['sudahdibaca']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['hasilreview']}</td>
                        </tr>`;
                    
                    tableBodyHTML += tableRowHTML;
                }
            }
            tableBodyHTML += '</tbody>';

            // Construct the complete HTML content
            var htmlContent = `
                <div style="padding: 20px;">
                    ${documentInfoHTML}
                    <div style="overflow-x: auto;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 16px;">
                            <caption style="caption-side: top; text-align: center; font-weight: bold; font-size: 20px; margin-bottom: 10px;">Feedback</caption>
                            ${tableHeaderHTML}
                            ${tableBodyHTML}
                        </table>
                    </div>
                </div>
                <img src="{{ asset('images/INKAICON.png') }}" alt="Company Logo" class="company-logo" style="position: absolute; top: 10px; right: 10px; width: 80px; height: 80px; object-fit: contain;">`;

            // Show SweetAlert2 modal with close and print buttons
            Swal.fire({
                html: htmlContent,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, print it!",
                cancelButtonText: "Close",
                width: '90%', // Lebar modal 90%
                padding: '2rem', // Padding konten modal
                customClass: {
                    image: 'img-fluid rounded-circle' // Menggunakan kelas Bootstrap untuk memastikan gambar perusahaan responsif
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Printed!",
                        text: "Your file has been printed.",
                        icon: "success"
                    });
                    printDocumentSummary(documentId);
                }
            });
        }

        function printDocumentSummary(documentId) {
            // Get the URL for the PDF
            var pdfUrl = "{{ url('document/memo') }}/" + documentId + "/pdf";

            // Open the PDF URL in a new window/tab for printing
            window.open(pdfUrl, '_blank');
        }
    
        document.getElementById('status').addEventListener('change', function () {
            var alasanContainer = document.getElementById('alasan-container');
            if (this.value === 'Tolak') {
                alasanContainer.style.display = 'block';
            } else {
                alasanContainer.style.display = 'none';
            }
        });
        
   
        function submitAnswer(namaFile, namaProject, idDocument, namaDivisi,notificationcategory) {
            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah status dokumen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah status!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    var status = document.getElementById('status').value;
                    var alasan = document.getElementById('alasan').value;

                    var url = "{{ url('/mail') }}?namafile=" + encodeURIComponent(namaFile) +
                        "&namaproject=" + encodeURIComponent(namaProject) +
                        "&iddocument=" + encodeURIComponent(idDocument) +
                        "&namadivisi=" + encodeURIComponent(namaDivisi) +
                        "&status=" + encodeURIComponent(status) +
                        "&alasan=" + encodeURIComponent(alasan)+
                        "&notificationcategory=" + encodeURIComponent(notificationcategory);

                    // Example of redirecting:
                    window.location.href = url;
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Status Anda berhasil diubah.",
                        icon: "success"
                    });
                }
            });
            
            
        }
    </script>
@endsection
