<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Court;
use Illuminate\Support\Facades\Storage;

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
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('courts', 'public');
            }
        }

        $validated['images'] = $imagePaths;
        $court = Court::create($validated);

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
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Start with the court’s current images (array from DB)
    $imagePaths = $court->images ?? [];

    // ✅ Handle deletions
    if ($request->filled('delete_images')) {
        foreach ($request->delete_images as $img) {
            // delete from storage
            Storage::disk('public')->delete($img);

            // remove from array
            $imagePaths = array_diff($imagePaths, [$img]);
        }
    }

    // ✅ Handle new uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePaths[] = $image->store('courts', 'public');
        }
    }

    // Reindex array (important when using array_diff)
    $validated['images'] = array_values($imagePaths);

    // ✅ Update court record
    $court->update($validated);

    return redirect()
        ->route('admin.courts.index')
        ->with('success', 'Court updated successfully.');
}


    public function destroy(Court $court)
    {
        // Optionally delete images from storage
        if (!empty($court->images)) {
            foreach ($court->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $court->delete();
        return redirect()->route('admin.courts.index')->with('success', 'Court deleted.');
    }
}
