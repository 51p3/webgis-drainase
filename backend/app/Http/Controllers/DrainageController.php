<?php

namespace App\Http\Controllers;

use App\Models\Drainage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DrainageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show');
    }

    public function index(Request $request)
    {
        $query = Drainage::with('district', 'village', 'photos');

        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('code', 'ilike', '%' . $request->search . '%');
        }

        return response()->json(
            $query->paginate($request->per_page ?? 50)
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', Drainage::class);

        $validated = $request->validate([
            'code' => 'required|unique:drainages',
            'name' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'type' => 'required|in:U-Ditch,Concrete,Stone Masonry,Earth Channel',
            'condition' => 'required|in:Good,Moderate,Damaged',
            'description' => 'nullable|string',
            'geometry' => 'required|json',
        ]);

        $drainage = Drainage::create($validated);

        $this->logActivity('create', 'Drainage', $drainage->id);

        return response()->json($drainage->load('district', 'village', 'photos'), 201);
    }

    public function show(Drainage $drainage)
    {
        return response()->json($drainage->load('district', 'village', 'photos'));
    }

    public function update(Request $request, Drainage $drainage)
    {
        $this->authorize('update', $drainage);

        $validated = $request->validate([
            'code' => 'sometimes|unique:drainages,code,' . $drainage->id,
            'name' => 'sometimes|string',
            'district_id' => 'sometimes|exists:districts,id',
            'village_id' => 'sometimes|exists:villages,id',
            'length' => 'sometimes|numeric',
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'type' => 'sometimes|in:U-Ditch,Concrete,Stone Masonry,Earth Channel',
            'condition' => 'sometimes|in:Good,Moderate,Damaged',
            'description' => 'nullable|string',
            'geometry' => 'sometimes|json',
        ]);

        $drainage->update($validated);

        $this->logActivity('update', 'Drainage', $drainage->id);

        return response()->json($drainage->load('district', 'village', 'photos'));
    }

    public function destroy(Drainage $drainage)
    {
        $this->authorize('delete', $drainage);

        $drainage->photos()->delete();
        $drainage->delete();

        $this->logActivity('delete', 'Drainage', $drainage->id);

        return response()->json(['message' => 'Drainage deleted successfully']);
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
