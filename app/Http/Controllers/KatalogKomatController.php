<?php

namespace App\Http\Controllers;

use App\Models\KatalogKomat;
use App\Models\Newbomkomat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class KatalogKomatController extends Controller
{


    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        } elseif ($jenisupload == "formatrencana") {
            $hasil = $this->importExcel($request);
        } else {
            \Log::error('Invalid upload type: ' . $jenisupload);
            return response()->json(['error' => 'Invalid upload type.'], 400);
        }

        return $hasil;
    }



    public function formatprogress(Request $request)
    {
        // Validasi file yang diunggah
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // Log file upload status
        \Log::info('File upload successful. Processing the file...');

        // Ambil file yang diunggah
        $file = $request->file('file');

        // Pastikan file valid sebelum melanjutkan
        if (!$file->isValid()) {
            \Log::error('Invalid file upload.');
            return response()->json(['error' => 'File upload failed or invalid.'], 400);
        }

        try {
            // Hapus semua data dari tabel katalog_komat
            KatalogKomat::truncate(); // Menghapus semua baris

            // Konversi Excel menjadi array
            $revisiData = Excel::toArray(new \stdClass(), $file)[0];
            if (empty($revisiData)) {
                \Log::error('No data found in the Excel file.');
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            \Log::info('Excel data successfully parsed. Processing data...');

            // Proses data yang diimpor
            $processedData = $this->progressreportexported($revisiData);

            // Lakukan insert batch untuk mempercepat
            $chunkSize = 1000; // Ukuran batch
            foreach (array_chunk($processedData, $chunkSize) as $chunk) {
                KatalogKomat::insert($chunk);
                \Log::info('Batch of ' . count($chunk) . ' records inserted successfully.');
            }

            \Log::info('All data successfully inserted.');

        } catch (\Exception $e) {
            \Log::error('Error inserting data into KatalogKomat: ' . $e->getMessage());
            return response()->json(['error' => 'Error inserting data: ' . $e->getMessage()], 500);
        }

        // Jika berhasil, kembalikan respons sukses
        return response()->json(['success' => 'Data successfully processed and saved.']);
    }




    public function progressreportexportedpenuhduplikat($importedData)
    {
        $revisiData = [];

        try {
            foreach ($importedData as $row) {
                $kodematerial = trim($row[1] ?? ""); // Kolom B
                $deskripsi = trim($row[2] ?? "");  // Kolom C
                $spesifikasi = trim($row[3] ?? "");  // Kolom D
                $UoM = trim($row[4] ?? ""); // Kolom E
                $stokUUekpedisi = trim($row[5] ?? ""); // Kolom F
                $stokUUgudang = trim($row[6] ?? ""); // Kolom G
                $stokprojectekpedisi = trim($row[7] ?? ""); // Kolom H
                $stokprojectgudang = trim($row[8] ?? ""); // Kolom I

                // Validasi untuk kolom integer
                if (!is_numeric($stokUUekpedisi)) {
                    $stokUUekpedisi = 0; // Atur ke nilai default
                }
                if (!is_numeric($stokUUgudang)) {
                    $stokUUgudang = 0;
                }
                if (!is_numeric($stokprojectekpedisi)) {
                    $stokprojectekpedisi = 0;
                }
                if (!is_numeric($stokprojectgudang)) {
                    $stokprojectgudang = 0;
                }

                // Jika ada kodematerial, tambahkan ke array
                if (!empty($kodematerial)) {
                    $revisiData[] = [
                        'kodematerial' => $kodematerial,
                        'deskripsi' => $deskripsi,
                        'spesifikasi' => $spesifikasi,
                        'UoM' => $UoM,
                        'stokUUekpedisi' => $stokUUekpedisi,
                        'stokUUgudang' => $stokUUgudang,
                        'stokprojectekpedisi' => $stokprojectekpedisi,
                        'stokprojectgudang' => $stokprojectgudang,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            \Log::info('Data successfully processed for batch insert.');

        } catch (\Exception $e) {
            \Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }

    public function progressreportexported($importedData)
    {
        $revisiData = [];
        $seenMaterials = []; // Array untuk melacak kodematerial yang sudah dimasukkan

        try {
            foreach ($importedData as $row) {
                $kodematerial = trim($row[1] ?? ""); // Kolom B
                $deskripsi = trim($row[2] ?? "");  // Kolom C
                $spesifikasi = trim($row[3] ?? "");  // Kolom D
                $UoM = trim($row[4] ?? ""); // Kolom E
                $stokUUekpedisi = trim($row[5] ?? ""); // Kolom F
                $stokUUgudang = trim($row[6] ?? ""); // Kolom G
                $stokprojectekpedisi = trim($row[7] ?? ""); // Kolom H
                $stokprojectgudang = trim($row[8] ?? ""); // Kolom I

                // Validasi untuk kolom integer
                if (!is_numeric($stokUUekpedisi)) {
                    $stokUUekpedisi = 0; // Atur ke nilai default
                }
                if (!is_numeric($stokUUgudang)) {
                    $stokUUgudang = 0;
                }
                if (!is_numeric($stokprojectekpedisi)) {
                    $stokprojectekpedisi = 0;
                }
                if (!is_numeric($stokprojectgudang)) {
                    $stokprojectgudang = 0;
                }

                // Jika kodematerial kosong, lewati
                if (empty($kodematerial)) {
                    continue;
                }

                // Cek apakah kodematerial sudah ada
                if (!in_array($kodematerial, $seenMaterials)) {
                    // Jika belum, tambahkan ke array revisiData dan seenMaterials
                    $revisiData[] = [
                        'kodematerial' => $kodematerial,
                        'deskripsi' => $deskripsi,
                        'spesifikasi' => $spesifikasi,
                        'UoM' => $UoM,
                        'stokUUekpedisi' => $stokUUekpedisi,
                        'stokUUgudang' => $stokUUgudang,
                        'stokprojectekpedisi' => $stokprojectekpedisi,
                        'stokprojectgudang' => $stokprojectgudang,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Tandai kodematerial sebagai sudah diproses
                    $seenMaterials[] = $kodematerial;
                }
            }

            \Log::info('Data successfully processed for batch insert.');

        } catch (\Exception $e) {
            \Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }




    public function showUploadForm()
    {
        return view('katalogkomat.uploadkomat');
    }

    public function index()
    {
        return view('katalogkomat.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = KatalogKomat::select(['kodematerial', 'deskripsi', 'spesifikasi', 'UoM', 'stokUUekpedisi', 'stokUUgudang', 'stokprojectekpedisi', 'stokprojectgudang']);
            return DataTables::of($data)
                ->addIndexColumn() // Menambahkan nomor urut
                ->make(true);
        }
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan kodematerial, deskripsi, atau spesifikasi
        $results = KatalogKomat::where('kodematerial', 'LIKE', '%' . $query . '%')
            ->orWhere('deskripsi', 'LIKE', '%' . $query . '%')
            ->orWhere('spesifikasi', 'LIKE', '%' . $query . '%')
            ->get();

        // Ambil daftar kodematerial yang ditemukan
        $kodematerials = $results->pluck('kodematerial');

        // Ambil proyek terkait dari tabel newbomkomat
        $newbomkomats = Newbomkomat::with('newbom.projectType')
            ->whereIn('kodematerial', $kodematerials)
            ->get();

        // Kelompokkan proyek berdasarkan kodematerial tanpa duplikasi
        $proyekData = [];
        foreach ($newbomkomats as $newbomkomat) {
            $title = $newbomkomat->newbom->projectType->title;
            if (!isset($proyekData[$newbomkomat->kodematerial])) {
                $proyekData[$newbomkomat->kodematerial] = [];
            }
            if (!in_array($title, $proyekData[$newbomkomat->kodematerial])) {
                $proyekData[$newbomkomat->kodematerial][] = $title;
            }
        }

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $latestUpdate = $results->max('created_at')->format('d/m/Y');
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
            $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";
            $textResult .= "🏢 *Plant :* _2100 INKA MADIUN_\n\n";
        }

        // Looping melalui hasil pencarian dan susun dalam format teks
        foreach ($results as $result) {
            $textResult .= "📝 *Kode Material*: " . $result->kodematerial . "\n";
            $textResult .= "📋 *Deskripsi*: " . $result->deskripsi . "\n";
            $textResult .= "📊 *Spesifikasi*: " . $result->spesifikasi . "\n";
            $textResult .= "📦 *UoM*: " . $result->UoM . "\n";
            $textResult .= "📈 *Stok UU di Ekspedisi*: " . $result->stokUUekpedisi . "\n";
            $textResult .= "📦 *Stok UU di Gudang*: " . $result->stokUUgudang . "\n";
            $textResult .= "📊 *Stok Project di Ekspedisi*: " . $result->stokprojectekpedisi . "\n";
            $textResult .= "📦 *Stok Project di Gudang*: " . $result->stokprojectgudang . "\n";

            // Tambahkan daftar proyek terkait jika ada
            if (isset($proyekData[$result->kodematerial])) {
                $textResult .= "🌍 *Terikat dengan proyek:*\n";
                foreach ($proyekData[$result->kodematerial] as $proyek) {
                    $textResult .= "- " . $proyek . "\n";
                }
            } else {
                $textResult .= "⚠️ Tidak terikat dengan proyek mana pun.\n";
            }

            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar hasil
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada katalog komat yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }




}
