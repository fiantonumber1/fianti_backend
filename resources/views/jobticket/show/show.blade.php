@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.show', ['id'=>$jobticketpart->id]) }}">List Dokumen</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')  
<div align="center">
<div class="col-9">

    <!-- <div class="container mt-0" style="padding: 40px 60px 1px 1px;"> -->
        <div class="card card-outline card-danger">
            

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="true">Jobticket</a>
                    </li>
                   
                    
                </ul>

                <div class="tab-content" id="myTabContent">
                    <!-- Progress Tab Content -->
                    <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                        
                        <div class="row">
                            <div class="col-12">
                                
                                    <div class="card-header">
                                        <table class="table table-bordered my-2 table-responsive-">
                                            <tbody>
                                                <tr>
                                                    <td rowspan="7" style="width: 25%" class="text-center">
                                                        <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2" style="max-width: 250px">
                                                    </td>
                                                    <td rowspan="7" style="width: 50%">
                                                        <h1 class="text-xl text-center mt-2">List Dokumen</h1>
                                                    </td>
                                                    <td style="width: 25%" class="p-1">Project: <b>{{ ucwords(str_replace('-', ' ', $jobticketpart->projectType->title)) }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 25%" class="p-1">Bagian: <b>{{ ucfirst($jobticketpart->unit->name) }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="card-header">
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        
                                        <ul class="nav nav-tabs" id="documentTab" role="tablist">
                                            @foreach ($groupedDocuments as $documentKind => $documents)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ Str::slug($documentKind) }}-tab" data-toggle="tab" href="#{{ Str::slug($documentKind) }}" role="tab" aria-controls="{{ Str::slug($documentKind) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                        {{ $documentKind }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <div class="tab-content" id="documentTabContent">
                                        
                                            @php
                                                $loopIndex = 0;  // Inisialisasi variabel loop
                                            @endphp

                                            @foreach ($groupedDocuments as $documentKind => $documents)
                                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ Str::slug($documentKind) }}" role="tabpanel" aria-labelledby="{{ Str::slug($documentKind) }}-tab">
                                                    <table id="example2-{{ $loopIndex }}" class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>No Dokumen</th>
                                                                <th>Nama Dokumen</th>
                                                                <th>Posisi Rev Terakhir</th>
                                                                <th>Status</th>
                                                                <th>Edit</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($documents as $index => $progressReport)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $progressReport->documentnumber }}</td>
                                                                    <td>{{ $progressReport->jobticketterakhir->documentname ?? 'No Document' }}</td>
                                                                    <td class="project-actionkus text-right">
                                                                        <style>
                                                                            body {
                                                                                font-family: Arial, sans-serif;
                                                                                margin: 0;
                                                                                padding: 0;
                                                                                background-color: #f0f2f5; /* Warna latar belakang yang lembut */
                                                                            }
                                                                            .project-actionkus {
                                                                                display: flex;
                                                                                align-items: center;
                                                                                justify-content: center;
                                                                                padding: 20px;
                                                                            }
                                                                            .action-group {
                                                                                display: flex;
                                                                                align-items: center;
                                                                                margin: 0 10px;
                                                                            }
                                                                            .arrow {
                                                                                margin: 0 5px;
                                                                                font-size: 24px;
                                                                                color: #00b0ff; /* Warna biru yang futuristik */
                                                                            }
                                                                            .container {
                                                                                display: flex;
                                                                                align-items: center;
                                                                            }
                                                                            .boxblue {
                                                                                margin-right: 5px;
                                                                                border: 1px solid #00b0ff;
                                                                                border-radius: 10px;
                                                                                padding: 10px; /* Tambahkan sedikit padding */
                                                                                background-color: #e1f5fe; /* Warna biru muda */
                                                                                box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
                                                                            }
                                                                            .box {
                                                                                margin-right: 5px;
                                                                                border: 1px solid #ccc;
                                                                                border-radius: 10px;
                                                                                padding: 10px; /* Tambahkan sedikit padding */
                                                                                background-color: #ffffff;
                                                                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                                                            }
                                                                            h2 {
                                                                                font-size: 20px;
                                                                                margin-bottom: 10px;
                                                                                color: #333;
                                                                            }
                                                                            ul {
                                                                                list-style-type: none;
                                                                                margin: 0;
                                                                                padding: 0;
                                                                            }
                                                                            li {
                                                                                margin-bottom: 10px;
                                                                            }
                                                                            .keterangan {
                                                                                margin-left: 5px;
                                                                                font-size: 16px;
                                                                                color: #555;
                                                                            }
                                                                            .indicator {
                                                                                width: 20px;
                                                                                height: 20px;
                                                                                border-radius: 50%;
                                                                                margin-right: 5px;
                                                                            }
                                                                            .green {
                                                                                background-color: #4caf50; /* Warna hijau */
                                                                            }
                                                                            .red {
                                                                                background-color: #f44336; /* Warna merah */
                                                                            }
                                                                            .yellow {
                                                                                background-color: #ffeb3b; /* Warna kuning */
                                                                            }
                                                                            .blue {
                                                                                background-color: #2196f3; /* Warna biru */
                                                                            }
                                                                            .orange {
                                                                                background-color: #ff9800; /* Warna orange */
                                                                            }
                                                                            .black {
                                                                                background-color: #212121; /* Warna hitam */
                                                                            }
                                                                        </style>
                                                                        
                                                                        @php
                                                                            $classbox1 = "boxblue";
                                                                            
                                                                            // Menggunakan null coalescing untuk jobticket_started_id atau menampilkan "Nonaktif"
                                                                            $drafterstatus = isset($progressReport->allrule->drafter_status) ? $progressReport->allrule->drafter_status : 'Not Approve';

                                                                            // Menggunakan null coalescing untuk checker_status atau menampilkan "Nonaktif"
                                                                            $checkerstatus = isset($progressReport->allrule->checker_status) ? $progressReport->allrule->checker_status : 'Not Approve';

                                                                            // Menggunakan null coalescing untuk approver_status atau menampilkan "Nonaktif"
                                                                            $approverstatus = isset($progressReport->allrule->approver_status) ? $progressReport->allrule->approver_status : 'Not Approve';
                                                                        @endphp


                                                                        <a class="{{ $classbox1 }}" href="#">
                                                                            <div class="container">
                                                                                <div class="indicator 
                                                                                    {{ $drafterstatus == 'Approve' ||  ($progressReport->lastthreedocument->last() && $progressReport->lastthreedocument->last()->status === 'closed') ? 'green' 
                                                                                    : ($drafterstatus == 'Ongoing' ? 'orange' 
                                                                                    : ($drafterstatus == 'Belum dibaca' ? 'yellow' 
                                                                                    : 'red')) }}" 
                                                                                    title="{{ $drafterstatus == 'Approve' ? 'Drafter telah upload dokumen' 
                                                                                    : ($drafterstatus == 'Ongoing' ? 'Drafter sudah menyelesaikan dokumen tetapi belum upload dokumen' 
                                                                                    : ($drafterstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' 
                                                                                    : 'Belum ada tindakan yang bisa diambil')) }}">
                                                                                </div>
                                                                                <span class="keterangan">Drafter</span>
                                                                            </div>
                                                                        </a>

                                                                        <span class="arrow">→</span>

                                                                        <a class="{{ $classbox1 }}" href="#">
                                                                            <div class="container">
                                                                                <div class="indicator 
                                                                                    {{ $checkerstatus == 'Approve' ||  ($progressReport->lastthreedocument->last() && $progressReport->lastthreedocument->last()->status === 'closed')? 'green' 
                                                                                    : ($checkerstatus == 'Ongoing' ? 'orange' 
                                                                                    : ($checkerstatus == 'Belum dibaca' ? 'yellow' 
                                                                                    : 'red')) }}" 
                                                                                    title="{{ $checkerstatus == 'Approve' ? 'Checker telah menyetujui' 
                                                                                    : ($checkerstatus == 'Ongoing' ? 'Checker belum menyetujui' 
                                                                                    : ($checkerstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' 
                                                                                    : 'Belum ada tindakan yang bisa diambil')) }}">
                                                                                </div>
                                                                                <span class="keterangan">Checker</span>
                                                                            </div>
                                                                        </a>

                                                                        <span class="arrow">→</span>

                                                                        <a class="{{ $classbox1 }}" href="#">
                                                                            <div class="container">
                                                                                <div class="indicator 
                                                                                    {{ $approverstatus == 'Approve' ||  ($progressReport->lastthreedocument->last() && $progressReport->lastthreedocument->last()->status === 'closed') ? 'green' 
                                                                                    : ($approverstatus == 'Ongoing' ? 'orange' 
                                                                                    : ($approverstatus == 'Belum dibaca' ? 'yellow' 
                                                                                    : 'red')) }}" 
                                                                                    title="{{ $approverstatus == 'Approve' ? 'Approver telah menyetujui' 
                                                                                    : ($approverstatus == 'Ongoing' ? 'Approver belum menyetujui' 
                                                                                    : ($approverstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' 
                                                                                    : 'Belum ada tindakan yang bisa diambil')) }}">
                                                                                </div>
                                                                                <span class="keterangan">Approver</span>
                                                                            </div>
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        @foreach($progressReport->lastthreedocument as $jobticket)
                                                                            <p>
                                                                                <span class="badge badge-primary">Rev: {{ $jobticket->rev }}</span>
                                                                                @if($jobticket->status === 'closed')
                                                                                    <span class="badge badge-success">Status: Closed</span>
                                                                                    <span class="badge bg-purple">Drafter: {{ $jobticket->drafter_name }}</span>
                                                                                @elseif($jobticket->drafter_id === null)
                                                                                    <span class="badge badge-danger">Status: No Pic</span>
                                                                                    <span class="badge bg-gray">Drafter: Tidak ada</span>
                                                                                @else
                                                                                    <span class="badge badge-warning">Status: Ongoing</span>
                                                                                    <span class="badge bg-teal">Drafter: {{ $jobticket->drafter_name }}</span>
                                                                                @endif
                                                                            </p>
                                                                        @endforeach
                                                                    </td>
                                                                    <td> 
                                                                        <a href="{{ route('jobticket.showdocument', ['id'=>$jobticketpart->id,'iddocumentnumber'=>$progressReport->id]) }}" class="btn btn-default bg-maroon d-block mb-1">
                                                                            <i class="fas fa-info-circle"></i> List Revisi
                                                                        </a>
                                                                        
                                                                        <a href="" class="btn btn-default bg-warning d-block mb-1">
                                                                            <i class="nav-icon fas fa-envelope"></i>
                                                                            <span class="badge badge-danger navbar-badge" style="font-size: 1.5em; font-weight: bold;">
                                                                                @if($progressReport->jobticketHistoriescount > 0)
                                                                                    {{ $progressReport->jobticketHistoriescount }}
                                                                                @else
                                                                                    0
                                                                                @endif
                                                                            </span>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @php
                                                    $loopIndex++;  // Increment untuk ID tabel berikutnya
                                                @endphp
                                            @endforeach

                                        </div>

                                    </div>
                                
                            </div>
                        </div>
            
                    </div>

                    


                </div>

            
        </div>
    <!-- </div> -->
</div>
</div>
@endsection

@push('css')
    <style>
        .centered-content {
            /* Atur gaya elemen di sini jika diperlukan */
            text-align: center; /* Menempatkan teks di tengah jika ada */
        }
        .table-hover tbody tr.checked {
            background-color: #f0f8ff; /* Warna biru muda */
        }

        .table-hover tbody tr.checked td {
            color: #333; /* Warna teks untuk kontras */
        }
    </style>

@endpush

@push('scripts') 

    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
    <script>
        $(document).ready(function () {
            var loopIndex = 0; // Inisialisasi variabel loop
            
            // Loop melalui setiap tabel yang dihasilkan
            $('.table-hover').each(function() {
                // Inisialisasi DataTables pada setiap tabel dengan ID yang unik
                $(this).attr('id', 'example2-' + loopIndex);
                $('#example2-' + loopIndex).DataTable({
                    "paging": true,        // Enable pagination
                    "lengthChange": false,   // Enable the change of records per page
                    "searching": true,      // Enable search
                    "ordering": true,       // Enable column ordering
                    "info": true,           // Show info at the bottom of the table
                    "autoWidth": false,     // Disable auto width adjustment
                    "responsive": true,     // Make the table responsive
                    "pageLength": 10,       // Default number of rows per page
                });

                loopIndex++;  // Increment untuk tabel berikutnya
            });
        });
    </script>

@endpush
