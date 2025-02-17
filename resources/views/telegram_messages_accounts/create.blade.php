<!-- resources/views/telegram_messages_accounts/create.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Telegram Message Account</title>
</head>
<body>
    <h1>Create Telegram Message Account</h1>

    <form action="{{ route('telegram_messages_accounts.store') }}" method="POST">
        @csrf
        <label for="account">Account:</label>
        <input type="text" id="account" name="account" required>
        <br>
        <label for="telegram_id">Telegram ID:</label>
        <input type="text" id="telegram_id" name="telegram_id" required>
        <br>
        <button type="submit">Create</button>
    </form>

    <a href="{{ route('telegram_messages_accounts.index') }}">Back to List</a>
</body>
</html>
