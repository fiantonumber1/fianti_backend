<?php

namespace App\Http\Controllers;
use App\Models\Jobticket;
use App\Models\User;
use App\Models\Unit;
use App\Exports\NewMemoExport;
use Illuminate\Support\Str;
use App\Models\NewMemo;
use App\Models\Notification;
use App\Models\NotificationDaily;
use App\Models\Category;
use App\Models\CollectFile;
use App\Models\ProjectType;
use Carbon\Carbon;
use App\Models\Wagroupnumber;
use Illuminate\Support\Facades\Cache;
use App\Models\NewMemoKomat;
use Illuminate\Http\Request;
use App\Models\NewMemoFeedback;
use App\Models\NewMemoTimeline;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;


class NewMemoController extends Controller
{




    // Method to add feedback to a memo
    public function addFeedback(Request $request, $memoId)
    {
        $request->validate([
            'author' => 'required|string',
            'comment' => 'required|string',
        ]);

        $newMemo = NewMemo::findOrFail($memoId);

        $feedback = new NewMemoFeedback([
            'pic' => $request->pic,
            'author' => $request->author,
            'level' => $request->level,
            'email' => $request->email,
            'comment' => $request->comment,
            'conditionoffile' => $request->conditionoffile,
            'conditionoffile2' => $request->conditionoffile2,
        ]);

        $newMemo->feedbacks()->save($feedback);

        return response()->json($feedback, 201);
    }

    // Method to add komat to a memo
    public function addKomat(Request $request, $memoId)
    {
        $request->validate([
            'kodematerial' => 'required|string',
            'material' => 'required|string',
            'supplier' => 'required|string',
        ]);

        $newMemo = NewMemo::findOrFail($memoId);

        $komat = new NewMemoKomat([
            'kodematerial' => $request->kodematerial,
            'material' => $request->material,
            'supplier' => $request->supplier,
        ]);

        $newMemo->komats()->save($komat);

        return response()->json($komat, 201);
    }

    public function uploadDocMTPR(Request $request)
    {
        // Validasi request
        $request->validate([
            'file.*' => 'required',
            'category' => 'required',
            'proyek_type_id' => 'required',
            'operator' => 'required',
        ]);

        $userName = auth()->user()->name;

        $existingDoc = NewMemo::where('documentnumber', $request->input('documentnumber'))
            ->where('proyek_type_id', $request->input('proyek_type_id'))
            ->exists();

        // Jika ada file dengan nama yang sama dan ID yang berbeda, lemparkan pengecualian
        if ($existingDoc) {
            return response()->json(['Message' => "Sudah pernah diiput"]);
        }

        // Membuat NewMemo baru
        $document = NewMemo::create([
            'documentname' => $request->input('documentname'),
            'documentnumber' => $request->input('documentnumber'),
            'proyek_type_id' => $request->input('proyek_type_id'),
            'category' => $request->input('category'),
            'documentstatus' => "Terbuka",
            'memokind' => "",
            'memoorigin' => $request->input('memoorigin'),
            'asliordummy' => "asli",
            'operator' => $request->input('operator'),
        ]);

        // Membuat NewMemoKomat baru
        // NewMemoKomat::create([
        //     'new_memo_id' => $document->id,
        //     'kodematerial' => '',
        //     'material' => '',
        //     'supplier' => '',
        // ]);

        // Membuat NewMemoTimeline baru
        NewMemoTimeline::create([
            'new_memo_id' => $document->id,
            'infostatus' => 'documentopened',
            'entertime' => now(), // Set current datetime or valid default value
        ]);

        // Membuat NewMemoFeedback baru
        $feedback = NewMemoFeedback::create([
            'new_memo_id' => $document->id,
            'pic' => auth()->user()->rule,
            'author' => auth()->user()->name,
            'sudahdibaca' => $request->input('review'),
            'hasilreview' => $request->input('hasil_review'),
            'level' => "pembukadokumen",
            'email' => $request->email,
            'comment' => "",
            'conditionoffile' => $request->input('conditionoffile'),
            'conditionoffile2' => $request->input('conditionoffile2'),
        ]);

        // Ambil nama pengguna dari objek autentikasi
        $userName = auth()->user()->name;
        $listcollectable_id = [];
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

            // Simpan file di storage/app/public/uploads
            $path = $uploadedFile->storeAs('public/uploads', $newFilename);

            // Simpan file terkait di database
            $newmemoFile = new CollectFile();
            $newmemoFile->filename = $newFilename;
            $newmemoFile->link = str_replace('public/', '', $path);
            $newmemoFile->collectable_id = $feedback->id; // Menghubungkan file dengan feedback
            $newmemoFile->collectable_type = NewMemoFeedback::class; // Tipe polimorfik
            $newmemoFile->save();
            $listcollectable_id[] = $feedback->id;
        }


        $unit = Unit::where('name', $document->operator)->first();
        $document->notifsystem()->create([
            'status' => 'unread',
            'idunit' => $unit->id,
            'infostatus' => 'User dont read this message',
            'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
        ]);

        // Menyusun daftar file untuk di-download
        $files = CollectFile::whereIn('collectable_id', $listcollectable_id)->get(); // Menambahkan get()
        $list = '';
        foreach ($files as $file) {
            $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
        }


        $pesan = 'Memo: ' . $request->input('documentname') . ' telah dibuka dan ditunjukan ke unit :' . $document->operator . "\n\n" .
            "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
            $list .
            "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";


        //dipensiunkan telegram
        // $jenispesan = "text";
        // $teleunit = TelegramMessagesAccount::where('account', $document->operator)->first();
        // TelegramMessage::create([
        //     'message_kind' => $jenispesan,
        //     'message' => $pesan,
        //     'telegram_messages_accounts_id' => $teleunit->id,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // wa message
        Wagroupnumber::ujisendunit($document->operator, $pesan);


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

        return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function showDocument(Request $request, $id)
    {
        $statussetujulist = [];
        // Mencari dokumen dengan eager loading untuk relasi yang diperlukan
        $document = NewMemo::with(['feedbacks.files', 'komats', 'timelines'])->findOrFail($id);
        $projectType = ProjectType::findOrFail($document->proyek_type_id);
        $projectname = $projectType->title;
        // Mendapatkan detail dari dokumen
        $document = $document->detailonedocument();
        // Decode JSON from project_pic column, ensure it's an array
        $projectpics = json_decode($document->project_pic, true);
        if (!is_array($projectpics)) {
            $projectpics = [];
        }

        $nama_divisi = auth()->user()->rule;
        $yourrule = $nama_divisi;
        $timelines = collect($document->timelines); // Menggunakan collect untuk $timelines

        // Logika untuk mengelola timeline berdasarkan divisi
        if ($nama_divisi == $document->operator) {
            $nama_divisi_share_read = $timelines->firstWhere('infostatus', $nama_divisi . '_share_read');
            if (!$nama_divisi_share_read) {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_share_read',
                    'entertime' => now(),
                ]);
            } else {
                if (in_array($nama_divisi, $projectpics) && $document->operatorsignature == "Aktif") {
                    $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
                    if (!$nama_divisi_unit_read) {
                        NewMemoTimeline::create([
                            'new_memo_id' => $document->id,
                            'infostatus' => $nama_divisi . '_unit_read',
                            'entertime' => now(),
                        ]);
                    } else {
                        if ($document->unitvalidation == "Aktif") {
                            $nama_divisi_combine_read = $timelines->firstWhere('infostatus', $nama_divisi . '_combine_read');
                            if (!$nama_divisi_combine_read) {
                                NewMemoTimeline::create([
                                    'new_memo_id' => $document->id,
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
            if (!$nama_divisi_finish_read && $document->seniormanagervalidation == "Aktif") {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_finish_read',
                    'entertime' => now(),
                ]);
            }
        } elseif (strpos($nama_divisi, 'Senior Manager') !== false) {
            $seniorvalid_read = $timelines->firstWhere('infostatus', $nama_divisi . '_seniorvalid_read');
            if (!$seniorvalid_read && $document->operatorcombinevalidation == "Aktif") {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_seniorvalid_read',
                    'entertime' => now(),
                ]);
            }
        } elseif ($nama_divisi == "Manager Product Engineering") {
            $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
            if (!$nama_divisi_unit_read && $document->manageroperatorvalidation != "Aktif" && $document->SMname == "Senior Manager Engineering" && $document->operatorcombinevalidation == "Aktif") {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_unit_read',
                    'entertime' => now(),
                ]);
            }
        } else {
            $nama_divisi_unit_read = $timelines->firstWhere('infostatus', $nama_divisi . '_unit_read');
            if (!$nama_divisi_unit_read) {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_unit_read',
                    'entertime' => now(),
                ]);
            }
        }

        // Inisialisasi logs atau data tambahan lainnya
        $logs = [];


        $unit = Unit::where('name', $nama_divisi)->first();
        // Cari tugas divisi yang pertama ditemukan
        $tugasdivisis = Notification::where('notifmessage_id', $document->id)
            ->where('idunit', $unit->id)
            ->where("notifmessage_type", "App\\Models\\NewMemo")
            ->get();

        // Jika tugas divisi ditemukan, update status sudah dibaca
        if ($tugasdivisis->isNotEmpty()) {
            foreach ($tugasdivisis as $tugasdivisi) {
                $tugasdivisi->update([
                    'status' => "read",
                ]);
            }
        }


        // Mengembalikan tampilan dengan data yang diperlukan
        return view('newmemo.memo.memo', compact(
            'document',
            'logs',
            'projectname',
            'statussetujulist',
            'yourrule'
        ));
    }


    public function roadmap(Request $request, $id)
    {
        $document = NewMemo::with(['feedbacks.files', 'komats', 'timelines'])->findOrFail($id);
        // Mengembalikan tampilan dengan data yang diperlukan
        $newMemo = $document->detailonedocument();

        $MTPRsend = $newMemo->MTPRsend;
        $operatorsignature = $newMemo->operatorsignature;
        $operatorshare = $newMemo->operatorshare;
        $unitpicvalidation = $newMemo->unitpicvalidation;
        $unitvalidation = $newMemo->unitvalidation;
        $operatorcombinevalidation = $newMemo->operatorcombinevalidation;
        $selfunitvalidation = $newMemo->selfunitvalidation;
        $seniormanagervalidation = $newMemo->seniormanagervalidation;
        $MTPRvalidation = $newMemo->MTPRvalidation;
        $manageroperatorvalidation = $newMemo->manageroperatorvalidation;
        $posisi1 = $newMemo->posisi1;
        $posisi2 = $newMemo->posisi2;
        $posisi3 = $newMemo->posisi3;
        $positionPercentage = $newMemo->positionPercentage;
        $SMname = $newMemo->SMname;
        $unitstepverificator = $newMemo->unitstepverificator;
        $withMTPR = $newMemo->withMTPR;

        $document->MTPRsend = $MTPRsend;
        $document->operatorshare = $operatorshare;
        $document->operatorsignature = $operatorsignature;
        $document->unitpicvalidation = $unitpicvalidation;
        $document->unitvalidation = $unitvalidation;
        $document->operatorcombinevalidation = $operatorcombinevalidation;
        $document->selfunitvalidation = $selfunitvalidation;
        $document->seniormanagervalidation = $seniormanagervalidation;
        $document->MTPRvalidation = $MTPRvalidation;
        $document->manageroperatorvalidation = $manageroperatorvalidation;
        $document->positionPercentage = $positionPercentage;
        $document->SMname = $SMname;
        $document->posisi1 = $posisi1;
        $document->posisi2 = $posisi2;
        $document->posisi3 = $posisi3;
        $document->withMTPR = $withMTPR;
        // Membuat array singkatan unit
        $unitsingkatan = [];
        $allunitunderpe = Category::getlistCategoryMemberByName('unitunderpe');

        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->singkatanUnit($unit);
        }
        $unitsingkatan["Senior Manager Engineering"] = "SME";
        $unitsingkatan["Senior Manager Desain"] = "SMD";
        $unitsingkatan["Senior Manager Teknologi Produksi"] = "SMTP";
        return view('newmemo.roadmap', compact(
            'document',
            'unitsingkatan'
        ));
    }

    public function memoedit($id)
    {
        $document = NewMemo::findOrFail($id);
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        return view('newmemo.editdokumen', compact('document', 'category', 'listproject'));
    }

    public function uploadForm()
    {
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        $allunitunderpe = Category::getlistCategoryMemberByName('unitunderpe');
        return view('newmemo.uploadMTPR', [
            'informasi' => "",
            'filelinkId' => "",
            'listproject' => $listproject,
            'allunitunderpe' => $allunitunderpe,
        ]);
    }

    public function documentsignature($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('newmemo.uploadsignature', compact('document'));
    }

    public function updateinformasimemo(Request $request, $id)
    {
        try {

            $document = NewMemo::with('feedbacks')->findOrFail($id);
            $documentsebelum = $document;
            $timeline = $document->timelines;

            $nama_divisi = $document->operator;

            $nama_divisi_share_read = $timeline->firstWhere('infostatus', $nama_divisi . '_share_read');
            if (!$nama_divisi_share_read) {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => $nama_divisi . '_share_read',
                    'entertime' => now(),
                ]);
            }



            $remaininformation = $document->komats;

            if ($request->has('new_komponen') && $request->has('new_kodematerial') && $request->has('new_supplier')) {
                $komponenList = $request->input('new_komponen');
                $kodeMaterialList = $request->input('new_kodematerial');
                $supplierList = $request->input('new_supplier');
                foreach ($komponenList as $key => $komponen) {
                    if ($komponen != '' && $kodeMaterialList[$key] != '' && $supplierList[$key] != "") {
                        NewMemoKomat::create([
                            'new_memo_id' => $document->id,
                            'kodematerial' => $kodeMaterialList[$key],
                            'material' => $komponen,
                            'supplier' => $supplierList[$key],
                        ]);
                    }

                }
            }


            if ($request->has('project_pic')) {
                $document->update([
                    'documentname' => $request->input('documentname'),
                    'project_type' => $request->input('project_type'),
                    'memokind' => $request->input('memokind'),
                    'project_pic' => json_encode($request->input('project_pic')),
                ]);

                foreach ($request->input('project_pic') as $pic) {
                    try {
                        $namaFile = $request->input('documentname');
                        $namaDivisi = $pic;

                        $unit = Unit::where('name', $pic)->first();
                        $existingFile = Notification::where('idunit', $unit->id)
                            ->where('notifmessage_id', $document->id)
                            ->where('notifarray->type', 'share')
                            ->first();

                        if (!$existingFile) {
                            //mail anggota unit
                            $document->notifsystem()->create([
                                'status' => 'unread',
                                'idunit' => $unit->id,
                                'infostatus' => 'User dont read this message',
                                'notifarray' => json_encode(['type' => 'share', 'message' => 'Order received']),
                            ]);
                            //mail manager unit
                            $unit = Unit::where('name', 'Manager ' . $pic)->first();
                            $document->notifsystem()->create([
                                'status' => 'unread',
                                'idunit' => $unit->id,
                                'infostatus' => 'User dont read this message',
                                'notifarray' => json_encode(['type' => 'share', 'message' => 'Order received']),
                            ]);

                            // Menyusun daftar file untuk di-download
                            $feedbacks = $document->feedbacks;
                            $list = '';

                            if ($feedbacks->isNotEmpty()) {
                                $files = $feedbacks->last()->files;

                                foreach ($files as $file) {
                                    $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
                                }
                            }

                            $pesan = "Memo " . $namaFile . " dikirimkan ke unit ini untuk dicek/dikerjakan.\n\n" .
                                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                                $list .
                                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

                            // pensiun
                            // $jenispesan = "text";

                            // tele anggota unit
                            // $teleunit = TelegramMessagesAccount::where('account', $pic)->first();

                            // TelegramMessage::create([
                            //     'message_kind' => $jenispesan,
                            //     'message' => $pesan,
                            //     'telegram_messages_accounts_id' => $teleunit->id,
                            //     'created_at' => now(),
                            //     'updated_at' => now(),
                            // ]);

                            // tele manager unit
                            // $teleunit = TelegramMessagesAccount::where('account', 'Manager ' . $pic)->first();

                            // TelegramMessage::create([
                            //     'message_kind' => $jenispesan,
                            //     'message' => $pesan,
                            //     'telegram_messages_accounts_id' => $teleunit->id,
                            //     'created_at' => now(),
                            //     'updated_at' => now(),
                            // ]);

                            // wa anggota unit
                            Wagroupnumber::ujisendunit($pic, $pesan);


                            $document->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Memo berhasil dikirim ke unit unit',
                                    'datasebelum' => '',
                                    'datasesudah' => $document,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id, // Add user_id here
                                'aksi' => 'documentshare',
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error in storing data: ' . $e->getMessage());
                    }
                }


            } else {
                $document->update([
                    'documentname' => $request->input('documentname'),
                    'project_type' => $request->input('project_type'),
                    'memokind' => $request->input('memokind'),
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
        $document->systemLogs()->create([
            'message' => json_encode([
                'message' => $pesan,
                'datasebelum' => '',
                'datasesudah' => $document,
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'documentupdate',
        ]);
        return redirect()->route('new-memo.show', ['memoId' => $document->id]);
    }

    public function documentfeedback($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('newmemo.uploadfeedback', compact('document'));
    }

    public function sendDecision(Request $request, $id)
    {
        $document = NewMemo::findOrFail($id);
        $posisiid = $request->input('posisi');
        $decision = $request->input('decision');
        $newmemofeedback = NewMemoFeedback::findOrFail($posisiid);
        $newmemofeedback->conditionoffile = $decision;
        $newmemofeedback->save();
        return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');

    }
    public function deletedFeedbackDecision(Request $request, $id)
    {
        $document = NewMemo::findOrFail($id);
        $posisiid = $request->input('posisi');
        $newmemofeedback = NewMemoFeedback::findOrFail($posisiid)->delete();
        return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');

    }
    public function documentmanagerfeedback($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('newmemo.uploadmanagerfeedback', compact('document'));
    }


    public function documentcombine($id)
    {
        $document = NewMemo::findOrFail($id);
        return view('newmemo.uploadcombine', compact('document'));
    }

    public function sendfowardDocument(Request $request, $id)
    {
        $level = $request->input("level");
        $document = NewMemo::with('feedbacks')->findOrFail($id);
        $idfeedback = $request->input('idfeedback');
        $decision = $request->input('decision');
        $newmemofeedback = NewMemoFeedback::findOrFail($idfeedback);
        $newmemofeedback->conditionoffile = "Terkirim";
        $newmemofeedback->save();
        $files = $newmemofeedback->files;

        // Create new feedback
        $newmemofeedbackbaru = NewMemoFeedback::create([
            'new_memo_id' => $id,
            'pic' => auth()->user()->rule ?? 'unknown',
            'author' => $newmemofeedback->author,
            'sudahdibaca' => $newmemofeedback->sudahdibaca,
            'hasilreview' => $newmemofeedback->hasilreview,
            'level' => $level,
            'email' => $newmemofeedback->email,
            'comment' => $newmemofeedback->comment,
            'conditionoffile' => "Diterima",
            'conditionoffile2' => $request->input('conditionoffile2'),
        ]);

        // Mengelola file
        foreach ($files as $file) {
            CollectFile::create([
                'filename' => $file->filename,
                'link' => $file->link,
                'collectable_id' => $newmemofeedbackbaru->id,
                'collectable_type' => NewMemoFeedback::class,
            ]);
        }





        if ($level === "selesai") {
            $timelines = collect($document->timelines); // Menggunakan collect untuk $timelines
            $documentclosed = $timelines->firstWhere('infostatus', 'documentclosed');
            if (!$documentclosed) {
                NewMemoTimeline::create([
                    'new_memo_id' => $document->id,
                    'infostatus' => 'documentclosed',
                    'entertime' => now(),
                ]);
            }
            $document->update([
                'documentstatus' => "Tertutup",
            ]);

            $pesan = "Memo " . $request->input("documentname") . " telah ditutup.";

            // log
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Memo berhasil dibuat',
                    'datasebelum' => '',
                    'datasesudah' => $document,
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'documentclosed',
            ]);

            return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
        } else {
            // Menyusun daftar file untuk di-download
            $feedbacks = $document->feedbacks;
            $list = '';

            if ($feedbacks->isNotEmpty()) {
                $files = $feedbacks->last()->files;

                foreach ($files as $file) {
                    $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
                }
            }

            $pesan = "Memo " . $request->input("documentname") . " dikirimkan ke " . $level . " untuk menunggu persetujuan.\n\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

            // If $level starts with "Manager ", remove that prefix
            if (Str::startsWith($level, 'Manager ')) {
                $level = Str::replaceFirst('Manager ', '', $level);
            }

            // mail
            $unit = Unit::where('name', $level)->first();

            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'User dont read this message',
                'notifarray' => json_encode(
                    [
                        'type' => 'order',
                        'message' => 'Order received'
                    ]
                ),
            ]);



            if ($level == "MTPR") {
                if ($newmemofeedback->hasilreview == "Ya, dapat diterima") {
                    $pesan = "Memo " . $request->input("documentname") . " telah dinyatakan disetujui.\n\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::ujisendunit("QC INC", $pesan);


                }

                // send to wa
                Wagroupnumber::sendWaMessage(['6285335086789'], $pesan);

            } else {



                // send to wa
                Wagroupnumber::ujisendunit($level, $pesan);
            }


            // masukan log
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Memo berhasil dibuat',
                    'datasebelum' => '',
                    'datasesudah' => $document,
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'documentfowarded',
            ]);

            return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
        }




    }



    public function newmemodownload(Request $request)
    {
        // Ambil data dokumen berdasarkan daftar ID yang dikirim dari AJAX
        $newmemos = NewMemo::whereIn('id', $request->document_ids)
            ->with('komats')
            ->get();

        return Excel::download(new NewMemoExport($newmemos), 'new_memo.xlsx');
    }


    public function newmemodownloadall(Request $request)
    {
        $selectedUnit = $request->input('unit', 'all'); // Default 'all' jika kosong

        // Ambil semua data new memo beserta relasinya
        $newmemos = NewMemo::with('komats', 'projectType')->get();

        if ($selectedUnit !== 'all') {
            $newmemos = $newmemos->reject(function ($newmemo) use ($selectedUnit) {
                $projectPic = json_decode($newmemo->project_pic, true);

                // Jika JSON valid dan berupa array, cek apakah unit ada dalam array
                $statusUnit = is_array($projectPic) && in_array($selectedUnit, $projectPic, true);

                return !$statusUnit; // Jika statusUnit == false, data direject (dihapus)
            });
        }

        // Cek apakah hasil filter ada data atau tidak
        if ($newmemos->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang tersedia untuk unit yang dipilih.');
        }

        // Kirim data ke Export Class
        return Excel::download(new NewMemoExport($newmemos), 'new_memo_per_project.xlsx');
    }






    public function updateStatus(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $document = NewMemo::findOrFail($id);

        // Ubah status dokumen
        $newStatus = $request->status;
        $document->documentstatus = $newStatus;
        $document->save();

        if ($newStatus == 'Tertutup' && $request->hasFile('file')) {
            // Mengembalikan status baru sebagai respons AJAX
            if ($newStatus == 'Tertutup' && $request->hasFile('file')) {
                try {
                    $userName = auth()->user()->name;
                    // Simpan feedback
                    $feedback = NewMemoFeedback::create([
                        'new_memo_id' => $document->id,
                        'pic' => auth()->user()->rule,
                        'author' => $userName,
                        'sudahdibaca' => "tutupterpaksa",
                        'hasilreview' => "tutupterpaksa",
                        'level' => "tutupterpaksa",
                        'email' => auth()->user()->email,
                        'comment' => "tutupterpaksa",
                        'conditionoffile' => "tutupterpaksa",
                        'conditionoffile2' => "tutupterpaksa",
                    ]);

                    // Handle file uploads
                    foreach ($request->file('file') as $uploadedFile) {
                        // Dapatkan nama file asli dan formatnya
                        $filename = $uploadedFile->getClientOriginalName();
                        $fileFormat = $uploadedFile->getClientOriginalExtension();
                        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                        // Gabungkan nama file, nama pengguna, dan format file
                        $newFilename = "{$filenameWithoutExtension}_{$userName}.{$fileFormat}";

                        // Periksa apakah nama file sudah ada, dan buat nama baru jika perlu
                        $count = 0;
                        while (CollectFile::where('filename', $newFilename)->exists()) {
                            $count++;
                            $newFilename = "{$filenameWithoutExtension}_{$count}.{$fileFormat}";
                        }

                        // Simpan file di folder 'public/uploads'
                        $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                        // Simpan informasi file ke database
                        $newmemoFile = new CollectFile();
                        $newmemoFile->filename = $newFilename;
                        $newmemoFile->link = str_replace('public/', '', $path); // Hapus 'public/' dari path
                        $newmemoFile->collectable_id = $feedback->id;
                        $newmemoFile->collectable_type = NewMemoFeedback::class;
                        $newmemoFile->save();
                    }


                    // Log perubahan
                    $pesan = 'Memo: ' . $document->id . ' berhasil diupdate dokumen dengan bertanda tangan.';
                    $document->systemLogs()->create([
                        'message' => json_encode([
                            'message' => $pesan,
                            'datasebelum' => '',
                            'datasesudah' => $document,
                        ]),
                        'level' => 'info',
                        'user' => auth()->user()->name,
                        'user_id' => auth()->user()->id, // Add user_id here
                        'aksi' => 'documentforcetoclosed',
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['new_status' => "Gagal"], 500);
                }
            }

        }

        // Mengembalikan status baru sebagai respons AJAX
        return response()->json(['new_status' => $newStatus]);
    }

    public function uploadsignaturefeedbackmerge(Request $request, $id)
    {
        try {
            // Find the document to be updated
            $document = NewMemo::findOrFail($id);

            // Update user information
            $userName = auth()->user()->name;
            $userEmail = auth()->user()->email;
            $level = $request->input('level');


            if ($request->input('feedbacklevel')) {
                $pic = str_replace("Manager ", "", auth()->user()->rule);
            } else {
                $pic = auth()->user()->rule;
            }

            // Create new feedback
            $feedback = NewMemoFeedback::create([
                'new_memo_id' => $document->id,
                'pic' => $pic,
                'author' => $userName,
                'sudahdibaca' => $request->input('review'),
                'hasilreview' => $request->input('hasil_review'),
                'level' => $level,
                'email' => $userEmail,
                'comment' => $request->input('comment'),
                'conditionoffile' => $request->input('conditionoffile'),
                'conditionoffile2' => $request->input('conditionoffile2'),
            ]);

            // Handle file uploads
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedFile) {
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                    $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                    $filename = $filenameWithUserAndFormat;

                    $count = 0;
                    $newFilename = $filename;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    }

                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = $newFilename;
                    $newmemoFile->link = str_replace('public/', '', $path);
                    ;
                    $newmemoFile->collectable_id = $feedback->id;
                    $newmemoFile->collectable_type = NewMemoFeedback::class;
                    $newmemoFile->save();
                }
            }

            if ($request->filecount > 0) {
                for ($i = 0; $i < $request->filecount; $i++) {
                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = "filekosong";
                    $newmemoFile->link = '';
                    $newmemoFile->collectable_id = $feedback->id;
                    $newmemoFile->collectable_type = NewMemoFeedback::class;
                    $newmemoFile->save();
                }
            }


            // Logging based on conditionoffile2 value
            if ($request->input('conditionoffile2') == "signature") {
                $pesan = 'Memo: ' . $document->id . ' berhasil diupdate dokumen dengan bertanda tangan.';
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'documentsignature',
                ]);
            } elseif ($request->input('conditionoffile2') == "combine") {
                $pesan = 'Memo: ' . $document->id . ' menerima finalisasi feedback oleh PE.';
                $pesansingkat = 'Finalisasi feedback diupload';
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'documentcombine',
                ]);
            } elseif ($request->input('conditionoffile2') == "feedback") {
                $pesan = 'Memo: ' . $document->id . ' menerima feedback oleh ' . (strpos(auth()->user()->rule, "Manager") !== false ? 'Manager' : 'staff') . '.';
                $pesansingkat = 'Memo berhasil menerima feedback';
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'documentfeedback',
                ]);
            }

            return redirect()->route('new-memo.show', ['memoId' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    public function unsendDecision(Request $request, $id)
    {
        // Temukan dokumen yang sesuai dengan ID
        $document = NewMemo::findOrFail($id);
        $idfeedback = $request->input('idfeedback');

        // Temukan feedback yang sesuai dengan ID
        $newmemofeedback = NewMemoFeedback::findOrFail($idfeedback);
        $waktudasar = $newmemofeedback->created_at;

        // Ambil semua feedback yang terkait dengan dokumen
        $userinformations = $document->feedbacks;

        // Hapus feedback yang lebih baru dari $waktudasar
        foreach ($userinformations as $userinformation) {
            $waktu = $userinformation->created_at;
            if ($waktu > $waktudasar) {
                $userinformation->delete();
            }
        }

        // Hapus feedback yang dipilih
        $newmemofeedback->delete();

        // Redirect ke halaman yang sesuai
        return redirect()->route('new-memo.show', ['memoId' => $document->id]);
    }

    public function singkatanUnit($namaUnit)
    {
        if ($namaUnit == "RAMS") {
            return $namaUnit;
        } elseif ($namaUnit == "MTPR") {
            return $namaUnit;
        } else {
            $singkatan = "";
            $kata = explode(" ", $namaUnit);
            foreach ($kata as $k) {
                $singkatan .= substr($k, 0, 1);
            }
            return $singkatan;
        }

    }
    public function indexterbuka()
    {
        // Cache selama 3 jam (180 menit)
        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        foreach ($units as $key => $unit) {
            // Memeriksa apakah nama unit mengandung kata "Manager"
            if (str_contains($unit->name, 'Manager') || str_contains($unit->name, 'Senior Manager') || str_contains($unit->name, 'MTPR')) {
                // Menghapus unit dari koleksi
                unset($units[$key]);
            } else {
                // Menetapkan singkatan unit jika unit tidak dihapus
                $unitname = $this->singkatanUnit($unit->name);
                $unit->singkatan = str_replace('&', 'AND', $unitname);
            }
        }
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        $allunitunderpe = Category::getlistCategoryMemberByName('unitunderpe');

        // Membuat array singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->singkatanUnit($unit);
        }
        $unitsingkatan["Senior Manager Engineering"] = "SME";
        $unitsingkatan["Senior Manager Desain"] = "SMD";
        $unitsingkatan["Senior Manager Teknologi Produksi"] = "SMTP";
        // Mendapatkan semua dokumen dengan relasi terkait
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])->where('documentstatus', "Terbuka")->get();
        [$revisiall, $documents] = NewMemo::indexlogic($listproject, $documents, $units);


        // Mengembalikan tampilan dengan data yang diperlukan
        return view('newmemo.index.index', compact('revisiall', 'allunitunderpe', 'unitsingkatan', 'units'));
    }

    public function indextertutup(Request $request)
    {
        // Ambil dokumen dengan status "Tertutup"
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])
            ->where('documentstatus', "Tertutup")->orderBy('created_at', 'desc');

        if ($request->ajax()) {
            return DataTables::of($documents)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($document) {
                    return '<input type="checkbox" class="document-checkbox" data-id="' . $document->id . '">';
                })
                ->addColumn('deadline', function ($document) {
                    $timeline = collect($document->timelines)->where('infostatus', 'documentopened')->first();
                    return $timeline ? Carbon::parse($timeline->entertime)->addDays(5)->format('d/m/Y') : null;
                })
                ->addColumn('action', function ($document) {
                    $authuser = auth()->user();
                    $button = '';

                    // Tombol untuk mengubah status dokumen
                    if ($authuser->rule == $document->operator || $authuser->rule == "superuser") {
                        $button .= '<button type="button" class="btn document-status-button document-status-button-' . ($document->documentstatus == 'Terbuka' ? 'open' : 'closed') . ' btn-sm ' . ($document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success') . '" title="' . $document->documentstatus . '" onclick="toggleDocumentStatus(this)" data-document-status="' . $document->documentstatus . '" data-document-id="' . $document->id . '">';
                        $button .= '<i class="' . ($document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle') . '"></i>';
                        $button .= '<span>' . $document->documentstatus . '</span></button>';
                    } else {
                        $button .= '<button type="button" class="btn document-status-button document-status-button-' . ($document->documentstatus == 'Terbuka' ? 'open' : 'closed') . ' btn-sm ' . ($document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success') . '" title="' . $document->documentstatus . '" data-document-status="' . $document->documentstatus . '" data-document-id="' . $document->id . '">';
                        $button .= '<i class="' . ($document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle') . '"></i>';
                        $button .= '<span>' . $document->documentstatus . '</span></button>';
                    }

                    // Tombol detail
                    $button .= '<a class="btn btn-primary btn-sm" href="' . route('new-memo.show', ['memoId' => $document->id, 'rule' => $authuser->rule]) . '"><i class="fas fa-folder"></i> Detail</a>';

                    // Tombol hapus untuk superuser
                    if ($authuser->rule == "superuser") {
                        $button .= '<button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(\'' . $document->id . '\')"><i class="fas fa-eraser"></i> Delete</button>';
                    }

                    // Tombol detail
                    $button .= '<a class="btn btn-warning btn-sm" href="' . route('new-memo.roadmap', ['memoId' => $document->id]) . '"><i class="fas fa-map"></i> Roadmap </a>';

                    // Tombol detail
                    $button .= '<a class="btn bg-maroon btn-sm" href="' . route('new-memo.timelinetracking', ['memoId' => $document->id]) . '"><i class="fas fa-flag"></i> Milestone </a>';


                    // Tombol detail
                    $button .= '<a class="btn btn-default bg-teal btn-sm" href="' . route('new-memo.downloadfilesfromlastfeedback', ['memoId' => $document->id]) . '"><i class="fas fa-download"></i> Last File</a>';

                    $document = $document->detailonedocument();
                    // Button with verification status display
                    if ($document->verification_status) {
                        $button .= '<button type="button" class="btn btn-success btn-sm document-status-button document-status-button-closed" disabled><i class="fas fa-check-circle"></i> Verified</button>';
                    } else {
                        $button .= '<button type="button" class="btn btn-danger btn-sm document-status-button document-status-button-open" onclick="toggleDocumentStatus(this)" data-document-status="' . $document->documentstatus . '" data-document-id="' . $document->id . '">';
                        $button .= '<i class="' . ($document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle') . '"></i>';
                        $button .= '<span>' . 'Unverified' . '</span></button>';
                    }




                    return $button;
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }

        // Jika bukan AJAX, kembalikan tampilan
        return view('newmemo.index.indexyajra');
    }


    public function unfinished($selectedUnits = ['Quality Engineering', 'Mechanical Engineering System'])
    {
        $data = [];
        // Ambil semua user ID untuk semua unit yang dipilih
        $userIds = User::where(function ($query) use ($selectedUnits) {
            foreach ($selectedUnits as $selectedUnit) {
                $query->orWhere('rule', 'like', '%' . $selectedUnit . '%');
            }
        })->pluck('id');

        // Ambil semua jobticket dengan checker_id yang sesuai dan filter dokumen yang belum selesai
        $CheckerJobtickets = Jobticket::with(['jobticketStarted.revisions'])
            ->whereIn('checker_id', $userIds)  // Ambil jobticket yang memiliki checker_id dari unit yang dipilih
            ->whereHas('jobticketStarted.revisions', function ($query) {
                $query->whereNull('checker_status');  // Pastikan checker_status masih null
            })
            ->get();

        // Ambil semua jobticket dengan approver_id yang sesuai dan filter dokumen berdasarkan status
        $ApproverJobtickets = Jobticket::with(['jobticketStarted.revisions'])
            ->whereIn('approver_id', $userIds)
            ->whereHas('jobticketStarted.revisions', function ($query) {
                $query->whereNull('approver_status')
                    ->whereNotNull('checker_status');
            })
            ->get();








        foreach ($selectedUnits as $selectedUnit) {
            // Ambil semua user ID
            $filteredUserIds = User::where('rule', 'like', '%' . $selectedUnit . '%')->pluck('id');

            // Ambil semua jobticket dengan checker_id yang sesuai dan filter dokumen yang belum selesai
            $jobtickets = $CheckerJobtickets->whereIn('checker_id', $filteredUserIds);

            // Hitung jumlah dokumen yang belum selesai per checker_id
            $unfinishedCheckerCounts = $jobtickets->groupBy('checker_id')->map->count();

            // Pastikan unfinishedCheckerCounts adalah collection
            if (!$unfinishedCheckerCounts instanceof \Illuminate\Support\Collection) {
                $unfinishedCheckerCounts = collect();
            }

            // Ambil data user yang memiliki dokumen belum selesai
            $unfinishedCheckerDocuments = User::whereIn('id', $unfinishedCheckerCounts->keys())
                ->get()
                ->map(function ($user) use ($unfinishedCheckerCounts) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'unfinished_count' => $unfinishedCheckerCounts[$user->id] ?? 0,
                    ];
                })
                ->sortByDesc('unfinished_count')
                ->values();





            // Ambil semua jobticket dengan approver_id yang sesuai dan filter dokumen berdasarkan status
            $unfinishedApproverJobtickets = $ApproverJobtickets->whereIn('approver_id', $filteredUserIds);

            // Hitung jumlah dokumen yang belum selesai per approver_id
            $unfinishedApproverCounts = $unfinishedApproverJobtickets->groupBy('approver_id')->map->count();

            // Pastikan unfinishedApproverCounts adalah collection
            if (!$unfinishedApproverCounts instanceof \Illuminate\Support\Collection) {
                $unfinishedApproverCounts = collect();
            }

            // Ambil data user yang memiliki dokumen belum selesai
            $unfinishedApproverDocuments = User::whereIn('id', $unfinishedApproverCounts->keys())
                ->get()
                ->map(function ($user) use ($unfinishedApproverCounts) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'unfinished_count' => $unfinishedApproverCounts[$user->id] ?? 0,
                    ];
                })
                ->sortByDesc('unfinished_count')
                ->values();

            $data[$selectedUnit] = [
                'checker' => $unfinishedCheckerDocuments,
                'approver' => $unfinishedApproverDocuments
            ];
        }

        return $data;

    }


    public function notifMemowhatsapp()
    {
        // Cache unit names selama 3 jam (180 menit)
        $units = Cache::remember('unit_names', 180, function () {
            return Unit::all()->pluck('name')->toArray();
        });

        // Ambil semua memo dengan status 'Terbuka'
        $newMemos = NewMemo::with(['feedbacks', 'komats', 'timelines'])->where('documentstatus', 'Terbuka')->get();

        // Ambil status unit dari memo
        $dataunit = Newmemo::getstatusunit();

        // Ambil data lead time per unit
        $importdata = Newmemo::leadtimeperunit();

        // Inisialisasi array untuk memisahkan memo berdasarkan unit
        $memoByUnit = [];

        foreach ($units as $unit) {
            $memoByUnit[$unit] = [
                'document' => [],
                'rank' => $importdata[$unit]['rank'] ?? "",
                'unitcount' => $importdata[$unit]['unitcount'] ?? "",
                'leadtimeaverage' => $importdata[$unit]['leadtimeaverage'] ?? "",
                'memocount' => $importdata[$unit]['memocount'] ?? "",
            ];
        }

        foreach ($newMemos as $newMemo) {
            $newMemo = $newMemo->detailonedocument();
            $pics = json_decode($newMemo->project_pic, true);

            if (is_array($pics)) {
                foreach ($pics as $pic) {
                    if (in_array($pic, $units)) {
                        $documentNumber = $newMemo->documentnumber;
                        $statusUnit = $dataunit[$documentNumber][$pic] ?? null;

                        if ($statusUnit != "Aktif") {
                            $newMemo->statusunit = $statusUnit;
                            $memoByUnit[$pic]['document'][] = $newMemo;
                        }

                        if ($pic == "Product Engineering") {
                            if ($newMemo->operatorcombinevalidation == "Ongoing" || $newMemo->operatorcombinevalidation == 'Sudah dibaca' || $newMemo->operatorcombinevalidation == 'Belum dibaca') {
                                $newMemo->statusunit = "Perlu combine feedback";
                                $memoByUnit[$pic]['document'][] = $newMemo;
                            }

                            if ($newMemo->manageroperatorvalidation == "Ongoing" || $newMemo->manageroperatorvalidation == 'Sudah dibaca' || $newMemo->manageroperatorvalidation == 'Belum dibaca') {
                                $newMemo->statusunit = "Manager perlu menyetujui combine feedback";
                                $memoByUnit[$pic]['document'][] = $newMemo;
                            }


                        }
                    }
                }
            }
        }

        // Menambahkan dokumen yang tidak terikat ke unit manapun (unbound)
        $unboundnewMemos = $newMemos->filter(function ($doc) {
            $projectPic = json_decode($doc->project_pic, true);
            // Cek jika project_pic adalah null, array kosong, atau bukan array
            return is_null($projectPic) || (is_array($projectPic) && empty($projectPic));
        });

        foreach ($unboundnewMemos as $unboundnewMemo) {
            // Atur status untuk dokumen unbound
            $unboundnewMemo->statusunit = "Sebagai distributor dokumen, Belum dibagikan ke unit - unit";

            // Pastikan operator valid sebelum menambahkan ke memoByUnit
            if (isset($unboundnewMemo->operator) && array_key_exists($unboundnewMemo->operator, $memoByUnit)) {
                $memoByUnit[$unboundnewMemo->operator]['document'][] = $unboundnewMemo;
            }
        }







        // Ambil unit yang termasuk dalam kategori 'unitunderpe'
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0] ?? "[]";
        $allunitunderpe = json_decode(trim($categoryprojectbaru, '"'), true);

        $dataunit = collect($this->unfinished($allunitunderpe));
        foreach ($allunitunderpe as $unit) {
            $data['unitstatusterakhir'] = $memoByUnit[$unit];
            $data['jobtickets'] = $dataunit->get($unit, []);
            $data['unit'] = $unit;

            // Generate PDF
            // Generate tanggal saat ini dalam format "Y-m-d"
            $tanggal = date('Y-m-d'); // Format: 2024-12-17
            $pdf = Pdf::loadView('newmemo.unitmemoallertpdf', $data);
            $nama = "app/" . $unit . '_' . $tanggal . '_Notification.pdf';
            $pdfFilePath = [
                "link" => storage_path($nama),
                "namafile" => $nama
            ];
            $pdf->save($pdfFilePath["link"]);

            // Simpan file PDF ke storage dan database
            $file = $this->storeUploadedFile($pdfFilePath["link"], $unit);
            $list = "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";

            $pesan = 'Laporan Memo dan Jobticket (Bagi unit yang berpartisipasi)' . "\n\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";


            // Kirim pesan melalui WhatsApp (tambahkan logika pengiriman jika diperlukan)

            // wa anggota unit
            Wagroupnumber::ujisendunit($unit, $pesan);
            // wa file anggota unit
            Wagroupnumber::ujisendunit($unit, "" . $file->id);






            // Hapus file PDF sementara (opsional)
            if (file_exists($pdfFilePath["link"])) {
                unlink($pdfFilePath["link"]);
            }

        }

        return "Sukses";
    }

    private function storeUploadedFile($filePath, $unit)
    {
        $uploadedFile = new \Illuminate\Http\UploadedFile($filePath, basename($filePath));

        $filename = $uploadedFile->getClientOriginalName();
        $fileFormat = $uploadedFile->getClientOriginalExtension();
        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
        $filenameWithUserAndFormat = $filenameWithoutExtension . '.' . $fileFormat;

        $count = 0;
        $newFilename = $filenameWithUserAndFormat;
        while (CollectFile::where('filename', $newFilename)->exists()) {
            $count++;
            $newFilename = $filenameWithoutExtension . '_' . $count . '.' . $fileFormat;
        }

        $path = $uploadedFile->storeAs('public/uploads', $newFilename);

        $notificationdaily = NotificationDaily::create(["name" => $unit]);

        $files = new CollectFile();
        $files->filename = $newFilename;
        $files->link = str_replace('public/', '', $path);
        $files->collectable_id = $notificationdaily->id; // Menghubungkan file dengan feedback
        $files->collectable_type = NotificationDaily::class; // Tipe polimorfik

        $files->save();

        return $files;
    }


    public function leadtimeperunit()
    {
        $memoByUnit = NewMemo::leadtimeperunit();
        return $memoByUnit;
    }

    public function downloadfilesfromlastfeedback($id)
    {
        // Mengambil dokumen beserta feedback, komats, dan timelines
        $document = NewMemo::with(['feedbacks', 'komats', 'timelines'])->findOrFail($id);

        // Mengambil feedback terakhir
        $lastFeedback = $document->feedbacks->last();

        // Mengecek apakah ada feedback terakhir
        if (!$lastFeedback) {
            return redirect()->back()->with('error', 'Tidak ada feedback terakhir yang ditemukan.');
        }

        // Mengambil file dari tabel CollectFile berdasarkan feedback terakhir
        $files = CollectFile::where('collectable_id', $lastFeedback->id)
            ->where('collectable_type', NewMemoFeedback::class)
            ->get();

        // Mengecek apakah ada file terkait
        if ($files->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada file pada feedback terakhir.');
        }

        // Mengambil path file dan mengunduh satu per satu
        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . $file->link); // Asumsi path file disimpan dalam kolom 'link'

            if (file_exists($filePath)) {
                // Mengembalikan download untuk setiap file
                return response()->download($filePath);
            } else {
                return redirect()->back()->with('error', 'File ' . $file->filename . ' tidak ditemukan.');
            }
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

        // Lakukan pencarian berdasarkan documentnumber atau documentname
        $results = NewMemo::with(['feedbacks.files']) // Eager loading untuk feedback dan files
            ->where('documentnumber', 'LIKE', '%' . $query . '%')
            ->orWhere('documentname', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $latestUpdate = $results->max('created_at')->format('d/m/Y');
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
            $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";
        }

        // Looping melalui hasil pencarian dan susun dalam format teks
        foreach ($results as $result) {
            $textResult .= "📄 *Nomor Dokumen*: " . $result->documentnumber . "\n";
            $textResult .= "📋 *Nama Dokumen*: " . $result->documentname . "\n";
            $textResult .= "📅 *Status Dokumen*: " . $result->documentstatus . "\n";
            $textResult .= "📂 *Jenis Memo*: " . $result->memokind . "\n\n"; // Tambahkan spasi di sini

            // Mengambil file dari feedback terakhir
            $lastFeedback = $result->feedbacks->last();
            if ($lastFeedback) {
                $files = $lastFeedback->files;

                // Menambahkan file ke dalam hasil
                if ($files->isNotEmpty()) {
                    $textResult .= "📁 *File dari Posisi Terakhir:*\n";
                    foreach ($files as $file) {
                        $textResult .= "- ID: " . $file->id . "\n";
                        $textResult .= "- Nama File: " . "\n"; // Format rapi
                        $textResult .= $file->filename . "\n\n"; // Format rapi
                        $textResult .= "\n";
                    }
                    $textResult .= "📂 *Unduh dengan instruksi:* `Downloadfile_id`\n\n"; // Tambahkan baris baru di sini
                } else {
                    $textResult .= "📁 *Tidak ada file pada feedback terakhir.*\n\n";
                }
            } else {
                $textResult .= "📁 *Tidak ada feedback untuk dokumen ini.*\n\n";
            }

            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar hasil
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }

    public function timelinetracking($memoId)
    {
        // Mengambil dokumen beserta feedback, komats, dan timelines
        $document = NewMemo::with(['feedbacks', 'komats', 'timelines'])->findOrFail($memoId);
        // Mengambil feedback terakhir
        $unitsingkatan = [];
        $allunitunderpe = Category::getlistCategoryMemberByName('unitunderpe');

        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->singkatanUnit($unit);
        }
        $unitsingkatan["Senior Manager Engineering"] = "SME";
        $unitsingkatan["Senior Manager Desain"] = "SMD";
        $unitsingkatan["Senior Manager Teknologi Produksi"] = "SMTP";
        $unitsingkatan["Manager Product Engineering"] = "MPE";
        $document = $document->detailonedocument();
        $Feedbacks = $document->feedbacks;
        return view('newmemo.timeline.ujitimeline', compact('document', 'unitsingkatan'));
    }



}
