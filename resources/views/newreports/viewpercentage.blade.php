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
                    @php
                        
                        if($keyan !== 'All'){
                            $newreports = $revisi['newreports'];
                            $totalpersentaseeksternalall=0;
                            $totalpersentaseinternalall=0;
                            $totaldocument=0;
                            $totalunit=0;
                            foreach($newreports as $newreport){
                                $totalunit+=1;
                            }
                            
                        }

                        foreach($newreports as $newreport){
                            if(($newreport->unit=="Desain Bogie & Wagon" && $newreport->proyek_type=="KCI")||($newreport->unit=="Sistem Mekanik" && $newreport->proyek_type=="KCI") || ($newreport->unit=="Desain Interior" && $newreport->proyek_type=="KCI") ||($newreport->unit=="Desain Carbody" && $newreport->proyek_type=="KCI")||($newreport->unit=="Product Engineering"&&$newreport->proyek_type=="100 Unit Bogie TB1014")){
                                $totalpersentaseeksternal=100/$totalunit;
                            }else{
                                $totalpersentaseeksternal=number_format($newreport->seniorpercentage, 2)/$totalunit;
                            }
                            $totalpersentaseinternal=number_format($newreport->seniorpercentage, 2)/$totalunit;

                            $totalpersentaseeksternalall+=$totalpersentaseeksternal;
                            $totalpersentaseinternalall+=$totalpersentaseinternal;
                            $totaldocument+=$newreport->documentcount;
                        }
                        $totalpersentaseeksternalall=number_format($totalpersentaseeksternalall, 2);
                        $totalpersentaseinternalall=number_format($totalpersentaseinternalall, 2);

                    @endphp

                    <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $keyan }}" role="tabpanel" aria-labelledby="custom-tabs-one-{{ $keyan }}-tab">
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
                                            <td style="width: 25%" class="p-1">Tanggal: <b>{{ \Carbon\Carbon::parse($startDate)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/y') }}</b></td>
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
                            </div>

                            

                            <div class="card-body">
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
                                                    @if(session('internalon'))
                                                        <span class="badge badge-warning" style="font-size: 2rem;">{{ $newreport->totalpersentaseinternal}} %</span>  
                                                    @else
                                                        <span class="badge badge-success d-1"style="font-size: 2rem;">{{ $newreport->totalpersentaseeksternal}} %</span>
                                                        <span class="badge badge-warning d-none"style="font-size: 2rem;">{{ $newreport->totalpersentaseinternal}} %</span>  
                                                    @endif
                                                </td>
                                                <td>
                                                    <style>
                                                        .badge-fffd19 {
                                                            background-color: #fffd19; /* Warna latar belakang khusus */
                                                            color: #000; /* Warna teks */
                                                        }
                                                    </style>
                                                    <span class="badge badge-danger"style="font-size: 1.5rem;">Total Dokumen: {{ $newreport->documentcount }}</span>
                                                    @php
                                                        $releaseinfo = $newreport->releasecount();
                                                        if (session('internalon')) {
                                                            $release=$newreport->documentrelease;
                                                        }else{
                                                            if(($newreport->unit=="Desain Bogie & Wagon" && $newreport->proyek_type=="KCI")||($newreport->unit=="Sistem Mekanik" && $newreport->proyek_type=="KCI") || ($newreport->unit=="Desain Interior" && $newreport->proyek_type=="KCI") ||($newreport->unit=="Desain Carbody" && $newreport->proyek_type=="KCI")||($newreport->unit=="Product Engineering"&&$newreport->proyek_type=="100 Unit Bogie TB1014")){
                                                                $release=$newreport->documentcount;
                                                            }else {
                                                                $release=$newreport->documentrelease;
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="badge badge-fffd19"style="font-size: 1.5rem;">Dokumen Release: {{ $release }}</span>
                                                    <!-- <span class="badge badge-fffd19"style="font-size: 1.5rem;">Dokumen Aneh: {{ $newreport->satuini }}</span> -->
                                                </td>
                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        
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
@endsection
