<!-- resources/views/telegram_messages_accounts/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Telegram Messages Accounts</title>
</head>
<body>
    <h1>Telegram Messages Accounts</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <a href="{{ route('telegram_messages_accounts.create') }}">Create New Account</a>

    <ul>
        @foreach($accounts as $account)
            <li>
                {{ $account->account }} - {{ $account->telegram_id }}
                <a href="{{ route('telegram_messages_accounts.show', $account) }}">View</a>
                <a href="{{ route('telegram_messages_accounts.edit', $account) }}">Edit</a>
                <form action="{{ route('telegram_messages_accounts.destroy', $account) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
