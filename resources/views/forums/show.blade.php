@extends('layouts.split3')

@php
    function extractYouTubeId($url) {
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        return $vars['v'] ?? null;
    }
@endphp

@section('container2')
    <div class="container">
        <div class="card card-danger direct-chat direct-chat-warning">
            <div class="card-header">
                <h3 class="card-title">{{ $forum->topic }} | {{ $forum->description }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="height: 800px; overflow-y: auto;">
                <div class="direct-chat-messages" id="chat-container" style="height: 800px; overflow-y: auto;">
                    <div class="direct-chat-msg left">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left">
                                <p>System</p>
                            </span>
                            <span class="direct-chat-timestamp float-right"></span>
                        </div>
                        <img class="direct-chat-img" src="{{ asset('images/usericon1.png') }}" alt="message user image" />
                        <div class="direct-chat-text">
                            Sistem realtime chat hanya untuk sebagian akun, untuk sisanya perlu melakukan refresh (Menghindari INKA Office Melambat)
                        </div>
                    </div>
                    @foreach ($conversationMessages as $message)
                        <div class="direct-chat-msg {{ $message->user->id == auth()->id() ? 'right' : 'left' }}">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left">
                                    <p>{{ $message->user->name }}</p>
                                </span>
                                <span class="direct-chat-timestamp float-right">{{ $message->created_at }}</span>
                            </div>
                            <img class="direct-chat-img" src="{{ asset('images/usericon1.png') }}" alt="message user image" />
                            <div class="direct-chat-text">
                                

                                @if ($message->chat_type === 'youtube')
                                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ extractYouTubeId($message->chat) }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @else
                                    {{ $message->chat }}
                                @endif
                                @if($message->chatFiles)
                                    @foreach ($message->chatFiles as $file)
                                        @php
                                            $newLinkFile = str_replace('uploads/', '', $file->link);
                                        @endphp
                                        <div class="card-text mt-2">
                                            <a href="{{ route('document.preview', ['linkfile' => $newLinkFile]) }}">{{ $file->filename }}</a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            <div class="card-footer">
                <form id="chat-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="chat">Your Message</label>
                        <textarea name="chat" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="filenames">Pilih File</label>
                        <input type="file" class="form-control-file" id="filenames" name="filenames[]" multiple>
                    </div>
                    <div id="fileList"></div>

                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="password" value="{{ request()->password }}">
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('chat-form');
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

        let lastMessageId = {{ $conversationMessages->last()->id ?? 0 }};
            function loadChats() {
                $.ajax({
                    url: '{{ route('forums.newChats', $forum->id) }}',
                    type: 'GET',
                    data: {
                        last_message_id: lastMessageId,
                        password: '{{ request()->password }}'
                    },
                    success: function(data) {
                        if (data.trim() !== '') {
                            $('#chat-container').append(data);
                            lastMessageId = $('#chat-container .direct-chat-msg:last').data('message-id');
                        }
                    },
                    error: function() {
                        console.log('Failed to fetch new messages');
                    }
                });
            }
            
        function sendChat(event) {
            event.preventDefault();

            var formData = new FormData($('#chat-form')[0]);

            $.ajax({
                url: '{{ route('forums.chats.store', $forum->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    loadChats();
                    $('#chat-form')[0].reset();
                },
                error: function() {
                    console.log('Failed to send message');
                }
            });
        }


        $(document).ready(function() {
            @if ($isSpecialUser)
                
            @endif
            setInterval(loadChats, 10000); // 10 detik
            $('#chat-form').on('submit', sendChat);
        });
    </script>
@endsection
