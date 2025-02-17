<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Newbom;
use App\Models\Newprogressreport;
use App\Models\Category;
use App\Imports\BomsImport;
use App\Models\Newbomkomat;
use App\Models\Newbomkomathistory;
use App\Models\NewMemo;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewbomExport;

class NewBOMController extends Controller
{

    public function ujicoba()
    {
        $newbomkomat = Newbomkomat::find(4);
        $newbomkomat->newprogressreports()->attach([280, 281]);
    }



    public function destroyNewbom($id)
    {
        $newbom = Newbom::findOrFail($id);
        $newbom->delete();
        return response()->json(null, 204);
    }

    public function showNewbom($id)
    {
        // Fetch the specific newbom along with its related newbomkomats and systemLogs
        $newbom = Newbom::with([
            'newbomkomats.newprogressreports',
            'systemLogs' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Order by the newest first
            }
        ])->findOrFail($id);
        // Fetch the project type related to the newbom with caching for 3 hours
        $project = Cache::remember("project_type_{$newbom->proyek_type_id}", 10800, function () use ($newbom) {
            return ProjectType::findOrFail($newbom->proyek_type_id);
        });

        // Fetch only the documents related to the project type of the specific newbom with caching
        $documents = Cache::remember('new_memos_with_related_data', 600, function () {
            return NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        });

        // Get additional data for the fetched documents
        $additionalData = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Check if the additional data has the required keys
        if (!isset($additionalData['listdatadocuments']) || !isset($additionalData['percentagememoterbuka']) || !isset($additionalData['percentagememotertutup'])) {
            // Handle case where data is incomplete
            return response()->json(['error' => 'Data tidak lengkap'], 500);
        }

        $listdatadocumentencode = $additionalData['listdatadocuments'];
        $percentagememoterbuka = $additionalData['percentagememoterbuka'];
        $percentagememotertutup = $additionalData['percentagememotertutup'];

        // Generate BOM data
        [$groupedKomats, $groupprogress, $seniorpercentage, $materialopened, $materialclosed] = $newbom->bomoneshow($documents, [], $listdatadocumentencode);

        // Get the related newbomkomats
        $newbomkomats = $newbom->newbomkomats;


        // Return the view with the necessary data
        return view('newbom.show', compact('newbom', 'newbomkomats', 'groupedKomats', 'groupprogress', 'seniorpercentage'));
    }

    public function indexNewbom()
    {
        $data = Newbom::infoall();
        $newboms = $data['newboms'];
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        $revisiall = $data['revisiall'];
        $groupbomnumberpercentage = $data['groupbomnumberpercentage'];
        // Get additional data for the fetched documents
        $additionalData = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Check if the additional data has the required keys
        if (!isset($additionalData['listdatadocuments']) || !isset($additionalData['percentagememoterbuka']) || !isset($additionalData['percentagememotertutup'])) {
            // Handle case where data is incomplete
            return response()->json(['error' => 'Data tidak lengkap'], 500);
        }

        $listdatadocumentencode = $additionalData['listdatadocuments'];
        foreach ($newboms as $newbom) {
            [$groupedKomats, $groupprogress, $seniorpercentage, $materialopened, $materialclosed] = $newbom->bomoneshow($documents, [], $listdatadocumentencode);
            $newbom->seniorpercentage = $seniorpercentage;
        }
        return view('newbom.index', compact('newboms', 'revisiall', 'groupbomnumberpercentage'));
    }

    public function indexlogpercentage()
    {
        $logs = Newbom::historyPercentage()->sortByDesc('created_at');
        return view('newbom.indexlogpercentage', compact('logs'));
    }

    // Metode untuk Newbomkomat
    public function storeNewbomkomat(Request $request, $id)
    {
        // Create a new instance of Newbomkomat and assign request data to it
        $data = Newbom::infoall();
        $newbom = Newbom::findOrFail($id);
        $newbomkomat = new Newbomkomat();
        $newbomkomat->newbom_id = $id;
        $newbomkomat->kodematerial = $request->kodematerial;
        $newbomkomat->material = $request->material;
        $newbomkomat->status = $request->status;
        // Save the new instance to the database
        $newbomkomat->save();
        $newbom->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dibuat',
                'datasebelum' => [],
                'datasesudah' => [$newbomkomat],
                'persentase' => $data['groupbomnumberpercentage'],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomcreate',
        ]);

        // Return the created resource as a JSON response with status code 201
        return response()->json($newbomkomat, 201);
    }

    public function changeNewbomkomat(Request $request, $id, $idkomat)
    {
        $data = Newbom::infoall();
        $newbom = Newbom::findOrFail($id);
        // Create a new instance of Newbomkomat and assign request data to it
        $newbomkomatsebelum = Newbomkomat::find($idkomat);
        $newbomkomat = Newbomkomat::find($idkomat);
        $newbomkomat->newbom_id = $id;
        $newbomkomat->kodematerial = $request->kodematerial;
        $newbomkomat->material = $request->material;
        $newbomkomat->status = $request->status;
        // Save the new instance to the database
        $newbomkomat->save();
        $newbom->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data diubah',
                'datasebelum' => [$newbomkomatsebelum],
                'datasesudah' => [$newbomkomat],
                'persentase' => $data['groupbomnumberpercentage'],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomchange',
        ]);

        // Return the created resource as a JSON response with status code 201
        return response()->json($newbomkomat, 201);
    }

    public function deleteNewbomkomat(Request $request, $id, $idkomat)
    {
        $data = Newbom::infoall();
        $newbomkomatsebelum = Newbomkomat::find($idkomat);
        $newbom = Newbom::findOrFail($id);
        // Create a new instance of Newbomkomat and assign request data to it
        $newbom->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dihapus',
                'datasebelum' => [$newbomkomatsebelum],
                'datasesudah' => [],
                'persentase' => $data['groupbomnumberpercentage'],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomdelete',
        ]);
        $newbomkomat = Newbomkomat::find($idkomat)->delete();
        // Return the created resource as a JSON response with status code 201
        return response()->json($newbomkomat, 201);
    }

    public function importExcel(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the file from the request
        $file = $request->file('file');

        // Import data using BomsImport
        $import = new BomsImport();
        $revisiData = $import->collection(Excel::toCollection($import, $file)->first());

        $bom = Newbom::where('BOMnumber', $request->bomnumber)->first();

        if ($bom) {
            return response()->json(['Message' => "Sudah pernah diiput"]);
        } else {
            $newbom = Newbom::create([
                'BOMnumber' => $request->bomnumber,
                'proyek_type' => "",
                'proyek_type_id' => $request->project_type_id,
                'unit' => $request->unit,
            ]);

            $newbomKomatData = [];
            foreach ($revisiData as $data) {
                $newbomKomatData[] = [
                    'newbom_id' => $newbom->id,
                    'kodematerial' => $data['kodematerial'],
                    'material' => $data['material'],
                    'status' => $data['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Newbomkomat::insert($newbomKomatData);
            $hasil = Newbom::infoall();
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data ditambahkan',
                    'datasebelum' => [],
                    'datasesudah' => $newbomKomatData,
                    'persentase' => $hasil['groupbomnumberpercentage'],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'bomaddition',
            ]);



        }

        return redirect()->route('newbom.index');
    }

    public function showUploadForm()
    {
        $projects = ProjectType::all();
        $units = Category::getlistCategoryMemberByName('unitunderpe');
        return view('newbom.uploadbom', compact('projects', 'units'));
    }

    public function operatorfindbykomat(Request $request)
    {
        // Validate the request input
        $request->validate([
            'komat' => 'required|string|max:255',
        ]);

        // Retrieve the 'komat' value from the request
        $komat = $request->input('komat');

        // Find 'Newbomkomat' entity based on 'komat' value
        $newbomkomat = Newbomkomat::where('kodematerial', $komat)->first();

        // If not found, return an empty response with 404 status
        if (!$newbomkomat) {
            return response()->json(['operator' => ''], 404); // 404 Not Found
        }

        // Find related 'Newbom' entity using ID from 'Newbomkomat'
        $newbom = Newbom::find($newbomkomat->newbom_id);

        // Get 'unit' as operator or return an empty string if 'unit' does not exist
        $operator = $newbom->unit ?? '';

        // Return operator as JSON response
        return response()->json(['operator' => $operator]);
    }



    public function formatprogressold1(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });


        $revisiData = Excel::toArray(new \stdClass(), $file)[0];
        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process the imported data
        $processedData = $this->progressreportexported($revisiData);
        $groupedProcessedData = [];

        $allProjectTypes = ProjectType::pluck('id', 'title')->toArray();

        try {
            foreach ($processedData as $item) {
                $proyek_type_title = trim($item['proyek_type']);
                $unit = $item['unit'];
                $bomnumber = $item['bomnumber'];

                // Skip if proyek_type_title, unit, or bomnumber is empty
                if (empty($proyek_type_title) || empty($unit) || empty($bomnumber)) {
                    continue;
                }

                // Cari proyek_type_id dari array yang sudah diambil sebelumnya
                if (!isset($allProjectTypes[$proyek_type_title])) {
                    continue; // Skip if project type not found
                }

                $proyek_type_id = $allProjectTypes[$proyek_type_title];

                // Buat key untuk pengelompokan data
                $groupKey = $proyek_type_id . '@' . $unit . '@' . $bomnumber;

                // Kelompokkan data ke dalam array berdasarkan key
                if (!isset($groupedProcessedData[$groupKey])) {
                    $groupedProcessedData[$groupKey] = [];
                }

                $groupedProcessedData[$groupKey][] = $item;
            }

            // Hapus data yang memiliki "unit" null dari $processedData
            foreach ($processedData as $key => $item) {
                if ($item["unit"] == null) {
                    unset($processedData[$key]);
                }
            }

            $stringkiriman = "";
            $exportedRecords = [];
            $exportedCount = 0;

            $allBomprogress = Newbom::whereIn('proyek_type_id', array_column($processedData, 'proyek_type_id'))
                ->get()
                ->keyBy(function ($item) {
                    return $item->proyek_type_id . '@' . $item->unit . '@' . $item->BOMnumber;
                });

            $allKodematRecords = Newbomkomat::whereIn('newbom_id', $allBomprogress->pluck('id'))
                ->get()
                ->groupBy('newbom_id');

            foreach ($groupedProcessedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";

                list($proyek_type_id, $unit, $bomnumber) = explode('@', $groupKey);

                $bomprogress = $allBomprogress->get($groupKey);

                if (!$bomprogress) {
                    $bomprogress = Newbom::firstOrCreate([
                        'proyek_type_id' => $proyek_type_id,
                        'unit' => $unit,
                        'BOMnumber' => $bomnumber
                    ]);
                }

                $id = $bomprogress->id;
                $existingReports = $allKodematRecords->get($id, collect())->keyBy(function ($item) {
                    return $item->kodematerial . '@' . $item->material;
                });
                $newRecords = [];
                $updateRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $kodematerial = trim($item['kodematerial']);
                    $keterangan = $item['keterangan'];
                    $material = $item['material'];

                    if (empty($kodematerial)) {
                        continue; // Skip if kodematerial is empty
                    }

                    if (strpos($kodematerial, "\n") !== false || strpos($kodematerial, " ") !== false) {
                        $parts = preg_split('/\r\n|\r|\n| /', $kodematerial);
                        $kodematerial = trim(end($parts));
                    }

                    if (empty($kodematerial)) {
                        continue; // Skip if kodematerial is empty after split
                    }

                    if (strpos(strtolower($keterangan), 'delete') !== false) {
                        $existingRecord = $existingReports->get($kodematerial);
                        if ($existingRecord) {
                            $existingRecord->delete();
                            $bomprogress->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Material deleted',
                                    'kodematerial' => $kodematerial,
                                    'keterangan' => $keterangan,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'materialdeletion',
                            ]);
                        }
                        continue; // Skip if material is deleted
                    }

                    $rev = $item['rev'];
                    if ($rev === null || $rev === '') {
                        continue; // Skip if rev is empty or null
                    }

                    $key = $kodematerial . '@' . $material;
                    $existingRecord = $existingReports->get($key);

                    if ($existingRecord) {
                        if ($this->compareRevisions($rev, $existingRecord->rev)) {
                            $existingRecord->material = $item['material'];
                            $existingRecord->status = $item['status'] ?? $existingRecord->status;
                            $existingRecord->rev = $rev;
                            $updateRecords[] = $existingRecord;

                            $historyRecords[] = [
                                'newbomkomat_id' => $existingRecord->id,
                                'kodematerial' => $kodematerial,
                                'material' => $item['material'],
                                'status' => $item['status'] ?? $existingRecord->status,
                                'rev' => $rev,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $exportedRecords[] = $item;
                            $exportedCount++;
                        }
                    } else {
                        $newRecords[] = [
                            'newbom_id' => $id,
                            'kodematerial' => $kodematerial,
                            'material' => $item['material'],
                            'rev' => $rev,
                            'status' => $item['status'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $historyRecords[] = [
                            'newbomkomat_id' => null,
                            'kodematerial' => $kodematerial,
                            'material' => $item['material'],
                            'status' => $item['status'] ?? '',
                            'rev' => $rev,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $exportedRecords[] = $item;
                        $exportedCount++;
                    }
                }

                if (!empty($newRecords)) {
                    $uniqueRecords = []; // Array untuk menyimpan kombinasi unik dari record

                    foreach ($newRecords as $record) {
                        $kodematerial = $record['kodematerial'];
                        $newbom_id = $record['newbom_id'];

                        // Buat key unik berdasarkan kombinasi kodematerial dan newbom_id
                        $uniqueKey = $kodematerial . '-' . $newbom_id;

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
                        Newbomkomat::insert($newRecordsNoDouble);
                    }

                    $newlyInsertedRecords = Newbomkomat::whereIn('material', array_column($newRecords, 'material'))
                        ->whereIn('kodematerial', array_column($newRecords, 'kodematerial'))
                        ->get()
                        ->keyBy(function ($item) {
                            return $item->material . '@' . $item->kodematerial;
                        });

                    foreach ($historyRecords as &$history) {
                        if (isset($history['material'])) {
                            $key = $history['material'] . '@' . $history['kodematerial'];
                            if (isset($newlyInsertedRecords[$key])) {
                                $history['newbomkomat_id'] = $newlyInsertedRecords[$key]->id;
                            }
                        }
                    }
                }

                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        $record->save();
                    }
                }

                if (!empty($historyRecords)) {
                    Newbomkomathistory::insert($historyRecords);
                }

                $bomprogress->systemLogs()->create([
                    'message' => json_encode(['message' => 'Materials processed', 'count' => count($data)]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'materialprocessing',
                ]);
            }



        } catch (\Exception $e) {

            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Progress report formatted successfully', 'exported_count' => $exportedCount]);
    }


    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });


        $revisiData = Excel::toArray(new \stdClass(), $file)[0];
        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process the imported data
        $processedData = $this->progressreportexported($revisiData);
        $groupedProcessedData = [];

        $allProjectTypes = ProjectType::pluck('id', 'title')->toArray();
        $newRecordsall = [];
        $updateRecordsall = [];

        try {
            foreach ($processedData as $item) {
                $proyek_type_title = trim($item['proyek_type']);
                $unit = $item['unit'];
                $bomnumber = $item['bomnumber'];

                if (empty($proyek_type_title) || empty($unit) || empty($bomnumber)) {
                    continue;
                }

                if (!isset($allProjectTypes[$proyek_type_title])) {
                    continue;
                }

                $proyek_type_id = $allProjectTypes[$proyek_type_title];
                $groupKey = $proyek_type_id . '@' . $unit . '@' . $bomnumber;

                if (!isset($groupedProcessedData[$groupKey])) {
                    $groupedProcessedData[$groupKey] = [];
                }

                $groupedProcessedData[$groupKey][] = $item;
            }

            foreach ($processedData as $key => $item) {
                if ($item["unit"] == null) {
                    unset($processedData[$key]);
                }
            }

            $exportedRecords = [];
            $exportedCount = 0;

            $allBomprogress = Newbom::whereIn('proyek_type_id', array_column($processedData, 'proyek_type_id'))
                ->get()
                ->keyBy(function ($item) {
                    return $item->proyek_type_id . '@' . $item->unit . '@' . $item->BOMnumber;
                });

            foreach ($groupedProcessedData as $groupKey => $data) {
                list($proyek_type_id, $unit, $bomnumber) = explode('@', $groupKey);

                $bomprogress = $allBomprogress->get($groupKey);
                if (!$bomprogress) {
                    $bomprogress = Newbom::firstOrCreate([
                        'proyek_type_id' => $proyek_type_id,
                        'unit' => $unit,
                        'BOMnumber' => $bomnumber
                    ]);
                }

                $id = $bomprogress->id;
                $kodematerialList = array_column($data, 'kodematerial');

                $allKodematRecords = Newbomkomat::where('newbom_id', $id)
                    ->whereIn('kodematerial', $kodematerialList)
                    ->get()
                    ->groupBy('kodematerial');

                $newRecords = [];
                $updateRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $kodematerial = trim($item['kodematerial']);
                    $keterangan = $item['keterangan'];
                    $material = $item['material'];

                    if (empty($kodematerial)) {
                        continue;
                    }

                    if (strpos($kodematerial, "\n") !== false || strpos($kodematerial, " ") !== false) {
                        $parts = preg_split('/\r\n|\r|\n| /', $kodematerial);
                        $kodematerial = trim(end($parts));
                    }

                    if (empty($kodematerial)) {
                        continue;
                    }

                    if (strpos(strtolower($keterangan), 'delete') !== false) {
                        $existingRecord = $allKodematRecords->get($kodematerial)->first();
                        if ($existingRecord) {
                            $existingRecord->delete();
                            $bomprogress->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Material deleted',
                                    'kodematerial' => $kodematerial,
                                    'keterangan' => $keterangan,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'materialdeletion',
                            ]);
                        }
                        continue;
                    }

                    $rev = $item['rev'];
                    if ($rev === null || $rev === '') {
                        continue;
                    }

                    $kodematerialRecords = $allKodematRecords->get($kodematerial);
                    if ($kodematerialRecords && $kodematerialRecords->isNotEmpty()) {
                        $existingRecord = $kodematerialRecords->first();
                        if ($this->compareRevisions($rev, $existingRecord->rev)) {
                            $existingRecord->material = $item['material'];
                            $existingRecord->status = $item['status'] ?? $existingRecord->status;
                            $existingRecord->rev = $rev;
                            $updateRecords[] = $existingRecord;
                            $updateRecordsall[] = $existingRecord;

                            $historyRecords[] = [
                                'newbomkomat_id' => $existingRecord->id,
                                'kodematerial' => $kodematerial,
                                'material' => $item['material'],
                                'status' => $item['status'] ?? $existingRecord->status,
                                'rev' => $rev,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $exportedRecords[] = $item;
                            $exportedCount++;
                        }
                    } else {
                        $datanewrecords = [
                            'newbom_id' => $id,
                            'kodematerial' => $kodematerial,
                            'material' => $item['material'],
                            'rev' => $rev,
                            'status' => $item['status'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $newRecords[] = $datanewrecords;
                        $newRecordsall[] = $datanewrecords;

                        $exportedRecords[] = $item;
                        $exportedCount++;
                    }
                }

                if (!empty($newRecords)) {
                    Newbomkomat::insert($newRecords);

                    $newlyInsertedRecords = Newbomkomat::whereIn('material', array_column($newRecords, 'material'))
                        ->whereIn('kodematerial', array_column($newRecords, 'kodematerial'))
                        ->get()
                        ->keyBy(function ($item) {
                            return $item->material . '@' . $item->kodematerial;
                        });

                    foreach ($historyRecords as &$history) {
                        $key = $history['material'] . '@' . $history['kodematerial'];
                        if (isset($newlyInsertedRecords[$key])) {
                            $history['newbomkomat_id'] = $newlyInsertedRecords[$key]->id;
                        }
                    }
                }

                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        $record->save();
                    }
                }

                if (!empty($historyRecords)) {
                    Newbomkomathistory::insert($historyRecords);
                }

                $bomprogress->systemLogs()->create([
                    'message' => json_encode(['message' => 'Materials processed', 'count' => count($historyRecords)]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'materialprocessing',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // Returning only successfully inserted or updated records
        return response()->json([
            'message' => 'Materials processed successfully',
            'data' => [
                'newRecords' => $newRecordsall,
                'updatedRecords' => $updateRecordsall,
            ]
        ]);
    }

    public function formatupdateprogressold1(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            // Mengambil file dari request
            $file = $request->file('file');

            // Membaca data dari file Excel
            $revisiData = Excel::toArray(new \stdClass(), $file)[0];

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            // Memproses data yang diimpor
            $processedData = $this->updatekomatprogressexported($revisiData);
            foreach ($processedData as $item) {
                $kodematerial = trim($item['kodematerial']);
                $bomnumber = trim($item['bomnumber']);

                $newbom = Newbom::where('BOMnumber', $bomnumber)->first();
                $bomnumbervalue = $newbom->BOMnumber;
                $newbomkomat = Newbomkomat::where('kodematerial', $kodematerial)->where('newbom_id', $bomnumbervalue)->first();

                $idsnewprogressrepots = [];
                foreach ($item['spesifikasi'] as $spesifikasi) {

                    $newprogressreport = Newprogressreport::where('nodokumen', $spesifikasi)->first();
                    $idsnewprogressrepots[] = $newprogressreport->id;

                }
                $newbomkomat->newprogressreports()->attach($idsnewprogressrepots);
            }


        } catch (\Exception $e) {

        }

        // Mengembalikan hanya data yang berhasil dimasukkan atau diperbarui

    }

    public function formatupdateprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            // Mengambil file dari request
            $file = $request->file('file');

            // Membaca data dari file Excel
            $revisiData = Excel::toArray(new \stdClass(), $file)[0];

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            // Memproses data yang diimpor
            $processedData = $this->updatekomatprogressexported($revisiData);

            // Mengumpulkan data yang diperlukan untuk batch query
            $bomnumbers = array_unique(array_column($processedData, 'bomnumber'));
            $kodematerials = array_unique(array_column($processedData, 'kodematerial'));

            // Query untuk semua Newbom berdasarkan BOMnumber
            $newboms = Newbom::whereIn('BOMnumber', $bomnumbers)->get()->keyBy('BOMnumber');

            // Query untuk semua Newbomkomat berdasarkan kodematerial dan newbom_id
            $newbomkomats = Newbomkomat::whereIn('kodematerial', $kodematerials)
                ->whereIn('newbom_id', $newboms->pluck('id')->toArray())
                ->get()
                ->keyBy(fn($komat) => $komat->kodematerial . '|' . $komat->newbom_id);

            // Mengumpulkan semua spesifikasi untuk batch query
            $allSpesifikasi = [];
            foreach ($processedData as $item) {
                $allSpesifikasi = array_merge($allSpesifikasi, $item['spesifikasi']);
            }

            $newprogressreports = Newprogressreport::whereIn('nodokumen', $allSpesifikasi)->get()->keyBy('nodokumen');

            // Memproses data
            foreach ($processedData as $item) {
                $kodematerial = trim($item['kodematerial']);
                $bomnumber = trim($item['bomnumber']);

                // Ambil data Newbom dan Newbomkomat dari koleksi
                $newbom = $newboms[$bomnumber] ?? null;
                if (!$newbom) {
                    continue; // Skip jika newbom tidak ditemukan
                }

                $newbomkomatKey = $kodematerial . '|' . $newbom->id;
                $newbomkomat = $newbomkomats[$newbomkomatKey] ?? null;
                if (!$newbomkomat) {
                    continue; // Skip jika newbomkomat tidak ditemukan
                }

                $idsnewprogressrepots = [];
                foreach ($item['spesifikasi'] as $spesifikasi) {
                    $newprogressreport = $newprogressreports[$spesifikasi] ?? null;
                    if ($newprogressreport) {
                        $idsnewprogressrepots[] = $newprogressreport->id;
                    }
                }

                // Attach IDs jika ada
                if (!empty($idsnewprogressrepots)) {
                    $newbomkomat->newprogressreports()->attach($idsnewprogressrepots);
                }
            }

            return response()->json(['success' => 'Data processed successfully.'], 200);
        } catch (\Exception $e) {
            \Log::error('Error processing progress update: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }
    }




    public function progressreportexported($importedData)
    {
        $revisiData = [];

        try {
            foreach ($importedData as $row) {
                $proyek_type = trim($row[1] ?? ""); //B
                $unit = $this->perpanjangan(trim($row[2] ?? "")); //C
                $bomnumber = trim($row[3] ?? "");
                $rev = (string) trim($row[4] ?? ""); // Ensure rev is treated as a string
                $kodematerial = trim($row[5] ?? "");

                if (strpos($kodematerial, "\n") !== false || strpos($kodematerial, " ") !== false) {
                    $parts = preg_split('/\r\n|\r|\n| /', $kodematerial);
                    $kodematerial = trim(end($parts));
                }

                $material = trim($row[6] ?? "");
                $status = trim($row[7] ?? "");
                $keterangan = trim($row[8] ?? "");

                if (strpos(strtolower($keterangan), 'delete') !== false) {
                    $keterangan = 'delete';
                }

                if (empty($kodematerial)) {
                    // Skip processing if kodematerial is empty
                    continue;
                }

                if ($proyek_type !== "" && $unit !== "" && $bomnumber !== "" && $kodematerial !== "") {
                    // Append data to the revisiData array
                    $revisiData[] = [
                        'proyek_type' => $proyek_type,
                        'unit' => $unit,
                        'bomnumber' => $bomnumber,
                        'rev' => $rev,
                        'kodematerial' => $kodematerial,
                        'material' => $material,
                        'status' => $status,
                        'keterangan' => $keterangan,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }

    public function updatekomatprogressexported($importedData)
    {
        $revisiData = [];

        try {
            foreach ($importedData as $row) {
                // Validasi bahwa elemen array yang diperlukan tersedia
                $bomnumber = isset($row[1]) ? trim($row[1]) : "";
                $kodematerial = isset($row[3]) ? trim($row[3]) : "";
                $spesifikasi = isset($row[4]) ? explode(",", trim($row[4])) : [];

                // Pastikan data tidak kosong
                if (!empty($bomnumber) && !empty($kodematerial) && !empty($spesifikasi)) {
                    $revisiData[] = [
                        'bomnumber' => $bomnumber,
                        'kodematerial' => $kodematerial,
                        'spesifikasi' => $spesifikasi,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }




    private function compareRevisions($newRev, $oldRev)
    {
        // Define the custom revision order
        $revisionOrder = array_merge(['0'], range('A', 'Z'));

        // Get the index of the new and old revisions
        $newRevIndex = array_search($newRev, $revisionOrder);
        $oldRevIndex = array_search($oldRev, $revisionOrder);

        // Return true if new revision is newer (has a higher index) than the old one
        return $newRevIndex > $oldRevIndex;
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        } elseif ($jenisupload == "formatupdateprogress") {
            $hasil = $this->formatupdateprogress($request);
        } elseif ($jenisupload == "formatrencana") {
            $hasil = $this->importExcel($request);
        }
        return $hasil;
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

    public function search(Request $request)
    {
        // Validasi input pencarian
        $validatedData = $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $validatedData['query'];

        // Ambil data dari database dengan eager loading untuk relasi
        $results = Newbomkomat::with(['newbom.projectType'])
            ->where('material', 'LIKE', '%' . $query . '%')
            ->orWhere('kodematerial', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";

            // Looping melalui hasil pencarian dan susun dalam format teks
            foreach ($results as $result) {
                $unit = $result->newbom->unit ?? "Tidak Diketahui";
                $project = $result->newbom->projectType->title ?? "Tidak Diketahui";

                $textResult .= "📌 *Material*: " . ($result->material ?? '-') . "\n";
                $textResult .= "🔄 *Kode Material*: " . ($result->kodematerial ?? '-') . "\n";
                $textResult .= "🏢 *Unit*: " . $unit . "\n";
                $textResult .= "🗷️ *Project*: " . $project . "\n";
                $textResult .= "----------------------------------\n\n"; // Garis pemisah antar dokumen
            }
        } else {
            // Jika tidak ada hasil, tambahkan pesan "Tidak ada hasil"
            $textResult = "⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }

    public function searchkomat(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if ($query) {
            $results = Newbomkomat::with(['newbom.projectType'])
                ->where('material', 'LIKE', '%' . $query . '%')
                ->orWhere('kodematerial', 'LIKE', '%' . $query . '%')
                ->get();
        }

        return view('newbom.search_results', compact('results', 'query'));
    }

    public function downloadbom($id)
    {
        return Excel::download(new NewbomExport($id), 'newbom.xlsx');
    }



}
