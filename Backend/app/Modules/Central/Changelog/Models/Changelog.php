<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Changelog extends Model{

    use \TraitsFunc;

    protected $table = 'changelogs';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Category(){
        return $this->belongsTo('App\Models\CentralCategory','category_id');
    }

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('changeLogs', $id, $photo,false);
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null) {
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
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        if($status != null){
            $source->where('status',$status);
        }
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
        $data->color = $source->Category != null ? $source->Category->color : '';
        $data->title_ar = $source->title_ar;
        $data->title_en = $source->title_en;
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->description_ar = $source->description_ar;
        $data->description_en = $source->description_en;
        $data->description = $source->{'description_'.LANGUAGE_PREF};
        $data->photo = self::getPhotoPath($source->id, $source->image);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        $data->dateForHuman = \Carbon\Carbon::parse($data->created_at)->diffForHumans();
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
