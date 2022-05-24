<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCategory extends Model{

    use \TraitsFunc;

    protected $table = 'status_category';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Statuses(){
        return $this->hasMany('App\Models\Status','category_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->with('Statuses')->where(function ($query) use ($input,$status) {
                    if (isset($input['title']) && !empty($input['title'])) {
                        $query->where('title', 'LIKE', '%' . $input['title'] . '%');
                    } 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
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

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->title_ar = $source->title_ar != null ? $source->title_ar : '';
        $data->title_en = $source->title_en != null ? $source->title_en : '';
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->description_ar = $source->description_ar != null ? $source->description_ar : '';
        $data->description_en = $source->description_en != null ? $source->description_en : '';
        $data->description = $source->{'description_'.LANGUAGE_PREF};
        $data->statuses = Status::generateObj($source->Statuses(0))['data'];
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? 'مسودة' : 'مفعلة';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
