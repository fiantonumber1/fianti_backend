<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
        {
            $categories = Category::all();
            return view('categories.index', compact('categories'));
        }
    // Menampilkan semua kategori
    // Menampilkan form untuk input kategori
    public function create()
    {
        return view('categories.create');
    }

    // Menyimpan kategori baru
    public function store(Request $request)
{
    $request->validate([
        'category_name' => 'required',
        'category_member.*' => 'required', // Ubah validasi ini agar bekerja dengan benar pada array
    ]);
    $category = new Category();
    $category->category_name = $request->category_name;
    $category->category_member = json_encode($request->category_member);
    $category->save();
    return redirect()->back()->with('success', 'Member berhasil dihapus');
}

public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);
    
    $newMember = $request->input('new_member');
    if($newMember) {
        $members = json_decode($category->category_member);
        $members[] = $newMember;
        $category->category_member = json_encode($members);
        $category->save();
    }
    
    return redirect()->back()->with('success', 'Member berhasil dihapus');
}
public function destroy($id)
{
    $category = Category::findOrFail($id);
    $category->delete();

    return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus');
}

public function destroyMember($categoryId, $memberId)
{
    $category = Category::findOrFail($categoryId);
    $members = json_decode($category->category_member);

    // Hapus anggota dari array
    $updatedMembers = array_filter($members, function ($member) use ($memberId) {
        return $member != $memberId;
    });

    // Simpan kembali array yang diperbarui ke dalam format JSON
    $category->category_member = json_encode(array_values($updatedMembers));
    $category->save();

    return redirect()->back()->with('success', 'Member berhasil dihapus');
}
public function storeMember(Request $request, $categoryId)
{
    $request->validate([
        'new_member' => 'required',
    ]);

    $category = Category::findOrFail($categoryId);
    $members = json_decode($category->category_member);

    // Tambahkan anggota baru ke dalam array
    $members[] = $request->new_member;

    // Simpan kembali array yang diperbarui ke dalam format JSON
    $category->category_member = json_encode($members);
    $category->save();

    return redirect()->back()->with('success', 'Member berhasil ditambahkan');
}



}
