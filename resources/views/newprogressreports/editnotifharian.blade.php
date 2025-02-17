@extends('layouts.universal')

@section('container2')
    <div class="content-header mt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left mt-1">
                        <li class="breadcrumb-item"><a href="">Progres</a></li>
                        <li class="breadcrumb-item active text-bold"><a href="">Edit Notif Harian Unit</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mt-2">
        <div class="card card-danger card-outline mt-2">
            <!-- Display success message -->
            @if(session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-header mt-1">
                <h3 class="card-title text-bold">Edit Notif Harian Unit</h3>
            </div>

            <div class="card-body mt-1">
                <form action="{{ route('newprogressreports.update-notif-harian-unit', $notifHarianUnit->id) }}" method="POST">
                    @csrf
                    <label for="title">Title:</label><br>
                    <input type="text" id="title" name="title" value="{{ $notifHarianUnit->title }}" required><br><br>

                    <label for="documentkind">Select Document Kinds:</label><br>
                    <select id="documentkind" name="documentkind[]" multiple required>
                        @foreach ($documentKinds as $documentKind)
                            <option value="{{ $documentKind->id }}" 
                                @if(in_array($documentKind->id, $selectedDocumentKinds)) selected @endif>
                                {{ $documentKind->name }}
                            </option>
                        @endforeach
                    </select><br><br>

                    <label for="telegrammessagesaccount_id">Select Telegram Account:</label><br>
                    <select id="telegrammessagesaccount_id" name="telegrammessagesaccount_id">
                        <option value="">None</option>
                        @foreach ($telegrammessagesaccounts as $account)
                            <option value="{{ $account->id }}" 
                                @if($account->id == $notifHarianUnit->telegrammessagesaccount_id) selected @endif>
                                {{ $account->account }}
                            </option>
                        @endforeach
                    </select><br><br>

                    <button type="submit">Update Notif Harian Unit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
