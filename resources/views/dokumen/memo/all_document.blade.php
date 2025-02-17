@extends('layouts.table1')

@section('container1')
    <h1>Dokumen All</h1>
@endsection

@section('container2')
    <h3 class="card-title">Page: Dokumen All</h3>
@endsection

@section('container3')
    @if(count($documents) > 0)
        @if(auth()->user()->rule=="superuser")
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <!-- Tombol untuk menghapus yang dipilih -->
                    <button type="button" class="btn btn-danger btn-sm btn-block" onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <!-- Tambahkan tombol upload di sini -->
                    <a href="" class="btn btn-primary btn-sm btn-block mb-3">Upload Dokumen</a>
                </div>
            </div>
        @endif
        <table id="example2" class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                    </th>
                    <th>Tanggal Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Timeline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    @php
                        $key = key($documents);
                        next($documents);
                        $waktudokumen = json_decode($document->timeline,true)["documentopened"];
                    @endphp
                    <tr>
                        <td>
                            <div class="icheck-primary">
                                <!-- Tambahkan name dan ID unik -->
                                <input type="checkbox" value="{{ $document->id }}" name="document_ids[]" id="checkbox{{ $key }}">
                                <label for="checkbox{{ $key }}"></label>
                            </div>
                        </td>
                        <td>
                            {{$waktudokumen}}
                            
                        </td>
                        <td>
                            <a href="{{ route('memo.show', ['id' => $document->id, 'rule' => auth()->user()->rule]) }}" class="inbox-title">
                                {{ $document->documentname }}
                            </a>
                        </td>
                        <td>
                            <div class="indicators">
                                <div class="indicator {{ $document->created_at ? 'green' : 'red' }}">
                                    <div class="indicator-tooltip">{{ $document->created_at ? 'Dikirim MTPR pada ' . $document->created_at : 'Belum dibaca' }}</div>
                                </div>

                                <div class="indicator {{ $document->pereadstatus ? 'green' : 'red' }}">
                                    <div class="indicator-tooltip">{{ $document->pereadstatus ? 'Dibaca PE pada ' . $document->pereadstatus : 'Belum dibaca' }}</div>
                                </div>

                                <div class="indicator {{ $document->underpereadstatus ? 'green' : 'red' }}">
                                    <div class="indicator-tooltip">{{ $document->underpereadstatus ? 'Dibaca Unit pada ' . $document->underpereadstatus : 'Belum dibaca' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="mt-2">
                                <p class="status-badge">
                                    Status:
                                    <span class="badge badge-{{ $document->documentstatus == 'Terbuka' ? 'success' : ($document->documentstatus == 'Pending' ? 'warning' : 'danger') }}">
                                        {{ $document->documentstatus }}
                                    </span>
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada file yang ditemukan.</p>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
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
                    checkboxes.forEach(function(checkbox) {
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
                        success: function(response) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item yang dipilih telah dihapus.',
                                icon: 'success'
                            });

                            // Refresh halaman setelah penghapusan
                            location.reload();
                        },
                        error: function(xhr, status, error) {
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

@endsection
