<?php

namespace App\Http\Controllers;

use App\Models\Wagroupnumber;
use Illuminate\Http\Request;

class WagroupnumberController extends Controller
{
    public function index()
    {
        $groups = Wagroupnumber::all();
        return view('wagroupnumbers.index', compact('groups'));
    }

    public function create()
    {
        return view('wagroupnumbers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'groupname' => 'required|string|max:255',
            'number' => 'required|string',
        ]);

        Wagroupnumber::create($validated);

        return redirect()->route('wagroupnumbers.index')->with('success', 'Group added successfully.');
    }

    public function edit(Wagroupnumber $wagroupnumber)
    {
        return view('wagroupnumbers.edit', compact('wagroupnumber'));
    }

    public function update(Request $request, Wagroupnumber $wagroupnumber)
    {
        $validated = $request->validate([
            'groupname' => 'required|string|max:255',
            'number' => 'required|string|max:15',
        ]);

        $wagroupnumber->update($validated);

        return redirect()->route('wagroupnumbers.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(Wagroupnumber $wagroupnumber)
    {
        $wagroupnumber->delete();

        return redirect()->route('wagroupnumbers.index')->with('success', 'Group deleted successfully.');
    }

    public function verify(Wagroupnumber $wagroupnumber)
    {
        $wagroupnumber->update(['isverified' => true]);

        return redirect()->route('wagroupnumbers.index')->with('success', 'Group verified successfully.');
    }
}
