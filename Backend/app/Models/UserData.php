<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model{

    use \TraitsFunc;

    protected $table = 'user_data';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'domain',
        'email',
        'phone',
        'password',
    ];
   

    
}
