<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class UserExtraQuota extends Model{

    use \TraitsFunc;

    protected $table = 'user_extra_quotas';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'addon_id',
        'start_date',
        'duration_type',
        'end_date',
        'status',
        'tenant_id',
        'global_user_id',
    ];

    public function ExtraQuota(){
        return $this->belongsTo('App\Models\ExtraQuota','extra_quota_id');
    }


    static function getOne($id) {
        return self::find($id);
    }

    static function dataList($user_id=null,$end_date=null,$statusArr=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',$input['id']);
                    }
                });

        if($statusArr != null){
            $source->whereIn('status',$statusArr);
        }
        if($user_id != null){
            $source->where('user_id',$user_id);
        }
        if($end_date != null){
            $source->where('end_date',$end_date);
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

    static function getOneForUserByType($user_id,$type){
        $source = self::NotDeleted()->where('global_user_id',$user_id)->where('status',1)->withSum(['ExtraQuota'=> function($withQuery) use ($type){
            $withQuery->where('extra_type',$type);
        }],'extra_count')->orderBy('id','desc')->get();

        if(!$source || !isset($source[0])){
            return 0;
        }
        return count($source) * $source[0]->extra_quota_sum_extra_count;
    }

    static function getForUser($user_id){
        $dataObj = self::where('global_user_id',$user_id)->get();

        if(!$dataObj){
            return [];
        }
        $list = [];
        foreach ($dataObj as $value) {
            $list[$value->id] = self::getData($value);
        }
        return [$dataObj->pluck('extra_quota_id'),$list];
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


    // Status == 1 => Active
    // Status == 0 => Not Active
    // Status == 2 ==> Disabled
    static function getData($source){
        $dataObj = new \stdClass();
        $dataObj->id = $source->id;
        $dataObj->user_id = $source->user_id;
        $dataObj->status = $source->status;
        $dataObj->statusText = self::getStatus($source->status);
        $dataObj->ExtraQuota = isset($source->ExtraQuota) ? ExtraQuota::getData($source->ExtraQuota) : '';
        $dataObj->extra_quota_id = $source->extra_quota_id;
        $dataObj->global_user_id = $source->global_user_id;
        $dataObj->tenant_id = $source->tenant_id;
        $dataObj->start_date = $source->start_date;
        $dataObj->end_date = $source->end_date;
        $dataObj->duration_type = $source->duration_type;
        $dataObj->days = (strtotime($source->end_date) - strtotime($source->start_date)) / (60 * 60 * 24);
        $dataObj->usedDays = (strtotime(date('Y-m-d')) - strtotime($source->start_date)) / (60 * 60 * 24);
        $dataObj->leftDays = $dataObj->days - $dataObj->usedDays;
        $dataObj->rate = $dataObj->days != 0 ? ($dataObj->leftDays / $dataObj->days) * 100 : 0;
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

}
