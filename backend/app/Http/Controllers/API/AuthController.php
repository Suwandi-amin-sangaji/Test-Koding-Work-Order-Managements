<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->sendError('Unauthorized', 401);
        }

        $user = $request->user();
        $token = $user->createToken('API Token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token
        ], 'Berhasil login');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse(null, 'Berhasil logout');
    }
}
