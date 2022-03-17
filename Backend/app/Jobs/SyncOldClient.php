<?php
namespace App\Jobs;
// ini_set('memory_limit', '128M');

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;
use Schema;
use App\Models\User;
use App\Models\Group;
use App\Models\CentralUser;
use App\Models\GroupMsg;
use App\Models\GroupNumber;
use App\Models\Category;
use App\Models\Contact;
use App\Models\ChatDialog;
use App\Models\Template;
use App\Models\ChatMessage;
use App\Models\ContactLabel;
use App\Models\Reply;
use App\Models\Bot;
use App\Models\CentralChannel;
use App\Models\CentralVariable;
use App\Models\UserChannels;
use App\Models\PaymentInfo;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\ModTemplate;
use App\Models\Variable;
use App\Models\ModNotificationReport;


class SyncOldClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    // New Data
    // users
    // groups
    // groupNumbers
    // contacts
    // chat
    // bot
    // group_messages
    // quick_reply
    // tags
    // salla
    // zid


    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $userObj;
    public $requiredSync;
    public $connectionInitiated;
    public $token;
    public $channel;
    public $addons;
    public $baseUrl;
    public $instanceId;
    public $membership_id;
    public $tenant_id;
    public $groupId;

    public function __construct($userObj,$requiredSync)
    {
        $this->userObj = $userObj;
        $this->requiredSync = $requiredSync;
        $this->membership_id = 1;
        $this->connectionInitiated = 0;
        $this->token = '';
        $this->instanceId = '';
        $this->tenant_id = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first()->tenant_id;
        $this->addons = [];
        $this->channel = 50000;
        $this->baseUrl = 'https://whatsloop.net/api/v1/';
        $this->groupId = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->userObj->is_old == 1 && $this->userObj->is_synced == 0){
            $this->pullData();
        }
        // foreach($this->requiredSync as $oneItem){
        //     if($oneItem == 'contacts'){
        //         $this->pullContacts();
        //     }
        // }
    }

    public function getIDS($data)
    {
        $data = str_replace('#','',$data);
        $data = explode(',',$data);
        return $data;
    }

    public function initConnection(){
        // Get User Details
        $userObj = $this->userObj;
        $mainURL = $this->baseUrl.'user-details';
        $data = ['phone' => str_replace('+','',$userObj->phone) /*'966570116626'*/];
        if($this->connectionInitiated == 0){
            $result =  Http::post($mainURL,$data);
            if($result->ok() && $result->json()){
                $data = $result->json();
                if($data['status'] == true){
                    $moduleData = [];
                    // Begin Sync
                    $this->connectionInitiated = 1;
                    $this->token = $data['UserData']['JWTToken'];
                }
            }
        }
    }

    public function pullInstanceData(){
        // Get User Instace
        $userObj = $this->userObj;
        $mainURL = $this->baseUrl.'migration/user-instance';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $instanceData = @$data['data']['Instance'][0];
                $packageData = @$data['data']['Package'];

                $channel = [
                    'id' => $instanceData['instanceId'],
                    'token' => $instanceData['instanceToken'],
                    'name' => $instanceData['WhatsAppName'],
                    'start_date' => date('Y-m-d',$instanceData['StartDate']),
                    'end_date' => $instanceData['EndDate'] != '' ? date('Y-m-d',$instanceData['EndDate']) : date('Y-m-d',$instanceData['StartDate']),
                ];

                $central = CentralChannel::generateNewKey($channel['token']);

                $extraData = [
                    'instanceId' => $central[0],
                    'instanceToken' => $central[1],
                    'tenant_id' => $this->tenant_id,
                    'global_user_id' => $userObj->global_id,
                ];

                $extraChannelData = array_merge($channel,$extraData);

                if($instanceData['instanceId'] != ''){
                    $centralChannel = CentralChannel::where('id',$instanceData['instanceId'])->first();
                    if(!$centralChannel){
                        CentralChannel::create($extraChannelData);                    
                    }else{
                        $centralChannel->update($channel);
                        $centralChannel->update([
                            'tenant_id' => $this->tenant_id,
                            'global_user_id' => $userObj->global_id,
                        ]);
                    }

                    $tenantChannel = UserChannels::where('id',$channel['id'])->first();
                    if(!$tenantChannel){
                        UserChannels::create($channel);                    
                    }else{
                        $tenantChannel->update($channel);                    
                    }
                }

                $this->channel = $instanceData['instanceId'];
                $this->instanceId = $extraData['instanceId'];

                $this->determineMembership($packageData,$instanceData);
            }
        }
    }

    public function determineMembership($membershipObj,$instanceData)
    {
        $userObj = $this->userObj;
        $instanceId = $this->instanceId;
        $addons = [];
        $membershipObj = (object) $membershipObj;
        $start_date = $instanceData['StartDate'];
        $end_date = $instanceData['EndDate'] != '' ? $instanceData['EndDate'] : $instanceData['StartDate'];

        if($membershipObj->Title_ar == 'المنصة التفاعلية'){
            $addons[] = 2;
            $addons[] = 3;
        }elseif($membershipObj->Title_ar == 'باقه البوت'){
            $addons[] = 1;
        }elseif($membershipObj->Title_ar == 'باقه API'){
        }elseif($membershipObj->Title_ar == 'باقه شاملة' || $membershipObj->Title_ar == 'باقة خدمة عملاء واتس لوب'){
            $addons[] = 1; // Bot
            $addons[] = 2; // LiveChat
            $addons[] = 3; // Group Message
            $addons[] = 4; // Zid
            $addons[] = 5; // Salla
            $addons[] = 11; // API
        }elseif($membershipObj->Title_ar == 'باقه زد'){
            $addons[] = 4;
            $addons[] = 1;
        }elseif($membershipObj->Title_ar == 'باقة سلة'){
            $addons[] = 5;
            $addons[] = 1;
        }

        \DB::connection('main')->table('old_memberships')->insert([
            'membership' => $membershipObj->Title_ar,
            'user_id' => $userObj->id,
        ]);

        $datediff = $end_date - $start_date;
        $daysLeft = (int) round($datediff / (60 * 60 * 24));
        $duration_type = 1;
        if($daysLeft > 30){
            $duration_type = 2;
        }
        
        if($this->channel != 50000){
            User::where('id',$userObj->id)->update([
                'duration_type' =>  $duration_type,
                'membership_id' => $this->membership_id ,
                'channels' => serialize([$this->channel]),
                'addons' => serialize($addons),
            ]);

            CentralUser::where('id',$userObj->id)->update([
                'duration_type' =>  $duration_type,
                'membership_id' => $this->membership_id ,
                'channels' => serialize([$this->channel]),
                'addons' => serialize($addons),
            ]);

            foreach($addons as $addon_id){
                $oneAddonData = [
                    'tenant_id' => $this->tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'addon_id' => $addon_id,
                    'status' => 1,
                    'duration_type' => $duration_type,
                    'start_date' => date('Y-m-d',$start_date),
                    'end_date' => date('Y-m-d',$end_date), 
                ];
                $userAddonObj = UserAddon::where('user_id',$userObj->id)->where('addon_id',$addon_id)->first();
                if($userAddonObj){
                    $userAddonObj->update($oneAddonData);
                }else{
                    UserAddon::insert($oneAddonData);
                }
            }

            $this->addons = $addons;

            if(in_array(4,$addons)){
                $service = 'zid';
                $baseUrl = CentralVariable::getVar('ZidURL');
                $storeID = $instanceData['ZID_StoreID'];
                $storeToken = CentralVariable::getVar('ZidMerchantToken');
                $managerToken = $instanceData['ZID_MANAGER_TOKEN'];

                Variable::where('var_key','ZidStoreID')->firstOrCreate([
                    'var_key' => 'ZidStoreID',
                    'var_value' => $storeID,
                ]);
                Variable::where('var_key','ZidStoreToken')->firstOrCreate([
                    'var_key' => 'ZidStoreToken',
                    'var_value' => $managerToken
                ]);

                $models = ['customers','orders','products','abandoned-carts'];
                foreach($models as $modelName){
                    $params = [];
                    if($modelName == 'products'){
                        $dataURL = $baseUrl.'/'.$modelName.'/'; 
                    }elseif(in_array($modelName,['customers','orders','abandoned-carts'])){
                        $dataURL = $baseUrl.'/managers/store/'.$modelName.'/'; 
                    }

                    $tableName = $service.'_'.$modelName;
                    if($modelName == 'abandoned-carts'){
                        $params = [
                            'page' => 1,
                            'page_size' => 100,
                        ];
                        $tableName = $service.'_abandonedCarts';
                    }


                    $myHeaders = [
                        "X-MANAGER-TOKEN" => $managerToken,
                        "STORE-ID" => $storeID,
                        "ROLE" => 'Manager',
                        'User-Agent' => 'whatsloop/1.00.00 (web)',
                    ];

                    $dataArr = [
                        'baseUrl' => $baseUrl,
                        'storeToken' => $storeToken,
                        'dataURL' => $dataURL,
                        'tableName' => $tableName,
                        'myHeaders' => $myHeaders,
                        'service' => $service,
                        'params' => $params,
                    ];
                    // dd($dataArr);
                    $externalHelperObj = new \ExternalServices($dataArr);

                    if (!Schema::hasTable($tableName)) {
                        $externalHelperObj->startFuncs();
                    }
                }

            }
            
            if(in_array(5,$addons)){
                Variable::where('var_key','SallaStoreToken')->firstOrCreate([
                    'var_key' => 'SallaStoreToken',
                    'var_value' => $instanceData['Salla_APIKey'],
                ]);

                $service = 'salla';
                $baseUrl = CentralVariable::getVar('SallaURL');
                $storeToken = $instanceData['Salla_APIKey']; 

                $models = ['customers','orders','products','abandonedCarts'];
                foreach($models as $modelName){
                    $dataURL = $baseUrl.'/'.$modelName;
                    if($modelName == 'abandonedCarts'){
                        $dataURL = $baseUrl.'/carts/abandoned';
                    }
                    $tableName = $service.'_'.$modelName;
                    $myHeaders =[];

                    $dataArr = [
                        'baseUrl' => $baseUrl,
                        'storeToken' => $storeToken,
                        'dataURL' => $dataURL,
                        'tableName' => $tableName,
                        'myHeaders' => $myHeaders,
                        'service' => $service,
                        'params' => [],
                    ];

                    if($modelName == 'orders'){
                        $newDataArr = $dataArr;
                        $newDataArr['dataURL'] = $dataArr['dataURL'].'/statuses';
                        $newDataArr['tableName'] = $service.'_order_status';  

                        $externalHelperObj = new \ExternalServices($newDataArr);
                        if (!Schema::hasTable($tableName)) {
                            $externalHelperObj->startFuncs();
                        }
                    }

                    $externalHelperObj = new \ExternalServices($dataArr);
                    if ((!Schema::hasTable($tableName))) {
                        $externalHelperObj->startFuncs();
                    }
                }

            }    
        }
    }

    public function pullGroupNumbers()
    {
        $userObj = $this->userObj;
        // Get User Group Numbers
        $mainURL = $this->baseUrl.'migration/user-contacts-groups';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['NumbersGroups'];
                GroupNumber::where('name_en','Sync')->delete();
                foreach($loopData as $oneItemData){
                    if( $oneItemData['id'] != 1){
                        $item = [
                            'id' => $oneItemData['id'],
                            'channel' => $oneItemData['Title_en'] == 'Sync' ? '' : $this->instanceId,
                            'name_ar' => $oneItemData['Title_ar'],
                            'name_en' => $oneItemData['Title_en'],
                            'status' => 1,
                            'created_by' => $userObj->id,
                            'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                        ];
                        $dataObj = GroupNumber::find($oneItemData['id']);
                        if($dataObj){
                            $dataObj->update($item);
                        }else{
                            GroupNumber::create($item);
                        }
                    }
                }
            }
        }
    }

    public function pullLabels()
    {
        $userObj = $this->userObj;
        // Get User Labels
        $mainURL = $this->baseUrl.'migration/user-labels';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['Labels'];
                foreach($loopData as $oneItemData){
                    $item = [
                        'id' => $oneItemData['id'],
                        'channel' => $this->instanceId,
                        'name_ar' => $oneItemData['Title_ar'],
                        'name_en' => $oneItemData['Title_en'],
                        'color_id' => Category::getColorIndex($oneItemData['Color']),
                        'labelId' => '',
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                    ];

                    $labelId = '';
                    $mainWhatsLoopObj = new \MainWhatsLoop();
                    $data['name'] = Category::reformLabelName($oneItemData['Title_ar'],$oneItemData['Title_en']);
                    $addResult = $mainWhatsLoopObj->createLabel($data);
                    $result = $addResult->json();
                    if($result['status']['status'] == 1){
                        $labelId = isset($result['data']['label']) && !empty($result['data']['label']) ? $result['data']['label']['id'] : null;
                    }
                    $item['labelId'] = $labelId;

                    $dataObj = Category::find($oneItemData['id']);
                    if($dataObj){
                        $dataObj->update($item);
                    }else{
                        Category::create($item);
                    }
                }
            }
        }
    }

    public function pullBasicTemplates()
    {
        $userObj = $this->userObj;
        // Get User Group Numbers
        $mainURL = $this->baseUrl.'migration/whats-msgs-temp';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['WhatsMsgsTemp'];
                foreach($loopData as $oneItemData){
                    if( $oneItemData['id'] != 1){
                        $item = [
                            'id' => $oneItemData['id'],
                            'channel' => $this->instanceId,
                            'name_ar' => $oneItemData['Title_ar'],
                            'name_en' => $oneItemData['Title_en'],
                            'description_ar' => $oneItemData['Content_ar'],
                            'description_en' => $oneItemData['Content_en'],
                            'status' => 1,
                            'created_by' => $userObj->id,
                            'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                        ];
                        $dataObj = Template::find($oneItemData['id']);
                        if($dataObj){
                            $dataObj->update($item);
                        }else{
                            Template::create($item);
                        }
                    }
                }
            }
        }
    }

    public function pullContacts()
    {
        $userObj = $this->userObj;
        // Get User Contacts
        $mainURL = $this->baseUrl.'migration/user-contacts';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['Contacts']['data'];
                $lastPage = @$data['Contacts']['last_page'];

                if($lastPage > 1){
                    for ($i = 2; $i <= $lastPage ; $i++) {
                        $params =  ['page' => $i];
                        $result =  Http::withToken($this->token)->get($mainURL,$params);
                        if($result->ok() && $result->json()){
                            $data = $result->json();
                            if($data['status'] == true){
                                $loopData = array_merge($loopData,@$data['Contacts']['data']);
                            }
                        }
                    }
                }

                foreach($loopData as $oneItemData){
                    $item = [
                        'id' => $oneItemData['id'],
                        'group_id' => $oneItemData['Groupid'],
                        'has_whatsapp' => 1,
                        'phone' => '+'.$oneItemData['Number'],
                        'name' => $oneItemData['Name_ar'],
                        'email' => $oneItemData['Email'],
                        'city' => $oneItemData['Town'],
                        'country' => $oneItemData['Country'],
                        'lang' => $oneItemData['Language'] != '' && $oneItemData['Language'] == 'عربي' ? 0 : 1,
                        'notes' => $oneItemData['AdditionalInfo'],
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                    ];
                    $contactObj = Contact::find($oneItemData['id']);
                    if($contactObj){
                        $contactObj->update($item);
                    }else{
                        Contact::create($item);
                    }

                    $dialog = [
                        'id' => $oneItemData['ChatApiID'],
                        'name' => $oneItemData['Name_ar'],
                        'image' => $oneItemData['Photo'],
                        'metadata' => 'a:3:{s:7:"isGroup";b:0;s:12:"participants";a:0:{}s:15:"groupInviteLink";N;}',
                        'is_pinned' => $oneItemData['PinToTop'],
                        'is_read' => 0,
                        'modsArr' => serialize($this->getIDS($oneItemData['Moderators'])),
                    ];

                    $dialogObj = ChatDialog::find($oneItemData['ChatApiID']);
                    if($dialogObj){
                        $dialogObj->update($dialog);
                    }else{
                        ChatDialog::create($dialog);
                    }

                    $contactLabels = $this->getIDS($oneItemData['Labels']);
                    if(count($contactLabels) > 0){
                        foreach($contactLabels as $contactLabel){
                            if($contactLabel != 0){
                                $label = [
                                    'contact' => $oneItemData['Number'],
                                    'category_id' => $contactLabel,
                                    'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                                ];

                                $labelObj = ContactLabel::where('contact',$oneItemData['Number'])->where('category_id',$contactLabel)->first();
                                if($labelObj){
                                    $labelObj->update($label);
                                }else{
                                    ContactLabel::create($label);
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    public function pullQuickReplies()
    {
        $userObj = $this->userObj;
        // Get User Quick Replies
        $mainURL = $this->baseUrl.'migration/user-quick-reply';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['QuickReplies'];
                foreach($loopData as $oneItemData){
                    $item = [
                        'id' => $oneItemData['id'],
                        'channel' => $this->instanceId,
                        'name_ar' => $oneItemData['Title_ar'],
                        'name_en' => $oneItemData['Title_en'],
                        'description_ar' => $oneItemData['Content_ar'],
                        'description_en' => $oneItemData['Content_en'],
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                    ];

                    $dataObj = Reply::find($oneItemData['id']);
                    if($dataObj){
                        $dataObj->update($item);
                    }else{
                        Reply::create($item);
                    }
                }
            }
        }
    }

    public function getRules($modSections)
    {
        $rules = '';
        $modSections = explode(',',$modSections);
        foreach($modSections as $modSection){
            if($modSection == 'Home'){
                $rules.= 'general,';
            }elseif($modSection == '#SALLA'){
                $rules.= 'salla-customers,salla-products,salla-abandoned-carts,salla-orders,';
            }elseif($modSection == 'Salla_Settings'){
                $rules.= 'updateSalla,';
            }elseif($modSection == 'Salla_WhatsTemp'){
                $rules.= 'salla-templates,';
            }elseif($modSection == 'Salla_SendStatus'){
                $rules.= 'salla-reports,';
            }elseif($modSection == '#ZID'){
                $rules.= 'zid-customers,zid-products,zid-abandoned-carts,zid-orders,';
            }elseif($modSection == 'ZIDSettings'){
                $rules.= 'updateZid,';
            }elseif($modSection == 'WhatsZidTemp'){
                $rules.= 'zid-templates,';
            }elseif($modSection == 'ZIDSendStatus'){
                $rules.= 'zid-reports,';
            }elseif($modSection == 'LiveChatNew' || $modSection == 'LiveChat'){
                $rules.= 'list-livechat,';
            }elseif($modSection == '#BOT' || $modSection == 'BotMsgs'){
                $rules.= 'list-bots,charts-bot,';
            }elseif($modSection == 'CreateBot'){
                $rules.= 'add-bot,copy-bot,';
            }elseif($modSection == '#GROUPMSGS' || $modSection == 'GroupMsgs'){
                $rules.= 'list-group-messages,view-group-message,';
            }elseif($modSection == 'SendGroupMsgs'){
                $rules.= 'add-group-message,';
            }elseif($modSection == 'MsgsLogs'){
                $rules.= 'list-messages-archive,';
            }elseif($modSection == 'Labels'){
                $rules.= 'list-categories,';
            }elseif($modSection == 'QuickReplies'){
                $rules.= 'list-replies,';
            }elseif($modSection == 'Contacts'){
                $rules.= 'list-contacts,';
            }elseif($modSection == 'ExcelContacts'){
                $rules.= 'export-contacts,';
            }elseif($modSection == 'NumbersGroups'){
                $rules.= 'list-group-numbers,';
            }elseif($modSection == 'AddNumbersToGroups'){
                $rules.= 'add-number-to-group,';
            }elseif($modSection == 'MyAccount' || $modSection == 'Profile'){
                $rules.= 'profile,paymentInfo,taxInfo,notifications,offers,';
            }elseif($modSection == 'WhatsAppAccountsStatus'){
                $rules.= 'list-statuses,';
            }elseif($modSection == '#MSGSTEMP' || $modSection == 'WhatsMsgsTemp'){
                $rules.= 'list-templates,';
            }elseif($modSection == 'APITab' || $modSection == 'API'){
                $rules.= 'apiSetting,';
            }elseif($modSection == 'APIDocs'){
                $rules.= 'apiGuide,';
            }elseif($modSection == 'WebHookSettings'){
                $rules.= 'webhookSetting,';
            }
        }
        $data = explode(',',$rules);
        $data = array_filter(array_unique($data));
        return serialize($data);
    }

    public function pullGroupsData()
    {
        $userObj = $this->userObj;
        // Get User Groups
        $mainURL = $this->baseUrl.'migration/user-moderators-groups';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['ModeratorsGroups'];
                Group::truncate();
                foreach($loopData as $oneItemData){
                    if($oneItemData['Title_ar'] != 'مدير'){
                        $item = [
                            'id' => $oneItemData['id'],
                            'name_ar' => $oneItemData['Title_ar'],
                            'name_en' => $oneItemData['Title_en'],
                            'status' => 1,
                            'rules' => $this->getRules($oneItemData['Mod_Sections']),
                            'created_by' => $userObj->id,
                            'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                        ];

                        $dataObj = Group::find($oneItemData['id']);
                        if($dataObj){
                            $dataObj->update($item);
                        }else{
                            Group::create($item);
                        }
                    }else{
                        $this->groupId = $oneItemData['id'];
                    }
                }
            }
        }
    }

    public function pullUsersData()
    {
        $userObj = $this->userObj;
        // Get User Moderators
        $mainURL = $this->baseUrl.'migration/user-moderators';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['Moderatos'];
                $mainUser = User::first();
                $channel_id = $mainUser->channels;
                foreach($loopData as $oneItemData){
                    $item = [
                        'name' => $oneItemData['Username'],
                        'group_id' => $oneItemData['AdminsGroups'] == $this->groupId ? 1 : $oneItemData['AdminsGroups'],
                        'email' => $oneItemData['Email'],
                        'channels' => $channel_id,
                        'two_auth' => 0,
                        'phone' => '+'.$oneItemData['Mobile'],
                        'password' => \Hash::make('whatsloop'),
                        'extra_rules' => '',
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',strtotime($oneItemData['Date'])),
                    ];
                    $dataObj = User::where('phone',$item['phone'])->first();
                    if($dataObj){
                        if($dataObj->id == $mainUser->id){
                            unset($item['group_id']);
                            unset($item['password']);
                            if($item['email'] == ''){
                                unset($item['email']);
                            }
                            $dataObj->update($item);
                        }
                    }else{
                        User::create($item);
                    }
                    $oneSignalPlayerId = $oneItemData['OneSignalPlayerID'];
                    $oneSignalPlayerIdAndroid = $oneItemData['OneSignalPlayerIDAndroid'];
                    if($oneSignalPlayerId != ''){
                         Variable::where('var_key','ONESIGNALPLAYERID_'.$oneItemData['Mobile'])->firstOrCreate([
                            'var_key' => 'ONESIGNALPLAYERID_'.$oneItemData['Mobile'],
                            'var_value' => $oneSignalPlayerId,
                        ]);
                    }

                    if($oneSignalPlayerIdAndroid != ''){
                         Variable::where('var_key','ONESIGNALPLAYERIDANDROID_'.$oneItemData['Mobile'])->firstOrCreate([
                            'var_key' => 'ONESIGNALPLAYERIDANDROID_'.$oneItemData['Mobile'],
                            'var_value' => $oneSignalPlayerIdAndroid,
                        ]);
                    }
                }
            }
        }
    }

    public function pullBots()
    {
        $userObj = $this->userObj;
        // Get User Bots
        $mainURL = $this->baseUrl.'migration/user-bot';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['Bot'];
                foreach($loopData as $oneItemData){
                    $basicData = [
                        'id' => $oneItemData['id'],
                        'channel' => $this->instanceId,
                        'message_type' => 1,
                        'message' => $oneItemData['ClientMessage'],
                        'reply_type' => (int)$oneItemData['MessageType'] + 1,
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                    ];
                    
                    $extraData = [];
                    if($basicData['reply_type'] == 1){
                        $extraData = [
                            'reply' => $oneItemData['MessageContent'],
                        ];
                    }elseif($basicData['reply_type'] == 2){
                        $extraData = [
                            'reply' => $oneItemData['MessageFileCaption'],
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['reply_type'] == 3){
                        $extraData = [
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['reply_type'] == 4){
                        $extraData = [
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['reply_type'] == 5){
                        $extraData = [
                            'https_url' => $oneItemData['MessageLink'],
                            'url_title' => $oneItemData['MessageTitle'],
                            'url_desc' => $oneItemData['MessageDescription'],
                            'url_image' => $oneItemData['MessageLinkPhoto'],
                        ];   
                    }elseif($basicData['reply_type'] == 6){
                        $extraData = [
                            'whatsapp_no' => '+'.$oneItemData['MessageContact'],
                        ];   
                    }elseif($basicData['reply_type'] == 7){
                        $extraData = [
                            'lat' => $oneItemData['MapLatitude'],
                            'lng' => $oneItemData['MapLongitude'],
                            'address' => $oneItemData['MapAddress'],
                        ];   
                    }elseif($basicData['reply_type'] == 8){
                        $extraData = [
                            'webhook_url' => $oneItemData['WebHookLink'],
                        ];   
                    }

                    $item = array_merge($basicData,$extraData);
                    $dataObj = Bot::find($oneItemData['id']);
                    if($dataObj){
                        $dataObj->update($item);
                    }else{
                        Bot::create($item);
                    }
                }
            }
        }
    }

    public function pullMessages()
    {
        $userObj = $this->userObj;
        // Get User Dialogs
        $mainURL = $this->baseUrl.'migration/user-chats';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['Chats']['data'];
                $lastPage = @$data['Chats']['last_page'];

                if($lastPage > 1){
                    for ($i = 2; $i <= $lastPage ; $i++) {
                        $params =  ['page' => $i];
                        $result =  Http::withToken($this->token)->get($mainURL,$params);
                        if($result->ok() && $result->json()){
                            $data = $result->json();
                            if($data['status'] == true){
                                $loopData = array_merge($loopData,@$data['Chats']['data']);
                            }
                        }
                    }
                }

                foreach($loopData as $oneItemData){
                    $messageData = $this->getMessageData($oneItemData['MessageType'],$oneItemData['MessagePhotoFileOgg']);
                    $item = [
                        'id' => $oneItemData['MessageId'],
                        'body' => $oneItemData[$messageData[2]],
                        'fromMe' => '+'.$oneItemData['SenderNumber'] == $userObj->phone ? 1 : 0,
                        'isForwarded' => 0,
                        'author' => $oneItemData['SenderNumber'].'@c.us',
                        'caption' => $oneItemData['MessageFileCaption'],
                        'time' => $oneItemData['Date'],
                        'chatId' => $oneItemData['ChatApiID'],
                        'messageNumber' => $oneItemData['id'],
                        'message_type' => $messageData[0],
                        'type' => $messageData[1],
                        'sending_status' => $oneItemData['SeenStatus'],
                        'status' => 1,
                        'senderName' => '+'.$oneItemData['SenderNumber'] ,
                        'chatName' => '+'. (str_replace('@c.us','',$oneItemData['ChatApiID'])),
                    ];

                    if(in_array($messageData[0], ['photo','image','video','sound'])){
                        $folder = '/home/wloop/public_html/public/uploads/'.$this->tenant_id.'/chats/'.$item['body'];
                        $url = 'https://whatsloop.net/resources/Gallery/'.$item['body'];
                        // $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $directory = '/home/wloop/public_html/public/uploads/'.$this->tenant_id.'/chats/';
                        // dd($extension);
                        if(!file_exists($folder)){
                            @$content = file_get_contents($url);                    
                            if(!file_exists($directory)){
                                @mkdir($directory, 0777, true);
                            }
                            $succ = file_put_contents($folder, $content);   
                        }
                        $item['body'] = 'https://wloop.net/public/uploads/'.$this->tenant_id.'/chats/'.$item['body']; 
                    }

                    $dataObj = ChatMessage::find($item['id']);
                    if($dataObj){
                        $dataObj->update($item);
                    }else{
                        ChatMessage::create($item);
                    }
                }
            }
        }
    }

    public function getMessageData($type,$file_name){
        if($type == 0){
            return ['text','chat','MessageContent'];
        }elseif($type == 1){
            $check = \ImagesHelper::checkExtensionType(substr($file_name, strrpos($file_name, '.') + 1));
            $whats_message_type = $check == 'photo' ? 'image' : 'document' ;
            return [$check,$whats_message_type,'MessagePhotoFileOgg'];
        }elseif($type == 2){
            return ['video','video','MessagePhotoFileOgg'];
        }elseif($type == 3){
            return ['sound','ppt','MessagePhotoFileOgg'];
        }elseif($type == 4){
            return ['link','link','MessageLink'];
        }elseif($type == 5){
            return ['contact','contact','MessageContact'];
        }elseif($type == 6){
            return ['location','location','MapAddress'];
        }elseif($type == 7){
            return ['webhook','webhook','MessageContent'];
        }elseif($type == 8){
            return ['call','call','MessageContent'];
        }
    }

    public function pullGroupMsgs()
    {
        $userObj = $this->userObj;
        // Get User Group Messages
        $mainURL = $this->baseUrl.'migration/user-group-msgs';
        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                $loopData = @$data['GroupMsgs'];
                foreach($loopData as $oneItemData){
                    $basicData = [
                        'id' => $oneItemData['id'],
                        'channel' => $this->instanceId,
                        'group_id' => $oneItemData['Groupid'],
                        'messages_count' => $oneItemData['MessagesCount'],
                        'message_type' => (int)$oneItemData['MessageType'] + 1,
                        'later' => 0,
                        'publish_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                        'status' => 1,
                        'created_by' => $userObj->id,
                        'created_at' => date('Y-m-d H:i:s',$oneItemData['Date']),
                    ];

                    $extraData = [];
                    if($basicData['message_type'] == 1){
                        $extraData = [
                            'message' => $oneItemData['MessageContent'],
                        ];
                    }elseif($basicData['message_type'] == 2){
                        $extraData = [
                            'message' => $oneItemData['MessageFileCaption'],
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['message_type'] == 3){
                        $extraData = [
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['message_type'] == 4){
                        $extraData = [
                            'file_name' => $oneItemData['MessagePhotoFileOgg'],
                        ];   
                    }elseif($basicData['message_type'] == 5){
                        $extraData = [
                            'https_url' => $oneItemData['MessageLink'],
                            'url_title' => $oneItemData['MessageTitle'],
                            'url_desc' => $oneItemData['MessageDescription'],
                            'url_image' => $oneItemData['MessageLinkPhoto'],
                        ];   
                    }elseif($basicData['message_type'] == 6){
                        $extraData = [
                            'whatsapp_no' => '+'.$oneItemData['MessageContact'],
                        ];   
                    }elseif($basicData['message_type'] == 7){
                        $extraData = [
                            'lat' => $oneItemData['MapLatitude'],
                            'lng' => $oneItemData['MapLongitude'],
                            'address' => $oneItemData['MapAddress'],
                        ];   
                    }elseif($basicData['reply_type'] == 8){
                        $extraData = [
                            'webhook_url' => $oneItemData['WebHookLink'],
                        ];   
                    }

                    $item = array_merge($basicData,$extraData);

                    $dataObj = GroupMsg::find($oneItemData['id']);
                    if($dataObj){
                        $dataObj->update($item);
                    }else{
                        GroupMsg::create($item);
                    }
                }
            }
        }
    }

    public function pullTemplates($modId)
    {
        $userObj = $this->userObj;
        if($modId == 1){
            // Get User Salla Templates
            $mainURL = $this->baseUrl.'migration/user-salla-templates';
        }elseif($modId == 2){
            // Get User Zid Templates
            $mainURL = $this->baseUrl.'migration/user-zid-templates';
        }

        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                if($modId == 1){
                    $loopData = @$data['SallaTemplates'];
                }elseif($modId == 2){
                    $loopData = @$data['ZidTemplates'];
                }

                if(count($loopData) > 0){
                    ModTemplate::where('mod_id',$modId)->delete();
                }
                foreach($loopData as $oneItemData){
                    $item = [
                        'channel' => $this->instanceId,
                        'statusText' => $oneItemData['Status'],
                        'content_ar' => $oneItemData['Content_ar'],
                        'content_en' => $oneItemData['Content_ar'],
                        'mod_id' => $modId,
                        'status' => $oneItemData['Type'] == 'on' ? 1 : 0,
                        'updated_by' => $userObj->id,
                        'updated_at' => date('Y-m-d H:i:s',(int)$oneItemData['Updated_Date']),
                    ];

                    ModTemplate::create($item);
                }
            }
        }
    }

    public function pullNotificationReports($modId)
    {
        $userObj = $this->userObj;
        if($modId == 1){
            // Get User Salla Notification Reports
            $mainURL = $this->baseUrl.'migration/user-salla-status';
        }elseif($modId == 2){
            // Get User Zid Notification Reports
            $mainURL = $this->baseUrl.'migration/user-zid-status';
        }

        $result =  Http::withToken($this->token)->get($mainURL);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                if($modId == 1){
                    $loopData = @$data['SallaStatus'];
                }elseif($modId == 2){
                    $loopData = @$data['ZidStatus'];
                }

                if(count($loopData) > 0){
                    ModNotificationReport::where('mod_id',$modId)->delete();
                }

                foreach($loopData as $oneItemData){
                    if($modId == 1){
                        // Salla
                        if($oneItemData['waitpayment'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'بإنتظار الدفع',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['waitpayment_Date']),
                            ]);
                        }

                        if($oneItemData['waitpreview'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'بإنتظار المراجعة',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['waitpreview_Date']),
                            ]);
                        }

                        if($oneItemData['underprogress'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'قيد التنفيذ',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['underprogress_Date']),
                            ]);
                        }

                        if($oneItemData['progressed'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'تم التنفيذ',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['progressed_Date']),
                            ]);
                        }
                        
                        if($oneItemData['indelivery'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'جاري التوصيل',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['indelivery_Date']),
                            ]);
                        }

                        if($oneItemData['delivered'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'تم التوصيل',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['delivered_Date']),
                            ]);
                        }

                        if($oneItemData['charged'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'تم الشحن',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['charged_Date']),
                            ]);
                        }

                        if($oneItemData['canceled'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'ملغي',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['canceled_Date']),
                            ]);
                        }

                        if($oneItemData['retrieved'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'مسترجع',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['retrieved_Date']),
                            ]);
                        }
                    }elseif($modId == 2){
                        //Zid
                        if($oneItemData['new'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'جديد',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['new_Date']),
                            ]);
                        }

                        if($oneItemData['preparing'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'جاري التجهيز',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['preparing_Date']),
                            ]);
                        }

                        if($oneItemData['ready'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'جاهز',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['ready_Date']),
                            ]);
                        }

                        if($oneItemData['indelivery'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'جارى التوصيل',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['indelivery_Date']),
                            ]);
                        }

                        if($oneItemData['delivered'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'تم التوصيل',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['delivered_Date']),
                            ]);
                        }

                        if($oneItemData['cancelled'] == 1){
                            ModNotificationReport::create([
                                'mod_id' => $modId,
                                'order_id' => $oneItemData['OrderID'],
                                'statusText' => 'تم الالغاء',
                                'created_at' => date('Y-m-d H:i:s',(int) $oneItemData['cancelled_Date']),
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function pullData()
    {
        $userObj = $this->userObj;
        $syncData = $this->requiredSync;

        if($this->token == ''){
            $this->initConnection($userObj);
        }
        
        $this->pullInstanceData();

        $this->pullGroupsData();  
        
        $this->pullUsersData();

        $this->pullGroupNumbers();   

        $this->pullLabels();  

        $this->pullBasicTemplates();   

        $this->pullContacts();  

        $this->pullQuickReplies();

        if(in_array(1,$this->addons)){            
            $this->pullBots();
        }

        if(in_array(2,$this->addons)){            
            $this->pullMessages(); 
        }

        if(in_array(3,$this->addons)){            
            $this->pullGroupMsgs();
        }

        if(in_array(4,$this->addons)){            
            $this->pullTemplates(2); // Zid Templates
            $this->pullNotificationReports(2);
        }
        
        if(in_array(5,$this->addons)){            
            $this->pullTemplates(1); // Salla Templates
            $this->pullNotificationReports(1);
        }

        $this->finalizeMigration();
    }

    public function finalizeMigration(){
        $userObj = $this->userObj;
        if(count($this->userObj->channels) > 0){
            // if(in_array(3,$this->addons)){
            //     \Artisan::call('tenants:run groupMsg:send --tenants='.$this->tenant_id);
            // }

            // \Artisan::call('tenants:run instance:status --tenants='.$this->tenant_id);

            // if(in_array(2,$this->addons)){
            //     \Artisan::call('tenants:run sync:messages --tenants='.$this->tenant_id);
            //     \Artisan::call('tenants:run sync:dialogs --tenants='.$this->tenant_id);
            // }

            // \Artisan::call('push:channelSetting');
            // if(in_array(4,$this->addons) || in_array(5,$this->addons)){
            //     \Artisan::call('push:addonSetting');
            // }

            User::where('id',$userObj->id)->update([
                'is_synced' =>  1,
            ]);

            CentralUser::where('id',$userObj->id)->update([
                'is_synced' =>  1,
            ]);
        }

    }

}
