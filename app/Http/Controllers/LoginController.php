<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ResponseTrait;

    public function __invoke(LoginRequest $request)
    {
        if (Auth::attempt($request->only(['email', 'password']))) {
            $user = Auth::user();

            $token = $user->createToken($user->email);

            return $this->successResponseJson(['token'=> $token->plainTextToken]);
        }
    }
}
