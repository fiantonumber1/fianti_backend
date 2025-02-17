@extends('layouts.split3')

@section('container1')
    {{-- Feedback Form --}}
    <div class="col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Submit Feedback for Hazard Log: {{ $hazardLog->hazard_ref }}</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('hazard_logs.submitFeedback', $hazardLog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pic" value="{{ auth()->user()->rule }}">
                    <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="level" value="{{ $level }}">
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                    
                    @if($kind=="feedback")
                        <input type="hidden" name="conditionoffile" value="draft">
                    @elseif($kind=="combine")
                        <input type="hidden" name="conditionoffile" value="approve">
                    @endif
                    @if($kind=="feedback")
                        <input type="hidden" name="conditionoffile2" value="feedback">
                    @elseif($kind=="combine")
                        <input type="hidden" name="conditionoffile2" value="combine">
                    @endif
                    <div class="form-group">
                        <label for="comment">Comment (optional):</label>
                        <textarea class="form-control" id="comment" name="comment" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="filenames">Pilih File</label>
                        <input type="file" class="form-control-file" id="filenames" name="filenames[]" multiple>
                    </div>
                    <div id="fileList"></div>
                    <div>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
    </script>
@endsection
