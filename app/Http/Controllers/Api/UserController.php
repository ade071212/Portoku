<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // GET /api/users — list all users (admin only)
    public function index(): JsonResponse
    {
        $users = User::select('id', 'username', 'email', 'full_name', 'role', 'profile_photo', 'created_at')
            ->orderBy('id')
            ->get();

        return response()->json(['success' => true, 'users' => $users]);
    }

    // POST /api/users/delete — delete user (admin only)
    public function delete(Request $request): JsonResponse
    {
        $id = (int) $request->input('id', 0);

        if ($id === session('user_id')) {
            return response()->json(['error' => 'Tidak bisa menghapus akun sendiri.'], 400);
        }

        $user = User::find($id);
        if ($user) {
            if ($user->profile_photo) {
                $oldPath = str_replace('storage/', '', $user->profile_photo);
                Storage::disk('public')->delete($oldPath);
            }
            $user->delete();
        }

        return response()->json(['success' => true, 'message' => 'User dihapus.']);
    }

    // POST /api/users/make-admin — make user admin
    public function makeAdmin(Request $request): JsonResponse
    {
        $id = (int) $request->input('id', 0);
        User::where('id', $id)->update(['role' => 'admin']);
        return response()->json(['success' => true, 'message' => 'User dijadikan admin.']);
    }

    // POST /api/users/remove-admin — remove admin role
    public function removeAdmin(Request $request): JsonResponse
    {
        $id = (int) $request->input('id', 0);

        if ($id === session('user_id')) {
            return response()->json(['error' => 'Tidak bisa menghapus role admin sendiri.'], 400);
        }

        User::where('id', $id)->update(['role' => 'user']);
        return response()->json(['success' => true, 'message' => 'Role admin dicabut.']);
    }
}
