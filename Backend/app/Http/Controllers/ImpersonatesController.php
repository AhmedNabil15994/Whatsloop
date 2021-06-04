<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserChannels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Database\Models\ImpersonationToken;
use Illuminate\Http\RedirectResponse;
use Session;
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
        session(['group_name' => $userObj->Group->name_ar]);
        // $channels = User::getData($userObj)->channels;
        $channels = $userObj->channels != null ? UserChannels::NotDeleted()->whereIn('id',unserialize($userObj->channels))->get() : [];
        session(['channel' => !empty($channels) ? $channels[0]->id : null]);
        session(['membership' => $userObj->membership_id]);
        if($isAdmin){
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
            session(['addons' => $userObj->addons !=  null ? UserAddon::dataList(unserialize($userObj->addons)) : []]);
        }else{
            $mainUser = User::first();
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$mainUser->global_id)->first();
            session(['addons' => $mainUser->addons !=  null ? UserAddon::dataList(unserialize($mainUser->addons)) : []]);
        }
        session(['tenant_id' => $tenantObj->tenant_id]);


        if(!empty($userObj->membership_id)){
            $membershipFeatures = \DB::connection('main')->table('memberships')->where('id',Session::get('membership'))->first()->features;
            $featuresId = unserialize($membershipFeatures);
            $features = \DB::connection('main')->table('membership_features')->whereIn('id',$featuresId)->pluck('title_en');
            $dailyMessageCount = (int) $features[0];
            $employessCount = (int) $features[1];
            $storageSize = (int) $features[2];
            session(['dailyMessageCount' => $dailyMessageCount]);
            session(['employessCount' => $employessCount]);
            session(['storageSize' => $storageSize]);
        }
        
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
