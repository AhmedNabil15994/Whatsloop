<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model{

    protected $table = 'messages';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function newMessage($source){
        $source = (object) $source;
        $dataObj = self::where('id',$source->id)->first();
        if($dataObj == null){
            $dataObj = new  self;
        }
        
        $dataObj->id = $source->id;
        $dataObj->body = isset($source->body) ? $source->body : '';
        $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
        $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
        $dataObj->author = isset($source->author) ? $source->author : '';
        $dataObj->time = isset($source->time) ? $source->time : '';
        $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
        $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
        if(isset($source->status)){
            $dataObj->status = $source->status;
        }
        $dataObj->type = isset($source->type) ? $source->type : '' ;
        $dataObj->senderName = isset($source->senderName) ? $source->senderName : '' ;
        $dataObj->chatName = isset($source->chatName) ? $source->chatName : '' ;
        $dataObj->quotedMsgBody = isset($source->quotedMsgBody) ? $source->quotedMsgBody : '' ;
        $dataObj->quotedMsgId = isset($source->quotedMsgId) ? $source->quotedMsgId : '' ;
        $dataObj->quotedMsgType = isset($source->quotedMsgType) ? $source->quotedMsgType : '' ;
        return $dataObj->save();
    }

    static function getData($source){
        $dataObj = new \stdClass();
        if($source){
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->body = isset($source->body) ? $source->body : '';
            $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
            $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
            $dataObj->author = isset($source->author) ? $source->author : '';
            $dataObj->time = isset($source->time) ? $source->time : '';
            $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
            $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
            $dataObj->status = $source->status != null ? $source->status : ($source->status == null && $source->fromMe == 0 ? $source->senderName : '');
            $dataObj->type = isset($source->type) ? $source->type : '' ;
            $dataObj->senderName = isset($source->senderName) ? $source->senderName : '' ;
            $dataObj->chatName = isset($source->chatName) ? $source->chatName : '' ;
            $dataObj->quotedMsgBody = isset($source->quotedMsgBody) ? $source->quotedMsgBody : '' ;
            $dataObj->quotedMsgId = isset($source->quotedMsgId) ? $source->quotedMsgId : '' ;
            $dataObj->quotedMsgType = isset($source->quotedMsgType) ? $source->quotedMsgType : '' ;
            return $dataObj;
        }
    }  
}
