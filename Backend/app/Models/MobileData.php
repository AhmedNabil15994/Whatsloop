<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileData extends Model{

    use \TraitsFunc;

    protected $table = 'mobile_data';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
