<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon; // Import Carbon class
use App\Models\Category;
use App\Models\Newreport;
use Illuminate\Http\Request;
use App\Models\ProjectType;
use App\Models\Newprogressreport;
use App\Models\Newprogressreporthistory;
use App\Models\NewProgressReportsLevel;
use App\Models\NewProgressReportDocumentKind;
use App\Models\DailyNotification;
use Exception;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgressreportsImport;


use App\Imports\ProgressreportsTreediagramImport;
use App\Imports\RawprogressreportsImport;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DateTime;
use DateTimeZone;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Wagroupnumber;
use App\Models\NotifHarianUnit;

class NewprogressreportController extends Controller
{
    protected $bottelegramController;

    public function __construct(BotTelegramController $bottelegramController)
    {

        $this->bottelegramController = $bottelegramController;
    }
    public function index()
    {
        $newprogressreports = Newprogressreport::all();
        return view('newprogressreports.index', compact('newprogressreports'));
    }

    public function create($newreport)
    {
        return view('newprogressreports.create', compact('newreport'));
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima dari formulir jika diperlukan

        // Simpan data baru ke dalam database
        $newProgressReport = new Newprogressreport();
        $newProgressReport->newreport_id = $request->input('newreport_id');
        $newProgressReport->nodokumen = $request->input('nodokumen');
        $newProgressReport->namadokumen = $request->input('namadokumen');
        $newProgressReport->documentkind_id = $request->input('jenisdokumen');

        // Simpan progress report ke dalam database
        $newProgressReport->save();
        $newreportId = $newProgressReport->newreport_id;
        $newreport = Newreport::find($newreportId);
        $projectandvalue = Newreport::calculatelastpercentage();
        $newreport->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dibuat',
                'datasebelum' => [],
                'datasesudah' => [$newProgressReport],
                'persentase' => $projectandvalue[0],
                'persentase_internal' => $projectandvalue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'progresscreate',
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('newprogressreports.index')->with('success', 'New progress report created successfully');
    }

    public function destroy($id)
    {
        // Cari entitas berdasarkan id
        $newProgressReport = Newprogressreport::find($id);

        if (auth()->user()->name == "Dian Pertiwi") {
            // Pastikan entitas ditemukan sebelum dihapus
            if ($newProgressReport) {
                // Hapus entitas
                $progressReportsBeforeDelete = [$newProgressReport];
                $newreportId = $newProgressReport->newreport_id;
                $newProgressReport->delete();


                // Ambil model Newreport
                $newreport = Newreport::find($newreportId);
                $projectandvalue = Newreport::calculatelastpercentage();





                $newreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data dihapus',
                        'datasebelum' => $progressReportsBeforeDelete,
                        'datasesudah' => [],
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'progressdelete',
                ]);

                // Redirect dengan pesan sukses jika berhasil
                return redirect()->route('newprogressreports.index')->with('success', 'New progress report deleted successfully');
            } else {
                // Redirect dengan pesan error jika entitas tidak ditemukan
                return redirect()->route('newprogressreports.index')->with('error', 'New progress report not found');
            }
        } else {
            // Pastikan entitas ditemukan sebelum dihapus
            if ($newProgressReport) {
                // Hapus entitas
                $progressReportsBeforeDelete = [$newProgressReport];
                $newreportId = $newProgressReport->newreport_id;
                if ($newProgressReport->status != "RELEASED") {
                    $newProgressReport->delete();
                }



                // Ambil model Newreport
                $newreport = Newreport::find($newreportId);
                $projectandvalue = Newreport::calculatelastpercentage();





                $newreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data dihapus',
                        'datasebelum' => $progressReportsBeforeDelete,
                        'datasesudah' => [],
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'progressdelete',
                ]);

                // Redirect dengan pesan sukses jika berhasil
                return redirect()->route('newprogressreports.index')->with('success', 'New progress report deleted successfully');
            } else {
                // Redirect dengan pesan error jika entitas tidak ditemukan
                return redirect()->route('newprogressreports.index')->with('error', 'New progress report not found');
            }
        }

    }

    public function detail($id)
    {
        // Find the entity by ID
        $newProgressReport = Newprogressreport::with('revisions')->find($id);

        // Ensure the entity is found before rendering the view
        if ($newProgressReport) {
            // Decode the temporystatus JSON
            $hasilwaktu = json_decode($newProgressReport->temporystatus, true);
            $useronly = auth()->user();
            $newreport = Newreport::findOrFail($newProgressReport->newreport_id);
            $index = 1;
            $statusrevisi = $hasilwaktu['statusrevisi'] ?? "dibuka";
            $utc_time = $hasilwaktu['start_time_run'] ?? "Belum Ada";
            $elapsedSeconds = $hasilwaktu['total_elapsed_seconds'] ?? 0;
            $startTime = $hasilwaktu['start_time'] ?? null;
            $pauseTime = $hasilwaktu['pause_time'] ?? null;
            $currentTime = Carbon::now();
            $totalTime = 0;

            if ($startTime !== null) {
                $startTime = Carbon::parse($startTime); // Convert to Carbon object
                if ($pauseTime !== null) {
                    $pauseTime = Carbon::parse($pauseTime); // Convert to Carbon object
                }
                if ($pauseTime === null) {
                    $totalTime = $currentTime->diffInSeconds($startTime) + $elapsedSeconds;
                } else {
                    $totalTime = $pauseTime->diffInSeconds($startTime) + $elapsedSeconds;
                }
            }

            // Convert UTC time to Asia/Jakarta timezone
            if ($utc_time != "Belum Ada") {
                $date = new DateTime($utc_time, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $waktuindo = $date->format('d/m/Y');
            } else {
                $waktuindo = "Belum Ada";
            }
            // Pass the required variables to the view
            return view('newprogressreports.detail', compact(
                'newProgressReport',
                'useronly',
                'newreport',
                'index',
                'statusrevisi',
                'totalTime',
                'waktuindo',
                'startTime',
                'pauseTime',
                'hasilwaktu'
            ));
        } else {
            // Redirect with an error message if the entity is not found
            return redirect()->route('newprogressreports.index')->with('error', 'New progress report not found');
        }
    }

    public function update($id)
    {
        $newProgressReport = Newprogressreport::find($id);
        return redirect()->route('newprogressreports.index')->with('success', 'New progress report updated successfully');
    }

    public function show(Newprogressreport $newprogressreport)
    {
        return view('newprogressreports.show', compact('newprogressreport'));
    }

    public function edit(Newprogressreport $newprogressreport)
    {
        return view('newprogressreports.edit', compact('newprogressreport'));
    }


    public function picktugas(Request $request, $id, $name)
    {
        try {
            $progressReport = Newprogressreport::findOrFail($id);
            $progressReport->update([
                'drafter' => $name,
            ]);
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function starttugas(Request $request, $id, $name)
    {
        try {
            $progressReport = Newprogressreport::findOrFail($id);
            $progressReport->starttugasbaru();
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function pausetugas(Request $request, $id, $name)
    {
        $progressReport = Newprogressreport::findOrFail($id);
        $result = $progressReport->pausetugasbaru();
        // If document not found, return an error response
        if (!$result) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Return a success response
        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

    public function resettugas(Request $request, $id, $name)
    {
        $progressReport = Newprogressreport::findOrFail($id);
        $response = $progressReport->resettugasbaru();
        // If document not found, return an error response
        if (!$response) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Return a success response
        return response()->json(['success' => 'Data berhasil diperbarui']);
    }


    public function resumetugas(Request $request, $id, $name)
    {
        try {
            $progressReport = Newprogressreport::findOrFail($id);
            $response = $progressReport->resumetugasbaru();
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function selesaitugas(Request $request, $id, $name)
    {
        try {
            $progressReport = Newprogressreport::findOrFail($id);
            $response = $progressReport->selesaitugasbaru();
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
            $progressReport = Newprogressreport::findOrFail($id);
            $response = $progressReport->izinkanrevisitugasbaru();
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateprogressreport(Request $request, $id)
    {
        try {
            $nodokumen = $request->input('nodokumen') ?? "";
            $namadokumen = $request->input('namadokumen') ?? "";
            $level = $request->input('level') ?? "";
            $drafter = $request->input('drafter') ?? "";
            $checker = $request->input('checker') ?? "";

            $progressnodokumen = $request->input('progressnodokumen') ?? "";
            if ($progressnodokumen != "") {
                $newprogress = Newprogressreport::where('nodokumen', $progressnodokumen)->first();
                $newprogress->parent_revision_id = $id;
                $newprogress->save();
            }





            $deadlinereleasedate = $request->input('deadlinerelease') ?? "";
            $realisasi = $request->input('realisasi') ?? "";
            $status = $request->input('status') ?? "";

            $progressreport = Newprogressreport::findOrFail($id);
            $progressreportsebelum = Newprogressreport::findOrFail($id);

            $nodokumensebelum = $progressreport->nodokumen;
            $namadokumensebelum = $progressreport->namadokumen;
            $levelsebelum = $progressreport->level ?? "";
            $draftersebelum = $progressreport->drafter ?? "";
            $checkersebelum = $progressreport->checker ?? "";
            $deadlinereleasesebelum = $progressreport->deadlinereleasedate ?? "";
            $realisasisebelum = $progressreport->realisasi ?? "";
            $statussebelum = $progressreport->status ?? "";

            $progressreport->nodokumen = $nodokumen;
            $progressreport->namadokumen = $namadokumen;
            $progressreport->level = $level;
            $progressreport->drafter = $drafter;
            $progressreport->checker = $checker;

            // Mengonversi deadlinerelease dari format 'dd-mm-yyyy' ke format timestamp
            $progressreport->deadlinereleasedate = $deadlinereleasedate ? Carbon::createFromFormat('d-m-Y', $deadlinereleasedate) : null;

            $progressreport->realisasi = $realisasi;
            $progressreport->status = $status;
            $progressreport->save();


            $pesan = 'Perubahan dokumen. No Dokumen: ' . $nodokumensebelum . ' -> ' . $nodokumen . ', Nama Dokumen: ' . $namadokumensebelum . ' -> ' . $namadokumen . ', Drafter: ' . $draftersebelum . ' -> ' . $drafter . ', Checker: ' . $checkersebelum . ' -> ' . $checker . ', Deadline Release: ' . $deadlinereleasesebelum . ' -> ' . $deadlinereleasedate . ', Realisasi: ' . $realisasisebelum . ' -> ' . $realisasi . ', Status: ' . $statussebelum . ' -> ' . $status;

            // Panggil fungsi untuk memperbarui log
            $newreport = Newreport::find($progressreport->newreport_id);

            $projectandvalue = Newreport::calculatelastpercentage();
            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => $pesan,
                    'datasebelum' => [$progressreportsebelum],
                    'datasesudah' => [$progressreport],
                    'persentase' => $projectandvalue[0],
                    'persentase_internal' => $projectandvalue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'progresschange',
            ]);
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function showUploadFormExcel()
    {
        $categoryproject = Category::where('category_name', 'project')->pluck('category_member');
        $unit_for_progres_dokumen = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        return view('newprogressreports.uploadexcel', compact('categoryproject', 'unit_for_progres_dokumen'));
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        } elseif ($jenisupload == "formatprogresskhusus") {
            $hasil = $this->formatprogresskhusus($request);
        } elseif ($jenisupload == "formatrencana") {
            $hasil = $this->formatrencana($request);
        } elseif ($jenisupload == "format") {
            $hasil = $this->formatdasar($request);
        } elseif ($jenisupload == "Treediagram") {
            $hasil = $this->formatTreediagram($request);
        } elseif ($jenisupload == "formatperbaikan") {
            $hasil = $this->formatrencana($request);
        }
        return $hasil;
    }

    public function formatrencana(Request $request)
    {
        //format butuh perbaikan
        // Validate request
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'progressreportname' => 'required|string',
        ]);

        // Get the file from the request
        $file = $request->file('file');
        // Import data using RawprogressreportsImport
        $import = new RawprogressreportsImport();
        $revisiData = Excel::toCollection($import, $file)->first();

        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process imported data
        $processedData = $this->rencanaexported($revisiData);

        // Initialize an array to store grouped data
        $groupedData = [];

        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        $now = now();
        // Initialize arrays to collect successfully exported records
        $exportedRecords = [];
        $exportedCount = 0;
        // Group data by 'proyek_type' and 'unit'


        try {
            foreach ($processedData as $nodokumen => $item) {
                if (!isset($item['proyek_type']) || !isset($item['unit'])) {
                    return response()->json(['error' => 'Invalid data format.'], 400);
                }
                // Collect successfully exported records
                $proyek_type = $item['proyek_type'];
                $unit = $item['unit'];

                // Create a key based on proyek_type and unit
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    // Check if the key exists, otherwise initialize an empty array
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }

                    // Add item to the grouped data array
                    $groupedData[$groupKey][] = $item;
                }

            }

            foreach ($groupedData as $groupKey => $data) {
                // Pastikan groupKey memiliki format yang benar
                if (!str_contains($groupKey, '-')) {
                    return response()->json([
                        'error' => true,
                        'message' => "Invalid groupKey format",
                        'groupKey' => $groupKey
                    ], 400);
                }

                list($proyek_type, $unit) = explode('-', $groupKey, 2); // Limit hanya 2 bagian

                // Cek apakah ProjectType ditemukan
                $project = ProjectType::where('title', $proyek_type)->first();
                if (!$project) {
                    return response()->json([
                        'error' => true,
                        'message' => "ProjectType not found",
                        'proyek_type' => $proyek_type
                    ], 404);
                }

                // Buat atau update Newreport
                $progressreport = Newreport::firstOrCreate(
                    ['proyek_type' => $proyek_type, 'proyek_type_id' => $project->id, 'unit' => $unit],
                    ['status' => 'Terbuka']
                );

                $id = $progressreport->id;

                // Pastikan ada key 'nodokumen' dalam data
                if (empty(array_column($data, 'nodokumen'))) {
                    return response()->json([
                        'error' => true,
                        'message' => "nodokumen missing or empty",
                        'data' => $data
                    ], 400);
                }

                // Ambil existing records berdasarkan newreport_id dan nodokumen
                $existingReports = Newprogressreport::where('newreport_id', $id)
                    ->whereIn('nodokumen', array_column($data, 'nodokumen'))
                    ->get()
                    ->keyBy('nodokumen');

                $newRecords = [];
                $updateRecords = [];
                $exportedRecords = [];
                $exportedCount = 0;
                $now = now();

                foreach ($data as $item) {
                    $nodokumen = $item['nodokumen'];
                    if (isset($existingReports[$nodokumen])) {
                        $existingReport = $existingReports[$nodokumen];
                        $updateRecords[] = $existingReport;
                        $exportedRecords[] = $item;
                        $exportedCount++;
                    } else {
                        $newRecords[] = [
                            'newreport_id' => $id,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $item['namadokumen'] ?? "",
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $exportedRecords[] = $item;
                        $exportedCount++;
                    }
                }

                // Bulk insert new records jika ada
                if (!empty($newRecords)) {
                    Newprogressreport::insert($newRecords);
                }

                // Bulk update existing records jika ada
                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        if ($record) {
                            $record->save();
                        }
                    }
                }

                // Kalkulasi persentase terakhir
                try {
                    $projectandvalue = Newreport::calculatelastpercentage();
                } catch (Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => "Error calculating last percentage",
                        'exception' => $e->getMessage()
                    ], 500);
                }

                // Simpan log sistem
                try {
                    $progressreport->systemLogs()->create([
                        'message' => json_encode([
                            'message' => 'Data Excel successfully imported',
                            'updatedata' => $updateRecords,
                            'databaru' => $newRecords,
                            'persentase' => $projectandvalue[0] ?? null,
                            'persentase_internal' => $projectandvalue[1] ?? null,
                        ]),
                        'level' => 'info',
                        'user' => auth()->user()->name,
                        'user_id' => auth()->user()->id,
                        'aksi' => 'progressaddition',
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => "Error saving system log",
                        'exception' => $e->getMessage()
                    ], 500);
                }
            }

            // Return sukses jika semua berhasil
            return response()->json([
                'message' => 'Data Excel successfully imported',
                'exported_records' => $exportedRecords,
                'exported_count' => $exportedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing Excel file: ' . $e->getMessage()], 500);
        }
    }


    public function rencanaexported($importedData)
    {
        $revisiData = [];
        foreach ($importedData as $key => $row) {
            $unit = trim($row[1] ?? "");
            $proyek_type = trim($row[2] ?? "");
            $nodokumen = trim($row[3] ?? "");
            $namadokumen = trim($row[4] ?? "");

            // Check if any value is "-" or an empty string
            if (
                $unit === "-" || $proyek_type === "-" || $nodokumen === "-" ||
                $unit === "" || $proyek_type === "" || $nodokumen === ""
            ) {
                continue; // Skip this row
            }

            $revisiData[$nodokumen] = [
                'unit' => $unit,
                'proyek_type' => $proyek_type,
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
            ];
        }
        return $revisiData;
    }

    public function formatdasar(Request $request)
    {
        // Get the file from the request
        $file = $request->file('file');

        // Import data using ProgressreportsImport
        try {
            $import = new ProgressreportsImport();
            $importedData = Excel::toCollection($import, $file)->first();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing Excel file: ' . $e->getMessage()], 500);
        }

        if (!$importedData || $importedData->isEmpty()) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process imported data
        $revisiData = $importedData;
        $progressreport = Newreport::where('unit', $request->progressreportname)
            ->where('proyek_type', $request->proyek_type)
            ->first();

        if (!$progressreport) {
            // Handle case when no matching progress report is found
            $progressreport = Newreport::create([
                "unit" => $request->progressreportname,
                "proyek_type" => $request->proyek_type,
                "status" => "Terbuka",
            ]);
        }

        $id = $progressreport->id;

        // Get existing records
        $existingReports = Newprogressreport::where('newreport_id', $id)
            ->whereIn('nodokumen', $revisiData->pluck(0))
            ->whereIn('namadokumen', $revisiData->pluck(1))
            ->get()
            ->keyBy(function ($item) {
                return $item->nodokumen . '-' . $item->namadokumen;
            });

        $newRecords = [];
        $updateRecords = [];

        foreach ($revisiData as $itemget) {
            $key = $itemget[0] . '-' . $itemget[1];
            if (isset($existingReports[$key])) {
                // Prepare existing record for update
                $existingReports[$key]->level = $itemget[2];
                $existingReports[$key]->drafter = $itemget[3];
                $existingReports[$key]->checker = $itemget[4];
                $existingReports[$key]->deadlinereleasedate = $itemget[5] ? Carbon::createFromFormat('d-m-Y', $itemget[5]) : null;
                $existingReports[$key]->documentkind = $itemget[6];
                $existingReports[$key]->realisasi = $itemget[7];
                $existingReports[$key]->status = $itemget[8];
                $updateRecords[] = $existingReports[$key];
            } else {
                // Prepare new record for insertion
                $newRecords[] = [
                    'newreport_id' => $id,
                    'nodokumen' => $itemget[0],
                    'namadokumen' => $itemget[1],
                    'level' => $itemget[2],
                    'drafter' => $itemget[3],
                    'checker' => $itemget[4],
                    'deadlinereleasedate' => $itemget[5],
                    'documentkind' => $itemget[6],
                    'realisasi' => $itemget[7],
                    'status' => $itemget[8],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert new records
        if (!empty($newRecords)) {
            Newprogressreport::insert($newRecords);
        }

        // Bulk update existing records
        if (!empty($updateRecords)) {
            foreach ($updateRecords as $record) {
                $record->save();
            }
        }

        $projectandvalue = Newreport::calculatelastpercentage();
        $progressreport->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data Excel successfully imported',
                'updatedata' => $updateRecords,
                'databaru' => $newRecords,
                'persentase' => $projectandvalue[0],
                'persentase_internal' => $projectandvalue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'progressaddition',
        ]);

        // Log the action
        return response()->json(['message' => 'Data Excel successfully imported'], 200);
    }

    public function formatTreediagram(Request $request)
    {
        // Mengambil file dari request dan memvalidasi bahwa itu adalah file Excel
        $file = $request->file('file');
        $import = new ProgressreportsTreediagramImport();
        $importedData = Excel::toCollection($import, $file)->first();

        $revisiData = [];
        $penyimpananIndukSementara = [];

        $revisiData = $this->getvalueexcel($importedData);

        if (!$revisiData) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Mencari laporan kemajuan yang sesuai
        $progressreport = Newreport::where('unit', $request->progressreportname)
            ->where('proyek_type', $request->proyek_type)
            ->first();

        if (!$progressreport) {
            return response()->json(['error' => 'Progress report not found.'], 404);
        }

        $idku = $progressreport->id;
        $kumpulanid = [];
        // Memetakan ID dokumen induk
        foreach ($revisiData as $index => $itemget) {
            if (!isset($itemget['noindukdokumen'])) {
                continue; // Skip if noindukdokumen key is not set
            }
            $noindukdokumen = $itemget['noindukdokumen'];//substr($itemget['noindukdokumen'], 0, 9);
            if ($noindukdokumen != "") {
                $indukan = Newprogressreport::where('newreport_id', $idku)
                    ->where('nodokumen', 'LIKE', "$noindukdokumen%")
                    ->first();

                if ($indukan) {
                    $kumpulanid[$itemget['nodokumen']] = $indukan->id;
                }

            }

        }
        $ditambahkan = [];
        foreach ($kumpulanid as $nodokumen => $item) {
            // Memperbarui parent_revision_id untuk laporan yang ada
            //$nodokumen = substr($nodokumen, 0, 9);
            $progressakanupdate = Newprogressreport::where('newreport_id', $idku)
                ->where('nodokumen', 'LIKE', "$nodokumen%")
                ->first();
            if ($progressakanupdate && isset($kumpulanid[$progressakanupdate->nodokumen])) {
                $progressakanupdate->parent_revision_id = $kumpulanid[$progressakanupdate->nodokumen];
                $progressakanupdate->save();
                $ditambahkan[] = $progressakanupdate->nodokumen;
            }

        }

        $projectandvalue = Newreport::calculatelastpercentage();
        $progressreport->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data Excel successfully imported',
                'updatedata' => $ditambahkan,
                'persentase' => $projectandvalue[0],
                'persentase_internal' => $projectandvalue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'progressaddition',
        ]);

        // Mencatat tindakan
        return response()->json($ditambahkan, 200);
    }

    public function getvalueexcel($importedData)
    {
        foreach ($importedData as $key => $row) {
            if ($key === 0) {
                continue; // Skip the header row
            }

            $nowGeneration = null;
            $parent = '';

            for ($i = 0; $i < 8; $i++) {
                if (!empty(trim($row[$i] ?? ''))) {
                    $nowGeneration = trim($row[$i]);
                    $penyimpananIndukSementara[$i] = $nowGeneration;

                    if ($i === 0) {
                        $parent = "";
                    } else {
                        $parent = isset($penyimpananIndukSementara[$i - 1]) ? $penyimpananIndukSementara[$i - 1] : '';
                    }

                    $revisiData[$nowGeneration] = [
                        'noindukdokumen' => $parent,
                        'nodokumen' => $nowGeneration,
                    ];
                }
            }
        }
        return $revisiData;
    }

    private function transformDate($value)
    {
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('d-m-Y');
        }

        return $value;
    }




    public function progressreportexported($importedData)
    {
        $revisiData = [];
        $rejectAll = false; // Flag untuk menolak semua jika ada jenis dokumen kosong

        foreach ($importedData as $key => $row) {
            $proyek_type = trim($row[1] ?? ""); //B

            $nodokumen = trim($row[2] ?? "");
            $namadokumen = trim($row[3] ?? "");


            $papersize = trim($row[5] ?? "");
            $sheet = trim($row[6] ?? "");

            $rev = trim($row[7] ?? "");
            $realisasi = $this->transformDate(trim($row[9] ?? ""));
            $drafter = trim($row[10] ?? "");
            $checker = trim($row[11] ?? "");
            $jenisdokumen = trim($row[14] ?? "");
            $dcr = trim($row[15] ?? "");
            $status = trim($row[16] ?? "");
            $unit = $this->perpanjangan(trim($row[4] ?? ""));




            // Validate and parse realisasidate
            $realisasidate = null;
            $realisasiRaw = trim($row[9] ?? "");

            if (!empty($realisasiRaw)) {
                if (is_numeric($realisasiRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $realisasidate = $baseDate->addDays($realisasiRaw - 2)->setTime(0, 0, 0);  // Waktu 00:00:00
                } elseif (Carbon::hasFormat($realisasiRaw, 'd-m-Y')) {
                    $realisasidate = Carbon::createFromFormat('d-m-Y', $realisasiRaw)->setTime(0, 0, 0); // Waktu 00:00:00
                }
            }
            // Jika ada satu saja jenis dokumen yang kosong, semua data akan ditolak
            // if (!empty($proyek_type)) {
            //     if (empty($jenisdokumen)) {
            //         $rejectAll = true;
            //         break; // Keluar dari loop, tidak perlu lanjutkan
            //     }

            // }


            $revisiData[] = [
                'proyek_type' => $proyek_type,
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
                'jenisdokumen' => $jenisdokumen,
                'realisasi' => $realisasi,
                'realisasidate' => $realisasidate,
                'papersize' => $papersize,
                'sheet' => $sheet,
                'rev' => $rev,
                'drafter' => $drafter,
                'checker' => $checker,
                'unit' => $unit,
                'dcr' => $dcr,
                'status' => $status,
            ];
        }
        // Jika ada jenis dokumen kosong, return array kosong
        return $rejectAll ? [] : $revisiData;
    }

    public function updateDocumentKind(Request $request)
    {


        $progressReport = Newprogressreport::with('newprogressreporthistory')->findOrFail($request->progressreport_id);
        $progressReport->documentkind_id = $request->documentkind_id;
        $progressReport->save();
        // Update semua history terkait
        foreach ($progressReport->newprogressreporthistory as $history) {
            $history->documentkind_id = $request->documentkind_id;
            $history->save();
        }

        return response()->json([
            'success' => true,
            'documentkind_name' => $progressReport->documentKind->name ?? '',
            'message' => 'Jenis dokumen berhasil diperbarui!'
        ]);

    }






    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        $exportedCount = 0;

        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });


        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();
            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            $processedData = $this->progressreportexported($revisiData);
            $groupedData = [];

            foreach ($processedData as $item) {
                $proyek_type = trim($item['proyek_type']);
                $unit = $item['unit'];
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }
                    $groupedData[$groupKey][] = $item;
                }
            }

            $nodokumenList = array_column($processedData, 'nodokumen');
            $allProjectTypes = ProjectType::whereIn('title', array_unique(array_column($processedData, 'proyek_type')))
                ->get()
                ->keyBy('title');
            $allDocumentKinds = NewProgressReportDocumentKind::whereIn('name', array_unique(array_column($processedData, 'jenisdokumen')))
                ->get()
                ->keyBy('name');

            $stringkiriman = "";
            foreach ($groupedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";
                list($proyek_type, $unit) = explode('-', $groupKey);
                $project = $allProjectTypes[$proyek_type] ?? null;
                if (!$project) {
                    continue;
                }

                $progressreport = Newreport::firstOrCreate(
                    ['proyek_type_id' => $project->id, 'proyek_type' => $proyek_type, 'unit' => $unit],
                    ['status' => 'Terbuka']
                );

                $id = $progressreport->id;


                $existingReportgroups = Newprogressreport::with(['newreport.projectType'])
                    ->whereIn('nodokumen', $nodokumenList)
                    ->get()
                    ->groupBy(function ($report) {
                        // Menggabungkan nodokumen dengan title dari projectType
                        return $report->nodokumen . '-' . ($report->newreport->projectType->title ?? 'Unknown');
                    });

                // Membuat array dengan 'nodokumen-projectTypeTitle' sebagai kunci dan jumlah kemunculan sebagai nilai
                $nodokumenCounts = collect($nodokumenList)->mapWithKeys(function ($nodokumen) use ($existingReportgroups) {
                    // Filter keys berdasarkan nodokumen
                    $keys = $existingReportgroups->keys()->filter(function ($key) use ($nodokumen) {
                        return str_starts_with($key, $nodokumen . '-');
                    });

                    $values = [];
                    $keys->each(function ($key, $index) use (&$values) {
                        // Dokumen pertama memiliki nilai 0, sisanya 1
                        $values[$key] = $index === 0 ? 0 : 1;
                    });

                    return $values;
                })->toArray();






                $existingReports = Newprogressreport::where('newreport_id', $id)
                    ->whereIn('nodokumen', $nodokumenList)
                    ->get()
                    ->keyBy('nodokumen');

                $existingHistory = Newprogressreporthistory::with(['newProgressReport.newreport.projectType'])->whereIn('nodokumen', $nodokumenList)
                    ->whereIn('rev', array_column($data, 'rev'))
                    ->get()
                    ->keyBy(function ($item) {
                        return $item->nodokumen . '-' . $item->rev . '-' . $item->newProgressReport->newreport->projectType->title;
                    });

                $newRecords = [];
                $updateRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $projectname = $item['proyek_type'];
                    $countnodoc = $nodokumenCounts[$item['nodokumen'] . "-" . $projectname] ?? 0;
                    if ($countnodoc == 0) {
                        $releasedagain = 0;
                    } else {
                        $releasedagain = 1;
                    }

                    $nodokumen = $item['nodokumen'];
                    $jenisdokumen = $item['jenisdokumen'];
                    $documentkind = $allDocumentKinds[$jenisdokumen] ?? null;
                    $rev = $item['rev'] ?? "";
                    $key = $nodokumen . '-' . $rev . '-' . $projectname;

                    $realisasiDate = null;
                    if (!empty($item['realisasi'])) {
                        try {
                            $realisasiDate = new DateTime($item['realisasi']);
                        } catch (\Exception $e) {
                            $realisasiDate = null;
                        }
                    }

                    if (isset($existingReports[$nodokumen])) {
                        $existingRecord = $existingReports[$nodokumen];

                        // Validate existing 'realisasi' date
                        $existingRealisasiDate = null;
                        if (!empty($existingRecord->realisasi)) {
                            try {
                                $existingRealisasiDate = new DateTime($existingRecord->realisasi);
                            } catch (\Exception $e) {
                                $existingRealisasiDate = null;
                            }
                        }

                        if (($existingRecord->releasedagain != $releasedagain)) {
                            $existingRecord->releasedagain = $releasedagain;
                            $updateRecords[] = $existingRecord;
                        }

                        // Update only if the new 'realisasi' date is newer
                        if ($realisasiDate && (!$existingRealisasiDate || $realisasiDate > $existingRealisasiDate)) {
                            $existingRecord->namadokumen = $item['namadokumen'];
                            $existingRecord->papersize = $item['papersize'];
                            $existingRecord->sheet = $item['sheet'];
                            $existingRecord->drafter = $item['drafter'];
                            $existingRecord->checker = $item['checker'];
                            $existingRecord->realisasi = $item['realisasi'];
                            $existingRecord->realisasidate = $item['realisasidate'];
                            $existingRecord->dcr = $item['dcr'] ?? "";
                            if ($documentkind) {
                                $existingRecord->documentkind_id = $documentkind->id;
                            }
                            $existingRecord->status = $item['status'] ?? $existingRecord->status;
                            $updateRecords[] = $existingRecord;
                            $exportedRecords[] = $item;
                            $exportedCount++;
                        }

                        if (!isset($existingHistory[$key])) {

                            if (isset($item['rev'])) {
                                // Prepare history record
                                $historyRecords[] = [
                                    'newprogressreport_id' => $existingRecord->id,
                                    'nodokumen' => $nodokumen,
                                    'namadokumen' => $item['namadokumen'],
                                    // 'level' => $item['level'] ?? "",
                                    'papersize' => $item['papersize'],
                                    'sheet' => $item['sheet'],
                                    'drafter' => $item['drafter'],
                                    'checker' => $item['checker'],
                                    'realisasi' => $item['realisasi'],
                                    'realisasidate' => $item['realisasidate'],
                                    'documentkind_id' => $documentkind ? $documentkind->id : null,
                                    'rev' => $item['rev'],
                                    'dcr' => $item['dcr'] ?? "",
                                    'status' => $item['status'] ?? "",
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }



                        }




                    } else {
                        // no doc baru

                        $temporarydata = [
                            'newreport_id' => $id,
                            'nodokumen' => $item['nodokumen'],
                            'namadokumen' => $item['namadokumen'],
                            'papersize' => $item['papersize'],
                            'sheet' => $item['sheet'],
                            'drafter' => $item['drafter'],
                            'checker' => $item['checker'],
                            'documentkind_id' => $documentkind ? $documentkind->id : null,
                            'realisasi' => $item['realisasi'] ?? '',
                            'realisasidate' => $item['realisasidate'],
                            'dcr' => $item['dcr'] ?? "",
                            'status' => $item['status'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $temporarydata['releasedagain'] = $releasedagain;
                        $newRecords[] = $temporarydata;
                        $historyRecords[] = [
                            'newprogressreport_id' => null,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $item['namadokumen'],
                            // 'level' => $item['level'] ?? "",
                            'papersize' => $item['papersize'],
                            'sheet' => $item['sheet'],
                            'drafter' => $item['drafter'],
                            'checker' => $item['checker'],
                            'realisasi' => $item['realisasi'],
                            'realisasidate' => $item['realisasidate'],
                            'documentkind_id' => $documentkind ? $documentkind->id : null,
                            'rev' => $item['rev'] ?? "",
                            'dcr' => $item['dcr'] ?? "",
                            'status' => $item['status'] ?? "",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }



                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        $record->save();
                    }
                }

                if (!empty($historyRecords)) {
                    // Dapatkan ID dari new progress report yang baru saja dimasukkan
                    if (!empty($newRecords)) {
                        $uniquelist = []; // List untuk menyimpan kombinasi unik nodokumen dan newreport_id
                        $uniqueRecords = []; // Array untuk menyimpan kombinasi unik dari record

                        foreach ($newRecords as $record) {
                            $nodokumen = $record['nodokumen'];
                            $newreport_id = $record['newreport_id'];

                            // Buat key unik berdasarkan kombinasi nodokumen dan newreport_id
                            $uniqueKey = $nodokumen . '-' . $newreport_id;

                            // Cek apakah kombinasi ini sudah ada di uniqueRecords
                            if (!isset($uniqueRecords[$uniqueKey])) {
                                // Jika belum ada, simpan record dalam uniqueRecords
                                $uniqueRecords[$uniqueKey] = $record;
                            }
                        }

                        // Ubah uniqueRecords ke array biasa untuk insert
                        $newRecordsNoDouble = array_values($uniqueRecords);

                        // Setelah loop selesai, insert hanya record yang unik ke dalam database
                        if (!empty($newRecordsNoDouble)) {
                            Newprogressreport::insert($newRecordsNoDouble);
                        }

                        // Get the newly inserted records based on nodokumen to update history
                        $lastInsertedIds = Newprogressreport::whereIn('nodokumen', array_column($newRecords, 'nodokumen'))
                            ->pluck('id', 'nodokumen'); // Retrieve ID by nodokumen
                    }
                    // Perbarui historyRecords dengan ID yang baru
                    foreach ($historyRecords as &$history) {
                        if (!isset($history['newprogressreport_id']) || !$history['newprogressreport_id']) {
                            $history['newprogressreport_id'] = $lastInsertedIds[$history['nodokumen']] ?? null; // Assign the new ID
                        }

                        // Check if newprogressreport_id is still null, skip this record if so
                        if (!$history['newprogressreport_id']) {
                            continue; // Or handle this case with a specific error message
                        }
                    }
                    // Insert history records
                    if (!empty($historyRecords)) {
                        Newprogressreporthistory::insert($historyRecords);
                    }
                }

                $projectandvalue = Newreport::calculatelastpercentage();
                $progressreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data Excel successfully imported',
                        'updatedata' => $updateRecords,
                        'databaru' => $newRecords,
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'progressaddition',
                ]);
            }

            return response()->json(['message' => 'Data Excel successfully imported: ' . $stringkiriman], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function formatprogresskhusus(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $exportedCount = 0;
        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });


        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            $processedData = $this->progressreportexportedkhusus($revisiData);

            $groupedData = [];



            foreach ($processedData as $item) {
                $proyek_type = trim($item['proyek_type']);
                $unit = $item['unit'];
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }
                    $groupedData[$groupKey][] = $item;
                }
            }

            $nodokumenList = array_column($processedData, 'nodokumen');
            $allProjectTypes = ProjectType::whereIn('title', array_unique(array_column($processedData, 'proyek_type')))
                ->get()
                ->keyBy('title');

            $allDocumentKinds = NewProgressReportDocumentKind::pluck('id', 'name');
            $allDocumentLevels = NewProgressReportsLevel::pluck('id', 'title');


            $stringkiriman = "";

            foreach ($groupedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";
                list($proyek_type, $unit) = explode('-', $groupKey);
                $project = $allProjectTypes[$proyek_type] ?? null;
                if (!$project) {
                    \Log::warning("Project type not found: $proyek_type");
                    continue;
                }

                $progressreport = Newreport::firstOrCreate(
                    ['proyek_type_id' => $project->id, 'proyek_type' => $proyek_type, 'unit' => $unit],
                    ['status' => 'Terbuka']
                );

                $id = $progressreport->id;




                $existingReports = Newprogressreport::with(['histories', 'newreport'])
                    ->where('newreport_id', $id)
                    ->whereIn('nodokumen', $nodokumenList)
                    ->get()
                    ->keyBy('nodokumen');

                $newRecords = [];
                $updateRecords = [];
                $updateHistoryRecords = [];

                $bulkInsertData = [];

                foreach ($data as $item) {
                    // Pastikan nilai tidak mengandung '#N/A' atau format tidak valid
                    $papersize = isset($item['papersize']) && $item['papersize'] !== '#N/A' ? $item['papersize'] : null;
                    $sheet = isset($item['sheet']) && is_numeric($item['sheet']) ? $item['sheet'] : null;

                    $nodokumen = $item['nodokumen'];
                    $documentkind_id = $allDocumentKinds[trim($item['jenisdokumen'])] ?? null;
                    $documentlevel_id = $allDocumentLevels[trim($item['level'])] ?? null;
                    $startreleasedate = $item['startreleasedate'] ?? null;
                    $deadlinereleasedate = $item['deadlinereleasedate'] ?? null;
                    $realisasidate = $item['realisasidate'] ?? null;
                    $status = $item['status'] ?? null;

                    if (isset($existingReports[$nodokumen])) {
                        $existingRecord = $existingReports[$nodokumen];
                        $oldData = $existingRecord->toArray();

                        if ($startreleasedate) {
                            $existingRecord->startreleasedate = $startreleasedate;
                        }
                        if ($deadlinereleasedate) {
                            $existingRecord->deadlinereleasedate = $deadlinereleasedate;
                        }
                        if ($realisasidate) {
                            $existingRecord->realisasidate = $realisasidate;
                        }
                        if ($documentkind_id) {
                            $existingRecord->documentkind_id = $documentkind_id;
                        }
                        if ($documentlevel_id) {
                            $existingRecord->level_id = $documentlevel_id;
                        }
                        if ($papersize) {
                            $existingRecord->papersize = $papersize;
                        }
                        if ($sheet) {
                            $existingRecord->sheet = $sheet;
                        }
                        if ($status) {
                            $existingRecord->status = $status;
                        }

                        $newData = $existingRecord->toArray();

                        if ($oldData != $newData) {
                            $existingRecord->save();
                        }

                        if ($existingRecord->histories->isNotEmpty()) {
                            $lastHistory = $existingRecord->histories->last();

                            if ($lastHistory->rev == $item['rev']) {
                                $oldData = $lastHistory->toArray();
                                $updateData = [];

                                // Hanya update jika nilai berubah
                                if ($startreleasedate !== null && $oldData['startreleasedate'] !== $startreleasedate) {
                                    $updateData['startreleasedate'] = $startreleasedate;
                                }
                                if ($deadlinereleasedate !== null && $oldData['deadlinereleasedate'] !== $deadlinereleasedate) {
                                    $updateData['deadlinereleasedate'] = $deadlinereleasedate;
                                }
                                if ($realisasidate !== null && $oldData['realisasidate'] !== $realisasidate) {
                                    $updateData['realisasidate'] = $realisasidate;
                                }
                                if ($documentkind_id !== null && $oldData['documentkind_id'] !== $documentkind_id) {
                                    $updateData['documentkind_id'] = $documentkind_id;
                                }
                                if ($documentlevel_id !== null && $oldData['level_id'] !== $documentlevel_id) {
                                    $updateData['level_id'] = $documentlevel_id;
                                }
                                if ($papersize !== null && $oldData['papersize'] !== $papersize) {
                                    $updateData['papersize'] = $papersize;
                                }
                                if ($sheet !== null && $oldData['sheet'] !== $sheet) {
                                    $updateData['sheet'] = $sheet;
                                }

                                // Hanya update jika ada perubahan data
                                if (!empty($updateData)) {
                                    $updateResult = $lastHistory->update($updateData);
                                    if ($updateResult) {
                                        $updateHistoryRecords[] = 'dokumenupdate:' . $lastHistory->id . $lastHistory->nodokumen;
                                    }
                                }
                            }


                            $unitsnontp = [
                                "Desain Bogie & Wagon",
                                "Desain Carbody",
                                "Desain Elektrik",
                                "Desain Interior",
                                "Desain Mekanik",
                                "Product Engineering",
                                "Mechanical Engineering System",
                                "Quality Engineering",
                                "Electrical Engineering System"
                            ];

                            foreach ($existingRecord->histories as $history) {
                                $oldData = $history->toArray();
                                $updateData = [];

                                // Hanya update jika nilai berubah
                                if ($startreleasedate !== null && $oldData['startreleasedate'] !== $startreleasedate) {
                                    $updateData['startreleasedate'] = $startreleasedate;
                                }
                                if ($deadlinereleasedate !== null && $oldData['deadlinereleasedate'] !== $deadlinereleasedate) {
                                    $updateData['deadlinereleasedate'] = $deadlinereleasedate;
                                }
                                if ($realisasidate !== null && $oldData['realisasidate'] !== $realisasidate) {
                                    $updateData['realisasidate'] = $realisasidate;
                                }
                                if ($documentkind_id !== null && $oldData['documentkind_id'] !== $documentkind_id) {
                                    $updateData['documentkind_id'] = $documentkind_id;
                                }
                                if ($documentlevel_id !== null && $oldData['level_id'] !== $documentlevel_id) {
                                    $updateData['level_id'] = $documentlevel_id;
                                }
                                if ($papersize !== null && $oldData['papersize'] !== $papersize) {
                                    $updateData['papersize'] = $papersize;
                                }
                                if ($sheet !== null && $oldData['sheet'] !== $sheet) {
                                    $updateData['sheet'] = $sheet;
                                }

                                // Hanya update jika ada perubahan data
                                if (!empty($updateData)) {
                                    $updateResult = $history->update($updateData);
                                    if ($updateResult) {
                                        $updateHistoryRecords[] = 'dokumenupdate:' . $history->id . $history->nodokumen;
                                    }
                                }
                            }

                        } else {
                            $allowedRev = array_merge(['0'], range('A', 'Z'));
                            $rev = strtoupper($item['rev'] ?? '0');

                            if (isset($existingRecord) && in_array($rev, $allowedRev)) {
                                $bulkInsertData[] = [
                                    'newprogressreport_id' => $existingRecord->id,
                                    'nodokumen' => $nodokumen,
                                    'namadokumen' => $item['namadokumen'] ?? null,
                                    'rev' => $rev,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }

                        $updateRecords[] = $existingRecord;
                        $exportedRecords[] = $item;
                    }
                }

                if (!empty($bulkInsertData)) {
                    try {
                        Newprogressreporthistory::insert($bulkInsertData);
                        foreach ($bulkInsertData as $insertedItem) {
                            $updateHistoryRecords[] = 'suksesdokumenbaru:' . $insertedItem['nodokumen'];
                        }
                    } catch (\Exception $e) {
                        $updateHistoryRecords[] = 'gagaldokumenbaru:' . $e->getMessage();
                    }
                }



                // Update records in the database
                foreach ($updateRecords as $record) {
                    $record->save();
                }

                // Update project percentage and log
                $projectandvalue = Newreport::calculatelastpercentage();
                $progressreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data Excel successfully imported',
                        'updatedata' => $updateRecords,
                        'databaru' => $newRecords,
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'progressaddition',
                ]);
            }

            return response()->json(['message' => 'Data Excel successfully imported: ' . $stringkiriman . json_encode($updateHistoryRecords)], 200);
        } catch (\Exception $e) {
            \Log::error("Error processing Excel import: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function progressreportexportedkhusus($importedData)
    {
        $revisiData = [];
        $rejectAll = false; // Flag untuk menolak semua jika ada jenis dokumen kosong
        foreach ($importedData as $key => $row) {

            $proyek_type = trim($row[2] ?? "");
            $unit = trim($row[3] ?? "");
            $nodokumen = trim($row[4] ?? "");
            $namadokumen = trim($row[5] ?? "");
            $rev = trim($row[6] ?? "");
            $level = trim($row[7] ?? "");
            $drafter = trim($row[8] ?? "");
            $checker = trim($row[9] ?? "");

            // Validate and parse startreleasedate
            $startReleaseRaw = trim($row[10] ?? "");
            $startreleasedate = null; // Default null

            if (!empty($startReleaseRaw)) {
                if (is_numeric($startReleaseRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $startreleasedate = $baseDate->addDays($startReleaseRaw - 2)->setTime(0, 0, 0);
                } elseif (Carbon::hasFormat($startReleaseRaw, 'd-m-Y')) {
                    try {
                        $startreleasedate = Carbon::createFromFormat('d-m-Y', $startReleaseRaw)->setTime(0, 0, 0);
                    } catch (\Exception $e) {
                        $startreleasedate = null; // Tetap null jika format salah
                    }
                }
            }


            // Validate and parse deadlinereleasedate
            $deadlinereleasedate = null;
            $deadlineRaw = trim($row[11] ?? "");

            if (!empty($deadlineRaw)) {
                if (is_numeric($deadlineRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $deadlinereleasedate = $baseDate->addDays($deadlineRaw - 2)->setTime(0, 0, 0);  // Waktu 00:00:00
                } elseif (Carbon::hasFormat($deadlineRaw, 'd-m-Y')) {
                    $deadlinereleasedate = Carbon::createFromFormat('d-m-Y', $deadlineRaw)->setTime(0, 0, 0); // Waktu 00:00:00
                }
            }



            // Validate and parse realisasidate
            $realisasidate = null;
            $realisasiRaw = trim($row[12] ?? "");

            if (!empty($realisasiRaw)) {
                if (is_numeric($realisasiRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $realisasidate = $baseDate->addDays($realisasiRaw - 2)->setTime(0, 0, 0);  // Waktu 00:00:00
                } elseif (Carbon::hasFormat($realisasiRaw, 'd-m-Y')) {
                    $realisasidate = Carbon::createFromFormat('d-m-Y', $realisasiRaw)->setTime(0, 0, 0); // Waktu 00:00:00
                }
            }

            // Parse realisasi date with the transformDate function
            $realisasi = $this->transformDate(trim($row[12] ?? ""));

            $jenisdokumen = trim($row[13] ?? "");
            $status = trim($row[14] ?? "");
            $papersize = trim($row[15] ?? "");
            $sheet = trim($row[16] ?? "");

            // if (!empty($proyek_type) && empty($jenisdokumen)) {
            //     $rejectAll = true;
            //     break; // Keluar dari loop, tidak perlu lanjutkan
            // }

            $revisiData[] = [
                'proyek_type' => $proyek_type,
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
                'rev' => $rev,
                'drafter' => $drafter,
                'checker' => $checker,
                'deadlinereleasedate' => $deadlinereleasedate ? $deadlinereleasedate->format('Y-m-d H:i:s') : null,
                'realisasidate' => $realisasidate ? $realisasidate->format('Y-m-d H:i:s') : null,
                'realisasi' => $realisasi,
                'jenisdokumen' => $jenisdokumen,
                'status' => $status,
                'startreleasedate' => $startreleasedate ? $startreleasedate->format('Y-m-d H:i:s') : null,
                'unit' => $unit,
                'level' => $level,
                'papersize' => $papersize,
                'sheet' => $sheet,
            ];
        }
        return $revisiData;
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

    public function handleDeleteMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen

        if (empty($progressreportIds)) {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk dihapus'], 400);
        }

        $progressReports = Newprogressreport::whereIn('id', $progressreportIds)->get();

        if ($progressReports->isEmpty()) {
            return response()->json(['error' => 'Dokumen yang dipilih tidak ditemukan'], 404);
        }

        // Filter dokumen dengan status RELEASED
        $releasedReports = $progressReports->where('status', 'RELEASED');

        if ($releasedReports->isNotEmpty()) {
            return response()->json([
                'error' => 'Beberapa dokumen tidak dapat dihapus karena statusnya adalah RELEASED',
                'released_documents' => $releasedReports->pluck('id'), // Mengembalikan ID dokumen yang tidak valid
            ], 400);
        }

        $newreportId = $progressReports->first()->newreport_id ?? null;

        if (!$newreportId) {
            return response()->json(['error' => 'ID Newreport tidak valid'], 404);
        }

        $newreport = Newreport::find($newreportId);

        if (!$newreport) {
            return response()->json(['error' => 'Newreport tidak ditemukan'], 404);
        }

        // Simpan log sebelum dihapus
        $progressReportsBeforeDelete = $progressReports->toArray();

        $projectAndValue = Newreport::calculatelastpercentage();

        $newreport->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dihapus',
                'datasebelum' => $progressReportsBeforeDelete,
                'datasesudah' => [],
                'persentase' => $projectAndValue[0],
                'persentase_internal' => $projectAndValue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->id(),
            'aksi' => 'progressdelete',
        ]);

        // Hapus dokumen
        Newprogressreport::whereIn('id', $progressreportIds)->delete();

        return response()->json(['success' => 'Dokumen yang dipilih berhasil dihapus']);
    }



    public function handleUnreleaseMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen yang akan dihapus dari input form

        if (!empty($progressreportIds)) {
            // Ambil dokumen yang dipilih
            $progressReports = Newprogressreport::whereIn('id', $progressreportIds)->get();
            // Ambil ID Newreport dari dokumen yang akan dihapus
            $newreportId = $progressReports->first()->newreport_id;

            // Ambil model Newreport
            $newreport = Newreport::find($newreportId);
            $projectandvalue = Newreport::calculatelastpercentage();


            $progressReports = Newprogressreport::whereIn('id', $progressreportIds)->get();
            $progressReportsBeforeDelete = Newprogressreport::whereIn('id', $progressreportIds)->get();
            foreach ($progressReports as $document) {
                $document->status = "";
                $document->save(); // Pastikan untuk menyimpan perubahan status
            }
            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data direlease',
                    'datasebelum' => $progressReportsBeforeDelete,
                    'datasesudah' => $progressReports,
                    'persentase' => $projectandvalue[0],
                    'persentase_internal' => $projectandvalue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'progressrelease',
            ]);

            return response()->json(['success' => 'Dokumen yang dipilih berhasil diubah statusnya menjadi RELEASED']);
        } else {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk diubah statusnya'], 400);
        }
    }

    public function handleReleaseMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen yang akan dihapus dari input form

        if (!empty($progressreportIds)) {
            // Ambil dokumen yang dipilih
            $progressReports = Newprogressreport::whereIn('id', $progressreportIds)->get();
            // Ambil ID Newreport dari dokumen yang akan dihapus
            $newreportId = $progressReports->first()->newreport_id;

            // Ambil model Newreport
            $newreport = Newreport::find($newreportId);
            $projectandvalue = Newreport::calculatelastpercentage();


            $progressReports = Newprogressreport::whereIn('id', $progressreportIds)->get();
            $progressReportsBeforeDelete = Newprogressreport::whereIn('id', $progressreportIds)->get();
            foreach ($progressReports as $document) {
                $document->status = "RELEASED";
                $document->save(); // Pastikan untuk menyimpan perubahan status
            }
            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data direlease',
                    'datasebelum' => $progressReportsBeforeDelete,
                    'datasesudah' => $progressReports,
                    'persentase' => $projectandvalue[0],
                    'persentase_internal' => $projectandvalue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'progressrelease',
            ]);

            return response()->json(['success' => 'Dokumen yang dipilih berhasil diubah statusnya menjadi RELEASED']);
        } else {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk diubah statusnya'], 400);
        }
    }

    public function unlinkparent($id)
    {
        // Cari entitas berdasarkan id
        $newProgressReport = Newprogressreport::find($id);

        // Pastikan entitas ditemukan sebelum dihapus
        if ($newProgressReport) {
            // Hapus entitas
            $newProgressReport->parent_revision_id = null;
            $newProgressReport->save();
            // Redirect dengan pesan sukses jika berhasil
            return redirect()->route('newprogressreports.index')->with('success', 'New progress report deleted successfully');
        } else {
            // Redirect dengan pesan error jika entitas tidak ditemukan
            return redirect()->route('newprogressreports.index')->with('error', 'New progress report not found');
        }
    }

    // Fungsi otomatebom
    public function otomateprogressretrofit()
    {
        $allnodokumen = Newprogressreport::all(); // Mengambil data dari model Newprogressreport

        $scriptUrl = "https://script.google.com/macros/s/AKfycbyoNGII0-D7DYG-dzle4kd6hvRXkCdQ7aRH5laajnfQUWqHxhiTzdQyUIyWBxhlBErtYg/exec";

        // Ambil data dari API
        $response = file_get_contents($scriptUrl);
        $data = json_decode($response, true); // Mengubah JSON menjadi array asosiatif

        $updates = [];

        foreach ($allnodokumen as $nodokumen) {
            foreach ($data as $unit => $items) {
                foreach ($items as $item) {
                    if ($item['nodokumen'] == $nodokumen->nodokumen) {
                        if ($item['status'] == "EMPTY" && $nodokumen->status == "RELEASED") {
                            $updates[] = [
                                'Sheet' => $unit,
                                'nodokumen' => $nodokumen->nodokumen,
                                'newStatus' => $nodokumen->status,
                                'drafter' => $nodokumen->drafter,
                                'checker' => $nodokumen->checker,
                                'realisasi' => $nodokumen->realisasi,
                                'row' => $item['row'], // Baris yang relevan
                                'colStatus' => $item['colStatus'], // Kolom yang relevan
                                'colDrafter' => $item['colDrafter'], // Kolom yang relevan
                                'colChecker' => $item['colChecker'], // Kolom yang relevan
                                'colRealisasi' => $item['colRealisasi'], // Kolom yang relevan
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($updates)) {
            $this->sendUpdatesToSpreadsheet($updates);
            return $updates;
        }
    }


    // Fungsi untuk mengirim pembaruan ke Google Apps Script
    private function sendUpdatesToSpreadsheet($updates)
    {
        // URL endpoint Apps Script untuk pembaruan
        $updateUrl = "https://script.google.com/macros/s/AKfycbwvss9f0fem-IYWJ4z76NYTbqHOMeWi4uWShb6MMGEoPp0zHm6KWgF0kMzsQBZ4e_78WQ/exec"; // Ganti dengan URL skrip Apps Anda

        // Menggunakan cURL untuk mengirim POST request
        $ch = curl_init($updateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['updates' => $updates])); // Mengirim pembaruan dalam format JSON

        $response = curl_exec($ch);
        curl_close($ch);

        // Menangani response jika diperlukan
        // $responseData = json_decode($response, true);
        // Lakukan tindakan tambahan jika perlu
        return $updates;
    }

    public function storedokumentkind(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create new JobticketDocumentKind
        $documentKind = NewProgressReportDocumentKind::create($validatedData);

        // Redirect or return a success response
        return redirect()->back()->with('success', 'Jobticket Document Kind created successfully!');
    }

    // Function to display all JobticketDocumentKinds (View)
    public function indexdokumentkind()
    {
        // Cache selama 3 jam (180 menit)
        $documentKinds = Cache::remember('newprogressreportdocumentKinds', 180, function () {
            return NewProgressReportDocumentKind::all();
        });

        // Return the view with the document kinds data
        return view('newprogressreports.documentkind', compact('documentKinds'));
    }

    public function showSearchForm()
    {
        return view('newprogressreports.search');
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan nodokumen, namadokumen, level, atau drafter
        $results = NewProgressReport::where('nodokumen', 'LIKE', '%' . $query . '%')
            ->orWhere('namadokumen', 'LIKE', '%' . $query . '%')
            ->get();

        // Kembalikan hasil pencarian ke view
        return view('newprogressreports.search_results', compact('results'));
    }


    public function indexnotifharian()
    {
        // Fetch all document kinds (id and name)
        $documentKinds = NewProgressReportDocumentKind::select('id', 'name')->get();

        // Fetch all NotifHarianUnit entries
        $notifHarianUnits = NotifHarianUnit::all()->map(function ($unit) {
            // Decode documentkind JSON and get the names
            $documentKindIds = json_decode($unit->documentkind, true);
            $documentKindNames = NewProgressReportDocumentKind::whereIn('id', $documentKindIds)->pluck('name')->toArray();

            // Add the names as a new property to the NotifHarianUnit
            $unit->documentkind_names = $documentKindNames;

            return $unit;
        });

        // Cache selama 3 jam (180 menit)
        $telegrammessagesaccounts = Cache::remember('telegrammessagesaccounts', 180, function () {
            return Wagroupnumber::all();
        });

        // Return the data to the view
        return view('newprogressreports.indexnotifharian', [
            'notifHarianUnits' => $notifHarianUnits,
            'documentKinds' => $documentKinds,
            'telegrammessagesaccounts' => $telegrammessagesaccounts
        ]);
    }

    // Function to show the edit form
    public function editnotifharian($id)
    {
        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Fetch all document kinds (id and name)
        $documentKinds = NewProgressReportDocumentKind::select('id', 'name')->get();

        // Decode documentkind JSON
        $selectedDocumentKinds = json_decode($notifHarianUnit->documentkind, true);

        // Cache selama 3 jam (180 menit)
        $telegrammessagesaccounts = Cache::remember('telegrammessagesaccounts', 180, function () {
            return Wagroupnumber::all();
        });

        return view('newprogressreports.editnotifharian', [
            'notifHarianUnit' => $notifHarianUnit,
            'documentKinds' => $documentKinds,
            'selectedDocumentKinds' => $selectedDocumentKinds,
            'telegrammessagesaccounts' => $telegrammessagesaccounts
        ]);
    }

    // Function to update the existing NotifHarianUnit
    public function updatenotifharian(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'documentkind' => 'required|array',
            'documentkind.*' => 'integer', // Pastikan setiap item dalam array adalah integer
            'telegrammessagesaccount_id' => 'nullable|integer' // Opsional dan harus integer
        ]);

        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Konversi array documentkind menjadi JSON
        $documentkindJson = json_encode(array_map('intval', $validatedData['documentkind']));

        // Update NotifHarianUnit
        $notifHarianUnit->update([
            'title' => $validatedData['title'],
            'documentkind' => $documentkindJson,
            'telegrammessagesaccount_id' => $validatedData['telegrammessagesaccount_id']
        ]);

        return redirect()->route('newprogressreports.index-notif-harian-units')
            ->with('success', 'Notif Harian Unit updated successfully');
    }

    // Function to delete the NotifHarianUnit
    public function deletenotifharian($id)
    {
        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Delete the NotifHarianUnit
        $notifHarianUnit->delete();

        return redirect()->route('newprogressreports.index-notif-harian-units')
            ->with('success', 'Notif Harian Unit deleted successfully');
    }

    public function storenotifharian(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'documentkind' => 'required|array',
            'documentkind.*' => 'integer', // Pastikan setiap item dalam array adalah integer
            'telegrammessagesaccount_id' => 'nullable|integer' // Opsional dan harus integer
        ]);

        // Konversi array documentkind menjadi JSON
        $documentkindJson = json_encode(array_map('intval', $validatedData['documentkind']));

        // Buat NotifHarianUnit baru
        $notifHarianUnit = NotifHarianUnit::create([
            'title' => $validatedData['title'],
            'documentkind' => $documentkindJson, // Simpan array dalam format JSON
            'telegrammessagesaccount_id' => $validatedData['telegrammessagesaccount_id'] // Simpan ID akun Telegram
        ]);

        return response()->json(['message' => 'Notif Harian Unit created successfully', 'data' => $notifHarianUnit], 201);
    }

    public function whatsappsend()
    {
        // Rentang waktu: 24 jam dari detik ini
        $startTime = now()->subDay(); // 24 jam yang lalu
        $endTime = now(); // Saat ini

        // Retrieve all NotifHarianUnit with related data using eager loading
        $notifHarianUnits = NotifHarianUnit::get();

        // Menyimpan unit yang berhasil diproses
        $successUnits = [];

        foreach ($notifHarianUnits as $notifHarianUnit) {
            $date = date('d-m-Y'); // Format tanggal d-m-Y
            $notifName = "Output_Teknologi_{$notifHarianUnit->id}_{$date}";

            // Cek apakah DailyNotification dengan name yang sama sudah ada
            $exists = DailyNotification::where('name', $notifName)->exists();

            if (!$exists) {
                $unit = $notifHarianUnit->title;

                // Buat DailyNotification baru
                $dailyNotification = DailyNotification::create([
                    'name' => $notifName,
                    'day' => now(),
                    'notif_harian_unit_id' => $notifHarianUnit->id,
                    'read_status' => 'unread'
                ]);

                $documentKinds = json_decode($notifHarianUnit->documentkind, true);

                // Retrieve reports within the last 24 hours
                $updatedReports = Newprogressreporthistory::whereBetween('created_at', [$startTime, $endTime])
                    ->whereIn('documentkind_id', $documentKinds)
                    ->get();

                // Mengambil array ID dari request atau menggunakan default
                $historyIds = $updatedReports->pluck('id')->toArray();

                // Menambahkan ID ke relasi newProgressReportHistories
                if (!empty($historyIds)) {
                    $dailyNotification->newProgressReportHistories()->attach($historyIds);
                }

                // Siapkan pesan untuk WhatsApp
                $message = "📢 *Laporan Ekspedisi Dokumen Telah Dibuat!* 📢\n\n" .
                    "📂 *Daftar dokumen dapat diunduh melalui link berikut:*\n" .
                    "🔗 https://inka.goovicess.com/daily-notifications/show/{$dailyNotification->id}\n\n" .
                    "🚀 *Silakan dikonfirmasi!* Terima kasih atas kerja sama dan dukungan Anda. 🙏😊";

                // Kirim pesan WhatsApp
                Wagroupnumber::ujisendunit($unit, $message);

                // Menyimpan unit yang berhasil diproses
                $successUnits[] = $unit;
            }
        }

        // Mengembalikan daftar unit yang berhasil terbuat
        return "Unit yang berhasil terbuat: " . implode(', ', $successUnits);
    }


    public function ujicoba()
    {
        $hasil = Wagroupnumber::ujisendunit("Pabrik Banyuwangi", "Ujicoba");
        return $hasil;
    }





    public function searchdokumenbywa(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan nodokumen, namadokumen, level, atau drafter
        $results = NewProgressReport::where('nodokumen', 'LIKE', '%' . $query . '%')
            ->orWhere('namadokumen', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
        }

        // Looping melalui hasil pencarian dan susun dalam format teks
        foreach ($results as $result) {
            $documentKind = $result->documentkind->name ?? "❌ *Jenis Dokumen*: Belum ada jenis dokumennya";
            $status = $result->getLatestRevAttribute()->status ?? $result->status ?? "❌ *Status*: Belum ada status";
            $revisiTerakhir = $result->getLatestRevAttribute()->rev ?? "❌ *Revisi Terakhir*: Belum ada";

            // Tambahkan detail setiap dokumen ke $textResult dengan lebih menarik
            $textResult .= "📄 *Dokumen No*: " . $result->nodokumen . "\n";
            $textResult .= "📋 *Nama Dokumen*: " . $result->namadokumen . "\n";
            $textResult .= "📊 *Level*: " . $result->level . "\n";
            $textResult .= "✏️ *Drafter*: " . $result->drafter . "\n";
            $textResult .= "✔️ *Checker*: " . $result->checker . "\n";
            $textResult .= "⏳ *Deadline Release*: " . $result->deadlinereleasedate . "\n";
            $textResult .= "📚 *Jenis Dokumen*: " . $documentKind . "\n";
            $textResult .= "✅ *Realisasi*: " . $result->realisasi . "\n";
            $textResult .= "📌 *Status*: " . $status . "\n";
            $textResult .= "🔄 *Revisi Terakhir*: " . $revisiTerakhir . "\n";
            $textResult .= "🏢 *Unit*: " . $result->newreport->unit . "\n";
            $textResult .= "🏗️ *Project*: " . $result->newreport->projectType->title . "\n";
            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar dokumen
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }

    public function getproject(Request $request)
    {
        $projectTitle = $request->query('project');

        // Validasi parameter
        if (!$projectTitle) {
            return response()->json(['error' => 'Project title is required.'], 400);
        }

        // Ambil data workload berdasarkan project title
        $workloadData = Newprogressreport::getHoursProjectDatabyProject($projectTitle);

        return response()->json($workloadData);
    }


}
