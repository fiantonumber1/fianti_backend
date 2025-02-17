@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class                                   
@endphp


@section('container2') 
<div id="encoded-data" data-listprogressnodokumen="{{ json_encode($listdocuments) }}"></div>
<div id="encoded-data-memo" data-listprogressnodokumen="{{ json_encode($newmemo) }}"></div>

<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a href="">Tugas</a></li>

                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')

<div class="row justify-content-center">
    <div class="col-lg-18">

        <div class="card card-outline card-danger">
            <div class="card-header">
                <table class="table table-bordered table-hover mt-4">
                    <tbody>
                        <tr>
                            <td rowspan="7" class="text-center" style="width: 25%;">
                                <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                    style="max-width: 250px;">
                            </td>
                            <td rowspan="7" style="width: 50%;">
                                <h1 class="text-xl text-center mt-2">List Tugas</h1>
                                <h1 class="text-xl text-center mt-2">{{$useronly->name}}</h1>
                            </td>

                        </tr>

                        <tr>
                            <td style="width: 25%;" class="p-1">
                                Tanggal: <b>{{ date('d F Y') }}</b>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="card-body">

                <ul class="nav nav-tabs" id="jobticketTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="drafter-tab" data-toggle="tab" href="#drafter" role="tab"
                            aria-controls="drafter" aria-selected="true">Drafter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="checker-tab" data-toggle="tab" href="#checker" role="tab"
                            aria-controls="checker" aria-selected="false">Checker</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="approver-tab" data-toggle="tab" href="#approver" role="tab"
                            aria-controls="approver" aria-selected="false">Approver</a>
                    </li>
                </ul>


                <div class="tab-content" id="jobticketTabContent">
                    <button type="button" class="btn btn-success" onclick="addDocument()">Add
                        Document</button>

                    <!-- Tab Drafter -->
                    <div class="tab-pane fade show active" id="drafter" role="tabpanel" aria-labelledby="drafter-tab">

                        @include('jobticket.showdocumentself.table', ['jobtickets' => $drafterJobtickets, 'statusposition' => 'drafter'])


                    </div>
                    <div class="tab-pane fade" id="checker" role="tabpanel" aria-labelledby="checker-tab">

                        @include('jobticket.showdocumentself.table', ['jobtickets' => $checkerJobtickets, 'statusposition' => 'checker'])

                    </div>
                    <div class="tab-pane fade" id="approver" role="tabpanel" aria-labelledby="approver-tab">
                        @include('jobticket.showdocumentself.table', ['jobtickets' => $approverJobtickets, 'statusposition' => 'approver'])
                    </div>
                </div>



            </div>
        </div>

    </div>
</div>

@endsection


@push('css')
    <style>
        .badgekhusus {
            display: inline-block;
            padding: 5px 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #28a745;
            border-radius: 20px;
            transition: background-color 0.5s ease, transform 0.3s ease;
        }

        .badgekhusus.paused {
            background-color: #dc3545;
        }

        .badgekhusus.critical {
            background-color: #ffc107;
            color: #000;
        }

        #elapsed_time_{{ $jobticket->id ?? 1 }} {
            text-align: center;
        }

        .badge:hover {
            transform: scale(1.1);
        }

        .large-swal {
            width: 800px !important;
            /* Lebar yang lebih besar */
            height: auto !important;
            /* Tinggi menyesuaikan isi */
            max-height: 90vh !important;
            /* Batasi agar tidak terlalu tinggi */
            overflow-y: auto !important;
            /* Tambahkan scroll jika isinya terlalu banyak */
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @foreach($projects as $project)
        <div class="project-table" id="project-{{ $project->id }}" style="display: none;">
            <table id="example2-{{ $project->id }}" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Dibuat</th>
                        <th>Project</th>
                        <th>Nomor Dokumen</th>
                        <th>Nama Dokumen</th>
                        <th>Jenis Dokumen</th>
                        <th>Rev ke</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Informasi User</th>
                        <th>Dokumen pendukung (Vault)</th>
                        <th>Waktu Mulai</th>
                        <th>Countup</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php    $urutan = 1; @endphp
                    @foreach($drafterJobtickets->where('jobticketIdentity.jobticketPart.projectType.id', $project->id) as $index => $jobticket)
                        <tr>
                            <td>{{ $urutan++ }}</td>
                            <td>{{ Carbon::parse($jobticket->created_at)->format('d/m/Y') }}</td>
                            <td>{{ $jobticket->jobticketIdentity->jobticketPart->projectType->title }}</td>
                            <!-- Kode lainnya -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTables untuk setiap tabel dengan ID dinamis
            $('#example2').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: 'no-sort' } // Non-aktifkan pengurutan pada kolom dengan kelas "no-sort"
                ]
            });

            // Tampilkan tabel project pertama kali
            $('#projectSelect').val($('#projectSelect option:first').val()).trigger('change');

            // Script untuk menampilkan tabel sesuai project yang dipilih
            $('#projectSelect').on('change', function () {
                let selectedProject = $(this).val();
                $('.project-table').hide();  // Sembunyikan semua tabel project
                $('#' + selectedProject).show();  // Tampilkan tabel yang dipilih
            });
        });
    </script>

    <script>
        // Object to store intervals
        var intervals = {};

        // Function to update elapsed time
        function updateElapsedTime1(id, startTime, initialSeconds) {
            var elapsedTimeElement = document.getElementById('elapsed_time_' + id);

            // Function to format elapsed time
            function formatElapsedTime(seconds) {
                var hours = Math.floor(seconds / 3600);
                var minutes = Math.floor((seconds % 3600) / 60);
                var remainingSeconds = seconds % 60;
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
            }

            // Function to calculate elapsed time
            function calculateElapsedTime() {
                var now = new Date();
                var start = new Date(startTime);
                var elapsed = Math.floor((now - start) / 1000) + initialSeconds;
                return elapsed;
            }

            // Update elapsed time element
            function updateElapsedTime() {
                var elapsedSeconds = calculateElapsedTime();
                elapsedTimeElement.textContent = formatElapsedTime(elapsedSeconds);
            }

            // Initial update and log the initial state
            updateElapsedTime();
            console.log(`Initial update for id ${id}:`, elapsedTimeElement.textContent);

            // Clear existing interval if it exists
            if (intervals[id]) {
                clearInterval(intervals[id]);
            }

            // Update elapsed time periodically and store the interval
            intervals[id] = setInterval(function () {
                updateElapsedTime();
                console.log(`Updated time for id ${id}:`, elapsedTimeElement.textContent);
            }, 1000);
        }

        // Event listener when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($drafterJobtickets as $index => $item)
                    @php
                        $id = $item->id;
                        $temporystatus = $item->jobticketStarted;
                        $elapsedSeconds = $temporystatus->total_elapsed_seconds ?? 0;
                        $startTime = $temporystatus->start_time_run ?? null;
                        $pauseTime = $temporystatus->pause_time_run ?? null;
                    @endphp
                    @if($startTime != null && $pauseTime == null)
                        var elapsedTimeElement = document.getElementById('elapsed_time_{{ $id }}');
                        var kondisional = elapsedTimeElement ? elapsedTimeElement.textContent : '';
                        if (kondisional !== "Paused" && kondisional !== "Completed" && kondisional !== "Time up tidak berjalan") {
                            updateElapsedTime1('{{ $id }}', '{{ $startTime }}', {{ $elapsedSeconds }});
                        }
                    @endif
            @endforeach
        });
    </script>
    <script>

        var encodedDataElement = document.getElementById('encoded-data');
        var listprogressnodokumenDecoded = JSON.parse(encodedDataElement.dataset.listprogressnodokumen);
        listprogressnodokumenDecoded.unshift('');

        var selectedDocuments = {}; // Menggunakan objek untuk menyimpan dokumen yang dipilih

        @foreach($drafterJobtickets as $jobticket)
            selectedDocuments['{{ $jobticket->id }}'] = new Set(); // Set untuk setiap jobticket
        @endforeach



        function pickdocument(jobticketId, documentSupport, urutan) {
            // Ambil dokumen dari parameter documentSupport
            var existingDocuments = JSON.parse(documentSupport);

            // Membuat array baru berisi format yang diinginkan
            var existingDocumentValues = existingDocuments.map(doc => {
                return `${doc.id}@${doc.namadokumen}@${doc.nodokumen}@${doc.rev}`; // Format sesuai permintaan
            });

            // Mengisi selectedDocuments dengan existingDocumentValues
            existingDocumentValues.forEach(doc => {
                if (!selectedDocuments[jobticketId]) {
                    selectedDocuments[jobticketId] = new Set(); // Inisialisasi Set jika belum ada
                }
                selectedDocuments[jobticketId].add(doc); // Tambahkan dokumen ke Set
            });

            var listprogressnodokumen = listprogressnodokumenDecoded;

            function loadOptions(searchTerm, pageIndex, pageSize, list) {
                searchTerm = searchTerm.toLowerCase();
                var startIndex = pageIndex * pageSize;
                var endIndex = startIndex + pageSize;
                var filteredList = list.filter(item => item.toLowerCase().includes(searchTerm));
                var optionsHtml = '';

                for (var i = startIndex; i < endIndex && i < filteredList.length; i++) {
                    var listItem = filteredList[i].split('@'); // Memecah string menjadi array
                    var namadoc = listItem[1] || ''; // Ambil nama dokumen dari bagian kedua
                    var nodoc = listItem[2] || ''; // Ambil nama dokumen dari bagian kedua
                    var rev = listItem[3] || ''; // Ambil versi dari bagian ketiga
                    var checked = selectedDocuments[jobticketId]?.has(filteredList[i]) ? 'checked' : ''; // Cek jika dokumen sudah ada

                    optionsHtml += `<label><input type="checkbox" value="${filteredList[i]}" class="progress-checkbox" ${checked}> ${namadoc} - ${nodoc}- ${rev}</label><br>`;
                }
                return optionsHtml;
            }

            var currentPageIndex = 0;
            var pageSize = 5;
            var progressnodokumenOptionsHtml = loadOptions('', currentPageIndex, pageSize, listprogressnodokumen);

            var html = `
                                                                                                                                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                                                                                                                                            <label for="edit-progressnodokumen">Tambahkan Dokumen Turunanannya</label>
                                                                                                                                                            <div id="checkbox-list" style="max-height: 200px; overflow-y: auto;">
                                                                                                                                                                ${progressnodokumenOptionsHtml}
                                                                                                                                                            </div>
                                                                                                                                                            <input type="text" id="progressnodokumen-search" class="swal2-input" placeholder="Search progress...">
                                                                                                                                                            <div id="selected-documents" style="margin-top: 10px;"></div>
                                                                                                                                                            <div id="progressnodokumen-pagination" style="margin-top: 10px;">
                                                                                                                                                                <button id="prev-progressnodokumen-page" ${currentPageIndex === 0 ? 'disabled' : ''}>Previous</button>
                                                                                                                                                                <button id="next-progressnodokumen-page" ${currentPageIndex * pageSize + pageSize >= listprogressnodokumen.length ? 'disabled' : ''}>Next</button>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                `;

            function updateSelectedDocuments() {
                var selectedHtml = Array.from(selectedDocuments[jobticketId] || []).map(doc => `<li>${doc}</li>`).join('');
                document.getElementById('selected-documents').innerHTML = selectedHtml ? `<ul>${selectedHtml}</ul>` : '';
            }

            Swal.fire({
                title: "Edit Dokumen",
                html: html,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                customClass: {
                    popup: 'large-swal'  // Kelas CSS khusus untuk mengatur ukuran
                },
                preConfirm: () => {
                    return Array.from(selectedDocuments[jobticketId] || []);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var newDocument = result.value;
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin memperbarui data ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, perbarui!',
                        cancelButtonText: 'Batal'
                    }).then((updateConfirmation) => {
                        if (updateConfirmation.isConfirmed) {
                            var updateUrl = `/jobticket/updatesupportdocument/${jobticketId}/`;
                            $.ajax({
                                url: updateUrl,
                                method: 'PUT',
                                data: {
                                    documentsupport: newDocument,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data berhasil diperbarui!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });

                                    // Kosongkan selectedDocuments dan perbarui dengan dokumen baru dari server
                                    selectedDocuments[jobticketId].clear();
                                    response.data.forEach(function (doc) {
                                        var formattedDoc = `${doc.id}@${doc.namadokumen}@${doc.nodokumen}@${doc.rev}`;
                                        selectedDocuments[jobticketId].add(formattedDoc);
                                    });

                                    // Update bagian dokumen tanpa reload
                                    var container = $('#document-support-container-' + urutan);
                                    container.html('');
                                    response.data.forEach(function (doc) {
                                        container.append(`<span class="badge bg-info">${doc.namadokumen} - ${doc.nodokumen} - ${doc.rev}</span><br>`);
                                    });

                                    // Setelah data diupdate, panggil updateSelectedDocuments untuk memperbarui tampilan dokumen yang dipilih
                                    updateSelectedDocuments();
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan',
                                        text: 'Gagal memperbarui data. Silakan coba lagi.'
                                    });
                                }
                            });
                        }
                    });
                }
            });

            document.getElementById('progressnodokumen-search').addEventListener('input', function () {
                var searchTerm = this.value;
                var filteredOptionsHtml = loadOptions(searchTerm, currentPageIndex, pageSize, listprogressnodokumen);
                document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                updateSelectedDocuments(); // Update selected documents list
            });

            document.getElementById('prev-progressnodokumen-page').addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex--;
                    var filteredOptionsHtml = loadOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen);
                    document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                    updateSelectedDocuments();
                    document.getElementById('prev-progressnodokumen-page').disabled = currentPageIndex === 0; // Disable button if on first page
                    document.getElementById('next-progressnodokumen-page').disabled = (currentPageIndex * pageSize + pageSize) >= listprogressnodokumen.length; // Disable if last page
                }
            });

            document.getElementById('next-progressnodokumen-page').addEventListener('click', function () {
                if ((currentPageIndex + 1) * pageSize < listprogressnodokumen.length) {
                    currentPageIndex++;
                    var filteredOptionsHtml = loadOptions(document.getElementById('progressnodokumen-search').value, currentPageIndex, pageSize, listprogressnodokumen);
                    document.getElementById('checkbox-list').innerHTML = filteredOptionsHtml;
                    updateSelectedDocuments();
                    document.getElementById('prev-progressnodokumen-page').disabled = currentPageIndex === 0; // Disable button if on first page
                    document.getElementById('next-progressnodokumen-page').disabled = ((currentPageIndex + 1) * pageSize) >= listprogressnodokumen.length; // Disable if last page
                }
            });

            document.getElementById('checkbox-list').addEventListener('change', function (event) {
                if (event.target.classList.contains('progress-checkbox')) {
                    var value = event.target.value;
                    if (event.target.checked) {
                        selectedDocuments[jobticketId].add(value); // Tambahkan dokumen ke Set
                    } else {
                        selectedDocuments[jobticketId].delete(value); // Hapus dokumen dari Set
                    }
                    updateSelectedDocuments(); // Update tampilan dokumen yang dipilih
                }
            });

            // Memperbarui tampilan dokumen yang dipilih saat dialog dibuka
            updateSelectedDocuments();
        }




        function pick(id, posisitable) {
            // Fetch available users (this should be done via an API call ideally)
            var users = @json($availableUsers); // Assuming you pass available users as JSON
            let positionOptions = ['approver', 'checker']; // Positions to select

            let userOptions = users.map(user => {
                return {
                    id: user.id,
                    name: user.name
                };
            });

            let userOptionsHtml = userOptions.map(option => {
                return `<option value="${option.id}">${option.name}</option>`;
            }).join('');

            let positionOptionsHtml = positionOptions.map(option => {
                return `<option value="${option}">${option.charAt(0).toUpperCase() + option.slice(1)}</option>`;
            }).join('');

            Swal.fire({
                title: 'Pilih Checker atau Approver',
                html: `
                                                                                                                                                        <div>
                                                                                                                                                            <label for="userSelect">Pilih Pengguna:</label>
                                                                                                                                                            <select id="userSelect" class="swal2-select">
                                                                                                                                                                ${userOptionsHtml}
                                                                                                                                                            </select>
                                                                                                                                                        </div>
                                                                                                                                                        <div style="margin-top: 10px;">
                                                                                                                                                            <label for="positionSelect">Pilih Posisi:</label>
                                                                                                                                                            <select id="positionSelect" class="swal2-select">
                                                                                                                                                                ${positionOptionsHtml}
                                                                                                                                                            </select>
                                                                                                                                                        </div>`,
                showCancelButton: true,
                confirmButtonText: 'Pilih',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return {
                        selectedUserId: document.getElementById('userSelect').value,
                        selectedPosition: document.getElementById('positionSelect').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let selectedUserId = result.value.selectedUserId;
                    let selectedUserName = users.find(user => user.id == selectedUserId).name;
                    let selectedPosition = result.value.selectedPosition;

                    // Proceed with the AJAX call to update the checker or approver
                    picktugas(id, posisitable, selectedUserName, selectedPosition);
                }
            });
        }

        function picktugas(id, posisitable, name, kindposition) {
            var picktugasUrl = `/jobticket/picktugas/${id}/${name}/${kindposition}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengambil pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ambil job ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: picktugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (kindposition === "drafter") {
                                $(`#drafter_${id}_${posisitable}`).text(name);
                                $(`#button_${id}_${posisitable}`)
                                    .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                    .html('<i class="fas fa-rocket"></i> Start Tugas')
                                    .removeClass('btn-success')
                                    .addClass('btn-warning');
                            } else {
                                if (kindposition === "checker") {
                                    // Set the name of the checker and hide the button
                                    $(`#checker_${id}_${posisitable}`).text(name);
                                    $(`#checkerbutton_${id}_${posisitable}`).remove();
                                } else if (kindposition === "approver") {
                                    // Set the name of the approver and hide the button
                                    $(`#approver_${id}_${posisitable}`).text(name);
                                    $(`#approverbutton_${id}_${posisitable}`).remove();
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pekerjaan berhasil diambil!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengambil pekerjaan ini.',
                            });
                        }
                    });
                }
            });
        }

        function starttugas(id, posisitable, name) {
            var starttugasUrl = `/jobticket/starttugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin memulai pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, mulai job ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: starttugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil dimulai!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `pausetugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-pause-circle"></i> Pause')
                                .removeClass('btn-warning')
                                .addClass('btn-secondary');
                            var startTime = new Date().toISOString();
                            var elapsedSeconds = response.elapsedSeconds || 0;
                            updateElapsedTime1(id, startTime, elapsedSeconds);

                            $(`#selesai_button_${id}_${posisitable}`).removeClass('d-none');
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        var encodeddatamemo = JSON.parse(document.getElementById('encoded-data-memo').dataset.listprogressnodokumen);



        function picknote(jobticketId, documentSupport, urutan, name) {
            var picknoteUrl = "{{ route('jobticket.picknote', ['id' => ':id']) }}".replace(':id', jobticketId);

            // Fungsi untuk memperbarui warna dan teks tombol
            function updateButtonColor() {
                const button = $(`#button_${jobticketId}_${urutan}`);
                button.attr("onclick", `starttugas('${jobticketId}', '${urutan}', '${name}')`)
                    .html('<i class="fas fa-rocket"></i> Start Tugas')
                    .removeClass('bg-gray')
                    .addClass('btn-warning');

                // Ubah tombol Pick Document menjadi abu-abu
                $(`#documentpickerbutton_${jobticketId}_${urutan}`).attr("onclick", `pickdocument('${jobticketId}', '${documentSupport}', '${urutan}', '${name}')`)
                    .removeClass('bg-purple')
                    .addClass('bg-gray');

                // Ubah tombol Pick Note menjadi abu-abu
                $(`#notebutton_${jobticketId}_${urutan}`).removeClass('bg-orange').addClass('bg-gray');
            }
            Swal.fire({
                title: 'Tambahkan Catatan',
                html: '<textarea id="note" class="swal2-textarea" placeholder="Tambahkan catatan (opsional)" rows="10" style="width: 75%;"></textarea>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Simpan catatan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'large-swal'  // Kelas CSS khusus untuk mengatur ukuran
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var note = $('#note').val();  // Ambil input catatan dari Swal

                    $.ajax({
                        url: picknoteUrl,  // Gunakan URL yang benar
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",  // Token CSRF dari meta tag di header
                            note: note  // Kirim catatan
                        },
                        success: function (response) {
                            updateButtonColor();
                            Swal.fire({
                                icon: 'success',
                                title: 'Catatan berhasil ditambahkan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#note_${jobticketId}`).text(note);  // Update catatan di UI
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);  // Log detail kesalahan
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menambahkan catatan: ' + error,
                            });
                        }
                    });
                }
            });
        }


        function pausetugas(id, posisitable, name) {
            var pausetugasUrl = `/jobticket/pausetugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menjeda pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, jeda pekerjaan ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Masukkan Alasan, Kind, dan Upload Bukti',
                        html:
                            '<div id="reason-container"><input id="reason" class="swal2-input" placeholder="Masukkan alasan"></div>' +
                            '<select id="kind" class="swal2-input">' +
                            '<option value="">Pilih Jenis Izin (Opsional)</option>' +
                            '<option value="memo">Memo</option>' +
                            '<option value="dinas">Dinas</option>' +
                            '<option value="support">Support</option>' +
                            '</select>' +
                            '<select id="kind_id" class="swal2-input" style="display: none;"></select>' +
                            '<div id="file-container"><input type="file" id="file" class="swal2-input" multiple></div>',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Jeda Tugas',
                        customClass: {
                            popup: 'super-large-swal'
                        },
                        preConfirm: () => {
                            const reason = document.getElementById('reason').value;
                            const kind = document.getElementById('kind').value;
                            const kind_id = document.getElementById('kind_id').value;
                            const files = document.getElementById('file').files;

                            if (kind !== 'memo' && !reason) {
                                Swal.showValidationMessage('Alasan harus diisi!');
                                return false;
                            }

                            return { reason, kind, kind_id, files };
                        }
                    }).then((inputResult) => {
                        if (inputResult.isConfirmed) {
                            const { reason, kind, kind_id, files } = inputResult.value;

                            var formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            formData.append('jobticket_id', id);
                            formData.append('reason', reason);
                            formData.append('kind', kind);
                            formData.append('kind_id', kind_id);

                            if (kind !== 'memo') {
                                for (let i = 0; i < files.length; i++) {
                                    formData.append('file[]', files[i]);
                                }
                            }

                            $.ajax({
                                url: pausetugasUrl,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pekerjaan berhasil dijeda!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $(`#button_${id}_${posisitable}`)
                                        .attr("onclick", `resumetugas('${id}','${posisitable}', '${name}')`)
                                        .html('<i class="fas fa-play-circle"></i> Lanjutkan')
                                        .removeClass('btn-secondary')
                                        .addClass('btn-primary');

                                    var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                                    elapsedTimeElement.textContent = "Jeda";

                                    if (intervals[id]) {
                                        clearInterval(intervals[id]);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Terjadi kesalahan:', error);
                                }
                            });
                        }
                    });

                    document.getElementById('kind').addEventListener('change', function () {
                        const kind = this.value;
                        const kind_id_select = document.getElementById('kind_id');
                        const reasonContainer = document.getElementById('reason-container');
                        const fileContainer = document.getElementById('file-container');

                        if (kind === 'memo') {
                            kind_id_select.style.display = 'block';
                            reasonContainer.style.display = 'none';
                            fileContainer.style.display = 'none';
                            kind_id_select.innerHTML = '';

                            for (const [id, documentname] of Object.entries(encodeddatamemo)) {
                                kind_id_select.innerHTML += `<option value="${id}">${documentname}</option>`;
                            }
                        } else {
                            kind_id_select.style.display = 'none';
                            reasonContainer.style.display = 'block';
                            fileContainer.style.display = 'block';
                            kind_id_select.innerHTML = '';
                        }
                    });
                }
            });
        }

        function resumetugas(id, posisitable, name) {
            var resumetugasUrl = `/jobticket/resumetugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melanjutkan pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan pekerjaan ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: resumetugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pekerjaan berhasil dilanjutkan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `pausetugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-pause-circle"></i> Pause')
                                .removeClass('btn-primary')
                                .addClass('btn-secondary');

                            var startTime = response.startTime;
                            var elapsedSeconds = response.elapsedSeconds;
                            updateElapsedTime1(id, startTime, elapsedSeconds);
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function selesaitugas(id, posisitable, name) {
            var selesaitugasUrl = `/jobticket/selesaitugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyelesaikan pekerjaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, selesaikan pekerjaan ini!',
                cancelButtonText: 'Batal',
                html: `
                                                                                                                                                        <p>Upload file (Wajib):</p>
                                                                                                                                                        <input type="file" id="fileInput" class="swal2-file" accept=".pdf,.doc,.docx,.jpg,.png">
                                                                                                                                                    `
            }).then((result) => {
                if (result.isConfirmed) {
                    var fileInput = document.getElementById("fileInput");
                    var file = fileInput.files[0];

                    // Validasi file tidak boleh kosong atau 0 byte
                    if (!file || file.size === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File tidak valid',
                            text: 'Harap unggah file yang tidak kosong.',
                        });
                        return; // Menghentikan proses jika file tidak valid
                    }

                    var formData = new FormData();
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("file", file);

                    $.ajax({
                        url: selesaitugasUrl,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan',
                                    text: response.error,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pekerjaan berhasil diselesaikan!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                $(`#button_${id}_${posisitable}`).remove();

                                $(`#button_${id}_${posisitable}`).removeClass('btn-danger').addClass('bg-gray');

                                var revisionElement = document.getElementById('revision_' + id);
                                if (revisionElement) {
                                    revisionElement.textContent = response.lastKey || "update";
                                }
                                var elapsedTimeElement = document.getElementById('elapsed_time_' + id);
                                elapsedTimeElement.textContent = "Selesai";

                                var detailButton = $(`#buttondetailtugas_${id}_${posisitable}`);
                                detailButton.removeClass('bg-gray').addClass('bg-maroon');

                                if (intervals[id]) {
                                    clearInterval(intervals[id]);
                                }
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                        }
                    });
                }
            });
        }

        function izinkanrevisitugas(id, posisitable, name) {
            var resetTugasUrl = `/jobticket/izinkanrevisitugas/${id}/${name}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin merevisi tugas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, revisi dan buka tugas ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: resetTugasUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tugas berhasil direvisi dan terbuka!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Update the button to reflect the task can now be started
                            $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-rocket"></i> Start Tugas')
                                .removeClass('btn-success')
                                .addClass('btn-warning');
                        },
                        error: function (xhr, status, error) {
                            console.error('Terjadi kesalahan:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan',
                                text: 'Gagal mereset tugas. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        }

        function detailtugas(jobticket_identity_part, jobticket_identity_id, jobticket_id) {
            var detailUrl = `/jobticket/show/${jobticket_identity_part}/${jobticket_identity_id}/${jobticket_id}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melihat detail?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, detail!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = detailUrl;
                }
            });
        }

        function deletetugas(jobticket_identity_part, jobticket_identity_id, jobticket_id) {
            var detailUrl = `/jobticket/deletejobticket/${jobticket_identity_part}/${jobticket_identity_id}/${jobticket_id}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus jobticket ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = detailUrl;
                }
            });
        }

    </script>


    <script>
        function releaseDocument(jobticketId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin merilis dokumen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Rilis!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan melalui AJAX
                    fetch(`/jobticket/jobticket-released/${jobticketId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                Swal.fire('Berhasil!', data.message, 'success');
                                location.reload(); // Refresh halaman setelah sukses
                            }
                        })
                        .catch(error => {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat merilis dokumen.', 'error');
                        });
                }
            });
        }
    </script>
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







    </script>
    <script>

        function pickposition(id, jobticket, posisitable, rule) {
            const users = @json($availableUsers);

            const positionOptions = rule.includes('Quality Engineering')
                ? ['checker', 'approver']
                : rule.includes('Electrical Engineering System')
                    ? ['drafter', 'checker', 'approver']
                    : ['checker', 'approver'];

            const userOptionsHtml = (position) => users
                .filter(user => {
                    if (rule.includes('Quality Engineering') && position === 'checker') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [149, 137, 178, 139].includes(user.id);
                    } else if (rule.includes('Quality Engineering') && position === 'approver') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [94].includes(user.id);
                    } else if (rule.includes('Electrical Engineering System') && position === 'approver') {
                        // Quality Engineering Checker hanya ID 1, 2, 3, 4
                        return [27].includes(user.id);
                    }
                    return true; // Semua pengguna untuk peran lainnya
                })
                .reduce((html, user) => html + `<option value="${user.id}">${user.name}</option>`, '');

            const selectedIds = {
                checker: jobticket.checker_id ?? '',
                approver: jobticket.approver_id ?? '',
                drafter: jobticket.drafter_id ?? '',
            };

            const htmlContent = positionOptions.map(position => `
                                                <div style="margin-top: 10px;">
                                                    <label for="${position}Select">Pilih ${position.charAt(0).toUpperCase() + position.slice(1)}:</label>
                                                    <select id="${position}Select" class="swal2-select" value="${selectedIds[position]}">
                                                        ${userOptionsHtml(position)}
                                                    </select>
                                                </div>
                                            `).join('');

            Swal.fire({
                title: 'Pilih PIC',
                html: htmlContent,
                didOpen: () => positionOptions.forEach(position => {
                    document.getElementById(`${position}Select`).value = selectedIds[position];
                }),
                showCancelButton: true,
                confirmButtonText: 'Pilih',
                cancelButtonText: 'Batal',
                customClass: { popup: 'large-swal' },
                preConfirm: () => positionOptions.reduce((data, position) => {
                    data[`${position}Id`] = document.getElementById(`${position}Select`)?.value;
                    return data;
                }, {}),
            }).then(result => {
                if (result.isConfirmed) {
                    const { checkerId, approverId, drafterId } = result.value;
                    const selectedNames = {
                        checker: users.find(user => user.id == checkerId)?.name,
                        approver: users.find(user => user.id == approverId)?.name,
                        drafter: users.find(user => user.id == drafterId)?.name,
                    };

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin mengambil pekerjaan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, ambil job ini!',
                        cancelButtonText: 'Batal',
                    }).then(confirmResult => {
                        if (confirmResult.isConfirmed) {
                            $.ajax({
                                url: `/jobticket/pickdraftercheckerapprover/${id}`,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    checker_id: checkerId,
                                    approver_id: approverId,
                                    drafter_id: drafterId,
                                },
                                success: response => {
                                    $(`#checker_${id} .badge`).text(selectedNames.checker);
                                    $(`#approver_${id} .badge`).text(selectedNames.approver);
                                    if (drafterId) {
                                        $(`#drafter_${id} .badge`).text(selectedNames.drafter);
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pekerjaan berhasil diambil!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                error: xhr => {
                                    const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil pekerjaan ini.';
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: errorMessage,
                                    });
                                }
                            });

                        }
                    });
                }
            });
        }

    </script>

@endpush