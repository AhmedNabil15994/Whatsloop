<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model{

    use \TraitsFunc;

    protected $table = 'notification_templates';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'type',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'status',
    ];
   
    static function getOne($type,$title)
    {
        return self::NotDeleted()->where('status',1)->where('type',$type)->where('title_en',$title)->first();
    }

    static function getOneById($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }
    
    static function dataList($status=null,$ids=null) {
        $input = \Request::all();

        $source = self::NotDeleted();

        if (isset($input['title_ar']) && !empty($input['title_ar'])) {
            $source->where('title_ar', 'LIKE', '%' . $input['title_ar'] . '%');
        } 
        if (isset($input['id']) && !empty($input['id'])) {
            $source->where('id',  $input['id']);
        } 
        if (isset($input['type']) && !empty($input['type'])) {
            $source->where('type',  $input['type']);
        } 
       
        if($status != null){
            $source->where('status',$status);
        }
        if($ids != null){
            $source->whereIn('id',$ids);
        }    

        $source->orderBy('id','ASC');

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
        $data->content_ar = $source->content_ar != null ? $source->content_ar : '';
        $data->content_en = $source->content_en != null ? $source->content_en : '';
        $data->type = $source->type;
        $data->typeText = $source->type == 1 ? trans('main.whatsAppMessage') :  trans('main.emailMessage');
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? 'مسودة' : 'مفعلة';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

}
