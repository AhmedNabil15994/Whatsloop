<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model{

    use \TraitsFunc;

    protected $table = 'coupons';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$ids=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input,$status,$ids) {
                    if (isset($input['code']) && !empty($input['code'])) {
                        $query->where('code', 'LIKE', '%' . $input['code'] . '%');
                    } 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if (isset($input['discount_type']) && !empty($input['discount_type'])) {
                        $query->where('discount_type',  $input['discount_type']);
                    } 
                    if (isset($input['discount_value']) && !empty($input['discount_value'])) {
                        if (strpos($input['discount_value'], '||') !== false) {
                            $arr = explode('||', $input['discount_value']);
                            $min = (int) $arr[0];
                            $max = (int) $arr[1];
                            $query->where('discount_value','>=',$min)->where('discount_value','<=',$max);
                        }else{
                            $query->where('discount_value',$input['discount_value']);
                        }
                    } 
                    if (isset($input['valid_type']) && !empty($input['valid_type'])) {
                        $query->where('valid_type',  $input['valid_type']);
                    } 
                    if (isset($input['valid_value']) && !empty($input['valid_value'])) {
                        $query->where('valid_value',  $input['valid_value']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
                    }
                    if($ids != null){
                        $query->whereIn('id',$ids);
                    }
                })->orderBy('sort','ASC');

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

    static function availableCoupons(){
        return self::NotDeleted()->where([
            ['status','=',1],
            ['valid_type','=',1],
            ['valid_value','>',0],
        ])->orWhere([
            ['status','=',1],
            ['valid_type','=',2],
            ['valid_value','>=',now()->format('Y-m-d')],
        ])->orderBy('id','desc')->get();
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->code = $source->code;
        $data->discount_type = $source->discount_type;
        $data->discount_value = $source->discount_value;
        $data->valid_type = $source->valid_type;
        $data->valid_value = $source->valid_value;
        $data->discount_typeText = trans('main.discount_type_'.$source->discount_type);
        $data->valid_typeText = trans('main.valid_type_'.$source->valid_type);
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? trans('main.notActive') : trans('main.active');
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

    static function checkCouponByCode($code, $notId = false){
        $dataObj = self::NotDeleted()->where('code',$code);

        if ($notId != false) {
            $notId = (array) $notId;
            $dataObj->whereNotIn('id', $notId);
        }

        return $dataObj->first();
    }

}
