<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    // GET /api/services — public visible services
    public function index(Request $request): JsonResponse
    {
        $username = $request->query('user', '');

        if (empty($username)) {
            $firstUser = User::orderBy('id')->first();
            $username = $firstUser ? $firstUser->username : '';
        }

        $services = Service::whereHas('user', fn($q) => $q->where('username', $username))
            ->where('visible', true)
            ->orderBy('id')
            ->get();

        return response()->json(['success' => true, 'services' => $services]);
    }

    // GET /api/services/all — all services owned by authenticated user
    public function all(): JsonResponse
    {
        $services = Service::where('user_id', session('user_id'))->orderBy('id')->get();
        return response()->json(['success' => true, 'services' => $services]);
    }

    // POST /api/services — create
    public function store(Request $request): JsonResponse
    {
        $service = Service::create([
            'user_id'     => session('user_id'),
            'title'       => $request->input('title', ''),
            'icon'        => $request->input('icon', 'fa-solid fa-star'),
            'description' => $request->input('description', ''),
            'visible'     => $request->input('visible', 1),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $service->id,
            'message' => 'Layanan ditambahkan.',
        ]);
    }

    // PUT /api/services/{id} — update
    public function update(Request $request, int $id): JsonResponse
    {
        $service = Service::where('id', $id)->where('user_id', session('user_id'))->first();
        if (!$service) {
            return response()->json(['error' => 'Layanan tidak ditemukan.'], 404);
        }

        $service->update([
            'title'       => $request->input('title', ''),
            'icon'        => $request->input('icon', 'fa-solid fa-star'),
            'description' => $request->input('description', ''),
            'visible'     => $request->input('visible', 1),
        ]);

        return response()->json(['success' => true, 'message' => 'Layanan diperbarui.']);
    }

    // PATCH /api/services/{id}/toggle — toggle visibility
    public function toggle(int $id): JsonResponse
    {
        $service = Service::where('id', $id)->where('user_id', session('user_id'))->first();
        if (!$service) {
            return response()->json(['error' => 'Layanan tidak ditemukan.'], 404);
        }
        $service->update(['visible' => !$service->visible]);
        return response()->json(['success' => true, 'message' => 'Visibilitas diperbarui.']);
    }

    // DELETE /api/services/{id} — delete
    public function destroy(int $id): JsonResponse
    {
        Service::where('id', $id)->where('user_id', session('user_id'))->delete();
        return response()->json(['success' => true, 'message' => 'Layanan dihapus.']);
    }
}
