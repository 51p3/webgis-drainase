<?php

namespace App\Http\Controllers;

use App\Models\Drainage;
use App\Models\FloodLocation;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function drainages(Request $request)
    {
        $drainages = Drainage::with('district', 'village', 'photos')
            ->whereNotNull('geometry')
            ->get();

        $features = $drainages->map(function ($drainage) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $drainage->id,
                    'name' => $drainage->name,
                    'code' => $drainage->code,
                    'type' => $drainage->type,
                    'condition' => $drainage->condition,
                    'length' => $drainage->length,
                    'width' => $drainage->width,
                    'height' => $drainage->height,
                    'district' => $drainage->district->name,
                    'village' => $drainage->village->name,
                    'photos' => $drainage->photos->pluck('photo_path'),
                ],
                'geometry' => $drainage->geometry,
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function floods(Request $request)
    {
        $floods = FloodLocation::with('district', 'village', 'photos')
            ->whereNotNull('geometry')
            ->get();

        $features = $floods->map(function ($flood) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $flood->id,
                    'name' => $flood->name,
                    'flood_depth' => $flood->flood_depth,
                    'flood_duration' => $flood->flood_duration,
                    'cause' => $flood->cause,
                    'district' => $flood->district->name,
                    'village' => $flood->village->name,
                    'photos' => $flood->photos->pluck('photo_path'),
                ],
                'geometry' => $flood->geometry,
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function districts()
    {
        return response()->json(District::all());
    }

    public function villages(Request $request)
    {
        $query = Village::query();

        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        return response()->json($query->get());
    }
}
