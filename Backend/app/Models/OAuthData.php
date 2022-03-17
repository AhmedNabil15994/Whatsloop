<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthData extends Model{

    use \TraitsFunc;

    protected $table = 'oauth_data';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'type',
        'user_id',
        'tenant_id',
        'phone',
        'domain',
        'access_token',
        'token_type',
        'expires_in',
        'authorization',
        'refresh_token',
        'created_at',
        'updated_at',

    ];
   

    
}
