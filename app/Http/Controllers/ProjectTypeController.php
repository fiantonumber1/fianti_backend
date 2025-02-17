<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{
    public function index()
    {
        $projectTypes = ProjectType::all();
        return view('project_types.index', compact('projectTypes'));
    }

    public function create()
    {
        return view('project_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        ProjectType::create($request->all());

        return redirect()->route('project_types.index')
                         ->with('success', 'Project Type created successfully.');
    }

    public function show(ProjectType $projectType)
    {
        return view('project_types.show', compact('projectType'));
    }

    public function edit(ProjectType $projectType)
    {
        return view('project_types.edit', compact('projectType'));
    }

    public function update(Request $request, ProjectType $projectType)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $projectType->update($request->all());

        return redirect()->route('project_types.index')
                         ->with('success', 'Project Type updated successfully.');
    }

    public function destroy(ProjectType $projectType)
    {
        $projectType->delete();

        return redirect()->route('project_types.index')
                         ->with('success', 'Project Type deleted successfully.');
    }

    public function data()
    {
        $data = ProjectType::all();

        return datatables()->of($data)->addIndexColumn()->addColumn('action', function ($data) {
            return '
            <div class="btn-group">
            <button onclick="editForm(`'. route('project_types.update', $data->id) .'`)" class="btn btn-link text-primary"><i class="fas fa-pencil-alt"></i></button>
            <button onclick="deleteData(`'. route('project_types.destroy', $data->id) .'`)"class="btn btn-link text-danger"><i class="fas fa-trash-alt"></i></button>
            </div>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
}