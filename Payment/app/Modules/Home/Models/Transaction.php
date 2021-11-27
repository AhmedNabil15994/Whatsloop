<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{

    use \TraitsFunc;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($tran_ref){
        return self::where('tran_ref', $tran_ref)
            ->first();
    }
    
}
