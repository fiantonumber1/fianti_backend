@extends('layouts.table1')

@php
    $categoryprojectbaru = json_decode($categoryproject, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"');
    $listproject = json_decode($categoryproject, true);

    $categoryprojectbaru = json_decode($category, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"');
    $listpic = json_decode($categoryproject, true);
@endphp

@section('container1')
    <h1>Monitoring Memo</h1>
@endsection

@section('container2')
    <h3 class="card-title">Page monitoring memo</h3>
@endsection

@section('container3')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Edit Memo</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('edit.Document', ['id' => $document->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="documentname">Nama Memo:</label>
                <textarea class="form-control" name="documentname" id="documentname" rows="5">{{ $document->documentname }}</textarea>
            </div>

            <div class="form-group">
                <label for="memokind">Jenis Memo:</label>
                <textarea class="form-control" name="memokind" id="memokind" rows="1" placeholder="Approval">{{ $document->memokind }}</textarea>
            </div>
                    @php
                        $komats = json_decode(json_decode($document->remaininformation)->komat);
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
                                @foreach ($komats as $index => $komat)
                                    @php
                                        $komponen = json_decode($komat)->komponen;
                                        $kodematerial = json_decode($komat)->kodematerial;
                                        $supplier = json_decode($komat)->supplier;
                                    @endphp
                                    <tr>
                                        <td>{{ $komponen }}</td>
                                        <td>{{ $kodematerial }}</td>
                                        <td>{{ $supplier }}</td>
                                        <td>
                                            <a href="#" class="btn btn-info btn-sm" onclick="showDocumentSummary('{{ $document->id }}',  '{{ $index }}','{{ $komponen }}','{{ $kodematerial }}','{{ $supplier }}')">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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
                        <input type="text" class="form-control" name="new_kodematerial[]" placeholder="Kode Material">
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
                        <option value="{{ $project }}" {{ $document->project_type == $project ? 'selected' : '' }}>{{ $project }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Project PIC:</label><br>
                @foreach($listpic as $pic)
                    @php
                        $picArray = $document->project_pic ? json_decode($document->project_pic) : [];
                        $isChecked = in_array($pic, $picArray);
                    @endphp
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="project_pic[]" value="{{ $pic }}" {{ $isChecked ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $pic }}</label>
                    </div>
                @endforeach
            </div>

            <input type="hidden" name="notificationcategory" value="{{ $document->category }}">

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $(".add-new").click(function(){
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

        $("#komat-container").on('click','.remove',function(){
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

@endsection
