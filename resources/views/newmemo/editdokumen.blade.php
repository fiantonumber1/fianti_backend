@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
$categoryprojectbaru = json_decode($category, true)[0];
$categoryproject = trim($categoryprojectbaru, '"');
$listpic = json_decode($categoryproject, true);
@endphp

@section('container2') 

<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="">Memo</a></li>
                    <li class="breadcrumb-item"><a
                            href="">Edit Dokumen</a></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

@endsection

@section('container3')  
<div class="row justify-content-center">



    <div class="col-10">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Memo</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('new-memo.posteditdocument', ['memoId' => $document->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="documentname">Nama Memo:</label>
                        <textarea class="form-control" name="documentname" id="documentname"
                            rows="5">{{ $document->documentname }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="memokind">Jenis Memo:</label>
                        <textarea class="form-control" name="memokind" id="memokind" rows="1"
                            placeholder="Approval">{{ $document->memokind }}</textarea>
                    </div>
                    @php
                        $komats = $document->komats;
                    @endphp

                    <div class="form-group">
                        <label for="komat">komat:</label>
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th>Kode Material</th>
                                    <th>Supplier</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($komats)
                                                            @foreach ($komats as $index => $komat)
                                                                                        @php
                                                                                            $komponen = $komat->komponen;
                                                                                            $kodematerial = $komat->kodematerial;
                                                                                            $supplier = $komat->supplier;
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td>{{ $komponen }}</td>
                                                                                            <td>{{ $kodematerial }}</td>
                                                                                            <td>{{ $supplier }}</td>
                                                                                            <td>
                                                                                                <a href="#" class="btn btn-info btn-sm"
                                                                                                    onclick="showDocumentSummary('{{ $document->id }}',  '{{ $index }}','{{ $komponen }}','{{ $kodematerial }}','{{ $supplier }}')">
                                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                                </a>
                                                                                            </td>
                                                                                        </tr>
                                                            @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group" id="komat-container">
                        <label for="komat">komat:</label>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="new_komponen[]" placeholder="Komponen">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="new_kodematerial[]"
                                    placeholder="Kode Material">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="new_supplier[]" placeholder="Supplier">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success add-new">Add New</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="project_type">Project:</label>
                        <select class="form-control" name="project_type" id="project_type">
                            @foreach($listproject as $project)
                                <option value="{{ $project->title }}" {{ $document->project_type == $project->title ? 'selected' : '' }}>
                                    {{ $project->title}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Project PIC:</label><br>
                        @if(empty($document->project_pic) || auth()->user()->id == 1)
                                                @php
                                                    $picArray = $document->project_pic ? json_decode($document->project_pic) : [];

                                                @endphp
                                                @foreach($listpic as $pic)
                                                                    @php
                                                                        $isChecked = in_array($pic, $picArray);
                                                                    @endphp
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="checkbox" name="project_pic[]" value="{{ $pic }}" {{ $isChecked ? 'checked' : '' }}>
                                                                        <label class="form-check-label">{{ $pic }}</label>
                                                                    </div>
                                                @endforeach
                        @else
                                                @php
                                                    $picArray = $document->project_pic ? json_decode($document->project_pic, true) : [];
                                                @endphp
                                                @foreach($picArray as $pic)
                                                    <span class="badge badge-info">{{ $pic }}</span>
                                                @endforeach
                        @endif



                    </div>







                    <input type="hidden" name="notificationcategory" value="{{ $document->category }}">

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>

        
    </div>

</div>
@endsection

@push('css') 

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables & Plugins -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tambahkan ini ke dalam <head> di file HTML Anda -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
    <!-- Sweetalert2 (include theme bootstrap) -->
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Donut Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>
    
@endpush

@push('scripts')

    <!-- jQuery -->
    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables & Plugins -->
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
    <script src="https://code.y.com/jquery-3.6.0.min.js" integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I="
        crossorigin="anonymous"></script>
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function () {
                $(".add-new").click(function () {
                    var html = '<div class="row mt-2">' +
                        '<div class="col-md-5">' +
                        '<input type="text" class="form-control" name="new_komponen[]" placeholder="Komponen">' +
                        '</div>' +
                        '<div class="col-md-5">' +
                        '<input type="text" class="form-control" name="new_kodematerial[]" placeholder="Kode Material">' +
                        '</div>' +
                        '<div class="col-md-5">' +
                        '<input type="text" class="form-control" name="new_supplier[]" placeholder="Supplier">' +
                        '</div>' +
                        '<div class="col-md-2">' +
                        '<button type="button" class="btn btn-danger remove">Remove</button>' +
                        '</div>' +
                        '</div>';
                    $("#komat-container").append(html);
                });

                $("#komat-container").on('click', '.remove', function () {
                    $(this).closest('.row').remove();
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            function showDocumentSummary(id, index, komponen, kodematerial, supplier) {
                Swal.fire({
                    title: "Input Material,Kode Material dan Supplier",
                    html: `
            <input id="material" class="swal2-input" value="${komponen}" placeholder="Material">
            <input id="kode-material" class="swal2-input" value="${kodematerial}" placeholder="Kode Material">
            <input id="supplier" class="swal2-input" value="${supplier}" placeholder="Supplier">
        `,
                    focusConfirm: false,
                    showCancelButton: true, // Tampilkan tombol batal
                    confirmButtonText: 'Update', // Mengubah teks tombol konfirmasi
                    cancelButtonText: 'Delete', // Mengubah teks tombol batal
                    preConfirm: () => {
                        return [

                            document.getElementById("material").value,
                            document.getElementById("kode-material").value,
                            document.getElementById("supplier").value
                        ];
                    }
                }).then((result) => {
                    if (result.value) {
                        const [material, kodeMaterial, supplier] = result.value;

                        Swal.fire(`Material: ${material},Kode Material: ${kodeMaterial},  Supplier: ${supplier}`);

                        // Kirim request ke endpoint 'update' dengan menggunakan method 'GET'
                        var updateUrl = `/komat/update/${id}/${index}?material=${material}&kodematerial=${kodeMaterial}&supplier=${supplier}`;

                        // Redirect atau buka URL untuk update
                        window.location.href = updateUrl;
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Kirim request ke endpoint 'delete' dengan menggunakan method 'GET'
                        var deleteUrl = `/komat/delete/${id}/${index}`;

                        // Redirect atau buka URL untuk delete
                        window.location.href = deleteUrl;
                    }
                });
            }
        </script>

    
@endpush