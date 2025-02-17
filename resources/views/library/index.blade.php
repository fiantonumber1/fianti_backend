@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="#">Library</a></li>
                    </ol>
                </div>
            </div>
        </div>
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
                    <h3 class="card-title text-bold">Library</h3>
                </div>  
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('library.create') }}" class="btn btn-primary mb-3">Unggah File Baru</a>

                    <!-- Dropdown untuk memilih proyek -->
                    <div class="form-group">
                        <label for="projectDropdown">Pilih Proyek:</label>
                        <select class="form-control" id="projectDropdown">
                            <option value="" selected disabled>Pilih Proyek</option>
                            @foreach($files->groupBy('project.title') as $projectTitle => $projectFiles)
                                <option value="{{ Str::slug($projectTitle) }}">{{ $projectTitle ?: 'Proyek Tidak Ada' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Container untuk menampilkan tabel proyek -->
                    <div id="projectTables">
                        @foreach($files->groupBy('project.title') as $projectTitle => $projectFiles)
                            <div class="project-table" id="table-{{ Str::slug($projectTitle) }}" style="display: none;">
                                <table class="table table-bordered table-hover mt-3">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Dokumen</th>
                                            <th>Nomor Dokumen</th>
                                            <th>Path File</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectFiles as $index => $file)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $file->file_name }}</td>
                                                <td>{{ $file->file_code }}</td>
                                                <td>
                                                    @if ($file->files)
                                                        @foreach ($file->files as $fileItem)
                                                            <div class="card-text mt-2">
                                                                @include('library.fileinfo', ['file' => $fileItem])
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p>Tidak ada file terkait</p>
                                                    @endif 
                                                </td>
                                                <td>
                                                    <a href="{{ route('library.edit', $file->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                    <form action="{{ route('library.destroy', $file->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus file ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTables untuk setiap tabel proyek
            @foreach($files->groupBy('project.title') as $projectTitle => $projectFiles)
                $('#table-{{ Str::slug($projectTitle) }} table').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
            @endforeach

            // Event listener untuk dropdown
            $('#projectDropdown').on('change', function() {
                var selectedProject = $(this).val();

                // Sembunyikan semua tabel proyek
                $('.project-table').hide();

                // Tampilkan tabel proyek yang dipilih
                if (selectedProject) {
                    $('#table-' + selectedProject).show();
                }
            });
        });
    </script>
@endpush
