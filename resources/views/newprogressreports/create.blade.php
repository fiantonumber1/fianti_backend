<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Progress Report</title>
</head>
<body>
    <h1>Create New Progress Report</h1>
    <!-- Form untuk input progress report -->
    <form method="POST" action="{{ route('newprogressreports.store', ['newreport' => $newreport]) }}">
        @csrf
        <!-- Isi formulir di sini sesuai dengan kebutuhan -->
        <input type="hidden" id="newreport_id" name="newreport_id" value="{{ $newreport }}">
        <label for="nodokumen">No Dokumen:</label><br>
        <input type="text" id="nodokumen" name="nodokumen"><br><br>
        <label for="namadokumen">Nama Dokumen:</label><br>
        <input type="text" id="namadokumen" name="namadokumen"><br><br>
        <label for="level">Level:</label><br>
        <input type="number" id="level" name="level"><br><br>
        <label for="checker">Checker:</label><br>
        <input type="text" id="checker" name="checker"><br><br>
        <label for="deadlinerelease">Deadline Release:</label><br>
        <input type="date" id="deadlinerelease" name="deadlinerelease"><br><br>
        <label for="documentkind">Document Kind:</label><br>
        <input type="text" id="documentkind" name="documentkind"><br><br>
        <label for="realisasi">Realisasi:</label><br>
        <input type="number" id="realisasi" name="realisasi"><br><br>
        <label for="status">Status:</label><br>
        <input type="text" id="status" name="status"><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
