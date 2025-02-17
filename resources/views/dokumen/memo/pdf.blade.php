<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Memo</title>
    <style>
        @page {
            size: landscape;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #000;
            padding: 12px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    @php
        $sumberinformasi= json_decode(json_decode($listdatadocumentencode,true)[$document->id],true);
        $datadikirimencoded = $sumberinformasi['datadikirimencoded'];
        $informasidokumenencoded = $sumberinformasi['informasidokumenencoded'];
        $document = json_decode($sumberinformasi['document']);
        
        // Parse JSON data
        $documentData = json_decode($datadikirimencoded, true);
        $documentInfo = json_decode($informasidokumenencoded, true);
    @endphp

    <div style="text-align: center;">
        <p style="font-weight: bold; font-size: 48px;">INFORMASI MEMO</p>
    </div>

    <div style="padding: 20px; font-size: 18px;">
        <p style="font-weight: bold;">Kepada Yth,</p>
        <ol>
            @foreach($documentData as $data)
                <li>{{ $data['pic'] }}</li>
            @endforeach
        </ol>
        
        <hr style="margin-top: 20px;">
        
        <div style="padding: 20px;">
            <p style="font-size: 18px;">Kami sampaikan informasi dokumen berikut:</p>
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="font-weight: bold; width: 30%">Jenis Dokumen:</td>
                    <td>{{ $documentInfo['category'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Nama Memo:</td>
                    <td>{{ $documentInfo['documentname'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Nomor Memo:</td>
                    <td>{{ $documentInfo['documentnumber'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Jenis Memo:</td>
                    <td>{{ $documentInfo['memokind'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Asal Memo:</td>
                    <td>{{ $documentInfo['memoorigin'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Status Dokumen:</td>
                    <td>{{ $documentInfo['documentstatus'] }}</td>
                </tr>
            </table>
        </div>

        <div style="overflow-x: auto;">
            <table style="border-collapse: collapse; width: 100%; font-size: 18px;">
                <caption style="caption-side: top; text-align: center; font-weight: bold; font-size: 24px; margin-bottom: 10px;">Feedback</caption>
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 12px;">Pic</th>
                        <th style="border: 1px solid #000; padding: 12px;">Nama Penulis</th>
                        <th style="border: 1px solid #000; padding: 12px;">Email</th>
                        <th style="border: 1px solid #000; padding: 12px;">Status Feedback</th>
                        <th style="border: 1px solid #000; padding: 12px;">Kategori</th>
                        <th style="border: 1px solid #000; padding: 12px;">Sudah Dibaca</th>
                        <th style="border: 1px solid #000; padding: 12px;">Hasil Review</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentData as $data)
                        @if(($data['pic'] !== "MTPR" || $data['level'] !== "pembukadokumen") && ($data['pic'] !== "Product Engineering" || $data['level'] !== "signature"))
                            <tr>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['pic'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['nama penulis'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['email'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['conditionoffile'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['conditionoffile2'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['sudahdibaca'] }}</td>
                                <td style="border: 1px solid #000; padding: 12px;">{{ $data['userinformations']['hasilreview'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
