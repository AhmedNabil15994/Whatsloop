<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class  UserTheme extends Model{

    use \TraitsFunc;

    protected $table = 'user_theme';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id) {
        return self::find($id);
    }

    static function getUserTheme($id) {
        $data = self::where('user_id',$id)->first();
        if($data != null ){
            return self::getData($data);
        }
    }

    static function getData($source){
        $dataObj = new \stdClass();
        $dataObj->user_id = $source->user_id;
        $dataObj->theme = $source->theme != null ? $source->theme : 'light';
        $dataObj->width = $source->width != null ? $source->width : 'fluid';
        $dataObj->menus_position = $source->menus_position != null ? $source->menus_position : 'fixed';
        $dataObj->sidebar_size = $source->sidebar_size != null ? $source->sidebar_size : 'default';
        $dataObj->user_info = $source->user_info != null ? $source->user_info : 'false';
        $dataObj->top_bar = $source->top_bar != null ? $source->top_bar : 'dark';
        return $dataObj;
    }

}
