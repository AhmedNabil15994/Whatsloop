<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientsRequests extends Model{
	protected $table = 'new_clients_requests';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

	static function getOne($phone){
		return self::where('phone',$phone)->first();
	}
}