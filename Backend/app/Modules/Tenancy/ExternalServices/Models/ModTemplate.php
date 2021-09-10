<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class ModTemplate extends Model{

    use \TraitsFunc;

    protected $table = 'mod_templates';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$mod_id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['status']) && $input['status'] != null) {
                        $query->where('status',$input['status']);
                    }
                    if (isset($input['statusText']) && !empty($input['statusText'])) {
                        $query->where('statusText',$input['statusText']);
                    } 
                });
        if($status != null){
            $source->where('status',$status);
        }
        if($mod_id != null){
            $source->where('mod_id',$mod_id);
        }
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }else if(Session::has('channelCode')){
            $source->where('channel',Session::get('channelCode'));
        }
        $source->orderBy('id','ASC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->channel = $source->channel;
        $data->content_ar = $source->content_ar;
        $data->content_en = $source->content_en;
        $data->content = $source->{'content_'.LANGUAGE_PREF};
        $data->statusText = $source->statusText;
        $data->mod_id = $source->mod_id;
        $data->status = $source->status;
        $data->statusIDText = $source->status == 1 ? trans('main.active') : trans('main.notActive');
        $data->updated_at = \Helper::formatDate($source->updated_at);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
