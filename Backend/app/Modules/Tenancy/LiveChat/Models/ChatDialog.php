<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatDialog extends Model{

    protected $table = 'dialogs';
    protected $primaryKey = 'id';
    protected $fillable = ['id','name','image','metadata','is_pinned','is_read','modsArr'];    
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    public function Messages(){
        return $this->hasMany('App\Models\ChatMessage','author','id');
    }

    static function dataList($limit,$name=null) {
        $input = \Request::all();
        if($name != null){
            $limit = 0;
            $source = self::with('Messages')->where('name','LIKE','%'.$name.'%')->orWhere('id','LIKE','%'.str_replace('+','',$name).'%');  
        }else{
            $source = self::with('Messages');  
        }

        if((isset($input['mine']) && !empty($input['mine'])) || !IS_ADMIN){
            $source->where('modsArr','LIKE','%'.USER_ID.'%');
        }

        $source->orderBy('last_time','DESC');
        return self::generateObj($source,$limit);
    }

    static function getPinned(){
        $source = self::where('is_pinned',1)->orderBy('last_time','DESC');  
        return self::generateObj($source);
    }

    static function generateObj($source,$limit=null){
        if($limit != null && $limit != 0){
            $sourceArr = $source->paginate($limit);
        }else{
            $sourceArr = $source->get();
        }
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }
        if($limit !=  null){
            $data['data'] = $list;
            $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        }else if($limit == 0){
            $data['data'] = $list;
        }else{
            $data = $list;
        }
        return $data;
    }

    static function newDialog($source){
        $source = (object) $source;
        $dataObj = self::where('id',$source->id)->first();
        if($dataObj == null){
            $dataObj = new  self;
        }
        
        $dataObj->id = $source->id;
        $dataObj->name = $dataObj->name != null ? $dataObj->name : (isset($source->name) ? self::reformName($source->name) : '');
        $dataObj->image = isset($source->image) ? $source->image : '';
        $dataObj->metadata = isset($source->metadata) ? serialize($source->metadata) : '';
        $dataObj->last_time = isset($source->last_time) ? $source->last_time : '';
        if(isset($source->is_pinned)){
            $dataObj->is_pinned = $source->is_pinned;
        }
        if(isset($source->is_read)){
            $dataObj->is_read = $source->is_read;
        }
        $dataObj->save();
        return $dataObj;
    }

    static function getData($source,$metaData=false){
        $dataObj = new \stdClass();
        if($source){
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->name = isset($source->name) ? $source->name : '';
            $dataObj->chatName = isset($source->name) ? self::reformChatId($source->name) : '';
            $dataObj->image = isset($source->image) ? $source->image : '';
            $dataObj->metadata = isset($source->metadata) ? unserialize($source->metadata) : [];
            $dataObj->last_time = isset($source->last_time) ? self::reformDate($source->last_time) : ''; 
            $dataObj->is_pinned = $source->is_pinned;
            $dataObj->is_read = $source->is_read;
            $dataObj->modsArr = $source->modsArr != null ? unserialize($source->modsArr) : [];
            if($metaData == false){
                $cats = ContactLabel::where('contact',str_replace('@c.us', '', $source->id))->pluck('category_id');
                $cats = reset($cats);
                $cats = empty($cats) ? [0] : $cats;
                $dataObj->labels = Category::dataList(null,$cats)['data'];
                $dataObj->labelsArr = $cats;
                $dataObj->moderators = !empty($dataObj->modsArr)  ? User::dataList(null,$dataObj->modsArr,'ar')['data'] : [];
                $dataObj->unreadCount = $source->Messages()->where('sending_status','!=',3)->count();
                $dataObj->lastMessage = ChatMessage::getData(ChatMessage::where('chatId',$source->id)->orderBy('messageNumber','DESC')->first());
                if(isset($source->metadata) && isset($dataObj->metadata['labels']) && isset($dataObj->metadata['labels'][0])){
                    $dataObj->label = Category::dataList($dataObj->metadata['labels'])['data'];
                }
            }

            return $dataObj;
        }
    }

    static function reformName($name){
        if(strpos($name, '+') !== false){
            $newName = str_replace('+', '', str_replace(' ', '', $name));
        }else{
            $newName = $name;
        }
        return $newName;
    }

    static function reformChatId($chatId){
        $chatId = str_replace('@c.us','',$chatId);
        $chatId = str_replace('@g.us','',$chatId);
        return $chatId;
    }

    static function reformDate($time){
        $diff = (time() - $time ) / (3600 * 24);
        $date = \Carbon\Carbon::parse(date('Y-m-d H:i:s'));
        if(round($diff) == 0){
            return date('h:i A',$time);;
        }else if($diff>0 && $diff<=1){
            return trans('main.yesterday');
        }else if($diff > 1 && $diff < 7){
            return $date->locale(LANGUAGE_PREF)->dayName;
        }else{
            return date('Y-m-d',$time);
        }
    }
}
