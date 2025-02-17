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
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach ($revisiall as $keyan => $revisi)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{ $keyan }}"
                        class="{{ $loop->first ? 'active' : '' }}"></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach ($revisiall as $keyan => $revisi)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    @php
                                        if ($keyan !== 'All') {
                                            $newreports = $revisi['newreports'];
                                            $totalpersentaseeksternalall = 0;
                                            $totalpersentaseinternalall = 0;
                                            $totaldocument = 0;
                                            $totalunit = 0;
                                            foreach ($newreports as $newreport) {
                                                $totalunit += 1;
                                            }

                                            foreach ($newreports as $newreport) {
                                                if (
                                                    ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") ||
                                                    ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") ||
                                                    ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") ||
                                                    ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") ||
                                                    ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")
                                                ) {
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
                                    <div class="card-header">
                                        <table class="table table-bordered my-2 table-responsive-">
                                            <tbody>
                                                <tr>
                                                    <td rowspan="4" style="width: 25%" class="text-center">
                                                        <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                                            style="max-width: 250px">
                                                    </td>
                                                    <td rowspan="4" style="width: 50%">
                                                        <h1 class="text-xl text-center mt-2">DAFTAR PROGRES</h1>
                                                    </td>
                                                    <td style="width: 25%" class="p-1">Project:
                                                        <b>{{ ucwords(str_replace('-', ' ', $keyan)) }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b></td>
                                                </tr>
                                                <tr>
                                                    @if(session('internalon'))
                                                        <td style="width: 25%" class="p-1">
                                                            Progres: <b><span
                                                                    class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}"
                                                                    style="font-size: 2rem;">{{$totalpersentaseinternalall}} %</span></b>
                                                        </td>
                                                    @else
                                                        <td style="width: 25%" class="p-1">
                                                            Progres: <b><span
                                                                    class="badge {{ session('internalon') ? 'badge-warning' : 'badge-success' }}"
                                                                    style="font-size: 2rem;">{{$totalpersentaseeksternalall}} %</span></b>
                                                        </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td style="width: 25%" class="p-1">
                                                        Total Dokumen: <b><span class="badge badge-info"
                                                                style="font-size: 1.5rem;">{{$totaldocument}}</span></b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-body">

                                        @include('newreports.index.table', ['newreports' => $revisi['newreports'], 'keyan' => $keyan])
                                    </div>
                                </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>




<script>
    document.getElementById('revisiDropdown').addEventListener('change', function () {
        const selectedRevisi = this.value;
        document.querySelectorAll('.tab-pane').forEach(function (tabPane) {
            if (tabPane.id === 'custom-tabs-one-' + selectedRevisi) {
                tabPane.classList.add('show', 'active');
            } else {
                tabPane.classList.remove('show', 'active');
            }
        });
    });
</script>
@endsection