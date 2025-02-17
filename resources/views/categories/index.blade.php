@extends('layouts.main')

@section('container3')
    <title>Semua Kategori</title>
@endsection


@section('container2')

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Files</h3>
    </div>
    <div class="card-body">
        <span>Daftar Kategori</span>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>
    <div class="card-body">
        <ul class="list-group">
            @foreach($categories as $category)
                <li class="list-group-item">
                    <h5>{{ $category->category_name }}</h5>
                    <ul>
                        @foreach(json_decode($category->category_member) as $member)
                            <li>
                                {{ $member }}
                                <form action="{{ route('members.destroy', ['categoryId' => $category->id, 'memberId' => $member]) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Form tambah member -->
                    <form action="{{ route('members.store', $category->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="new_member">Tambah Member:</label>
                            <input type="text" id="new_member" name="new_member" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
