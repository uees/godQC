<?php

namespace App\Api;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        // 验证失败返回401
        if (!$token = \Auth::attempt($credentials)) {
            $this->response()->unauthorized("登录失败");
        }

        return $this->respondWithToken($token);
    }

    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function logout()
    {
        auth()->logout();

        return $this->response()->message('退出登录成功');
    }

    public function me()
    {
        $user = auth()->user()->load('roles');

        return UserResource::make($user);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
