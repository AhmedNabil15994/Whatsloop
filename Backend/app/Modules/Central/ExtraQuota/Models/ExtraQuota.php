<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraQuota extends Model{

    use \TraitsFunc;

    protected $table = 'extra_quotas';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$ids=null,$notId=null) {
        $input = \Request::all();

        $source = self::NotDeleted();

        if (isset($input['title']) && !empty($input['title'])) {
            $source->where('title', 'LIKE', '%' . $input['title'] . '%');
        } 
        if (isset($input['id']) && !empty($input['id'])) {
            $source->where('id',  $input['id']);
        }
        if (isset($input['extra_type']) && !empty($input['extra_type'])) {
            $source->where('extra_type',  $input['extra_type']);
        } 
        if(isset($input['monthly_price']) && !empty($input['monthly_price'])){
            if (strpos($input['monthly_price'], '||') !== false) {
                $arr = explode('||', $input['monthly_price']);
                $min = (int) $arr[0];
                $max = (int) $arr[1];
                $source->where('monthly_price','>=',$min)->where('monthly_price','<=',$max);
            }else{
                $source->where('monthly_price',$input['monthly_price']);
            }
        }
        if($status != null){
            $source->where('status',$status);
        }
        if($ids != null){
            $source->whereIn('id',$ids);
        } 
        if($notId != null){
            $source->whereNotIn('id',$notId);
        }    

        $source->orderBy('sort','ASC');

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
        $data->extra_count = $source->extra_count;
        $data->extra_type = $source->extra_type;
        $data->extraTypeText = self::getTypeText($source->extra_type);
        $data->monthly_price = $source->monthly_price != 0 ? $source->monthly_price : '';
        $data->monthly_after_vat = $source->monthly_after_vat != null ? $source->monthly_after_vat : '';
        $data->annual_price = $source->monthly_price != 0 ? $source->monthly_price * 10 : '';
        $data->annual_after_vat = $source->monthly_after_vat != null ? $source->monthly_after_vat * 10 : '';
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? 'مسودة' : 'مفعلة';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function getTypeText($type){
        $text = '';
        if($type == 1){
            $text = trans('main.message');
        }else if($type == 2){
            $text = trans('main.employee');
        }else if($type == 3){
            $text = trans('main.gigaB');
        }
        return $text;
    }
    
    static function newSortIndex(){
        return self::count() + 1;
    }

}
