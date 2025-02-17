<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\JustiMemo;
use App\Models\Category;
use App\Models\JustiMemoFeedback;
use App\Models\CollectFile;
use App\Models\ProjectType;
use Carbon\Carbon;
use App\Models\Wagroupnumber;
use App\Models\TelegramMessage;
use App\Models\JustiMemoKomat;
use Illuminate\Http\Request;
use App\Models\JustiMemoTimeline;
use Illuminate\Routing\Controller;
use App\Http\Controllers\LogController;

class JustiMemoController extends Controller
{


    public function uploadForm()
    {
        $units = Unit::all();
        foreach ($units as $key => $unit) {
            // Memeriksa apakah nama unit mengandung kata "Manager"
            if (!str_contains($unit->name, 'Senior Manager')) {
                // Menghapus unit dari koleksi
                unset($units[$key]);
            }
        }
        $listproject = ProjectType::all();
        $allunitunderpe = Category::getlistCategoryMemberByName('unitunderpe');
        return view('justimemo.uploadMTPR', [
            'informasi' => "",
            'filelinkId' => "",
            'listproject' => $listproject,
            'allunitunderpe' => $allunitunderpe,
            'units' => $units,
        ]);
    }

    public function uploadDocMTPR(Request $request)
    {

        $userName = auth()->user()->name;

        $existingDoc = JustiMemo::where('documentnumber', $request->input('documentnumber'))
            ->where('proyek_type_id', $request->input('proyek_type_id'))
            ->exists();

        // Jika ada file dengan nama yang sama dan ID yang berbeda, lemparkan pengecualian
        if ($existingDoc) {
            return response()->json(['Message' => "Sudah pernah diiput"]);
        }

        // Membuat NewMemo baru
        $document = JustiMemo::create([
            'documentname' => $request->input('documentname'),
            'documentnumber' => $request->input('documentnumber'),
            'proyek_type_id' => $request->input('proyek_type_id'),
            'documentstatus' => "Terbuka",
            'project_pic_id' => json_encode($request->input('project_pic')),
        ]);


        // Membuat NewMemoTimeline baru
        JustiMemoTimeline::create([
            'justi_memo_id' => $document->id,
            'infostatus' => 'documentopened',
            'entertime' => now(), // Set current datetime or valid default value
        ]);

        // Membuat NewMemoFeedback baru
        $feedback = JustiMemoFeedback::create([
            'justi_memo_id' => $document->id,
            'pic' => auth()->user()->id,
            'sudahdibaca' => $request->input('review'),
            'hasilreview' => $request->input('hasil_review'),
            'level' => "pembukadokumen",
            'comment' => "",
            'conditionoffile' => $request->input('conditionoffile'),
            'conditionoffile2' => $request->input('conditionoffile2'),
        ]);

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
            while (CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            }

            // Jika nama file sudah ada, lanjutkan dengan menyimpan file
            $path = $uploadedFile->storeAs('uploads', $newFilename);

            // Simpan file terkait
            $hazardLogFile = new CollectFile();
            $hazardLogFile->filename = $newFilename;
            $hazardLogFile->link = $path;
            $hazardLogFile->collectable_id = $feedback->id; // Menghubungkan file dengan feedback
            $hazardLogFile->collectable_type = JustiMemoFeedback::class; // Tipe polimorfik
            $hazardLogFile->save();
        }

        $unit = Unit::where('name', "Product Engineering")->first();
        $document->notifsystem()->create([
            'status' => 'unread',
            'idunit' => $unit->id,
            'infostatus' => 'User dont read this message',
            'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
        ]);

        $pesan = 'Memo Justifikasi: ' . $request->input('documentname') . ' telah dibuka.';
        $jenispesan = "text";




        // WA SEND
        Wagroupnumber::ujisendunit("MTPR", $pesan);

        $document->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Memo berhasil dibuat',
                'datasebelum' => '',
                'datasesudah' => $document,
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'documentaddition',
        ]);

        return redirect()->route('justi-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function showDocument(Request $request, $id)
    {
        // Mencari dokumen dengan eager loading untuk relasi yang diperlukan
        $document = JustiMemo::with(['feedbacks.files', 'timelines'])->findOrFail($id);
        // Ambil array dari JSON `project_pic_id`
        $unitIds = json_decode($document->project_pic_id, true);

        // Cari unit berdasarkan ID dan ambil nama unit
        $listunit = Unit::whereIn('id', $unitIds)->pluck('name')->toArray();

        // Simpan kembali nama-nama unit ke dalam JSON
        $document->project_pic = json_encode($listunit);

        $projectType = ProjectType::findOrFail($document->proyek_type_id);
        $projectname = $projectType->title;
        // Mendapatkan detail dari dokumen
        list(
            $MTPRsend,
            $operatorsignature,
            $operatorshare,
            $unitpicvalidation,
            $unitvalidation,
            $operatorcombinevalidation,
            $selfunitvalidation,
            $seniormanagervalidation,
            $MTPRvalidation,
            $manageroperatorvalidation,
            $posisi1,
            $posisi2,
            $posisi3,
            $positionPercentage,
            $SMname
        ) = $document->detailonedocument();

        // Decode JSON from project_pic column, ensure it's an array
        $projectpics = json_decode($document->project_pic, true);
        if (!is_array($projectpics)) {
            $projectpics = [];
        }

        $nama_divisi = auth()->user()->rule;
        $timelines = collect($document->timelines); // Menggunakan collect untuk $timelines

        // Logika untuk mengelola timeline berdasarkan divisi
        if ($nama_divisi == $document->operator) {
            $nama_divisi_share_read = $timelines->firstWhere('infostatus', $nama_divisi . '_share_read');
            if (!$nama_divisi_share_read) {
                JustiMemoTimeline::create([
                    'justi_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_share_read',
                    'entertime' => now(),
                ]);
            } else {
                if (in_array($nama_divisi, $projectpics) && $document->operatorsignature == "Aktif") {
                    $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
                    if (!$nama_divisi_unit_read) {
                        JustiMemoTimeline::create([
                            'justi_memo_id' => $document->id,
                            'infostatus' => $nama_divisi . '_unit_read',
                            'entertime' => now(),
                        ]);
                    } else {
                        if ($unitvalidation == "Aktif") {
                            $nama_divisi_combine_read = $timelines->firstWhere('infostatus', $nama_divisi . '_combine_read');
                            if (!$nama_divisi_combine_read) {
                                JustiMemoTimeline::create([
                                    'justi_memo_id' => $document->id,
                                    'infostatus' => $nama_divisi . '_combine_read',
                                    'entertime' => now(),
                                ]);
                            }
                        }
                    }
                }
            }
        } elseif ($nama_divisi == "MTPR") {
            $nama_divisi_finish_read = $timelines->firstWhere('infostatus', $nama_divisi . '_finish_read');
            if (!$nama_divisi_finish_read && $seniormanagervalidation == "Aktif") {
                JustiMemoTimeline::create([
                    'justi_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_finish_read',
                    'entertime' => now(),
                ]);
            }
        } elseif (strpos($nama_divisi, 'Senior Manager') !== false) {
            $seniorvalid_read = $timelines->firstWhere('infostatus', $nama_divisi . '_seniorvalid_read');
            if (!$seniorvalid_read && $document->operatorcombinevalidation == "Aktif") {
                JustiMemoTimeline::create([
                    'justi_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_seniorvalid_read',
                    'entertime' => now(),
                ]);
            }
        } elseif ($nama_divisi == "Manager Product Engineering") {
            $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
            if (!$nama_divisi_unit_read && $document->manageroperatorvalidation != "Aktif" && $SMname == "Senior Manager Engineering" && $document->operatorcombinevalidation == "Aktif") {
                JustiMemoTimeline::create([
                    'justi_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_unit_read',
                    'entertime' => now(),
                ]);
            }
        } else {
            $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
            if (!$nama_divisi_unit_read) {
                JustiMemoTimeline::create([
                    'justi_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_unit_read',
                    'entertime' => now(),
                ]);
            }
        }

        // Inisialisasi logs atau data tambahan lainnya
        $logs = [];

        // Mengembalikan tampilan dengan data yang diperlukan
        return view('justimemo.memo', compact(
            'document',
            'operatorsignature',
            'selfunitvalidation',
            'unitvalidation',
            'unitpicvalidation',
            'manageroperatorvalidation',
            'logs',
            'operatorcombinevalidation',
            'seniormanagervalidation',
            'MTPRvalidation',
            'SMname',
            'projectname'
        ));
    }

    public function documentfeedback($id)
    {
        $document = JustiMemo::findOrFail($id);
        return view('newmemo.uploadfeedback', compact('document'));
    }


}
