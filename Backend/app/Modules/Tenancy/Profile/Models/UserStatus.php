<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class  UserStatus extends Model{

    use \TraitsFunc;

    protected $table = 'user_status';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id) {
        return self::find($id);
    }

    static function getUserChannels() {
        $data = self::orderBy('id','DESC');
        return self::getObj($data);
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::where(function ($query) use ($input) {
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',$input['id']);
                    }
                    if (isset($input['status']) && !empty($input['status'])) {
                        $query->where('status',$input['status']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        $source->select(\DB::raw('*, max(created_at) as created_at'))->groupBy(\DB::raw('DATE(created_at)'),\DB::raw('status'))->orderBy('created_at','DESC');
        return self::getObj($source);
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
        $dataObj->status = $source->status;
        $dataObj->statusText = self::getStatus($source->status);
        $dataObj->created_at = \Helper::formatDate($source->created_at);
        return $dataObj;
    }

    static function getStatus($status){
        if($status == 1){
            $text = trans('main.authenticated');
        }else if($status == 2){
            $text = trans('main.init');
        }else if($status == 3){
            $text = trans('main.loading');
        }else if($status == 4){
            $text = trans('main.gotQrCode');
        }
        return $text;
    }

}
