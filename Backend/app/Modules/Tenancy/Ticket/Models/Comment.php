<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model{

    use \TraitsFunc;

    protected $connection= 'main';
    protected $table = 'comments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('comments', $id, $photo,false);
    }

    public function Creator(){
        return $this->belongsTo('App\Models\User','created_by','id');
    }

    public function Ticket(){
        return $this->belongsTo('App\Models\Ticket','ticket_id','id');
    }

    static function getOne($id){
        $source = self::NotDeleted()
            ->where('id', $id);

        return $source->first();
    }

    static function dataList($ticket_id=null,$main=null) {
        $input = \Request::all();
        $source = self::NotDeleted()->where('status',1);

        if (isset($ticket_id) && $ticket_id != null ) {
            $source->where('ticket_id', $ticket_id);
        } 
        if (isset($main) && $main != null ) {
            $source->where('reply_on', $main);
        }else{
            $source->where('reply_on',0);
        }
        
        if(isset($input['user_id']) && !empty($input['user_id'])){
            $source->where('created_by',$input['user_id']);
        }

        if(isset($input['ticket_id']) && !empty($input['ticket_id'])){
            $source->where('ticket_id',$input['ticket_id']);
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
        return $list;
    }

    static function getData($source) {
        $creator = User::where('id',$source->Ticket->created_by)->first();
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->admin = $source->admin;
        $data->ticket_id = $source->ticket_id;
        $data->comment = $source->comment; 
        $data->creator_name = $source->creator_name; 
        $data->status = $source->status;
        $data->reply_on = $source->reply_on;
        $data->created_by = $source->created_by;
        $data->replies = $source->reply_on == 0 ? self::dataList($source->ticket_id,$source->id) : [];
        $data->image = User::selectImage($creator);
        $data->creator = $creator->name;
        $data->file_name = $source->file_name;
        $data->file = $source->file_name != null ? self::getPhotoPath($source->id, $source->file_name) : "";
        $data->file_size = $data->file != '' ? \ImagesHelper::getPhotoSize($data->file) : '';
        $data->file_type = $data->file != '' ? \ImagesHelper::checkFileExtension($data->file_name) : '';
        $data->created_at = \Carbon\Carbon::createFromTimeStamp(strtotime($source->created_at))->diffForHumans();
        return $data;
    }

}
