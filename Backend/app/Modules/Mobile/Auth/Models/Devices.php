<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devices extends Model{

    protected $table = 'devices';
    protected $primaryKey = 'id';
    public $timestamps = false;

    function APIAuth(){
        return $this->belongsTo('App\Models\ApiAuth', 'auth_id');
    }

    static function applyNewDevice($authId) {
        try{
            $deviceKey = $_SERVER['HTTP_DEVICEKEY'];
        }catch(\Exception $e ){
            return false;
        }

        $checkDevice = self::where('auth_id', $authId)
            ->where('device_key', $deviceKey)
            ->first();

        define('DEVICEKEY',$deviceKey);

        if ($checkDevice == null) {
            $newDevice = new self();
            $newDevice->auth_id = $authId;
            $newDevice->device_key = $deviceKey;
            $newDevice->created_at = DATE_TIME;
            $newDevice->version = $_SERVER['HTTP_APPVER'];
            $newDevice->save();
        }

        return true;
    }

    static function getDevicesBy($user_id,$byAll = false){
        return self::where('device_key','!=',"")->whereHas('APIAuth', function ($APIAuthQuery) use ($user_id,$byAll) {
            if ($byAll != false) {
                $APIAuthQuery->where('user_id', $user_id)->where('auth_expire',1);
            }else{
                $APIAuthQuery->whereIn('user_id',$user_id)->where('auth_expire',1);
            }
        })
        ->orderBy('id','DESC')
        ->groupby('device_key')
        ->pluck('device_key');
    }
}
