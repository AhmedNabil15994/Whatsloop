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
        $data->labelId = $source->labelId;
        $data->hexColor = $extraData[2];
        $data->colorName = $extraData[3];
        $data->title = $source->{'name_'.LANGUAGE_PREF};
        $data->whatsappName = self::getName($source->name_ar,$source->name_en);
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function getName($name_ar,$name_en){
        $fullName= '';
        if(!empty($name_ar)){
            $fullName = $name_ar;
            if(!empty($name_en)){
                $fullName.= ' - '.$name_en;
            }
        }else{
            if(!empty($name_en)){
                $fullName=$name_en;
            }
        }
        return $fullName;
    }

    static function getColor($color_id){
        if($color_id == 1){
            $color = trans('main.green');
            $labelClass = 'success';
            $hexColor = '#66ddaa';
            $colorName = 'MediumAquamarine';
        }else if($color_id == 2){
            $color = trans('main.blue');
            $labelClass = 'info';
            $hexColor = '#00bfff';
            $colorName = 'MayaBlue';
        }else if($color_id == 3){
            $color = trans('main.yellow');
            $labelClass = 'warning';
            $hexColor = '#ffcc33';
            $colorName = 'Sunglow';
        }else if($color_id == 4){
            $color = trans('main.red');
            $labelClass = 'danger';
            $hexColor = '#FF9999';
            $colorName = 'MonaLisa';
        }else if($color_id == 5){
            $color = trans('main.purple');
            $labelClass = 'primary';
            $hexColor = '#E6E6FA';
            $colorName = 'Lavender';
        }else if($color_id == 6){
            $color = trans('main.black');
            $labelClass = 'dark';
            $hexColor = '#323a46';
            $colorName = 'Nepal';
        }else{
            $color = '';
            $labelClass = '';
            $hexColor = '';
            $colorName = '';
        }
        return [$color,$labelClass,$hexColor,$colorName];
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
