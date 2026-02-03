<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Ensure default settings exist (Simple seeder logic on the fly if empty)
        if (Setting::count() === 0) {
            $defaults = [
                ['key' => 'toko_nama', 'value' => 'Koperasi Kita', 'label' => 'Nama Koperasi', 'type' => 'text'],
                ['key' => 'toko_alamat', 'value' => 'Jl. Pendidikan No. 1', 'label' => 'Alamat Koperasi', 'type' => 'textarea'],
                ['key' => 'toko_telepon', 'value' => '08123456789', 'label' => 'Nomor Telepon', 'type' => 'text'],
                ['key' => 'stok_min_default', 'value' => '5', 'label' => 'Batas Peringatan Stok Minimum', 'type' => 'number'],
            ];
            foreach ($defaults as $d) {
                Setting::create($d);
            }
        }

        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        \Illuminate\Support\Facades\Cache::flush(); // Clear setup cache

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
