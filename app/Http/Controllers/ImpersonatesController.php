<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Database\Models\ImpersonationToken;
use Illuminate\Http\RedirectResponse;

class ImpersonatesController extends Controller
{
    public static $ttl = 60; // seconds
    public static function makeResponse($token, int $ttl = null): RedirectResponse
    {
        $token = $token instanceof ImpersonationToken ? $token : ImpersonationToken::findOrFail($token);

        if (((string) $token->tenant_id) !== ((string) tenant()->getTenantKey())) {
            abort(403);
        }

        $ttl = $ttl ?? static::$ttl;

        if ($token->created_at->diffInSeconds(Carbon::now()) > $ttl) {
            abort(403);
        }

        session(['user_id'=>$token->user_id]);
        $userObj = User::getOne($token->user_id);
        $isAdmin = in_array($userObj->group_id, [1,]);
        session(['group_id' => $userObj->group_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => '']);
        $channels = User::getData($userObj)->channels;
        session(['channel' => $channels[0]->id]);

        // Auth::guard($token->auth_guard)->loginUsingId($token->user_id);

        $token->delete();

        return redirect($token->redirect_url);
    }

    public function index($token)
    {
        return self::makeResponse($token);
        // return UserImpersonation::makeResponse($token);
    }
}
