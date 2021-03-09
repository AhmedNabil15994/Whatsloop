<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class PaymentInfo extends Model{

    use \TraitsFunc;

    protected $table = 'payment_info';
    protected $primaryKey = 'id';
    public $timestamps = false;


    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->user_id = $source->user_id;
        $data->address = $source->address;
        $data->address2 = $source->address2;
        $data->city = $source->city;
        $data->country = $source->country;
        $data->region = $source->region;
        $data->postal_code = $source->postal_code;
        $data->tax_id = $source->tax_id;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }  
}
