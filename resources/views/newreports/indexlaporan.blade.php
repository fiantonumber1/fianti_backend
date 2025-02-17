@extends('layouts.universal')



@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="/">Progress</a></li>
            <li class="breadcrumb-item active text-bold">Tracking Progress</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection

@section('container3')
    
    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page monitoring memo <span class="badge badge-info ml-1"></span></h3>
        </div>  
        <div class="card-body">
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
                                
                                @if(auth()->user()->rule=="superuser")
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <!-- Tombol untuk menghapus yang dipilih -->
                                        <button type="button" class="btn btn-danger btn-sm btn-block mt-2" onclick="handleDeleteMultipleItems('{{ $keyan }}')">Hapus yang dipilih</button>
                                    </div>
                                @endif
                            
                                @if(auth()->user()->rule=="MTPR"||auth()->user()->rule=="superuser")
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <!-- Tambahkan tombol upload di sini -->
                                        
                                            <a href="{{ url('newprogressreports/upload') }}" class="btn btn-primary btn-sm btn-block mt-2">Upload Progress Report</a>
                                        
                                    </div>
                                @endif
                                <div class="col-md-3 col-sm-6 col-12">
                                    @if(session('internalon'))
                                        <button id="internalOffButton" class="btn btn-success mt-2 btn-borderless">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button id="internalButton" class="btn btn-default bg-white mt-2 btn-borderless d-none"></button>
                                    @else
                                        <button id="internalOffButton" class="btn btn-success mt-2 btn-borderless d-none">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button id="internalButton" class="btn btn-default bg-white mt-2 btn-borderless"></button>
                                    @endif
                                </div>
                                
                                <style>
                                    .btn-borderless {
                                        border: none;
                                        
                                    }
                                </style>
                        </div>
                        <!-- tab 1 -->
                        @if(session('internalon'))
                            <form action="{{ route('newreports.downloadbyproject', ['newreport' => str_replace( '_',' ', $keyan)]) }}" method="POST">
                                @csrf
                                <div class="form-row align-items-center mb-4">
                                    <div class="col-auto">
                                        <label for="start_date" class="sr-only">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control mb-2" required>
                                    </div>
                                    <div class="col-auto">
                                        <label for="end_date" class="sr-only">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control mb-2" required>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary mb-2">Download Report</button>
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('newreports.downloadduplicatebyproject', ['newreport' => str_replace( '_',' ', $keyan)]) }}" method="POST">
                                @csrf
                                <div class="form-row align-items-center mb-4">
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary mb-2">Download Duplicate Dokumen</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <table id="example2-{{ $keyan }}" class="table table-bordered table-hover table-striped">
                            @php
                                if($keyan !== 'All'){
                                    $newreports = $revisi['newreports'];
                                }
                            @endphp
                            

                            <thead>
                                <tr>
                                    <th>
                                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                    </th>
                                    <th>No</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Nama Proyek</th>
                                    <th scope="col">Persentase</th>
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
                                        
                                            
                                        
                                        <td style="width: 25%" class="p-1">
                                            @php
                                                if($newreport->unit=="Sistem Mekanik"||$newreport->unit=="Desain Interior"||$newreport->unit=="Desain Carbody"||($newreport->unit=="Product Engineering"&&$newreport->proyek_type=="100 Unit Bogie TB1014")){
                                                    $totalpersentaseeksternal='100';
                                                }elseif($newreport->unit=="Desain Bogie & Wagon" && $newreport->proyek_type=="KCI"){
                                                    $totalpersentaseeksternal='100';
                                                }else{
                                                    $totalpersentaseeksternal=number_format($newreport->seniorpercentage, 2);
                                                }
                                                $totalpersentaseinternal=number_format($newreport->seniorpercentage, 2);
                                            @endphp
                                            @php
                                                if ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "KCI" ){
                                                    $counting=9;
                                                    $documentrelease=9;
                                                    $documentunrelease=0;
                                                    $batas=10;
                                                    
                                                }elseif ($newreport->unit == "Electrical Engineering System" && $newreport->proyek_type == "KCI" ){
                                                    $counting=52;
                                                    $documentrelease=52;
                                                    $documentunrelease=0;
                                                    $batas=53;

                                                }elseif ($newreport->unit == "Mechanical Engineering System" && $newreport->proyek_type == "KCI" ){
                                                    $counting=38;
                                                    $documentrelease=38;
                                                    $documentunrelease=0;
                                                    $batas=39;

                                                }elseif ($newreport->unit == "Quality Engineering" && $newreport->proyek_type == "KCI" ){
                                                    $counting=194;
                                                    $documentrelease=165;
                                                    $documentunrelease=29;
                                                    $batas=195;

                                                }elseif ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI" ){
                                                    $counting=159;
                                                    $documentrelease=159;
                                                    $documentunrelease=0;
                                                    $batas=160;

                                                }elseif ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI" ){
                                                    $counting=229;
                                                    $documentrelease=229;
                                                    $documentunrelease=0;
                                                    $batas=230;

                                                }elseif ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI" ){
                                                    $counting=1075; 
                                                    $documentrelease=1075;
                                                    $documentunrelease=0;
                                                    $batas=1076;

                                                }elseif ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI" ){
                                                    $counting=538; 
                                                    $documentrelease=538; 
                                                    $documentunrelease=0;
                                                    $batas=539; 

                                                }elseif ($newreport->unit == "Desain Elektrik" && $newreport->proyek_type == "KCI" ){
                                                    $counting=232; 
                                                    $documentrelease=211; 
                                                    $documentunrelease=21;
                                                    $batas=233; 

                                                }elseif ($newreport->unit == "Shop Drawing" && $newreport->proyek_type == "KCI" ){
                                                    $counting=89; 
                                                    $documentrelease=89;
                                                    $documentunrelease=0;
                                                    $batas=90;

                                                }elseif ($newreport->unit == "Preparation & Support" && $newreport->proyek_type == "KCI" ){
                                                    $counting=57; 
                                                    $documentrelease=57;
                                                    $documentunrelease=0;
                                                    $batas=58;

                                                }elseif ($newreport->unit == "Welding Technology" && $newreport->proyek_type == "KCI" ){
                                                    $counting=197; 
                                                    $documentrelease=197; 
                                                    $documentunrelease=0;
                                                    $batas=198; 

                                                }elseif ($newreport->unit == "Teknologi Proses" && $newreport->proyek_type == "KCI" ){
                                                    $counting=534; 
                                                    $documentrelease=507; 
                                                    $documentunrelease=527;
                                                    $batas=535; 

                                                }elseif ($newreport->unit == "Welding Technology" && $newreport->proyek_type == "KCI" ){
                                                    $counting=197; 
                                                    $documentrelease=197; 
                                                    $documentunrelease=0;
                                                    $batas=198; 

                                                }
                                                $totalpersentaseeksternal=number_format($documentrelease/$counting*100);
                                            @endphp
                                            @if(session('internalon'))
                                                <span class="badge badge-warning"  style="font-size: 2rem;">{{ $totalpersentaseinternal. '%' }}</span>  
                                            @elseif (number_format($newreport->seniorpercentage, 2) == 0)
                                                <span class="badge badge-secondary  style="font-size: 2rem;"">-</span>
                                            @else
                                                <span class="badge badge-success d-1"  style="font-size: 2rem;">{{ $totalpersentaseeksternal . '%' }}</span>
                                                <span class="badge badge-warning d-none"  style="font-size: 2rem;">{{ $totalpersentaseinternal. '%' }}</span>  
                                            @endif
                                        </td>
                                        <td>{{$counting }}</td>
                                        <td>
                                            <a href="{{ route('newreports.laporan', $newreport->id) }}" class="btn btn-primary">View</a>
                                            <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-default bg-purple" onclick="return confirm('Are you sure?')">Download</button>
                                            </form>

                                            @if (auth()->user()->rule=="superuser"||auth()->user()->rule=="MTPR")
                                                <form action="{{ route('newreports.destroy', $newreport->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @endif
                                            @if (auth()->user()->name=="Dian Pertiwi")
                                                <form action="{{ route('newreports.destroydian', $newreport->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-default bg-pink" onclick="return confirm('Are you sure?')">Hapus Rencana saja (Khusus Dian)</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('newreports.doubledetector', $newreport->id) }}" class="btn btn-default bg-kakhi">double Detector: {{$newreport->doubledetectorcount()}}</a>
                                            
                                            
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
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


        document.getElementById('internalButton').addEventListener('click', function() {
            
            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (password === '12345') {
                        // Save the status to the session
                        return $.ajax({
                            url: '{{ route("set.internalon") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                location.reload();
                                // Reveal the hidden elements
                                document.querySelectorAll('.badge-warning.d-none').forEach(element => {
                                    element.classList.remove('d-none');
                                });
                                document.querySelectorAll('.badge-success.d-1').forEach(element => {
                                    element.classList.add('d-none');
                                });
                            },
                            error: function() {
                                Swal.showValidationMessage('Failed to set session');
                            }
                        });
                    } else {
                        Swal.showValidationMessage('Incorrect password');
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password correct, internal details revealed.',
                        icon: 'success'
                    });
                }
            });
        });


        document.getElementById('internalOffButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to turn off internal details?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, turn off',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Save the status to the session
                    return $.ajax({
                        url: '{{ route("set.internaloff") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            location.reload();
                            // Reveal the hidden elements
                            document.querySelectorAll('.badge-warning').forEach(element => {
                                element.classList.add('d-none');
                            });
                            document.querySelectorAll('.badge-success.d-1.d-none').forEach(element => {
                                element.classList.remove('d-none');
                            });
                        },
                        error: function() {
                            Swal.showValidationMessage('Failed to set session');
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Internal details turned off.',
                        icon: 'success'
                    });
                }
            });
        });

    </script>
@endsection

@section('script')
    <script>
        $(function () {
            @foreach ($revisiall as $key => $revisi)
            $('#example2-{{ $key }}').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
            @endforeach
        });
    </script>

    <script>
        $(function () {
            //Enable check and uncheck all functionality
            $('#checkAll').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('input[name="document_ids[]"]').prop('checked', false);
                    $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check first 10 checkboxes
                    $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                    $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks);
            });
        });
    </script>
@endsection
