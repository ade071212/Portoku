<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    // GET /api/contact — get contact info by username
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

        $contact = ContactInfo::whereHas('user', fn($q) => $q->where('username', $username))->first();

        return response()->json(['success' => true, 'contact' => $contact ?: []]);
    }

    // POST /api/contact — upsert contact info
    public function upsert(Request $request): JsonResponse
    {
        $userId = session('user_id');
        $data = [
            'cta_title'       => $request->input('cta_title', ''),
            'cta_description' => $request->input('cta_description', ''),
            'email'           => $request->input('email', ''),
            'linkedin'        => $request->input('linkedin', ''),
            'instagram'       => $request->input('instagram', ''),
            'whatsapp'        => $request->input('whatsapp', ''),
            'tiktok'          => $request->input('tiktok', ''),
        ];

        ContactInfo::updateOrCreate(['user_id' => $userId], $data);

        return response()->json(['success' => true, 'message' => 'Kontak berhasil disimpan.']);
    }
}
