<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin');
    }

    public function show()
    {
        $settings = Setting::all();
        $formatted = $settings->mapWithKeys(fn ($s) => [$s->key => $s->value]);

        return response()->json($formatted);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'sometimes|string|max:255',
            'logo' => 'sometimes|image|max:5120',
            'map_center_lat' => 'sometimes|numeric',
            'map_center_lng' => 'sometimes|numeric',
            'map_zoom' => 'sometimes|integer|min:1|max:20',
            'footer_text' => 'sometimes|string',
            'contact_email' => 'sometimes|email',
            'contact_phone' => 'sometimes|string',
            'contact_address' => 'sometimes|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'logo' && $request->hasFile('logo')) {
                $setting = Setting::firstOrCreate(['key' => $key]);
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $value = $request->file('logo')->store('settings', 'public');
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
