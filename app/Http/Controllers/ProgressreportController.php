<?php

namespace App\Http\Controllers;

use App\Models\NewMemo;
use Carbon\Carbon;
use App\Models\Log;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\ColumnAImport;
use App\Models\Progressreport;
use Illuminate\Routing\Controller;
use App\Exports\ProgressItemExport;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgressreportsImport;

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProgressreportsTreediagramImport;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgressreportController extends Controller
{
    protected $fileController;
    protected $logController;

    public function __construct(FileController $fileController,LogController $logController)
    {
        $this->fileController = $fileController;
        $this->logController = $logController;
    }

    public function importExcel(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $jenisupload = $request->jenisupload;

        if ($jenisupload == "format") {
            // Handle format upload
            $this->handleFormatUpload($request);
        } elseif ($jenisupload == "Treediagram") {
            // Handle Treediagram upload
            $this->handleTreediagramUpload($request);
        }

        return redirect()->route('progressreport.index');
    }

    private function handleFormatUpload(Request $request)
    {
        // Get the file from the request
        $file = $request->file('file');

        // Import data using ProgressreportsImport
        $import = new ProgressreportsImport();
        $importedData = Excel::toCollection($import, $file)->first();

        // Process imported data
        $revisiData = $import->collection($importedData);
        $progressreport = Progressreport::where('progressreportname', $request->progressreportname)
            ->where('proyek_type', $request->proyek_type)
            ->first();

        if (isset($progressreport)) {
            $revisiJson = json_decode($progressreport->revisi, true) ?? [];
            foreach ($revisiData as $index => $itemget) {
                $revisiJson[$index]=$revisiData[$index];
            }
            $progressreport->update([
                'revisi' => $revisiJson
                
            ]);
        } else {
            // Create new progress report
            $revisiJson = json_encode($revisiData);
            $progressreport = Progressreport::create([
                'progressreportname' => $request->progressreportname,
                'proyek_type' => $request->proyek_type,
                'revisi' => $revisiJson,
                'status' => "terbuka"
            ]);
        }

        // Log the action
        $this->logController->updatelog($progressreport->id, 'Data Excel berhasil diimpor', 'Penambahan data', auth()->user()->name, 'Progressreport');
    }

    private function handleTreediagramUpload(Request $request)
    {
        // Get the file from the request
        $file = $request->file('file');

        // Import data using ProgressreportsTreediagramImport
        $import = new ProgressreportsTreediagramImport();
        $importedData = Excel::toCollection($import, $file)->first();

        // Process imported data
        $nilaididapat = $import->collection($importedData);
        $groupproject = [];

        foreach ($nilaididapat as $index => $item) {
            $groupproject[$item['nodokumen']]['deadlinerelease'] = $item['deadlinerelease'];
        }

        // Update progress reports
        $progressreports = Progressreport::orderBy('created_at', 'desc')->get();

        foreach ($progressreports as $progressreport) {
            $nilai = 0;

            if (isset($progressreport)) {
                $revisiData = json_decode($progressreport->revisi, true) ?? [];

                foreach ($revisiData as $index => $item) {
                    if (isset($groupproject[$item['nodokumen']]['deadlinerelease'])) {
                        $revisiData[$index]['deadlinerelease'] = $groupproject[$item['nodokumen']]['deadlinerelease'];
                        $nilai++;
                    }
                }

                if ($nilai != 0) {
                    $progressreport->revisi = json_encode($revisiData);
                    $progressreport->save();
                }
            }
        }
    }

    public function showUploadForm()
    {
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        $unit_for_progres_dokumen = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        return view('progressreport.uploadprogressreport', compact('categoryproject','unit_for_progres_dokumen'));
    }

    public function progressreportoneshow($progressreport){
        $revisi = json_decode($progressreport->revisi, true);
        $documentrelease=0;
        foreach($revisi as $index => $item){
            $statuscondition = $item['status'];
            if($statuscondition=="RELEASED"){
                $documentrelease+=1;
            }
        }
        if(count($revisi)!=0){
            $seniorpercentage=$documentrelease/count($revisi)*100;
        }else{
            $seniorpercentage=0;
        }
        
        return $seniorpercentage;
    }

    public function lastdateinmonth($month, $year) {
        return date('Y-m-t', strtotime("$year-$month-01"));
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

    public function show($id)
    {
        $progressreport = Progressreport::findOrFail($id);
        $documents = NewMemo::all();
        $seniorpercentage = $this->progressreportoneshow($progressreport);
        $logs = Log::all()->filter(function ($log) use ($progressreport) {
            return json_decode($log->message)->id == $progressreport->id;
        });
        $revisi = json_decode($progressreport->revisi, true);
        $listanggota = $this->getColumnA();

        // Initialize the data structures
        $datalevel = [
            'all' => [
                'Predesign' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Intermediate Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Final Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Belum Diidentifikasi' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
            ]
        ];
        $datastatus = [
            'all' => ["Working Progress" => 0, "RELEASED" => 0, "Belum Dimulai" => 0]
        ];

        // Process each revision
        foreach ($revisi as $item) {
            $level = isset($item['level']) && !empty($item['level']) ? $item['level'] : 'Belum Diidentifikasi';
            $status = isset($item['status']) && !empty($item['status']) ? $item['status'] : 'Belum Dimulai';
            $drafter = isset($item['drafter']) && !empty($item['drafter']) ? str_replace(' ', '_', $item['drafter']) : 'unknown';

            // Initialize drafter-specific data if not already initialized
            if (!isset($datalevel[$drafter])) {
                $datalevel[$drafter] = [
                    'Predesign' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Intermediate Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Final Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Belum Diidentifikasi' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                ];
            }
            if (!isset($datastatus[$drafter])) {
                $datastatus[$drafter] = ["Working Progress" => 0, "RELEASED" => 0, "Belum Dimulai" => 0];
            }

            // Ensure valid level and status keys for drafter-specific data
            if (!isset($datalevel[$drafter][$level])) {
                $datalevel[$drafter][$level] = ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0];
            }
            if (!isset($datalevel[$drafter][$level][$status])) {
                $datalevel[$drafter][$level][$status] = 0;
            }

            // Update drafter-specific data
            $datalevel[$drafter][$level][$status]++;
            $datastatus[$drafter]["Working Progress"]++;

            if ($status == "RELEASED") {
                $datastatus[$drafter]["RELEASED"]++;
                $datastatus[$drafter]["Working Progress"]--;
            } elseif ($status != "RELEASED" && $status != "Working Progress") {
                $datastatus[$drafter]["Belum Dimulai"]++;
                $datastatus[$drafter]["Working Progress"]--;
            }

            // Ensure valid level and status keys for overall data
            if (!isset($datalevel['all'][$level])) {
                $datalevel['all'][$level] = ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0];
            }
            if (!isset($datalevel['all'][$level][$status])) {
                $datalevel['all'][$level][$status] = 0;
            }

            // Update overall data
            $datalevel['all'][$level][$status]++;
            $datastatus['all']["Working Progress"]++;

            if ($status == "RELEASED") {
                $datastatus['all']["RELEASED"]++;
                $datastatus['all']["Working Progress"]--;
            } elseif ($status != "RELEASED" && $status != "Working Progress") {
                $datastatus['all']["Belum Dimulai"]++;
                $datastatus['all']["Working Progress"]--;
            }
        }

        // Calculate percentage for each status in each level for all drafters
        $percentageLevel = [];
        $percentageStatus = [];

        foreach ($datalevel as $drafter => $levels) {
            foreach ($levels as $level => $statuses) {
                $total = array_sum($statuses);
                $percentageLevel[$drafter][$level] = array_map(function ($count) use ($total) {
                    return $total ? ($count / $total) * 100 : 0;
                }, $statuses);
            }

            // Calculate total counts for status
            $totalStatus = array_sum($datastatus[$drafter]);
            $percentageStatus[$drafter] = $totalStatus ? array_map(function ($count) use ($totalStatus) {
                return ($count / $totalStatus) * 100;
            }, $datastatus[$drafter]) : $datastatus[$drafter];
        }

        return view('progressreport.show', compact('progressreport', 'logs', 'seniorpercentage', 'listanggota', 'datalevel', 'percentageLevel', 'datastatus', 'percentageStatus'));
    }

    public function showJsonProgressreport($id)
    {
        $progressreport = Progressreport::findOrFail($id);
        return response()->json($progressreport);
    }

    public function index()
    {
        $progressreports = Progressreport::orderBy('created_at', 'desc')->get();
        $documents = NewMemo::all();
        $groupprogressreportnamepercentage=[];
        foreach($progressreports as $progressreport){
            $seniorpercentage=$this->progressreportoneshow($progressreport);
            $groupprogressreportnamepercentage[$progressreport->progressreportname]=$seniorpercentage;
        }
        // Ambil kategori unit under pe
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Hapus tanda kutip ganda tambahan
        $allunitunderpe = json_decode($categoryproject, true);
    
        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        $categoryprojectbaru = json_decode($categoryproject, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
        $listproject = json_decode($categoryproject, true);
        $revisiall = [];
        $revisiall["All"] = "";
        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $revisiall[$key]['progressreports'] = collect($progressreports)->where('proyek_type', $listproject[$i])->all();
        }
        unset($revisiall['All']);
        return view('progressreport.index', compact('revisiall', 'allunitunderpe', 'unitsingkatan','progressreports','groupprogressreportnamepercentage'));
    }

    public function updateprogressreport(Request $request, $id, $namadokumen){
        try {
            
            $nodokumen = $request->input('nodokumen')??"";
            $namadokumen = $request->input('namadokumen')??"";
            $level = $request->input('level')??"";
            $drafter = $request->input('drafter')??"";
            $checker = $request->input('checker')??"";
            $deadlinerelease = $request->input('deadlinerelease')??"";
            $realisasi = $request->input('realisasi')??"";
            $status = $request->input('status')??"";
            
            $progressreport = Progressreport::findOrFail($id);
            $revisiData = $progressreport->revisiData();
            [$index,$nodokumen]= $progressreport->findDocument($revisiData, $namadokumen);
            if (!isset($revisiData[$index])) {
                return response()->json(['error' => 'Index tidak valid'], 422);
            }
            
            $nodokumensebelum = $revisiData[$index]['nodokumen'];
            $namadokumensebelum = $revisiData[$index]['namadokumen'];
            $levelsebelum = $revisiData[$index]['level']??"";
            $draftersebelum = $revisiData[$index]['drafter']??"";
            $checkersebelum = $revisiData[$index]['checker']??"";
            $deadlinereleasesevelum = $revisiData[$index]['deadlinerelease']??"";
            $realisasisebelum = $revisiData[$index]['realisasi']??"";
            $statussebelum = $revisiData[$index]['status']??"";

            $revisiData[$index]['nodokumen'] = $nodokumen;
            $revisiData[$index]['namadokumen'] = $namadokumen;
            $revisiData[$index]['level'] = $level;
            $revisiData[$index]['drafter'] = $drafter;
            $revisiData[$index]['checker'] = $checker;
            $revisiData[$index]['deadlinerelease'] = $deadlinerelease;
            $revisiData[$index]['realisasi'] = $realisasi;
            $revisiData[$index]['status'] = $status;

            $progressreport->revisi = json_encode($revisiData);
            $progressreport->save();

            $pesan = 'Perubahan dokumen. No Dokumen: ' . $nodokumensebelum . ' -> ' . $nodokumen . ', Nama Dokumen: ' . $namadokumensebelum . ' -> ' . $namadokumen . ', Drafter: ' . $draftersebelum . ' -> '. ', Checker: ' . $checkersebelum . ' -> ' . $checker . ', Deadline Release: ' . $deadlinereleasesevelum . ' -> ' . $deadlinerelease . ', Realisasi: ' . $realisasisebelum . ' -> ' . $realisasi . ', Status: ' . $statussebelum . ' -> ' . $status;

            // Panggil fungsi untuk memperbarui log
            $this->logController->updatelog($progressreport->id, $pesan, 'Perubahan dokumen', auth()->user()->name, 'Progressreport');

            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function tambahprogress(Request $request, $id)
    {
        try {
            $nodokumen = $request->input('nodokumen') ?? "";
            $namadokumen = $request->input('namadokumen') ?? "";
            $level = $request->input('level') ?? "";
            $drafter = $request->input('drafter') ?? "";
            $checker = $request->input('checker') ?? "";
            $deadlinerelease = $request->input('deadlinerelease') ?? "";
            $realisasi = $request->input('realisasi') ?? "";
            $status = $request->input('status') ?? "";

            $progressreport = Progressreport::findOrFail($id);
            $revisiData = json_decode($progressreport->revisi, true);
            $data = [
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
                'level' => $level,
                'drafter' => $drafter,
                'checker' => $checker,
                'deadlinerelease' => $deadlinerelease,
                'realisasi' => $realisasi,
                'status' => $status
            ];
            $revisiData[$nodokumen] = $data;
            $progressreport->revisi = json_encode($revisiData);
            $progressreport->save();

            $pesan = 'Penambahan dokumen. No Dokumen: ' . $nodokumen . ', Nama Dokumen: ' . $namadokumen . ', Drafter: ' . $drafter . ', Deadline Release: ' . $deadlinerelease . ', Realisasi: ' . $realisasi . ', Status: ' . $status;
            $this->logController->updatelog($progressreport->id, $pesan, 'Penambahan dokumen', auth()->user()->name, 'Progressreport');

            return response()->json(['success' => 'Data berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id, $namadokumen)
    {
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $progressreport = Progressreport::findOrFail($id);
            
            // Decode JSON fields from the Progressreport model
            $revisiData = $progressreport->revisiData();
            [$index, $nodokumen] = $progressreport->findDocument($revisiData, $namadokumen);
            
            // Check if the key exists in the array
            if (array_key_exists($index, $revisiData)) {
                // Simpan data yang akan dihapus untuk keperluan log
                $deletedData = $revisiData[$index];

                // Hapus data dari array revisi berdasarkan kunci
                unset($revisiData[$index]);

                // Update nilai revisi pada model Progressreport
                $progressreport->revisi = json_encode($revisiData);
                $progressreport->save();

                // Buat pesan log untuk penghapusan dokumen
                $pesan = 'Penghapusan dokumen. Kode Material: ' . $deletedData['kodematerial'] . ', Material: ' . $deletedData['material'] . ', Status: ' . $deletedData['status'];

                // Panggil fungsi untuk memperbarui log
                $this->logController->updatelog($progressreport->id, $pesan, 'Penghapusan dokumen', auth()->user()->name, 'Progressreport');

                return back()->with('success', 'Status dokumen berhasil diperbarui.');
            } else {
                return back()->with('error', 'Dokumen tidak ditemukan.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }


    public function resettugas(Request $request, $id, $namadokumen, $name)
    {
        // Retrieve the Progressreport model instance
        $progressreport = Progressreport::findOrFail($id);

        // Call the resetDocument method on the Progressreport model
        $result = $progressreport->resetDocument($namadokumen);

        // If document not found, return an error response
        if (!$result) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Return a success response
        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

    public function detailtugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $response = $progressreport->detailtugasDocument($namadokumen, $name);

            if (isset($response['error'])) {
                return response()->json(['error' => $response['error']], 404);
            }

            // Pass the data to the view
            return view('progressreport.detailtugas', [
                'documentName' => $response['documentName'],
                'revisions' => $response['revisions']
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function picktugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $progressreport->picktugasDocument($namadokumen, $name);
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function starttugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $progressreport->updateDocument($namadokumen, $name);
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function pausetugas(Request $request, $id, $namadokumen, $name)
    {
        // Retrieve the Progressreport model instance
        $progressreport = Progressreport::findOrFail($id);

        // Call the pauseDocument method on the Progressreport model
        $result = $progressreport->pauseDocument($namadokumen);

        // If document not found, return an error response
        if (!$result) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Return a success response
        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

    public function resumetugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $response = $progressreport->resumeTask($namadokumen);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function selesaitugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $response = $progressreport->selesaiTask($namadokumen);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function izinkanrevisitugas(Request $request, $id, $namadokumen, $name)
    {
        try {
            $progressreport = Progressreport::findOrFail($id);
            $response = $progressreport->izinkanrevisiTask($namadokumen);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }
    public function deleteMultiple(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen yang akan dihapus dari input form
        if (!empty($progressreportIds)) {
            Progressreport::whereIn('id', $progressreportIds)->delete(); // Hapus dokumen yang dipilih
            foreach ($progressreportIds as $progressreportid) {
                $pesan = 'Progressreport: ' . $progressreportid . ' berhasil dihapus.';
                $this->logController->updatelog($progressreportid, $pesan, 'Penghapusan Progressreport', auth()->user()->name, 'Progressreport');
            }
            return redirect()->back()->with('success', 'Dokumen yang dipilih berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dipilih untuk dihapus');
        }

    }

    public function ujicobaprogressspreadsheet($id)
    {
        $progressreport = Progressreport::findOrFail($id);
        $progressreportname = $progressreport->progressreportname;
        
        // URL to fetch data from
        $url = "https://script.google.com/macros/s/AKfycbxlupBatmrB4oUf9VyZBQSloM0MnRc0pbQWm_N4cbM1erNfHr1hxSjKEayfMQHfEIRG/exec";

        // Fetch the data from the URL
        $urlContent = file_get_contents($url);
        $urlData = json_decode($urlContent, true);
        
        // Extract the 'user' data
        $userData = $urlData[$progressreportname];

        // Initialize the array to hold the updated data
        $revisiafterupdate = [];

        // Decode the revisi data
        $revisi = json_decode($progressreport->revisi, true);
        foreach ($revisi as $index => $item) {
            $revisi[$item['nodokumen']] = $item;
        }

        // Encode the updated revisi data
        $progressreport->revisi = json_encode($revisi);

        // Update the revisiafterupdate array with the existing revisi data
        foreach ($revisi as $index => $item) {
            $revisiafterupdate[$item['nodokumen']] = $item;
        }

        // Update the revisiafterupdate array with the fetched data
        foreach ($userData as $indexget => $itemget) {
            $revisiafterupdate[$itemget['nodokumen']] = [
                'nodokumen' => $itemget['nodokumen'],
                'namadokumen' => $itemget['namadokumen'],
                'level' => $itemget['level'],
                'checker' => $itemget['checker'],
                'drafter' => $itemget['drafter'],
                'deadlinerelease' => $itemget['deadlinerelease'],
                'documentkind' => $itemget['documentkind'],
                'realisasi' => $itemget['realisasi'],
                'status' => $itemget['status']
            ];
        }

        // Encode the updated revisi data
        $progressreport->revisi = json_encode($revisiafterupdate);

        // Save the progress report
        $progressreport->save();

        // Return a response (optional, you can adjust as needed)
        return response()->json(['message' => 'Progress report updated successfully.']);
    }

    
    public function updateUrl(Request $request, $id)
    {
        $progressReport = ProgressReport::findOrFail($id);
        $progressReport->linkspreadsheet = $request->input('newLinkSpreadsheet');
        $progressReport->linkscript = $request->input('newLinkScript');
        $progressReport->save();

        return response()->json(['success' => true]);
    }

    public function drafterupdate(Request $request, $id)
    {
        $progressReport = ProgressReport::findOrFail($id);
        $fileIds = $request->input('fileIds'); // Ambil ID file yang akan dihapus dari input form
        $drafter = $request->input('drafter');
        $progressReport->save();

        return response()->json(['success' => true]);
    }

    public function ExportMultipleProgress(Request $request, $id)
    {        
        $progressReport = ProgressReport::findOrFail($id);
        $nilaioutput = [];
        $revisiData = json_decode($progressReport->revisi, true);
        foreach ($revisiData as $index => $item) {
            $nilaioutput[] = [
                'nodokumen' => $item['nodokumen'],
                'namadokumen' => $item['namadokumen'],
                'level' => $item['level'] ?? "",
                'drafter' => $item['drafter'] ?? "" ,
                'checker' =>  $item['checker'] ?? "",
                'deadlinerelease' =>  $item['deadlinerelease'] ?? "",
                'documentkind' =>  $item['documentkind'] ?? "",
                'realisasi' =>  $item['realisasi'] ?? "",
                'status' =>  $item['status'] ?? "",
            ];
        }
        $export = new ProgressItemExport($nilaioutput);
        $fileName = $progressReport->progressreportname."_" .$progressReport->proyek_type."_". now()->timestamp . '.xlsx';
        $filePath = 'exports/' . $fileName; // Path file di storage

        // Simpan file ke storage
        Excel::store($export, $filePath);

        // Kembalikan response JSON dengan nama file
        return response()->json(['file_name' => $fileName, 'file_path' => $filePath]);
    }

}
