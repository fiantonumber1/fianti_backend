<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Monitoring pages based on uploaded BOM-based Memo.</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                        
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example2-{{ $keyan }}" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Komponen</th>
                                <th>Supplier</th>
                                <th>Deadline</th>
                                <th>Nomor Dokumen</th>
                                <th>Nama Dokumen</th>
                                <th>PIC</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedKomats = []; // Variabel untuk menyimpan komats yang telah dikelompokkan berdasarkan kode material
                                $penghitung = 0;

                                if($keyan != 'All'){
                                    $documents = $revisi['documents'];
                                }

                                foreach ($documents as $document) {
                                    $komats = json_decode(json_decode($document->remaininformation)->komat);

                                    foreach ($komats as $komat) {
                                        $kodematerial = json_decode($komat)->kodematerial;
                                        $supplier = json_decode($komat)->supplier;
                                        $komponen = json_decode($komat)->komponen;

                                        if (!isset($groupedKomats[$kodematerial])) {
                                            $groupedKomats[$kodematerial] = [];
                                        }

                                        $groupedKomats[$kodematerial][] = [
                                            'supplier' => $supplier,
                                            'komponen' => $komponen,
                                            'documentnumber' => $document->documentnumber,
                                            'documentname' => $document->documentname,
                                            'documentid' => $document->id,
                                        ];
                                    }
                                }
                            @endphp

                            @php
                                $counter = 1; // Inisialisasi variabel counter
                            @endphp

                            @foreach ($groupedKomats as $kodematerial => $komats)
                                @foreach ($komats as $index => $komat)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $kodematerial }}</td>
                                        <td>{{ $komat['komponen'] }}</td>
                                        <td>{{ $komat['supplier'] }}</td>
                                        <td>
                                            @php
                                                $sumberinformasi = json_decode(json_decode($listdatadocumentencode,true)[$komat['documentid']],true);
                                                $document = json_decode($sumberinformasi['document']);
                                                $date = \Carbon\Carbon::parse(json_decode($document->timeline,true)["documentopened"]);
                                                $date = $date->addDays(3);
                                                echo $date->format('d/m/Y');     
                                            @endphp
                                        </td>
                                        <td>{{ $komat['documentnumber'] }}</td>
                                        <td>{{ $komat['documentname'] }}</td>
                                        <td>
                                            @php                    
                                                $sumberinformasi = json_decode(json_decode($listdatadocumentencode,true)[$komat['documentid']],true);
                                                $projectpics = $sumberinformasi['projectpics'];
                                                $unitpicvalidation = $sumberinformasi['unitpicvalidation'];
                                                $MTPRvalidation = $sumberinformasi['MTPRvalidation'];
                                                $MTPRsend = $sumberinformasi['MTPRsend'];
                                                $PEshare = $sumberinformasi['PEshare'];
                                                $PEmanagervalidation = $sumberinformasi['PEmanagervalidation'];
                                                $seniormanagervalidation = $sumberinformasi['seniormanagervalidation'];
                                                $selfunitvalidation = $sumberinformasi['selfunitvalidation'];
                                                $unitvalidation = $sumberinformasi['unitvalidation'];
                                                $positionPercentage = $sumberinformasi['positionPercentage'];
                                                $datadikirimencoded = $sumberinformasi['datadikirimencoded'];
                                                $informasidokumenencoded = $sumberinformasi['informasidokumenencoded'];
                                                $document = json_decode($sumberinformasi['document']);
                                            @endphp
                                            @if (!empty($projectpics))
                                                @foreach($projectpics as $projectpic)
                                                    <a class="dropdown-item" href="#">{{ $unitsingkatan[$projectpic] }}</a>
                                                @endforeach
                                            @else
                                                <p>Tidak ada data unit</p>
                                            @endif
                                        </td>
                                        <td class="project_progress">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-{{ $positionPercentage == 100 ? 'success' : 'warning' }}" role="progressbar" aria-valuenow="{{ $positionPercentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $positionPercentage }}%">
                                                </div>
                                            </div>
                                            <small>
                                                {{ $positionPercentage }}% Complete
                                            </small>
                                        </td>
                                        <style>
                                            .document-status-button {
                                                padding: 2px 5px;
                                                border-radius: 3px;
                                                font-size: 14px;
                                            }
                                            .document-status-button-open {
                                                background-color: #dc3545;
                                                color: #fff;
                                            }
                                            .document-status-button-closed {
                                                background-color: #28a745;
                                                color: #fff;
                                            }
                                        </style>
                                        <td>
                                            <button type="button" class="btn document-status-button document-status-button-{{ $document->documentstatus == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success' }}" title="{{ $document->documentstatus }}" onclick="toggleDocumentStatus(this)" data-document-status="{{ $document->documentstatus }}" data-document-id="{{ $document->id }}">
                                                <i class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                                                <span>{{ $document->documentstatus }}</span>
                                            </button>
                                        </td>
                                        <td class="project-actions text-right">
                                            <div class="col-md-12 text-right column-layout">
                                                <a class="btn btn-primary btn-sm" href="{{ route('memo.show', ['id' => $document->id, 'rule' => auth()->user()->rule]) }}" style="width: 100px;">
                                                    <i class="fas fa-folder"></i> Detail
                                                </a>
                                            </div>
                                            <div class="col-md-12 text-right column-layout">
                                                <a href="#" class="btn btn-success btn-sm" onclick="showDocumentSummary('{{ $informasidokumenencoded }}', '{{ $datadikirimencoded }}', {{ $document->id }})" style="width: 100px;">
                                                    <i class="fas fa-print"></i> View
                                                </a>
                                            </div>
                                            <div class="col-md-12 text-right column-layout">
                                                <a class="btn btn-info btn-sm" href="{{ route('document.report', ['id' => $document->id, 'rule' => auth()->user()->rule]) }}" style="width: 100px;">
                                                    <i class="fas fa-chart-line"></i> Progress
                                                </a>
                                            </div>
                                            @if(auth()->user()->rule == "superuser")
                                                <div class="col-md-12 text-right column-layout">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $document->id }}')" style="width: 100px;">
                                                        <i class="fas fa-eraser"></i> Hapus
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>

                    </table>
                    
                </div>


                <!-- /.card-body -->
                </div>
                <!-- /.card -->
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section> 