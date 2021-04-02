<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatEmpLog extends Model{

    protected $table = 'chat_emp_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function dataList() {
        $input = \Request::all();
        $source = self::orderBy('id','DESC');
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
        $data->chatId = $source->chatId;
        $data->user_id = $source->user_id;
        $data->type = $source->type;
        $data->typeText = self::getTypeText($source->type);
        $data->ended = $source->ended;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function getTypeText($type){
        if($type == 1){
            return 'Chat Entered';
        }elseif($type == 2){
            return 'Chat Exited';
        }elseif($type == 3){
            return 'First Chat Reply';
        }
    }

    static function newRecord($chatId,$type,$user_id,$date,$ended){
        $newObj = new self;
        $newObj->chatId = $chatId;
        $newObj->type = $type;
        $newObj->user_id = $user_id;
        $newObj->created_at = $date;
        $newObj->ended = $ended;
        $newObj->save();
    }

    static function newLog($chatId,$type=null){
        // $userId = USER_ID;   
        $userId = 0;
        $date = date('Y-m-d H:i:s');  
        if($type != null){
            $dataObj = self::where('user_id',$userId)->where('chatId',$chatId)->orderBy('id','DESC')->first();
            if($dataObj != null && $dataObj->type == 1 && $dataObj->ended == 0){
                self::newRecord($chatId,$type,$userId,$date,1);
            }
        }else{
            $dataObj = self::where('user_id',$userId)->where('type','!=',3)->orderBy('id','DESC')->first();
            if($dataObj != null){
                if($dataObj->chatId != $chatId){
                    $oldChat = $dataObj->chatId;
                    if($dataObj->ended == 0){
                        $dataObj->ended = 1;
                        $dataObj->save();
                    }
                    if($dataObj->type == 1){
                        self::newRecord($oldChat,2,$userId,$date,1);
                    }
                    self::newRecord($chatId,1,$userId,$date,0);
                }else{
                    if($dataObj->type == 1){
                        if($dataObj->ended == 1){
                            self::newRecord($chatId,2,$userId,$date,1);
                        }
                        
                    }elseif($dataObj->type == 2){
                        $dataObj->ended = 1;
                        $dataObj->save();

                        self::newRecord($chatId,1,$userId,$date,0);
                    }
                }
            }else{
                $type = $type != null ? $type : 1;
                self::newRecord($chatId,$type,$userId,$date,0);
            }
        }
    }
}
