<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileManagement;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CollectFile;

class FileManagementController extends Controller
{
    
    public function index()
    {
        // Tambahkan eager loading untuk relasi 'files' dan 'project'
        $files = FileManagement::with(['project', 'files'])->get();
        return view('library.index', compact('files'));
    }

    public function create()
    {
        $projects = ProjectType::pluck('title', 'id'); // Ambil project untuk dropdown
        return view('library.create', compact('projects'));
    }

   

    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'file_code' => 'required',
            'project_id' => 'required|exists:project_types,id',
            'path_file' => 'required|file',
        ]);

        // Cek apakah file dengan file_code sudah ada
        $existingFile = FileManagement::where('file_code', $request->input('file_code'))
                                    ->where('project_id', $request->input('project_id'))
                                    ->first();

        if ($existingFile) {
            return redirect()->back()->withErrors(['file_code' => 'Dokumen dengan kode file ini sudah ada.']);
        }

        // Simpan data file di FileManagement
        $data = new FileManagement();
        $data->project_id = $request->input('project_id');
        $data->file_name = $request->input('file_name');
        $data->file_code = $request->input('file_code');
        $data->user_id = Auth::id();

        // Proses upload file
        if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $count = 0;
            $filename = "{$originalFileName}.{$extension}";
            $newFilename = $filename;

            // Memastikan nama file unik
            while (CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = "{$originalFileName}_{$count}.{$extension}";
            }

            // Simpan file dan catat path
            $path = $file->storeAs('public/uploads', $newFilename);
            $data->path_file = str_replace('public/', '', $path); // Simpan path file yang sudah diupload
        }

        // Simpan data ke database
        $data->save();

        // Simpan informasi file di CollectFile
        $collectFile = new CollectFile();
        $collectFile->filename = $newFilename;
        $collectFile->link = str_replace('public/', '', $path);;
        $collectFile->collectable_id = $data->id;
        $collectFile->collectable_type = FileManagement::class;
        $collectFile->save();

        return redirect()->route('library.index')->with('success', 'File berhasil diunggah!');
    }


    public function edit($id)
    {
        $file = FileManagement::findOrFail($id);
        $projects = ProjectType::pluck('title', 'id');
        return view('library.edit', compact('file', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $fileManagement = FileManagement::findOrFail($id);

        $request->validate([
            'file_name' => 'required',
            'file_code' => 'required',
            'project_id' => 'required|exists:project_types,id',
            'path_file' => 'file',
        ]);

        $fileManagement->file_name = $request->input('file_name');
        $fileManagement->file_code = $request->input('file_code');
        $fileManagement->project_id = $request->input('project_id');

        if ($request->hasFile('path_file')) {
            $file = $request->file('path_file');
            $path = $file->store('uploads/files', 'public');
            $fileManagement->path_file = $path;
        }

        $fileManagement->save();

        return redirect()->route('library.index')->with('success', 'File berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Temukan file yang akan dihapus dari FileManagement
        $file = FileManagement::findOrFail($id);

        // Hapus file dari CollectFile terlebih dahulu
        $collectFile = CollectFile::where('collectable_id', $file->id)
                                ->where('collectable_type', FileManagement::class)
                                ->first();
        
        if ($collectFile) {
            // Hapus file fisik jika ada di server
            if ($collectFile->link && Storage::disk('public')->exists($collectFile->link)) {
                Storage::disk('public')->delete($collectFile->link);
            }
            
            // Hapus record dari CollectFile
            $collectFile->delete();
        }

        // Hapus file dari FileManagement
        if ($file->path_file && Storage::disk('public')->exists($file->path_file)) {
            Storage::disk('public')->delete($file->path_file);
        }

        // Hapus record dari FileManagement
        $file->delete();

        return redirect()->route('library.index')->with('success', 'File berhasil dihapus!');
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan file_name atau file_code di FileManagement
        $results = FileManagement::where('file_name', 'LIKE', '%' . $query . '%')
            ->orWhere('file_code', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $latestUpdate = $results->max('created_at')->format('d/m/Y');
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
            $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";
        }

        // Looping melalui hasil pencarian
        foreach ($results as $result) {
            // Cari collectable_id di CollectFile berdasarkan file yang ditemukan
            $collectFile = CollectFile::where('collectable_id', $result->id)
                ->where('collectable_type', FileManagement::class)
                ->first();

            // Tampilkan informasi hasil pencarian
            $textResult .= "📄 *Nama Dokumen*: " . $result->file_name . "\n";
            $textResult .= "📋 *Nomor Dokumen*: " . $result->file_code . "\n";
            $textResult .= "📅 *Tanggal Dibuat*: " . $result->created_at->format('d/m/Y') . "\n";
            
            // Jika collectFile ditemukan, tambahkan instruksi unduh
            if ($collectFile) {
                $textResult .= "📂 *Panggil Dokumen*: Unduh dengan instruksi: Downloadfile_" . $collectFile->id . "\n";
            } else {
                $textResult .= "⚠️ *File tidak ditemukan di koleksi file*.\n";
            }

            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar hasil
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada file yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }


}
