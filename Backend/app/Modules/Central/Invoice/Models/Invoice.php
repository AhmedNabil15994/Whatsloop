<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model{

    use \TraitsFunc;

    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','client_id');
    }

    public function OldMembership(){
        return $this->hasOne('App\Models\OldMembership','user_id','client_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$client_id=null) {
        $input = \Request::all();

        $source = self::with('OldMembership')->NotDeleted()->where('status','!=',0)->where(function ($query) use ($input,$status,$client_id) { 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if (isset($input['client_id']) && !empty($input['client_id'])) {
                        $query->where('client_id',  $input['client_id']);
                    } 
                    if (isset($input['status']) && $input['status'] != null) {
                        $query->where('status',  $input['status']);
                    } 
                    if (isset($input['due_date']) && !empty($input['due_date'])) {
                        $query->where('due_date',  $input['due_date']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
                    }
                    if($client_id != null){
                        $query->where('client_id',$client_id);
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
        $data->client_id = $source->client_id;
        $data->client = $source->Client != null ? $source->Client->name : '';
        $data->company = $source->Client != null ? $source->Client->company : '';
        $data->transaction_id = $source->transaction_id;
        $data->due_date = $source->due_date;
        $data->paid_date = $source->paid_date != null ? $source->paid_date : '';
        $data->payment_method = $source->payment_method;
        $data->notes = $source->notes;
        $data->payment_gateaway = $source->payment_gateaway;
        $data->items = $source->items != null ? unserialize($source->items) : [];
        $data->sort = $source->sort;
        $data->main = $source->main;
        
        if($data->id != 1386){
            $data->oldPrice = $source->OldMembership != null && $source->main ? OldMembership::calcOldPrice($source->OldMembership,$data->items[0]['data']['duration_type']) : 0 ;
            $data->zidOrSalla = 0;
            if($data->oldPrice == 0){
                $datas = self::checkZidOrSalla($data->items,$source->total);
                $data->oldPrice = $datas[0];
                $data->zidOrSalla = $datas[1];
            }
            $data->discount = $data->oldPrice == 0 ? 0 : abs($source->total - $data->oldPrice);
            if($source->main && count($data->items) == 1){
                $data->discount = 0;
            }
            $data->total = $source->total;
            $data->roTtotal = $source->total - $data->discount;
        }else{
            $data->oldPrice =  0 ;
            $data->zidOrSalla = 0;
            $data->discount = $data->oldPrice == 0 ? 0 : abs($source->total - $data->oldPrice);
            if($source->main && count($data->items) == 1){
                $data->discount = 0;
            }
            $data->total = $source->total;
            $data->roTtotal = $source->total - $data->discount;
        }

        if($data->id == 7028){
            $data->zidOrSalla = 0;
            $data->oldPrice =  0 ;
            $data->discount = 1897.50;
            $data->total = $source->total;
            $data->roTtotal = $source->total - $data->discount;
        }

        if(in_array($data->id, [4849,4856,4857,6237])){
            $data->oldPrice =  0 ;
            $data->zidOrSalla = 0;
            $data->discount = ($source->total * 10 ) / 100;
            $data->total = $source->total;
            $data->roTtotal = $source->total - $data->discount;
        }

        if(in_array($data->id, [7178,])){
            $data->oldPrice =  0 ;
            $data->zidOrSalla = 0;
            $data->discount = ($source->total * 40 ) / 100;
            $data->total = $source->total;
            $data->roTtotal = $source->total - $data->discount;
        }
        
        $data->status = $source->status;
        $data->statusText = trans('main.invoice_status_'.$source->status);
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function checkZidOrSalla($items,$total){
        $hasSalla = 0 ;
        $hasZid = 0 ;
        $hasBot = 0 ;
        $duration_type = $items[0]['data']['duration_type'];
        foreach ($items as $key => $value) {
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Salla'){
                $hasSalla = 1;
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Zid'){
                $hasZid = 1;
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Bot'){
                $hasBot = 1;
            }
        }

        if(($hasBot && $hasSalla) || ($hasBot  && $hasZid)){
            return [$total - ($duration_type == 1 ? 230 : 2300),1];
        }

        return [0,0];
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

    static function getDisabled($user_id){
        $to = date('Y-m-t');
        $from = date('Y-m-01');
        return self::NotDeleted()->where('main',1)->whereBetween('due_date',[$from,$to])->where('client_id',$user_id)->where('status',2)->first();
    }
}
