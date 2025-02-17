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
        <!-- Dropdown for revisiall -->
        <div class="form-group">
            <label for="revisiSelect">Pilih Project :</label>
            <select class="form-control" id="revisiSelect" onchange="showRevisiContent(this.value)">
                @foreach ($revisiall as $keyan => $revisi)
                    <option value="{{ $keyan }}" @if($loop->first) selected @endif>{{ $keyan }}</option>
                @endforeach
            </select>
        </div>

        <!-- Content for revisiall -->
        <div class="revisi-content">
            @foreach ($revisiall as $keyan => $revisi)
                        <div id="revisi-{{ $keyan }}" class="revisi-section" @if(!$loop->first) style="display:none;" @endif>
                            <div class="row">
                                @if($authuser->rule == "superuser")
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <button type="button" class="btn btn-danger btn-sm btn-block"
                                            onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-12">
                                        <a href="" class="btn btn-primary btn-sm btn-block mb-3">Upload Dokumen</a>
                                    </div>
                                @endif
                                <!-- <div class="col-md-3 col-sm-6 col-12">
                                                                                                                                                                        <button type="button" class="btn btn-success btn-sm btn-block"
                                                                                                                                                                            onclick="handleReportMultipleItems()">Report yang dipilih</button>
                                                                                                                                                                    </div> -->
                                <div class="col-md-3 col-sm-6 col-12">
                                    <form action="{{ route('new-memo.downloadall') }}" method="GET">
                                        <div class="input-group">
                                            <select name="unit" class="form-control">
                                                <option value="all">All Units</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-success btn-sm">Download Report</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                            </div>

                            <!-- Nav tabs for units -->
                            <ul class="nav nav-tabs" id="unitTabs-{{ $keyan }}" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="all-tab-{{ $keyan }}" data-toggle="tab" href="#all-{{ $keyan }}"
                                        role="tab" aria-controls="all-{{ $keyan }}" aria-selected="true">All</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="unbound-tab-{{ $keyan }}" data-toggle="tab" href="#unbound-{{ $keyan }}"
                                        role="tab" aria-controls="unbound-{{ $keyan }}" aria-selected="false">Unbound</a>
                                </li>
                                @foreach($units as $unit)
                                    <li class="nav-item">
                                        <a class="nav-link" id="{{ str_replace(' ', '_', $unit->singkatan) }}-tab-{{ $keyan }}"
                                            data-toggle="tab" href="#{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                            role="tab" aria-controls="{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                            aria-selected="false">{{ $unit->singkatan }}</a>
                                    </li>
                                @endforeach

                            </ul>

                            <!-- Tab content -->
                            <div class="tab-content" id="unitTabContent-{{ $keyan }}">
                                <!-- Tab All -->
                                <div class="tab-pane fade show active" id="all-{{ $keyan }}" role="tabpanel"
                                    aria-labelledby="all-tab-{{ $keyan }}">
                                    <table id="example2-{{ $keyan }}-all" class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <!-- <th><span class="checkbox-toggle" id="checkAll-{{ $keyan }}"><i
                                                                                                                                    class="far fa-square"></i></span></th> -->
                                                <th>No</th>
                                                <th>Deadline</th>
                                                <th>Nomor Dokumen</th>
                                                <th>Nama Dokumen</th>
                                                <th>Progress</th>
                                                <th>Posisi Memo</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $documents = $revisi['documents'];
                                                $counterdokumen = 1;
                                            @endphp

                                            @include('newmemo.index.index_loopbody', ['documents' => $documents])
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Tabs for each unit -->
                                @foreach($units as $unit)
                                                <div class="tab-pane fade" id="{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                                    role="tabpanel" aria-labelledby="{{ str_replace(' ', '_', $unit->name) }}-tab-{{ $keyan }}">
                                                    <table id="example2-{{ $keyan }}-{{ str_replace(' ', '_', $unit->singkatan) }}"
                                                        class="table table-bordered table-hover table-striped">
                                                        <thead>
                                                            <tr>
                                                                <!-- <th><span class="checkbox-toggle"
                                                                                                                                                                                                                    id="checkAll-{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"><i
                                                                                                                                                                                                                        class="far fa-square"></i></span></th> -->
                                                                <th>No</th>
                                                                <th>Deadline</th>
                                                                <th>Nomor Dokumen</th>
                                                                <th>Nama Dokumen</th>
                                                                <th>Progress</th>
                                                                <th>Posisi Memo</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $documents = $revisi['units'][$unit->name];
                                                                $counterdokumen = 1;
                                                            @endphp

                                                            @include('newmemo.index.index_loopbody', ['documents' => $documents])
                                                        </tbody>
                                                    </table>
                                                </div>
                                @endforeach

                                <!-- Tab for unbound documents -->
                                <div class="tab-pane fade" id="unbound-{{ $keyan }}" role="tabpanel"
                                    aria-labelledby="unbound-tab-{{ $keyan }}">
                                    <table id="example2-{{ $keyan }}-unbound"
                                        class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <!-- <th><span class="checkbox-toggle" id="checkAll-unbound-{{ $keyan }}"><i
                                                                                                                        class="far fa-square"></i></span></th> -->
                                                <th>No</th>
                                                <th>Deadline</th>
                                                <th>Nomor Dokumen</th>
                                                <th>Nama Dokumen</th>
                                                <th>Progress</th>
                                                <th>Posisi Memo</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $documents = $revisi['units']['unbound'];
                                                $counterdokumen = 1;
                                            @endphp

                                            @include('newmemo.index.index_loopbody', ['documents' => $documents])
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function showRevisiContent(keyan) {
            // Hide all sections
            document.querySelectorAll('.revisi-section').forEach(function (section) {
                section.style.display = 'none';
            });

            // Show the selected section
            document.getElementById('revisi-' + keyan).style.display = 'block';
        }
    </script>
    <script>
        // Fungsi untuk menangani penghapusan multiple item dengan AJAX
        function handleDeleteMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                // Jika pengguna mengonfirmasi penghapusan
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function (checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    // Melakukan panggilan AJAX untuk menghapus item yang dipilih
                    $.ajax({
                        url: "{{ route('document.deleteMultiple') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function (response) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });

                            // Refresh halaman setelah penghapusan
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            // Tampilkan pesan error
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus item yang dipilih.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

    </script>



    <script>
        function handleReportMultipleItems() {
            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin melakukan report item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, report!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengambil daftar ID dokumen yang dipilih
                    var selectedDocumentIds = [];
                    var checkboxes = document.querySelectorAll('input[name="document_ids[]"]:checked');
                    checkboxes.forEach(function (checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    // Melakukan panggilan AJAX untuk melakukan report pada item yang dipilih
                    $.ajax({
                        url: "{{ route('new-memo.download') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function (response, status, xhr) {
                            // Membuat blob URL untuk unduh file
                            var blob = new Blob([response]);
                            var url = window.URL.createObjectURL(blob);

                            // Membuat anchor untuk men-download file
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'document_report.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);

                            // Hapus objek URL yang sudah tidak diperlukan
                            window.URL.revokeObjectURL(url);
                        },
                        error: function (xhr, status, error) {
                            var errorMessage = xhr.responseJSON.error || 'Terjadi kesalahan.';
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }


    </script>




    <script>
        function showDocumentSummary(informasidokumenencoded, ringkasan, documentId) {
            // Parse JSON data
            var documentData = JSON.parse(ringkasan);
            var documentInfo = JSON.parse(informasidokumenencoded);

            // Construct document information section
            var documentInfoHTML = `
                                                                            <div style="text-align: center;">
                                                                                <p style="font-weight: bold; font-size: 24px;">INFORMASI MEMO</p>
                                                                            </div>
                                                                            <div style="padding: 20px; font-size: 16px;">
                                                                                <p style="font-weight: bold;">Kepada Yth,</p>
                                                                                <ol>
                                                                        `;

            // Construct list of PICs
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                documentInfoHTML += `<li>${pic}</li>`;
            }

            // Add closing tags for list
            documentInfoHTML += `
                                                                                </ol>
                                                                                <hr style="margin-top: 20px;">
                                                                                <div style="padding: 20px;">
                                                                                    <p style="font-size: 16px;">Kami sampaikan informasi dokumen berikut:</p>
                                                                                    <table style="width: 100%; margin-bottom: 20px;">
                                                                                        <tr>
                                                                                            <td style="font-weight: bold; width: 30%">Jenis Dokumen:</td>
                                                                                            <td>${documentInfo['category']}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: bold">Nama Memo:</td>
                                                                                            <td>${documentInfo['documentname']}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: bold">Nomor Memo:</td>
                                                                                            <td>${documentInfo['documentnumber']}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: bold">Jenis Memo:</td>
                                                                                            <td>${documentInfo['memokind']}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: bold">Asal Memo:</td>
                                                                                            <td>${documentInfo['memoorigin']}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td style="font-weight: bold">Status Dokumen:</td>
                                                                                            <td>${documentInfo['documentstatus']}</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            `;

            // Construct table header
            var tableHeaderHTML = `
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Pic</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Nama Penulis</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Email</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Status Feedback</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Kategori</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Sudah Dibaca</th>
                                                                                    <th style="border: 1px solid #000; padding: 8px;">Hasil Review</th>
                                                                                </tr>
                                                                            </thead>`;

            // Construct table body
            var tableBodyHTML = '<tbody>';
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                var level = documentData[i].level;
                var userInformation = documentData[i].userinformations;

                // Filter out specific conditions
                if ((pic !== "MTPR" || level !== "pembukadokumen") && (pic !== "Product Engineering" || level !== "signature")) {
                    // Construct table row
                    var tableRowHTML = `
                                                                                    <tr>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${pic}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['nama penulis']}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['email']}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile']}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile2']}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['sudahdibaca']}</td>
                                                                                        <td style="border: 1px solid #000; padding: 8px;">${userInformation['hasilreview']}</td>
                                                                                    </tr>`;

                    tableBodyHTML += tableRowHTML;
                }
            }
            tableBodyHTML += '</tbody>';

            // Construct the complete HTML content
            var htmlContent = `
                                                                            <div style="padding: 20px;">
                                                                                ${documentInfoHTML}
                                                                                <div style="overflow-x: auto;">
                                                                                    <table style="border-collapse: collapse; width: 100%; font-size: 16px;">
                                                                                        <caption style="caption-side: top; text-align: center; font-weight: bold; font-size: 20px; margin-bottom: 10px;">Feedback</caption>
                                                                                        ${tableHeaderHTML}
                                                                                        ${tableBodyHTML}
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <img src="{{ asset('images/INKAICON.png') }}" alt="Company Logo" class="company-logo" style="position: absolute; top: 10px; right: 10px; width: 80px; height: 80px; object-fit: contain;">`;

            // Show SweetAlert2 modal with close and print buttons
            Swal.fire({
                html: htmlContent,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, print it!",
                cancelButtonText: "Close",
                width: '90%', // Lebar modal 90%
                padding: '2rem', // Padding konten modal
                customClass: {
                    image: 'img-fluid rounded-circle' // Menggunakan kelas Bootstrap untuk memastikan gambar perusahaan responsif
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Printed!",
                        text: "Your file has been printed.",
                        icon: "success"
                    });
                    printDocumentSummary(documentId);
                }
            });
        }

        function printDocumentSummary(documentId) {
            // Get the URL for the PDF
            var pdfUrl = "{{ url('document/memo') }}/" + documentId + "/pdf";

            // Open the PDF URL in a new window/tab for printing
            window.open(pdfUrl, '_blank');
        }

        function toggleDocumentStatus(button) {
            var documentId = $(button).data('document-id');
            var currentStatus = $(button).data('document-status');
            var newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';

            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah status dokumen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah status!',
                html: `
                                                                                ${newStatus === 'Tertutup' ? '<label for="fileUpload" style="margin-top: 10px;">Pilih file:</label><input type="file" id="fileUpload" multiple />' : ''}
                                                                            `,
                preConfirm: () => {
                    const fileInput = Swal.getPopup().querySelector('#fileUpload');

                    if (newStatus === 'Tertutup' && fileInput && fileInput.files.length === 0) {
                        Swal.showValidationMessage('File harus dipilih');
                        return false; // Stop the process if validation fails
                    }

                    return {
                        files: fileInput ? fileInput.files : []
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Prepare FormData to include file and status
                    var formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('status', newStatus);

                    // Add files if they exist
                    if (result.value.files && result.value.files.length > 0) {
                        $.each(result.value.files, function (index, file) {
                            formData.append('file[]', file);
                        });
                    }

                    // Mengirim permintaan AJAX untuk mengubah status dokumen
                    $.ajax({
                        url: "{{ url('new-memo/show') }}/" + documentId + "/updatedocumentstatus",
                        type: "POST", // Use POST instead of PUT to allow file upload
                        data: formData,
                        contentType: false, // Disable default content type to handle FormData
                        processData: false, // Disable automatic processing of data
                        success: function (response) {
                            // Update button UI as before
                            $(button).removeClass('document-status-button-' + currentStatus.toLowerCase()).addClass('document-status-button-' + newStatus.toLowerCase());
                            $(button).data('document-status', newStatus);
                            $(button).attr('title', newStatus);

                            var iconClass = newStatus === 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle';
                            $(button).find('i').removeClass().addClass(iconClass);
                            $(button).find('span').text(newStatus);

                            if (newStatus === 'Terbuka') {
                                $(button).removeClass('btn-success').addClass('btn-danger');
                            } else {
                                $(button).removeClass('btn-danger').addClass('btn-success');
                            }

                            Swal.fire({
                                title: "Berhasil!",
                                text: response.new_status === 'File Masuk' ? "Status dokumen berhasil diubah, file telah diunggah." : "Status dokumen berhasil diubah.",
                                icon: "success"
                            });
                        },
                        error: function (xhr, status, error) {
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

        function confirmDelete(documentId) {
            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus dokumen ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke URL hapus dengan mengganti {id} dengan id dokumen yang sesuai
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Status Anda berhasil diubah.",
                        icon: "success"
                    });
                    var Url = "{{ url('document/memo') }}/" + documentId + "/destroyget";

                    // Redirect ke URL untuk mengubah status dokumen
                    window.location.href = Url;
                }
            });
        }
    </script>

    <script>
        // Function to handle form submission with SweetAlert confirmation
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForm = document.getElementById('deleteForm');
            const submitBtn = document.getElementById('submitBtn');

            deleteForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Confirmation',
                    text: 'Do you want to submit the form?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Updated!",
                            text: "Your information has been uploaded.",
                            icon: "success"
                        });
                        deleteForm.submit(); // Submit the form if user confirms
                    }
                });
            });
        });
    </script>
    <script>
        $(function () {

            @foreach ($revisiall as $key => $revisi)
                $('#example2-{{ $key }}-all').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
                $('#example2-{{ $key }}-unbound').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
                @foreach($units as $unit)
                    $('#example2-{{ $key }}-{{ str_replace(' ', '_', $unit->singkatan) }}').DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true
                    });
                @endforeach
            @endforeach

        });
    </script>

    <script>

        $(function () {
            //Enable check and uncheck all functionality
            $('#checkAll').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('input[name="document_ids[]"]').prop('checked', false);
                    $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check first 10 checkboxes
                    $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                    $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks);
            });
        });
    </script>
@endpush