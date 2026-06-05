<?php

namespace App\Http\Controllers;

use App\Models\FloodLocation;
use App\Models\FloodPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloodPhotoController extends Controller
{
    public function index(FloodLocation $floodLocation)
    {
        return response()->json($floodLocation->photos);
    }

    public function store(Request $request, FloodLocation $floodLocation)
    {
        $this->authorize('update', $floodLocation);

        $request->validate([
            'photo' => 'required|image|max:10240',
            'caption' => 'nullable|string',
            'photo_date' => 'nullable|date',
        ]);

        $path = $request->file('photo')->store(
            'floods/' . $floodLocation->id,
            'public'
        );

        $photo = FloodPhoto::create([
            'flood_location_id' => $floodLocation->id,
            'photo_path' => $path,
            'caption' => $request->caption,
            'photo_date' => $request->photo_date ?? now(),
        ]);

        return response()->json($photo, 201);
    }

    public function destroy(FloodPhoto $photo)
    {
        $this->authorize('update', $photo->floodLocation);

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}
