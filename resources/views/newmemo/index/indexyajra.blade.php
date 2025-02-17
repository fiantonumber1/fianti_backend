@extends('layouts.universal')

@php
    $authuser = auth()->user();
@endphp

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
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
        <table id="documents-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th><span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span></th>
                    <th>No</th>
                    <th>Deadline</th>
                    <th>Nomor Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data akan dimuat melalui AJAX -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
$(function () {
    $('#documents-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("new-memo.indextertutup") }}', // Rute yang baru
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'deadline', name: 'deadline'},
            {data: 'documentnumber', name: 'documentnumber'},
            {data: 'documentname', name: 'documentname'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
    });
});

    function toggleDocumentStatus(button) {
        var documentId = $(button).data('document-id');
        var currentStatus = $(button).data('document-status');
        var newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan mengubah status dokumen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah status!',
            html: `
                <label for="fileUpload" style="margin-top: 10px;">Pilih file:</label>
                <input type="file" id="fileUpload" multiple />
            `,
            preConfirm: () => {
                const fileInput = Swal.getPopup().querySelector('#fileUpload');
                if (fileInput && fileInput.files.length === 0) {
                    Swal.showValidationMessage('File harus dipilih');
                    return false;
                }
                return {
                    files: fileInput ? fileInput.files : []
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', 'Tertutup');

                if (result.value.files && result.value.files.length > 0) {
                    $.each(result.value.files, function(index, file) {
                        formData.append('file[]', file);
                    });
                }

                $.ajax({
                    url: "{{ url('new-memo/show') }}/" + documentId + "/updatedocumentstatus",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(button).removeClass('document-status-button-' + currentStatus.toLowerCase())
                                .addClass('document-status-button-' + newStatus.toLowerCase())
                                .data('document-status', newStatus)
                                .attr('title', newStatus)
                                .find('i').removeClass()
                                .addClass(newStatus === 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle')
                                .end().find('span').text(newStatus);
                        
                        if (newStatus === 'Terbuka') {
                            $(button).removeClass('btn-success').addClass('btn-danger');
                        } else {
                            $(button).removeClass('btn-danger').addClass('btn-success');
                        }

                        Swal.fire({
                            title: "Berhasil!",
                            text: "Status dokumen berhasil diubah, dan file telah diunggah.",
                            icon: "success"
                        });

                        $('#documents-table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Gagal!",
                            text: "Gagal mengubah status dokumen.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

</script>
@endpush

