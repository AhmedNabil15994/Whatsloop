<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class CartEvents extends Model{

    use \TraitsFunc;

    protected $table = 'abandoned_carts_events';
    protected $primaryKey = 'id';
    protected $fillable = ['type','message_type','message','time','file_name','caption','bot_plus_id','updated_at'];    
    public $timestamps = false;

    static function getPhotoPath($name ,$id, $photo) {
        return \ImagesHelper::GetImagePath($name, $id, $photo,false);
    }

    public function BotPlus(){
        return $this->belongsTo('App\Models\BotPlus','bot_plus_id');
    }

    static function dataList($type=null) {
        $input = \Request::all();

        $source = self::NotDeleted();
        if($type != null){
            $source->where('type',$type);
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
        $data->type = $source->type;
        $data->message_type = $source->message_type;
        $data->message = $source->message;
        $data->time = $source->time;
        $data->caption = $source->caption != null ? $source->caption : '';
        $data->status = $source->status;
        $data->bot_plus_id = $source->bot_plus_id;
        $data->bot_plus = $source->bot_plus_id != null ? BotPlus::getData($source->BotPlus) : null;
        $data->file = $source->file_name != null ? self::getPhotoPath($source->type == 2 ? 'SallaCarts' : 'ZidCarts',$source->id, $source->file_name) : "";
        $data->file_name = $source->file_name;
        $data->file_size = $data->file != '' ? \ImagesHelper::getPhotoSize($data->file) : '';
        $data->file_type = $data->file != '' ? \ImagesHelper::checkFileExtension($data->file_name) : '';
        $data->messageTypeText = self::getMessageTypeText($data);
        $data->created_at = \Helper::formatDate($source->created_at);
        $data->updated_at = \Helper::formatDate($source->updated_at);
        return $data;
    }

    static function getMessageTypeText($data){
        $text = '';
        if($data->message_type == 1){
            return trans('main.text');
        }elseif($data->message_type == 3){
            return trans('main.botPlus');
        }elseif($data->message_type == 2){
            return $data->file_type == 'photo' ? trans('main.photos') : trans('main.file') ;
        }
        return $text;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
