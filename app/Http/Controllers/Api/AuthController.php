<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/auth/register  (Admin Only)
    public function register(Request $request): JsonResponse
    {
        $username = trim($request->input('username', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $fullName = trim($request->input('full_name', ''));
        // Admin dapat menentukan role; default = 'user'
        $role     = in_array($request->input('role'), ['admin', 'user']) ? $request->input('role') : 'user';

        if (empty($username) || empty($email) || empty($password)) {
            return response()->json(['error' => 'Username, email, dan password wajib diisi.'], 400);
        }
        if (strlen($username) < 3 || strlen($username) > 50) {
            return response()->json(['error' => 'Username harus 3-50 karakter.'], 400);
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json(['error' => 'Username hanya boleh huruf, angka, dan underscore.'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Format email tidak valid.'], 400);
        }
        if (strlen($password) < 6) {
            return response()->json(['error' => 'Password minimal 6 karakter.'], 400);
        }

        if (User::where('username', $username)->orWhere('email', $email)->exists()) {
            return response()->json(['error' => 'Username atau email sudah terdaftar.'], 409);
        }

        $user = User::create([
            'username'  => $username,
            'email'     => $email,
            'password'  => Hash::make($password),
            'full_name' => $fullName,
            'role'      => $role,
        ]);

        ContactInfo::create([
            'user_id'         => $user->id,
            'cta_title'       => 'Mari Berkolaborasi!',
            'cta_description' => 'Hubungi saya untuk bekerjasama.',
        ]);

        // Tidak auto-login — admin yang membuat akun ini
        return response()->json([
            'success' => true,
            'message' => "Akun '{$username}' berhasil dibuat!",
            'user'    => [
                'id'        => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'role'      => $user->role,
            ],
        ]);
    }

    // POST /api/auth/login
    public function login(Request $request): JsonResponse
    {
        $login    = trim($request->input('login', ''));
        $password = $request->input('password', '');

        if (empty($login) || empty($password)) {
            return response()->json(['error' => 'Username/email dan password wajib diisi.'], 400);
        }

        $user = User::where('username', $login)->orWhere('email', $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Username/email atau password salah.'], 401);
        }

        session([
            'user_id'  => $user->id,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'user'    => [
                'id'            => $user->id,
                'username'      => $user->username,
                'full_name'     => $user->full_name,
                'role'          => $user->role,
                'profile_photo' => $user->profile_photo,
            ],
        ]);
    }

    // POST /api/auth/logout
    public function logout(): JsonResponse
    {
        session()->flush();
        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }

    // GET /api/auth/check
    public function check(): JsonResponse
    {
        if (!session()->has('user_id')) {
            return response()->json(['logged_in' => false]);
        }

        $user = User::find(session('user_id'));
        if (!$user) {
            session()->flush();
            return response()->json(['logged_in' => false]);
        }

        return response()->json([
            'logged_in' => true,
            'user'      => [
                'id'            => $user->id,
                'username'      => $user->username,
                'full_name'     => $user->full_name,
                'role'          => $user->role,
                'profile_photo' => $user->profile_photo,
            ],
        ]);
    }
}
