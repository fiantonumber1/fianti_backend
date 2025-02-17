<!-- resources/views/hazard_logs/index.blade.php -->
<!-- resources/views/boms/index.blade.php -->
@php
    use Carbon\Carbon;
@endphp
@extends('layouts.tablemapping')

@section('container1') 
    <div class="col-sm-6">
        <h1>Daftar Semua Hazard Log</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Daftar Semua Hazard Log</li>
        </ol>
    </div>
@endsection

@section('container2')
    <h3 class="card-title">Page monitoring Hazard Log</h3>
@endsection

@section('container3')

    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
        @foreach ($revisiall as $keyan => $revisi)
            <li class="nav-item">
                <a class="nav-link @if($loop->first) active @endif" id="custom-tabs-one-{{ $keyan }}-tab" data-toggle="pill" href="#custom-tabs-one-{{ $keyan }}" role="tab" aria-controls="custom-tabs-one-{{ $keyan }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $keyan }}</a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="custom-tabs-one-tabContent">
        @foreach ($revisiall as $keyan => $revisi)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $keyan }}" role="tabpanel" aria-labelledby="custom-tabs-one-{{ $keyan }}-tab">
                    <div class="row">
                        @if(in_array(auth()->user()->rule, ["superuser"]))
                            <div class="col-md-3 col-sm-6 col-12">
                                <!-- Tombol untuk menghapus yang dipilih -->
                                <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                            </div>
                        @endif
                        <div class="col-md-3 col-sm-6 col-12">
                            <!-- Tambahkan tombol upload di sini -->
                            <a href="{{ url('hazard_logs/create') }}" class="btn btn-primary btn-sm btn-block mb-3">Tambah Hazard Log</a>
                        </div>
                    </div>
                

                    <table id="example2-{{ $keyan }}" class="table table-bordered table-hover">
                        @php
                            $hazardLogs = $revisi['hazardLogs'];
                        @endphp
                        <thead>
                            <tr>
                                <th>
                                    <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                </th>
                                <th scope="col">No</th>
                                <th scope="col">Hazard Ref</th>
                                <th scope="col">Posisi Dokumen</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                                $counterdokumen = 1; // Inisialisasi variabel counter
                            @endphp
                            @foreach ($hazardLogs as $hazardLog)
                                @php
                                    $key = key($hazardLog);
                                @endphp
                                @php
                                    $unitsingkatan=$hazardLog->unitsingkatan;
                                    $projectpics = $hazardLog->projectpics;
                                    $unitpicvalidation=$hazardLog->unitpicvalidation;

                                    $smunitpicvalidation=$hazardLog->smunitpicvalidation;
                                    $ramscombinevalidation=$hazardLog->ramscombinevalidation;
                                    
                                    
                                @endphp
                                <tr>
                                    <td>
                                        <div class="icheck-primary">
                                            <!-- Tambahkan name dan ID unik -->
                                            <input type="checkbox" value="{{ $hazardLog->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                            <label for="checkbox{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $counterdokumen++ }}</td>
                                    <td>{{ $hazardLog->hazard_ref }}</td>
                                    @php
                                        //sementara
                                        if($hazardLog->posisi1=="on"){
                                            $classbox1="boxblue";
                                        }else{
                                            $classbox1="box";
                                        }

                                        if($hazardLog->posisi2=="on"){
                                            $classbox2="boxblue";
                                        }else{
                                            $classbox2="box";
                                        }

                                        if($hazardLog->posisi3=="on"){
                                            $classbox3="boxblue";
                                        }else{
                                            $classbox3="box";
                                        }
                                        $hazardLog->ramsvalidation= 'Aktif';
                                    @endphp
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

                                    <td class="project-actionkus text-right">
                                        <a class="{{$classbox1}}" href="#">
                                            <div class="container">
                                                <div class="indicator 
                                                    {{ $hazardLog->ramsvalidation == 'Aktif' ? 'green' : 'red' }}" 
                                                    title="{{ $hazardLog->ramsvalidation == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                                </div>
                                                <span class="keterangan">RAMS</span>
                                            </div>
                                        </a>
                                        <span class="arrow">→</span>
                                        <div class="{{$classbox2}}" style="height: 300px;">
                                            <h2>Eng</h2>
                                            <ul>
                                                @foreach(['Product Engineering','Mechanical Engineering System','Electrical Engineering System','Quality Engineering'] as $projectpic)
                                                    <li>   
                                                    @if(isset($projectpics))
                                                        @if(in_array($projectpic, $projectpics))
                                                        <div class="container">
                                                            <div class="indicator 
                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                : 'red'))) }}" 
                                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca' 
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                                            </div>
                                                        </div>
                                                        @else
                                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                    @endif
                                                    @if($projectpic!="RAMS")
                                                    
                                                        <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                                    @else
                                                        <span class="keterangan">{{$projectpic}}</span>
                                                    @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="{{$classbox2}}" style="height: 300px;">
                                            <h2>Des</h2>
                                            <ul>
                                            @foreach(['Desain Mekanik & Interior','Desain Bogie & Wagon','Desain Carbody','Desain Elektrik'] as $projectpic)
                                                    <li>   
                                                    @if(isset($projectpics))
                                                        @if(in_array($projectpic, $projectpics))
                                                            <div class="indicator 
                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                : 'red'))) }}" 
                                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca' 
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                                            </div>
                                                        @else
                                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                            
                                                        @endif
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        
                                                    @endif
                                                    <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="{{$classbox2}}" style="height: 300px;">
                                            <h2>TP</h2>
                                            <ul>
                                            @foreach(['Preparation & Support','Welding Technology','Shop Drawing','Teknologi Proses'] as $projectpic)
                                                    <li>   
                                                    @if(isset($projectpics))
                                                        @if(in_array($projectpic, $projectpics))
                                                            <div class="indicator 
                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif' ? 'green' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? 'orange' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? 'yellow' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? 'blue'
                                                                : 'red'))) }}" 
                                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif' ? $projectpic . ' sudah approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' 
                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca' ? $projectpic . ' belum dibaca' 
                                                                : ($unitpicvalidation[$projectpic] == 'Sudah dibaca' ? $projectpic . ' sudah dibaca' 
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                                            </div>
                                                        @else
                                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                    @else
                                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                    @endif
                                                    <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <span class="arrow">→</span>

                                        <a class="{{$classbox3}}" href="#">
                                            <div class="container">
                                                
                                                <div class="indicator 
                                                    {{ $ramscombinevalidation == 'Aktif' ? 'green' 
                                                    : ($ramscombinevalidation == 'Ongoing' ? 'orange' 
                                                    : ($ramscombinevalidation == 'Belum dibaca' ? 'yellow' 
                                                    : ($ramscombinevalidation == 'Sudah dibaca' ? 'blue'
                                                    : 'red'))) }}" 
                                                    title="{{ $ramscombinevalidation == 'Aktif' ? 'RAMS' . ' sudah approve' 
                                                    : ($ramscombinevalidation == 'Ongoing' ? 'RAMS' . ' sudah melakukan feedback dan belum approve' 
                                                    : ($ramscombinevalidation == 'Belum dibaca' ? 'RAMS' . ' belum dibaca' 
                                                    : ($ramscombinevalidation == 'Sudah dibaca' ? 'RAMS' . ' sudah dibaca' 
                                                    : $projectpic . ' belum dikerjakan'))) }}">
                                                </div>
                                                <span class="keterangan">RAMS</span>
                                            </div>
                                        </a>

                                    </td>
                                    <td>
                                        @if($hazardLog->due_date)
                                            {{ Carbon::parse($hazardLog->due_date)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ucfirst($hazardLog->status)}}
                                    </td>
                                    <td>
                                        <a href="{{ url('hazard_logs/' . $hazardLog->id . '/show') }}" class="btn btn-sm btn-primary">Detail</a>
                                        <form action="{{ url('hazard_logs/' . $hazardLog->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
           
                

            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        function handleDeleteMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                // Jika pengguna mengonfirmasi penghapusan
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function(checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    // Melakukan panggilan AJAX untuk menghapus item yang dipilih
                    $.ajax({
                        url: "",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function(response) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });

                            // Refresh halaman setelah penghapusan
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Tampilkan pesan error
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    
    </script>
    
    
@endsection



















