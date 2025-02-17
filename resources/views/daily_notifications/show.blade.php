<!-- resources/views/newEkpedisi Dokumen/index.blade.php -->

@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="/">Ekpedisi Dokumen</a></li>
                    <li class="breadcrumb-item active text-bold">Tracking Ekpedisi Dokumen</li>
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
        <h3 class="card-title text-bold">Page Monitoring Ekpedisi Dokumen <span class="badge badge-info ml-1"></span>
        </h3>
    </div>
    <div class="card-body">
        <h1 class="mb-4">Daily Notification Details</h1>

        <!-- Card untuk Detail Daily Notification -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ $dailyNotification->name }}</h5>

                <p class="card-text">
                    <strong>Status Baca: </strong>{{ $dailyNotification->read_status }}<br>

                    <!-- Menampilkan User yang sudah menerima notifikasi -->
                    <strong>Pengguna yang Menerima:</strong>
                <ul>
                    @foreach($dailyNotification->users as $user)
                        <li>{{ $user->name }} ({{ $user->email }})</li>
                    @endforeach
                </ul>

                <!-- Form untuk Konfirmasi Status Baca -->
                <form id="readStatusForm"
                    action="{{ route('daily-notifications.downloadpdf', $dailyNotification->id) }}" method="GET">
                    @csrf
                    <button type="button" class="btn btn-sm btn-primary" id="confirmRead">
                        <i class="fas fa-eye"></i> Konfirmasi Sudah Diterima
                    </button>
                </form>
                </p>
            </div>
        </div>


        <!-- Tabel untuk Progress Report Histories -->
        <h2 class="mb-4">Progress Report Histories</h2>
        @if ($documentview->count() > 0)
            @foreach ($documentview as $documentKind => $reports)
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">{{ $documentKind }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No Dokumen</th>
                                    <th>Nama Dokumen</th>
                                    <th>Rev</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>{{ $report->nodokumen }}</td>
                                        <td>{{ $report->namadokumen }}</td>
                                        <td>{{ $report->rev }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-warning">
                No progress report histories found.
            </div>
        @endif
    </div>
</div>
@endsection


@push('scripts')
    <!-- SweetAlert Konfirmasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('confirmRead').addEventListener('click', function () {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Dokumen ini akan ditandai sebagai telah dibaca dan file akan dikirim ke WhatsApp Anda!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, saya yakin!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke URL download setelah konfirmasi
                    window.location.href = document.getElementById('readStatusForm').action;
                }
            });
        });
    </script>
@endpush