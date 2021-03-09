<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class  ChatSession extends Model{

    use \TraitsFunc;

    protected $table = 'chat_sessions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($chatId) {
        return self::where('chatId',$chatId)->first(); 
    }

}
