<!-- resources/views/inputevent.blade.php -->

@extends('layouts.main')

@section('container1')
<div class="container">
    <div class="card mt-3">
        
            <div class="card-header">
                <h3 class="card-title">Buat Jadwal Baru</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('meeting.delete', ['meetingId' => $meetingId]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit">Delete Meeting</button>
                </form>

            </div>


    </div>
</div>

    


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Ambil elemen form
        const form = document.getElementById('create-event-form');

        // Tambahkan event listener untuk form submit
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Hindari pengiriman form bawaan

            // Kirim request form dengan fetch
            fetch(this.action, {
                method: this.method,
                body: new FormData(this)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Room sudah dipesan');
                }
                return response.json();
            })
            .then(data => {
                // Tampilkan pesan sukses jika request berhasil
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                }).then(() => {
                    // Redirect to calendar page
                });
            })
            .catch(error => {
                // Tampilkan pesan error jika request gagal
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message,
                });
            });
        });
    </script>
@endsection






