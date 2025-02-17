<?php


namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use App\Exports\JobticketExport;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Wagroupnumber;
use Illuminate\Support\Facades\Storage;
use App\Exports\MultiSheetJobticketExport;
use App\Exports\ClosedJobTicketExport;
use App\Models\Jobticketreason;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use App\Models\Newprogressreport;
use App\Models\JobticketStartedRevReason;
use App\Models\Category;
use App\Models\NewMemo;
use App\Models\Jobticket;
use App\Models\JobticketHistory;
use App\Models\JobticketStartedRev;
use App\Models\Newprogressreporthistory;
use App\Models\User;
use App\Models\CollectFile;
use App\Models\JobticketIdentity;
use App\Models\JobticketPart;
use App\Models\JobticketDocumentKind;

use App\Models\ProjectType;
use App\Imports\ColumnAImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Imports\RawprogressreportsImport;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;

class JobticketController extends Controller
{

    public function storejobticketdokumentkind(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create new JobticketDocumentKind
        $documentKind = JobticketDocumentKind::create($validatedData);

        // Redirect or return a success response
        return redirect()->back()->with('success', 'Jobticket Document Kind created successfully!');
    }

    // Function to display all JobticketDocumentKinds (View)
    public function indexjobticketdokumentkind()
    {
        // Fetch all document kinds
        // Cache selama 3 jam (180 menit)
        $documentKinds = Cache::remember('documentKinds', 180, function () {
            return JobticketDocumentKind::all();
        });

        // Return the view with the document kinds data
        return view('jobticket.jobticketdocumentkind', compact('documentKinds'));
    }
    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        }
        if ($jenisupload == "formatrencana") {
            $hasil = $this->formatrencana($request);
        } elseif ($jenisupload == "format") {
            $hasil = $this->formatdasar($request);
        } elseif ($jenisupload == "Treediagram") {
            $hasil = $this->formatTreediagram($request);
        }
        return $hasil;
    }



    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $unit = $request->input('progressreportname');


        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });



        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();
            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }


            $processedData = $this->progressreportexported($revisiData, $unit);

            $groupedData = [];
            $exportedRecords = [];
            $exportedCount = 0;

            // Group data by key
            foreach ($processedData as $item) {
                $proyek_type = $item['proyek_type'];
                $documentkind = $item['documentkind'];
                $unit = $item['unit'];
                $documentnumber = $item['documentnumber'];
                $groupKey = $proyek_type . '@' . $documentkind . '@' . $unit . '@' . $documentnumber;

                if (in_array($proyek_type, $listproject)) {
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }
                    $groupedData[$groupKey][] = $item;
                }
            }

            // Fetch related data outside of the loop
            $projectTitles = array_unique(array_column($processedData, 'proyek_type'));
            $documentKindNames = array_unique(array_column($processedData, 'documentkind'));
            $unitNames = array_unique(array_column($processedData, 'unit'));
            $documentNumbers = array_unique(array_column($processedData, 'documentnumber'));

            $projects = ProjectType::whereIn('title', $projectTitles)->get()->keyBy('title');
            $documentKinds = JobticketDocumentKind::whereIn('name', $documentKindNames)->get()->keyBy('name');

            $units = Unit::whereIn('name', $unitNames)->get()->keyBy('name');
            $existingJobtickets = JobticketIdentity::whereIn('documentnumber', $documentNumbers)->get()->keyBy('documentnumber');

            $existingReportsLastRevs = Jobticket::whereIn('jobticket_identity_id', $existingJobtickets->pluck('id'))
                ->latest()
                ->get()
                ->groupBy('jobticket_identity_id')
                ->map(function ($group) {
                    return $group->first();
                });

            $drafterNames = array_unique(array_column($processedData, 'drafter'));
            $checkerNames = array_unique(array_column($processedData, 'checker'));
            $approverNames = array_unique(array_column($processedData, 'approver'));

            $drafters = User::whereIn('name', $drafterNames)->get()->keyBy('name');
            $checkers = User::whereIn('name', $checkerNames)->get()->keyBy('name');
            $approvers = User::whereIn('name', $approverNames)->get()->keyBy('name');

            $stringkiriman = "";

            foreach ($groupedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";
                list($proyek_type, $documentkind, $unit, $documentnumber) = explode('@', $groupKey);

                $project = $projects->get($proyek_type);
                $dockind = $documentKinds->get($documentkind);

                $unitpic = $units->get($unit);
                if (!$project || !$unitpic) {
                    continue;
                }
                $jobticketPart = JobticketPart::firstOrCreate([
                    'proyek_type_id' => $project->id,
                    'unit_id' => $unitpic->id,
                ]);

                if ($dockind) {
                    $jobticket = $existingJobtickets->get($documentnumber) ?? JobticketIdentity::create([
                        'jobticket_part_id' => $jobticketPart->id,
                        'documentnumber' => $documentnumber,
                        'jobticket_documentkind_id' => $dockind->id,
                    ]);
                } else {
                    $jobticket = $existingJobtickets->get($documentnumber) ?? JobticketIdentity::create([
                        'jobticket_part_id' => $jobticketPart->id,
                        'documentnumber' => $documentnumber,
                    ]);
                }




                $id = $jobticket->id;
                $existingRecord = $existingReportsLastRevs->get($id);

                $newRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $drafter_id = $drafters->get($item['drafter'])->id ?? null;
                    $checker_id = $checkers->get($item['checker'])->id ?? null;
                    $approver_id = $checkers->get($item['approver'])->id ?? null;


                    // $approver_id = $approvers->get($item['approver'])->id ?? null;
                    if ($approver_id == null) {
                        if ($unitpic->id == 14) {     // QE
                            $approver_id = 94;
                        } elseif ($unitpic->id == 3) { // Electrical
                            $approver_id = 27;
                        } elseif ($unitpic->id == 2) { // Product Engineering
                            $approver_id = 41;
                        }
                    }





                    $rev = $item['rev'];
                    $deadlinerelease = $item['deadlinerelease'];
                    if ($rev === null || $rev === '') {
                        continue;
                    }

                    if ($existingRecord && $existingRecord->documentnumber == $item['documentnumber']) {
                        if ($this->compareRevisions($rev, $existingRecord->rev)) {
                            // Jika revisi baru lebih tinggi, tambahkan ke newRecords
                            $newRecords[] = $this->prepareJobticketData($id, $item, $drafter_id, $checker_id, $approver_id, $deadlinerelease);
                            $exportedRecords[] = $item;
                            $exportedCount++;

                            // Catat revisi yang di-update ke history
                            $historyRecords[] = $this->prepareJobticketData($existingRecord->id, $item, $drafter_id, $checker_id, $approver_id, $deadlinerelease);
                        } else {
                            // Jika revisi sama atau lebih rendah, lewati
                            continue;
                        }
                    } else {
                        // Jika tidak ada existingRecord, input revisi baru
                        $newRecords[] = $this->prepareJobticketData($id, $item, $drafter_id, $checker_id, $approver_id, $deadlinerelease);
                        $exportedRecords[] = $item;
                        $exportedCount++;
                    }


                }

                if (!empty($newRecords)) {
                    if (
                        !Jobticket::where('jobticket_identity_id', $id)
                            ->where('rev', $rev)
                            ->exists()
                    ) {
                        // Insert new record if it does not already exist
                        Jobticket::insert($newRecords);
                    }

                }

                // if (!empty($historyRecords)) {
                //     JobticketHistory::insert($historyRecords);  // Assuming you have a history table
                // }
            }

            return response()->json(['message' => 'Data Excel successfully imported: ' . $stringkiriman], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing Excel file: ' . $e->getMessage()], 500);
        }
    }

    private function prepareJobticketData($id, $item, $drafter_id, $checker_id, $approver_id, $deadlinerelease)
    {
        $data = [
            'jobticket_identity_id' => $id,
            'rev' => $item['rev'],
            'documentname' => $item['documentname'],
            'level' => $item['level'],
            'drafter_id' => $drafter_id,
            'checker_id' => $checker_id,
            'approver_id' => $approver_id,
            'inputer_id' => auth()->user()->id,
            'publicstatus' => 'released',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if ($deadlinerelease != null) {
            $data['deadlinerelease'] = $deadlinerelease;
        }
        return $data;
    }

    private function compareRevisions($newRev, $oldRev)
    {
        // Define the custom revision order, including numbers and letters
        $revisionOrder = array_merge(['0'], range('A', 'Z'));

        // Handle cases where revision is missing or incorrect
        if ($newRev === null || $oldRev === null) {
            return false;
        }

        // Get the index of the new and old revisions
        $newRevIndex = array_search($newRev, $revisionOrder);
        $oldRevIndex = array_search($oldRev, $revisionOrder);

        // Jika revisi tidak ditemukan dalam $revisionOrder, kembalikan false
        if ($newRevIndex === false || $oldRevIndex === false) {
            return false;
        }

        // Return true jika revisi baru lebih tinggi dari yang lama
        return $newRevIndex > $oldRevIndex;
    }


    public function progressreportexported($importedData, $unit)
    {
        $revisiData = []; // Initialize an array to store processed data

        foreach ($importedData as $key => $row) {
            if ($row[3] != null) {
                $documentnumber = trim($row[3] ?? "");
                if (empty($documentnumber) || strlen($documentnumber) < 5) {
                    $documentnumber = 'DOC-' . date('YmdHis') . '-' . uniqid(); // Generate unique number
                }

                $proyek_type = trim($row[1] ?? "");
                $unit = $this->perpanjangan(trim($row[2] ?? ""));


                $rev = trim($row[4] ?? "");

                // Skip this iteration if $rev is empty
                if ($rev == "") {
                    continue;
                }

                $documentname = trim($row[5] ?? "");
                $level = trim($row[6] ?? "");
                $documentkind = trim($row[7] ?? "");


                $drafter = trim($row[8] ?? "");
                $checker = trim($row[9] ?? "");
                $approver = trim($row[10] ?? "");
                $deadlinerelease = trim($row[11] ?? "");

                // Normalize the date format for deadlinerelease
                $deadlinerelease = $this->normalizeDate($deadlinerelease);

                // Determine document kind and types for each unit
                // $documentkind = "";
                // $docTypes = [];

                // if ($unit == "Quality Engineering") {
                //     $docTypes = [
                //         'itp' => trim($row[11] ?? ""),
                //         'qr' => trim($row[12] ?? ""),
                //         'inc' => trim($row[13] ?? ""),
                //         'fab' => trim($row[14] ?? ""),
                //         'fin' => trim($row[15] ?? ""),
                //         'fit' => trim($row[16] ?? ""),
                //         'V&V' => trim($row[17] ?? "")
                //     ];
                // } elseif ($unit == "Electrical Engineering System") {
                //     $docTypes = [
                //         'DESC' => trim($row[11] ?? ""),
                //         'SPT' => trim($row[12] ?? ""),
                //         'FUS' => trim($row[13] ?? ""),
                //         'DWG' => trim($row[14] ?? ""),
                //         'VED' => trim($row[15] ?? ""),
                //         'JTF' => trim($row[16] ?? "")
                //     ];
                // } elseif ($unit == "Product Engineering") {
                //     $docTypes = [
                //         'BOK' => trim($row[11] ?? ""),
                //         'SPT' => trim($row[12] ?? ""),
                //         'V&V' => trim($row[13] ?? ""),
                //         'VED' => trim($row[14] ?? ""),
                //         'REN' => trim($row[15] ?? ""),
                //         'STD' => trim($row[16] ?? ""),
                //         'DRB' => trim($row[17] ?? ""),
                //         'BDG' => trim($row[18] ?? ""),
                //         'CBC' => trim($row[19] ?? ""),
                //         'JTF' => trim($row[20] ?? ""),
                //         'RIS' => trim($row[21] ?? ""),
                //         'REVIEW' => trim($row[22] ?? ""),
                //         'PROPOSAL' => trim($row[23] ?? "")
                //     ];
                // }

                // // Determine document kind based on the first "v" found in $docTypes
                // foreach ($docTypes as $type => $value) {
                //     if ($value === "v") {
                //         $documentkind = $type;
                //         break;
                //     }
                // }

                // Store processed data in $revisiData, using documentnumber as the key
                $revisiData[] = [
                    'proyek_type' => $proyek_type,
                    'documentnumber' => $documentnumber,
                    'documentname' => $documentname,
                    'documentkind' => $documentkind,
                    'rev' => $rev,
                    'unit' => $unit,
                    'level' => $level,
                    'drafter' => $drafter,
                    'checker' => $checker,
                    'approver' => $approver,
                    'deadlinerelease' => $deadlinerelease,
                ];
            }







        }



        return $revisiData;
    }

    /**
     * Normalize the date format to YYYY-MM-DD.
     *
     * @param string $date
     * @return string
     */
    private function normalizeDate($date)
    {
        if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $date)) {
            // Format: DD/MM/YYYY
            $dateParts = explode('/', $date);
            return "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
        } elseif (preg_match('/\d{2}-\d{2}-\d{4}/', $date)) {
            // Format: DD-MM-YYYY
            $dateParts = explode('-', $date);
            return "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
        } elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            // Format: YYYY-MM-DD (already correct)
            return $date;
        }

        // If date format is invalid, return null or handle as needed
        return null;
    }





    public function showUploadFormExcel()
    {
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        $unit_for_progres_dokumen = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        return view('jobticket.uploadexcel', compact('categoryproject', 'unit_for_progres_dokumen'));
    }

    public function perpanjangan($namasingkatan)
    {
        if ($namasingkatan == "QE") {
            return "Quality Engineering";
        } elseif ($namasingkatan == "EES") {
            return "Electrical Engineering System";
        } elseif ($namasingkatan == "MES") {
            return "Mechanical Engineering System";
        } elseif ($namasingkatan == "PE") {
            return "Product Engineering";
        } elseif ($namasingkatan == "EL") {
            return "Desain Elektrik";
        } elseif ($namasingkatan == "PS") {
            return "Preparation & Support";
        } elseif ($namasingkatan == "SD") {
            return "Shop Drawing";
        } elseif ($namasingkatan == "TP") {
            return "Teknologi Proses";
        } elseif ($namasingkatan == "WT") {
            return "Welding Technology";
        } elseif ($namasingkatan == "BG") {
            return "Desain Bogie & Wagon";
        } elseif ($namasingkatan == "CB") {
            return "Desain Carbody";
        } elseif ($namasingkatan == "SM") {
            return "Sistem Mekanik";
        } elseif ($namasingkatan == "INT") {
            return "Desain Interior";
        }
    }

    public function getColumnA()
    {
        // Path ke file Excel
        $filePath = public_path('daftaranggota/daftaranggota.xlsx');
        // Menggunakan import class untuk membaca file Excel
        $data = Excel::toCollection(new ColumnAImport, $filePath);
        // Memastikan sheet tidak kosong
        if ($data->isEmpty()) {
            return response()->json(['error' => 'File Excel kosong atau tidak valid'], 400);
        }
        // Mengambil sheet pertama
        $sheet = $data->first();
        // Mengambil nilai dari kolom A
        $columnA = $sheet->pluck('0')->toArray();
        // Mengembalikan sebagai respons JSON
        return $columnA;
    }

    // JobticketController.php


    public function index()
    {
        // Cache selama 3 jam (180 menit)
        $documentKinds = Cache::remember('documentKinds', 180, function () {
            return JobticketDocumentKind::all();
        });

        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });

        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        $users = Cache::remember('users', 180, function () {
            return User::all();
        });

        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");

        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = JobticketPart::singkatanUnit($unit);
        }

        // Ambil data jobticket dan revisi
        [$alljobticket, $revisiall] = JobticketPart::indexjobticket($unitsingkatan, $listproject);

        return view('jobticket.index', compact('alljobticket', 'revisiall', 'documentKinds', 'units', 'listproject', 'users'));
    }

    public function AddDocument(Request $request)
    {
        try {
            // Cek atau buat JobticketPart
            $jobticketpart = JobticketPart::firstOrCreate([
                'unit_id' => $request->unit_id,
                'proyek_type_id' => $request->proyek_type_id, // Assuming project corresponds to proyek_type_id
            ]);

            // Cek atau buat JobticketIdentity
            $jobticketidentity = JobticketIdentity::firstOrCreate([
                'jobticket_part_id' => $jobticketpart->id,
                'jobticket_documentkind_id' => $request->jobticket_documentkind_id,
                'documentnumber' => $request->documentnumber,
            ]);

            // Periksa revisi terakhir
            // Ambil jobticket terakhir berdasarkan jobticket_identity_id
            $lastJobticket = Jobticket::where('jobticket_identity_id', $jobticketidentity->id)
                ->latest('id') // Urutkan berdasarkan ID, revisi terbaru
                ->first();

            $lastRev = $lastJobticket?->rev ?? null;

            // Validasi apakah revisi lebih besar
            $isUpRevision = Jobticket::IsUpRevision($request->rev, $lastRev);
            if ($isUpRevision == false) {
                return response()->json(['message' => 'Jobticket dengan revisi yang sama sudah ada.'], 200);
            }




            $data = [
                'jobticket_identity_id' => $jobticketidentity->id,
                'rev' => $request->rev,
                'documentname' => $request->documentname,
                'inputer_id' => auth()->user()->id,
                'publicstatus' => 'released',
            ];
            if ($request->drafter != null) {
                $data['drafter_id'] = $request->drafter;
            }
            if ($request->checker != null) {
                $data['checker_id'] = $request->checker;
            }

            $rule = auth()->user()->rule; // Mengakses rule dari user yang sedang login
            $rule = str_replace("Manager ", "", $rule); // Menghapus kata "Manager " dari rule
            $unitpic = Unit::where('name', $rule)->first(); // Mengambil unit berdasarkan nama yang sesuai

            if ($unitpic) { // Pastikan unit ditemukan
                switch ($unitpic->id) {
                    case 14: // QE
                        $approver_id = 94;
                        break;
                    case 3: // Electrical
                        $approver_id = 27;
                        break;
                    case 2: // Product Engineering
                        $approver_id = 41;
                        break;
                    case 8: // Desain Elektrik
                        $approver_id = 51;
                        break;
                    default:
                        $approver_id = null; // Jika unit ID tidak sesuai
                        break;
                }
            } else {
                $approver_id = null; // Jika unit tidak ditemukan
            }


            $data['approver_id'] = $approver_id;

            $jobticket = Jobticket::create($data);


            // $jobticketIdentity = $jobticket->jobticketIdentity;
            // $jobticketPart = $jobticketIdentity->jobticketPart;

            // $projectname = $jobticketPart->projectType->title;

            // $message = "👤 *Manager*, silakan melakukan verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname} pada link berikut:\n" .
            //     "🔗 Kunjungi : ( http://192.168.13.160:8000/jobticket/manager/terbuka/ )\n" .
            //     "\n🚀 *Ayo segera verifikasi draft task/tugas !* Terima kasih atas kerjasamanya! 🙏😊";


            // if($jobticketIdentity->jobticket_documentkind_id==7){ // QE
            //     $approverwaphonenumber = User::find(94)->waphonenumber;
            //     Wagroupnumber::sendWaMessage([$approverwaphonenumber], $message);
            // }elseif($jobticketIdentity->jobticketPart->unit_id==3){ //ees
            //     $approverwaphonenumber1 = User::find(27)->waphonenumber; //pak diva
            //     Wagroupnumber::sendWaMessage([$approverwaphonenumber1], $message);
            // }elseif($jobticketIdentity->jobticketPart->unit_id==2){ //pe
            //     $approverwaphonenumber1 = User::find(41)->waphonenumber; // pak ndaru
            //     Wagroupnumber::sendWaMessage([$approverwaphonenumber1], $message);
            // }




            return response()->json(['message' => 'Dokumen berhasil ditambahkan! Tunggu konfirmasi Manager.'], 200);



        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan dokumen.'], 500);
        }
    }

    public function releasedDocument(Request $request, $id)
    {
        try {
            // Cari jobticket berdasarkan ID
            $jobticket = Jobticket::findOrFail($id);

            // Perbarui status menjadi 'released'
            $jobticket->update([
                'publicstatus' => 'released'
            ]);

            return response()->json([
                'message' => 'Dokumen berhasil dirilis! Tunggu konfirmasi Manager.'
            ], 200);
        } catch (\Exception $e) {
            // Tangani error dan beri pesan kesalahan
            return response()->json([
                'message' => 'Terjadi kesalahan saat merilis dokumen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }








    public function managershow()
    {
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });

        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        $users = Cache::remember('users', 180, function () {
            return User::all();
        });


        $useronly = auth()->user();
        // Mendapatkan peran pengguna yang sedang login
        $role = auth()->user()->rule;

        // Menghilangkan kata "Manager" dari peran jika ditemukan, kemudian melakukan trim
        $unitTitle = preg_replace('/\bManager\b/', '', $role);
        $unitTitle = trim($unitTitle);

        // Mencari unit berdasarkan judul yang telah dimodifikasi
        $unit = Unit::where('name', $unitTitle)->first();

        if (!$unit) {
            // Jika unit tidak ditemukan, kembalikan pesan atau tampilan error
            return redirect()->back()->with('error', 'Unit tidak ditemukan');
        }

        // Mengambil semua jobticket parts yang terkait dengan unit
        $jobticketparts = JobticketPart::where('unit_id', $unit->id)->get();

        // Mendapatkan ID dari setiap jobticket part
        $jobticketpartIds = $jobticketparts->pluck('id');

        $jobticketidentitys = JobticketIdentity::wherein('jobticket_part_id', $jobticketpartIds)->get();

        // Mendapatkan ID dari setiap jobticket part
        $jobticketidentityIds = $jobticketidentitys->pluck('id');



        // Mencari jobtickets berdasarkan ID yang diperoleh
        $jobtickets = Jobticket::with([
            'jobticketIdentity.jobticketDocumentkind',
            'jobticketIdentity.jobticketPart.projectType',
            'jobticketStarted.revisions'
        ])->where('publicstatus', 'drafted')->wherein('jobticket_identity_id', $jobticketidentityIds)->get();


        // Memanggil fungsi documentperson untuk memproses data
        $data = $this->documentperson($jobtickets, $listproject);
        $drafterJobtickets = $jobtickets->filter(function ($jobticket) use ($useronly) {
            return $jobticket->drafter_id === $useronly->id;
        });

        // $checkerJobtickets = $jobtickets->filter(function ($jobticket) use ($useronly) {
        // return $jobticket->checker_id === $useronly->id;
        // });

        $checkerJobtickets = $jobtickets->filter(function ($jobticket) use ($useronly) {
            // Pastikan relasi jobticketStarted dan revisions tersedia
            if ($jobticket->jobticketStarted && $jobticket->jobticketStarted->revisions) {
                // Cek apakah checker_status di revisions adalah null
                $revisions = $jobticket->jobticketStarted->revisions;
                $hasNullCheckerStatus = $revisions->contains(function ($revision) {
                    return $revision->checker_status === null;
                });

                // Jika checker_id cocok dengan user yang login dan ada checker_status null
                return $jobticket->checker_id === $useronly->id && $hasNullCheckerStatus;
            }
            return false;
        });
        $approverJobtickets = $jobtickets->filter(function ($jobticket) use ($useronly) {
            return $jobticket->approver_id === $useronly->id;
        });



        // Mengembalikan tampilan dengan data yang sudah diproses
        return view('jobticket.showdocumentself.showdocumentself', [
            'useronly' => $useronly,
            'documentKinds' => $data['documentKinds'],
            'newmemo' => $data['newmemo'],
            'projects' => $data['projects'],
            'jobtickets' => $data['jobtickets'],
            'listanggota' => $data['listanggota'],
            'availableUsers' => $data['availableUsers'],
            'availabledocuments' => $data['availabledocuments'],
            'listdocuments' => $data['listdocuments'],
            'listproject' => $listproject,
            'units' => $units,
            'users' => $users,
            'drafterJobtickets' => $drafterJobtickets,
            'checkerJobtickets' => $checkerJobtickets,
            'approverJobtickets' => $approverJobtickets,
        ]);
    }


    public function show($id)
    {
        $availableUsers = Cache::remember('available_users', 180, function () {
            return User::pluck('name', 'id');
        });


        // Eager load relasi yang diperlukan dengan memanggil JobticketPart
        $jobticketpart = JobticketPart::with([
            'jobticketidentitys.jobtickets.users',  // Memastikan memanggil relasi users
            'jobticketidentitys.jobtickets.jobticketStarted.revisions.files',  // Eager load revisions via jobticketStarted

            'jobticketidentitys.jobtickets.jobticketStarted',  // Eager load revisions via jobticketStarted



            'jobticketidentitys.jobticketDocumentkind',
            'jobticketidentitys.jobticketHistories',
        ])->findOrFail($id);

        // Mendapatkan relasi jobticketidentitys
        $jobticketidentitys = $jobticketpart->jobticketidentitys;

        // Mendapatkan list anggota dari metode getColumnA()
        $listanggota = $this->getColumnA();

        // Mendapatkan user yang sedang login
        $useronly = auth()->user();

        // Mendapatkan list nama user dari model User


        // Inisialisasi array generasi
        $generasi = [];

        // Looping melalui setiap progress report di dalam jobticketidentitys
        foreach ($jobticketidentitys as $index => $progressReport) {
            // Menghitung jumlah jobticketHistories dengan status 'unread'
            $progressReport->jobticketHistoriescount = $progressReport->jobticketHistories->where('status', 'unread')->count();

            // Filter jobtickets untuk hanya yang memiliki publicstatus 'released'
            $releasedJobtickets = $progressReport->jobtickets->filter(function ($jobticket) {
                return $jobticket->publicstatus === 'released';
            });

            // Ambil jobticket terakhir dengan publicstatus 'released'
            $progressReport->jobticketterakhir = $releasedJobtickets->last();

            $status = optional($progressReport->jobticketterakhir)->status;
            $lastJobticket = optional($progressReport->jobticketterakhir)->jobticketStarted;

            // Pastikan bahwa $lastJobticket bukan null dan revisi terakhir ada
            $lastRevision = $lastJobticket && optional($lastJobticket->revisions)->isNotEmpty()
                ? $lastJobticket->revisions->last()->tracking($status)
                : null;

            $progressReport->allrule = $lastRevision;




            // Menambahkan data children dan menghitung jumlah anak di generasi
            $generasi[$progressReport->id]['childreen'] = $progressReport->children;
            $generasi[$progressReport->id]['count'] = 0;

            // Inisialisasi latestRev
            $latestRev = '';

            // Filter jobtickets untuk hanya yang memiliki publicstatus 'released'
            $releasedJobtickets = collect($progressReport->jobtickets)->filter(function ($jobticket) {
                return $jobticket->publicstatus === 'released';
            });

            // Ambil tiga dokumen terakhir dengan publicstatus 'released'
            $progressReport->lastthreedocument = $releasedJobtickets->slice(-3);

            foreach ($progressReport->lastthreedocument as $jobticket) {
                // Dapatkan nama pengguna dari availableUsers menggunakan drafter_id, checker_id, dan approver_id
                $jobticket->drafter_name = $availableUsers[$jobticket->drafter_id] ?? null;
                $jobticket->checker_name = $availableUsers[$jobticket->checker_id] ?? null;
                $jobticket->approver_name = $availableUsers[$jobticket->approver_id] ?? null;

            }


            // Jika ada histories, lakukan sorting dan ambil rev terbaru
            if (!empty($progressReport->histories)) {
                $histories = collect($progressReport->histories); // Ubah array ke collection
                $sortedHistories = $histories->sortByDesc('rev'); // Sorting berdasarkan 'rev' secara descending
                $firstHistory = $sortedHistories->first(); // Ambil elemen pertama setelah sorting

                if ($firstHistory !== null) {
                    $latestRev = $firstHistory['rev']; // Ambil nilai 'rev' jika elemen tidak null
                    $progressReport->latestRev = $latestRev; // Simpan ke dalam progressReport
                }
            }
        }

        // Mengelompokkan dokumen berdasarkan jobticketDocumentkind
        $groupedDocuments = $jobticketidentitys->groupBy(function ($item) {
            return $item->jobticketDocumentkind->name ?? 'No Document Kind';
        });

        // Mengembalikan view dengan data yang sudah diolah
        return view('jobticket.show.show', [
            'revisiall' => [],
            'jobticketpart' => $jobticketpart,
            'jobticketidentitys' => $jobticketidentitys,
            'listanggota' => $listanggota,
            'useronly' => $useronly,
            'groupedDocuments' => $groupedDocuments,
            'progresspercentage' => 0,
            'data' => [],
            'weekData' => [],
            'datastatus' => [],
            'duplicates' => [],
            'generasi' => $generasi,
        ]);
    }


    public function showunit(Request $request)
    {
        // Get the list of available users
        $availableUsers = Cache::remember('available_users', 180, function () {
            return User::pluck('name', 'id');
        });



        $projects = Cache::remember('projects', 180, function () {
            return ProjectType::all();
        });

        $jobticketdocumentkinds = Cache::remember('jobticketdocumentkinds', 180, function () {
            return JobticketDocumentKind::all();
        });



        // Get the user's rule (e.g., Manager)
        $rule = auth()->user()->rule;

        // If the rule contains 'Manager', replace it with ''
        if (str_contains($rule, 'Manager')) {
            $rule = str_replace('Manager ', '', $rule);
        }

        // Get the unit based on the user's rule
        $unit = Unit::where('name', $rule)->first();
        // Ambil ID jobticket_identity yang dipilih
        $selectedIdentityId = request('project');
        if ($selectedIdentityId) {
            $project = ProjectType::find($selectedIdentityId);
            $jobticketpart = JobticketPart::where('unit_id', $unit->id)->where('proyek_type_id', $project->id)->with([
                'jobticketidentitys.jobtickets.users', // Load users related to jobticketidentitys
                'jobticketidentitys.jobtickets.jobticketStarted.revisions.files', // Eager load revisions and files
                'jobticketidentitys.jobtickets.jobticketStarted', // Eager load jobticketStarted
                'jobticketidentitys.jobticketDocumentkind',
                'jobticketidentitys.jobticketHistories',
            ])->first();
        } else {



            // Eager load the necessary relationships with JobticketPart
            $jobticketpart = JobticketPart::where('unit_id', $unit->id)->with([
                'jobticketidentitys.jobtickets.users', // Load users related to jobticketidentitys
                'jobticketidentitys.jobtickets.jobticketStarted.revisions.files', // Eager load revisions and files
                'jobticketidentitys.jobtickets.jobticketStarted', // Eager load jobticketStarted
                'jobticketidentitys.jobticketDocumentkind',
                'jobticketidentitys.jobticketHistories',
            ])->first();
        }



        // Get the jobticketidentitys relationship
        $jobticketidentitys = $jobticketpart->jobticketidentitys;

        // Get the list of members from getColumnA() method
        $listanggota = $this->getColumnA();

        // Get the current logged-in user
        $useronly = auth()->user();



        // Initialize the generasi array
        $generasi = [];

        // Loop through each progress report in jobticketidentitys
        foreach ($jobticketidentitys as $index => $progressReport) {
            // Count the number of unread jobticketHistories
            $progressReport->jobticketHistoriescount = $progressReport->jobticketHistories->where('status', 'unread')->count();

            // Filter jobtickets with publicstatus 'released'
            $releasedJobtickets = $progressReport->jobtickets->filter(function ($jobticket) {
                return $jobticket->publicstatus === 'released';
            });

            // Get the last released jobticket
            $progressReport->jobticketterakhir = $releasedJobtickets->last();

            // Get the status and last jobticketStarted related to the jobticketterakhir
            $status = optional($progressReport->jobticketterakhir)->status;
            $lastJobticket = optional($progressReport->jobticketterakhir)->jobticketStarted;

            // Get the last revision related to the lastJobticket if available
            $lastRevision = $lastJobticket && optional($lastJobticket->revisions)->isNotEmpty()
                ? $lastJobticket->revisions->last()->tracking($status)
                : null;

            // Assign the last revision to the progress report
            $progressReport->allrule = $lastRevision;

            // Add children data and initialize count for each progress report
            $generasi[$progressReport->id]['childreen'] = $progressReport->children;
            $generasi[$progressReport->id]['count'] = 0;

            // Initialize latestRev variable
            $latestRev = '';

            // Filter the jobtickets to only those with publicstatus 'released'
            $releasedJobtickets = collect($progressReport->jobtickets)->filter(function ($jobticket) {
                return $jobticket->publicstatus === 'released';
            });

            // Get the last three released jobtickets
            $progressReport->lastthreedocument = $releasedJobtickets->slice(-3);

            // Loop through the last three documents and assign drafter, checker, and approver names
            foreach ($progressReport->lastthreedocument as $jobticket) {
                $jobticket->drafter_name = $availableUsers[$jobticket->drafter_id] ?? null;
                $jobticket->checker_name = $availableUsers[$jobticket->checker_id] ?? null;
                $jobticket->approver_name = $availableUsers[$jobticket->approver_id] ?? null;
            }

            // If there are histories, sort and get the latest revision
            if (!empty($progressReport->histories)) {
                $histories = collect($progressReport->histories);
                $sortedHistories = $histories->sortByDesc('rev'); // Sort by 'rev' in descending order
                $firstHistory = $sortedHistories->first(); // Get the first element after sorting

                if ($firstHistory !== null) {
                    $latestRev = $firstHistory['rev'];
                    $progressReport->latestRev = $latestRev; // Save the latest revision to the progressReport
                }
            }
        }

        // Group the documents by jobticketDocumentkind
        $groupedDocuments = $jobticketidentitys->groupBy(function ($item) {
            return $item->jobticketDocumentkind->name ?? 'No Document Kind';
        });

        // Return the view with the processed data
        return view('jobticket.showunit.showunit', [
            'revisiall' => [],
            'jobticketpart' => $jobticketpart,
            'jobticketidentitys' => $jobticketidentitys,
            'listanggota' => $listanggota,
            'useronly' => $useronly,
            'groupedDocuments' => $groupedDocuments,
            'progresspercentage' => 0,
            'data' => [],
            'weekData' => [],
            'datastatus' => [],
            'duplicates' => [],
            'generasi' => $generasi,
            'projects' => $projects,
            'jobticketdocumentkinds' => $jobticketdocumentkinds,
            'unit' => $unit
        ]);
    }




    public function closedJobTicket(Request $request)
    {

        $selectedUnit = $request->input('unit');
        $datas = [];
        $jobtickets = Jobticket::with(['jobticketIdentity.jobticketPart.unit'])
            ->where('status', 'closed')->when($selectedUnit, fn($query) => $query->whereHas('jobticketIdentity.jobticketPart.unit', fn($q) => $q->where('name', $selectedUnit)))
            ->get();
        foreach ($jobtickets as $jobticket) {
            if (!isset($datas[$jobticket->jobticket_identity_id]) || $jobticket->updated_at > $datas[$jobticket->jobticket_identity_id]['updated_at']) {
                $datas[$jobticket->jobticket_identity_id] = [
                    'documentname' => $jobticket->documentname,
                    'rev' => $jobticket->rev,
                    'documentnumber' => $jobticket->jobticketIdentity->documentnumber,
                    "updated_at" => $jobticket->updated_at,
                ];
            }
        }

        $excelDatas = array_values($datas);

        return Excel::download(new ClosedJobTicketExport($excelDatas), 'closed_job_ticket.xlsx');
    }



    public function rank(Request $request)
    {

        $unit = Cache::remember('unit_exclude_manager', 180, function () {
            return Unit::where('name', 'NOT LIKE', '%Manager%')->pluck('name')->all();
        });

        $availableUsers = Cache::remember('available_users', 180, function () {
            return User::pluck('name', 'id');
        });

        $selectedUnit = $request->input('unit');
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());
        $previousStartDate = Carbon::parse($startDate)->subWeek()->toDateString();
        $previousEndDate = Carbon::parse($endDate)->subWeek()->toDateString();



        // Ranking drafter minggu ini
        $rankedDrafters = Jobticket::with(['jobticketIdentity.jobticketPart.unit'])
            ->select('drafter_id', DB::raw('COUNT(*) as closed_count'))
            ->whereNotNull('drafter_id')
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->when($selectedUnit, fn($query) => $query->whereHas('jobticketIdentity.jobticketPart.unit', fn($q) => $q->where('name', $selectedUnit)))
            ->groupBy('drafter_id')
            ->orderBy('closed_count', 'desc')
            ->get();

        // Ranking checker minggu ini
        $rankedCheckers = Jobticket::with(['jobticketIdentity.jobticketPart.unit'])
            ->select('checker_id', DB::raw('COUNT(*) as closed_count'))
            ->whereNotNull('checker_id')
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->when($selectedUnit, fn($query) => $query->whereHas('jobticketIdentity.jobticketPart.unit', fn($q) => $q->where('name', $selectedUnit)))
            ->groupBy('checker_id')
            ->orderBy('closed_count', 'desc')
            ->get();

        // Ranking drafter minggu sebelumnya
        $previousWeekData = Jobticket::whereNotNull('drafter_id')
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$previousStartDate, $previousEndDate])
            ->when($selectedUnit, fn($query) => $query->whereHas('jobticketIdentity.jobticketPart.unit', fn($q) => $q->where('name', $selectedUnit)))
            ->select('drafter_id', DB::raw('COUNT(*) as closed_count'))
            ->groupBy('drafter_id')
            ->pluck('closed_count', 'drafter_id');

        // Ranking checker minggu sebelumnya
        $previousWeekDataCheckers = Jobticket::whereNotNull('checker_id')
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$previousStartDate, $previousEndDate])
            ->when($selectedUnit, fn($query) => $query->whereHas('jobticketIdentity.jobticketPart.unit', fn($q) => $q->where('name', $selectedUnit)))
            ->select('checker_id', DB::raw('COUNT(*) as closed_count'))
            ->groupBy('checker_id')
            ->pluck('closed_count', 'checker_id');

        // Proses ranking dan status
        $rankedDrafters = $rankedDrafters->map(function ($item) use ($availableUsers, $previousWeekData) {
            $drafterName = $availableUsers[$item->drafter_id] ?? 'Unknown';

            // Mengecualikan drafter "Muhammad Arifianto"
            if (in_array(strtolower($drafterName), ['muhammad arifianto', 'muhammad arifianto 2'])) {
                return null;
            }

            $previousCount = $previousWeekData[$item->drafter_id] ?? 0;
            $status = $item->closed_count > $previousCount ? 'Meningkat'
                : ($item->closed_count < $previousCount ? 'Menurun' : 'Tetap');

            return [
                'drafter_name' => $drafterName,
                'closed_count' => $item->closed_count,
                'id' => $item->drafter_id,
                'status' => $status,
                'kind' => 'drafter'
            ];
        })->filter()->values();

        $rankedCheckers = $rankedCheckers->map(function ($item) use ($availableUsers, $previousWeekDataCheckers) {
            $checkerName = $availableUsers[$item->checker_id] ?? 'Unknown';
            if (in_array(strtolower($checkerName), ['muhammad arifianto', 'muhammad arifianto 2']))
                return null;
            $previousCount = $previousWeekDataCheckers[$item->checker_id] ?? 0;
            $status = $item->closed_count > $previousCount ? 'Meningkat'
                : ($item->closed_count < $previousCount ? 'Menurun' : 'Tetap');


            return [
                'checker_name' => $checkerName,
                'closed_count' => $item->closed_count,
                'id' => $item->checker_id,
                'status' => $status,
                'kind' => 'checker'
            ];
        })->filter()->values();

        // Data harian untuk chart Drafter
        $chartDataDrafters = $rankedDrafters->map(function ($drafter) use ($startDate, $endDate) {
            $dailyCounts = Jobticket::where('drafter_id', $drafter['id'])
                ->where('status', 'closed')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as daily_count'))
                ->groupBy('date')
                ->pluck('daily_count', 'date')
                ->toArray();

            // Isi dengan data harian dari $startDate hingga $endDate
            $dates = [];
            $counts = [];
            $currentDate = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            while ($currentDate <= $end) {
                $dateStr = $currentDate->toDateString();
                $dates[] = $dateStr;
                $counts[] = $dailyCounts[$dateStr] ?? 0;
                $currentDate->addDay();
            }

            return [
                'label' => $drafter['drafter_name'],
                'data' => $counts,
                'dates' => $dates
            ];
        });

        // Data harian untuk chart Checker
        $chartDataCheckers = $rankedCheckers->map(function ($checker) use ($startDate, $endDate) {
            $dailyCounts = Jobticket::where('checker_id', $checker['id'])
                ->where('status', 'closed')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as daily_count'))
                ->groupBy('date')
                ->pluck('daily_count', 'date')
                ->toArray();
            $dates = [];
            $counts = [];
            $currentDate = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            while ($currentDate <= $end) {
                $dateStr = $currentDate->toDateString();
                $dates[] = $dateStr;
                $counts[] = $dailyCounts[$dateStr] ?? 0;
                $currentDate->addDay();
            }
            return [
                'label' => $checker['checker_name'],
                'data' => $counts,
                'dates' => $dates
            ];
        });

        $reportData = collect();

        // Combine and calculate scores for drafters
        foreach ($rankedDrafters as $drafter) {
            // Cek jika sudah ada nama yang sama
            $existing = $reportData->firstWhere('name', $drafter['drafter_name']);


            if ($existing) {
                // Jika sudah ada, update score dan simpan kembali ke koleksi
                $existing['score'] += $drafter['closed_count'] * 1; // Tambahkan poin drafter
                // Gunakan put untuk mengupdate nilai pada koleksi
                $reportData = $reportData->map(function ($item) use ($existing) {
                    if ($item['name'] === $existing['name']) {
                        return $existing;
                    }
                    return $item;
                });
            } else {
                // Jika belum ada, tambahkan data drafter baru
                $reportData->push([
                    'name' => $drafter['drafter_name'],
                    'id' => $drafter['id'],
                    'score' => $drafter['closed_count'] * 1, // Drafter score (1 point per closed ticket)
                ]);
            }
        }

        // Combine and calculate scores for checkers
        foreach ($rankedCheckers as $checker) {
            // Cek jika sudah ada nama yang sama
            $existing = $reportData->firstWhere('name', $checker['checker_name']);

            if ($existing) {
                // Jika sudah ada, update score dan simpan kembali ke koleksi
                $existing['score'] += $checker['closed_count'] * 0.5; // Tambahkan poin checker
                // Gunakan put untuk mengupdate nilai pada koleksi
                $reportData = $reportData->map(function ($item) use ($existing) {
                    if ($item['name'] === $existing['name']) {
                        return $existing;
                    }
                    return $item;
                });
            } else {
                // Jika belum ada, tambahkan data checker baru
                $reportData->push([
                    'name' => $checker['checker_name'],
                    'id' => $checker['id'],
                    'score' => $checker['closed_count'] * 0.5, // Checker score (0.5 point per closed ticket)
                ]);
            }
        }

        // Sort the report data by score in descending order
        $reportData = $reportData->sortByDesc('score')->values();


        // Ambil semua user ID
        $userIds = User::where('rule', 'like', '%' . $selectedUnit . '%')->pluck('id');

        // Ambil semua jobticket dengan checker_id yang sesuai dan filter dokumen yang belum selesai
        $jobtickets = Jobticket::with(['jobticketStarted.revisions'])
            ->whereIn('checker_id', $userIds)
            ->whereHas('jobticketStarted.revisions', function ($query) {
                $query->whereNull('checker_status');
            })
            ->get();

        // Hitung jumlah dokumen yang belum selesai per checker_id
        $unfinishedCheckerCounts = $jobtickets->groupBy('checker_id')->map->count();

        // Ambil data user yang memiliki dokumen belum selesai
        $unfinishedCheckerDocuments = User::whereIn('id', $unfinishedCheckerCounts->keys())
            ->get()
            ->map(function ($user) use ($unfinishedCheckerCounts) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'unfinished_count' => $unfinishedCheckerCounts[$user->id] ?? 0, // Gunakan unfinishedCheckerCounts
                ];
            })
            ->sortByDesc('unfinished_count') // Mengurutkan berdasarkan unfinished_count (descending)
            ->values(); // Reset indeks



        // Ambil semua jobticket dengan approver_id yang sesuai dan filter dokumen berdasarkan status
        $unfinishedApproverJobtickets = Jobticket::with(['jobticketStarted.revisions'])
            ->whereIn('approver_id', $userIds)
            ->whereHas('jobticketStarted.revisions', function ($query) {
                $query->whereNull('approver_status') // Status approver belum selesai
                    ->whereNotNull('checker_status'); // Status checker sudah selesai
            })
            ->get();

        // Hitung jumlah dokumen yang belum selesai per approver_id
        $unfinishedApproverCounts = $unfinishedApproverJobtickets->groupBy('approver_id')->map->count();

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
            ->sortByDesc('unfinished_count') // Mengurutkan berdasarkan unfinished_count (descending)
            ->values(); // Reset indeks


        if ($selectedUnit) {
            // Ambil semua pengguna dengan rule yang mengandung $selectedUnit
            $users = User::where('rule', 'like', '%' . $selectedUnit . '%')->get();

            // Tambahkan drafter dengan nilai nol jika belum ada
            foreach ($users as $user) {
                $existingDrafter = $rankedDrafters->firstWhere('id', $user->id);
                if (!$existingDrafter) {
                    $rankedDrafters->push([
                        'drafter_name' => $user->name,
                        'closed_count' => 0,
                        'id' => $user->id,
                        'status' => 'Tidak Ada',
                        'kind' => 'drafter',
                    ]);
                }

                // Tambahkan checker dengan nilai nol jika belum ada
                $existingChecker = $rankedCheckers->firstWhere('id', $user->id);
                if (!$existingChecker) {
                    $rankedCheckers->push([
                        'checker_name' => $user->name,
                        'closed_count' => 0,
                        'id' => $user->id,
                        'status' => 'Tidak Ada',
                        'kind' => 'checker',
                    ]);
                }

                $existing = $reportData->firstWhere('id', $user->id);
                if (!$existing) {
                    $reportData->push([
                        'name' => $user->name,
                        'id' => $user->id,
                        'score' => 0, // Nilai nol karena tidak memiliki tiket tertutup
                    ]);
                }

                $existingunfinishedCheckerDocuments = $unfinishedCheckerDocuments->firstWhere('id', $user->id);
                if (!$existingunfinishedCheckerDocuments) {
                    $unfinishedCheckerDocuments->push([
                        'name' => $user->name,
                        'id' => $user->id,
                        'unfinished_count' => 0, // Nilai nol karena tidak memiliki tiket belum selesai
                    ]);
                }

                $existingunfinishedApproverDocuments = $unfinishedApproverDocuments->firstWhere('id', $user->id);
                if (!$existingunfinishedApproverDocuments) {
                    $unfinishedApproverDocuments->push([
                        'name' => $user->name,
                        'id' => $user->id,
                        'unfinished_count' => 0, // Nilai nol karena tidak memiliki tiket belum selesai
                    ]);
                }


            }
        }










        return view('jobticket.rank', compact(
            'rankedDrafters',
            'rankedCheckers',
            'startDate',
            'endDate',
            'unit',
            'selectedUnit',
            'chartDataDrafters',
            'chartDataCheckers',
            'reportData',

            'unfinishedCheckerDocuments', // Tambahkan ini
            'unfinishedApproverDocuments' // Tambahkan ini
        ));
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





    public function downloadWLA(Request $request)
    {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'id' => 'required|integer',
        ]);

        // Format startDate dan endDate ke dd/mm/yyyy
        $startDate = Carbon::parse($request->startDate)->format('d/m/Y');
        $endDate = Carbon::parse($request->endDate)->format('d/m/Y');
        $id = $request->id;

        $user = User::find($id);
        $name = $user ? $user->name : 'Unknown User';

        // Data untuk drafter
        $drafterDocLists = Jobticket::with([
            'jobticketIdentity'
        ])->where('drafter_id', $id)
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$request->startDate, $request->endDate]) // Gunakan original date untuk query
            ->get();

        // Data untuk checker
        $checkerDocLists = Jobticket::with([
            'jobticketIdentity'
        ])->where('checker_id', $id)
            ->where('status', 'closed')
            ->whereBetween('updated_at', [$request->startDate, $request->endDate]) // Gunakan original date untuk query
            ->get();

        if ($drafterDocLists->isEmpty() && $checkerDocLists->isEmpty()) {
            return back()->with('error', 'No data found for the selected criteria.');
        }

        // Unduh file Excel dengan dua sheet
        return Excel::download(
            new MultiSheetJobticketExport($drafterDocLists, $checkerDocLists, $startDate, $endDate, $name),
            'WLA_data.xlsx'
        );
    }




    public function documentperson($jobtickets, $projects)
    {


        // Cache selama 3 jam (180 menit)
        $documentKinds = Cache::remember('documentKinds', 180, function () {
            return JobticketDocumentKind::all();
        });
        // Get the list of available users
        $availableUsers = Cache::remember('available_users', 180, function () {
            return User::pluck('name', 'id');
        });

        // Ambil hanya kolom yang diperlukan untuk mengoptimalkan performa
        $availabledocuments = Newprogressreporthistory::select('id', 'namadokumen', 'nodokumen', 'rev')->get();
        $listdocuments = [];

        foreach ($availabledocuments as $availabledocument) {
            // Cek jika kolom namadokumen dan nodokumen null, lalu atur default
            $availabledocument->namadokumen = $availabledocument->namadokumen ?? "";
            $availabledocument->nodokumen = $availabledocument->nodokumen ?? "";
            $availabledocument->rev = $availabledocument->rev ?? "";

            // Mengubah format id menjadi string dan menambahkan ke array
            $formattedId = (string) $availabledocument->id;
            $listdocuments[] = $formattedId . '@' . $availabledocument->namadokumen . '@' . $availabledocument->nodokumen . '@' . $availabledocument->rev;
        }



        // Mengambil semua dokumen support secara batch
        $documentSupportData = Jobticket::getBatchDocumentSupport($jobtickets);

        // Iterasi melalui setiap jobticket
        // Ambil semua user untuk dropdown atau list user


        // Iterasi melalui setiap jobticket
        foreach ($jobtickets as $jobticket) {
            // Ambil dokumen support dari batch data dengan validasi json_decode
            $documentIds = json_decode($jobticket->documentsupport, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($documentIds)) {
                $jobticket->getDocumentSupport = collect($documentSupportData->whereIn('id', $documentIds));

            } else {
                $jobticket->getDocumentSupport = collect(); // Berikan koleksi kosong jika JSON tidak valid
            }

            // Dapatkan nama pengguna dari availableUsers menggunakan drafter_id, checker_id, dan approver_id
            $jobticket->drafter_name = $availableUsers[$jobticket->drafter_id] ?? null;
            $jobticket->checker_name = $availableUsers[$jobticket->checker_id] ?? null;
            $jobticket->approver_name = $availableUsers[$jobticket->approver_id] ?? null;
        }

        // Mendefinisikan list anggota, bisa diisi data yang relevan jika diperlukan
        $listanggota = [];

        $newmemo = NewMemo::where('documentstatus', 'Terbuka')->pluck('documentname', 'id');


        $data = [
            'documentKinds' => $documentKinds,
            'newmemo' => $newmemo,
            'projects' => $projects,
            'jobtickets' => $jobtickets,  // Mengirim data jobtickets ke view
            'listanggota' => $listanggota, // Mengirim list anggota yang masih kosong
            'availableUsers' => $availableUsers, // Mengirim semua user yang tersedia
            'availabledocuments' => $availabledocuments,
            'listdocuments' => $listdocuments
        ];
        return $data;
    }


    public function showdocument($idjobticketpart, $idjobticketidentity)
    {
        $useronly = auth()->user();

        // Get the list of available users
        $availableUsers = Cache::remember('available_users', 180, function () {
            return User::pluck('name', 'id');
        });

        // Ambil hanya kolom yang diperlukan untuk mengoptimalkan performa
        $availabledocuments = Newprogressreporthistory::select('id', 'namadokumen', 'nodokumen', 'rev')->get();
        $listdocuments = [];

        foreach ($availabledocuments as $availabledocument) {
            // Cek jika kolom namadokumen dan rev null, lalu atur default
            $availabledocument->namadokumen = $availabledocument->namadokumen ?? "";
            $availabledocument->nodokumen = $availabledocument->nodokumen ?? "";
            $availabledocument->rev = $availabledocument->rev ?? "";

            // Mengubah format id menjadi string dan menambahkan ke array
            $formattedId = (string) $availabledocument->id;
            $listdocuments[] = $formattedId . '@' . $availabledocument->namadokumen . '@' . $availabledocument->nodokumen . '@' . $availabledocument->rev;
        }

        // Ambil data jobticketpart
        $jobticketpart = JobticketPart::findOrFail($idjobticketpart);

        // Eager loading untuk mempercepat query pada relasi jobtickets dan user
        $jobticketidentitys = JobticketIdentity::with([
            'jobtickets.jobticketStarted',
            'jobtickets.jobticketIdentity',
        ])->findOrFail($idjobticketidentity);

        // Ambil jobtickets dari jobticketidentitys dan filter berdasarkan publicstatus 'released'
        $jobtickets = $jobticketidentitys->jobtickets->filter(function ($jobticket) {
            return $jobticket->publicstatus === 'released';
        });


        // Mengambil semua dokumen support secara batch
        $documentSupportData = Jobticket::getBatchDocumentSupport($jobtickets);

        // Ambil riwayat revisi jobticket dari tabel JobticketHistory, dengan eager loading relasi
        $jobtickethistories = JobticketHistory::where('jobticket_identity_id', $idjobticketidentity)
            ->with(['newprogressreport', 'newprogressreporthistory']) // Load relasi
            ->orderBy('created_at', 'desc')
            ->get();



        // Iterasi melalui setiap jobticket
        foreach ($jobtickets as $jobticket) {
            // Ambil dokumen support dari batch data dengan validasi json_decode
            $documentIds = json_decode($jobticket->documentsupport, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($documentIds)) {
                $jobticket->getDocumentSupport = collect($documentSupportData->whereIn('id', $documentIds));

            } else {
                $jobticket->getDocumentSupport = collect(); // Berikan koleksi kosong jika JSON tidak valid
            }

            // Dapatkan nama pengguna dari availableUsers menggunakan drafter_id, checker_id, dan approver_id
            $jobticket->drafter_name = $availableUsers[$jobticket->drafter_id] ?? null;
            $jobticket->checker_name = $availableUsers[$jobticket->checker_id] ?? null;
            $jobticket->approver_name = $availableUsers[$jobticket->approver_id] ?? null;
        }


        $listanggota = []; // Implementasi sesuai kebutuhan Anda

        // Cache selama 3 jam (180 menit)
        $availableUsers = Cache::remember('availableUsers', 180, function () {
            return User::all();
        });

        $uprevision = Jobticket::UpAlphabetic($jobtickets->last()->rev);

        $newmemo = NewMemo::where('documentstatus', 'Terbuka')->pluck('documentname', 'id');


        $selectusercontroller = null;
        if ($useronly->id == 1 || $useronly->id == 178 || $useronly->id == 137 || $useronly->id == 139) {
            $selectusercontroller = true;
        } elseif ($useronly->id == 41 || $useronly->id == 153) { // pak ndaru sama mbak cahaya
            $selectusercontroller = true;
        } elseif ($useronly->id == 27 || $useronly->id == 29) { // pak diva sama wella
            $selectusercontroller = true;
        }
        return view('jobticket.showdocument', [
            'newmemo' => $newmemo,
            'jobtickethistories' => $jobtickethistories,
            'jobticketpart' => $jobticketpart,
            'jobticketidentitys' => $jobticketidentitys,
            'jobtickets' => $jobtickets,
            'useronly' => $useronly,
            'listanggota' => $listanggota,
            'availableUsers' => $availableUsers,
            'availabledocuments' => $availabledocuments,
            'listdocuments' => $listdocuments,
            'uprevision' => $uprevision,
            'selectusercontroller' => $selectusercontroller
        ]);
    }

    public function showdocumentselfterbuka()
    {
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });

        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        $users = Cache::remember('users', 180, function () {
            return User::all();
        });


        $useronlyid = auth()->user()->id;
        // Mendapatkan user yang sedang login
        $useronly = User::find($useronlyid);
        // Mengambil semua job ticket berdasarkan drafter_id yang sesuai dengan ID user yang sedang login
        // dan mengurutkan yang status-nya null di urutan paling atas
        $jobtickets = Jobticket::with([
            'jobticketIdentity.jobticketDocumentkind',
            'jobticketIdentity.jobticketPart.projectType',
            'jobticketStarted.revisions'
        ])->where('status', null)->where(function ($query) use ($useronly) {
            $query->where('drafter_id', $useronly->id)
                ->orWhere('checker_id', $useronly->id)
                ->orWhere('approver_id', $useronly->id);
        })->orderByRaw('status IS NULL DESC') // Urutkan status null paling atas
            ->orderBy('status', 'asc') // Lanjutkan dengan urutan status lainnya jika tidak null
            ->get();

        $data = $this->documentperson($jobtickets, $listproject);
        // Bagi ke dalam tiga variabel
        $hasil = Jobticket::drafterCheckerApprover($jobtickets, $useronly);
        $drafterJobtickets = $hasil['drafterJobtickets'];

        foreach ($drafterJobtickets as $drafterJobticket) {
            $lastJobticket = $drafterJobticket->jobticketStarted;
            $status = $drafterJobticket->status;

            // Pastikan bahwa $lastJobticket bukan null dan revisi terakhir ada
            $lastRevision = $lastJobticket && optional($lastJobticket->revisions)->isNotEmpty()
                ? $lastJobticket->revisions->last()->tracking($status)
                : null;

            $drafterJobticket->allrule = $lastRevision;
        }








        $checkerJobtickets = $hasil['checkerJobtickets'];
        $approverJobtickets = $hasil['approverJobtickets'];
        return view('jobticket.showdocumentself.showdocumentself', [
            'useronly' => $useronly,
            'documentKinds' => $data['documentKinds'],
            'newmemo' => $data['newmemo'],
            'projects' => $data['projects'],
            'drafterJobtickets' => $drafterJobtickets,
            'checkerJobtickets' => $checkerJobtickets,
            'approverJobtickets' => $approverJobtickets,
            'listanggota' => $data['listanggota'],
            'availableUsers' => $users,
            'availabledocuments' => $data['availabledocuments'],
            'listdocuments' => $data['listdocuments'],
            'listproject' => $listproject,
            'units' => $units,
            'users' => $users,
        ]);
    }

    public function showdocumentselftertutup()
    {

        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });

        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        $users = Cache::remember('users', 180, function () {
            return User::all();
        });


        $useronlyid = auth()->user()->id;
        // Mendapatkan user yang sedang login
        $useronly = User::find($useronlyid);
        // Mengambil semua job ticket berdasarkan drafter_id yang sesuai dengan ID user yang sedang login
        // dan mengurutkan yang status-nya null di urutan paling atas
        // Panggil data sekali saja
        $jobtickets = Jobticket::with([
            'jobticketIdentity.jobticketDocumentkind',
            'jobticketIdentity.jobticketPart.projectType',
            'jobticketStarted.revisions'
        ])
            ->where(function ($query) use ($useronly) {
                $query->where('drafter_id', $useronly->id)
                    ->orWhere('checker_id', $useronly->id)
                    ->orWhere('approver_id', $useronly->id);
            })
            ->where('status', 'closed') // Filter status 'closed'
            ->orderByRaw('status IS NULL DESC') // Urutkan status null paling atas
            ->orderBy('status', 'asc') // Urutkan berdasarkan status lainnya
            ->get();

        // Bagi ke dalam tiga variabel
        $hasil = Jobticket::drafterCheckerApprover($jobtickets, $useronly);
        $drafterJobtickets = $hasil['drafterJobtickets'];
        $checkerJobtickets = $hasil['checkerJobtickets'];
        $approverJobtickets = $hasil['approverJobtickets'];

        // Sekarang $drafterJobtickets, $checkerJobtickets, dan $approverJobtickets adalah koleksi terpisah


        $data = $this->documentperson($jobtickets, $listproject);
        return view('jobticket.showdocumentself.showdocumentself', [
            'useronly' => $useronly,
            'documentKinds' => $data['documentKinds'],
            'newmemo' => $data['newmemo'],
            'projects' => $data['projects'],
            'drafterJobtickets' => $drafterJobtickets,
            'checkerJobtickets' => $checkerJobtickets,
            'approverJobtickets' => $approverJobtickets,
            'listanggota' => $data['listanggota'],
            'availableUsers' => $data['availableUsers'],
            'availabledocuments' => $data['availabledocuments'],
            'listdocuments' => $data['listdocuments'],
            'listproject' => $listproject,
            'units' => $units,
            'users' => $users,
        ]);
    }



    public function showdocumentmember($id, $status)
    {
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });

        $units = Cache::remember('units', 180, function () {
            return Unit::all();
        });

        $users = Cache::remember('users', 180, function () {
            return User::all();
        });

        $useronly = User::find($id);
        if ($status == "opened") {
            $status = null;
        }
        $jobtickets = Jobticket::with([
            'jobticketIdentity.jobticketDocumentkind',
            'jobticketIdentity.jobticketPart.projectType',
            'jobticketStarted.revisions'
        ])
            ->where('status', $status)
            ->where(function ($query) use ($id) {
                $query->where('drafter_id', $id)
                    ->orWhere('checker_id', $id)
                    ->orWhere('approver_id', $id);
            })
            ->orderByRaw('status IS NULL DESC') // Urutkan status null paling atas
            ->orderBy('status', 'asc') // Lanjutkan dengan urutan status lainnya jika tidak null
            ->get();



        $data = $this->documentperson($jobtickets, $listproject);



        $hasil = Jobticket::drafterCheckerApprover($jobtickets, $useronly);
        $drafterJobtickets = $hasil['drafterJobtickets'];
        $checkerJobtickets = $hasil['checkerJobtickets'];
        $approverJobtickets = $hasil['approverJobtickets'];




        return view('jobticket.showdocumentself.showdocumentself', [
            'documentKinds' => $data['documentKinds'],
            'newmemo' => $data['newmemo'],
            'projects' => $data['projects'],
            'jobtickets' => $data['jobtickets'],
            'useronly' => $useronly,
            'listanggota' => $data['listanggota'],
            'availableUsers' => $data['availableUsers'],
            'availabledocuments' => $data['availabledocuments'],
            'listdocuments' => $data['listdocuments'],
            'drafterJobtickets' => $drafterJobtickets,
            'checkerJobtickets' => $checkerJobtickets,
            'approverJobtickets' => $approverJobtickets,
            'listproject' => $listproject,
            'units' => $units,
            'users' => $users,

        ]);
    }







    public function updateRevision(Request $request, $id)
    {
        // Cari jobticket berdasarkan ID
        $jobticket = Jobticket::find($id);

        if ($jobticket) {
            // Replikasi jobticket untuk membuat jobticket baru dengan revisi yang diupdate
            $newJobticket = $jobticket->replicate();
            $newJobticket->rev = $request->input('rev');
            $newJobticket->drafter_id = null;
            $newJobticket->checker_id = null;
            $newJobticket->approver_id = null;
            $newJobticket->documentsupport = null;
            $newJobticket->note = null;
            $newJobticket->status = null;
            $newJobticket->created_at = now();
            $newJobticket->updated_at = now();
            $newJobticket->save();

            return response()->json(['success' => 'Jobticket baru dengan revisi berhasil dibuat']);
        }

        return response()->json(['error' => 'Jobticket tidak ditemukan'], 404);
    }



    public function updatesupportdocument(Request $request, $jobticketid)
    {
        // Temukan jobticket berdasarkan ID
        $jobticket = Jobticket::with('jobticketIdentity')->find($jobticketid);

        // Jika jobticket tidak ditemukan, kirim respon error
        if (!$jobticket) {
            return response()->json([
                'success' => false,
                'message' => 'Jobticket not found',
            ], 404);
        }

        // Validasi apakah documentsupport ada dalam request
        if (!$request->has('documentsupport') || !is_array($request->documentsupport)) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen support tidak valid atau tidak ditemukan',
            ], 400);
        }

        // Ambil dokumen dari request
        $datas = $request->documentsupport;

        // Buat list ID dokumen yang dipilih
        $listid = [];
        foreach ($datas as $data) {
            $listItem = explode('@', $data); // Gunakan explode untuk memecah string
            $listid[] = $listItem[0]; // Ambil ID dokumen dari elemen pertama
        }

        // Simpan dokumen support dalam bentuk JSON
        $jobticket->documentsupport = json_encode($listid);

        // Ambil dokumen yang sesuai dengan ID yang telah dipilih untuk dikembalikan
        $documents = Newprogressreporthistory::whereIn('id', $listid)->get();

        // Ambil ID dari newprogressreport terkait dokumen yang dipilih
        $ids_newprogressreport = [];
        foreach ($documents as $document) {
            // Akses relasi newProgressReport
            $newprogressreport = $document->newProgressReport;

            if ($newprogressreport) {
                // Ambil ID dari newprogressreport dan tambahkan ke array
                $ids_newprogressreport[] = (string) $newprogressreport->id;
            }
        }

        // Simpan newprogressreportids dengan ID baru
        $jobticket->jobticketIdentity->newprogressreportids = json_encode(array_values(array_unique($ids_newprogressreport)));

        // Simpan perubahan ke database
        $jobticket->push(); // Gunakan push untuk menyimpan model beserta relasinya

        // Kirim respons JSON dengan data dokumen yang diperbarui
        return response()->json([
            'success' => true,
            'message' => 'Dokumen support berhasil diperbarui',
            'data' => $documents
        ]);
    }
















    public function detail($idjobticketpart, $idjobticketidentity, $idjobticket)
    {


        // Check if user already has a signature (TTD) file
        $existingFile = CollectFile::where('collectable_id', auth()->user()->id)
            ->where('collectable_type', User::class)
            ->exists();

        $barcodeoption = $existingFile ? 'true' : 'false';

        // Find the Jobticket by ID with eager loading
        $jobticket = Jobticket::with([
            'jobticketIdentity.jobticketPart', // Eager load jobticketPart through jobticketIdentity
            'jobticketStarted.revisions',     // Eager load revisions through jobticketStarted
        ])->findOrFail($idjobticket);

        // Akses data jobticketIdentitys
        $jobticketidentitys = $jobticket->jobticketIdentity;

        // Akses data jobticketPart dari jobticketIdentitys
        $jobticketpart = $jobticketidentitys->jobticketPart;

        $useronly = auth()->user();
        $rule = $useronly->rule;

        // Ambil unit_id dan rule
        $unit_id = $jobticketpart->unit_id;
        $unit = Unit::findOrFail($unit_id);

        // Get the list of available users and cache the result
        // Ambil daftar pengguna terkait dengan unit secara efisien dan cache hasilnya
        $availableUsers = Cache::remember("available_users_{$unit->id}", 180, function () use ($unit) {
            return User::where('rule', 'like', '%' . $unit->name . '%')->pluck('name', 'id');
        });


        // Fetch related JobticketStarted and its revisions
        $jobticketStarted = $jobticket->jobticketStarted;

        if (!$jobticketStarted) {
            return redirect()->back()->with('error', 'Job ticket not started or no revisions found.');
        }

        $jobticketStartedrev = $jobticketStarted->revisions;

        // Tambahkan nama drafter, checker, dan approver menggunakan $availableUsers
        foreach ($jobticketStartedrev as $revision) {
            $revision->drafter_name = $availableUsers[$revision->drafter_id] ?? null;
            $revision->checker_name = $availableUsers[$revision->checker_id] ?? null;
            $revision->approver_name = $availableUsers[$revision->approver_id] ?? null;
        }

        // Return the view with jobticket and its revisions
        return view('jobticket.showdocumentrev.showdocumentrev', [
            'jobticketpart' => $jobticketpart,
            'jobticketidentitys' => $jobticketidentitys,
            'jobticket' => $jobticket,
            'jobticketStartedrev' => $jobticketStartedrev,
            'useronly' => $useronly,
            'availableUsers' => $availableUsers,
            'barcodeoption' => $barcodeoption
        ]);
    }





    public function markAsRead($id)
    {
        // Cari riwayat jobticket berdasarkan ID
        $history = JobticketHistory::findOrFail($id);

        // Ubah status menjadi 'read'
        $history->status = 'read';
        $history->save();

        return response()->json(['success' => true]);
    }


    public function close($idjobticketpart, $idjobticketidentity, $idjobticket)
    {
        // Retrieve the related JobticketPart, JobticketIdentity, and Jobticket
        $jobticketpart = JobticketPart::findOrFail($idjobticketpart);
        $jobticketidentity = JobticketIdentity::findOrFail($idjobticketidentity);
        $jobticket = Jobticket::findOrFail($idjobticket);

        // Update the Jobticket status to 'closed'
        $jobticket->status = "closed";
        $jobticket->save();

        $useronly = auth()->user();


        // Retrieve the related JobticketStarted and its revisions
        $jobticketStarted = $jobticket->jobticketStarted()->with('revisions')->first();

        // Check if JobticketStarted is found and handle if not
        if (!$jobticketStarted) {
            return redirect()->back()->with('error', 'Job ticket has not been started or no revisions found.');
        }

        // Fetch the revisions related to the job ticket
        $jobticketStartedrev = $jobticketStarted->revisions;

        // Redirect to the job ticket details page with a success message
        return redirect()->route('jobticket.detail', [
            'jobticket_identity_part' => $jobticketpart->id,
            'jobticket_identity_id' => $jobticketidentity->id,
            'jobticket_id' => $jobticket->id
        ])->with('success', 'Dokumen berhasil ditutup.');
    }


    public function revisionapprove(Request $request, $jobticketstartedrev_id, $kindposition)
    {
        try {
            $reason = $request->reason;
            $userName = auth()->user()->name;

            $jobticketstartedrev = JobticketStartedRev::with('files')->findOrFail($jobticketstartedrev_id);
            $jobticketstarted = $jobticketstartedrev->jobticketstarted;
            $jobticket = $jobticketstarted->jobticket;
            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;
            $projectname = $jobticketPart->projectType->title;
            $drafterwaphonenumber = User::find($jobticketstartedrev->drafter_id)->waphonenumber;
            $checkerwaphonenumber = User::find($jobticketstartedrev->checker_id)->waphonenumber;
            $approverwaphonenumber = User::find($jobticketstartedrev->approver_id)->waphonenumber;

            // Menyusun daftar file untuk di-download
            $files = $jobticketstartedrev->files;
            $list = '';
            if ($files->isNotEmpty()) {
                $lastFile = $files->last(); // Mengambil dokumen terakhir
                $list .= "📄 *" . $lastFile->filename . "* ➡️ 🔗 Downloadfile_" . $lastFile->id . "\n";
            }



            if ($kindposition == "checker") {
                $jobticketstartedrev->update([
                    'checker_status' => $request->status
                ]);


                if ($request->status == "Approve") {
                    $message = "👤 *Approver*, Silakan melakukan verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera verifikasi!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$approverwaphonenumber], $message);
                } elseif ($request->status == "Reject") {
                    $jobticketstartedrev->update([
                        'approver_status' => "Reject",
                        'checker_reason' => $request->reason
                    ]);

                    $jobticketstartedrevreason = JobticketStartedRevReason::create([
                        'jobticket_started_rev_id' => $jobticketstartedrev->id,
                        'rule' => 'checker',
                        'reason' => $request->reason,
                    ]);


                    if ($request->hasFile('reason_file')) {
                        foreach ($request->file('reason_file') as $uploadedFile) {
                            $filename = $uploadedFile->getClientOriginalName();
                            $fileFormat = $uploadedFile->getClientOriginalExtension();
                            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                            $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                            $filename = $filenameWithUserAndFormat;

                            $count = 0;
                            $newFilename = $filename;
                            // Check if the file with the same name exists and rename it
                            while (CollectFile::where('filename', $newFilename)->exists()) {
                                $count++;
                                $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . $fileFormat;
                            }

                            $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                            // Save file record to the database
                            $File = new CollectFile();
                            $File->filename = $newFilename;
                            $File->link = str_replace('public/', '', $path);
                            $File->collectable_id = $jobticketstartedrevreason->id;
                            $File->collectable_type = JobticketStartedRevReason::class;
                            $File->save();
                        }
                    }



                    $jobticket->izinkanrevisitugasbaru();
                    $message = "👤 *Drafter*, Silakan cek kembali verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah mengalami penolakan oleh checker pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera perbaiki!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);

                }
            } elseif ($kindposition == "approver") {
                $jobticketstartedrev->update([
                    'approver_status' => $request->status
                ]);
                if ($request->status == "Approve") {
                    $jobticket->status = 'closed';
                    $jobticket->save();

                    $message = "👤 *Drafter*, Selamat dokumen anda telah disetujui dengan No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah diterima oleh approver pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera upload di vault!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);
                } elseif ($request->status == "Reject") {
                    $jobticket->izinkanrevisitugasbaru();
                    $jobticketstartedrev->update([
                        'approver_reason' => $request->reason
                    ]);
                    $message = "👤 *Drafter*, Silakan cek kembali verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah mengalami penolakan oleh approver pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera perbaiki!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);
                }
            }

            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }



    public function reminder(Request $request, $jobticketstartedrev_id, $kindposition)
    {
        try {
            Log::info("Memulai proses reminder untuk ID: $jobticketstartedrev_id, Posisi: $kindposition");

            // 1. Mengambil data JobticketStartedRev
            try {
                $jobticketstartedrev = JobticketStartedRev::with('files')->findOrFail($jobticketstartedrev_id);
                $jobticketstarted = $jobticketstartedrev->jobticketstarted;
                $jobticket = $jobticketstarted->jobticket;
                $jobticketIdentity = $jobticket->jobticketIdentity;
                $jobticketPart = $jobticketIdentity->jobticketPart;
                $projectname = $jobticketPart->projectType->title;
            } catch (\Exception $e) {
                Log::error("Error saat mengambil data JobticketStartedRev: " . $e->getMessage());
                return response()->json(['error' => 'Gagal mengambil data job ticket.'], 500);
            }

            // 2. Mendapatkan nomor WhatsApp drafter dan approver
            try {
                $checkerwaphonenumber = User::findOrFail($jobticketstartedrev->checker_id)->waphonenumber;
                $approverwaphonenumber = User::findOrFail($jobticketstartedrev->approver_id)->waphonenumber;
            } catch (\Exception $e) {
                Log::error("Error saat mengambil nomor WhatsApp drafter atau approver: " . $e->getMessage());
                return response()->json(['error' => 'Gagal mengambil nomor WhatsApp drafter atau approver.'], 500);
            }

            // 3. Mendapatkan dokumen terakhir dari files
            $list = '';
            try {
                $files = $jobticketstartedrev->files;
                if ($files->isNotEmpty()) {
                    $lastFile = $files->last();
                    $list .= "📄 *" . $lastFile->filename . "* ➡️ 🔗 Downloadfile_" . $lastFile->id . "\n";
                }
            } catch (\Exception $e) {
                Log::error("Error saat mengambil dokumen terakhir: " . $e->getMessage());
                return response()->json(['error' => 'Gagal mengambil dokumen terakhir.'], 500);
            }

            // 4. Membuat dan mengirim pesan ke checker atau approver
            try {
                if ($kindposition === "checker") {
                    $message = "👤 *Checker*, Reminder untuk ACC No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname} . Silakan kunjungi:\n" .
                        "🔗 (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Dokumen tersedia di WhatsApp:\n" . $list .
                        "\n🚀 Segera verifikasi! Terima kasih. 🙏😊";

                    Log::info("Mengirim pesan ke checker.");
                    Wagroupnumber::sendWaMessage([$checkerwaphonenumber], $message);
                } elseif ($kindposition === "approver") {
                    $message = "👤 *Approver*, Reminder untuk ACC No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  sudah diterima. Link:\n" .
                        "🔗 (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 (Luar kantor): https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Dokumen tersedia di WhatsApp:\n" . $list .
                        "\n🚀 Segera verifikasi! Terima kasih. 🙏😊";

                    Log::info("Mengirim pesan ke approver.");
                    Wagroupnumber::sendWaMessage([$approverwaphonenumber], $message);
                }
            } catch (\Exception $e) {
                Log::error("Error saat mengirim pesan: " . $e->getMessage());
                return response()->json(['error' => 'Gagal mengirim pesan pengingat.'], 500);
            }

            return response()->json(['success' => 'Pengingat berhasil dikirim.']);
        } catch (\Exception $e) {
            Log::error("Terjadi kesalahan umum saat mengirim pengingat: " . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengirim pengingat.'], 500);
        }
    }





    public function approvebywa(Request $request, $jobticket_identity_part_id, $jobticket_identity_id, $jobticket_id, $jobticketstartedrev_id, $position)
    {
        try {
            $jobticketstartedrev = JobticketStartedRev::with('files')->findOrFail($jobticketstartedrev_id);
            // Menyusun daftar file untuk di-download
            $jobticketstarted = $jobticketstartedrev->jobticketstarted;
            $jobticket = $jobticketstarted->jobticket;
            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;

            $projectname = $jobticketPart->projectType->title;
            $drafterwaphonenumber = User::find($jobticketstartedrev->drafter_id)->waphonenumber;
            $checkerwaphonenumber = User::find($jobticketstartedrev->checker_id)->waphonenumber;
            $approverwaphonenumber = User::find($jobticketstartedrev->approver_id)->waphonenumber;

            $files = $jobticketstartedrev->files;
            $list = '';
            if ($files->isNotEmpty()) {
                $lastFile = $files->last(); // Mengambil dokumen terakhir
                $list .= "📄 *" . $lastFile->filename . "* ➡️ 🔗 Downloadfile_" . $lastFile->id . "\n";
            }


            if ($position == "checker") {
                $jobticketstartedrev->update([
                    'checker_status' => $request->status
                ]);
                if ($request->status == "Approve") {
                    $message = "👤 *Approver*, Silakan melakukan verifikasi No dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}   pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera verifikasi!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$approverwaphonenumber], $message);
                } elseif ($request->status == "Reject") {
                    $jobticketstartedrev->update([
                        'approver_status' => "Reject"
                    ]);

                    $jobticket->izinkanrevisitugasbaru();
                    $message = "👤 *Drafter*, Silakan cek kembali verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah mengalami penolakan oleh checker pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera perbaiki!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);
                }

            } elseif ($position == "approver") {
                $jobticketstartedrev->update([
                    'approver_status' => $request->status
                ]);
                if ($request->status == "Approve") {
                    $jobticket->status = 'closed';
                    $jobticket->save();
                    $message = "👤 *Drafter*, Selamat dokumen anda telah disetujui dengan No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah diterima oleh approver pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera upload di vault!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);
                } elseif ($request->status == "Reject") {
                    $jobticket->izinkanrevisitugasbaru();
                    $message = "👤 *Drafter*, Silakan cek kembali verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  telah mengalami penolakan oleh approver pada link berikut:\n" .
                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "🔗 Kunjungi (Luar kantor) : (https://inka.goovicess.com/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera perbaiki!* Terima kasih atas kerjasamanya! 🙏😊";

                    Wagroupnumber::sendWaMessage([$drafterwaphonenumber], $message);
                }


            }
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function uploadverifikasi($revision)
    {
        return view('jobticket.uploadverifikasi', compact('revision'));
    }

    public function editAndDownloadPDF($kindposition, $pdfPathfile, $imagePathfile, $namefile)
    {
        // Lokasi file PDF dan gambar
        $pdfPath = storage_path($pdfPathfile);
        $imagePath = storage_path($imagePathfile);

        // Periksa apakah file tersedia
        if (!file_exists($pdfPath) || !file_exists($imagePath)) {
            throw new \Exception('File PDF atau gambar tidak ditemukan.');
        }

        // Inisialisasi FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        // Tambahkan halaman pertama
        $pdf->addPage();
        $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

        // Tentukan posisi dan ukuran tanda tangan berdasarkan posisi
        $imageWidth = 17.4;
        $imageHeight = 17.4;
        $imageX = $kindposition === "checker" ? 114.5 : 165.5; // X sesuai checker atau approver
        $imageY = 48;

        // Tambahkan gambar tanda tangan
        $pdf->Image($imagePath, $imageX, $imageY, $imageWidth, $imageHeight);

        $namepdf = str_replace(".pdf", "", $namefile) . "(" . $kindposition . ")" . ".pdf";
        $outputPath = storage_path('app/public/uploads/' . $namepdf);

        // Simpan file
        $pdf->Output('F', $outputPath);

        return [$namepdf, 'public/uploads/' . $namepdf];
    }


    public function revisionapprovedoc(Request $request, $id, $kindposition)
    {
        $userName = auth()->user()->name;
        try {
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

                    // Check if the file with the same name exists and rename it
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . $fileFormat;
                    }

                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    // Save file record to the database
                    $hazardLogFile = new CollectFile();
                    $hazardLogFile->filename = $newFilename;
                    $hazardLogFile->link = str_replace('public/', '', $path);
                    $hazardLogFile->collectable_id = $id;
                    $hazardLogFile->collectable_type = JobticketStartedRev::class;
                    $hazardLogFile->save();
                }
            } elseif ($request->accwithoutupload == 'true' || $request->accautosignature == 'true') {
                // Get the last uploaded file related to this collectable_id
                $lastFile = CollectFile::where('collectable_id', $id)->latest()->first();

                if ($lastFile) {
                    // Duplicate the last file record
                    $duplicatedFile = $lastFile->replicate();


                    if ($request->accautosignature == 'true') {
                        // Lokasi file PDF dan tanda tangan
                        $pdfPathfile = 'app/public/' . $lastFile->link;
                        // Check if user already has a signature (TTD) file
                        $existingFile = CollectFile::where('collectable_id', auth()->user()->id)
                            ->where('collectable_type', User::class)
                            ->first();
                        $imagePathfile = 'app/public/' . $existingFile->link; // Sesuaikan dengan lokasi file tanda tangan

                        $namefile = $duplicatedFile->filename;
                        // Panggil metode untuk menambahkan tanda tangan
                        [$namepdf, $locationpdf] = $this->editAndDownloadPDF($kindposition, $pdfPathfile, $imagePathfile, $namefile);
                        $duplicatedFile->filename = $namepdf;
                        $duplicatedFile->link = str_replace('public/', '', $locationpdf);

                    }


                    $duplicatedFile->created_at = now(); // Update timestamp for the duplicated file
                    $duplicatedFile->save();
                }
            }


            // Fetch jobticket and related data
            $jobticketstartedrev = JobticketStartedRev::findOrFail($id);

            $drafterwaphonenumber = User::find($jobticketstartedrev->drafter_id)->waphonenumber;
            $checkerwaphonenumber = User::find($jobticketstartedrev->checker_id)->waphonenumber;
            $approverwaphonenumber = User::find($jobticketstartedrev->approver_id)->waphonenumber;


            $jobticketstarted = $jobticketstartedrev->jobticketstarted;
            $jobticket = $jobticketstarted->jobticket;
            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;
            $projectname = $jobticketPart->projectType->title;

            // Menyusun daftar file untuk di-download
            $files = $jobticketstartedrev->files;
            $list = '';

            foreach ($files as $file) {
                $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
            }
            $message = "👤 *Checker*, Silakan melakukan verifikasi No dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}   pada link berikut:\n" .
                "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera verifikasi!* Terima kasih atas kerjasamanya! 🙏😊";
            Wagroupnumber::sendWaMessage([$checkerwaphonenumber], $message);
            // Redirect to the jobticket detail route
            return redirect()->route('jobticket.detail', [
                'jobticket_identity_part' => $jobticketPart->id,
                'jobticket_identity_id' => $jobticketIdentity->id,
                'jobticket_id' => $jobticket->id
            ])->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    public function picktugas(Request $request, $id, $name, $kindposition)
    {
        try {
            $jobticket = Jobticket::findOrFail($id);
            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;
            $projectname = $jobticketPart->projectType->title;
            // Menyusun daftar file untuk di-download

            if ($kindposition == "drafter") {
                $jobticket->update([
                    'drafter_id' => auth()->user()->id
                ]);
                $message = "👤 *Manager / Perwakilan*, silakan melakukan verifikasi No Dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}  pada link berikut:\n" .
                    "🔗 Kunjungi : ( http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/ )\n" .
                    "\n🚀 *Ayo segera pilih checker dan approver!* Terima kasih atas kerjasamanya! 🙏😊";


                if ($jobticketIdentity->jobticket_documentkind_id == 6 || $jobticketIdentity->jobticket_documentkind_id == 7) {
                    $approverwaphonenumber = User::find(178)->waphonenumber;
                    Wagroupnumber::sendWaMessage([$approverwaphonenumber], $message);
                } elseif ($jobticketIdentity->jobticket_documentkind_id == 5 || $jobticketIdentity->jobticket_documentkind_id == 8) { //fit & inc
                    $approverwaphonenumber1 = User::find(137)->waphonenumber; //yudho
                    $approverwaphonenumber2 = User::find(139)->waphonenumber; //lucky
                    Wagroupnumber::sendWaMessage([$approverwaphonenumber1, $approverwaphonenumber2], $message);
                } elseif ($jobticketIdentity->jobticketPart->unit_id == 3) { //ees
                    $approverwaphonenumber1 = User::find(27)->waphonenumber; //pak diva
                    $approverwaphonenumber2 = User::find(29)->waphonenumber;  // wella
                    Wagroupnumber::sendWaMessage([$approverwaphonenumber1, $approverwaphonenumber2], $message);
                } elseif ($jobticketIdentity->jobticketPart->unit_id == 2) { //pe
                    $approverwaphonenumber1 = User::find(41)->waphonenumber; // pak ndaru
                    $approverwaphonenumber2 = User::find(153)->waphonenumber;  // mbak cahaya
                    Wagroupnumber::sendWaMessage([$approverwaphonenumber1, $approverwaphonenumber2], $message);
                }



            } elseif ($kindposition == "checker") {
                $jobticket->update([
                    'checker_id' => auth()->user()->id
                ]);
            } elseif ($kindposition == "approver") {
                $jobticket->update([
                    'approver_id' => auth()->user()->id
                ]);
            }

            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function pickdraftercheckerapprover(Request $request, $id)
    {
        try {


            // Ambil data jobticket
            $jobticket = Jobticket::with([
                'jobticketStarted.revisions.files',
                'jobticketIdentity.jobticketPart'
            ])->findOrFail($id);

            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;
            $projectname = $jobticketPart->projectType->title;

            // Update jobticket
            $jobticket->checker_id = $request->checker_id;
            $jobticket->approver_id = $request->approver_id;
            if ($request->has('drafter_id')) {
                $jobticket->drafter_id = $request->drafter_id;
            }
            $jobticket->save();

            // Periksa apakah jobticketStarted ada dan memiliki metode revisions()
            if (isset($jobticket->jobticketStarted) && method_exists($jobticket->jobticketStarted, 'revisions')) {
                // Periksa apakah revisi ada
                if ($jobticket->jobticketStarted->revisions()->exists()) {
                    $jobticketstartedrev = $jobticket->jobticketStarted->revisions()->latest()->first();

                    if ($jobticketstartedrev) {
                        // Pastikan checker_id dan approver_id ada dalam request
                        if ($request->has('checker_id') && $request->has('approver_id')) {
                            $jobticketstartedrev->checker_id = $request->checker_id;
                            $jobticketstartedrev->approver_id = $request->approver_id;

                            // Update drafter_id jika ada di request
                            if ($request->has('drafter_id')) {
                                $jobticketstartedrev->drafter_id = $request->drafter_id;
                            }
                            $jobticketstartedrev->save();

                            // Pastikan checker memiliki nomor WhatsApp
                            $checker = User::find($request->checker_id);
                            if ($checker && !empty($checker->waphonenumber)) {
                                $checkerwaphonenumber = $checker->waphonenumber;

                                // Ambil file terakhir jika ada
                                if ($jobticketstartedrev->files()->exists()) {
                                    $latestFile = $jobticketstartedrev->files()->latest()->first();
                                    $list = $latestFile ? "📄 *{$latestFile->filename}* ➡️ 🔗 Downloadfile_{$latestFile->id}\n" : '';
                                } else {
                                    $list = '';
                                }

                                // Pastikan project name dan jobticket identity tersedia
                                if (!empty($projectname) && isset($jobticket->jobticketIdentity)) {
                                    $message = "👤 *Checker*, Silakan melakukan verifikasi No dokumen : {$jobticket->jobticketIdentity->documentnumber}, " .
                                        "Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname} pada link berikut:\n" .
                                        "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                                        "📂 Berikut dokumen terakhir yang bisa diunduh by WhatsApp:\n" .
                                        $list . "\n🚀 *Ayo segera verifikasi!* Terima kasih atas kerjasamanya! 🙏😊";

                                    Wagroupnumber::sendWaMessage([$checkerwaphonenumber], $message);
                                }
                            }
                        }
                    }
                }
            }



            // // Siapkan respon sukses
            $response = [
                'checker_name' => User::find($request->checker_id)->name,
                'approver_name' => User::find($request->approver_id)->name,
            ];
            if ($request->has('drafter_id')) {
                $response['drafter_name'] = User::find($request->drafter_id)->name;
            }

            return response()->json($response, 200);

        } catch (\Throwable $e) {
            // Tangani error dan log jika perlu
            \Log::error("Error in pickdraftercheckerapprover: {$e->getMessage()}");

            return response()->json([
                'message' => 'Gagal memproses permintaan. Silakan coba lagi atau hubungi administrator.',
            ], 500);
        }
    }




    public function jobticketstartedrevpickdraftercheckerapprover(Request $request, $id)
    {
        $jobticketstartedrev = JobticketStartedRev::with(['jobticketstarted.jobticket.jobticketIdentity.jobticketPart'])
            ->findOrFail($id);

        // Update checker dan approver
        $jobticketstartedrev->checker_id = $request->checker_id;
        $jobticketstartedrev->approver_id = $request->approver_id;
        // Save the changes to the database
        $jobticketstartedrev->save();



        // Cek apakah terdapat revisi
        $notif = $files = $jobticketstartedrev->files->count() > 0 ? "yes" : "no";
        if ($notif == 'yes') {
            // Fetch jobticket and related data
            // Ambil revisi terbaru dari jobticket
            $jobticketstarted = $jobticketstartedrev->jobticketstarted;
            $jobticket = $jobticketstarted->jobticket;
            $jobticketIdentity = $jobticket->jobticketIdentity;
            $jobticketPart = $jobticketIdentity->jobticketPart;

            $projectname = $jobticketPart->projectType->title;


            $checkerwaphonenumber = User::find($jobticketstartedrev->checker_id)->waphonenumber;

            // Menyusun daftar file untuk di-download
            $files = $jobticketstartedrev->files;
            $list = '';

            foreach ($files as $file) {
                $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
            }
            $message = "👤 *Checker*, Silakan melakukan verifikasi No dokumen : {$jobticketIdentity->documentnumber}, Nama Dokumen : {$jobticket->documentname}, Rev : {$jobticket->rev} , Proyek : {$projectname}   pada link berikut:\n" .
                "🔗 Kunjungi : (http://192.168.13.160:8000/jobticket/show/{$jobticketPart->id}/{$jobticketIdentity->id}/{$jobticket->id})\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera verifikasi!* Terima kasih atas kerjasamanya! 🙏😊";
            Wagroupnumber::sendWaMessage([$checkerwaphonenumber], $message);
            // Redirect to the jobticket detail route
            return redirect()->route('jobticket.detail', [
                'jobticket_identity_part' => $jobticketPart->id,
                'jobticket_identity_id' => $jobticketIdentity->id,
                'jobticket_id' => $jobticket->id
            ])->with('success', 'Dokumen berhasil diperbarui.');

        }



        // // Update the jobticket information with the new checker and approver IDs
        $jobticketstartedrev->jobticketstarted->jobticket->update([
            'checker_id' => $request->checker_id,
            'approver_id' => $request->approver_id,
        ]);

        return response()->json(['message' => 'Checker and Approver updated successfully.']);
    }


    public function picknote(Request $request, $id)
    {
        try {
            $jobticket = Jobticket::findOrFail($id);
            $jobticket->update([
                'note' => $request->note
            ]);

            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function starttugas(Request $request, $id, $name)
    {
        try {
            $jobticket = Jobticket::findOrFail($id);
            $jobticket->starttugasbaru();
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function pausetugas(Request $request, $id, $name)
    {
        $jobticket = Jobticket::findOrFail($id);
        $result = $jobticket->pausetugasbaru();

        $kind_type = "";
        if ($request->kind == "memo") {
            $kind_type = NewMemo::class;
            $kind_id = $request->kind_id;
            $reason = null;
        } else {
            $kind_type = null;
            $kind_id = null;
            $reason = $request->reason;
        }

        // Buat job ticket reason
        $jobticketreason = Jobticketreason::create([
            'jobticket_id' => $request->jobticket_id,
            'reason' => $reason,
            'kind' => $request->kind,
            'kind_id' => $kind_id,
            'kind_type' => $kind_type,
            'start' => now(), // Menambahkan start dengan nilai timestamp saat ini
        ]);

        // Ambil user yang login
        $userName = auth()->user()->name;

        // Periksa jika ada file yang diupload
        if ($request->hasFile('file')) {
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
                $jobticketreasonFile = new CollectFile();
                $jobticketreasonFile->filename = $newFilename;
                $jobticketreasonFile->link = str_replace('public/', '', $path); // Hapus 'public/' dari path
                $jobticketreasonFile->collectable_id = $jobticketreason->id;
                $jobticketreasonFile->collectable_type = Jobticketreason::class;
                $jobticketreasonFile->save();
            }
        }



        // Return a success response
        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

    public function resumetugas(Request $request, $id, $name)
    {
        try {
            $jobticket = Jobticket::with('reasons')->findOrFail($id);
            $reason = $jobticket->reasons->last();

            if ($reason) {
                $reason->update(['end' => now()]);
            }


            $response = $jobticket->resumetugasbaru();
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function selesaitugas(Request $request, $id, $name)
    {
        try {
            $userName = auth()->user()->name;
            $jobticket = Jobticket::findOrFail($id);

            // Memperoleh respons dari selesaitugasbaru
            $response = $jobticket->selesaitugasbaru();

            // Cek apakah ada error pada response dari selesaitugasbaru
            if (isset($response['error'])) {
                return response()->json($response);
            }

            // Jika ada file yang diunggah, proses unggahan file
            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $filename = $uploadedFile->getClientOriginalName();
                $fileFormat = $uploadedFile->getClientOriginalExtension();
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                $filename = $filenameWithUserAndFormat;

                $count = 0;
                $newFilename = $filename;

                // Check if the file with the same name exists and rename it
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . $fileFormat;
                }

                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Save file record to the database
                $JobticketFile = new CollectFile();
                $JobticketFile->filename = $newFilename;
                $JobticketFile->link = str_replace('public/', '', $path);
                $JobticketFile->collectable_id = $response['newRevisionId'];
                $JobticketFile->collectable_type = JobticketStartedRev::class;
                $JobticketFile->save();
            }

            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function izinkanrevisitugas(Request $request, $id, $name)
    {
        try {
            $jobticket = Jobticket::findOrFail($id);
            $response = $jobticket->izinkanrevisitugasbaru();
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function deleteRevision($id)
    {
        // Cari revisi berdasarkan ID
        $revision = JobticketStartedRev::findOrFail($id);

        // Ambil semua file terkait dengan revisi ini
        $files = $revision->files;

        // Hapus file dari storage dan database
        foreach ($files as $file) {
            try {
                // Hapus file fisik dari penyimpanan
                Storage::delete($file->link);
            } catch (\Throwable $th) {
                //throw $th;
            }
            // Hapus file dari database
            $file->delete();
        }

        // Hapus revisi dari database
        $revision->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Revisi dan file berhasil dihapus.');
    }

    public function deletejobticket($idjobticketpart, $idjobticketidentity, $idjobticket)
    {
        // Cari jobticket berdasarkan ID
        $jobticket = Jobticket::findOrFail($idjobticket);
        // Fetch related JobticketStarted and its revisions
        $jobticketStarted = $jobticket->jobticketStarted()->with('revisions.files')->first();
        if ($jobticketStarted) {
            $jobticketStartedrev = $jobticketStarted->revisions;

            if ($jobticketStartedrev) {
                foreach ($jobticketStartedrev as $revision) {
                    // Ambil semua file terkait dengan revisi ini
                    $files = $revision->files;

                    // Hapus file dari storage dan database
                    foreach ($files as $file) {
                        try {
                            // Hapus file fisik dari penyimpanan
                            Storage::delete($file->link);
                            // Hapus file dari database
                            $file->delete();
                        } catch (\Throwable $th) {
                            // Tangani error dengan log atau beri pesan
                            \Log::error('Failed to delete file: ' . $file->link, ['error' => $th->getMessage()]);
                        }
                    }

                    // Hapus revisi dari database
                    $revision->delete();
                }
            }
        }


        // Hapus jobticket dari database
        $jobticket->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jobticket beserta turunannya berhasil dihapus.');
    }

    public function updateInfoHari()
    {
        $printjson = [];
        $jobticketHistories = []; // Array untuk menampung data JobticketHistory yang akan diinsert

        // Ambil data dari tabel newprogressreports yang dibuat atau diperbarui hari ini
        $newprogressreports = Newprogressreport::with('newprogressreporthistory') // Eager load history
            ->whereDate('updated_at', now()->toDateString())->get();

        // Iterasi melalui setiap newprogressreport
        foreach ($newprogressreports as $newprogressreport) {
            // Konversi ID newprogressreport ke string
            $newprogressreportId = (string) $newprogressreport->id;

            // Ambil jobtickets yang terkait dengan newprogressreport_id
            $jobticketidentitys = JobticketIdentity::whereJsonContains('newprogressreportids', $newprogressreportId)->get();

            if ($jobticketidentitys->isEmpty()) {
                // Simpan pesan kesalahan untuk ID yang tidak ditemukan
                $printjson['error'][] = "No jobtickets found for newprogressreport ID: " . $newprogressreportId;
            } else {
                // Ambil revisi terbaru dari relasi newprogressreporthistory
                $lastRevision = $newprogressreport->newprogressreporthistory()->latest()->first();

                if ($lastRevision) {
                    foreach ($jobticketidentitys as $jobticketidentity) {
                        // Cek apakah entri JobticketHistory sudah ada
                        $existingHistory = JobticketHistory::where('jobticket_identity_id', $jobticketidentity->id)
                            ->where('newprogressreporthistory_id', $lastRevision->id)
                            ->where('newprogressreport_id', $newprogressreport->id)
                            ->exists();

                        if (!$existingHistory) {
                            // Tambahkan data ke array untuk batch insert
                            $jobticketHistories[] = [
                                'historykind' => "newdocumentrev",
                                'jobticket_identity_id' => $jobticketidentity->id,
                                'newprogressreporthistory_id' => $lastRevision->id,
                                'newprogressreport_id' => $newprogressreport->id,
                                'description' => "Dokumen telah terupdate",
                                'status' => "unread",
                                'created_at' => now(), // Tambahkan created_at
                                'updated_at' => now(), // Tambahkan updated_at
                            ];
                        }

                        // Simpan hasil JSON ke dalam array untuk respon
                        $printjson[$jobticketidentity->documentname] = json_encode($lastRevision->rev);
                    }
                } else {
                    // Jika tidak ada revisi
                    foreach ($jobticketidentitys as $jobticketidentity) {
                        $printjson[$jobticketidentity->documentname] = "null for ID: " . $newprogressreportId;
                    }
                }
            }
        }

        // Batch insert ke JobticketHistory jika ada data yang perlu disimpan
        if (!empty($jobticketHistories)) {
            JobticketHistory::insert($jobticketHistories);
        }

        // Kembalikan respon dalam format JSON
        return response()->json($printjson);
    }

    public function ujiuji()
    {
        // Path file PDF dan gambar
        $pdfPath = storage_path('app/public/files/original.pdf');
        $imagePath = storage_path('app/public/images/logo.png');
        $outputPath = storage_path('app/public/hasilstamp/modified.pdf');

        // Pastikan direktori output ada
        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Periksa keberadaan file PDF asli
        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'File PDF asli tidak ditemukan.'], 404);
        }

        // Periksa keberadaan file gambar
        if (!file_exists($imagePath)) {
            return response()->json(['error' => 'File gambar tidak ditemukan.'], 404);
        }

        // Membuat instance FPDI
        $pdf = new Fpdi();

        // Memuat file PDF asli
        $pageCount = $pdf->setSourceFile($pdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            // Tambahkan halaman baru sesuai template
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Tambahkan gambar ke setiap halaman (sesuaikan posisi dan ukuran)
            $pdf->Image($imagePath, 10, 10, 50); // (path, x, y, width)
        }

        // Simpan PDF hasil modifikasi
        $pdf->Output($outputPath, 'F');

        // Kembalikan file hasil modifikasi untuk diunduh
        return response()->download($outputPath);
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
        $jobticketidentity = JobticketIdentity::where('documentnumber', 'LIKE', '%' . $query . '%')
            ->orWhereHas('jobtickets', function ($queryBuilder) use ($query) {
                $queryBuilder->where('documentname', 'LIKE', '%' . $query . '%');
            })
            ->first();

        if (!$jobticketidentity) {
            return response("⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*")
                ->header('Content-Type', 'text/plain');
        }

        // Ambil jobticket terkait
        $jobticket = $jobticketidentity->jobtickets->last();
        if (!$jobticket) {
            return response("⚠️ Tidak ada job ticket yang ditemukan untuk pencarian: *" . $query . "*")
                ->header('Content-Type', 'text/plain');
        }

        // Ambil revisi terakhir
        $revision = $jobticket->jobticketStarted->revisions->last();
        if (!$revision) {
            return response("⚠️ Tidak ada revisi yang ditemukan untuk pencarian: *" . $query . "*")
                ->header('Content-Type', 'text/plain');
        }

        // Ambil file terakhir dari revisi terakhir
        $lastFile = $revision->files->last();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";

        // Tambahkan header hasil pencarian
        $latestUpdate = $jobticketidentity->updated_at ? $jobticketidentity->updated_at->format('d/m/Y') : "Tidak ada tanggal";
        $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";

        // Tambahkan detail dokumen
        $textResult .= "📄 *Nomor Dokumen*: " . $jobticketidentity->documentnumber . "\n";
        $textResult .= "📋 *Nama Dokumen*: " . ($jobticket->documentname ?? 'Tidak tersedia') . "\n";
        $textResult .= "📄 *Revisi Terakhir*: " . $jobticket->rev . "\n";

        // Tambahkan detail file terakhir
        if ($lastFile) {
            $textResult .= "📁 *File dari Revisi Terakhir:*\n";
            $textResult .= "- ID: " . $lastFile->id . "\n";
            $textResult .= "- Nama File: " . $lastFile->filename . "\n\n";
            $textResult .= "📂 *Unduh dengan instruksi:* `Downloadfile_" . $lastFile->id . "`\n\n";
        } else {
            $textResult .= "📁 *Tidak ada file pada revisi terakhir.*\n\n";
        }

        $textResult .= "----------------------------------\n\n";

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }



    public function downloadjobticket(Request $request)
    {
        $unit_name = $request->input('unit_name', 'Quality Engineering'); // Default "Quality Engineering"
        $documentkind = $request->input('documentkind', 'all'); // Default "all"
        $proyek_name = $request->input('proyek_name', 'all'); // Default "all"
        $files = [];

        // Ambil documentkind jika tidak 'all'
        $jobticketdocumentkind = $documentkind !== 'all'
            ? JobticketDocumentKind::where('name', $documentkind)->first()
            : null;

        // Jika proyek_name bukan 'all', ambil data proyek
        $project = $proyek_name !== 'all'
            ? ProjectType::where('title', $proyek_name)->first()
            : null;

        // Temukan unit berdasarkan nama
        $unit = Unit::where('name', $unit_name)->first();

        // Validasi data proyek dan unit jika diperlukan
        if ($proyek_name !== 'all' && !$project) {
            return response()->json(['error' => 'Proyek tidak ditemukan'], 404);
        }
        if (!$unit) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        // Ambil semua bagian jobticket sesuai proyek dan unit
        $jobticketparts = JobticketPart::query()
            ->when($proyek_name !== 'all', function ($query) use ($project) {
                $query->where('proyek_type_id', $project->id);
            })
            ->where('unit_id', $unit->id)
            ->when($jobticketdocumentkind, function ($query) use ($jobticketdocumentkind) {
                // Tambahkan filter untuk jobticketidentity_documentkind_id jika documentkind tertentu
                $query->whereHas('jobticketidentitys', function ($subQuery) use ($jobticketdocumentkind) {
                    $subQuery->where('jobticket_documentkind_id', $jobticketdocumentkind->id);
                });
            })
            ->with([
                'jobticketidentitys.jobtickets' => function ($query) {
                    $query->where('status', 'closed'); // Filter status "closed"
                },
                'jobticketidentitys.jobtickets.jobticketStarted.revisions.files' => function ($query) {
                    $query->latest();
                }
            ])
            ->get();

        // Iterasi jobticketparts untuk mengumpulkan file terakhir
        foreach ($jobticketparts as $jobticketpart) {
            foreach ($jobticketpart->jobticketidentitys as $jobticketidentity) {
                $lastJobticket = $jobticketidentity->jobtickets->last();
                if ($lastJobticket) {
                    $lastRevision = $lastJobticket->jobticketStarted->revisions->last();
                    if ($lastRevision && $lastRevision->files->last()) {
                        $fileLink = $lastRevision->files->last();
                        $files[] = storage_path('app/public/' . $fileLink->link); // Path file
                    }
                }
            }
        }

        // Return ZIP sebagai response
        if (!empty($files)) {
            $zipFileName = 'jobticket_files_' . $unit_name . '_' . $documentkind . '_' . $proyek_name . '_' . date('Y-m-d_H-i-s') . '.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        $zip->addFile($file, basename($file));
                    }
                }
                $zip->close();
            } else {
                return response()->json(['error' => 'Failed to create ZIP file'], 500);
            }

            // Kirim file sebagai response
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
    }



    public function downloadexcel(Request $request)
    {
        $unit_name = $request->input('unit_name', 'Quality Engineering');
        $documentkind = $request->input('documentkind', 'all');
        $proyek_name = $request->input('proyek_name', 'all');

        $jobticketdocumentkind = $documentkind !== 'all'
            ? JobticketDocumentKind::where('name', $documentkind)->first()
            : null;

        $project = $proyek_name !== 'all'
            ? ProjectType::where('title', $proyek_name)->first()
            : null;

        $unit = Unit::where('name', $unit_name)->first();

        if ($proyek_name !== 'all' && !$project) {
            return response()->json(['error' => 'Proyek tidak ditemukan'], 404);
        }
        if (!$unit) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        $jobticketparts = JobticketPart::query()
            ->when($proyek_name !== 'all', function ($query) use ($project) {
                $query->where('proyek_type_id', $project->id);
            })
            ->where('unit_id', $unit->id)
            ->when($jobticketdocumentkind, function ($query) use ($jobticketdocumentkind) {
                $query->whereHas('jobticketidentitys', function ($subQuery) use ($jobticketdocumentkind) {
                    $subQuery->where('jobticket_documentkind_id', $jobticketdocumentkind->id);
                });
            })
            ->with([
                'jobticketidentitys.jobtickets' => function ($query) {
                    $query->where('status', 'closed');
                },
                'jobticketidentitys.jobtickets.jobticketStarted.revisions.files' => function ($query) {
                    $query->latest();
                }
            ])
            ->get();

        $data = [];

        foreach ($jobticketparts as $jobticketpart) {
            foreach ($jobticketpart->jobticketidentitys as $jobticketidentity) {
                foreach ($jobticketidentity->jobtickets as $jobticket) {
                    $data[] = [
                        'Proyek' => $project ? $project->title : 'All',
                        'Unit' => $unit->name,
                        'No Dokumen' => $jobticketidentity->documentnumber,
                        'Jenis Dokumen' => $jobticketdocumentkind ? $jobticketdocumentkind->name : 'All',
                        'Status' => $jobticket->status,
                        'Tanggal Dibuat' => $jobticket->created_at,
                    ];
                }
            }
        }

        if (empty($data)) {
            return response()->json(['error' => 'Tidak ada data untuk diekspor'], 404);
        }

        return Excel::download(new JobticketExport($data), 'Jobticket_Report.xlsx');
    }




    public function updateDocumentName(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'documentname' => 'required|string|max:255',
        ]);

        // Temukan JobticketIdentity berdasarkan ID
        $jobticketidentity = JobticketIdentity::find($id);

        // Periksa apakah data ditemukan
        if (!$jobticketidentity) {
            return redirect()->back()->with('error', 'Jobticket Identity tidak ditemukan.');
        }

        // Ambil semua jobtickets terkait dan perbarui document_name
        foreach ($jobticketidentity->jobtickets as $jobticket) {
            $jobticket->documentname = $request->input('documentname');
            $jobticket->save(); // Simpan perubahan
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Nama dokumen berhasil diperbarui.');
    }














}
