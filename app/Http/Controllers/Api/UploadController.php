<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    // POST /api/upload
    public function upload(Request $request): JsonResponse
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'File tidak ditemukan.'], 400);
        }

        $file = $request->file('file');
        $type = $request->query('type', 'image'); // 'profile' or 'image'

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json(['error' => 'Format file tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.'], 400);
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return response()->json(['error' => 'Ukuran file maksimal 5MB.'], 400);
        }

        $userId = session('user_id');
        $filename = $type . '_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');
        $relativePath = 'storage/' . $path;

        if ($type === 'profile') {
            $user = User::find($userId);
            // Delete old photo
            if ($user->profile_photo) {
                $oldPath = str_replace('storage/', '', $user->profile_photo);
                Storage::disk('public')->delete($oldPath);
            }
            $user->update(['profile_photo' => $relativePath]);
        }

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload.',
            'path'    => $relativePath,
        ]);
    }
}
