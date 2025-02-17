<?php

namespace App\Http\Controllers;
use App\Models\Forum;
use App\Models\Category;
use App\Models\HazardLog;
use App\Models\CollectFile;
use Illuminate\Http\Request;
use App\Models\HazardLogFeedback;
use Illuminate\Routing\Controller;
use App\Models\HazardLogReductionMeasure;
use Illuminate\Support\Facades\Cache;

class HazardLogController extends Controller
{
    public function index()
    {
        $hazardLogs = HazardLog::infoplus();
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $filteredhazardLogs = collect($hazardLogs)->where('proyek_type', $listproject[$i])->all();
            // Simpan dokumen yang telah difilter ke dalam revisiall
            $revisiall[$key]['hazardLogs'] = $filteredhazardLogs;
        }

        return view('hazard_logs.index', compact('hazardLogs', 'revisiall'));
    }

    public function create()
    {
        // Ambil kategori unit under pe
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Hapus tanda kutip ganda tambahan
        $listpic = json_decode($categoryproject, true);


        $categoryproject1 = Category::where('category_name', 'project')->pluck('category_member');
        $categoryprojectbaru = json_decode($categoryproject1, true)[0];
        $categoryprojectbaru1 = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
        $listproject = json_decode($categoryprojectbaru1, true);
        return view('hazard_logs.create', compact('listproject', 'listpic'));
    }

    public function store(Request $request)
    {
        // Custom validation
        $request->validate([
            'hazard_ref' => 'required|string|max:255',
            'proyek_type' => 'nullable|string|max:255',
            'operating_mode' => 'nullable|string|max:255',
            'system' => 'nullable|string|max:255',
            'hazard' => 'nullable|string|max:255',
            'hazard_cause' => 'nullable|string|max:255',
            'accident' => 'nullable|string|max:255',
            'IF' => 'nullable|string|max:255',
            'IS' => 'nullable|string|max:255',
            'risk_reduction_measures' => 'nullable|string|max:255',
            'resolution_status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'haz_owner' => 'nullable|string|max:255',
            'hazard_status' => 'nullable|string|max:255',
            'RF' => 'nullable|string|max:255',
            'RS' => 'nullable|string|max:255',
            'RR' => 'nullable|string|max:255',
            'IR' => 'nullable|string|max:255',
            'verification_evidence_reference' => 'nullable|string|max:255',
            'validation_evidence_reference' => 'nullable|string|max:255',
            'comments' => 'nullable|string|max:255',
            'hazard_unit' => 'nullable|array',
            'due_date' => 'nullable|date', // Validasi untuk due_date
        ], [
            'hazard_ref.required' => 'Hazard Ref harus diisi.',
            'hazard_ref.string' => 'Hazard Ref harus berupa teks.',
            'hazard_ref.max' => 'Hazard Ref tidak boleh melebihi 255 karakter.',
            'due_date.date' => 'Due Date harus berupa tanggal yang valid.', // Pesan validasi untuk due_date
        ]);

        // Ubah array hazard_unit menjadi JSON
        $hazardUnitJson = json_encode($request->hazard_unit);

        // Jika validasi berhasil, simpan data
        $hazardLog = new HazardLog();
        $hazardLog->hazard_ref = $request->hazard_ref;
        $hazardLog->proyek_type = $request->proyek_type;
        $hazardLog->operating_mode = $request->operating_mode;
        $hazardLog->system = $request->system;
        $hazardLog->hazard = $request->hazard;
        $hazardLog->hazard_cause = $request->hazard_cause;
        $hazardLog->accident = $request->accident;
        $hazardLog->IF = $request->IF;
        $hazardLog->IS = $request->IS;
        $hazardLog->IR = $request->IR;
        $hazardLog->resolution_status = $request->resolution_status;
        $hazardLog->source = $request->source;
        $hazardLog->haz_owner = $request->haz_owner;
        $hazardLog->hazard_status = $request->hazard_status;
        $hazardLog->RF = $request->RF;
        $hazardLog->RS = $request->RS;
        $hazardLog->RR = $request->RR;
        $hazardLog->verification_evidence_reference = $request->verification_evidence_reference;
        $hazardLog->validation_evidence_reference = $request->validation_evidence_reference;
        $hazardLog->comments = $request->comments;
        $hazardLog->hazard_unit = $hazardUnitJson;
        $hazardLog->due_date = $request->due_date; // Simpan due_date
        $hazardLog->status = "Terbuka";
        $hazardLog->save();

        if ($request->has('hazard_unit') && $request->has('reduction_measures')) {
            foreach ($request->hazard_unit as $index => $unitName) {
                $reductionMeasure = $request->reduction_measures[$index];

                $hazardLogReductionMeasure = new HazardLogReductionMeasure();
                $hazardLogReductionMeasure->hazard_log_id = $hazardLog->id;
                $hazardLogReductionMeasure->unit_name = $unitName;
                $hazardLogReductionMeasure->reduction_measure = $reductionMeasure;
                $hazardLogReductionMeasure->status = 'needanswer';
                $hazardLogReductionMeasure->save();
            }
        }

        return redirect()->route('hazard_logs.index')->with('success', 'Hazard Log created successfully.');
    }


    public function viewfeedback($id, $level)
    {
        $hazardLog = HazardLog::findOrFail($id);
        $kind = 'feedback';
        return view('hazard_logs.feedback', compact('hazardLog', 'kind', 'level'));
    }

    public function viewcombine($id, $level)
    {
        $hazardLog = HazardLog::findOrFail($id);
        $kind = 'combine';
        return view('hazard_logs.feedback', compact('hazardLog', 'kind', 'level'));
    }

    public function submitFeedback(Request $request, $id)
    {
        $useronly = auth()->user()->name;
        // Temukan hazard log berdasarkan ID
        $hazardLog = HazardLog::findOrFail($id);

        // Validasi input
        $request->validate([
            'pic' => 'required|string',
            'author' => 'required|string',
            'email' => 'required|email',
            'conditionoffile' => 'required|string',
            'file.*' => 'nullable|file|max:2048', // Ubah sesuai kebutuhan ukuran file
        ]);

        // Simpan feedback
        $feedback = new HazardLogFeedback();
        $feedback->hazard_log_id = $hazardLog->id;
        $feedback->pic = $request->pic;
        $feedback->author = $request->author;
        $feedback->level = $request->level ?? '';
        $feedback->email = $request->email;
        $feedback->comment = $request->comment;
        $feedback->conditionoffile = $request->conditionoffile;
        $feedback->conditionoffile2 = $request->conditionoffile2;
        $feedback->save();

        // Handle multiple file uploads
        if ($request->hasFile('filenames')) {
            foreach ($request->file('filenames') as $file) {
                $filename = $file->getClientOriginalName();
                $filename = $file->getClientOriginalName();
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                $fileFormat = $file->getClientOriginalExtension();
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $useronly . '.' . $fileFormat;
                $count = 1;
                $filename = $filenameWithUserAndFormat;

                while (CollectFile::where('filename', $filename)->exists()) {
                    $filename = $filenameWithoutExtension . '_' . $useronly . '_' . $count . '.' . $fileFormat;
                    $count++;
                }

                $path = $file->storeAs('uploads', $filename);

                // Simpan file terkait
                $hazardLogFile = new CollectFile();
                $hazardLogFile->filename = $filename;
                $hazardLogFile->link = $path;
                $hazardLogFile->collectable_id = $feedback->id; // Menghubungkan file dengan feedback
                $hazardLogFile->collectable_type = HazardLogFeedback::class; // Tipe polimorfik
                $hazardLogFile->save();
            }
        }

        return redirect()->route('hazard_logs.show', $hazardLog->id)->with('success', 'Feedback submitted successfully.');
    }

    public function deletestatus($id)
    {
        $hazardLog = HazardLog::findOrFail($id);
        $hazardLog->status = 'Terhapus';
        $hazardLog->save();
        return redirect()->route('hazard_logs.show', $hazardLog->id)->with('success', 'Feedback submitted successfully.');
    }

    public function destroyFeedback($hazardLogId, $feedbackId)
    {
        try {
            $hazardLog = HazardLog::findOrFail($hazardLogId);
            $hazardLogFeedback = HazardLogFeedback::findOrFail($feedbackId);

            // Delete associated files (if needed)
            $hazardLogFeedback->hazardLogFiles()->delete();

            // Delete the feedback
            $hazardLogFeedback->delete();

            return redirect()->route('hazard_logs.show', $hazardLogId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }

    public function approveFeedback($hazardLogId, $feedbackId)
    {
        try {
            $hazardLog = HazardLog::findOrFail($hazardLogId);
            $hazardLogFeedback = HazardLogFeedback::findOrFail($feedbackId);
            $hazardLogFeedback->conditionoffile = "approve";
            $hazardLogFeedback->update();
            return redirect()->route('hazard_logs.show', $hazardLogId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }
    public function rejectFeedback($hazardLogId, $feedbackId)
    {
        try {
            $hazardLog = HazardLog::findOrFail($hazardLogId);
            $hazardLogFeedback = HazardLogFeedback::findOrFail($feedbackId);
            $hazardLogFeedback->conditionoffile = "reject";
            $hazardLogFeedback->update();
            return redirect()->route('hazard_logs.show', $hazardLogId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $hazardLog = HazardLog::findOrFail($id);
        return view('hazard_logs.edit', compact('hazardLog'));
    }

    public function update(Request $request, $id)
    {
        $hazardLog = HazardLog::findOrFail($id);

        $validatedData = $request->validate([
            'hazard_ref' => 'required|string|max:255',
            'operating_mode' => 'nullable|string|max:255',
            'system' => 'nullable|string|max:255',
            'hazard' => 'nullable|string|max:255',
            'hazard_cause' => 'nullable|string|max:255',
            'accident' => 'nullable|string|max:255',
            'IF' => 'nullable|string|max:255',
            'IS' => 'nullable|string|max:255',
            'risk_reduction_measures' => 'nullable|string|max:255',
            'resolution_status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'haz' => 'nullable|string|max:255',
            'owner' => 'nullable|string|max:255',
            'hazard_status' => 'nullable|string|max:255',
            'date_updated' => 'nullable|string|max:255',
            'RF' => 'nullable|string|max:255',
            'RS' => 'nullable|string|max:255',
            'RR' => 'nullable|string|max:255',
            'verification_evidence_reference' => 'nullable|string|max:255',
            'validation_evidence_reference' => 'nullable|string|max:255',
            'comments' => 'nullable|string|max:255',
        ]);

        $hazardLog->update($validatedData);

        return redirect()->route('hazard_logs.index')->with('success', 'Hazard Log updated successfully.');
    }

    public function destroy($id)
    {
        $hazardLog = HazardLog::findOrFail($id);
        $hazardLog->delete();

        return redirect()->route('hazard_logs.index')->with('success', 'Hazard Log deleted successfully.');
    }

    public function show($id)
    {
        $hazardLog = HazardLog::with(['hazardlogfeedback.hazardLogFiles', 'reductionMeasures'])->findOrFail($id);
        $hazardUnit = json_decode($hazardLog->hazard_unit, true);
        $data = $hazardLog->getVerificatorData();
        $unitpicvalidation = $data['unitpicvalidation'];
        $unitvalidation = $data['unitvalidation'];
        $ramscombinevalidation = $data['ramscombinevalidation'];
        return view('hazard_logs.show', compact('hazardLog', 'hazardUnit', 'unitpicvalidation', 'unitvalidation', 'ramscombinevalidation'));
    }


    public function approvehazardlog($hazardLogId, $reductionMeasureId)
    {
        $hazardLogReductionMeasure = HazardLogReductionMeasure::findOrFail($reductionMeasureId);
        $hazardLogReductionMeasure->status = 'approve';
        $hazardLogReductionMeasure->save();
        return redirect()->back()->with('success', 'Reduction measure approved.');
    }

    public function rejecthazardlog(Request $request, $hazardLogId, $reductionMeasureId)
    {
        $hazardLogReductionMeasure = HazardLogReductionMeasure::findOrFail($reductionMeasureId);
        $hazardLogReductionMeasure->status = 'reject';
        $hazardLogReductionMeasure->reason = $request->input('reason');
        $hazardLogReductionMeasure->save();
        return redirect()->back()->with('success', 'Reduction measure rejected.');
    }

    public function addhazardlog(Request $request, $hazardLogId, $unitName)
    {
        $hazardLogReductionMeasure = new HazardLogReductionMeasure();
        $hazardLogReductionMeasure->hazard_log_id = $hazardLogId;
        $hazardLogReductionMeasure->unit_name = $unitName;
        $hazardLogReductionMeasure->reduction_measure = $request->input('reason');
        $hazardLogReductionMeasure->status = 'needanswer'; // Atur status sesuai kebutuhan Anda
        $hazardLogReductionMeasure->reason = '';
        $hazardLogReductionMeasure->save();

        return redirect()->back()->with('success', 'Reduction measure uploaded successfully.');
    }



    public function makeforum(Request $request, $hazardLogId, $reductionMeasureId)
    {
        // Validasi input
        $request->validate([
            'topic' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Cari Reduction Measure
        $hazardLogReductionMeasure = HazardLogReductionMeasure::findOrFail($reductionMeasureId);

        // Buat Forum baru
        $forum = new Forum([
            'topic' => $request->input('topic'),
            'description' => $request->input('description'),
            'password' => '',
        ]);

        // Simpan Forum
        $hazardLogReductionMeasure->forums()->save($forum);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Forum berhasil dibuat.');
    }




}
