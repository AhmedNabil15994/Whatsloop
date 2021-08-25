<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model{

    use \TraitsFunc;

    protected $table = 'faqs';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('faqs', $id, $photo,false);
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
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        if($status != null){
            $source->where('status',$status);
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

        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->title_ar = $source->title_ar;
        $data->title_en = $source->title_en;
        $data->description_ar = $source->description_ar;
        $data->description_en = $source->description_en;
        $data->title = $source->{'title_'.LANGUAGE_PREF};
        $data->description = $source->{'description_'.LANGUAGE_PREF};
        $data->photo = self::getPhotoPath($source->id, $source->image);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->type = $data->photo != '' ? \ImagesHelper::checkFileExtension($data->photo) : '';
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
