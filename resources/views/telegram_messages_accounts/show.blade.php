<!-- resources/views/telegram_messages_accounts/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Show Telegram Message Account</title>
</head>
<body>
    <h1>Show Telegram Message Account</h1>

    <p>Account: {{ $telegramMessagesAccount->account }}</p>
    <p>Telegram ID: {{ $telegramMessagesAccount->telegram_id }}</p>

    <a href="{{ route('telegram_messages_accounts.edit', $telegramMessagesAccount) }}">Edit</a>
    <form action="{{ route('telegram_messages_accounts.destroy', $telegramMessagesAccount) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Delete</button>
    </form>

    <a href="{{ route('telegram_messages_accounts.index') }}">Back to List</a>
</body>
</html>
