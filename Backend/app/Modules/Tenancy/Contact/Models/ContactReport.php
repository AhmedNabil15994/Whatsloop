<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class ContactReport extends Model{

    use \TraitsFunc;

    protected $table = 'contact_reports';
    protected $primaryKey = 'id';
    public $timestamps = false;
   
    public function Group(){
        return $this->belongsTo('App\Models\GroupNumber','group_id');
    }
    public function GroupMsg(){
        return $this->belongsTo('App\Models\GroupMsg','group_message_id');
    }

    static function getOne($id){
        return self::where('id', $id)
            ->first();
    }

    static function dataList($status=null) {
        $input = \Request::all();

        $source = self::with('Group')->where(function ($query) use ($input) {
                    if (isset($input['contact']) && !empty($input['contact'])) {
                        $query->where('contact', 'LIKE', '+' . $input['contact'] . '%');
                    } 
                    if (isset($input['group_id']) && !empty($input['group_id'])) {
                        $query->where('group_id', $input['group_id']);
                    } 
                    if (isset($input['group_message_id']) && !empty($input['group_message_id'])) {
                        $query->where('group_message_id', $input['group_message_id']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        if($status != null){
            $source->where('status',$status);
        }
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->whereHas('Group',function($groupQuery) use ($input){
                $groupQuery->where('channel',$input['channel']);
            });
        }else if(Session::has('channel')){
            $source->whereHas('Group',function($groupQuery){
                $groupQuery->where('channel',Session::get('channel'));
            });
        }

        if(isset($input['message_type']) && !empty($input['message_type'])){
            $source->whereHas('GroupMsg',function($groupQuery) use ($input){
                $groupQuery->where('message_type',$input['message_type']);
            });
        }

        if(isset($input['message_type']) && !empty($input['message_type'])){
            $source->whereHas('GroupMsg',function($groupQuery) use ($input){
                $groupQuery->where('message_type',$input['message_type']);
            });
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
        $groupObj = GroupNumber::getData($source->Group);
        $messageObj = GroupMsg::getData($source->GroupMsg);
        $data->id = $source->id;
        $data->group_id = $source->group_id;
        $data->group_message_id = $source->group_message_id;
        $data->channel = $groupObj->channel;
        $data->sender = $messageObj->creator;
        $data->group = $source->group_id != null ? $groupObj : [];
        $data->groupMessage = $source->group_message_id != null ? $messageObj : [];
        $data->message_type = $messageObj->message_type_text;
        $data->message_content = $messageObj->message;
        $data->phone = $source->contact;
        $data->message_id = $source->message_id;
        $data->phone2 = str_replace('+', '', $source->contact);       
        $data->status = self::getStatus($source->status,$messageObj);
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function getStatus($status,$groupMsgObj){
        if($groupMsgObj->sent_type == trans('main.publishSoon')){
            $status= "<span class='badge badge-dark'>".trans('main.publishSoon')."</span>";
            return $status;
        }

        if($status == 0){
            $status = "<span class='badge badge-danger'>".trans('main.notSent')."</span>";
        }else if($status == 1){
            $status = "<span class='badge badge-success'>".trans('main.sent')."</span>";
        }else if($status == 2){
            $status = "<span class='badge badge-info'>".trans('main.received')."</span>";
        }else if($status == 3){
            $status = "<span class='badge badge-primary'>".trans('main.seen')."</span>";
        }
        return $status;
    }

    static function newStatus($contact,$group_id,$group_message_id,$status,$message_id=''){
        $dataObj = self::where('contact',$contact)->where('group_id',$group_id)->where('group_message_id',$group_message_id)->first();

        if($dataObj == null){
            $dataObj = new self;
            $dataObj->contact = $contact;
            $dataObj->group_id = $group_id;
            $dataObj->group_message_id = $group_message_id;
            $dataObj->status = $status;
            $dataObj->message_id = $message_id;
            $dataObj->created_at = date('Y-m-d H:i:s');
            $dataObj->save();
        }

        $dataObj->status = $status;
        if($message_id != ''){
            $dataObj->message_id = $message_id;
        }
        $dataObj->created_at = date('Y-m-d H:i:s');
        $dataObj->save();
    }

}
