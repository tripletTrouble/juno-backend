<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\InteractsWithJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use InteractsWithJson;

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($data)) {
            $token = $request->user()->createToken('api-token');

            return $this->sendJson([
                'token' => $token->plainTextToken
            ]);
        }

        return $this->sendJson(null, 403, 'Authentication failed!', [
            'email' => 'Kredensial yang diberikan tidak ditemukan di sistem'
        ]);
    }
}
