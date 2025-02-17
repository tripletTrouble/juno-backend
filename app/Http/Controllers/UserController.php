<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\InteractsWithJson;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use InteractsWithJson;

    public function index(Request $request)
    {
        $users = User::when(
            $request->has('q'),
            fn(Builder $builder) =>
            $builder->where('name', 'like', sprintf('%%%s%%', $request->input('q')))
                ->orWHere('email', 'like', sprintf('%%%s%%', $request->input('q')))
        )
        ->orderBy('name')
        ->paginate('10');

        return $this->sendJson($users);
    }

    public function me(Request $request)
    {
        return $this->sendJson($request->user());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|string|unique:users,email',
            'name' => 'required|string|max:100',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);

        User::create($data);

        return $this->sendJson(null, 201, 'User has been created');
    }
}
