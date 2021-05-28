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
        $data->payment_method = $source->payment_method;
        $data->payment_method_text = self::getPaymentMethod($source->payment_method);
        $data->currency = $source->currency;
        $data->currency_text = $source->currency && $source->currency == 1 ? trans('main.sar') : trans('main.usd');
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }  

    static function getPaymentMethod($method){
        $text = '';
        if($method == 1){
            $text = trans('main.mada');
        }elseif($method == 2){
            $text = trans('main.visaMaster');
        }elseif($method == 3){
            $text = trans('main.bankTransfer');
        }
        return $text;
    }
}
