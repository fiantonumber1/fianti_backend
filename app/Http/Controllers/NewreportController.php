<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Newprogressreport;
use App\Models\NewProgressReportDocumentKind;
use App\Models\NewProgressReportsLevel;
use Carbon\Carbon; // Import Carbon class
use App\Models\Category;
use App\Models\Newreport;
use App\Models\ProjectType;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Imports\ColumnAImport;
use App\Exports\NewreportExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewreportExportDownload;
use App\Http\Controllers\FileController;
use App\Exports\NewreportDuplicateExport;
use GuzzleHttp\Client;
class NewreportController extends Controller
{
    protected $fileController;
    protected $logController;

    public function __construct(FileController $fileController, LogController $logController)
    {
        $this->fileController = $fileController;
        $this->logController = $logController;
    }

    public function index()
    {

        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");
        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        [$newreports, $revisiall] = Newreport::indexnewreport($unitsingkatan, $listproject);
        return view('newreports.index.index', compact('newreports', 'revisiall'));
    }

    public function indexslideshow()
    {

        // Cache selama 3 jam (180 menit)
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listprojectkhusus', 180, function () {
            return ProjectType::whereIn('title', ['KCI', 'Retrofit', '1164 PPCW BM 54 TON', '50 Locomotive Platform UGL'])->get();
        });

        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");

        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        [$newreports, $revisiall] = Newreport::indexnewreport($unitsingkatan, $listproject);
        return view('newreports.index.indexslideshow', compact('newreports', 'revisiall'));
    }

    public function calculatelastpercentage()
    {
        $projectandvalue = Newreport::calculatelastpercentage();
        return $projectandvalue;
    }

    public function indexlogpercentage()
    {
        $logs = Newreport::historyPercentage()->sortByDesc('created_at');
        return view('newreports.indexlogpercentage', compact('logs'));
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

    public function create()
    {
        return view('newreports.create');
    }

    public function store(Request $request)
    {
        Newreport::create($request->all());
        return redirect()->route('newreports.index')->with('success', 'New report created successfully');
    }


    public function doubledetector($id)
    {
        $newreport = Newreport::select('id', 'unit', 'proyek_type')->find($id);
        $duplicates = $newreport->doubledetector();
        // Kembalikan hasil sebagai JSON
        if (!empty($duplicates)) {
            return response()->json(['duplicates' => $duplicates], 200);
        } else {
            return response()->json(['message' => 'No duplicate nodokumen found'], 200);
        }
    }

    public function destroydian($id)
    {
        $newreport = Newreport::select('id', 'unit', 'proyek_type')->find($id);

        if ($newreport) {
            $hasil = $newreport->destroydian();
            return response()->json(['informasi' => $hasil], 200);
        }
    }

    public function downloadprogress(Request $request, $id)
    {
        // Validate input dates if necessary
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Ambil data Newreport berdasarkan ID
        $newreport = Newreport::select('id', 'unit', 'proyek_type')->find($id);
        $unit = $newreport->unit;
        $project = $newreport->proyek_type;

        // Ambil data progress dari Newreport
        $progressData = $newreport->getProgressData();

        // Ambil tanggal awal dan akhir dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Hitung data mingguan sesuai dengan rentang tanggal yang dipilih
        $data = $newreport->calculateWeeklyData($progressData['progressReports'], $startDate, $endDate);
        $weekData = $newreport->calculateWeeklyPercentage($progressData['progressReports'], $startDate, $endDate);

        // Buat objek NewreportExport
        // Combine data for export
        $informasi = [];
        $exportData = [];
        foreach ($data as $week => $item) {
            $exportData[] = [
                'Week' => $week,
                'Start Date' => $item['start'],
                'End Date' => $item['end'],
                'Total Revisions (Plan)' => $weekData[$week]['value'],
                'Total Revisions (Realisasi)' => $item['nilai'],
                'Total Percentage (Plan)' => $weekData[$week]['percentage'],
                'Total Percentage (Realisasi)' => $item['nilaipresentase'],
            ];
        }
        $informasi = [];
        $informasi[] = [
            'Unit' => $unit,
            'Project' => $project,
            'Exporteddata' => $exportData,
        ];
        // Buat objek NewreportExport
        $export = new NewreportExport($informasi);

        // Tentukan nama file Excel
        $fileName = $newreport->unit . "_" . $newreport->proyek_type . "_" . now()->timestamp . '.xlsx';

        // StreamedResponse untuk langsung download
        return Excel::download($export, $fileName);
    }

    public function downloadprogressbyproject(Request $request, $project)
    {
        // Validate input dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Retrieve start and end dates from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch all Newreport data with the given project type
        $informasi = Newreport::downloadprogressbyproject($project, $startDate, $endDate);

        // Create NewreportExport object
        $export = new NewreportExport($informasi);
        // Define the Excel file name
        $fileName = 'All_Units_Report_' . now()->timestamp . '.xlsx';

        // Return StreamedResponse to directly download the file
        return Excel::download($export, $fileName);
    }

    public function viewbyprojectprogress(Request $request, $project)
    {
        // Validate input dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project' => 'required|string',
        ]);

        // Retrieve start and end dates from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $listproject = [$project];
        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");
        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        [$newreports, $revisiall] = Newreport::byprojectprogress($unitsingkatan, $listproject, $startDate, $endDate);
        return view('newreports.viewpercentage', compact('newreports', 'revisiall', 'startDate', 'endDate'));
    }

    public function downloadduplicatebyproject(Request $request, $project)
    {

        // Fetch all Newreport data with the given project type
        $informasi = Newreport::downloaddoubledetector($project);

        // Create NewreportExport object
        $export = new NewreportDuplicateExport($informasi);
        // Define the Excel file name
        $fileName = 'All_Units_Duplicate_Document_Report_' . now()->timestamp . '.xlsx';

        // Return StreamedResponse to directly download the file
        return Excel::download($export, $fileName);
    }


    public function downloadlaporan(Request $request, $id)
    {
        $newreport = Newreport::with(relations: [
            'projectType',
            'newprogressreports.histories',
            'newprogressreports.levelKind',
            'newprogressreports.documentkind'
        ])->find($id);

        $proyek_type = $newreport->projectType;
        $unitname = $newreport->unit;

        // Retrieve the related progress reports
        $progressreports = $newreport->newprogressreports;
        foreach ($progressreports as $result) {

            $result->revisiTerakhir = $result->getLatestRevAttribute()->rev ?? "Tidak tercantum";
            $result->kindofdocument = $result->documentkind->name ?? "Tidak tercantum";
            $result->level = $result->levelKind->title ?? "";
            $result->projecttype = $proyek_type->title ?? "";
            $result->unit = $unitname;



        }


        // Create a new export instance with the progress reports
        $export = new NewreportExportDownload($progressreports);

        // Define the Excel file name
        $fileName = $unitname . "_" . $proyek_type->title . "_" . now()->timestamp . '.xlsx';

        // Return StreamedResponse to directly download the file
        return Excel::download($export, $fileName);
    }

    public function edit(Newreport $newreport)
    {
        return view('newreports.edit', compact('newreport'));
    }

    public function update(Request $request, Newreport $newreport)
    {
        $newreport->update($request->all());
        return redirect()->route('newreports.index')->with('success', 'New report updated successfully');
    }

    public function destroy(Newreport $newreport)
    {
        $newreport->delete();
        return redirect()->route('newreports.index')->with('success', 'New report deleted successfully');
    }

    public function showlog($newreport, $logid)
    {
        $log = SystemLog::findOrFail($logid);
        return view('newreports.log', compact('log'));
    }

    public function show($id)
    {

        // Eager load the 'newprogressreports' relationship and its nested relationships
        $newreport = Newreport::with([
            'newprogressreports.children',
            'newprogressreports.documentKind',
            'newprogressreports.histories.documentKind', // Relasi history dan documentKind diambil dari history
            'systemLogs' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan waktu pembuatan
            }
        ])
            ->select('id', 'unit', 'proyek_type')
            ->findOrFail($id);

        // Cache selama 3 jam (180 menit)
        $jenisdokumen = Cache::remember('jenisdokumen', 180, function () {
            return NewProgressReportDocumentKind::all();
        }); // Cache selama 3 jam (180 menit)

        $userdef = auth()->user();




        //////////////////// KHUSUS KCI /////////////////
        //////////////////// KHUSUS KCI /////////////////
        if ($newreport->proyek_type == "KCI") {
            $unitdeadlinerelease = [];
            $unitdeadlinerelease['Desain Bogie & Wagon'] = '06-12-2023';
            $unitdeadlinerelease['Sistem Mekanik & Interior'] = '06-12-2023';
            $unitdeadlinerelease['Desain Elektrik'] = '06-12-2023';
            $unitdeadlinerelease['Desain Mekanik'] = '06-12-2023';
            $unitdeadlinerelease['Desain Mekanik & Interior'] = '06-12-2023';
            $unitdeadlinerelease['Sistem Mekanik'] = '06-12-2023';
            $unitdeadlinerelease['Desain Interior'] = '06-12-2023';
            $unitdeadlinerelease['Welding Technology'] = '30-10-2024';
            $unitdeadlinerelease['Shop Drawing'] = '30-10-2024';
            $unitdeadlinerelease['Preparation & Support'] = '29-09-2023';
            $unitdeadlinerelease['Teknologi Proses'] = '30-10-2024';
            $unitdeadlinerelease['Mechanical Engineering System'] = '29-11-2023';
            $unitdeadlinerelease['Desain Carbody'] = '06-12-2023';
            $unitdeadlinerelease['Quality Engineering'] = '23-10-2024';
            $unitdeadlinerelease['Product Engineering'] = '20-09-2023';
            $unitdeadlinerelease['Electrical Engineering System'] = '29-11-2023';
            $data = [];
            $dataall = [];
            // URL Google Apps Script yang baru
            $googleScriptUrl = 'https://script.google.com/macros/s/AKfycbybSW9SzZLaLMqnhYw7qZjj23GRVaxWTWC5yw4328OVo07s7v_Rf_zVn55PScglOdaQ-g/exec';

            // Nama-nama sheet yang ingin diambil datanya
            $sheetNames = [$newreport->unit];  // Gantilah dengan nama sheet yang sesuai

            // Membuat client Guzzle
            $client = new Client();

            try {
                // Mengirim POST request ke Google Apps Script
                $response = $client->post($googleScriptUrl, [
                    'json' => $sheetNames,
                ]);

                // Memeriksa status response
                if ($response->getStatusCode() == 200) {

                    // Parsing response JSON
                    $data = json_decode($response->getBody(), true);

                    if (is_array($data)) {
                        // Menampilkan data
                        foreach ($data as $sheetName => $sheetDatas) {
                            if (is_array($sheetDatas)) {
                                foreach ($sheetDatas as $sheetData) {
                                    $docnumber = $sheetData['No Dokumen'];
                                    $dataall[$docnumber] = $sheetData;
                                }
                            }

                        }
                    }

                } else {
                    return "Failed to retrieve data. Status code: " . $response->getStatusCode();
                }

            } catch (\Exception $e) {
                return "Error: " . $e->getMessage();
            }
        }
        //////////////////// KHUSUS KCI ////////////////
        //////////////////// KHUSUS KCI ////////////////





        // Use eager-loaded relationships
        $progressReports = $newreport->newprogressreports;
        $generasi = [];
        foreach ($progressReports as $index => $progressReport) {
            $progressReport->newreport_id = $id;



            if (!in_array($userdef->rule, ['QC FAB', 'QC FIN', 'QC FAB', 'QC FIN', 'QC INC', 'Fabrikasi', 'PPC', 'QC Banyuwangi', 'Pabrik Banyuwangi', 'Fabrikasi', 'PPC', 'QC Banyuwangi'])) {

                // sesi internal menjadi back background dan eksternal menjadi main background

                if (session('internalon')) {
                    $progressReport->statusterbaru = $progressReport->status ?? "";

                } else {
                    if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                        $progressReport->statusterbaru = 'REALESED';
                    } else {
                        $progressReport->statusterbaru = $progressReport->status ?? "";
                    }


                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    if ($newreport->proyek_type == "KCI") {

                        unset($progressReports[$index]);

                    }
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                }

            } else {

                if (!session('internalon')) {
                    $progressReport->statusterbaru = $progressReport->status ?? "";

                } else {
                    if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                        $progressReport->statusterbaru = 'REALESED';
                    } else {
                        $progressReport->statusterbaru = $progressReport->status ?? "";
                    }


                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    if ($newreport->proyek_type == "KCI") {

                        unset($progressReports[$index]);

                    }
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                    /////////// Khusus KCI ////////////////////////////
                }
            }

            $generasi[$progressReport->id]['childreen'] = $progressReport->children;
            $generasi[$progressReport->id]['count'] = $progressReport->children->count();
            $latestRev = '';
            if (!empty($progressReport->histories)) {
                $progressReport->latestRev = $progressReport->latest_rev;  // Menggunakan accessor
                if ($progressReport->getLatestRevAttribute()) {
                    $progressReport->realisasi = $progressReport->getLatestRevAttribute()->realisasi;
                    if ($progressReport->getLatestRevAttribute()->status == "RELEASED") {
                        $progressReport->status = $progressReport->getLatestRevAttribute()->status;
                    }
                    $progressReport->namadokumen = $progressReport->getLatestRevAttribute()->namadokumen;
                    $progressReport->rev = $progressReport->getLatestRevAttribute()->rev;
                }


            }




        }


        if (!in_array($userdef->rule, ['QC FAB', 'QC FIN', 'QC FAB', 'QC FIN', 'QC INC', 'Fabrikasi', 'PPC', 'QC Banyuwangi', 'Pabrik Banyuwangi', 'Fabrikasi', 'PPC', 'QC Banyuwangi'])) {

            // sesi internal menjadi back background dan eksternal menjadi main background
            if (session('internalon')) {

            } else {
                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
                if ($newreport->proyek_type == "KCI") {
                    $progressReports = [];
                    $count = 0;

                    foreach ($dataall as $index => $datatunggal) {
                        $progressReports[$count++] = [
                            'newreport_id' => $newreport->id,
                            'id' => $count++,
                            'nodokumen' => $datatunggal["No Dokumen"],
                            'namadokumen' => $datatunggal["Nama Dokumen"],
                            'deadlinereleasedate' => $unitdeadlinerelease[$newreport->unit] ? Carbon::createFromFormat('d-m-Y', $unitdeadlinerelease[$newreport->unit]) : null,
                            'level' => '',
                            'drafter' => $datatunggal["Drafter"],
                            'checker' => $datatunggal["Checker"],
                            'realisasi' => $datatunggal["Realisasi"],
                            'documentkind' => "",
                            'status' => $datatunggal["Status"],
                            'statusterbaru' => $datatunggal["Status"],
                            'parent_revision_id' => 1,
                            'children' => collect([]), // Menginisialisasi children sebagai koleksi
                            'temporystatus' => null // Menambahkan properti temporystatus di sini
                        ];
                    }

                    // Mengonversi setiap elemen $progressReports menjadi objek
                    $progressReports = array_map(function ($item) {
                        return (object) $item;
                    }, $progressReports);

                    // Mengonversi ke koleksi Laravel
                    $progressReports = collect($progressReports);
                }

                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
            }

        } else {

            // sesi internal menjadi main background dan eksternal menjadi back background    
            if (!session('internalon')) {

            } else {
                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
                if ($newreport->proyek_type == "KCI") {
                    $progressReports = [];
                    $count = 0;

                    // Initialize count variable if not already done
                    $count = isset($count) ? $count : 1;

                    // Retrieve all progress reports once before the loop, indexed by nodokumen
                    $existingProgressReports = Newprogressreport::whereIn('nodokumen', collect($dataall)->pluck('No Dokumen'))->get()->keyBy('nodokumen');

                    // Initialize an empty array to store progress reports
                    $progressReports = [];

                    // Loop through $dataall and process each item
                    foreach ($dataall as $index => $datatunggal) {
                        // Ensure 'No Dokumen' exists in the current data
                        if (!isset($datatunggal["No Dokumen"]) || empty($datatunggal["No Dokumen"])) {
                            // Handle missing or invalid 'No Dokumen' (skip or log error)
                            continue;
                        }

                        // Check if the current document exists in the retrieved progress reports
                        $newprogressreport = $existingProgressReports->get($datatunggal["No Dokumen"]);

                        // If exists, use its id; otherwise, assign a new value
                        if ($newprogressreport) {
                            $nilai = $newprogressreport->id;
                        } else {
                            $nilai = $count++; // Increment count for new documents
                        }

                        // Ensure other required fields exist
                        $nodokumen = $datatunggal["No Dokumen"] ?? ''; // Default to empty string if not available
                        $namadokumen = $datatunggal["Nama Dokumen"] ?? 'Unknown Document'; // Provide fallback value
                        $drafter = $datatunggal["Drafter"] ?? 'Unknown Drafter';
                        $checker = $datatunggal["Checker"] ?? 'Unknown Checker';
                        $realisasi = $datatunggal["Realisasi"] ?? 0;
                        $status = $datatunggal["Status"] ?? 'Unknown Status';

                        // Build the progress report array
                        $progressReports[$nilai] = [
                            'id' => $nilai,
                            'newreport_id' => $id,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $namadokumen,
                            'deadlinereleasedate' => $unitdeadlinerelease[$newreport->unit] ?? null, // Handle possible undefined index
                            'level' => '',
                            'drafter' => $drafter,
                            'checker' => $checker,
                            'realisasi' => $realisasi,
                            'documentkind' => "",
                            'status' => $status,
                            'statusterbaru' => $status,
                            'parent_revision_id' => 1,
                            'children' => collect([]), // Initialize children as a collection
                            'temporystatus' => null // Add temporystatus property here
                        ];
                    }


                    // Mengonversi setiap elemen $progressReports menjadi objek
                    $progressReports = array_map(function ($item) {
                        return (object) $item;
                    }, $progressReports);

                    // Mengonversi ke koleksi Laravel
                    $progressReports = collect($progressReports);
                }

                /////////// Khusus KCI ////////////////////////////
                /////////// Khusus KCI ////////////////////////////
            }
        }




        $progressData = $newreport->getProgressData();
        $releaseinfo = $newreport->releasecount();
        $duplicates = $newreport->doubledetector();

        // Calculate necessary data
        $levelStatusData = $newreport->calculateLevelStatusData($progressData['progressReports']);
        $percentageData = $newreport->calculatePercentageData($levelStatusData['datalevel'], $levelStatusData['datastatus']);
        $data = $newreport->calculateWeeklyData($progressData['progressReports'], '02-01-2023', '02-01-2025');
        $weekData = $newreport->calculateWeeklyPercentage($progressData['progressReports'], '02-01-2023', '02-01-2025');
        $listanggota = $this->getColumnA();
        $useronly = auth()->user();
        $statuslist = ['All', 'RELEASED', 'UNRELEASED'];
        $revisiall = [];

        for ($i = 0; $i < count($statuslist); $i++) {
            $key = str_replace(' ', '_', $statuslist[$i]);

            if ($statuslist[$i] === 'UNRELEASED') {
                // Mengambil semua newreports yang tidak berstatus RELEASED
                $revisiall[$key]['progressReports'] = collect($progressReports)->where('status', '!=', 'RELEASED')->all();
            } else {
                $revisiall[$key]['progressReports'] = collect($progressReports)->where('status', $statuslist[$i])->all();
            }
        }

        $countunrelease = $releaseinfo['countunrelease'];
        $countrelease = $releaseinfo['countrelease'];
        $progresspercentage = $releaseinfo['progresspercentage'];

        // Untuk menambahkan semua newreports dalam kategori 'All'
        $revisiall['All']['progressReports'] = $progressReports;

        // Menentukan nilai persentase berdasarkan kondisi tertentu
        $progressPercentageFormatted = number_format($progresspercentage, 2);

        if (!in_array($userdef->rule, ['QC FAB', 'QC FIN', 'QC FAB', 'QC FIN', 'QC INC', 'Fabrikasi', 'PPC', 'QC Banyuwangi', 'Pabrik Banyuwangi', 'Fabrikasi', 'PPC', 'QC Banyuwangi'])) {

            // sesi internal menjadi back background dan eksternal menjadi main background
            if (session('internalon')) {
                // Jika 'internalon' di sesi aktif
                $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                $unrelease = $countunrelease;
                $release = $countrelease;

                $newreport->nilaipersentase = $nilaipersentase;
                $newreport->release = $release;
                $newreport->unrelease = $unrelease;

            } else {
                // Jika 'internalon' di sesi tidak aktif
                if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                    $nilaipersentase = '100%';
                    $unrelease = 0;
                    $release = $countunrelease + $countrelease;

                    $newreport->nilaipersentase = $nilaipersentase;
                    $newreport->release = $release;
                    $newreport->unrelease = $unrelease;

                } else {
                    $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                    $unrelease = $countunrelease;
                    $release = $countrelease;

                    $newreport->nilaipersentase = $nilaipersentase;
                    $newreport->release = $release;
                    $newreport->unrelease = $unrelease;
                }
            }

        } else {

            // sesi internal menjadi main background dan eksternal menjadi back background
            if (session('internalon')) {
                // Jika 'internalon' di sesi aktif
                $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                $unrelease = $countunrelease;
                $release = $countrelease;

                $newreport->nilaipersentase = $nilaipersentase;
                $newreport->release = $release;
                $newreport->unrelease = $unrelease;

            } else {
                // Jika 'internalon' di sesi tidak aktif
                if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                    $nilaipersentase = '100%';
                    $unrelease = 0;
                    $release = $countunrelease + $countrelease;

                    $newreport->nilaipersentase = $nilaipersentase;
                    $newreport->release = $release;
                    $newreport->unrelease = $unrelease;

                } else {
                    $nilaipersentase = ($progressPercentageFormatted == 0) ? '-' : $progressPercentageFormatted . '%';
                    $unrelease = $countunrelease;
                    $release = $countrelease;

                    $newreport->nilaipersentase = $nilaipersentase;
                    $newreport->release = $release;
                    $newreport->unrelease = $unrelease;
                }
            }

        }


        return view('newreports.show.show', [
            'newreport_id' => $id,
            'revisiall' => $revisiall,
            'newreport' => $newreport,
            'progressReports' => $progressReports,
            'datastatus' => $levelStatusData['datastatus'],
            'datalevel' => $levelStatusData['datalevel'],
            'percentageLevel' => $percentageData['percentageLevel'],
            'percentageStatus' => $percentageData['percentageStatus'],
            'indukan' => $progressData['indukan'],
            'listprogressnodokumenencode' => $progressData['listprogressnodokumen'],
            'listanggota' => $listanggota,
            'data' => $data,
            'weekData' => $weekData,
            'duplicates' => $duplicates,
            'countrelease' => $countrelease,
            'countunrelease' => $countunrelease,
            'progresspercentage' => $progresspercentage,
            'useronly' => $useronly,
            'generasi' => $generasi,
            'jenisdokumen' => $jenisdokumen
        ]);
    }

    public function showrev($idprogress, $id)
    {
        // Temukan report berdasarkan ID dengan eager loading pada relasi 'newprogressreporthistory'
        $newprogressreport = Newprogressreport::with('newprogressreporthistory')->findOrFail($id);

        // Mendapatkan user yang sedang login
        $userdef = auth()->user();

        // Mendapatkan data revisi yang terkait dengan report tersebut
        $newreporthistorys = $newprogressreport->newprogressreporthistory;

        // Kirim data ke view
        return view('newreports.newprogressreporthistory', [
            'idprogress' => $idprogress,
            'newprogressreport' => $newprogressreport,
            'newreporthistorys' => $newreporthistorys,
        ]);
    }

    public function updateDocumentNumber(Request $request)
    {
        // Validasi input
        $request->validate([
            'nodokumen' => 'required|string|max:255',
        ]);

        // Perbarui data nodokumen pada newprogressreport
        $nodokumen = $request->input('nodokumen');
        $nodokumenlama = $request->input('nodokumenlama');
        $newreport_id = $request->input('newreport_id');


        // Cari data newprogressreport dengan history-nya
        $newprogressreport = Newprogressreport::with('newprogressreporthistory')->where('newreport_id', $newreport_id)->where('nodokumen', $nodokumenlama)->first();

        // Periksa apakah data ditemukan
        if (!$newprogressreport) {
            return response()->json([
                'status' => 'error',
                'title' => 'Gagal!',
                'message' => 'Data tidak ditemukan' . $nodokumen . " " . $newreport_id,
            ], 404);
        }


        $newprogressreport->nodokumen = $nodokumen;
        $newprogressreport->save();

        // Perbarui data pada semua history terkait jika ada
        $updatedRows = 0;
        if ($newprogressreport->newprogressreporthistory()->exists()) {
            $updatedRows = $newprogressreport->newprogressreporthistory()->update(['nodokumen' => $nodokumen]);
        }

        // Berikan respons berdasarkan hasil update
        if ($updatedRows > 0) {
            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil!',
                'message' => 'No dokumen berhasil diperbarui.'
            ]);
        } elseif ($updatedRows === 0 && $newprogressreport->wasChanged('nodokumen')) {
            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil!',
                'message' => 'No dokumen pada laporan utama berhasil diperbarui.'
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'title' => 'Perhatian!',
                'message' => 'Tidak ada data yang diperbarui.'
            ]);
        }

    }

    public function target(Request $request)
    {
        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }



        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';

        return view('newreports.hasil', compact('projectsData', 'project', 'download'));




    }


    public function getProjectDatatenminutes(Request $request)
    {
        $project = $request->projectName;

        // Buat key cache unik berdasarkan nama project
        $cacheKey = "project_data_{$project}";

        // Simpan hasil query ke cache selama 10 menit
        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($project) {
            return Newprogressreport::getProjectDatastatisfordashboard($project);
        });

        return response()->json($result);
    }

    public function getProjectData(Request $request)
    {
        $project = $request->projectName;

        // Buat key cache unik berdasarkan nama project
        $cacheKey = "project_data_{$project}";


        $result = Newprogressreport::getProjectDatastatisfordashboard($project);

        return response()->json($result);
    }

    public function jamorang(Request $request)
    {
        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }

        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';

        return view('newreports.jo', compact('projectsData', 'project', 'download'));




    }

    public function getHoursProjectData(Request $request)
    {
        $year = $request->year;
        $result = Newprogressreport::getHoursProjectData($year);
        return response()->json($result);
    }


    public function downloadChart(Request $request)
    {


        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }



        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';

        return view('newreports.gantt_chart', compact('projectsData', 'project', 'download'));




    }


}
