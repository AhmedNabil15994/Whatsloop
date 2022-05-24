<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralComment extends Model{

    use \TraitsFunc;

    protected $table = 'comments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function Creator(){
        return $this->belongsTo('App\Models\CentralUser','created_by','id');
    }

    public function Ticket(){
        return $this->belongsTo('App\Models\CentralTicket','ticket_id','id');
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
        $data = new  \stdClass();
        if($source->admin){
            $creator = CentralUser::getData($source->Creator);
        }else{
            $creator = CentralUser::getData(CentralUser::where('global_id',$source->Ticket->global_id)->first());
        }
        $data->id = $source->id;
        $data->ticket_id = $source->ticket_id;
        $data->comment = $source->comment; 
        $data->admin = $source->admin; 
        $data->creator_name = $source->creator_name; 
        $data->status = $source->status;
        $data->reply_on = $source->reply_on;
        $data->replies = $source->reply_on == 0 ? self::dataList($source->ticket_id,$source->id) : [];
        $data->image = $creator->photo;
        $data->creator = $creator->name;
        $data->created_at = \Carbon\Carbon::createFromTimeStamp(strtotime($source->created_at))->diffForHumans();
        return $data;
    }

}
