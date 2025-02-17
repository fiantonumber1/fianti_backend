<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal" id="" enctype="multipart/form-data">
            @method('put')
            @csrf

            <div class="modal-content card card-indigo">
                <div class="modal-header card-header">
                <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <label for="file_name" class="col-lg-12 control-label"><i class="fas fa-store-alt"></i> Nama Dokumen<span style="color:red; font-size:20px">*</span></label>
                        <div class="col-lg-12">
                            <input type="text" name="file_name" id="file_name" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="file_code" class="col-lg-12 control-label"><i class="fas fa-map-marked-alt"></i> Kode Dokumen<span style="color:red; font-size:20px">*</span></label>
                        <div class="col-lg-12">
                            <input name="file_code" id="file_code" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="path_file">Pilih Dokumen<span style="color:red; font-size:20px">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="path_file" class="custom-file-input" id="path_file" onchange="preview('.preview-path_file', this.files[0])">
                                    <label class="custom-file-label" for="path_file">Pilih Dokumen</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <object data="" type="application/pdf" class="preview-path_file" style="display: none; width: 100%; height: 600px;"></object>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="project_id" class="col-lg-12 control-label">
                            <i class="fas fa-store-alt"></i> Nama Proyek<span style="color:red; font-size:20px">*</span>
                        </label>
                        <div class="col-lg-12">
                            <select name="project_id" id="project_id" class="form-control" required autofocus>
                                <option>--Pilih Proyek--</option>
                                @foreach($project as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary col-lg-4" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> {{ __('back.Kembali') }}
                    </button>
                    <button type="button" class="btn btn-success col-lg-4"  onclick="submitForm(this.form)">
                         {{ __('back.Simpan') }} <i class="fa fa-arrow-circle-right"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

