<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model{

    use \TraitsFunc;

    protected $table = 'statuses';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Category(){
        return $this->belongsTo('App\Models\StatusCategory','category_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
            if (isset($input['title_ar']) && !empty($input['title_ar'])) {
                $query->where('title_ar', 'LIKE', '%' . $input['title_ar'] . '%');
            } 
            if (isset($input['title_en']) && !empty($input['title_en'])) {
                $query->where('title_en', 'LIKE', '%' . $input['title_en'] . '%');
            } 
            if (isset($input['category_id']) && !empty($input['category_id'])) {
                $query->where('category_id',  $input['category_id']);
            } 
            if (isset($input['status']) && !empty($input['status'])) {
                $query->where('status',  $input['status']);
            } 
            
        });
        $source->orderBy('id','DESC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->category_id = $source->category_id;
        $data->category = $source->Category != null ? $source->Category->{'title_'.LANGUAGE_PREF} : '';
        $data->title_ar = $source->title_ar;
        $data->title_en = $source->title_en;
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->description_ar = $source->description_ar;
        $data->description_en = $source->description_en;
        $data->description = $source->{'description_'.LANGUAGE_PREF};
        $data->status = $source->status;
        $data->statusText = $source->status == 1 ? trans('main.active') : trans('main.notActive');
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        $data->dateForHuman = \Carbon\Carbon::parse($data->created_at)->diffForHumans();
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
