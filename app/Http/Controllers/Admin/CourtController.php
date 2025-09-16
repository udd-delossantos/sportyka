<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Court;

class CourtController extends Controller
{
    public function index()
    {
        $courts = Court::all();
        return view('admin.courts.index', compact('courts'));
    }

    public function create()
    {
        return view('admin.courts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sport' => 'required|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,in-use',
        ]);

        Court::create($validated);
        return redirect()->route('admin.courts.index')->with('success', 'Court created successfully.');
    }

    public function edit(Court $court)
    {
        return view('admin.courts.edit', compact('court'));
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sport' => 'required|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,in-use',
        ]);

        $court->update($validated);
        return redirect()->route('admin.courts.index')->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $court->delete();
        return redirect()->route('admin.courts.index')->with('success', 'Court deleted.');
    }
}
