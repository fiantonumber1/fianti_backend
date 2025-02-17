<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('unit.index', compact('units'));
    }

    public function create()
    {
        return view('unit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Unit::create($request->all());

        return redirect()->route('unit.index')
                         ->with('success', 'Project Type created successfully.');
    }

    public function show(Unit $unit)
    {
        return view('unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $Unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Unit->update($request->all());

        return redirect()->route('unit.index')
                         ->with('success', 'Project Type updated successfully.');
    }

    public function destroy(Unit $Unit)
    {
        $Unit->delete();

        return redirect()->route('unit.index')
                         ->with('success', 'Project Type deleted successfully.');
    }

    
}