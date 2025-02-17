@foreach ($newMessages as $message)
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
