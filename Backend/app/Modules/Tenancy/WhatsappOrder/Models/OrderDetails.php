<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model{

    protected $table = 'order_details';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('order_id', $id)->first();
    }

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('bank_transfers', $id, $photo,false);
    }

    public function Order(){
        return $this->belongsTo('App\Models\Order','order_id');
    }

    static function dataList() {
        $input = \Request::all();
        $source = self::orderBy('id','DESC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->paginate(15);
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }
        $data['data'] = $list;
        $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        return $data;
    }

    static function getData($source){
        $dataObj = new \stdClass();
        $dataObj->id = $source->id;
        $dataObj->order_id = $source->order_id;
        $dataObj->name = $source->name;
        $dataObj->phone = $source->phone;
        $dataObj->email = $source->email;
        $dataObj->country = $source->country;
        $dataObj->city = $source->city;
        $dataObj->region = $source->region;
        $dataObj->address = $source->address;
        $dataObj->shipping_method = $source->shipping_method;
        $dataObj->bank_name = $source->bank_name;
        $dataObj->account_name = $source->account_name;
        $dataObj->account_number = $source->account_number;
        $dataObj->photo = self::getPhotoPath($source->order_id, $source->image);
        $dataObj->photo_name = $source->image;
        $dataObj->photo_size = $dataObj->photo != '' ? \ImagesHelper::getPhotoSize($dataObj->photo) : '';
        $dataObj->transaction_id = $source->transaction_id;
        $dataObj->paymentGateaway = $source->paymentGateaway;
        return $dataObj;
    }

}
