<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model{

    protected $table = 'api_keys';
    protected $primaryKey = 'id';
    protected $fillable = ['api_key','api_value','status'];
    public $timestamps = false;
    
    static function checkApiKey() {
        $apiKey = $_SERVER['HTTP_APIKEY'];
        return ApiKeys::where('api_value', $apiKey)->where('status',1)->first();
    }
}
