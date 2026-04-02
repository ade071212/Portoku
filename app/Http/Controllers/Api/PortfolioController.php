<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    // GET /api/portfolio — public visible projects by username
    public function index(Request $request): JsonResponse
    {
        $username = $request->query('user', '');

        if (empty($username)) {
            $firstUser = User::orderBy('id')->first();
            $username = $firstUser ? $firstUser->username : '';
        }

        $projects = Project::whereHas('user', fn($q) => $q->where('username', $username))
            ->where('visible', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['success' => true, 'projects' => $projects]);
    }

    // GET /api/portfolio/all — all projects owned by authenticated user
    public function all(): JsonResponse
    {
        $projects = Project::where('user_id', session('user_id'))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['success' => true, 'projects' => $projects]);
    }

    // POST /api/portfolio — create project
    public function store(Request $request): JsonResponse
    {
        $project = Project::create([
            'user_id'    => session('user_id'),
            'title'      => $request->input('title', ''),
            'tag'        => $request->input('tag', ''),
            'description'=> $request->input('description', ''),
            'image'      => $request->input('image', ''),
            'link'       => $request->input('link', ''),
            'video'      => $request->input('video', ''),
            'visible'    => $request->input('visible', 1),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $project->id,
            'message' => 'Proyek berhasil ditambahkan.',
        ]);
    }

    // PUT /api/portfolio/{id} — update project
    public function update(Request $request, int $id): JsonResponse
    {
        $project = Project::where('id', $id)->where('user_id', session('user_id'))->first();
        if (!$project) {
            return response()->json(['error' => 'Proyek tidak ditemukan.'], 404);
        }

        $project->update([
            'title'      => $request->input('title', ''),
            'tag'        => $request->input('tag', ''),
            'description'=> $request->input('description', ''),
            'image'      => $request->input('image', ''),
            'link'       => $request->input('link', ''),
            'video'      => $request->input('video', ''),
            'visible'    => $request->input('visible', 1),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return response()->json(['success' => true, 'message' => 'Proyek berhasil diperbarui.']);
    }

    // PATCH /api/portfolio/{id}/toggle — toggle visibility
    public function toggle(int $id): JsonResponse
    {
        $project = Project::where('id', $id)->where('user_id', session('user_id'))->first();
        if (!$project) {
            return response()->json(['error' => 'Proyek tidak ditemukan.'], 404);
        }
        $project->update(['visible' => !$project->visible]);
        return response()->json(['success' => true, 'message' => 'Visibilitas diperbarui.']);
    }

    // DELETE /api/portfolio/{id} — delete project
    public function destroy(int $id): JsonResponse
    {
        Project::where('id', $id)->where('user_id', session('user_id'))->delete();
        return response()->json(['success' => true, 'message' => 'Proyek dihapus.']);
    }
}
