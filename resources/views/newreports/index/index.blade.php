@extends('layouts.universal')



@section('container2')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
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
            <h3 class="card-title text-bold">Page monitoring dokumen <span class="badge badge-info ml-1"></span></h3>
        </div>  
        <div class="card-body">
            <!-- Dropdown for selecting revisi -->
            <div class="form-group">
                <label for="revisiDropdown">Pilih Project:</label>
                <select class="form-control" id="revisiDropdown">
                    @foreach ($revisiall as $keyan => $revisi)
                        <option value="{{ $keyan }}" @if($loop->first) selected @endif>{{ $keyan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Content for each revisi -->
            <div class="tab-content" id="revisiContent">
                @foreach ($revisiall as $keyan => $revisi)
                    @php
                        if($keyan !== 'All'){
                            $newreports = $revisi['newreports'];
                            $totalpersentaseeksternalall = 0;
                            $totalpersentaseinternalall = 0;
                            $totaldocument = 0;
                            $totalunit = 0;
                            foreach($newreports as $newreport){
                                $totalunit += 1;
                            }

                            foreach($newreports as $newreport){
                                if(($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || 
                                   ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || 
                                   ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || 
                                   ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || 
                                   ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                                    $totalpersentaseeksternal = 100 / $totalunit;
                                } else {
                                    $totalpersentaseeksternal = number_format($newreport->seniorpercentage, 2) / $totalunit;
                                }
                                $totalpersentaseinternal = number_format($newreport->seniorpercentage, 2) / $totalunit;

                                $totalpersentaseeksternalall += $totalpersentaseeksternal;
                                $totalpersentaseinternalall += $totalpersentaseinternal;
                                $totaldocument += $newreport->documentcount;
                            }
                            $totalpersentaseeksternalall = number_format($totalpersentaseeksternalall, 2);
                            $totalpersentaseinternalall = number_format($totalpersentaseinternalall, 2);
                        }
                    @endphp

                    <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $keyan }}" role="tabpanel">
                        <div class="card card-outline card-danger">
                            <div class="card-header">
                                <table class="table table-bordered my-2 table-responsive-">
                                    <tbody>
                                        <tr>
                                            <td rowspan="4" style="width: 25%" class="text-center">
                                                <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2" style="max-width: 250px">
                                            </td>
                                            <td rowspan="4" style="width: 50%">
                                                <h1 class="text-xl text-center mt-2">DAFTAR PROGRES</h1>
                                            </td>
                                            <td style="width: 25%" class="p-1">Project: <b>{{ ucwords(str_replace('-', ' ', $keyan)) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b></td>
                                        </tr>
                                        <tr>
                                            @if(session('internalon'))
                                                <td style="width: 25%" class="p-1">
                                                    Progres: <b><span class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}" style="font-size: 2rem;">{{$totalpersentaseinternalall}} %</span></b>
                                                </td>
                                            @else
                                                <td style="width: 25%" class="p-1">
                                                    Progres: <b><span class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}" style="font-size: 2rem;">{{$totalpersentaseeksternalall}} %</span></b>
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td style="width: 25%" class="p-1">
                                                Total Dokumen: <b><span class="badge badge-info" style="font-size: 1.5rem;">{{$totaldocument}}</span></b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-header">

                                <div class="row"> 
                                    @if(auth()->user()->rule=="superuser")
                                        <div class="col-md-3 col-sm-6 col-6 p-0">
                                            <!-- Tombol untuk menghapus yang dipilih -->
                                            <button type="button" class="btn btn-danger btn-sm btn-block mt-0" onclick="handleDeleteMultipleItems('{{ $keyan }}')">Hapus yang dipilih</button>
                                        </div>
                                    @endif
                                
                                    @if(auth()->user()->rule=="MTPR"||auth()->user()->rule=="superuser")
                                        
                                        <div class="col-md-3 col-sm-6 col-6 p-0">
                                            <!-- Tambahkan tombol upload di sini -->
                                            
                                                <a href="{{ url('newprogressreports/upload') }}" class="btn btn-primary btn-sm btn-block mt-0">Upload Progress Report</a>
                                            
                                        </div>
                                        
                                        
                                    @endif

                                    <div class="col-md-3 col-sm-6 col-6 p-0">
                                        @if(session('internalon'))
                                            <button id="internalOffButton" class="btn btn-success mt-0 btn-borderless">
                                                <i class="fas fa-arrow-left"></i>
                                            </button>
                                            <button id="internalButton" class="btn btn-default bg-white mt-0 btn-borderless d-none"></button>
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
                                    
                                    <div class="row"> 
                                        <div class="col-md-3 col-sm-6 col-6 p-0">
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
                                        </div>
                                        

                                    </div>

                                    <div class="row"> 
                                    
                                
                                        @if(auth()->user()->rule=="MTPR"||auth()->user()->rule=="superuser")
                                            
                                            
                                            <div class="col-md-3 col-sm-6 col-6 p-0">
                                                <!-- Tambahkan tombol upload di sini -->
                                                    <a href="{{ url('newreports/indexlogpercentage') }}" class="btn btn-default bg-teal btn-sm btn-block mt-0">History Progress</a>
                                                
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-6 p-0">
                                                <form action="{{ route('newreports.downloadduplicatebyproject', ['newreport' => str_replace( '_',' ', $keyan)]) }}" method="POST">
                                                    @csrf
                                                    <div class="col-auto">
                                                        <button type="submit" class="btn btn-default bg-purple btn-sm btn-block mt-0">Download Duplicate Dokumen</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                        @endif

                                        
                                
                            
                                    </div>
                                    
                                @endif

                            </div>

                            <div class="card-header">
                                <div class="col-md-3 col-sm-6 col-6 p-0">
                                    <form action="{{ route('newreports.viewbyprojectprogress', ['newreport' => str_replace('_', ' ', $keyan)]) }}" method="GET">
                                        <div class="form-row align-items-center mb-4">
                                            <div class="col-auto">
                                                <label for="start_date" class="sr-only">Start Date</label>
                                                <input type="date" id="start_date" name="start_date" class="form-control mb-2" required>
                                            </div>
                                            <input type="hidden" name="project" value="{{ str_replace('-', ' ', $keyan) }}">
                                            <div class="col-auto">
                                                <label for="end_date" class="sr-only">End Date</label>
                                                <input type="date" id="end_date" name="end_date" class="form-control mb-2" required>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary mb-2">View Laporan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card-body">
                                @include('newreports.index.table', ['newreports' => $revisi['newreports'], 'keyan' => $keyan])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.getElementById('revisiDropdown').addEventListener('change', function() {
            const selectedRevisi = this.value;
            document.querySelectorAll('.tab-pane').forEach(function(tabPane) {
                if (tabPane.id === 'custom-tabs-one-' + selectedRevisi) {
                    tabPane.classList.add('show', 'active');
                } else {
                    tabPane.classList.remove('show', 'active');
                }
            });
        });
    </script>
@endsection


@push('scripts')
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
    <script>
        $(function () {
            @foreach ($revisiall as $key => $revisi)
            $('#example2-{{ $key }}').DataTable({
                "paging": false,
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
@endpush
