<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class GroupMsg extends Model{

    use \TraitsFunc;

    protected $table = 'group_messages';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function Group(){
        return $this->belongsTo('App\Models\Group','group_id');
    }

    public function photos(){
        return $this->morphMany('App\Models\Photo', 'imageable');
    }
    
    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('users', $id, $photo);
    }

    static function dataList($status=null,$userIds= null,$group_id=null) {
        $input = \Request::all();

        $source = self::NotDeleted();

        if (isset($input['email']) && !empty($input['email'])) {
            $source->where('email', 'LIKE', '%' . $input['email'] . '%');
        }
        if (isset($input['group_id']) && !empty($input['group_id'])) {
            $source->where('group_id',  $input['group_id']);
        }
        if($status != null){
            $source->where('status',1)->where('is_active',1);
        }
        if($group_id != null){
            $source->where('group_id',$group_id)->where('is_active',1);
        }
        if($userIds != null){
            $source->whereNotIn('id',$userIds);
        }
        if(isset($input['status']) && $input['status'] != ''){
            $source->where('status',$input['status']);
        }
        if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
            $source->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
        }
        $source->orderBy('sort', 'ASC');
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

    static function selectImage($source){
        
        if($source->image != null){
            return self::getPhotoPath($source->id, $source->image);
        }else{
            if(isset($source->Company)){
                return Company::getPhotoPath($source->Company->id,$source->Company->logo);
            }else{
                return Variable::getVar('الصورة الافتراضية للمشرفين:');
            }
        }
    }


    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->photo = self::selectImage($source);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? self::getPhotoSize($data->photo) : '';
        $data->group = $source->Group != null ? $source->Group->title : '';
        $data->group_id = $source->group_id;
        $data->email = $source->email;
        $data->name = $source->name != null ? $source->name : '';
        $data->whatsapp_no = $source->whatsapp_no != null ? $source->whatsapp_no : '';
        $data->phone = $source->phone != null ? $source->phone : '';
        $data->username = $source->username;
        $data->session_time = $source->session_time;
        $data->lang = $source->lang;
        $data->notify_me = $source->notify_me;
        $data->code = $source->code;
        $data->views = $source->views;
        $data->is_active = $source->is_active;
        $data->status = $source->status;
        $data->statusText = $source->status == 1 ? 'مفعل' : 'غير مفعل' ;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function getOne($id) {
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }


    static function getLoginUser($username){
        $userObj = self::NotDeleted()
            ->where('username', $username)
            ->where('is_active', 1)
            ->where('status', 1)
            ->first();

        if($userObj == null ) { //  || $userObj->Profile->group_id != 1
            return false;
        }

        return $userObj;
    }

    static function getPhotoSize($url){
        if($url == "" || !is_file($url)){
            return '';
        }
        $image = get_headers($url, 1);
        $bytes = $image["Content-Length"];
        $mb = $bytes/(1024 * 1024);
        return number_format($mb,2) . " MB ";
    }

    static function checkUserPermissions($userObj) {
        $endPermissionUser = [];
        $endPermissionGroup = [];

        $groupObj = $userObj->Group;
        $groupPermissions = $groupObj != null ? $groupObj->permissions : null;

        $groupPermissionValue = unserialize($groupPermissions);
        if($groupPermissionValue != false){
            $endPermissionGroup = $groupPermissionValue;
        }

        $permissions = (array) array_unique(array_merge($endPermissionUser, $endPermissionGroup));

        return $permissions;
    }

    static function userPermission(array $rule){

        if(USER_ID && IS_ADMIN == false) {
            return count(array_intersect($rule, PERMISSIONS)) > 0;
        }

        return true;
    }

    static function checkUserByEmail($email, $notId = false){
        $dataObj = self::NotDeleted()
            ->where('email', $email)->where('is_active', 1)
            ->where('status', 1);

        if ($notId != false) {
            $dataObj->whereNotIn('id', [$notId]);
        }

        return $dataObj->first();
    }

    static function checkUserByPhone($phone, $notId = false){
        $dataObj = self::NotDeleted()
            ->where('phone', $phone)->where('is_active', 1)
            ->where('status', 1);

        if ($notId != false) {
            $dataObj->whereNotIn('id', [$notId]);
        }

        return $dataObj->first();
    }

    static function checkUserByUserName($username, $notId = false){
        $dataObj = self::NotDeleted()->where('username',$username);

        if ($notId != false) {
            $notId = (array) $notId;
            $dataObj->whereNotIn('id', $notId);
        }

        return $dataObj->first();
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
