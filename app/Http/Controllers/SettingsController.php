<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'system_title' => 'required|string|max:255',
        ]);

        Setting::updateOrCreate(['key' => 'system_title'], ['value' => $validated['system_title']]);
        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }
}