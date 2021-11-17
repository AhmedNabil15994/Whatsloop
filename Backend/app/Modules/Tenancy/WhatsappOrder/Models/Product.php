<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('product_id', $id)->first();
    }

    static function dataList() {
        $input = \Request::all();
        $source = self::where('id','!=',0);
        if(isset($input['price']) && !empty($input['price'])){
            $source->where('price',$input['price']);
        }
        if(isset($input['name']) && !empty($input['name'])){
            $source->where('name','LIKE','%'.$input['name'].'%');
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
            $dataObj->product_id = $source->product_id;
            $dataObj->name = $source->name;
            $dataObj->currency = $source->currency;
            $dataObj->category_id = $source->category_id;
            $dataObj->price = $source->price;
            $dataObj->quantity = trans('main.unlimitted');
            $dataObj->images = $source->images != null ? unserialize($source->images) : [];
            $dataObj->mainImage = !empty($dataObj->images) ? $dataObj->images[0] : '';
            return $dataObj;
        }
    }
}
