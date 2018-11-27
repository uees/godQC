<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    protected static $passwordClient;

    public function __construct()
    {
        $this->middleware('auth:api')->only(['me', 'logout', 'logoutAnywhere']);

        if (!isset(static::$passwordClient)) {
            static::$passwordClient = Client::where('password_client', 1)
                ->where('revoked', 0)
                ->whereNull('user_id')
                ->first();

            if (is_null(static::$passwordClient)) {
                abort(500);
            }
        }
    }

    public function login(Request $request)
    {
        $request->merge([
            'grant_type' => 'password',
            'scope' => '',
            'client_id' => static::$passwordClient->id,
            'client_secret' => static::$passwordClient->secret,
        ]);

        $request = Request::create('oauth/token', 'POST', $request->all());

        return Route::dispatch($request);
    }

    public function refresh(Request $request)
    {
        $request->merge([
            'grant_type' => 'refresh_token',
            'scope' => '',
            'client_id' => static::$passwordClient->id,
            'client_secret' => static::$passwordClient->secret,
        ]);

        $request = Request::create('oauth/token', 'POST', $request->all());

        return Route::dispatch($request);
    }

    public function logout()
    {
        $token = \Auth::guard('api')->user()->token();
        if ($token->delete()){
            \DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->delete();
        }

        return $this->message('登出成功');
    }

    public function logoutAnywhere()
    {
        $token_ids = \Auth::guard('api')->user()->tokens->pluck('id')->toArray();
        if (\Auth::guard('api')->user()->tokens()->delete()) {
            \DB::table('oauth_refresh_tokens')
                ->whereIn('access_token_id', $token_ids)
                ->delete();
        }

        return $this->message('登出成功');
    }

    public function me()
    {
        $user_id = \Auth::guard('api')->id();
        $user = User::with('roles')->find($user_id);

        return UserResource::make($user);
    }
}
