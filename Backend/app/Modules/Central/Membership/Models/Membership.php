<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model{

    use \TraitsFunc;

    protected $table = 'memberships';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$ids=null) {
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
        $features = $source != null ? Feature::NotDeleted()->where('status',1)->whereIn('id',unserialize($source->features))->pluck('title_'.LANGUAGE_PREF) : [];
        // dd($features);
        $data->id = $source->id;
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->title_ar = $source->title_ar != null ? $source->title_ar : '';
        $data->title_en = $source->title_en != null ? $source->title_en : '';
        $data->monthly_price = $source->monthly_price != 0 ? $source->monthly_price : '';
        $data->annual_price = $source->annual_price != 0 ? $source->annual_price : '';
        $data->features = $source->features != null ? unserialize($source->features) : [];
        $data->featruesText = $source->features != null ? str_replace(',', ' <br>', implode(',', reset($features))) : [];
        $data->featruesArr = $source->features != null ? $features : [];
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
