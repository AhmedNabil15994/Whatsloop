<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addons extends Model{

    use \TraitsFunc;

    protected $table = 'addons';
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
        if(isset($input['annual_price']) && !empty($input['annual_price'])){
            if (strpos($input['annual_price'], '||') !== false) {
                $arr = explode('||', $input['annual_price']);
                $min = (int) $arr[0];
                $max = (int) $arr[1];
                $source->where('annual_price','>=',$min)->where('annual_price','<=',$max);
            }else{
                $source->where('annual_price',$input['annual_price']);
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
        $data->module = $source->module;
        $data->title_ar = $source->title_ar != null ? $source->title_ar : '';
        $data->title_en = $source->title_en != null ? $source->title_en : '';
        $data->title = $source->{'title_'. \App::getLocale()};
        $data->monthly_price = $source->monthly_price != 0 ? $source->monthly_price : '';
        $data->annual_price = $source->annual_price != 0 ? $source->annual_price : '';
        $data->sort = $source->sort;
        $data->monthly_after_vat = $source->monthly_after_vat != null ? $source->monthly_after_vat : '';
        $data->annual_after_vat = $source->annual_after_vat != null ? $source->annual_after_vat : '';
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? 'مسودة' : 'مفعلة';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }
    
    static function newSortIndex(){
        return self::count() + 1;
    }

}
