<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index', 'show', 'public');
    }

    public function index(Request $request)
    {
        $query = News::with('user');

        if (!Auth::check() || !Auth::user()->hasRole(['admin', 'super_admin'])) {
            $query->where('status', 'published');
        }

        if ($request->has('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%')
                  ->orWhere('content', 'ilike', '%' . $request->search . '%');
        }

        return response()->json(
            $query->orderBy('published_at', 'desc')->paginate($request->per_page ?? 10)
        );
    }

    public function public(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        $this->authorize('create', News::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);

        $news = News::create($validated);

        $this->logActivity('create', 'News', $news->id);

        return response()->json($news->load('user'), 201);
    }

    public function show(News $news)
    {
        if ($news->status === 'draft' && Auth::user()?->id !== $news->user_id && !Auth::user()?->hasRole(['admin', 'super_admin'])) {
            abort(403);
        }

        return response()->json($news->load('user'));
    }

    public function update(Request $request, News $news)
    {
        $this->authorize('update', $news);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'thumbnail' => 'nullable|image|max:5120',
            'status' => 'sometimes|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $news->update($validated);

        $this->logActivity('update', 'News', $news->id);

        return response()->json($news->load('user'));
    }

    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        if ($news->thumbnail) {
            Storage::disk('public')->delete($news->thumbnail);
        }

        $news->delete();

        $this->logActivity('delete', 'News', $news->id);

        return response()->json(['message' => 'News deleted successfully']);
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
