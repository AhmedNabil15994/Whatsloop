<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model{

    use \TraitsFunc;

    protected $table = 'bank_transfers';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','user_id');
    }

    public function Invoice(){
        return $this->belongsTo('App\Models\Invoice','invoice_id');
    }

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('bank_transfers', $id, $photo,false);
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$user_id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input,$status,$user_id) { 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if (isset($input['user_id']) && !empty($input['user_id'])) {
                        $query->where('user_id',  $input['user_id']);
                    } 
                    if (isset($input['status']) && $input['status'] != null) {
                        $query->where('status',  $input['status']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
                    }
                    if($user_id != null){
                        $query->where('user_id',$user_id);
                    }
                })->orderBy('id','DESC');

        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        // $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->user_id = $source->user_id;
        $data->client = $source->Client != null ? $source->Client->name : '';
        $data->company = $source->Client != null ? $source->Client->company : '';
        $data->global_id = $source->global_id;
        $data->tenant_id = $source->tenant_id;
        $data->invoice_id = $source->invoice_id;
        $data->items = $source->invoice_id != null && Invoice::getOne($source->invoice_id) != null ? Invoice::getData($source->Invoice)->items : [];
        $data->order_no = $source->order_no;
        $data->total = $source->Invoice != null && Invoice::getOne($source->invoice_id) != null ? Invoice::getData($source->Invoice)->roTtotal : $source->total;
        $data->domain = $source->domain;
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = self::getStatus($source->status);
        $data->photo = self::getPhotoPath($source->id, $source->image);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
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

    static function newSortIndex(){
        return self::count() + 1;
    }

}
