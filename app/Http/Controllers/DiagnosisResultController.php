<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosisResult;


class DiagnosisResultController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'svm_prediction' => 'required|string',
            'naive_bayes_prediction' => 'required|string',
            'final_diagnosis' => 'required|string',
        ]);

        $diagnosisResult = DiagnosisResult::create([
            'user_id' => $request->user_id,
            'svm_prediction' => $request->svm_prediction,
            'naive_bayes_prediction' => $request->naive_bayes_prediction,
            'final_diagnosis' => $request->final_diagnosis,
        ]);

        return response()->json([
            'message' => 'Diagnosis result saved successfully!',
            'data' => $diagnosisResult
        ], 201);
    }

    public function show($id)
    {
        $diagnosisResult = DiagnosisResult::with('user')->findOrFail($id);

        return response()->json($diagnosisResult);
    }
}

