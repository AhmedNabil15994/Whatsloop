<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model{

    use \TraitsFunc;

    protected $table = 'domains';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::where('id', $id)
            ->first();
    }

    static function getOneByDomain($domain){
        return self::where('domain', $domain)
            ->first();
    }

    
}
