<!-- resources/views/telegram_messages_accounts/edit.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Telegram Message Account</title>
</head>
<body>
    <h1>Edit Telegram Message Account</h1>

    <form action="{{ route('telegram_messages_accounts.update', $telegramMessagesAccount) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="account">Account:</label>
        <input type="text" id="account" name="account" value="{{ $telegramMessagesAccount->account }}" required>
        <br>
        <label for="telegram_id">Telegram ID:</label>
        <input type="text" id="telegram_id" name="telegram_id" value="{{ $telegramMessagesAccount->telegram_id }}" required>
        <br>
        <button type="submit">Update</button>
    </form>

    <a href="{{ route('telegram_messages_accounts.index') }}">Back to List</a>
</body>
</html>
