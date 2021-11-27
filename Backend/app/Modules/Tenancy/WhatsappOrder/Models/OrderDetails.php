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

    static function transfersList(){
        $input = \Request::all();
        $source = self::orderBy('id','DESC');
        return self::generateObj($source,true);
    }

    static function generateObj($source,$type=null){
        $sourceArr = $source->paginate(15);
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            if($type !=null){
                $list[$key] = self::getTransfer($value);
            }else{
                $list[$key] = self::getData($value);
            }
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
        $dataObj->transfer_date = $source->transfer_date;
        $dataObj->transfer_status = $source->transfer_status;
        $dataObj->photo = self::getPhotoPath($source->order_id, $source->image);
        $dataObj->photo_name = $source->image;
        $dataObj->photo_size = $dataObj->photo != '' ? \ImagesHelper::getPhotoSize($dataObj->photo) : '';
        $dataObj->transaction_id = $source->transaction_id;
        $dataObj->paymentGateaway = $source->paymentGateaway;
        return $dataObj;
    }

    static function getTransfer($source){
        $dataObj = new \stdClass();
        $dataObj->id = $source->id;
        $dataObj->order_id = $source->order_id;
        $dataObj->order_no = $source->Order != null ? $source->Order->order_id : '';
        $dataObj->client = $source->name;
        $dataObj->total = $source->Order != null ? ($source->Order->total_after_discount > 0 ? $source->Order->total_after_discount : $source->Order->total ) : '';
        $dataObj->created_at = $source->transfer_date;
        $dataObj->status = $source->transfer_status;
        $dataObj->phone = $source->phone;
        $dataObj->statusText = self::getStatus($source->transfer_status);
        $dataObj->photo = self::getPhotoPath($source->order_id, $source->image);
        $dataObj->photo_name = $source->image;
        $dataObj->photo_size = $dataObj->photo != '' ? \ImagesHelper::getPhotoSize($dataObj->photo) : '';
        return $dataObj;
    }

    static function getStatus($status){
        if($status == 1){
            return trans('main.requestSent');
        }elseif($status == 2){
            return trans('main.accept');
        }elseif($status == 3){
            return trans('main.refuse');
        }
    }

}
