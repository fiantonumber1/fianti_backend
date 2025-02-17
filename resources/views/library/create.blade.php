@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                    
                        <li class="breadcrumb-item"><a href="{{route('library.index')}}">Library</a></li>
                        <li class="breadcrumb-item"><a href="#">Upload File</a></li>
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
                    <h3 class="card-title text-bold">Unggah File Baru</h3>
                </div>  
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('library.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="file_name">Nama Dokumen</label>
                            <input type="text" name="file_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="file_code">Nomor Dokumen</label>
                            <input type="text" name="file_code" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="project_id">Pilih Proyek</label>
                            <select name="project_id" class="form-control" required>
                                <option value="">-- Pilih Proyek --</option>
                                @foreach ($projects as $id => $title)
                                    <option value="{{ $id }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="path_file">Pilih File</label>
                            <input type="file" name="path_file" class="form-control-file" id="fileInput" required>
                            <div id="previewContainer" style="display: none; margin-top: 10px;">
                                <img id="previewImage" src="#" alt="Pratinjau Gambar" style="max-height: 200px; display: none;">
                                <embed id="previewPdf" src="#" type="application/pdf" style="width: 100%; height: 500px; display: none;" />
                                <video id="previewVideo" controls style="width: 100%; height: auto; display: none;">
                                    <source id="previewVideoSource" src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <audio id="previewAudio" controls style="width: 100%; height: auto; display: none;">
                                    <source id="previewAudioSource" src="#" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <iframe id="previewText" src="#" style="width: 100%; height: 500px; display: none;"></iframe>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Unggah</button>
                    </form>
                </div>  
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('fileInput').onchange = function(event) {
        var file = event.target.files[0];
        var previewImage = document.getElementById('previewImage');
        var previewPdf = document.getElementById('previewPdf');
        var previewVideo = document.getElementById('previewVideo');
        var previewAudio = document.getElementById('previewAudio');
        var previewText = document.getElementById('previewText');
        var previewContainer = document.getElementById('previewContainer');

        if (file) {
            var reader = new FileReader();
            var fileType = file.type;

            reader.onload = function(e) {
                previewContainer.style.display = 'block'; // Tampilkan kontainer pratinjau

                if (fileType.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';   // Tampilkan pratinjau gambar
                    previewPdf.style.display = 'none';      // Sembunyikan pratinjau PDF
                    previewVideo.style.display = 'none';    // Sembunyikan pratinjau video
                    previewAudio.style.display = 'none';    // Sembunyikan pratinjau audio
                    previewText.style.display = 'none';     // Sembunyikan pratinjau teks
                } else if (fileType === 'application/pdf') {
                    previewPdf.src = e.target.result;
                    previewPdf.style.display = 'block';     // Tampilkan pratinjau PDF
                    previewImage.style.display = 'none';    // Sembunyikan pratinjau gambar
                    previewVideo.style.display = 'none';    // Sembunyikan pratinjau video
                    previewAudio.style.display = 'none';    // Sembunyikan pratinjau audio
                    previewText.style.display = 'none';     // Sembunyikan pratinjau teks
                } else if (fileType.startsWith('video/')) {
                    previewVideo.style.display = 'block';   // Tampilkan pratinjau video
                    previewVideoSource.src = e.target.result;
                    previewImage.style.display = 'none';    // Sembunyikan pratinjau gambar
                    previewPdf.style.display = 'none';      // Sembunyikan pratinjau PDF
                    previewAudio.style.display = 'none';    // Sembunyikan pratinjau audio
                    previewText.style.display = 'none';     // Sembunyikan pratinjau teks
                } else if (fileType.startsWith('audio/')) {
                    previewAudio.style.display = 'block';   // Tampilkan pratinjau audio
                    previewAudioSource.src = e.target.result;
                    previewImage.style.display = 'none';    // Sembunyikan pratinjau gambar
                    previewPdf.style.display = 'none';      // Sembunyikan pratinjau PDF
                    previewVideo.style.display = 'none';    // Sembunyikan pratinjau video
                    previewText.style.display = 'none';     // Sembunyikan pratinjau teks
                } else if (fileType.startsWith('text/') || fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    previewText.src = e.target.result;
                    previewText.style.display = 'block';    // Tampilkan pratinjau teks
                    previewImage.style.display = 'none';    // Sembunyikan pratinjau gambar
                    previewPdf.style.display = 'none';      // Sembunyikan pratinjau PDF
                    previewVideo.style.display = 'none';    // Sembunyikan pratinjau video
                    previewAudio.style.display = 'none';    // Sembunyikan pratinjau audio
                } else {
                    previewContainer.style.display = 'none';
                    alert('Format file tidak bisa ditampilkan.');
                }
            };

            reader.readAsDataURL(file); // Membaca file dan mengubahnya jadi data URL
        }
    };
</script>
@endpush
