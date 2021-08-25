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
        $source = self::NotDeleted()->where('user_id',$user_id)->where('status',2)->pluck('addon_id');
        return reset($source);
    }

    static function dataList($addons=null,$user_id=null,$end_date=null) {
        $input = \Request::all();
        if($addons != null){
            $data = [];
            $allData = self::NotDeleted()->where('user_id',$user_id)->whereIn('status',[1,2])->pluck('addon_id');
            $dataId = self::NotDeleted()->where('user_id',$user_id)->where('status',2)->pluck('addon_id');
            $data[0] = reset($allData);
            $data[1] = reset($dataId);
            return $data;
        }else{
            $source = self::NotDeleted()->where('status',$statusArr);
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

    static function getData($source){
        $dataObj = new \stdClass();
        $dataObj->id = $source->id;
        $dataObj->Addon = isset($source->Addon) ? $source->Addon : '';
        $dataObj->user_id = $source->user_id;
        $dataObj->status = $source->status;
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
        $dataObj->rate = ($dataObj->leftDays / $dataObj->days) * 100;
        return $dataObj;
    }

}
