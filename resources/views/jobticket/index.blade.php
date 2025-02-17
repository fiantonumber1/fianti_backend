@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card card-danger card-outline">
                <div class="card-header">
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <h3 class="card-title text-bold">Page monitoring jobticket <span
                            class="badge badge-info ml-1"></span></h3>
                </div>
                <div class="card-body">
                    <!-- Dropdown for project selection -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select Project
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @foreach ($revisiall as $keyan => $revisi)
                                <a class="dropdown-item @if($loop->first) active @endif" href="#" data-toggle="tab"
                                    data-target="#custom-tabs-one-{{ $keyan }}">{{ $keyan }}</a>
                            @endforeach
                        </div>
                    </div>



                    <button type="button" class="btn btn-success" onclick="addDocument()">Add Document</button>


                    <!-- Content for each project -->
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        @foreach ($revisiall as $keyan => $revisi)
                                                <div class="tab-pane fade @if($loop->first) show active @endif"
                                                    id="custom-tabs-one-{{ $keyan }}" role="tabpanel"
                                                    aria-labelledby="custom-tabs-one-{{ $keyan }}-tab">
                                                    <div class="card card-outline card-danger">
                                                        <div class="card-header">
                                                            <table class="table table-bordered my-2 table-responsive-">
                                                                <tbody>
                                                                    <tr>
                                                                        <td rowspan="4" style="width: 25%" class="text-center">
                                                                            <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo"
                                                                                class="p-2" style="max-width: 250px">
                                                                        </td>
                                                                        <td rowspan="4" style="width: 50%">
                                                                            <h1 class="text-xl text-center mt-2">List Jobticket</h1>
                                                                        </td>
                                                                        <td style="width: 25%" class="p-1">Project:
                                                                            <b>{{ ucwords(str_replace('-', ' ', $keyan)) }}</b>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                    </tr>
                                                                    <tr>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>



                                                        <div class="card-body">
                                                            <table id="example2-{{ $keyan }}"
                                                                class="table table-bordered table-hover table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>
                                                                            <span class="checkbox-toggle" id="checkAll"><i
                                                                                    class="far fa-square"></i></span>
                                                                        </th>
                                                                        <th>No</th>
                                                                        <th scope="col">Unit</th>
                                                                        <th scope="col">Nama Proyek</th>
                                                                        <th scope="col">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $counterdokumen = 1;
                                                                    @endphp
                                                                    @foreach ($revisi['alljobticket'] as $newreport)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="icheck-primary">
                                                                                    <input type="checkbox" value="{{ $newreport->id }}"
                                                                                        name="document_ids[]" id="checkbox{{ $newreport->id }}">
                                                                                    <label for="checkbox{{ $newreport->id }}"></label>
                                                                                </div>
                                                                            </td>
                                                                            <td>{{ $counterdokumen++ }}</td>
                                                                            <td>{{ $newreport->unit->name }}</td>
                                                                            <td>{{ $newreport->projectType->title }}</td>
                                                                            <td>
                                                                                <a href="{{ route('jobticket.show', $newreport->id) }}"
                                                                                    class="btn btn-primary">View</a>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>


        function addDocument() {
            const user = @json($useronly);
            const rule = user.rule.replace('Manager ', '').trim();

            const listproject = @json($listproject); // Data proyek dari server
            const documentKinds = @json($documentKinds); // Jenis dokumen dari server
            const units = @json($units).filter(unit => unit.name === rule); // Ambil unit sesuai rule
            const routeUrl = "{{ route('jobticket.AddDocument') }}"; // Laravel Route
            const users = @json($users).filter(user => user.rule.includes(rule)); // Ambil user sesuai rule


            Swal.fire({
                title: 'Tambah Dokumen',
                html: `
            <form id="addDocumentForm">
                <div class="form-group text-left">
                    <label for="project">Proyek</label>
                    <select id="project" class="form-control">
                        ${listproject.map(project => `<option value="${project.id}">${project.title}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group text-left">
                    <label for="unit">Unit</label>
                    <select id="unit" class="form-control">
                        ${units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group text-left">
                    <label for="noDokumen">No Dokumen</label>
                    <div class="input-group">
                        <input type="text" id="noDokumen" class="form-control" placeholder="Masukkan No Dokumen">
                    </div>
                    <div class="input-group mt-2">
                        <button type="button" id="generateNoDokumen" class="btn btn-outline-secondary">Generate (Jika No Dokumen Tidak Ada)</button>
                    </div>
                </div>
                <div class="form-group text-left">
                    <label for="namaDokumen">Nama Dokumen</label>
                    <input type="text" id="namaDokumen" class="form-control" placeholder="Masukkan Nama Dokumen">
                </div>
                <div class="form-group text-left">
                    <label for="revisi">Revisi ke (Akan Dikerjakan)</label>
                    <input type="text" id="revisi" class="form-control" placeholder="Masukkan Revisi">
                </div>
                <div class="form-group text-left">
                    <label for="jenisDokumen">Jenis Dokumen</label>
                    <select id="jenisDokumen" class="form-control">
                        ${documentKinds.map(kind => `<option value="${kind.id}">${kind.name}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group text-left">
                    <label for="drafter">Drafter</label>
                    <select id="drafter" class="form-control">
                        ${users.map(user => `<option value="${user.id}">${user.name}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group text-left">
                    <label for="checker">Checker</label>
                    <select id="checker" class="form-control">
                        ${users.map(user => `<option value="${user.id}">${user.name}</option>`).join('')}
                    </select>
                </div>
            </form>
        `,
                confirmButtonText: 'Simpan',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                didRender: () => {
                    document.getElementById('generateNoDokumen').addEventListener('click', () => {
                        const uniqueNumber = `DOC-${Date.now()}-${uniqid()}`;
                        document.getElementById('noDokumen').value = uniqueNumber;
                    });
                },
                preConfirm: () => {
                    const project = Swal.getPopup().querySelector('#project').value;
                    const unit = Swal.getPopup().querySelector('#unit').value;
                    const noDokumen = Swal.getPopup().querySelector('#noDokumen').value.trim();
                    const namaDokumen = Swal.getPopup().querySelector('#namaDokumen').value.trim();
                    const revisi = Swal.getPopup().querySelector('#revisi').value.trim();
                    const jenisDokumen = Swal.getPopup().querySelector('#jenisDokumen').value;
                    const drafter = Swal.getPopup().querySelector('#drafter').value;
                    const checker = Swal.getPopup().querySelector('#checker').value;

                    if (!noDokumen || !namaDokumen || !revisi || !unit || !jenisDokumen || !project) {
                        Swal.showValidationMessage('Semua kolom wajib diisi.');
                        return false;
                    }

                    return { project, unit, noDokumen, namaDokumen, revisi, jenisDokumen, drafter, checker };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { project, unit, noDokumen, namaDokumen, revisi, jenisDokumen, drafter, checker } = result.value;

                    $.ajax({
                        url: routeUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            proyek_type_id: project,
                            unit_id: unit,
                            jobticket_documentkind_id: jenisDokumen,
                            documentnumber: noDokumen,
                            documentname: namaDokumen,
                            rev: revisi,
                            drafter: drafter || null,
                            checker: checker || null
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menambah dokumen.',
                            });
                        }
                    });
                }
            });

        }

        // Fungsi untuk menghasilkan uniqid di JavaScript
        function uniqid() {
            const time = Date.now();
            const random = Math.floor(Math.random() * 1000000);
            return `${time.toString(16)}${random.toString(16)}`;
        }



        // function addDocument() {
        //     const user = @json($useronly);
        //     const rule = user.rule.replace('Manager ', '').trim();

        //     const listproject = @json($listproject); // Data proyek dari server
        //     const documentKinds = @json($documentKinds); // Jenis dokumen dari server
        //     const units = @json($units).filter(unit => unit.name === rule); // Ambil unit sesuai rule
        //     const routeUrl = "{{ route('jobticket.AddDocument') }}"; // Laravel Route
        //     const users = @json($users).filter(user => user.rule.includes(rule)); // Ambil user sesuai rule


        //     Swal.fire({
        //         title: 'Tambah Dokumen',
        //         html: `
        //                                                                                                                         <form id="addDocumentForm">
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="project">Proyek</label>
        //                                                                                                                                 <select id="project" class="form-control">
        //                                                                                                                                     ${listproject.map(project => `<option value="${project.id}">${project.title}</option>`).join('')}
        //                                                                                                                                 </select>
        //                                                                                                                             </div>
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="unit">Unit</label>
        //                                                                                                                                 <select id="unit" class="form-control">
        //                                                                                                                                     ${units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
        //                                                                                                                                 </select>
        //                                                                                                                             </div>
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="noDokumen">No Dokumen</label>
        //                                                                                                                                 <div class="input-group">
        //                                                                                                                                     <input type="text" id="noDokumen" class="form-control" placeholder="Masukkan No Dokumen">
        //                                                                                                                                 </div>
        //                                                                                                                                 <div class="input-group mt-2">
        //                                                                                                                                     <button type="button" id="generateNoDokumen" class="btn btn-outline-secondary">Generate (Jika No Dokumen Tidak Ada)</button>
        //                                                                                                                                 </div>
        //                                                                                                                             </div>
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="namaDokumen">Nama Dokumen</label>
        //                                                                                                                                 <input type="text" id="namaDokumen" class="form-control" placeholder="Masukkan Nama Dokumen">
        //                                                                                                                             </div>
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="revisi">Revisi ke (Akan Dikerjakan)</label>
        //                                                                                                                                 <input type="text" id="revisi" class="form-control" placeholder="Masukkan Revisi">
        //                                                                                                                             </div>
        //                                                                                                                             <div class="form-group text-left">
        //                                                                                                                                 <label for="jenisDokumen">Jenis Dokumen</label>
        //                                                                                                                                 <select id="jenisDokumen" class="form-control">
        //                                                                                                                                     ${documentKinds.map(kind => `<option value="${kind.id}">${kind.name}</option>`).join('')}
        //                                                                                                                                 </select>
        //                                                                                                                             </div>
        //                                                                                                                             ${user.rule.includes('Manager')
        //                 ? `
        //                                                                                                                                         <div class="form-group text-left">
        //                                                                                                                                             <label for="drafter">Drafter</label>
        //                                                                                                                                             <select id="drafter" class="form-control">
        //                                                                                                                                                 ${users.map(user => `<option value="${user.id}">${user.name}</option>`).join('')}
        //                                                                                                                                             </select>
        //                                                                                                                                         </div>

        //                                                                                                                                         <div class="form-group text-left">
        //                                                                                                                                             <label for="checker">Checker</label>
        //                                                                                                                                             <select id="checker" class="form-control">
        //                                                                                                                                                 ${users.map(user => `<option value="${user.id}">${user.name}</option>`).join('')}
        //                                                                                                                                             </select>
        //                                                                                                                                         </div>
        //                                                                                                                                     `
        //                 : ''
        //             }
        //                                                                                                                         </form>
        //                                                                                                                     `,
        //         confirmButtonText: 'Simpan',
        //         showCancelButton: true,
        //         cancelButtonText: 'Batal',
        //         didRender: () => {
        //             // Tambahkan event listener untuk tombol generate
        //             document.getElementById('generateNoDokumen').addEventListener('click', () => {
        //                 const uniqueNumber = `DOC-${Date.now()}-${uniqid()}`;
        //                 document.getElementById('noDokumen').value = uniqueNumber; // Menampilkan ke input
        //             });
        //         },
        //         preConfirm: () => {
        //             const project = Swal.getPopup().querySelector('#project').value;
        //             const unit = Swal.getPopup().querySelector('#unit').value;
        //             const noDokumen = Swal.getPopup().querySelector('#noDokumen').value.trim();
        //             const namaDokumen = Swal.getPopup().querySelector('#namaDokumen').value.trim();
        //             const revisi = Swal.getPopup().querySelector('#revisi').value.trim();
        //             const jenisDokumen = Swal.getPopup().querySelector('#jenisDokumen').value;

        //             let drafter = null;
        //             let checker = null;
        //             if (user.rule.includes('Manager')) {
        //                 drafter = Swal.getPopup().querySelector('#drafter').value;
        //                 checker = Swal.getPopup().querySelector('#checker').value;
        //             }

        //             // Validate input
        //             if (!noDokumen || !namaDokumen || !revisi || !unit || !jenisDokumen || !project) {
        //                 Swal.showValidationMessage('Semua kolom wajib diisi.');
        //                 return false;
        //             }

        //             return { project, unit, noDokumen, namaDokumen, revisi, jenisDokumen, drafter, checker };

        //         }
        //     }).then((result) => {
        //         if (result.isConfirmed) {

        //             const { project, unit, noDokumen, namaDokumen, revisi, jenisDokumen, drafter, checker } = result.value;


        //             // Send data to server using AJAX
        //             $.ajax({
        //                 url: routeUrl,
        //                 method: 'POST',
        //                 data: {
        //                     _token: '{{ csrf_token() }}', // Add CSRF token
        //                     proyek_type_id: project,
        //                     unit_id: unit,
        //                     jobticket_documentkind_id: jenisDokumen,
        //                     documentnumber: noDokumen,
        //                     documentname: namaDokumen,
        //                     rev: revisi,
        //                     drafter: drafter || null,
        //                     checker: checker || null
        //                 },
        //                 success: function (response) {
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: 'Berhasil',
        //                         text: response.message,
        //                     }).then(() => {
        //                         location.reload(); // Refresh page
        //                     });
        //                 },
        //                 error: function (xhr) {
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: 'Gagal',
        //                         text: 'Terjadi kesalahan saat menambah dokumen.',
        //                     });
        //                 }
        //             });
        //         }
        //     });
        // }

        // // Fungsi untuk menghasilkan uniqid di JavaScript
        // function uniqid() {
        //     const time = Date.now();
        //     const random = Math.floor(Math.random() * 1000000);
        //     return `${time.toString(16)}${random.toString(16)}`;
        // }


    </script>



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

        $(document).ready(function () {
            // Handle dropdown item click
            $('.dropdown-item').click(function () {
                var target = $(this).data('target');
                $('.tab-pane').removeClass('show active');
                $(target).addClass('show active');
            });
        });


    </script>
@endpush