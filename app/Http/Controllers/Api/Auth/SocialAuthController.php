<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return response()->json([
            'url' => Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl()
        ]);
    }    

    public function handleGoogleCallback(Request $request)
    {        
        try {

            if (!$request->has('code')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authorization code tidak ditemukan'
                ], 400);
            }
            
            // Dapatkan user info dari Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // Cek apakah user sudah terdaftar berdasarkan google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User sudah terdaftar - Login
                $token = auth()->guard('api')->login($user) ?: JWTAuth::fromUser($user);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ],
                    'token' => $token
                ], 200);
            } else {
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(16)), // Random password
                    'email_verified_at' => Carbon::now(),
                ]);                

                // Generate JWT token (uses api guard configured for jwt)
                $token = auth()->guard('api')->login($newUser) ?: JWTAuth::fromUser($newUser);

                return response()->json([
                'success' => true,
                'user' => $newUser,
                'token' => $token
                ], 200);
            }                
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function loginWithGoogleToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $response = Http::get(
                'https://oauth2.googleapis.com/tokeninfo',
                ['id_token' => $request->token]
            );

            if (!$response->ok()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token Google tidak valid',
                ], 401);
            }

            $payload = $response->json();

            // Validasi audience
            if ($payload['aud'] !== config('services.google.client_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Client ID tidak cocok',
                ], 401);
            }

            // Cari user berdasarkan google_id
            $user = User::where('google_id', $payload['sub'])->first();

            // 🔹 JIKA USER BELUM ADA
            if (!$user) {
                $user = User::where('email', $payload['email'])->first();

                if ($user) {
                    // 🔁 USER LAMA → UPDATE
                    $user->update([
                        'google_id' => $payload['sub'],
                        'avatar' => $payload['picture'] ?? $user->avatar,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                } else {
                    // 🆕 USER BARU
                    $user = User::create([
                        'name' => $payload['name'],
                        'email' => $payload['email'],
                        'google_id' => $payload['sub'],
                        'avatar' => $payload['picture'] ?? null,
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // 🔐 Generate JWT
            $token = auth()->guard('api')->login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Login Google berhasil',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    
}
