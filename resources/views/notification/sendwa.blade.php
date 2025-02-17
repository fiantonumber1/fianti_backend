@extends('layouts.main')

@section('container1')
    <div class="col-sm-6">
        <h1>Kirim Pesan WhatsApp</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
            <li class="breadcrumb-item active">Kirim Pesan WhatsApp</li>
        </ol>
    </div>
@endsection

@section('container2')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Kirim Pesan</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('notification.sendwa') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="phonenumber">Pilih Nomor Telepon:</label>
                    <div>
                        <input type="checkbox" id="select_all"> <label for="select_all">Pilih Semua</label>
                    </div>
                    @foreach($userphonebook as $user)
                        <div>
                            <input type="checkbox" class="checkbox-phonenumber" id="phonenumber_{{ $loop->index }}" name="phonenumbers[]" value="{{ $user->waphonenumber }}">
                            <label for="phonenumber_{{ $loop->index }}">{{ $user->name }}</label>
                        </div>
                    @endforeach
                    <small class="form-text text-muted">Anda dapat memilih lebih dari satu nomor telepon.</small>
                </div>

                <div class="form-group">
                    <label for="pesan">Pesan:</label>
                    <textarea id="pesan" name="pesan" class="form-control" rows="4" required placeholder="Tulis pesan yang akan dikirim"></textarea>
                </div>

                <div class="form-group">
                    <label for="sender_name">Nama Pengirim:</label>
                    <select id="sender_name" name="sender_name" class="form-control">
                        <option value="{{ $senderName }}">{{ $senderName }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Kirim</button>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <script>
        document.getElementById('select_all').addEventListener('click', function(e) {
            var checkboxes = document.querySelectorAll('.checkbox-phonenumber');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = e.target.checked;
            });
        });
    </script>
@endsection


