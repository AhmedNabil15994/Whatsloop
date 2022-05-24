<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralTicket extends Model{

    use \TraitsFunc;

    protected $table = 'tickets';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('tickets', $id, $photo,false);
    }

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','global_id','global_id');
    }

    public function Department(){
        return $this->belongsTo('App\Models\CentralDepartment','department_id');
    }

    public function LastComment(){
        return $this->hasOne(CentralComment::class,'ticket_id','id')->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->with('Creator')->where('id', '!=', null);
        });
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$user_id=null) {
        $input = \Request::all();
        $IS_ADMIN = IS_ADMIN;
        $USER_ID = USER_ID;
        $source = self::NotDeleted()->whereHas('Department',function($whereQuery) use ($IS_ADMIN,$USER_ID){
            if(!$IS_ADMIN){
                $whereQuery->where('emps','LIKE','%'.$USER_ID .'%');
            }
        })->where(function ($query) use ($input) {
                    if (isset($input['subject']) && !empty($input['subject'])) {
                        $query->where('subject', 'LIKE', '%' . $input['subject'] . '%');
                    } 
                    if (isset($input['department_id']) && !empty($input['department_id'])) {
                        $query->where('department_id', $input['department_id']);
                    } 
                    if (isset($input['priority_id']) && !empty($input['priority_id'])) {
                        $query->where('priority_id', $input['priority_id']);
                    } 
                    if (isset($input['status']) && !empty($input['status'])) {
                        $query->where('status', $input['status']);
                    } 
                    if (isset($input['user_id']) && !empty($input['user_id'])) {
                        $query->where('user_id', $input['user_id']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', date('Y-m-d',strtotime($input['from'])).' 00:00:00')->where('created_at','<=',date('Y-m-d',strtotime($input['to'])). ' 23:59:59');
                    }
                });
        if($status != null){
            $source->where('status',$status);
        }

        if($user_id != null){
            $source->where('user_id',$user_id);
        }

        if(!$IS_ADMIN){
            $source->orWhere('assignment','LIKE','%'.$USER_ID .'%');
        }

        $source->orderBy('sort','DESC');
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
        $client = $source->Client ? CentralUser::getData(CentralUser::where('global_id',$source->global_id)->first()) : null;
        $data->id = $source->id;
        $data->global_id = $source->global_id;
        $data->subject = $source->subject;
        $data->description = $source->description;
        $data->user_id = $client->id;
        $data->client = $source->Client != null ? $client->name : '';
        $data->client_image = $source->Client != null ? $client->photo : '';
        $data->department_id = $source->department_id;
        $data->department = $source->department_id != null ? $source->Department->{'title_'.LANGUAGE_PREF} : '';
        $data->priority_id = $source->priority_id;
        $data->priority = $source->priority_id != null ? self::getPriority($source->priority_id) : '';
        $data->assignment = $source->assignment != null ? unserialize($source->assignment) : [];
        $data->files = $source->files != null ? self::getImages(unserialize($source->files),$source->id) : [];
        $data->status = $source->status;
        $data->statusText = self::getStatus($source->status);
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        $data->last_comment = $source->LastComment != null && $source->LastComment->creator_name != null ? $source->LastComment->creator_name : '';
        $data->last_comment_date = $source->LastComment != null ? $source->LastComment->created_at : '';    
        return $data;
    }
   
    static function getStatus($status){
        $text = '';
        if($status == 1){
            $text = trans('main.open');
        }else if($status == 2){
            $text = trans('main.answered');
        }else if($status == 3){
            $text = trans('main.customerReply');
        }else if($status == 4){
            $text = trans('main.onHold');
        }else if($status == 5){
            $text = trans('main.inProgress');
        }else if($status == 6){
            $text = trans('main.closed');
        }
        return $text;
    }

    static function getPriority($priority_id){
        $text = '';
        if($priority_id == 1){
            $text = trans('main.low');
        }else if($priority_id == 2){
            $text = trans('main.medium');
        }else if($priority_id == 3){
            $text = trans('main.high');
        }
        return $text;
    }

    static function getImages($images,$id){
        $myImages = [];
        foreach ($images as $value) {
            $dataObj = new \stdClass();
            $dataObj->photo = self::getPhotoPath($id, $value);
            $dataObj->photo_name = $value;
            $dataObj->photo_size = \ImagesHelper::getPhotoSize($dataObj->photo);
            array_push($myImages, $dataObj);
        }
        return $myImages;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
