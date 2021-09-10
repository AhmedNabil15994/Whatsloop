<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('order_id', $id)->first();
    }

    public function Message(){
        return $this->belongsTo('App\Models\ChatMessage','message_id');
    }

    public function Contact(){
        return $this->belongsTo('App\Models\Contact','client_id');
    }

    static function dataList() {
         $input = \Request::all();
        $source = self::where('id','!=',0);
        if(isset($input['price']) && !empty($input['price'])){
            $source->where('total',$input['price']);
        }
        if(isset($input['name']) && !empty($input['name'])){
            $source->where('client_id','LIKE','%'.$input['name'].'%'.'@c.us')->orWhere('order_id',$input['name']);
        }
        $source->orderBy('id','DESC');
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
        if($source){
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->order_id = $source->order_id;
            $dataObj->subtotal = $source->subtotal;
            $dataObj->tax = $source->tax;
            $dataObj->total = $source->total;
            $dataObj->status = $source->status;
            $dataObj->message_id = $source->message_id;
            $dataObj->products = $source->products != null ? unserialize($source->products) : [];
            $dataObj->client_id = $source->client_id;
            $dataObj->created_at = isset($source->created_at) ? self::reformDate($source->created_at)[0] : ''; 
            $dataObj->created_at2 = isset($source->created_at) ? date('Y-m-d',$source->created_at) : ''; 
            $dataObj->client = Contact::NotDeleted()->where('phone','+'.str_replace('@c.us','',$source->client_id))->first()->name;
            return $dataObj;
        }
    }

    static function reformDate($time){
        $diff = (time() - $time ) / (3600 * 24);
        $date = \Carbon\Carbon::parse(date('Y-m-d H:i:s'));
        if(round($diff) == 0){
            return [date('Y-m-d',$time),date('h:i A',$time)];
        }else if($diff>0 && $diff<=1){
            return [trans('main.yesterday'), date('h:i A',$time)];
        }else if($diff > 1 && $diff < 7){
            return [$date->locale(LANGUAGE_PREF)->dayName,date('h:i A',$time)];
        }else{
            return [date('Y-m-d',$time),date('h:i A',$time)];
        }
    }
}
