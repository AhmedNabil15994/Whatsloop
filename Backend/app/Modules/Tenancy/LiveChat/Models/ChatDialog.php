<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatDialog extends Model{

    protected $table = 'dialogs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function dataList($limit) {
        $source = self::orderBy('last_time','DESC');  
        return self::generateObj($source,$limit);
    }

    static function generateObj($source,$limit){
        $sourceArr = $source->paginate($limit);
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }
        $data['data'] = $list;
        $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        return $data;
    }

    static function newDialog($source){
        $source = (object) $source;
        $dataObj = self::where('id',$source->id)->first();
        if($dataObj == null){
            $dataObj = new  self;
        }
        
        $dataObj->id = $source->id;
        $dataObj->name = isset($source->name) ? $source->name : '';
        $dataObj->image = isset($source->image) ? $source->image : '';
        $dataObj->metadata = isset($source->metadata) ? serialize($source->metadata) : '';
        $dataObj->last_time = isset($source->last_time) ? $source->last_time : '';
        $dataObj->is_pinned = isset($source->is_pinned) ? $source->is_pinned : 0;
        $dataObj->is_read = isset($source->is_read) ? $source->is_read : 0;
        $dataObj->save();
        return $dataObj;
    }

    static function getData($source){
        $dataObj = new \stdClass();
        if($source){
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->name = isset($source->name) ? $source->name : '';
            $dataObj->image = isset($source->image) ? $source->image : '';
            $dataObj->metadata = isset($source->metadata) ? unserialize($source->metadata) : [];
            $dataObj->last_time = isset($source->time) ? self::reformDate($source->last_time) : ''; 
            $dataObj->is_pinned = $source->is_pinned;
            $dataObj->is_read = $source->is_read;

            $dataObj->lastMessage = ChatMessage::getData(ChatMessage::where('chatId',$source->id)->orderBy('time','DESC')->first());
            if(isset($source->metadata) && isset($dataObj->metadata['labels']) && isset($dataObj->metadata['labels'][0])){
                $dataObj->label = Category::getOne($dataObj->metadata['labels'][0]) != null ? Category::getData(Category::getOne($dataObj->metadata['labels'][0])) : [];
            }

            return $dataObj;
        }
    }

    static function reformDate($time){
        $diff = (time() - $time ) / (3600 * 24);
        $date = \Carbon\Carbon::parse(date('Y-m-d H:i:s'));
        if(round($diff) == 0){
            return date('h:i A',$time);;
        }else if($diff == 1){
            return trans('main.yesterday');
        }else if($diff > 1 && $diff < 7){
            return $date->locale(LANGUAGE_PREF)->dayName;
        }else{
            return date('Y-m-d h:i:s A',$time);
        }
    }
}
