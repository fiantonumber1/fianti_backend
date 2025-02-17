@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('jobticket.index') }}">List Unit & Project</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('jobticket.show', ['id' => $jobticketpart->id]) }}">List
                            Dokumen</a></li>
                    <li class="breadcrumb-item active text-bold"><a
                            href="{{ route('jobticket.showdocument', ['id' => $jobticketpart->id, 'iddocumentnumber' => $jobticketidentitys->id]) }}">List
                            Revisi (Ambil Jobticket)</a></li>
                    <li class="breadcrumb-item active text-bold"><a
                            href="{{ route('jobticket.detail', ['jobticket_identity_part' => $jobticketpart->id, 'jobticket_identity_id' => $jobticketidentitys->id, 'jobticket_id' => $jobticket->id]) }}">List
                            Perbaikan</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('container3')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-24">

            <div class="card card-outline card-info">
                <div class="card-header">
                    <p class="card-subtitle mb-0">
                        Nomor Dokumen: <strong>{{ $jobticket->jobticketIdentity->documentnumber ?? "" }}</strong><br>
                        Revision: <strong>{{ $jobticket->rev }}</strong><br>
                        Status Revisi: <strong>{{ $jobticket->status ?? "Belum ditutup" }}</strong>
                        <!-- @if($jobticket->status == null)
                                <a href="{{ route('jobticket.close', ['jobticket_identity_part'=>$jobticket->jobticketIdentity->jobticketPart->id,'jobticket_identity_id' => $jobticket->jobticketIdentity->id,'jobticket_id' => $jobticket->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-lock"></i> Tutup Rev <strong>{{ $jobticket->rev }}</strong>
                                </a>
                            @endif -->
                    </p>
                </div>
                <!-- https://colab.research.google.com/github/KevinFeng1/toxic-comment-classification/blob/master/Predictive_Maintenance_using_LSTM.ipynb#scrollTo=ulY14O06knOI -->
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Perbaikan Ke</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Total Elapsed Seconds</th>
                                <th>Drafter</th>
                                <th>Checker</th>
                                <th>Approver</th>
                                <th>Revision Status</th>
                                <th>Files</th>
                                <th>Aksi Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobticketStartedrev as $index => $revision)
                                                                                                                                                                                                                            <tr>
                                                        <td>{{ $revision->id }}</td>
                                                        <td>{{ $jobticket->rev }}_{{ $revision->revisionname }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($revision->start_time_run)->format('d/m/Y H:i:s') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($revision->end_time_run)->format('d/m/Y H:i:s') }}</td>
                                                        <td>{{ $revision->total_elapsed_seconds }}</td>
                                                        <td id="drafter_{{ $jobticket->id }}_{{ $index }}">
                                                            <span class="badge badge-info">{{ $revision->drafter_name ?? "Unknown" }}</span>

                                                        </td>

                                                        <td id="checker_{{ $jobticket->id }}_{{ $index }}">

                                                            @if(isset($revision->checker_id))
                                                                                <span class="badge badge-info">{{ $revision->checker_name }}</span>
                                                                                <span
                                                                                    class="badge badge-warning">{{ $revision->checker_status ?? "Belum Disetujui" }}</span>

                                                                                @if($revision->checker_status == "Reject")
                                                                                                    @foreach($revision->reasons->where('rule', 'checker') as $reason)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="p-2 mb-2 border border-warning rounded">
                                                                                                                            <span class="badge badge-warning">{{ $reason->reason }}</span>

                                                                                                                            @if ($reason->files)
                                                                                                                                                    <p class="card-text mt-2"><strong>File:</strong></p>
                                                                                                                                                    @php
                                                                                                                                                        // Mengambil file dengan created_at paling akhir
                                                                                                                                                        $latestFile = $reason->files->sortByDesc('created_at')->first();
                                                                                                                                                        if ($latestFile) {
                                                                                                                                                            $newLinkFile = str_replace('uploads/', '', $latestFile->link);
                                                                                                                                                        }
                                                                                                                                                    @endphp

                                                                                                                                                    @if ($latestFile)
                                                                                                                                                        <div class="card-text mt-2">
                                                                                                                                                            @include('jobticket.showdocumentrev.fileinfo', ['file' => $latestFile])
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                    @endif
                                                                                                                            @endif
                                                                                                                        </div>
                                                                                                    @endforeach
                                                                                @endif

                                                            @else
                                                                <span class="badge badge-info">No Pic</span>
                                                                <span class="badge badge-warning">Need Checker PIC</span>
                                                            @endif


                                                            @if($revision->checker_id == $useronly->id && $revision->checker_status == null && count($revision->files) > 0)
                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                    id="checkerbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="approvetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $revision->id }}', 'checker','Approve', '{{ $barcodeoption }}')">
                                                                    <i class="fas fa-check-circle"></i> Setujui (Checker)
                                                                </a>
                                                                <a href="#" class="btn btn-danger btn-sm d-block mb-1"
                                                                    id="checkerbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="approvetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $revision->id }}', 'checker','Reject', '{{ $barcodeoption }}')">
                                                                    <i class="fas fa-check-circle"></i> Revisi (Checker)
                                                                </a>
                                                            @elseif($revision->checker_status == null && $revision->checker_id != null)  
                                                                                                                                                                                                                                                                <!-- && $revision->drafter_id == $useronly->id -->
                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                    id="remindercheckerbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="remindertugas('{{ $revision->id }}', 'checker')">
                                                                    <i class="fas fa-bell"></i> Reminder Checker
                                                                </a>


                                                            @endif
                                                        </td>

                                                        <td id="approver_{{ $jobticket->id }}_{{ $index }}">
                                                            @if(isset($revision->approver_id))
                                                                            <span class="badge badge-info">{{ $revision->approver_name}}</span>
                                                                            <span
                                                                                class="badge badge-warning">{{ $revision->approver_status ?? "Belum Disetujui" }}</span>
                                                                            @if($revision->approver_status == "Reject")
                                                                                            @if($revision->approver_status == "Reject")
                                                                                                            @foreach($revision->reasons->where('rule', 'approver') as $reason)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="p-2 mb-2 border border-warning rounded">
                                                                                                                                <span class="badge badge-warning">{{ $reason->reason }}</span>

                                                                                                                                @if ($reason->files)
                                                                                                                                                    <p class="card-text mt-2"><strong>File:</strong></p>
                                                                                                                                                    @php
                                                                                                                                                        // Mengambil file dengan created_at paling akhir
                                                                                                                                                        $latestFile = $revision->files->sortByDesc('created_at')->first();
                                                                                                                                                        if ($latestFile) {
                                                                                                                                                            $newLinkFile = str_replace('uploads/', '', $latestFile->link);
                                                                                                                                                        }
                                                                                                                                                    @endphp

                                                                                                                                                    @if ($latestFile)
                                                                                                                                                        <div class="card-text mt-2">
                                                                                                                                                            @include('jobticket.showdocumentrev.fileinfo', ['file' => $latestFile])
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                    @endif
                                                                                                                                @endif
                                                                                                                            </div>
                                                                                                            @endforeach
                                                                                            @endif
                                                                            @endif
                                                            @else
                                                                <span class="badge badge-info">No Pic</span>
                                                                <span class="badge badge-warning">Need Approver PIC</span>
                                                            @endif

                                                            @if($revision->approver_id == $useronly->id && $revision->checker_status != null && $revision->approver_status == null && count($revision->files) > 0)
                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                    id="approverbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="approvetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $revision->id }}',  'approver','Approve', '{{ $barcodeoption }}')">
                                                                    <i class="fas fa-check-circle"></i> Setujui (Approver)
                                                                </a>
                                                                <a href="#" class="btn btn-danger btn-sm d-block mb-1"
                                                                    id="approverbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="approvetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $revision->id }}',  'approver','Reject', '{{ $barcodeoption }}')">
                                                                    <i class="fas fa-check-circle"></i> Revisi (Approver)
                                                                </a>
                                                            @elseif($revision->approver_status == null && $revision->checker_status != null && $revision->approver_id != null)
                                                                                                                                                                                                                                    <!-- && $revision->drafter_id == $useronly->id -->
                                                                <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                                                    id="reminderapproverbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                    onclick="remindertugas('{{ $revision->id }}', 'approver')">
                                                                    <i class="fas fa-bell"></i> Reminder Approver
                                                                </a>

                                                            @endif

                                                        </td>

                                                        <td>{{ ucfirst($revision->revision_status) }}</td>
                                                        <td>
                                                            @if ($revision->files)
                                                                                            <p class="card-text"><strong>File:</strong></p>
                                                                                            @php
                                                                                                // Mengambil file dengan created_at paling akhir
                                                                                                $latestFile = $revision->files->sortByDesc('created_at')->first();
                                                                                                if ($latestFile) {
                                                                                                    $newLinkFile = str_replace('uploads/', '', $latestFile->link);
                                                                                                }
                                                                                            @endphp
                                                                                            @if ($latestFile)
                                                                                                <div class="card-text mt-2">
                                                                                                    @include('jobticket.showdocumentrev.fileinfo', ['file' => $latestFile])
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                            @endif
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if($useronly->rule == "superuser")
                                                                @if($jobticket->status == null)
                                                                    <a href="#" class="btn btn-danger btn-sm"
                                                                        onclick="deleteRevision('{{ $revision->id }}')">
                                                                        <i class="fas fa-trash"></i> Hapus
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if($jobticket->status == null)
                                                                <!-- @if($jobticket->drafter_id == $useronly->id && $revision->approver_status == null && $revision->checker_status == null)
                                                                                                                                                                                                                                                                                                                                                                                                                                    <a href="{{ route('jobticket.uploadverifikasi', ['revision' => $revision->id]) }}" class="btn btn-info btn-sm d-block mb-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-upload"></i> Upload Dokumen
                                                                                                                                                                                                                                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                                                                                                                                                                                                                                            @endif -->

                                                                @if(($useronly->rule == "Manager " . $jobticketpart->unit->name || $useronly->id == 178 || $useronly->id == 1) && (!isset($revision->approver_id) || !isset($revision->checker_id)))
                                                                    <a href="#" class="btn btn-default bg-pink d-block mb-1"
                                                                        id="selectuserbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                        onclick="pickposition('{{ $revision->id }}', '{{ $index }}', {{ json_encode($revision) }})">
                                                                        <i class="fas fa-hand-pointer"></i> Select User
                                                                    </a>
                                                                @endif

                                                                @if(($useronly->rule == "Manager " . $jobticketpart->unit->name) && (!isset($revision->approver_status) || !isset($revision->checker_status)))
                                                                    <a href="#" class="btn btn-default bg-pink d-block mb-1"
                                                                        id="selectuserbutton_{{ $jobticket->id }}_{{ $index }}"
                                                                        onclick="pickposition('{{ $revision->id }}', '{{ $index }}', {{ json_encode($revision) }})">
                                                                        <i class="fas fa-hand-pointer"></i> Take Over
                                                                    </a>
                                                                @endif







                                                            @endif
                                                        </td>
                                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>

        function deleteRevision(revision) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus revisi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/jobticket/deleterevision/${revision}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Revisi berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menghapus revisi ini.',
                            });
                        }
                    });
                }
            });
        }


        function remindertugas(id, kindposition) {
            var approveUrl = `/jobticket/reminder/${id}/${kindposition}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin reminder ${kindposition}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, saya ingatkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(approveUrl, { _token: '{{ csrf_token() }}' })
                        .done(function (response) {
                            Swal.fire(
                                'Berhasil!',
                                response.success || 'Pengingat berhasil dikirim!',
                                'success'
                            );
                        })
                        .fail(function (jqXHR) {
                            // Mendapatkan pesan error dari server dan menampilkannya di Swal
                            var errorMessage = jqXHR.responseJSON?.error || 'Terjadi kesalahan. Silakan coba lagi.';
                            Swal.fire(
                                'Gagal!',
                                errorMessage,
                                'error'
                            );
                            // Menampilkan log error untuk debugging jika diperlukan
                            console.error("Error:", errorMessage);
                        });
                }
            });
        }

        function approvetugas(id, posisitable, revision, kindposition, status, $barcodeoption) {
            var approveUrl = `/jobticket/approveperbaikan/${revision}/${kindposition}`;

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin melakukan ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Ya, lakukan ${status}!`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (status === 'Approve') {
                        let autoSignatureOption = '';

                        // Tampilkan opsi 'Acc dengan tanda tangan otomatis' hanya jika $barcodeoption == 'true'
                        if ($barcodeoption === 'true') {
                            autoSignatureOption = `
                                                                                    <div class="form-group mt-3">
                                                                                        <input type="checkbox" id="accAutoSignature" name="accAutoSignature">
                                                                                        <label for="accAutoSignature">Acc dengan tanda tangan otomatis? (hanya untuk posisi tertentu (ujicoba) & file yang diacc wajib pdf && anda sudah upload ttd)</label>
                                                                                    </div>
                                                                                `;
                        }

                        Swal.fire({
                            title: 'Upload File',
                            html: `
                                                                                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        @method('PUT')
                                                                                        <div class="form-group">
                                                                                            <label for="file" class="file-label">Pilih File:</label>
                                                                                            <div id="file-input-container">
                                                                                                <div class="file-input-group">
                                                                                                    <input type="file" id="fileInput" name="file[]" class="form-control-file" multiple>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group mt-3">
                                                                                                <input type="checkbox" id="accWithoutUpload" name="accWithoutUpload">
                                                                                                <label for="accWithoutUpload">Acc tanpa upload ulang? (syarat drafter sudah memberikan ttd checker dan approver diawal)</label>
                                                                                            </div>
                                                                                            ${autoSignatureOption}
                                                                                        </div>
                                                                                    </form>
                                                                                `,
                            showCancelButton: true,
                            confirmButtonText: 'Lanjutkan',
                            cancelButtonText: 'Batal',
                            preConfirm: () => {
                                var accWithoutUpload = document.getElementById('accWithoutUpload').checked;
                                var accAutoSignature = $barcodeoption === 'true'
                                    ? document.getElementById('accAutoSignature').checked
                                    : false;
                                var fileInput = document.getElementById('fileInput');

                                if (!accWithoutUpload && !accAutoSignature && fileInput.files.length === 0) {
                                    Swal.showValidationMessage(
                                        'Anda harus memilih setidaknya satu file, memilih "Acc tanpa upload ulang", atau memilih "Acc dengan tanda tangan otomatis (muncul jika anda sudah upload barcode)".'
                                    );
                                    return false;
                                }

                                var form = document.getElementById('uploadForm');
                                var formData = new FormData(form);
                                formData.append('accwithoutupload', accWithoutUpload ? 'true' : 'false');
                                formData.append('accautosignature', accAutoSignature ? 'true' : 'false');
                                return formData;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Mengupload...',
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: `/jobticket/revisionapprovedoc/${revision}/${kindposition}`,
                                    method: 'POST',
                                    data: result.value,
                                    processData: false,
                                    contentType: false,
                                    success: function (uploadResponse) {
                                        Swal.close();
                                        approveTask(id, posisitable, approveUrl, status, kindposition);
                                    },
                                    error: function (xhr, status, error) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan saat mengupload file.',
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        approveTask(id, posisitable, approveUrl, status, kindposition);
                    }
                }
            });
        }


        function approveTask(id, posisitable, approveUrl, status, kindposition) {
            if (status === "Reject") {
                Swal.fire({
                    title: 'Masukkan alasan penolakan',
                    html: `
                                                                            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="form-group">
                                                                                    <label for="reason_file" class="file-label">Pilih File (Tidak Wajib):</label>
                                                                                    <div id="file-input-container">
                                                                                        <div class="file-input-group">
                                                                                            <input type="file" id="reason_file" name="reason_file[]" class="form-control-file" multiple>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        `,
                    input: 'textarea',
                    inputLabel: 'Alasan',
                    inputPlaceholder: 'Ketik alasan penolakan di sini...',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const reason = result.value;

                        // Create a new FormData object
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('status', status);
                        formData.append('reason', reason);

                        // Append each selected file to the FormData object as reason_file
                        const files = document.getElementById('reason_file').files;
                        for (let i = 0; i < files.length; i++) {
                            formData.append('reason_file[]', files[i]);
                        }

                        $.ajax({
                            url: approveUrl,
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tugas berhasil ditolak!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                if (kindposition === 'checker') {
                                    $(`#checkerbutton_${id}_${posisitable}`).remove();
                                    $(`#checker_${id}_${posisitable}`).text(status);
                                } else if (kindposition === 'approver') {
                                    $(`#approverbutton_${id}_${posisitable}`).remove();
                                    $(`#approver_${id}_${posisitable}`).text(status);
                                }
                            },
                            error: function (xhr, status, error) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menolak tugas ini.',
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Memproses...',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: approveUrl,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function (response) {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Tugas berhasil disetujui!',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        if (kindposition === 'checker') {
                            $(`#checkerbutton_${id}_${posisitable}`).remove();
                            $(`#checker_${id}_${posisitable}`).text(status);
                        } else if (kindposition === 'approver') {
                            $(`#approverbutton_${id}_${posisitable}`).remove();
                            $(`#approver_${id}_${posisitable}`).text(status);
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyetujui tugas ini.',
                        });
                    }
                });
            }
        }


        function pickposition(id, posisitable, revision) {
            // Convert available users to an array of objects
            var users = Object.entries(@json($availableUsers)).map(([id, name]) => ({ id, name }));
            let positionOptions = ['checker', 'approver'];

            // Build user options HTML for select
            let userOptionsHtml = users.map(user => `<option value="${user.id}">${user.name}</option>`).join('');

            // ID checker dan approver yang telah terpilih (jika ada)
            let selectedCheckerId = revision.checker_id ?? '';
            let selectedApproverId = revision.approver_id ?? '';

            Swal.fire({
                title: 'Pilih Checker dan Approver',
                html: `
                                                                        <div>
                                                                            <label for="checkerSelect">Pilih Checker:</label>
                                                                            <select id="checkerSelect" class="swal2-select">
                                                                                ${userOptionsHtml}
                                                                            </select>
                                                                        </div>
                                                                        <div style="margin-top: 10px;">
                                                                            <label for="approverSelect">Pilih Approver:</label>
                                                                            <select id="approverSelect" class="swal2-select">
                                                                                ${userOptionsHtml}
                                                                            </select>
                                                                        </div>`,
                didOpen: () => {
                    document.getElementById('checkerSelect').value = selectedCheckerId;
                    document.getElementById('approverSelect').value = selectedApproverId;
                },
                showCancelButton: true,
                confirmButtonText: 'Pilih',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'large-swal'
                },
                preConfirm: () => {
                    return {
                        selectedCheckerId: document.getElementById('checkerSelect').value,
                        selectedApproverId: document.getElementById('approverSelect').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let selectedCheckerId = result.value.selectedCheckerId;
                    let selectedApproverId = result.value.selectedApproverId;
                    let selectedCheckerName = users.find(user => user.id == selectedCheckerId).name;
                    let selectedApproverName = users.find(user => user.id == selectedApproverId).name;

                    // Konfirmasi sebelum AJAX
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin menunjuk checker dan approver ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, ambil job ini!',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let picktugasUrl = `/jobticket/jobticketstartedrev/pickdraftercheckerapprover/${id}`;

                            $.ajax({
                                url: picktugasUrl,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    checker_id: selectedCheckerId,
                                    approver_id: selectedApproverId
                                },
                                success: function (response) {
                                    $(`#checker_${id} .badge`).text(selectedCheckerName);
                                    $(`#approver_${id} .badge`).text(selectedApproverName);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pekerjaan berhasil diambil!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
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
            });
        }

    </script>
@endpush

@push('css')
    <style>
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

        .super-large-swal {
            width: 1200px !important;
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