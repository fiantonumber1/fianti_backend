<?php

namespace App\Http\Controllers;
use App\Models\File;
use App\Models\CollectFile;
use App\Models\Category;
use App\Models\User;
use App\Models\Notification;
use App\Models\NewMemo;
use App\Imports\UsersImport;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Http\Request;
use App\Models\Log as AppLog;
use App\Exports\DokumensExport;
use App\Imports\DokumensImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;




class FileController extends Controller
{

    protected $logController;
    protected $bottelegramController;

    public function __construct(LogController $logController, BotTelegramController $bottelegramController)
    {
        $this->logController = $logController;
        $this->bottelegramController = $bottelegramController;
    }



    public function updateinformasimetadata(Request $request, $id)
    {
        try {
            // Ambil data file berdasarkan ID
            $file = File::findOrFail($id);

            // Periksa apakah ada file lain dengan nama yang sama
            $existingFile = File::where('filename', $request->input('filename'))->where('id', '!=', $id)->first();

            // Jika ada file dengan nama yang sama dan ID yang berbeda, lemparkan pengecualian
            if ($existingFile) {
                throw new \Exception("File dengan nama yang sama sudah ada.");
            }

            // Jika tidak ada file dengan nama yang sama, lanjutkan dengan pembaruan
            $file->update([
                'filename' => $request->input('filename'),
                'metadata' => $request->input('metadata'),
                'project_type' => $request->input('project_type'),
                'project_pic' => $request->input('project_pic'),
                // tambahkan atribut lain yang ingin diupdate di sini
            ]);

            $informasiupload = "Berhasil mengupdate file.";
        } catch (\Exception $e) {
            $informasiupload = "Gagal mengupdate file: " . $e->getMessage();
        }
        return redirect()->route('metadata.show', ['id' => $file->id]); // Mengarahkan kembali ke halaman metadata/1 dengan data yang diperlukan
    }


    public function updateinformasimemo(Request $request, $id)
    {
        try {

            $document = NewMemo::findOrFail($id);
            $documentsebelum = $document;
            $timeline = json_decode($document->timeline, true);

            if (!isset($timeline['documentshared'])) {
                $timeline['documentshared'] = now();
            }

            $remaininformation = json_decode($document->remaininformation, true);
            $additionalkomat = [];
            if ($request->has('new_komponen') && $request->has('new_kodematerial') && $request->has('new_supplier')) {
                $komponenList = $request->input('new_komponen');
                $kodeMaterialList = $request->input('new_kodematerial');
                $supplierList = $request->input('new_supplier');
                foreach ($komponenList as $key => $komponen) {

                    if ($komponen != '' && $kodeMaterialList[$key] != '' && $supplierList[$key] != "") {
                        $totalan = [];
                        $totalan['komponen'] = $komponen;
                        $totalan['kodematerial'] = $kodeMaterialList[$key];
                        $totalan['supplier'] = $supplierList[$key];
                        $totalan['timestamp'] = now();
                        $additionalkomat[] = json_encode($totalan);
                    }

                }
            }

            $listkomats = json_decode(json_decode($document->remaininformation)->komat);
            $updatedkomats = array_merge($listkomats, $additionalkomat);
            $remaininformation['komat'] = json_encode($updatedkomats);
            if ($request->has('project_pic')) {
                $document->update([
                    'documentname' => $request->input('documentname'),
                    'project_type' => $request->input('project_type'),
                    'memokind' => $request->input('memokind'),
                    'project_pic' => json_encode($request->input('project_pic')),
                    'remaininformation' => json_encode($remaininformation),
                    'timeline' => json_encode($timeline),
                    'underpereadstatus' => now(),
                ]);

                foreach ($request->input('project_pic') as $pic) {
                    try {
                        $namaFile = $request->input('documentname');
                        $namaProject = $request->input('project_type');
                        $iddocument = $id;
                        $namaDivisi = $pic;
                        $status = "Terkirim"; // Set status to "Terkirim" by default
                        $alasan = ""; // Empty reason by default

                        $existingFile = Notification::where('nama_file', $namaFile)
                            ->where('nama_divisi', $namaDivisi)
                            ->first();

                        if (!$existingFile) {
                            // Saving data to the database using model
                            Notification::create([
                                'nama_file' => $namaFile,
                                'nama_project' => $namaProject,
                                'NewMemo_id' => $iddocument,
                                'nama_divisi' => $namaDivisi,
                                'status' => $status,
                                'alasan' => $alasan,
                                'sudahdibaca' => "belum dibaca",
                                'notificationcategory' => $request->input('notificationcategory'),
                            ]);

                            Notification::create([
                                'nama_file' => $namaFile,
                                'nama_project' => $namaProject,
                                'NewMemo_id' => $iddocument,
                                'nama_divisi' => "Manager " . $namaDivisi, // Menggunakan operator titik (.)
                                'status' => $status,
                                'alasan' => $alasan,
                                'sudahdibaca' => "belum dibaca",
                                'notificationcategory' => $request->input('notificationcategory'),
                            ]);
                            $pesan = "Memo " . $namaFile . " dikirimkan ke unit ini untuk dicek/dikerjakan.";
                            $jenispesan = "text";
                            $this->bottelegramController->informasichatbot($pesan, $namaDivisi, $jenispesan);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error in storing data: ' . $e->getMessage());
                    }
                }

                $informasiupload = "Berhasil mengupdate file.";
            } else {
                $document->update([
                    'documentname' => $request->input('documentname'),
                    'project_type' => $request->input('project_type'),
                    'memokind' => $request->input('memokind'),
                    'remaininformation' => json_encode($remaininformation),
                    'timeline' => json_encode($timeline),
                    'underpereadstatus' => now(),
                ]);
            }


            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data ditambahkan',
                    'datasebelum' => $documentsebelum,
                    'datasesudah' => '',
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'bomaddition',
            ]);



        } catch (\Exception $e) {
            $informasiupload = "Gagal mengupdate file: " . $e->getMessage();
        }
        $pesan = 'Memo: ' . $document->id . ' berhasil diedit.';
        $this->logController->updatelog($document->id, $pesan, 'Memo berhasil diedit', auth()->user()->name, 'memo');
        return redirect()->route('memo.show', ['id' => $document->id]);
    }


    public function uploadForm()
    {
        $documentcategory = Category::where('category_name', 'documentkind')->pluck('category_member');
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        return view('NewMemo/memo/uploadMTPR', [
            'informasi' => "",
            'filelinkId' => "",
            'documentcategory' => $documentcategory,
            'categoryproject' => $categoryproject,
        ]);
    }

    public function showuploadfile()
    {
        return view('file.uploadfile');
    }

    public function postuploadfile(Request $request)
    {
        // Ambil daftar nama file yang sudah ada
        $existingFiles = File::pluck('filename')->toArray();

        // Upload file baru jika ada
        foreach ($request->file('file') as $key => $uploadedFile) {
            // Ambil nama pengguna dari objek autentikasi
            $userName = auth()->user()->name;

            // Dapatkan nama file yang diunggah
            $filename = $request->input('uploadfile');

            // Dapatkan ekstensi file
            $fileFormat = $uploadedFile->getClientOriginalExtension();

            // Hapus ekstensi file dari nama file
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

            // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
            $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

            // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file

            $filename = $filenameWithUserAndFormat;
            // Periksa apakah nama file sudah ada
            $count = 0;
            $newFilename = $filename;
            while (in_array($newFilename, $existingFiles)) {
                $count++;
                $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            }

            // Jika nama file sudah ada, lanjutkan dengan menyimpan file
            $path = $uploadedFile->storeAs('uploads', $newFilename);

            // Simpan informasi file baru ke dalam database
            File::create([
                'filename' => $newFilename,
                'metadata' => "",
                'linkfile' => $path,
                'category' => "",
                'project_type' => "",
                'documentname' => "",
                'author' => auth()->user()->name,
                'comment' => "Tidak ada",
                'count' => $count + 1
            ]);
        }
        return redirect()->intended('/');
    }

    public function ShowUploadDocMTPRExcell()
    {
        return view('NewMemo.memo.massuploadMTPR');
    }

    public function uploadDocMTPRExcel(Request $request)
    {
        Excel::import(new DokumensImport, request()->file('excel_file'));
        // Redirect to the intended URL or home
        return redirect()->intended('/');
    }

    public function massuploaduser()
    {
        return view('auth.massuploaduser');
    }
    public function uploadmassuploaduser(Request $request)
    {
        Excel::import(new UsersImport, request()->file('excel_file'));
        // Redirect to the intended URL or home
        return redirect()->intended('/');
    }


    public function uploadDocMTPR(Request $request)
    {
        // Validasi request
        $request->validate([
            'file.*' => 'required',
            'category' => 'required',
            'project_type' => 'required',
        ]);

        // Inisialisasi informasi upload
        $informasiupload = "";
        $userinformations = [];
        $listfilenames = [];
        $listmetadatas = [];
        $listlinkfiles = [];

        // Ambil daftar nama file yang sudah ada
        $existingFiles = File::pluck('filename')->toArray();
        // Ambil nama pengguna dari objek autentikasi
        $userName = auth()->user()->name;
        // Upload file baru jika ada
        foreach ($request->file('file') as $key => $uploadedFile) {


            // Dapatkan nama file yang diunggah
            $filename = $uploadedFile->getClientOriginalName();

            // Dapatkan ekstensi file
            $fileFormat = $uploadedFile->getClientOriginalExtension();

            // Hapus ekstensi file dari nama file
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

            // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
            $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

            // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file

            $filename = $filenameWithUserAndFormat;
            // Periksa apakah nama file sudah ada
            $count = 0;
            $newFilename = $filename;
            while (in_array($newFilename, $existingFiles)) {
                $count++;
                $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            }

            // Jika nama file sudah ada, lanjutkan dengan menyimpan file
            $path = $uploadedFile->storeAs('uploads', $newFilename);

            // Tambahkan informasi bahwa pengunggahan berhasil untuk setiap file
            $informasiupload .= "File $newFilename sukses terupload. ";

            // Simpan informasi file baru ke dalam database
            File::create([
                'filename' => $newFilename,
                'metadata' => "",
                'linkfile' => $path,
                'category' => $request->input('category'),
                'project_type' => $request->input('project_type'),
                'documentname' => "", // Anda perlu menyesuaikan ini
                'author' => $request->input('rule'),
                'comment' => "Tidak ada",
                'count' => $count + 1
            ]);

            $listfilenames[] = $newFilename;
            $listmetadatas[] = $request->input('metadata', "");
            $listlinkfiles[] = $path;
        }

        $userName = auth()->user()->name;
        $userEmail = auth()->user()->email;

        $userInfo = [
            'nama penulis' => $userName,
            'email' => $userEmail,
            'sudahdibaca' => $request->input('review'),
            'hasilreview' => $request->input('hasil_review'),
            'comment' => $request->input('comment'),
            'time' => now(),
            'conditionoffile' => $request->input('conditionoffile'),
        ];

        $userInfoJson = json_encode($userInfo);

        $Infounit = [
            'pic' => auth()->user()->rule,
            'level' => "pembukaNewMemo",
            'userinformations' => $userInfoJson,
            'listfilenames' => $listfilenames,
            'listmetadatas' => $listmetadatas,
            'listlinkfiles' => $listlinkfiles,
            'author' => auth()->user()->name,
            'comment' => "",
            'time' => now(),
            'conditionoffile' => $request->input('conditionoffile'),
        ];

        $userinformations[] = json_encode($Infounit);

        $timeline = [
            'documentopened' => now(),
        ];
        $komatlist = [];
        $komattunggal = [
            'komponen' => "KOMPONENUJICOBA",
            'kodematerial' => "12345678",
        ];
        for ($i = 0; $i < 0; $i++) {
            $komatlist[] = json_encode($komattunggal);
        }
        $remaininformation = [
            'komat' => json_encode($komatlist),
        ];
        $timelineJson = json_encode($timeline);
        $remaininformationJson = json_encode($remaininformation);

        $existingDoc = NewMemo::where('documentnumber', $request->input('documentnumber'))
            ->where('project_type', $request->input('project_type'))
            ->exists();
        // Jika ada file dengan nama yang sama dan ID yang berbeda, lemparkan pengecualian
        if ($existingDoc) {
            return response()->json(['Message' => "Sudah pernah diiput"]);
        }

        $document = NewMemo::create([
            'documentname' => $request->input('documentname'),
            'documentnumber' => $request->input('documentnumber'),
            'category' => $request->input('category'),
            'project_type' => $request->input('project_type'),
            'documentstatus' => "Terbuka",
            'memokind' => "",
            'memoorigin' => $request->input('memoorigin'),
            'userinformations' => json_encode($userinformations),
            'timeline' => $timelineJson,
            'remaininformation' => $remaininformationJson,
            'asliordummy' => "asli",
        ]);

        $iddocument = $document->id;

        // Menggunakan ID yang baru dibuat dari NewMemo
        $file = Notification::create([
            'NewMemo_id' => $iddocument,
            'nama_divisi' => "Product Engineering",
            'nama_file' => $newFilename, // Anda perlu menyesuaikan ini
            'nama_project' => $request->input('project_type'),
            'status' => "Terima",
            'alasan' => "",
            'sudahdibaca' => "belum dibaca",
            'notificationcategory' => "memo",
        ]);
        $pesan = 'Memo: ' . $request->input('documentname') . ' telah dibuka.';
        $this->logController->updatelog($iddocument, $pesan, 'Memo berhasil dibuat', auth()->user()->name, 'memo');
        $jenispesan = "text";
        $this->bottelegramController->informasichatbot($pesan, "Product Engineering", $jenispesan);
        $document->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data ditambahkan',
                'datasebelum' => '',
                'datasesudah' => $document,
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomaddition',
        ]);
        return redirect()->route('memo.show', ['id' => $iddocument])->with('success', 'NewMemo berhasil diperbarui.');
    }



    public function downloadFile($id)
    {
        // Cari file berdasarkan ID
        $file = File::findOrFail($id);

        // Lakukan pengecekan apakah file ada
        if ($file) {
            // Ambil path file
            $filePath = storage_path('app/' . $file->linkfile);

            // Pastikan file benar-benar ada di server
            if (file_exists($filePath)) {
                // Set header untuk download file
                return response()->download($filePath, $file->filename);
            } else {
                // Jika file tidak ditemukan, bisa diarahkan ke halaman error atau yang lainnya
                abort(404);
            }
        } else {
            // Jika data file tidak ditemukan, bisa diarahkan ke halaman error atau yang lainnya
            abort(404);
        }
    }


    // Metode pengontrol untuk preview NewMemo
    public function previewDocument($linkfile)
    {
        // Mendapatkan path file di public/uploads
        $filePath = public_path($linkfile);

        // Cek apakah file ada
        if (!file_exists($filePath)) {
            abort(404); // Menangani jika file tidak ditemukan
        }

        // Mendapatkan ekstensi file
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Cek jenis file berdasarkan ekstensi
        switch (strtolower($fileExtension)) { // Menambahkan strtolower untuk memastikan perbandingan tidak sensitif huruf besar/kecil
            case 'pdf':
                // Tampilkan file PDF dalam bentuk preview
                return response()->file($filePath, ['Content-Type' => 'application/pdf']);

            case 'jpg':
            case 'jpeg':
            case 'png':
                // Tampilkan gambar JPEG atau PNG dalam bentuk preview
                return response()->file($filePath, ['Content-Type' => 'image/jpeg']);

            default:
                // Unduh file selain PDF, JPEG, dan PNG
                return response()->download($filePath);
        }
    }


    public function streamdownloadfile()
    {
        $id = (int) request()->id; // Ambil ID dari request
        $file = CollectFile::find($id); // Temukan file berdasarkan ID

        // Periksa apakah file ada
        if (!$file) {
            return response()->json(['message' => 'File tidak ditemukan.'], 404);
        }

        $filePath = public_path('storage/' . $file->link);

        // Periksa apakah file fisik ada
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File tidak ditemukan di server.'], 404);
        }

        // Mendapatkan nama file
        $fileName = basename($filePath);

        return response()->stream(function () use ($filePath) {
            // Baca dan kirim file
            readfile($filePath);
        }, 200, [
            'Content-Type' => 'application/octet-stream', // Atur tipe konten sesuai kebutuhan
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"', // Menyertakan nama file dalam header
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'phonenumber' => 'required|string',
            'file' => 'required|file',  // Validasi file
        ]);

        $uploadedFile = $request->file('file');
        $id = (int) $request->input('id');
        $collectable_id = (int) $request->input('collectable_id');
        $phonenumber = $request->input('phonenumber');

        // Mengganti awalan 62 dengan 0 jika nomor telepon diawali dengan 62
        if (substr($phonenumber, 0, 2) === '62') {
            $phonenumberFirstZero = '0' . substr($phonenumber, 2);
        } else {
            $phonenumberFirstZero = $phonenumber;
        }

        // Cek nomor telepon
        $user = User::where('waphonenumber', $phonenumber)
            ->orWhere('waphonenumber', $phonenumberFirstZero)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        $userName = $user->name;
        $filename = $uploadedFile->getClientOriginalName();
        $fileFormat = $uploadedFile->getClientOriginalExtension();
        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        // Buat nama file baru berdasarkan username dan tanggal
        $newFilename = "{$filenameWithoutExtension}_{$userName}.{$fileFormat}";

        // Cek keberadaan nama file dan tambahkan penomoran jika sudah ada file dengan nama yang sama
        $count = 0;
        while (CollectFile::where('filename', $newFilename)->exists()) {
            $count++;
            $newFilename = "{$filenameWithoutExtension}_{$count}.{$fileFormat}";
        }

        $path = $uploadedFile->storeAs('public/uploads', $newFilename);

        // Memperbarui record jika ID ditemukan dan file masih "filekosong"
        $newmemoFile = CollectFile::find($id);
        if ($newmemoFile && $newmemoFile->filename === "filekosong" && $newmemoFile->collectable_id === $collectable_id) {
            $newmemoFile->filename = $newFilename;  // Set nama file baru
            $newmemoFile->link = str_replace('public/', '', $path);
            $newmemoFile->save();
        } else {
            return response()->json(['message' => 'ID tidak ditemukan / sudah diisi / nomor feedback salah.'], 404);
        }

        return response()->json([
            'message' => 'File dan ID berhasil diterima',
        ], 200);  // Mengembalikan status 200 jika berhasil
    }









    public function searchForm()
    {
        return view('search');
    }
    public function deleteFile($id)
    {
        $file = File::findOrFail($id);
        // Lakukan pengecekan izin atau otorisasi untuk menghapus file
        $file->delete();

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }
    public function deleteFileMultiple(Request $request)
    {
        $fileIds = $request->input('fileIds'); // Ambil ID file yang akan dihapus dari input form
        File::whereIn('id', $fileIds)->delete(); // Hapus file yang dipilih

        return redirect()->back()->with('success', 'File yang dipilih berhasil dihapus');
    }

    public function deleteDocumentMultiple(Request $request)
    {
        $documentIds = $request->input('document_ids'); // Retrieve document IDs from the input form

        if (!empty($documentIds)) {
            // Fetch documents before deletion for logging purposes
            $documents = NewMemo::whereIn('id', $documentIds)->get();

            // Create system logs for the deletion action
            foreach ($documents as $document) {
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data dihapus',
                        'datasebelum' => $document,
                        'datasesudah' => '',
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'document_deletion',
                ]);
            }

            // Delete selected documents
            NewMemo::whereIn('id', $documentIds)->delete();

            // Handle additional cleanup
            foreach ($documentIds as $id) {
                $this->destroyfileandnotifindocument($id);
            }

            return redirect()->back()->with('success', 'NewMemo yang dipilih berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Tidak ada NewMemo yang dipilih untuk dihapus');
        }
    }



    public function reportDocumentMultiple(Request $request)
    {
        $documentIds = $request->input('document_ids');

        if (empty($documentIds)) {
            return response()->json(['error' => 'Tidak ada NewMemo yang dipilih untuk diekspor'], 400);
        }

        // Ambil data NewMemo berdasarkan ID
        $documents = NewMemo::whereIn('id', $documentIds)->get();

        if ($documents->isEmpty()) {
            return response()->json(['error' => 'NewMemo yang dipilih tidak ditemukan'], 404);
        }

        $listdatadocuments = [];
        $countterbuka = 0;
        $counttertutup = 0;

        foreach ($documents as $document) {
            $documentstatus = $document->documentstatus;
            if ($documentstatus == "Terbuka") {
                $countterbuka++;
            } else {
                $counttertutup++;
            }

            [$timeline, $informasiNewMemoencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain] = $this->getAdditionalDataonedocumentdirect($document);

            $listdatadocuments[$document->id] = [
                'document' => json_encode($document),
                'informasiNewMemoencoded' => $informasiNewMemoencoded,
                'datadikirimencoded' => $datadikirimencoded,
                'positionPercentage' => $positionPercentage,
                'unitpicvalidation' => $unitpicvalidation,
                'projectpics' => $projectpics,
                'PEsignature' => $PEsignature,
                'userinformations' => $userinformations,
                'selfunitvalidation' => $selfunitvalidation,
                'PEmanagervalidation' => $PEmanagervalidation,
                'unitvalidation' => $unitvalidation,
                'status' => $status,
                'indonesiatimestamps' => $indonesiatimestamps,
                'level' => $level,
                'MTPRsend' => $MTPRsend,
                'PEshare' => $PEshare,
                'seniormanagervalidation' => $seniormanagervalidation,
                'MTPRvalidation' => $MTPRvalidation,
                'MPEvalidation' => $MPEvalidation,
                'SMname' => $SMname,
                'arrayprojectpicscount' => $arrayprojectpicscount,
                'timeline' => $timeline
            ];
        }

        // Ekspor data ke dalam file Excel
        $export = new DokumensExport($documents, $listdatadocuments);
        $fileName = 'document_report_' . now()->timestamp . '.xlsx'; // Menambahkan timestamp ke nama file

        // Langsung kirim file Excel sebagai response
        return Excel::download($export, $fileName);
    }




    public function showAllMetadata()
    {
        // Ambil semua file dan metadata-nya dari database
        $files = File::all();
        return view('file.all_file', compact('files'));


    }
    public function Documentbaseddocumentopened($documents)
    {
        // Ubah koleksi model Eloquent menjadi array
        $documentsArray = $documents->toArray();

        // Urutkan array berdasarkan documentopened dari paling baru ke paling lama
        usort($documentsArray, function ($a, $b) {
            $dateA = strtotime(json_decode($a['timeline'], true)["documentopened"]);
            $dateB = strtotime(json_decode($b['timeline'], true)["documentopened"]);
            // Urutkan secara terbalik (paling baru ke paling tua)
            return $dateB - $dateA;
        });

        // Pisahkan NewMemo dengan status "Terbuka"
        $terbukaDocuments = [];
        $otherDocuments = [];

        foreach ($documentsArray as $document) {
            if ($document['documentstatus'] === 'Terbuka') {
                $terbukaDocuments[] = $document;
            } else {
                $otherDocuments[] = $document;
            }
        }

        // Gabungkan NewMemo dengan status "Terbuka" di bagian atas
        $documentsArray = array_merge($terbukaDocuments, $otherDocuments);

        // Kembalikan array ke bentuk koleksi model Eloquent
        $documents = NewMemo::hydrate($documentsArray);
        return $documents;
    }


    public function showAllDocument()
    {
        // Ambil semua NewMemo dan urutkan berdasarkan tanggal
        $documents = NewMemo::orderBy('created_at', 'desc')->get();
        $documents = $this->Documentbaseddocumentopened($documents);
        return view('NewMemo.memo.all_document', compact('documents'));
    }

    public function decodeUserInformation($userInfo)
    {
        try {
            $decodedInfo = json_decode($userInfo, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
            }
            return $decodedInfo;
        } catch (\Exception $e) {
            \Log::error('Error decoding JSON: ' . $e->getMessage());
            return [];
        }
    }


    public function destroyfileandnotifindocument($id)
    {
        try {
            // Temukan NewMemo berdasarkan ID
            $NewMemo = NewMemo::findOrFail($id);

            // Hapus tugas divisi yang terkait dengan NewMemo
            Notification::where('NewMemo_id', $id)->delete();
            $userinformations = json_decode($NewMemo->userinformations);
            $deleteddocument = "";
            for ($i = 0; $i < count($userinformations); $i++) {
                if (json_decode($userinformations[$i]) != "") {
                    $filenames = json_decode($userinformations[$i])->listfilenames;
                    $linkfiles = json_decode($userinformations[$i])->listlinkfiles;
                    foreach ($filenames as $index => $filename) {
                        $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                        // Menghapus file dari direktori '\storage\app\uploads'
                        try {
                            Storage::delete('uploads/' . $newLinkFile);
                        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                            // File tidak ditemukan, lanjutkan ke iterasi berikutnya
                            continue;
                        }
                    }

                    if ($filenames) {
                        for ($j = 0; $j < count($filenames); $j++) {
                            $deletedfilename = $filenames[$j];
                            File::where('filename', $deletedfilename)->delete();
                        }
                    }
                }
            }
            // Lakukan penghapusan NewMemo
            $NewMemo->delete();
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, tampilkan pesan error
        }
    }

    public function destroydocument($id)
    {
        $this->destroyfileandnotifindocument($id);
        return redirect()->back();
    }

    public function searchMetadata(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'search_type' => 'required|string', // tambahkan validasi untuk search_type
        ]);

        $query = $request->input('query');
        $search_type = $request->input('search_type');

        // Perbaikan sintaks: gunakan operator perbandingan (==) untuk memeriksa nilai, bukan operator assignment (=)
        if ($search_type === "metadata") {
            $files = File::where($search_type, 'LIKE', "%$query%")->get();
            return view('search_results', [
                'files' => $files,
                'query' => $query,
                'search_type' => $search_type
            ]);
        } else {
            $files = File::where($search_type, 'LIKE', "%$query%")->get();
            return view('project_results', [
                'files' => $files,
                'query' => $query,
                'search_type' => $search_type
            ]);
        }


    }

    public function showMetadata(Request $request, $id)
    {
        $file = File::findOrFail($id);
        return view('file.metadata', compact('file'));
    }









    public function showDocument(Request $request, $id)
    {
        $document = NewMemo::findOrFail($id);
        [$timeline, $informasiNewMemoencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain] = NewMemo::getAdditionalDataonedocumentdirect($document);
        $nama_divisi = auth()->user()->rule;

        // Cari tugas divisi yang pertama ditemukan
        $Notification = Notification::whereRaw('CAST(NewMemo_id AS CHAR) = ?', [$id])
            ->where('nama_divisi', $nama_divisi)
            ->first();

        // Jika tugas divisi ditemukan, update status sudah dibaca
        if ($Notification) {
            $Notification->update([
                'sudahdibaca' => "sudah dibaca",
            ]);
        }
        // 'documentshared' tidak memiliki nilai
        $timeline = json_decode($document->timeline, true);
        if ($nama_divisi == "Product Engineering") {
            if (!isset($timeline[$nama_divisi . '_share' . '_read'])) {
                $timeline[$nama_divisi . '_share' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
            } else {
                if (in_array($nama_divisi, $projectpics) && $PEsignature == "Aktif") {
                    if (!isset($timeline[$nama_divisi . '_unit' . '_read'])) {
                        $timeline[$nama_divisi . '_unit' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
                    } else {
                        if ($unitvalidation == "Aktif") {
                            $timeline[$nama_divisi . '_combine' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
                        }
                    }
                }

            }
        } elseif ($nama_divisi == "MTPR") {
            if (!isset($timeline[$nama_divisi . '_finish' . '_read']) && $seniormanagervalidation == "Aktif") {
                $timeline[$nama_divisi . '_finish' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
            }
        }
        if (strpos($nama_divisi, 'Senior Manager') !== false) {
            if ($PEmanagervalidation == "Aktif") {
                $timeline[$nama_divisi . '_seniorvalid' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
            }
        } elseif ($nama_divisi == "Manager Product Engineering") {
            if ($MPEvalidation != "Aktif" && $SMname = "Senior Manager Engineering" && $PEmanagervalidation == "Aktif") {
                if (!isset($timeline[$nama_divisi . '_unit' . '_read'])) {
                    $timeline[$nama_divisi . '_unit' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
                }
            }
        } else {
            if (!isset($timeline[$nama_divisi . '_unit' . '_read'])) {
                $timeline[$nama_divisi . '_unit' . '_read'] = ['status' => "Sudah dibaca", 'waktu' => now()];
            }
        }

        $document->update([
            'timeline' => json_encode($timeline),
        ]);
        [$timeline, $informasiNewMemoencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain] = NewMemo::getAdditionalDataonedocumentdirect($document);
        $jenisdata = "memo";
        $logs = AppLog::all()->filter(function ($log) use ($document, $jenisdata) {
            return json_decode($log->message)->id == $document->id && $log->jenisdata == $jenisdata;
        });
        return view('NewMemo.memo.memo', compact(
            'timeline',
            'logs',
            'document',
            'positionPercentage',
            'unitpicvalidation',
            'projectpics',
            'PEsignature',
            'userinformations',
            'selfunitvalidation',
            'PEmanagervalidation',
            'unitvalidation',
            'status',
            'indonesiatimestamps',
            'level',
            'MTPRsend',
            'PEshare',
            'seniormanagervalidation',
            'MTPRvalidation',
            'MPEvalidation',
            'SMname',
            'arrayprojectpicscount'
        ));
    }





    private function getAdditionalonedocumentkhususpdf($idNewMemo)
    {
        $documents = NewMemo::where('id', $idNewMemo)->get();
        $listdatadocuments = [];
        $countterbuka = 0;
        $counttertutup = 0;
        for ($i = 0; $i < count($documents); $i++) {
            $documentstatus = $documents[$i]->documentstatus;
            if ($documentstatus == "Terbuka") {
                $countterbuka++;
            } else {
                $counttertutup++;
            }
            $document = $documents[$i];
            [$timeline, $informasiNewMemoencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain] = NewMemo::getAdditionalDataonedocumentdirect($document);
            $Infounit = [
                'document' => json_encode($document),
                'informasiNewMemoencoded' => $informasiNewMemoencoded,
                'datadikirimencoded' => $datadikirimencoded,
                'positionPercentage' => $positionPercentage,
                'unitpicvalidation' => $unitpicvalidation,
                'projectpics' => $projectpics,
                'PEsignature' => $PEsignature,
                'userinformations' => $userinformations,
                'selfunitvalidation' => $selfunitvalidation,
                'PEmanagervalidation' => $PEmanagervalidation,
                'unitvalidation' => $unitvalidation,
                'status' => $status,
                'indonesiatimestamps' => $indonesiatimestamps,
                'level' => $level,
                'MTPRsend' => $MTPRsend,
                'PEshare' => $PEshare,
                'seniormanagervalidation' => $seniormanagervalidation,
                'MTPRvalidation' => $MTPRvalidation,
                'MPEvalidation' => $MPEvalidation,
                'SMname' => $SMname,
                'arrayprojectpicscount' => $arrayprojectpicscount
            ];
            $Infounitencode = json_encode($Infounit);
            $listdatadocuments[$document->id] = $Infounitencode;
        }
        $percentagememoterbuka = ($countterbuka / ($countterbuka + $counttertutup)) * 100;
        $percentagememotertutup = ($counttertutup / ($countterbuka + $counttertutup)) * 100;
        $listdatadocumentencode = json_encode($listdatadocuments);
        return [$listdatadocumentencode, $percentagememoterbuka, $percentagememotertutup];
    }

    public function singkatanUnit($namaUnit)
    {
        $singkatan = "";
        $kata = explode(" ", $namaUnit);
        foreach ($kata as $k) {
            $singkatan .= substr($k, 0, 1);
        }
        return $singkatan;
    }

    public function mappingAllDocument()
    {
        $documents = NewMemo::all();
        [$listdatadocumentencode, $percentagememoterbuka, $percentagememotertutup] = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Memperbarui NewMemo berdasarkan status terbuka atau tertutup
        $documents = $this->Documentbaseddocumentopened($documents);

        // Ambil kategori unit under pe
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Hapus tanda kutip ganda tambahan
        $allunitunderpe = json_decode($categoryproject, true);

        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->singkatanUnit($unit);
        }
        $unitsingkatan["Senior Manager Engineering"] = "SME";
        $unitsingkatan["Senior Manager Desain"] = "SMD";
        $unitsingkatan["Senior Manager Teknologi Produksi"] = "SMTP";
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        $categoryprojectbaru = json_decode($categoryproject, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
        $listproject = json_decode($categoryproject, true);
        $revisiall = $this->tiapproject($documents, $listproject, $percentagememoterbuka, $percentagememotertutup);
        unset($revisiall['All']);
        // Kembalikan tampilan dengan NewMemo, unit under pe, singkatan unit, dan data tambahan
        return view('NewMemo.memo.mapping', compact('revisiall', 'documents', 'allunitunderpe', 'unitsingkatan', 'listdatadocumentencode', 'percentagememoterbuka', 'percentagememotertutup'));
    }

    public function tiapproject($documents, $listproject, $percentagememoterbuka, $percentagememotertutup)
    {
        $revisiall = [];
        $revisiall["All"]['documents'] = $documents;
        $revisiall["All"]['persentase'] = [
            'terbuka' => $percentagememoterbuka,
            'tertutup' => $percentagememotertutup
        ];
        $documentTerbuka = collect($documents)->filter(function ($doc) {
            return strtolower($doc['documentstatus']) == 'terbuka' && !is_null($doc['project_type']) && $doc['project_type'] !== '';
        })->count();

        $documentTertutup = collect($documents)->filter(function ($doc) {
            $status = strtolower($doc['documentstatus']);
            return ($status == 'tertutup' || $status == '') && !is_null($doc['project_type']) && $doc['project_type'] !== '';
        })->count();


        $revisiall["All"]['jumlah'] = [
            'terbuka' => $documentTerbuka,
            'tertutup' => $documentTertutup
        ];
        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $filteredDocuments = collect($documents)->where('project_type', $listproject[$i])->all();
            // Simpan NewMemo yang telah difilter ke dalam revisiall
            $revisiall[$key]['documents'] = $filteredDocuments;

            // Hitung jumlah NewMemo terbuka dan tertutup
            $documentTerbuka = count(collect($filteredDocuments)->where('documentstatus', "Terbuka")->all());
            $documentTertutup = count(collect($filteredDocuments)->where('documentstatus', "Tertutup")->all());

            // Hitung persentase NewMemo terbuka
            $totalDocuments = count($filteredDocuments);
            $persentaseTerbuka = ($totalDocuments > 0) ? ($documentTerbuka / $totalDocuments) * 100 : 0;

            // Hitung persentase NewMemo tertutup
            $persentaseTertutup = ($totalDocuments > 0) ? ($documentTertutup / $totalDocuments) * 100 : 0;

            // Simpan persentase ke dalam revisiall
            $revisiall[$key]['jumlah'] = [
                'terbuka' => $documentTerbuka,
                'tertutup' => $documentTertutup
            ];
            $revisiall[$key]['persentase'] = [
                'terbuka' => $persentaseTerbuka,
                'tertutup' => $persentaseTertutup
            ];
        }
        return $revisiall;
    }



    public function ReportDocument(Request $request, $id)
    {
        $document = NewMemo::findOrFail($id);
        $nama_divisi = auth()->user()->rule;

        // Cari tugas divisi yang pertama ditemukan
        $file = Notification::whereRaw('CAST(NewMemo_id AS CHAR) = ?', [$id])
            ->where('nama_divisi', $nama_divisi)
            ->first();

        // Jika tugas divisi ditemukan, update status sudah dibaca
        if ($file) {
            $file->update([
                'sudahdibaca' => "sudah dibaca",
            ]);
        }

        return view('NewMemo.memo.documenttimeline', compact('document'));
    }

    public function metadataedit($id)
    {
        $file = File::findOrFail($id);
        return view('file.editmetadata', compact('file'));
    }

    public function memoedit($id)
    {
        $document = NewMemo::findOrFail($id);
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        return view('NewMemo.memo.editNewMemo', compact('document', 'category', 'categoryproject'));
    }
    public function documentfeedback($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('NewMemo.memo.uploadfeedback', compact('document'));
    }

    public function documentmanagerfeedback($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('NewMemo.memo.uploadmanagerfeedback', compact('document'));
    }
    public function documentcombine($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('NewMemo.memo.uploadcombine', compact('document'));
    }
    public function documentsignature($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('NewMemo.memo.uploadsignature', compact('document'));
    }

    public function uploadsignaturefeedbackmerge(Request $request, $id)
    {
        try {
            // Temukan NewMemo yang akan diperbarui
            $document = NewMemo::findOrFail($id);
            $userinformations = json_decode($document->userinformations, true) ?? [];

            // Upload file baru jika ada
            $listfilenames = [];
            $listmetadatas = [];
            $listlinkfiles = [];
            $existingFiles = File::pluck('filename')->toArray();
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $index => $uploadedFile) {
                    // Ambil nama pengguna dari objek autentikasi
                    $userName = auth()->user()->name;
                    // Dapatkan nama file yang diunggah
                    $filename = $uploadedFile->getClientOriginalName();
                    // Dapatkan ekstensi file
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    // Hapus ekstensi file dari nama file
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                    // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                    $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                    // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file
                    $filename = $filenameWithUserAndFormat;
                    // Periksa apakah nama file sudah ada
                    $count = 0;
                    $newFilename = $filename;
                    while (File::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    }
                    $path = $uploadedFile->storeAs('uploads', $newFilename);

                    // Simpan informasi file baru ke dalam database
                    File::create([
                        'filename' => $newFilename,
                        'metadata' => $request->input('metadata', ""),
                        'linkfile' => $path,
                        'category' => $document->category,
                        'project_type' => $document->project_type,
                        'documentname' => $document->documentname,
                        'author' => $request->input('rule'),
                        'comment' => $request->input('comment'),
                        'count' => $count + 1
                    ]);

                    $listfilenames[] = $newFilename;
                    $listmetadatas[] = $request->input('metadata', "");
                    $listlinkfiles[] = $path;
                }
            }

            // Update informasi pengguna
            $userName = auth()->user()->name;
            $userEmail = auth()->user()->email;
            $userInfo = [
                'nama penulis' => $userName,
                'email' => $userEmail,
                'sudahdibaca' => $request->input('review'),
                'hasilreview' => $request->input('hasil_review'),
                'comment' => $request->input('comment'),
                'time' => now(),
                'conditionoffile' => $request->input('conditionoffile'),
                'conditionoffile2' => $request->input('conditionoffile2'),
            ];

            // Tambahkan informasi baru ke dalam array
            $Infounit = [
                'pic' => $request->input('picrule'),
                'level' => $request->input('level'),
                'userinformations' => json_encode($userInfo),
                'listfilenames' => $listfilenames,
                'listmetadatas' => $listmetadatas,
                'listlinkfiles' => $listlinkfiles,
                'author' => $request->input('author'),
                'comment' => $request->input('comment'),
                'time' => now(),
                'conditionoffile' => $request->input('conditionoffile'),
            ];
            $userinformations[] = json_encode($Infounit);

            // Perbarui NewMemo
            $document->update([
                'userinformations' => json_encode($userinformations),
                'feedbacktimestamp' => now(),
            ]);

            if ($request->input('conditionoffile2') == "signature") {
                $pesan = 'Memo: ' . $document->id . ' berhasil diupdate NewMemo dengan bertanda tangan.';
                $pesansingkat = 'Memo berhasil diupdate';
                $this->logController->updatelog($document->id, $pesan, $pesansingkat, auth()->user()->name, 'memo');
            } elseif ($request->input('conditionoffile2') == "combine") {
                $pesan = 'Memo: ' . $document->id . ' menerima finalisasi feedback oleh PE.';
                $pesansingkat = 'Finalisasi feedback diupload';
                $this->logController->updatelog($document->id, $pesan, $pesansingkat, auth()->user()->name, 'memo');
            } elseif ($request->input('conditionoffile2') == "feedback") {
                if (strpos(auth()->user()->rule, "Manager") == true) {
                    $pesan = 'Memo: ' . $document->id . ' menerima feedback oleh Manager.';
                } else {
                    $pesan = 'Memo: ' . $document->id . ' menerima feedback oleh staff.';
                }
                $pesansingkat = 'Memo berhasil menerima feedback';
                $this->logController->updatelog($document->id, $pesan, $pesansingkat, auth()->user()->name, 'memo');

            }

            return redirect()->route('memo.show', ['id' => $document->id])->with('success', 'NewMemo berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui NewMemo: ' . $e->getMessage());
        }

    }


    public function sendfowardDocument(Request $request, $id)
    {   // Mengubah status conditionoffile
        $posisi = $request->input('posisi');
        $iddocument = $request->input('iddocument');
        $decision = $request->input('decision');
        $document = NewMemo::findOrFail($iddocument);
        $userinformations = json_decode($document->userinformations, true) ?? [];
        for ($i = 0; $i < count($userinformations); $i++) {
            if (json_decode($userinformations[$i]) != "") {
                $sumberinformasi = json_decode($userinformations[$i])->userinformations;
                if ($sumberinformasi) {
                    try {
                        $userInfo = json_decode($sumberinformasi, true);

                        // Check for JSON decoding errors
                        if (json_last_error() != JSON_ERROR_NONE) {
                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                        }
                    } catch (\Exception $e) {
                        // Log the error message or throw an exception
                        \Log::error('Error decoding JSON: ' . $e->getMessage());

                        // Handle decoding error, for example:
                        $userInfo = [];
                    }
                    foreach ($userInfo as $key => $value) {
                        if ($key == 'time' && $value == $posisi) {
                            $userinformationsdecoded = json_decode($userinformations[$i], true);
                            $userInfo['conditionoffile'] = $decision;
                            $userInfoDecoded = json_encode($userInfo);
                            $userinformationsdecoded['userinformations'] = $userInfoDecoded;
                            $userinformations[$i] = json_encode($userinformationsdecoded);
                            $document->update([
                                'userinformations' => json_encode($userinformations)
                            ]);
                        }
                    }
                }
            }

        }

        $document = NewMemo::findOrFail($id);
        $timeline = json_decode($document->timeline, true);
        $userInformations = $userinformations ?? [];

        // Memeriksa apakah JSON berhasil di-decode
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->route('memo.show', ['id' => 1000])->with('success', 'NewMemo berhasil diperbarui.');
        }
        $rawUserInformation = $request->input("sumberinformasi");
        $temporaryUserInformation = json_decode($rawUserInformation, true);
        $namadivisi = $request->input("picunit");
        $level = $request->input("level");
        // Mengonversi userinformations menjadi array asosiatif
        $sumberinformasiArray = json_decode($temporaryUserInformation['userinformations'], true);
        // Menetapkan waktu saat ini
        $sumberinformasiArray['time'] = now();
        $sumberinformasiArray['comment'] = "";
        $sumberinformasiArray['nama penulis'] = auth()->user()->name;
        $sumberinformasiArray['email'] = auth()->user()->email;
        // Menetapkan nilai yang diperbarui ke dalam userinformations
        $temporaryUserInformation['pic'] = $namadivisi;
        $temporaryUserInformation['level'] = $level;
        $temporaryUserInformation['userinformations'] = json_encode($sumberinformasiArray);
        $finalvalue = json_encode($temporaryUserInformation);
        $userInformations[] = $finalvalue;
        $Status = "";
        if ($namadivisi === "Product Engineering" || $namadivisi === "MTPR" || $level === "MTPR") {
            $Status = "Terima";
        }
        $document->update([
            'documentname' => $document->documentname,
            'userinformations' => json_encode($userInformations),
            'feedbacktimestamp' => now(),
        ]);
        Notification::create([
            'NewMemo_id' => $request->input("iddocument"),
            'nama_file' => $request->input("documentname"),
            'nama_project' => $request->input("project_type"),
            'nama_divisi' => $level,
            'status' => $Status,
            'alasan' => "",
            'sudahdibaca' => "belum dibaca",
            'notificationcategory' => "memo",
        ]);
        if ($request->input("level") == "selesai") {
            if (isset($timeline['documentclosed'])) {
                // 'documentshared' memiliki nilai
                $happen = "hi";
            } else {
                // 'documentshared' tidak memiliki nilai
                $timeline['documentclosed'] = now();
            }
            $document->update([
                'documentstatus' => "Tertutup",
                'timeline' => json_encode($timeline),
            ]);
            $Status = "Terima";
            $pesan = "Memo " . $request->input("documentname") . " telah ditutup.";
            $this->logController->updatelog($iddocument, $pesan, 'Memo ditutup', auth()->user()->name, 'memo');
            $jenispesan = "text";
            $this->bottelegramController->informasichatbot($pesan, $level, $jenispesan);
            return redirect()->route('memo.show', ['id' => $document->id])->with('success', 'NewMemo berhasil diperbarui.');
        } else {
            $pesan = "Memo " . $request->input("documentname") . " dikirimkan ke " . $level . "untuk menunggu persetujuan.";
            $this->logController->updatelog($iddocument, $pesan, 'Foward Memo', auth()->user()->name, 'memo');
            $jenispesan = "text";
            $this->bottelegramController->informasichatbot($pesan, $level, $jenispesan);
            return redirect()->route('memo.show', ['id' => $document->id])->with('success', 'NewMemo berhasil diperbarui.');
        }
    }

    public function documentdelete(Request $request, $id)
    {
        $document = NewMemo::findOrFail($id);
        $document->delete();
        return redirect()->route('memo.show', ['id' => $document->id])->with('success', 'NewMemo berhasil diperbarui.');
    }

    public function sendDecision(Request $request, $id)
    {
        $posisi = $request->input('posisi');
        $iddocument = $request->input('iddocument');
        $decision = $request->input('decision');
        $document = NewMemo::findOrFail($iddocument);
        $userinformations = json_decode($document->userinformations, true) ?? [];
        $idfeedbackberdasarwaktu = "";
        for ($i = 0; $i < count($userinformations); $i++) {
            if (json_decode($userinformations[$i]) != "") {
                $sumberinformasi = json_decode($userinformations[$i])->userinformations;
                if ($sumberinformasi) {
                    try {
                        $userInfo = json_decode($sumberinformasi, true);

                        // Check for JSON decoding errors
                        if (json_last_error() != JSON_ERROR_NONE) {
                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                        }
                    } catch (\Exception $e) {
                        // Log the error message or throw an exception
                        \Log::error('Error decoding JSON: ' . $e->getMessage());

                        // Handle decoding error, for example:
                        $userInfo = [];
                    }
                    foreach ($userInfo as $key => $value) {
                        if ($key == 'time' && $value == $posisi) {
                            $userinformationsdecoded = json_decode($userinformations[$i], true);
                            $userInfo['conditionoffile'] = $decision;
                            $idfeedbackberdasarwaktu = $userInfo['time'];
                            $userInfoDecoded = json_encode($userInfo);
                            $userinformationsdecoded['userinformations'] = $userInfoDecoded;
                            $userinformations[$i] = json_encode($userinformationsdecoded);
                            $document->update([
                                'userinformations' => json_encode($userinformations)
                            ]);
                            $pesan = 'Feedback: ' . $idfeedbackberdasarwaktu . ' berubah status menjadi ' . $decision;
                            $this->logController->updatelog($document->id, $pesan, 'Feedback berubah status', auth()->user()->name, 'memo');
                            return redirect()->back()->with('success', 'NewMemo berhasil dihapus');
                            // return view('showinformation.info', ['message' => $userinformations[$i]]);
                        }
                    }
                }
            }

        }

        // return view('showinformation.info', ['message' => 'tidak ada data untuk unit ini']);
    }




    public function deletedFeedbackDecision(Request $request, $id, $sendtime)
    {
        $posisi = $sendtime;
        $document = NewMemo::findOrFail($id);
        $dasaran = json_decode($document->userinformations, true) ?? [];
        $yangdicari = -100; // Inisialisasi indeks yang dicari
        // Mencari indeks yang sesuai dengan posisi yang diberikan
        foreach ($dasaran as $index => $item) {
            $bagian1 = json_decode($item, true) ?? [];
            $sumberinformasi = $bagian1['userinformations'] ?? null;
            if ($sumberinformasi) {
                $nilaivalue = json_decode($sumberinformasi, true)['time'];
                if ($nilaivalue == $posisi) {
                    $yangdicari = $index;
                    break;
                }
            }
        }

        // Jika indeks yang sesuai ditemukan, hapus item tersebut
        if ($yangdicari !== -100) {
            $itemToDelete = $dasaran[$yangdicari];
            $filenames = $itemToDelete['listfilenames'] ?? [];
            $linkfiles = $itemToDelete['listlinkfiles'] ?? [];

            // Hapus file-file yang terkait dengan item yang akan dihapus
            foreach ($filenames as $index => $filename) {
                $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                try {
                    Storage::delete('uploads/' . $newLinkFile);
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    // File tidak ditemukan, lanjutkan ke iterasi berikutnya
                    continue;
                }
            }

            // Hapus item dari array
            unset($dasaran[$yangdicari]);

            // Perbarui data NewMemo
            $document->update([
                'userinformations' => json_encode(array_values($dasaran)) // Menggunakan array_values untuk mengatur ulang indeks array
            ]);
            return redirect()->route('memo.show', ['id' => $document->id]);
        }
    }


    public function unsendDecision(Request $request, $id, $sendtime)
    {
        $posisi = $sendtime;
        $document = NewMemo::findOrFail($id);
        $dasaran = json_decode($document->userinformations, true) ?? [];
        $yangdicari = []; // Inisialisasi indeks yang dicari
        // Mencari indeks yang sesuai dengan posisi yang diberikan
        foreach ($dasaran as $index => $item) {
            $bagian1 = json_decode($item, true) ?? [];
            $sumberinformasi = $bagian1['userinformations'] ?? null;
            if ($sumberinformasi) {
                $nilaivalue = json_decode($sumberinformasi, true)['time'];
                if ($nilaivalue >= $posisi) {
                    $yangdicari[] = $index;
                }
            }
        }

        // Jika indeks yang sesuai ditemukan, hapus item tersebut
        if (isset($yangdicari)) {

            foreach ($yangdicari as $yangdicarisatuan) {
                $itemToDelete = $dasaran[$yangdicarisatuan];
                $filenames = $itemToDelete['listfilenames'] ?? [];
                $linkfiles = $itemToDelete['listlinkfiles'] ?? [];

                // Hapus file-file yang terkait dengan item yang akan dihapus
                foreach ($filenames as $index => $filename) {
                    $newLinkFile = str_replace('uploads/', '', $linkfiles[$index]);
                    try {
                        Storage::delete('uploads/' . $newLinkFile);
                    } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                        // File tidak ditemukan, lanjutkan ke iterasi berikutnya
                        continue;
                    }
                }

                // Hapus item dari array
                unset($dasaran[$yangdicarisatuan]);
            }
            $timeline = json_decode($document->timeline, true);
            foreach ($timeline as $key => $timelinetunggal) {
                if (strpos($key, 'Senior Manager') !== false) {
                    unset($timeline[$key]);
                }
            }
            // Perbarui data NewMemo
            $document->update([
                'userinformations' => json_encode(array_values($dasaran)), // Menggunakan array_values untuk mengatur ulang indeks array
                'timeline' => json_encode($timeline),
                'documentstatus' => "Terbuka",
            ]);
            return redirect()->route('memo.show', ['id' => $document->id]);
        }
    }




    public function awalan()
    {
        return view('layouts.awalan');
    }

    public function main2()
    {
        return view('layouts.main2');
    }

    public function ujicobapdf()
    {
        $data = [];
        $pdf = Pdf::loadView('kebutuhan_masa_depan.modal_pdf_viewer', $data);
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice.pdf"');
    }



    public function mappingpersonalDocument($idNewMemo)
    {
        $documents = NewMemo::all();
        $document = NewMemo::findOrFail($idNewMemo);
        [$listdatadocumentencode, $percentagememoterbuka, $percentagememotertutup] = NewMemo::getAdditionalDataalldocumentdirect($documents);
        $data = [];
        $data['listdatadocumentencode'] = $listdatadocumentencode;
        $data['document'] = $document;
        $pdf = Pdf::loadView('NewMemo.memo.pdf', $data);
        return $pdf->stream('invoice.pdf');
    }

    public function mappingpersonalDocumentdownload($idNewMemo)
    {
        // Retrieve all documents and find the specific document by its ID
        $documents = NewMemo::all();
        $document = NewMemo::findOrFail($idNewMemo);

        // Get additional data for all documents
        [$listdatadocumentencode, $percentagememoterbuka, $percentagememotertutup] = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Prepare data for the PDF view
        $data = [];
        $data['listdatadocumentencode'] = $listdatadocumentencode;
        $data['document'] = $document;

        // Generate PDF and save it to a file
        $pdf = Pdf::loadView('NewMemo.memo.pdf', $data);
        $pdfFilePath = storage_path('app/invoice.pdf');
        $pdf->save($pdfFilePath);

        // Create InputFile object from the saved PDF file
        $inputFile = InputFile::create($pdfFilePath, 'invoice.pdf');

        // Send document using Telegram
        Telegram::sendDocument([
            'chat_id' => -4198400936,
            'document' => $inputFile,
        ]);

        // Return the PDF download response to the user
        return response()->download($pdfFilePath, 'invoice.pdf')->deleteFileAfterSend(true);
    }


    public function updateStatus(Request $request, $id)
    {
        // Temukan NewMemo berdasarkan ID
        $document = NewMemo::findOrFail($id);

        // Ubah status NewMemo
        $newStatus = $document->documentstatus == 'Terbuka' ? 'Tertutup' : 'Terbuka';
        $document->documentstatus = $newStatus;
        $document->save();

        // Mengembalikan status baru sebagai respons AJAX
        return response()->json(['new_status' => $newStatus]);
    }

    public function updatekomat(Request $request, $id, $index)
    {

        try {

            $material = $request->input('material');
            $kodematerial = $request->input('kodematerial');
            $supplier = $request->input('supplier');

            $document = NewMemo::findOrFail($id);
            $komats = json_decode(json_decode($document->remaininformation)->komat);
            foreach ($komats as $halo => $komat) {
                if ($halo == $index) {
                    $arraypribadi = [];
                    $arraypribadi['komponen'] = $material;
                    $arraypribadi['kodematerial'] = $kodematerial;
                    $arraypribadi['supplier'] = $supplier;
                    $komats[$index] = json_encode($arraypribadi);
                }

            }
            $arrayremain = json_decode($document->remaininformation, true);
            $arrayremain['komat'] = json_encode($komats);
            $document->remaininformation = json_encode($arrayremain);
            $document->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update data: ' . $e->getMessage()], 500);
        }
    }

    public function deletekomat(Request $request, $id, $index)
    {
        try {
            $document = NewMemo::findOrFail($id);
            $komats = json_decode(json_decode($document->remaininformation)->komat);
            foreach ($komats as $halo => $komat) {
                unset($komats[$index]);
            }
            $arrayremain = json_decode($document->remaininformation, true);
            $arrayremain['komat'] = json_encode($komats);
            $document->remaininformation = json_encode($arrayremain);
            $document->save();
            return back()->with('success', 'Status NewMemo berhasil diperbarui.');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update data: ' . $e->getMessage()], 500);
        }
    }



}