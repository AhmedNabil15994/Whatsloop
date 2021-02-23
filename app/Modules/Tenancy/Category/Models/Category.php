<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Category extends Model{

    use \TraitsFunc;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['color_id']) && !empty($input['color_id'])) {
                        $query->where('color_id',$input['color_id']);
                    } 
                    if (isset($input['name_ar']) && !empty($input['name_ar'])) {
                        $query->where('name_ar', 'LIKE', '%' . $input['name_ar'] . '%');
                    } 
                    if (isset($input['name_en']) && !empty($input['name_en'])) {
                        $query->where('name_en', 'LIKE', '%' . $input['name_en'] . '%');
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }else if(Session::has('channel')){
            $source->where('channel',Session::get('channel'));
        }
        $source->orderBy('sort','ASC');
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
        $extraData = self::getColor($source->color_id);
        $data->channel = $source->channel;
        $data->color_id = $source->color_id;
        $data->color = $extraData[0];
        $data->labelClass = 'badge badge-'.$extraData[1];
        $data->name_ar = $source->name_ar;
        $data->name_en = $source->name_en;
        $data->title = $source->{'name_'.LANGUAGE_PREF};
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function getColor($color_id){
        $color = '';
        $labelClass = '';
        if($color_id == 1){
            $color = trans('main.green');
            $labelClass = 'success';
        }else if($color_id == 2){
            $color = trans('main.blue');
            $labelClass = 'info';
        }else if($color_id == 3){
            $color = trans('main.yellow');
            $labelClass = 'info';
        }else if($color_id == 4){
            $color = trans('main.red');
            $labelClass = 'danger';
        }else if($color_id == 5){
            $color = trans('main.purple');
            $labelClass = 'primary';
        }else if($color_id == 6){
            $color = trans('main.black');
            $labelClass = 'dark';
        }
        return [$color,$labelClass];
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
