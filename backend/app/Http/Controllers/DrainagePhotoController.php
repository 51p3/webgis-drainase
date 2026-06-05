<?php

namespace App\Http\Controllers;

use App\Models\Drainage;
use App\Models\DrainagePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrainagePhotoController extends Controller
{
    public function index(Drainage $drainage)
    {
        return response()->json($drainage->photos);
    }

    public function store(Request $request, Drainage $drainage)
    {
        $this->authorize('update', $drainage);

        $request->validate([
            'photo' => 'required|image|max:10240',
            'caption' => 'nullable|string',
            'photo_date' => 'nullable|date',
        ]);

        $path = $request->file('photo')->store(
            'drainages/' . $drainage->id,
            'public'
        );

        $photo = DrainagePhoto::create([
            'drainage_id' => $drainage->id,
            'photo_path' => $path,
            'caption' => $request->caption,
            'photo_date' => $request->photo_date ?? now(),
        ]);

        return response()->json($photo, 201);
    }

    public function destroy(DrainagePhoto $photo)
    {
        $this->authorize('update', $photo->drainage);

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}
