<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model{

    protected $table = 'messages';
    protected $primaryKey = 'id';
    public $timestamps = false;


    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function newMessage($source) {
        $source = (object) $source;
        $data = new  self;
        $data->id = $source->id;
        $data->body = $source->body;
        $data->fromMe = $source->fromMe;
        $data->isForwarded = $source->isForwarded;
        $data->author = $source->author;
        $data->time = $source->time;
        $data->chatId = $source->chatId;
        $data->messageNumber = $source->messageNumber;
        if(isset($source->status)){
            $data->status = $source->status;
        }
        $data->type = $source->type;
        // $data->type_id = $source->type_id;
        $data->senderName = $source->senderName;
        $data->chatName = $source->chatName;
        $data->quotedMsgBody = $source->quotedMsgBody;
        $data->quotedMsgId = $source->quotedMsgId;
        $data->quotedMsgType = $source->quotedMsgType;
        return $data->save();
    }  
}
