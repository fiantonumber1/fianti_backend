<?php

namespace App\Http\Controllers;

use App\Models\InnovationProgress;
use Illuminate\Http\Request;

class InnovationProgressController extends Controller
{
    public function index()
    {
        $innovations = InnovationProgress::all();
        return view('innovation_progress.index', compact('innovations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:innovation_progress,name',
            'description' => 'nullable|string',
            'manual_book_link' => 'nullable|url',
            'flow_chart_link' => 'nullable|url',
            'documentation_link' => 'nullable|url',
        ]);

        InnovationProgress::create($request->all());

        return response()->json(['success' => 'Innovation Progress added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $innovation = InnovationProgress::findOrFail($id);
        $innovation->update($request->only(['name', 'description', 'manual_book_link', 'flow_chart_link', 'documentation_link']));

        return response()->json(['message' => 'Update successful'], 200);
    }


    public function destroy($id)
    {
        $innovation = InnovationProgress::findOrFail($id);
        $innovation->delete();

        return response()->json(['success' => 'Innovation Progress deleted successfully!']);
    }
}
