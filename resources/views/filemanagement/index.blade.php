@extends('layouts.universal')

@push('css')
    <link rel="stylesheet" href="{{ asset('/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush


@section('container2')
    <div class="content-header">
    <div class="container-fluid">
        <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb bg-white px-2 float-left">
            <li class="breadcrumb-item"><a href="/">File Management</a></li>
            <li class="breadcrumb-item active text-bold">File Management</li>
            </ol>
        </div>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <button onclick="addForm(`{{ route('project_types.store') }}`)" class="btn btn-success btn-flat">
                        <i class="fas fa-plus-circle"></i> {{ __('back.Tambah Data') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stiped table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th width="5%">{{ __('back.No') }}</th>
                                    <th>{{ __('back.Nama') }}</th>
                                    <th width="15%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                        </table>  
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <button onclick="addForm(`{{ route('file.store') }}`)" class="btn btn-success btn-flat">
                        <i class="fas fa-plus-circle"></i> {{ __('back.Tambah Data') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stiped table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th width="5%">{{ __('back.No') }}</th>
                                    <th>Nama Proyek</th>
                                    <th>Nama File</th>
                                    <th width="15%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                        </table>  
                    </div>
                </div>
            </div>
        </div>
    </div>

    @includeif('file.form')
@endsection



@push('scripts')
    <script src="{{ asset('/adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        let modal = '#modal-form';

        function loopErrors(errors) {
            $('.invalid-feedback').remove();

            if (errors == undefined) {
                return;
            }

            for (error in errors) {
                $(`[name=${error}]`).addClass('is-invalid');

                if ($(`[name=${error}]`).hasClass('select2')) {
                    $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                        .insertAfter($(`[name=${error}]`).next());
                } else if ($(`[name=${error}]`).hasClass('summernote')) {
                    $('.note-editor').addClass('is-invalid');
                    $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                        .insertAfter($(`[name=${error}]`).next());
                } else if ($(`[name=${error}]`).hasClass('custom-control-input')) {
                    $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                        .insertAfter($(`[name=${error}]`).next());
                } else {
                    if ($(`[name=${error}]`).length == 0) {
                        $(`[name="${error}[]"]`).addClass('is-invalid');
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name="${error}[]"]`).next());
                    } else {
                        if ($(`[name=${error}]`).next().hasClass('input-group-append') || $(`[name=${error}]`).next().hasClass('input-group-prepend')) {
                            $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                                .insertAfter($(`[name=${error}]`).next());
                            $('.input-group-append .input-group-text').css('border-radius', '0 .25rem .25rem 0');
                            $('.input-group-prepend').next().css('border-radius', '0 .25rem .25rem 0');
                        } else {
                            $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                                .insertAfter($(`[name=${error}]`));
                        }
                    }

                }
            }
        }

        function showAlert(message, type) {
            let title = '';
            switch (type) {
                case 'success':
                    title = 'Success';
                    break;
                case 'danger':
                    title = 'Failed';
                    break;
                default:
                    break;
            }

            $(document).Toasts('create', {
                class: `bg-${type}`,
                title: title,
                body: message
            });

            setTimeout(() => {
                $('.toasts-top-right').remove();
            }, 3000);
        }

    function preview(target, image) {
        if (image) {
            if (window.URL && window.URL.createObjectURL) {
                $(target)
                    .attr('src', window.URL.createObjectURL(image))
                    .show();
            } else {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(target)
                        .attr('src', e.target.result)
                        .show();
                };
                reader.readAsDataURL(image);
            }
        } else {
            $(target).hide();
        }
    }

        let table = $('#table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('project_types.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'project_code'},
                {data: 'project_name'},
                {data: 'action', searchable: false, sortable: false},
            ]
        });

        function addForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Tambah Data');
            $('#modal-form').css({
            'max-height': '97vh',
            'overflow-y': 'auto'
            });
            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=project_code]').focus();
        }


        function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Data');
            $('#modal-form').css({
            'max-height': '97vh',
            'overflow-y': 'auto'
            });
            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=project_code]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=project_id]').val(response.project_id);
                    $('#modal-form [name=project_code]').val(response.project_code);
                    $('#modal-form [name=project_name]').val(response.project_name);
                    $('.preview-path_file').attr('data', '/storage/' + response.data.path_file).show();
                    console.log(response);
                })
                .fail((errors) => {
                    var alertMessage = "Data Gagal Ditampilkan";
                    alert(alertMessage);
                    return;
                });
        }

        
        function submitForm(originalForm) {
            console.log(originalForm);
            $.post({
                    url: $(originalForm).attr('action'),
                    data: new FormData(originalForm),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false
                })
                .done(response => {
                    $(modal).modal('hide');
                    showAlert(response.message, 'success');
                    table.ajax.reload();
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                    showAlert(errors.responseJSON.message, 'danger');
                });
        }

        function deleteData(url) {
            if (confirm('Apakah data akan dihapus?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    success: (response) => {
                        showAlert(response.message, 'success');
                        table.ajax.reload();
                    },
                    error: (errors) => {
                        var alertMessage = "Tidak dapat menghapus data";
                        showAlert(alertMessage, 'danger');                   
                    }
                });
            }
        }

        function downloadFile(url) {
            $.ajax({
                url: url,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    var filename = "";                   
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) { 
                        filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(data);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function() {
                    alert('File not found or an error occurred.');
                }
            });
        }
    </script>
@endpush