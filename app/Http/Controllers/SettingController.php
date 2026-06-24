<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAllAsArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name'         => ['required', 'string', 'max:100'],
            'store_address'      => ['nullable', 'string', 'max:255'],
            'store_phone'        => ['nullable', 'string', 'max:20'],
            'store_tagline'      => ['nullable', 'string', 'max:255'],
            'receipt_footer'     => ['nullable', 'string', 'max:500'],
            'receipt_paper_size' => ['required', 'in:58,80'],
            'tax_rate'           => ['required', 'integer', 'min:0', 'max:100'],
            'qris_image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        if ($request->hasFile('qris_image')) {
            $old = Setting::get('qris_image');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $data['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        } else {
            unset($data['qris_image']);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log('update', 'Pengaturan toko diperbarui', 'Setting');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
