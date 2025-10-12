<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GcashQrCode;

class GcashQrCodeController extends Controller
{
    public function index()
    {
        $qrs = GcashQrCode::all();
        $activeQr = GcashQrCode::where('is_active', true)->first();
        return view('admin.settings.gcash_qr_codes', compact('qrs', 'activeQr'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gcash_qr' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('gcash_qr')->store('gcash_qr', 'public');

        GcashQrCode::create([
            'file_path' => 'storage/'.$path,
            'is_active' => false,
        ]);

        return back()->with('success', 'QR Code uploaded successfully!');
    }

    public function setActive($id)
    {
        GcashQrCode::query()->update(['is_active' => false]);
        GcashQrCode::where('id', $id)->update(['is_active' => true]);

        return back()->with('success', 'Active QR Code changed successfully!');
    }

    public function destroy($id)
    {
        $qr = GcashQrCode::findOrFail($id);
        if ($qr->is_active) {
            return back()->with('error', 'Cannot delete the active QR Code.');
        }
        $qr->delete();
        return back()->with('success', 'QR Code deleted.');
    }
}
