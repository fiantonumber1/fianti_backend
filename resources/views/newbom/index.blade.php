<!-- resources/views/newbom/index.blade.php -->

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
            <h3 class="card-title text-bold">Page monitoring BOM <span class="badge badge-info ml-1"></span></h3>
        </div>  
        <div class="card-body">
            <!-- Dropdown for revisions -->
            <div class="form-group">
                <label for="revisionDropdown">Pilih Project :</label>
                <select id="revisionDropdown" class="form-control">
                    @foreach ($revisiall as $keyan => $revisi)
                        <option value="{{ $keyan }}" {{ $loop->first ? 'selected' : '' }}>{{ $keyan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Content based on selected dropdown -->
            <div class="tab-content" id="custom-tabs-one-tabContent">
                @foreach ($revisiall as $keyan => $revisi)
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $keyan }}" role="tabpanel">
                        
                        @if(in_array(auth()->user()->rule, ["Product Engineering","superuser",'MTPR']))
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Button for deleting selected items -->
                                    <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- Upload BOM button -->
                                    <a href="{{ url('newboms/uploadexcel') }}" class="btn btn-primary btn-sm btn-block mb-3">Upload BOM</a>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12">
                                    <!-- BOM History button -->
                                    <a href="{{ url('newboms/logpercentage') }}" class="btn btn-success btn-sm btn-block mb-3">History BOM</a>
                                </div>
                            </div>
                        @endif

                        <table id="example2-{{ $keyan }}" class="table table-bordered table-hover">
                            @php
                                if($keyan !== 'All'){
                                    $newboms = $revisi['boms'];
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
                                    <th scope="col">Unit </th>
                                    <th scope="col" style="width: 15%; text-align: center;">Persentase</th>
                                    <th scope="col">Detail</th> <!-- Column for detail button -->
                                    <th scope="col">Waktu Terbit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counterdokumen = 1; // Initialize counter variable
                                @endphp
                                @foreach ($newboms as $newbom)
                                    @php
                                        $key = key($newboms);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <!-- Unique name and ID for checkboxes -->
                                                <input type="checkbox" value="{{ $newbom->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                                <label for="checkbox{{ $key }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $counterdokumen++ }}</td>
                                        <td>{{ $newbom->BOMnumber }}</td>
                                        <td>{{ $newbom->proyek_type }}</td>
                                        <td>{{ $newbom->unit }}</td>
                                        <td style="width: 15%; text-align: center;" class="p-1">
                                            <span class="badge bg-{{number_format($newbom->seniorpercentage, 2) == 100 ? 'success' : 'warning'}}" style="font-size: 2rem;">{{ number_format($newbom->seniorpercentage, 2) }} %</span>  
                                        </td>
                                        <td>
                                            <a href="{{ route('newbom.show', $newbom->id) }}" class="btn btn-primary">Detail</a> <!-- Detail button -->
                                        </td>
                                        <td>{{ $newbom->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    

@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        // Function to handle multiple item deletion with AJAX
        function handleDeleteMultipleItems() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function(checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    $.ajax({
                        url: "",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });
                            location.reload();
                        },
                        error: function(xhr, status, error) {
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

        // Script for handling dropdown selection
        $('#revisionDropdown').change(function() {
            var selectedTab = $(this).val();
            $('.tab-pane').removeClass('show active');
            $('#custom-tabs-one-' + selectedTab).addClass('show active');
        });
    
    </script>
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

        $(function () {
            // Enable check and uncheck all functionality
            $('#checkAll').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    $('input[name="document_ids[]"]').prop('checked', false);
                    $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                    $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks);
            });
        });
    </script>

@endpush
