@extends('layouts.split3')

@section('container1')
    {{-- Feedback Form --}}
    <div class="col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Submit {{$kind}} for Document: {{ $ramsdocument->documentname }}</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('ramsdocuments.submitFeedbackCombine', $ramsdocument->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pic" value="{{ auth()->user()->rule }}">
                    <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="level" value="{{$level}}">
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                    
                    @if($kind=="feedback")
                        @if(auth()->user()->rule == "RAMS")
                            <div class="form-group">
                                <label for="conditionoffile">Verifikasi:</label>
                                <select class="form-control" id="conditionoffile" name="conditionoffile">
                                    <option value="respond">Respon</option>
                                </select>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="conditionoffile">Verifikasi:</label>
                                <select class="form-control" id="conditionoffile" name="conditionoffile">
                                    <option value="approve">Ya, Approve</option>
                                    <option value="incomplete">Ya, Kurang Kelengkapan</option>
                                    <option value="wrong">Tidak, Dokumen Salah</option>
                                </select>
                            </div>
                        @endif
                    @elseif($kind=="smfeedback")
                        @if(auth()->user()->rule == "RAMS")
                            <div class="form-group">
                                <label for="conditionoffile">Verifikasi:</label>
                                <select class="form-control" id="conditionoffile" name="conditionoffile">
                                    <option value="respond">Respon</option>
                                </select>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="conditionoffile">Verifikasi:</label>
                                <select class="form-control" id="conditionoffile" name="conditionoffile">
                                    <option value="approve">Ya, Approve</option>
                                    <option value="incomplete">Ya, Kurang Kelengkapan</option>
                                    <option value="wrong">Tidak, Dokumen Salah</option>
                                </select>
                            </div>
                        @endif
                    @elseif($kind=="combine")
                        <div class="form-group">
                            <label for="conditionoffile">Verifikasi:</label>
                            <select class="form-control" id="conditionoffile" name="conditionoffile">
                                <option value="approve">Kombinasi Feedback</option>
                            </select>
                        </div>
                    @elseif($kind=="finalisasi")
                        <div class="form-group">
                            <label for="conditionoffile">Verifikasi:</label>
                            <select class="form-control" id="conditionoffile" name="conditionoffile">
                                <option value="approve">Dokumen Gabungan Terakhir</option>
                            </select>
                        </div>
                    @endif
                    
                    @if($kind=="feedback")
                        <input type="hidden" name="conditionoffile2" value="feedback">
                    @elseif($kind=="smfeedback")
                        <input type="hidden" name="conditionoffile2" value="smfeedback">
                    @elseif($kind=="combine")
                        <input type="hidden" name="conditionoffile2" value="combine">
                    @elseif($kind=="finalisasi")
                        <input type="hidden" name="conditionoffile2" value="finalisasi">
                    @endif
                    
                    <div class="form-group">
                        <label for="comment">Comment (optional):</label>
                        <textarea class="form-control" id="comment" name="comment" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="filenames">Pilih File</label>
                        <input type="file" class="form-control-file" id="filenames" name="filenames[]" multiple >
                    </div>
                    <div id="fileList"></div>
                    <div>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                    <!-- <div>
                        <button type="button" class="btn btn-success" onclick="confirmUpload()">Upload</button>
                    </div> -->
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('filenames');
            const fileList = document.getElementById('fileList');

            fileInput.addEventListener('change', function () {
                fileList.innerHTML = '';
                Array.from(fileInput.files).forEach(file => {
                    const listItem = document.createElement('div');
                    listItem.textContent = file.name;
                    fileList.appendChild(listItem);
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting normally

                // You can add additional validation logic here if needed

                // Show SweetAlert with confirmation message
                Swal.fire({
                    title: 'Yakin ingin unggah file?',
                    text: 'Pilih "Ya" untuk mengunggah file.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'File Berhasil Diunggah!',
                            text: 'Tindakan selanjutnya di sini...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        form.submit(); // Submit the form
                    }
                });
            });
        });
    </script>
@endsection

@section('container3')
    <a href="{{ route('ramsdocuments.show', $ramsdocument->id) }}" class="btn btn-primary">Back</a>
@endsection
