<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    // GET /api/profile — get public profile
    public function show(Request $request): JsonResponse
    {
        $username = $request->query('user', '');

        if (empty($username)) {
            if (session()->has('username')) {
                $username = session('username');
            } else {
                $firstUser = User::orderBy('id')->first();
                $username = $firstUser ? $firstUser->username : '';
            }
        }

        if (empty($username)) {
            return response()->json(['error' => 'Tidak ada user ditemukan.'], 404);
        }

        $user = User::where('username', $username)
            ->select('id', 'username', 'full_name', 'bio', 'profile_photo', 'badge', 'headline', 'description', 'role', 'created_at')
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'profile' => $user]);
    }

    // POST /api/profile — update profile (auth required)
    public function update(Request $request): JsonResponse
    {
        $userId = session('user_id');
        $allowedFields = ['full_name', 'bio', 'badge', 'headline', 'description'];
        $data = [];

        foreach ($allowedFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if (empty($data)) {
            return response()->json(['error' => 'Tidak ada data untuk diperbarui.'], 400);
        }

        User::where('id', $userId)->update($data);

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    }
}
