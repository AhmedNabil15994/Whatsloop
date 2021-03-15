<?php 

namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class  Variable extends Model{

    use \TraitsFunc;

    protected $table = 'variables';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getVar($key) {
        $variableObj = self::where('var_key',$key)->first();
        return $variableObj != null ? $variableObj->var_value : '';
    }

}
