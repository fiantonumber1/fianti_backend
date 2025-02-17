<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Ruang Rapat Hari Ini</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div id="scheduler-{{ $projectName }}" style="height: 600px; width: 100%;"></div>
                        <!-- Adjust height and width as needed -->


                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Progress Memo</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="canvas3-detailed-{{ $projectName }}"
                            style="min-height: 500px; height: 500px; max-height: 500px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

        </div>




        <div class="row mb-2">

        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Progress Dokumen</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="canvas3-progressreport-detailed-{{ $projectName }}"
                            style="min-height: 500px; height: 500px; max-height: 500px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Progress BOM</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="canvas31-detailed-{{ $projectName }}"
                            style="min-height: 500px; height: 500px; max-height: 500px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
















    </div>
</section>