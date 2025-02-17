@php
    use App\Models\Newreport;
    use Carbon\Carbon; // Import Carbon class
@endphp

@extends('layouts.main')

@section('container1')
<div class="container mt-4">
    <h1 class="mb-4">Document: {{ $newProgressReport->title }}</h1>

    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="information-tab" data-toggle="tab" href="#information" role="tab" aria-controls="information" aria-selected="true">Information</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="revisions-tab" data-toggle="tab" href="#revisions" role="tab" aria-controls="revisions" aria-selected="false">Revisions</a>
        </li>
    </ul>

    <div class="tab-content" id="mainTabContent">
        <div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="information-tab">
            <div class="list-group-item mt-3">
                <h4>Information</h4>
                <p><strong>Document Number:</strong> {{ $newProgressReport->nodokumen }}</p>
                <p><strong>Document Name:</strong> {{ $newProgressReport->namadokumen }}</p>
                {{-- Add other information fields as needed --}}

                @php
                    
                @endphp

                <p><strong>Start Time:</strong> {{ $waktuindo }}</p>
                <p><strong>Total Elapsed Time:</strong> 
                    <span id="totalElapsedTime">
                        @if($startTime !== null)
                            {{ gmdate('H:i:s', $totalTime) }}
                        @endif
                    </span>
                </p>


                <p><strong>Supporting Documents:</strong>
                    <span class="badge badge-info">
                        @if(isset($indukan[strval($newProgressReport->id)]))
                            @foreach ($indukan[strval($newProgressReport->id)]["dokumen"] as $anak)
                                {{ $anak['namadokumen'] }} ({{ $anak['status'] }})
                            @endforeach
                        @else
                            Tidak ada dokumen pendukung
                        @endif
                    </span>
                </p>

                <p><strong>Last Revision:</strong> 
                    <span class="badge badge-warning">{{ $hasilwaktu['revisionlast'] ?? "Belum ada" }}</span>
                </p>

                @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == $newreport->unit || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                    @if(!isset($hasilwaktu['start_time']))
                        @if($newProgressReport->drafter == "-" || $newProgressReport->drafter == "" || $newProgressReport->drafter == null)
                            @if(!isset($indukan[strval($newProgressReport->id)]["persen"]))
                                <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="picktugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                    <i class="fas fa-hand-pointer"></i> Pick Tugas (Tanpa Dokumen Pendukung)
                                </a>
                            @elseif(isset($indukan[strval($newProgressReport->id)]["persen"]) && ($indukan[strval($newProgressReport->id)]["persen"]['count'] != $indukan[strval($newProgressReport->id)]["persen"]['countrelease']))
                                <a href="#" class="btn btn-default bg-pink d-block mb-1">
                                    <i class="fas fa-hand-pointer"></i> Dokumen Pendukung Belum Release
                                </a>
                            @elseif(isset($indukan[strval($newProgressReport->id)]["persen"]) && ($indukan[strval($newProgressReport->id)]["persen"]['count'] == $indukan[strval($newProgressReport->id)]["persen"]['countrelease']))
                                <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="picktugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                    <i class="fas fa-hand-pointer"></i> Pick Tugas
                                </a>
                            @endif
                        @else
                            @if($statusrevisi != "ditutup")
                                @if($newProgressReport->drafter == $useronly->name)
                                    <a href="#" class="btn btn-warning btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="starttugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                        <i class="fas fa-rocket"></i> Start Tugas
                                    </a>
                                @else
                                    <a href="#" class="btn btn-default bg-white d-block mb-1">
                                        <i class="fas fa-hand-pointer"></i> Tugas Milik Orang
                                    </a>
                                @endif
                            @else
                                @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                    <a href="#" class="btn btn-success btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="izinkanrevisitugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                        <i class="fas fa-edit"></i> Izinkan Revisi
                                    </a>
                                @endif
                            @endif
                        @endif
                    @else
                        @if($useronly->name == $newProgressReport->drafter)
                            @if($hasilwaktu['pause_time'] == null)
                                <a href="#" class="btn btn-secondary btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="pausetugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                    <i class="fas fa-pause-circle"></i> Pause
                                </a>
                            @else
                                <a href="#" class="btn btn-primary btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="resumetugas('{{ $newProgressReport->id }}',  '{{ $index }}', '{{ $useronly->name }}')">
                                    <i class="fas fa-play-circle"></i> Resume
                                </a>
                            @endif

                            <a href="#" class="btn btn-danger btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="selesaitugas('{{ $newProgressReport->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                <i class="fas fa-check-circle"></i> Selesai
                            </a>
                        @endif
                    @endif

                    @if($useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                        <a href="#" class="btn btn-info btn-sm d-block mb-1" id="button_{{ $newProgressReport->id }}_{{ $index }}" onclick="displayTime('{{ $newProgressReport->id }}', '{{ $index }}')">
                            <i class="fas fa-clock"></i> Display Elapsed Time
                        </a>
                    @endif
                @endif
            </div>
            <a href="{{ route('newreports.show', $newProgressReport->newreport_id) }}" class="btn btn-primary mt-4">Back to List</a>
        </div>

        <div class="tab-pane fade" id="revisions" role="tabpanel" aria-labelledby="revisions-tab">
        <ul class="nav nav-tabs" id="revisionTabs" role="tablist">
        @forelse($newProgressReport->revisions as $index => $revision)
            <li class="nav-item">
                <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="revision-tab-{{ $index }}" data-toggle="tab" href="#revision-{{ $index }}" role="tab" aria-controls="revision-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    Revision {{ $revision->revisionname }}
                </a>
            </li>
        @empty
            <li class="nav-item">
                <span class="nav-link active">No Revisions</span>
            </li>
        @endforelse
    </ul>

    <div class="tab-content" id="revisionContent">
        @forelse($newProgressReport->revisions as $index => $revision)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="revision-{{ $index }}" role="tabpanel" aria-labelledby="revision-tab-{{ $index }}">
                <div class="list-group-item">
                    <h4>{{ $revision->revisionname }}</h4>
                    <p><strong>Start Time:</strong> {{ $revision->start_time_run }}</p>
                    <p><strong>End Time:</strong> {{ $revision->end_time_run }}</p>
                    <p><strong>Status:</strong> {{ $revision->revision_status }}</p>
                    <p><strong>Total Elapsed Time:</strong> {{ $revision->total_elapsed_seconds }} seconds</p>
                </div>
            </div>
        @empty
            <div class="tab-pane fade show active">
                <p>No revisions available.</p>
            </div>
        @endforelse
    </div>
    {{-- Tombol untuk kembali ke daftar new progress report --}}
    <a href="{{ route('newreports.show', $newProgressReport->newreport_id) }}" class="btn btn-primary mt-4">Back to List</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I=" crossorigin="anonymous"></script>
<script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function picktugas(id, posisitable, name) {
        var picktugasUrl = `/newprogressreports/picktugas/${id}/${name}`;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengambil tugas ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ambil!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: picktugasUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Tugas telah diambil.',
                            'success'
                        );
                        $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `starttugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-rocket"></i> Start Tugas')
                                .removeClass('btn-success')
                                .addClass('btn-warning');
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan:', error);
                    }
                });
            }
        });
    }

    function starttugas(id, posisitable, name) {
        var starttugasUrl = `/newprogressreports/starttugas/${id}/${name}`;

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
                    success: function(response) {
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
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan:', error);
                    }
                });
            }
        });
    }

    function pausetugas(id, posisitable, name) {
        var pausetugasUrl = `/newprogressreports/pausetugas/${id}/${name}`;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menjeda tugas ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Jeda!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: pausetugasUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Tugas telah dijeda.',
                            'success'
                        );
                        $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `resumetugas('${id}','${posisitable}', '${name}')`)
                                .html('<i class="fas fa-play-circle"></i> Resume')
                                .removeClass('btn-secondary')
                                .addClass('btn-primary');
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan:', error);
                    }
                });
            }
        });
    }

    function resumetugas(id, posisitable, name) {
        var resumetugasUrl = `/newprogressreports/resumetugas/${id}/${name}`;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin melanjutkan tugas ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: resumetugasUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Tugas telah dilanjutkan.',
                            'success'
                        );
                        $(`#button_${id}_${posisitable}`)
                                .attr("onclick", `pausetugas('${id}', '${posisitable}', '${name}')`)
                                .html('<i class="fas fa-pause-circle"></i> Pause')
                                .removeClass('btn-primary')
                                .addClass('btn-secondary');
                        updateElapsedTime1(id, name, posisitable); // Resume elapsed time
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan:', error);
                    }
                });
            }
        });
    }

    function selesaitugas(id, posisitable, name) {
        var selesaitugasUrl = `/newprogressreports/selesaitugas/${id}/${name}`;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin telah menyelesaikan pekerjaan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: selesaitugasUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Tugas telah diselesaikan.',
                            'success'
                        );
                        $(`#button_${id}_${posisitable}`).remove();
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan:', error);
                    }
                });
            }
        });
    }
    function izinkanrevisitugas(id, posisitable, name) {
            var resetTugasUrl = `/newprogressreports/izinkanrevisitugas/${id}/${name}`;

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
                        success: function(response) {
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
                        error: function(xhr, status, error) {
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



    


</script>
<script>
<script>
    // Define variables from Blade template
    var startTime = {{$startTime}}; // Ensure $startTime is properly formatted as a JavaScript Date string or null
    var totalElapsedTime = {{ $totalTime }}; // Assuming $totalTime is the total elapsed seconds

    function updateTotalElapsedTime() {
        var totalElapsedTimeElement = document.getElementById('totalElapsedTime');

        if (startTime !== null) {
            var now = new Date();
            var start = new Date(startTime);
            var elapsedSeconds = Math.floor((now - start) / 1000) + totalElapsedTime;

            var hours = Math.floor(elapsedSeconds / 3600);
            var minutes = Math.floor((elapsedSeconds % 3600) / 60);
            var seconds = elapsedSeconds % 60;

            // Format elapsed time as HH:MM:SS
            var formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Update the displayed total elapsed time
            totalElapsedTimeElement.textContent = formattedTime;
        }
    }

    // Update elapsed time every second
    setInterval(updateTotalElapsedTime, 1000);

    // Call the function once to initiate the display
    updateTotalElapsedTime();
</script>
        
</script>
@endsection


