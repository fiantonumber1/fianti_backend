<!-- resources/views/boms/index.blade.php -->

@extends('layouts.universal')

@section('container2') 
    <div class="content-header">
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
            <ol class="breadcrumb bg-white px-2 float-left">
                <li class="breadcrumb-item"><a href="/">BOM</a></li>
                <li class="breadcrumb-item active text-bold">Tracking BOM</li>
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
                        
                        
                        @if(in_array(auth()->user()->rule, ["Product Engineering","superuser"]))
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Tombol untuk menghapus yang dipilih -->
                                    <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Tambahkan tombol upload di sini -->
                                    <a href="{{ url('uploadbom') }}" class="btn btn-primary btn-sm btn-block mb-3">Upload BOM</a>
                                </div>
                            </div>
                        @endif

                        <table id="example2-{{ $keyan }}" class="table table-bordered table-hover">
                            @php
                                if($keyan !== 'All'){
                                    $boms = $revisi['boms'];
                                }
                            @endphp
                            <thead>
                                <tr>
                                    <th>
                                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                                    </th>
                                    <th scope="col">No</th>
                                    <th scope="col">Nomor BOM</th>
                                    <th scope="col">Tipe Proyek</th>
                                    <th scope="col">Persentase</th>
                                    <th scope="col">Detail</th> <!-- Tambahkan kolom untuk tombol detail -->
                                    <th scope="col">Waktu Terbit</th> <!-- Tambahkan kolom untuk tombol detail -->
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counterdokumen = 1; // Inisialisasi variabel counter
                                @endphp
                                @foreach ($boms as $bom)
                                    @php
                                        $key = key($boms);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <!-- Tambahkan name dan ID unik -->
                                                <input type="checkbox" value="{{ $bom->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                                <label for="checkbox{{ $key }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $counterdokumen++ }}</td>
                                        <td>{{ $bom->BOMnumber }}</td>
                                        <td>{{ $bom->proyek_type }}</td>
                                        <td>
                                            <span class="badge bg-{{$groupbomnumberpercentage[$bom->BOMnumber] == 100 ? 'success' : 'warning'}}" style="padding: 5px;">
                                                {{ number_format($groupbomnumberpercentage[$bom->BOMnumber], 2) }}% Completed
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('bom.show', $bom->id) }}" class="btn btn-primary">Detail</a> <!-- Tambahkan tombol detail -->
                                        </td>
                                        <td>{{ $bom->created_at }}</td>
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
