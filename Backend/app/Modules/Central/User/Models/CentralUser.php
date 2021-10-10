<?php

namespace App\Models;

use App\Models\Tenant;
use App\Models\TenantPivot;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
// use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class CentralUser extends Model implements SyncMaster
{
    // Note that we force the central connection on this model
    use \ResourceSync, \TraitsFunc, CentralConnection;

    protected $guarded = [];
    public $timestamps = false;
    public $table = 'users';

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users', 'global_user_id', 'tenant_id', 'global_id')
            ->using(TenantPivot::class);
    }

    public function getTenantModelName(): string
    {
        return User::class;
    }

    public function getGlobalIdentifierKey()
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }

    public function getGlobalIdentifierKeyName(): string
    {
        return 'global_id';
    }

    public function getCentralModelName(): string
    {
        return static::class;
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'name',
            'password',
            'phone',
            'email',
            'notifications',
            'offers',
            'pin_code',
            'emergency_number',
            'two_auth',
            'company',
            'membership_id',
            'image',
            'duration_type',
            'addons',
            'status',
            'is_active',
            'is_approved',
        ];
    }

    public function Group(){
        return $this->belongsTo('App\Models\CentralGroup','group_id');
    }

    public function Membership(){
        return $this->belongsTo('App\Models\Membership','membership_id');
    }

    // public function PaymentInfo(){
    //     return $this->hasOne('App\Models\PaymentInfo','user_id');
    // }
    
    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('users', $id, $photo,false);
    }

    static function dataList($group_id = null,$ids = null,$langPref=null) {
        $input = \Request::all();

        $source = self::NotDeleted();
        if (isset($input['name']) && !empty($input['name'])) {
            $source->where('name', 'LIKE', '%' . $input['name'] . '%');
        }
        if (isset($input['channels']) && !empty($input['channels'])) {
            $source->where('channels', 'LIKE', '%' . $input['channels'] . '%');
        }
        if (isset($input['email']) && !empty($input['email'])) {
            $source->where('email', 'LIKE', '%' . $input['email'] . '%');
        }
        if (isset($input['group_id']) && !empty($input['group_id'])) {
            $source->where('group_id',  $input['group_id']);
        }
        if (isset($input['phone']) && !empty($input['phone'])) {
            $source->where('phone',  $input['phone']);
        }
        if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
            $source->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
        }
        if($group_id != null){
            if($group_id == 'domains'){
                $source->where('group_id',0);
            }else{
                $source->where('group_id',$group_id);
            }
        }
        if($ids != null){
            $source->whereIn('id',$ids);
        }
        $source->orderBy('sort', 'ASC');
        return self::generateObj($source,$langPref);
    }

    static function generateObj($source,$langPref=null){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value,$langPref);
        }

        $data['data'] = $list;

        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

    static function authenticatedUser(){
        return self::getData(self::getOne(USER_ID));
    }
    
    static function selectImage($source){
        if($source->image != null){
            $data = self::getPhotoPath($source->id, $source->image);
            return ($data == "" ? asset('tenancy/assets/images/def_user.svg') : $data);
        }else{
            return asset('tenancy/assets/images/def_user.svg');
        }
    }
// $tenant = Tenant::create([
//             'phone' => request('phone'),
//             'title' => request('title'),
//             'description' => request('description')
//         ]);
        
//         $tenant->domains()->create([
//             'domain' => request('subdomain'),
//         ]);

    static function  getDomain($source)
    {
        $domain = '';
        $tenant = $source->tenants()->first();
        if($tenant != null){
            $domain = $tenant->domains()->first()->domain;
        }
        return $domain;
    }

    static function getData($source,$langPref=null) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->photo = self::selectImage($source);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->group = $source->Group != null ? $langPref == null ? $source->Group->{'name_'.LANGUAGE_PREF} : $source->Group->{'name_'.$langPref} : '';
        $data->group_id = $source->group_id;
        $data->email = $source->email != null ? $source->email : '';
        $data->company = $source->company;
        $data->name = $source->name != null ? $source->name : '';
        $data->phone = $source->phone != null ? str_replace('+', '', $source->phone) : '';
        $data->status = $source->status;
        $data->notifications = $source->notifications;
        $data->offers = $source->offers;
        $data->setting_pushed = $source->setting_pushed;
        $data->duration_type = $source->duration_type;
        $data->pin_code = $source->pin_code;
        $data->emergency_number = $source->emergency_number;
        $data->two_auth = $source->two_auth;
        $data->membership_id = $source->membership_id;
        $data->addons = $source->addons;
        $data->is_old = $source->is_old;
        $data->is_synced = $source->is_synced;
        $data->domain = self::getDomain($source); //$tenants->first()->domains()->first()->domain : '';
        $data->sort = $source->sort;
        // $data->paymentInfo = $source->PaymentInfo != null ? $source->PaymentInfo : '';
        $data->extra_rules = $source->extra_rules != null ? unserialize($source->extra_rules) : [];
        $data->channels = $source->channels != null ? UserChannels::NotDeleted()->whereIn('id',unserialize($source->channels))->get() : [];
        $data->channelCodes = !empty($data->channels) ?  implode(',', unserialize($source->channels)) : '';
        $data->channelIDS = !empty($data->channels) ? unserialize($source->channels) : [];
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }
    
    static function getOne($id) {
        return self::where('id', $id)
            ->first();
    }

    static function getLoginUser($email){
        $userObj = self::where('email', $email)->where('status',1)
            ->first();

        if($userObj == null ) { //  || $userObj->Profile->group_id != 1
            return false;
        }

        return $userObj;
    }

     static function checkUserBy($type,$value, $notId = false){
        $dataObj = self::NotDeleted()
            ->where($type,$value)->where('status',1);

        if ($notId != false) {
            $dataObj->whereNotIn('id', [$notId]);
        }

        return $dataObj->first();
    }

    static function checkUserPermissions($userObj) {
        $endPermissionUser = [];
        $endPermissionGroup = [];

        $groupObj = $userObj->Group;
        $groupPermissions = $groupObj != null ? $groupObj->rules : null;

        $groupPermissionValue = unserialize($groupPermissions);
        if($groupPermissionValue != false){
            $endPermissionGroup = $groupPermissionValue;
        }
        $extra_rules = $userObj->extra_rules != null ? unserialize($userObj->extra_rules) : [];
        $permissions = (array) array_unique(array_merge($endPermissionUser, $endPermissionGroup,$extra_rules));

        return $permissions;
    }

    static function userPermission(array $rule){

        if(USER_ID && IS_ADMIN == false) {
            return count(array_intersect($rule, PERMISSIONS)) > 0;
        }
                            // <span class="m-form__help LastUpdate">تم الحفظ فى :  {{ $data->data->created_at }}</span>

        return true;
    }
}