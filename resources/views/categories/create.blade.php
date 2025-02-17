@extends('layouts.main')

@section('container3')
    <title>Input Kategori</title>
@endsection

@section('container1')
    <h1>Input Kategori</h1>
@endsection

@section('container2')
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Input Kategori Baru</div>

                    <div class="card-body">
                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="category_name">Nama Kategori:</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="category_member">Anggota Kategori:</label>
                                <div id="member_fields">
                                    <input type="text" name="category_member[]" class="form-control mb-2" required>
                                </div>
                                <button type="button" class="btn btn-primary" id="add_member">Tambah Anggota</button>
                            </div>

                            <button type="submit" class="btn btn-success">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addMemberButton = document.getElementById('add_member');
            const memberFields = document.getElementById('member_fields');

            addMemberButton.addEventListener('click', function () {
                const newMemberField = document.createElement('div');
                newMemberField.innerHTML = '<input type="text" name="category_member[]" class="form-control mb-2" required>';
                memberFields.appendChild(newMemberField);
            });
        });
    </script>
@endsection
