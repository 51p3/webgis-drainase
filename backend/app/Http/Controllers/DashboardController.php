<?php

namespace App\Http\Controllers;

use App\Models\Drainage;
use App\Models\FloodLocation;
use App\Models\News;
use App\Models\DrainagePhoto;
use App\Models\FloodPhoto;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_drainage' => Drainage::count(),
            'total_drainage_length' => Drainage::sum('length') ?? 0,
            'total_flood_locations' => FloodLocation::count(),
            'total_drainage_photos' => DrainagePhoto::count(),
            'total_news' => News::where('status', 'published')->count(),
        ];

        $drainage_per_district = Drainage::select('district_id')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('districts.name')
            ->join('districts', 'drainages.district_id', '=', 'districts.id')
            ->groupBy('district_id', 'districts.name')
            ->get();

        $drainage_condition = Drainage::select('condition')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition')
            ->get();

        $flood_per_district = FloodLocation::select('district_id')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('districts.name')
            ->join('districts', 'flood_locations.district_id', '=', 'districts.id')
            ->groupBy('district_id', 'districts.name')
            ->get();

        $latest_drainages = Drainage::with('district', 'village')
            ->latest()
            ->limit(5)
            ->get();

        $latest_floods = FloodLocation::with('district', 'village')
            ->latest()
            ->limit(5)
            ->get();

        $latest_news = News::where('status', 'published')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'charts' => [
                'drainage_per_district' => $drainage_per_district,
                'drainage_condition' => $drainage_condition,
                'flood_per_district' => $flood_per_district,
            ],
            'latest' => [
                'drainages' => $latest_drainages,
                'floods' => $latest_floods,
                'news' => $latest_news,
            ]
        ]);
    }
}
