<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundle extends Model{

    use \TraitsFunc;

    protected $table = 'bundles';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;


    public function Membership(){
        return $this->belongsTo('App\Models\Membership','membership_id','id');
    }

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
        $addons = [];
        if(!empty($source->addons)){
            $addons = Addons::NotDeleted()->where('status',1)->whereIn('id',unserialize($source->addons))->pluck('title_'.LANGUAGE_PREF);
        }
        // dd($addons);
        $data->id = $source->id;
        $data->membership_id = $source->membership_id;
        $data->membershipText = $source->Membership ? $source->Membership->{'title_'.LANGUAGE_PREF} : '';
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->title_ar = $source->title_ar != null ? $source->title_ar : '';
        $data->title_en = $source->title_en != null ? $source->title_en : '';
        $data->description = $source->{'description_'.LANGUAGE_PREF};
        $data->description_ar = $source->description_ar != null ? $source->description_ar : '';
        $data->description_en = $source->description_en != null ? $source->description_en : '';
        $data->addons = $source->addons != null ? unserialize($source->addons) : [];
        $data->addonsText = $source->addons != null ? str_replace(',', ' <br>', implode(',', reset($addons))) : [];
        $data->addonsArr = $source->addons != null ? $addons : [];
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
