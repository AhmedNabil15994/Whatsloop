<?php

namespace App\Models;

use App\Models\CentralUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Contracts\Syncable;
use Session;
// use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class User extends Authenticatable implements Syncable
{
    use HasFactory,\TraitsFunc,\ResourceSync;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'duration_type',
        'email',
        'password',
        'code',
        'phone',
        'domain',
        'company',
        'membership_id',
        'addons',
        'group_id',
        'channels',
        'extra_rules',
        'image',
        'pin_code',
        'emergency_number',
        'notifications',
        'offers',
        'two_auth',
        'sort',
        'is_active',
        'is_approved',
        'global_id',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = [];
    public $timestamps = false;

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
        return CentralUser::class;
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
            'image',
            'pin_code',
            'emergency_number',
            'two_auth',
            'company',
            'membership_id',
            'duration_type',
            'addons',
            'status',
            'is_active',
            'is_approved',
        ];
    }

    public function Group(){
        return $this->belongsTo('App\Models\Group','group_id');
    }

    public function Membership(){
        return $this->belongsTo('App\Models\Membership','membership_id');
    }

    public function PaymentInfo(){
        return $this->hasOne('App\Models\PaymentInfo','user_id');
    }
    
    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('users', $id, $photo);
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
            $source->where('group_id',$group_id);
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
        return self::getData(self::with(['Group','PaymentInfo','Membership'])->find(USER_ID));
    }
    
    static function selectImage($source){
        if($source->image != null){
            return self::getPhotoPath($source->id, $source->image);
        }else{
            return asset('images/def_user.svg');
        }
    }

    static function getData($source,$langPref=null) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->global_id = $source->global_id;
        $data->photo = self::selectImage($source);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->group = $source->Group != null ? $langPref == null ? $source->Group->{'name_'.LANGUAGE_PREF} : $source->Group->{'name_'.$langPref} : '';
        $data->duration_type = $source->duration_type;
        $data->group_id = $source->group_id;
        $data->email = $source->email;
        $data->company = $source->company;
        $data->name = $source->name != null ? $source->name : '';
        $data->phone = $source->phone != null ? str_replace('+', '', $source->phone) : '';
        $data->status = $source->status;
        $data->notifications = $source->notifications;
        $data->offers = $source->offers;
        $data->domain = $source->domain;
        $data->is_old = $source->is_old;
        $data->is_synced = $source->is_synced;
        $data->sort = $source->sort;
        $data->pin_code = $source->pin_code;
        $data->emergency_number = $source->emergency_number;
        $data->two_auth = $source->two_auth;
        $data->membership_id = $source->membership_id;
        $data->membership = $source->Membership != null ? $source->Membership->{'title_'.LANGUAGE_PREF} : '';
        $data->addons = $source->addons;
        $data->paymentInfo = $source->PaymentInfo != null ? $source->PaymentInfo : '';
        $data->extra_rules = $source->extra_rules != null ? unserialize($source->extra_rules) : [];
        $data->channels = $source->channels != null ? unserialize($source->channels) : [];
        $data->channelCodes = !empty($data->channels) ? implode(',', unserialize($source->channels)) : '';
        $data->channelIDS = unserialize($source->channels);
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
            ->where($type,$value)->where('status',1)->first();

        if ($notId != false) {
            $dataObj->whereNotIn('id', [$notId])->first();
        }

        return $dataObj;
    }

    static function checkUserPermissions($userObj) {
        if($userObj->group_id == 1){
            return array_values(\Helper::getAllPerms());
        }
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
        return count(array_intersect($rule, \Session::has('user_id') ? PERMISSIONS : [])) > 0;
    }

    static function setSessions($user){
        $isAdmin = in_array($user->group_id, [1,]);
        $userObj = $user;
        if(!$isAdmin){
            $userObj = User::first();  
        }

        session(['group_id' => $userObj->group_id]);
        session(['global_id' => $userObj->global_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['domain' => $userObj->domain]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => $userObj->Group->name_ar]);
        // $channels = User::getData($userObj)->channels;
        $channels = $userObj->channels != null ? UserChannels::NotDeleted()->whereIn('id',unserialize($userObj->channels))->get() : [];
        session(['channel' => !empty($channels) ? $channels[0]->id : null]);
        session(['channelCode' => !empty($channels) ? CentralChannel::where('id',$channels[0]->id)->first()->instanceId : null ]);
        session(['membership' => $userObj->membership_id]);

        
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $userAddons = $userObj->addons !=  null ? UserAddon::dataList(unserialize($userObj->addons),$userObj->id) : [];
        session(['addons' => !empty($userAddons) ? $userAddons[0] : [] ]);
        session(['deactivatedAddons' => !empty($userAddons) ? $userAddons[1] : [] ]);
        session(['disabledAddons' => !empty($userAddons) ? $userAddons[2] : [] ]);
        session(['phone' => $userObj->phone ]);
        session(['tenant_id' => $tenantObj->tenant_id]);
        session(['is_old' => $userObj->is_old]);
        session(['is_synced' => $userObj->is_synced]);
        $invoiceObj = Invoice::getDisabled($userObj->id);
        session(['invoice_id' => $invoiceObj == null ? 0 : $invoiceObj->id]);

        // Get Membership and Extra Quotas Features
        if(!empty($userObj->membership_id)){
            $membershipFeatures = \DB::connection('main')->table('memberships')->where('id',Session::get('membership'))->first()->features;
            $featuresId = unserialize($membershipFeatures);
            $features = \DB::connection('main')->table('membership_features')->whereIn('id',$featuresId)->pluck('title_en');
            $dailyMessageCount = (int) $features[0];
            $employessCount = (int) $features[1];
            $storageSize = (int) $features[2];
            session(['dailyMessageCount' => $dailyMessageCount]);
            session(['employessCount' => $employessCount]);
            session(['storageSize' => $storageSize]);
        }
    }

}
