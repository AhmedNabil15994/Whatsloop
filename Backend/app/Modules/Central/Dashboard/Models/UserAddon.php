<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class UserAddon extends Model{

    use \TraitsFunc;

    protected $table = 'user_addons';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'addon_id',
        'start_date',
        'duration_type',
        'status',
        'end_date',
        'tenant_id',
        'global_user_id',
    ];

    public function Addon(){
        return $this->belongsTo('App\Models\Addons','addon_id');
    }

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','user_id');
    }

    static function getOne($id) {
        return self::find($id);
    }

    static function getDeactivated($user_id){
        $source = self::NotDeleted()->where([
            ['user_id',$user_id],
            ['status','!=',1]
        ])->orWhere([
            ['user_id',$user_id],
            ['end_date','<',date('Y-m-d')]
        ])->pluck('addon_id');
        return array_unique(reset($source));
    }

    static function getActivated($user_id){
        $source = self::NotDeleted()->where([
            ['user_id',$user_id],
            ['status','==','IN(1,3)'],
        ])->orWhere([
            ['user_id',$user_id],
            ['end_date','>=',date('Y-m-d')]
        ])->get();
        return reset($source);
    }

    static function dataList($addons=null,$user_id=null,$end_date=null,$statusArr=null) {
        $input = \Request::all();
        if($addons != null || $addons == ' '){
            $data = [];
            $allData = self::NotDeleted()->where('user_id',$user_id)->whereIn('status',[1,2,3])->pluck('addon_id');
            $disabled = self::NotDeleted()->where('user_id',$user_id)->whereIn('status',[3])->pluck('addon_id');
            $dataId = self::NotDeleted()->where([
                    ['user_id',$user_id],
                    ['status',2]
                ])->orWhere([
                    ['user_id',$user_id],
                    ['end_date','<',date('Y-m-d')]
                ])->pluck('addon_id');
            $data[0] = array_unique(reset($allData));
            $data[1] = array_unique(reset($dataId));
            $data[2] = array_unique(reset($disabled));
            return $data;
        }else{
            $source = self::NotDeleted()->whereIn('status',$statusArr);
            if($user_id != null){
                $source->where('user_id',$user_id);
            }
            if($end_date != null){
                $source->where('end_date',$end_date);
            }
        }
        
        $source->orderBy('id','DESC');
        return self::getObj($source);
    }

    static function getDataForUser($user_id){
        $dataList = self::NotDeleted()->where('user_id',$user_id)->get();
        $list = [];
        foreach ($dataList as $value) {
            $list[$value->addon_id] = $value->duration_type;
        }
        return $list;
    }

    static function getAllDataForUser($user_id){
        $dataList = self::NotDeleted()->where('user_id',$user_id)->get();
        $list = [];
        foreach ($dataList as $value) {
            $list[$value->addon_id] = [$value->duration_type,$value->end_date];
        }
        return $list;
    }

    static function getObj($source) {
        $sourceArr = $source->get();

        $list = [];
        foreach ($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;
        return $data;
    }

    // Status == 1 => Active
    // Status == 0 => Not Active
    // Status == 2 ==> Deactivated Due To Payment
    // Status == 3 ==> Disabled By User 
    static function getData($source){
        $dataObj = new \stdClass();
        $dataObj->id = $source->id;
        $dataObj->Addon = isset($source->Addon) ? Addons::getData($source->Addon) : '';
        $dataObj->user_id = $source->user_id;
        $dataObj->status = $source->status;
        $dataObj->statusText = self::getStatus($source->status);
        $dataObj->duration_type = $source->duration_type;
        $dataObj->setting_pushed = $source->setting_pushed;
        $dataObj->addon_id = $source->addon_id;
        $dataObj->global_user_id = $source->global_user_id;
        $dataObj->tenant_id = $source->tenant_id;
        $dataObj->start_date = $source->start_date;
        $dataObj->end_date = $source->end_date;
        $dataObj->days = (strtotime($source->end_date) - strtotime($source->start_date)) / (60 * 60 * 24);
        $dataObj->usedDays = (strtotime(date('Y-m-d')) - strtotime($source->start_date)) / (60 * 60 * 24);
        $dataObj->leftDays = $dataObj->days - $dataObj->usedDays;
        $dataObj->rate = $dataObj->days ? ($dataObj->leftDays / $dataObj->days) * 100 : 0;
        return $dataObj;
    }

    static function getStatus($status){
        $text = '';
        if($status == 0){
            $text = trans('main.notActive');
        }elseif($status == 1){
            $text = trans('main.active');
        }elseif($status == 2){
            $text = trans('main.suspended');
        }elseif($status == 3){
            $text = trans('main.deactivated');
        }
        return $text;
    }

    static function checkUserAvailability($userId,$addonId){
        $first = User::first()->id;
        $userId = $userId != $first ? $first : $userId;
        return self::where('user_id',$userId)->where('addon_id',$addonId)->where('status',1)->first();
    }
}
