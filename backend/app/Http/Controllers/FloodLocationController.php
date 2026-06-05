<?php

namespace App\Http\Controllers;

use App\Models\FloodLocation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FloodLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
    }

    public function index(Request $request)
    {
        $query = FloodLocation::with('district', 'village', 'photos');

        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        return response()->json(
            $query->paginate($request->per_page ?? 50)
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', FloodLocation::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'flood_depth' => 'required|numeric',
            'flood_duration' => 'nullable|string',
            'cause' => 'nullable|string',
            'description' => 'nullable|string',
            'geometry' => 'required|json',
        ]);

        $flood = FloodLocation::create($validated);

        $this->logActivity('create', 'FloodLocation', $flood->id);

        return response()->json($flood->load('district', 'village', 'photos'), 201);
    }

    public function show(FloodLocation $floodLocation)
    {
        return response()->json($floodLocation->load('district', 'village', 'photos'));
    }

    public function update(Request $request, FloodLocation $floodLocation)
    {
        $this->authorize('update', $floodLocation);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'district_id' => 'sometimes|exists:districts,id',
            'village_id' => 'sometimes|exists:villages,id',
            'flood_depth' => 'sometimes|numeric',
            'flood_duration' => 'nullable|string',
            'cause' => 'nullable|string',
            'description' => 'nullable|string',
            'geometry' => 'sometimes|json',
        ]);

        $floodLocation->update($validated);

        $this->logActivity('update', 'FloodLocation', $floodLocation->id);

        return response()->json($floodLocation->load('district', 'village', 'photos'));
    }

    public function destroy(FloodLocation $floodLocation)
    {
        $this->authorize('delete', $floodLocation);

        $floodLocation->photos()->delete();
        $floodLocation->delete();

        $this->logActivity('delete', 'FloodLocation', $floodLocation->id);

        return response()->json(['message' => 'Flood location deleted successfully']);
    }

    private function logActivity($action, $model, $modelId)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
