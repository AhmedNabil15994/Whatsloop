<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Contact extends Model{

    use \TraitsFunc;

    protected $table = 'contacts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'city',
        'country',
        'phone',
        'group_id',
        'lang',
        'notes',
        'sort',
        'status',
        'created_at',
        'created_by',
    ];
    public function Group(){
        return $this->belongsTo('App\Models\GroupNumber','group_id');
    }

    public function Reports(){
        return $this->hasMany('App\Models\ContactReport','contact','phone');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function newPhone($phone){
        $phone = '+'.str_replace('@c.us', '', $phone);
        $contactObj = self::where('phone',$phone)->first();
        if($contactObj == null){
            $contactObj = new self;
            $contactObj->name = $phone;
            $contactObj->phone = $phone;
            $contactObj->group_id = 1;
            $contactObj->sort = self::newSortIndex();
        }
        $contactObj->has_whatsapp = 1;
        $contactObj->status = 1;
        $contactObj->created_at = date('Y-m-d H:i:s');
        $contactObj->save();
    }

    static function getOneByPhone($phone){
        $contactObj = self::NotDeleted()->where('phone','+'.$phone)->orderBy('id','DESC')->first();
        if($contactObj != null){
            return self::getData($contactObj,null,null,true);
        }
    }

    static function dataList($status=null,$id=null,$group_id=null,$withMessageStatus=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['name']) && !empty($input['name'])) {
                        $query->where('name', 'LIKE', '%' . $input['name'] . '%');
                    } 
                    if (isset($input['email']) && !empty($input['email'])) {
                        $query->where('email', 'LIKE', '%' . $input['email'] . '%');
                    } 
                    if (isset($input['city']) && !empty($input['city'])) {
                        $query->where('city', 'LIKE', '%' . $input['city'] . '%');
                    } 
                    if (isset($input['country']) && !empty($input['country'])) {
                        $query->where('country', 'LIKE', '%' . $input['country'] . '%');
                    } 
                    if (isset($input['group_id']) && !empty($input['group_id'])) {
                        $query->where('group_id', $input['group_id']);
                    } 
                    if (isset($input['phone']) && !empty($input['phone'])) {
                        $query->where('phone', $input['phone']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
                if($status != null){
                    $source->where('status',$status);
                }
        if(Session::has('channel')){
            $source->whereHas('Group',function($groupQuery){
                $groupQuery->where('channel',Session::get('channel'))->orWhere('channel','');
            });
        }
        if($id != null){
            $source->whereNotIn('id',$id);
        }
        if($group_id != null){
            $source->where('group_id',$group_id);
        }
        $source->orderBy('sort','ASC');
        return self::generateObj($source,$withMessageStatus);
    }

    static function getFullContactsInfo($group_id,$group_message_id){
        $source = self::NotDeleted()->with('Reports')->where('group_id',$group_id);
        if(Session::has('channel')){
            $source->whereHas('Group',function($groupQuery){
                $groupQuery->where('channel',Session::get('channel'))->orWhere('channel','');
            });
        }
        $source->orderBy('sort','ASC');
        return self::generateObj($source,'withMessageStatus',$group_message_id);
    }

    static function getContactsReports(){
        $source = self::NotDeleted();
        if(Session::has('channel')){
            $source->whereHas('Group',function($groupQuery){
                $groupQuery->where('channel',Session::get('channel'))->orWhere('channel','');
            });
        }
        $source = $source->select('*','phone as phones',\DB::raw('count(*) as total'))->groupBy('created_at','group_id')->orderBy('created_at','DESC')->get();

        $list = [];
        $i = 1;
        foreach ($source as $key => $value) {
            $contacts = self::NotDeleted()->where('group_id',$value->group_id)->where('created_at',$value->created_at)->get();
            $contacts = reset($contacts);

            $myContacts = [];
            $hasWhatsapp = [];
            $hasNoWhatsapp = [];
            foreach ($contacts as $contact) {
                $myContacts[] = str_replace('+', '', $contact->phone);
                $hasWhatsapp[] = $contact->has_whatsapp  == 1 ? trans('main.yes') : '----'; 
                $hasNoWhatsapp[] = $contact->has_whatsapp  == 0 ? trans('main.yes') : '----'; 
            }
            
            $list[$key] = new \stdClass();
            $list[$key]->id = $i;
            $list[$key]->group_id = $value->group_id;
            $list[$key]->group_name = $value->Group->{'name_'.LANGUAGE_PREF};
            $list[$key]->status = trans('main.done');
            $list[$key]->total = $value->total;
            $list[$key]->hasWhatsapp = implode(' <br/> ', $hasWhatsapp);
            $list[$key]->hasNoWhatsapp = implode(' <br/> ', $hasNoWhatsapp);
            $list[$key]->contacts = implode(' <br/> ', $myContacts);
            $list[$key]->created_at = $value->created_at;
            $i++;
        }
        return $list;
    }

    static function generateObj($source,$withMessageStatus=null,$group_message_id=null){
        $sourceArr = $source->get();
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value,$withMessageStatus,$group_message_id);
        }
        $data['data'] = $list;
        return $data;
    }

    static function getData($source,$withMessageStatus=null,$group_message_id=null,$dets=false) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->group_id = $source->group_id;
        $data->group = $source->group_id != null ? $source->Group->{'name_'.LANGUAGE_PREF} : '';
        $data->phone = $source->phone;
        $data->phone2 = str_replace('+', '', $source->phone);
        $data->name = $source->name;
        $data->lang = $source->lang;
        $data->langText = $source->lang == 0 ? trans('main.arabic') : trans('main.english');
        $data->notes = $source->notes;
        $data->has_whatsapp = $source->has_whatsapp;
        $data->email = $source->email != null ? $source->email : '';
        $data->city = $source->city != null ? $source->city : '';
        if($dets != false){
            $cats = ContactLabel::where('contact',$data->phone2)->pluck('category_id');
            $data->labels = Category::dataList(reset($cats))['data'];
        }
        $data->country = $source->country != null ? $source->country : '';
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        if($withMessageStatus != null){
            $status = [];
            $groupMsgObj = GroupMsg::getData(GroupMsg::getOne($group_message_id));
            if($groupMsgObj->sent_type == trans('main.publishSoon')){
                $status= ['dark',trans('main.publishSoon')];
                $data->reportStatus = $status;
                return $data;
            }

            $reportObj = $source->Reports()->where('group_message_id',$group_message_id)->where('group_id',$source->group_id)->orderBy('id','DESC')->first();
            if($reportObj == null){
                $status= ['info',trans('main.inPrgo')];
            }else{
                if($reportObj->status == 0){
                    $status = ['danger',trans('main.notSent')];
                }else if($reportObj->status == 1){
                    $status = ['success',trans('main.sent')];
                }else if($reportObj->status == 2){
                    $status = ['info',trans('main.received')];
                }else if($reportObj->status == 3){
                    $status = ['primary',trans('main.seen')];
                }
            }
            $data->reportStatus = $status;

        }
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
