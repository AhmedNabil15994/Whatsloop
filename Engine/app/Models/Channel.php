<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class  Channel extends Model{

    use \TraitsFunc;

    protected $table = 'channels';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'token',
        'instanceId',
        'instanceToken',
        'start_date',
        'end_date',
        'tenant_id',
        'global_user_id',
    ];


    static function getOne($id) {
        return self::find($id);
    }

    static function getUserChannel($id,$token) {
        return self::NotDeleted()->where([
            ['instanceId',$id],
            ['instanceToken',$token],
        ])->orderBy('end_date','DESC')->first();
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',$input['id']);
                    }
                    if (isset($input['token']) && !empty($input['token'])) {
                        $query->where('token',$input['token']);
                    } 
                    if (isset($input['name']) && !empty($input['name'])) {
                        $query->where('name', 'LIKE', '%' . $input['name'] . '%');
                    } 
                });
        if(!IS_ADMIN){
            $source->where('id',Session::get('channel'));
        }
        $source->orderBy('end_date','DESC');
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
        $dataObj->tenant_id = $source->tenant_id;
        $dataObj->global_user_id = $source->global_user_id;
        $dataObj->name = $source->name;
        $dataObj->token = $source->token;
        $dataObj->instanceId = $source->instanceId;
        $dataObj->instanceToken = $source->instanceToken;
        $dataObj->start_date = $source->start_date;
        $dataObj->end_date = $source->end_date;
        return $dataObj;
    }

}
