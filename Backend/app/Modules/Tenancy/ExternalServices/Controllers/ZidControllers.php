<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\ModTemplate;
use App\Models\User;
use App\Models\Variable;
use App\Models\Template;
use App\Models\CentralVariable;
use App\Jobs\AbandonedCart;
use App\Jobs\SyncAddonsData;
use App\Models\UserAddon;
use App\Models\ModNotificationReport;
use App\Models\OAuthData;
use App\Models\BotPlus;
use App\Models\Bot;
use App\Models\Category;
use App\Models\UserExtraQuota;
use App\Models\CartEvents;
use Storage;
use DB;
use DataTables;

class ZidControllers extends Controller {

    use \TraitsFunc;
    public $service = 'zid';

    public function checkPerm(){
        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(4,$disabled)){
            $dis = 1;
        }
    }

    public function settings(){
        $mainUser = User::first();
        // Prepare OAuth Data
        $oauthData = [
            'type' => 'zid',
            'user_id' => $mainUser->id,
            'tenant_id' => TENANT_ID,
            'phone' => $mainUser->phone,
            'domain' => $mainUser->domain,
            'created_at' => date('Y-m-d H:i:s'),

        ];

        $firstObj = OAuthData::where('type','zid')->orderBy('id','DESC')->first();
        if($firstObj->access_token != null){
            $oauthDataObj = OAuthData::where('type','zid')->where('user_id',$mainUser->id)->first();
            if(!$oauthDataObj){
                OAuthData::where('type','zid')->where('user_id',$mainUser->id)->create($oauthData);
            }

            $base_url = 'https://oauth.zid.sa';

            //Authorization Endpoint (Redirect)
            $queries = http_build_query([
                'client_id' => CentralVariable::getVar('ZID_CLIENT_ID'),
                'redirect_uri' => config('app.BASE_URL').'/oauth/callback',
                'response_type' => 'code'
            ]);
            // dd($queries);
            $input = \Request::all();

            $rules = [
                'store_id' => 'required',
            ];

            $message = [
                'store_id.required' => trans('main.storeIDValidation'),
            ];

            if(isset($input['store_id']) && !empty($input['store_id'])){
                $zidStoreID = Variable::NotDeleted()->where('var_key','ZidStoreID')->first();
                if($zidStoreID == null){
                    $zidStoreID = new Variable;
                    $zidStoreID->var_key = 'ZidStoreID';
                    $zidStoreID->var_value = $input['store_id'];
                    $zidStoreID->created_at = DATE_TIME;
                    $zidStoreID->created_by = USER_ID;
                    $zidStoreID->save();
                }else{
                    $zidStoreID->var_value = $input['store_id'];
                    $zidStoreID->updated_at = DATE_TIME;
                    $zidStoreID->updated_by = USER_ID;
                    $zidStoreID->save();
                }
            }

            if(isset($input['store_token']) && !empty($input['store_token'])){
                $zidStoreToken = Variable::NotDeleted()->where('var_key','ZidStoreToken')->first();
                if($zidStoreToken == null){
                    $zidStoreToken = new Variable;
                    $zidStoreToken->var_key = 'ZidStoreToken';
                    $zidStoreToken->var_value = $input['store_token'];
                    $zidStoreToken->created_at = DATE_TIME;
                    $zidStoreToken->created_by = USER_ID;
                    $zidStoreToken->save();
                }else{
                    $zidStoreToken->var_value = $input['store_token'];
                    $zidStoreToken->updated_at = DATE_TIME;
                    $zidStoreToken->updated_by = USER_ID;
                    $zidStoreToken->save();
                }
            }

            return redirect($base_url . '/oauth/authorize?' . $queries);
        }else{
            Session::flash('success', trans('main.try_again_in_minute'));
            return redirect()->to('/dashboard');
        }        
    }

    public function postSettings(Request $request){
        $input = $request->all();
        if($input['type'] == 'success'){
            $data = json_decode($input['data']);
            $storeId = Variable::getVar('ZidStoreID');

            $userObj = User::first();
            $addonObj = UserAddon::where('addon_id',4)->where('user_id',$userObj->id)->first();
            // if($addonObj->setting_pushed != 1){
                $webhookUrl = str_replace('://', '://'.$userObj->domain.'.', config('app.BASE_URL')).'/whatsloop/webhooks/zid-webhook';
                $actions = ['order.create','order.status.update','product.create','product.update','product.publish','product.delete'];
                $url = CentralVariable::getVar('ZidURL').'/managers/webhooks';
                
                $managerToken = CentralVariable::getVar('ZidMerchantToken2');
                $authorize = CentralVariable::getVar('ZidMerchantToken');
                $oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
                
                foreach($actions as $key => $action){
                    $urlData = [
                        'event' => $action,
                        'target_url' => $webhookUrl,
                        'original_id' => 610 + $key,
                        'subscriber' => ucwords(str_replace('.',' ',$action)).' Notify',
                        'conditions' => "{}",
                    ];
                    $payload = json_encode($urlData);


                    if($storeId && $managerToken){

                        $ch = curl_init($url);

                        curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload ); 
                        curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                            'Content-Type: application/json', 
                            'STORE-ID: '.$storeId,
                            'ROLE: Manager',
                            'X-MANAGER-TOKEN: '.$managerToken,
                            'User-Agent: whatsloop/1.00.00 (web)',
                            'Accept-Language: en',
                            'Authorization: Bearer '.$authorize,
                        )); 
                        curl_setopt($ch, CURLOPT_POST,           1 );
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
                        $result=curl_exec ($ch);   
                        $addonObj->setting_pushed = 1;
                        $addonObj->save();
                    }
                }
            // }


            Session::flash('success', trans('main.inPrgo'));
            return redirect()->to('/dashboard');     
        }else{
            Session::flash('success', trans('main.try_again_in_minute'));
            return redirect()->to('/dashboard');
        }
    }












    public function customers(Request $request){
        $input = \Request::all();
        $modelName = 'customers';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeToken = Variable::getVar('ZidStoreToken');
        $oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
        $authorize = $oauthDataObj != null && $oauthDataObj->token_type != null ? $oauthDataObj->token_type . ' ' . $oauthDataObj->authorization : $storeToken;

        $dataURL = $baseUrl.'/managers/store/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $storeToken,
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $authorize,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';

        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            try {
                dispatch(new SyncAddonsData($dataArr))->onConnection('cjobs');;
            } catch (Exception $e) {
                
            }
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }
        
        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    public function products(Request $request){
        $input = \Request::all();
        $modelName = 'products';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = Variable::getVar('ZidStoreToken');
        $oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
        $authorize = $oauthDataObj != null && $oauthDataObj->token_type != null ? $oauthDataObj->token_type . ' ' . $oauthDataObj->authorization : $storeToken;

        
        $dataURL = $baseUrl.'/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $storeToken,
            "STORE-ID" => $storeID,
            "ROLE" => 'Manager',
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $authorize,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [
                'page' => 1,
                'page_size' => 100,
            ],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';

        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            try {
                dispatch(new SyncAddonsData($dataArr))->onConnection('cjobs');;
            } catch (Exception $e) {
                
            }
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }
        
        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    public function orders(Request $request){
        $input = \Request::all();
        $modelName = 'orders';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeToken = Variable::getVar('ZidStoreToken');
        $oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
        $authorize = $oauthDataObj != null && $oauthDataObj->token_type != null ? $oauthDataObj->token_type . ' ' . $oauthDataObj->authorization : $storeToken;
        
        $dataURL = $baseUrl.'/managers/store/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $storeToken,
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $authorize,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';

        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            try {
                dispatch(new SyncAddonsData($dataArr))->onConnection('cjobs');;
            } catch (Exception $e) {
                
            }
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }

        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    protected function validateInsertBotPlusObject($input){
        $rules = [
            'title' => 'required',
            'body' => 'required',
            'footer' => 'required',
            'buttons' => 'required',
        ];

        $message = [
            'title.required' => trans('main.titleValidate'),
            'body.required' => trans('main.bodyValidate'),
            'footer.required' => trans('main.footerValidate'),
            'buttons.required' => trans('main.buttonsValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function resendCarts(){
        $input = \Request::all();
        $message = '';

        if(!isset($input['message_type']) || empty($input['message_type'])){
            return  \TraitsFunc::ErrorMessage(trans('main.messageTypeValidate'));
        }
        if(!isset($input['time']) || empty($input['time'])){
            return  \TraitsFunc::ErrorMessage(trans('main.timeValidate'));
        }
       
        if($input['message_type'] == 1){
            if(!isset($input['message_type']) || empty($input['message_type'])){
                Session::flash('error', trans('main.messageValidate'));
                return redirect()->back()->withInput();
            }
            $message = $input['content'];
        }elseif($input['message_type'] == 2){
            $message = '';
        }elseif($input['message_type'] == 3){
            $validate = $this->validateInsertBotPlusObject($input);
            if($validate->fails()){
                Session::flash('error', $validate->messages()->first());
                return redirect()->back()->withInput();
            }

            for ($i = 0; $i < $input['buttons']; $i++) {
                if(!isset($input['btn_text_'.($i+1)]) || empty($input['btn_text_'.($i+1)]) || $input['btn_text_'.($i+1)] == null ){
                    Session::flash('error', trans('main.invalidText',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                if(!isset($input['btn_reply_type_'.($i+1)]) || empty($input['btn_reply_type_'.($i+1)]) || $input['btn_reply_type_'.($i+1)] == null ){
                    Session::flash('error', trans('main.invalidType',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $replyType = (int)$input['btn_reply_type_'.($i+1)];
                if($replyType == 1 && ( !isset($input['btn_reply_'.($i+1)]) || empty($input['btn_reply_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidReply',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                if($replyType == 2 && ( !isset($input['btn_msg_'.($i+1)]) || empty($input['btn_msg_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $modelType = '';
                if($replyType == 2 && ( !isset($input['btn_msg_type_'.($i+1)]) || empty($input['btn_msg_type_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $modelType = (int)$input['btn_msg_type_'.($i+1)];
                $modelName = $modelType != '' ?  ($modelType == 1 ? '\App\Models\Bot' : '\App\Models\BotPlus')  : '';
                $msg = $replyType == 1 ? $input['btn_reply_'.($i+1)] : '';

                if($modelName != '' && $msg == ''){
                    $dataObj = $modelName::find($input['btn_msg_'.($i+1)]);
                    if($dataObj){
                        $msg = $dataObj->id;
                    }
                }

                $myData[] = [
                    'id' => $i + 1,
                    'text' => $input['btn_text_'.($i+1)],
                    'reply_type' => $input['btn_reply_type_'.($i+1)],
                    'msg_type' => $modelType,
                    'model_name' => $modelName,
                    'msg' => $msg,
                ];
            }
            $message = $input['body'];
        }

        $dataArr = [
            'type' => 1,
            'message_type' => $input['message_type'],
            'message' => $message,
            'time' => $input['time'],
            'file_name' => null,
            'caption' => null,
            'status' => 1,
            'bot_plus_id' => null,
            'created_at' => DATE_TIME,
            'created_by' => USER_ID,
        ];

        if(!isset($input['event_id']) || empty($input['event_id'])){
            $updates = [];
            $dataObjID = \DB::table('abandoned_carts_events')->insertGetId($dataArr);
            if(in_array($input['message_type'], [2])){
                $file = Session::get('msgFile');
                if($file){
                    $storageFile = Storage::files($file);
                    if(count($storageFile) > 0){
                        $images = self::addImage($storageFile[0],$dataObjID);
                        if ($images == false) {
                            Session::flash('error', trans('main.uploadProb'));
                            return redirect()->back()->withInput();
                        }
                        $updates['file_name'] = $images;
                        $updates['message'] = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/ZidCarts/'.$dataObjID.'/'.$images;
                        if(Session::has('msgFileType') && Session::get('msgFileType') != 'file'){
                            $updates['caption'] = $input['caption'];
                        }
                    }
                }
            }

            if($input['message_type'] == 3){
                $botObj = new BotPlus;
                $botObj->channel = Session::get('channelCode');
                $botObj->message_type = 1;
                $botObj->message = 'Zid AbandonedCart #'.$dataObjID;
                $botObj->title = $input['title'];
                $botObj->body = $input['body'];
                $botObj->footer = $input['footer'];
                $botObj->buttons = $input['buttons'];
                $botObj->buttonsData = serialize($myData);
                $botObj->sort = BotPlus::newSortIndex();
                $botObj->status = 1;
                $botObj->deleted_by = 1;
                $botObj->deleted_at = DATE_TIME;
                $botObj->save();

                $updates['bot_plus_id'] = $botObj->id;
            }
            if(!empty($updates)){
                \DB::table('abandoned_carts_events')->where('id',$dataObjID)->update($updates);
            }
            Session::flash('success', trans('main.addSuccess'));
        }else{
            $updates = [];
            $dataObjID = $input['event_id'];
            $eventObj = CartEvents::find($input['event_id']);
            if(!$eventObj){
                Session::flash('error', trans('main.notFound'));
                return redirect()->back();
            }

            if(in_array($input['message_type'], [2])){
                $file = Session::get('msgFile');
                if($file){
                    $storageFile = Storage::files($file);
                    if(count($storageFile) > 0){
                        $images = self::addImage($storageFile[0],$dataObjID);
                        if ($images == false) {
                            Session::flash('error', trans('main.uploadProb'));
                            return redirect()->back()->withInput();
                        }
                        $updates['file_name'] = $images;
                        $updates['message'] = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/ZidCarts/'.$dataObjID.'/'.$images;
                        if(Session::has('msgFileType') && Session::get('msgFileType') != 'file'){
                            $updates['caption'] = $input['caption'];
                        }
                    }
                }
            }

            if($input['message_type'] == 3){
                if($eventObj->bot_plus_id != null){
                    $botObj = BotPlus::find($eventObj->bot_plus_id);
                }else{
                    $botObj = new BotPlus;                    
                }
                $botObj->channel = Session::get('channelCode');
                $botObj->message_type = 1;
                $botObj->message = 'Zid AbandonedCart #'.$dataObjID;
                $botObj->title = $input['title'];
                $botObj->body = $input['body'];
                $botObj->footer = $input['footer'];
                $botObj->buttons = $input['buttons'];
                $botObj->buttonsData = serialize($myData);
                $botObj->sort = BotPlus::newSortIndex();
                $botObj->status = 1;
                $botObj->deleted_by = 1;
                $botObj->deleted_at = DATE_TIME;
                $botObj->save();

                $updates['bot_plus_id'] = $botObj->id;
                $updates['message'] = $message;
            }
            if(!empty($updates)){
                \DB::table('abandoned_carts_events')->where('id',$dataObjID)->update($updates);
            }
            Session::flash('success', trans('main.addSuccess'));
        }

        return redirect()->back();
    }

    public function getEvent(){
        $input = \Request::all();
        $eventObj = CartEvents::find($input['id']);
        if(!$eventObj){
            return \TraitsFunc::ErrorMessage(trans('main.notFound'));
        }
        $dataList['data'] = CartEvents::getData($eventObj);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function updateEvent(){
        $input = \Request::all();
        $eventObj = CartEvents::find($input['id']);
        if(!$eventObj){
            return \TraitsFunc::ErrorMessage(trans('main.notFound'));
        }
        $eventObj->status = $input['status'];
        $eventObj->save();

        $dataList['data'] = CartEvents::getData($eventObj);
        $dataList['status'] = \TraitsFunc::SuccessMessage(trans('main.editSuccess'));
        return \Response::json((object) $dataList);        
    }

    public function sendAbandoned(){
        $input = \Request::all();
        if(!isset($input['message']) || empty($input['message'])){
            return  \TraitsFunc::ErrorMessage(trans('main.messageValidate'));
        }

        if(!isset($input['clientsData']) || empty($input['clientsData'])){
            return  \TraitsFunc::ErrorMessage(trans('main.clientsValidate'));
        }

        Template::where('name_en','abandonedCarts')->update([
            'description_ar' => $input['message'],
        ]);
        
        if($input['sendTime'] == 1){
            try {
                dispatch(new AbandonedCart(2,$input))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
        }
        
        return \TraitsFunc::SuccessResponse(trans('main.inPrgo'));
    }

    public function abandonedCarts(Request $request){
        $tempObj = Template::where('name_en','abandonedCarts')->first();
        if(!$tempObj){
            Template::create([
                'channel' => \Session::get('channelCode'),
                'name_ar' => 'abandonedCarts',
                'name_en' => 'abandonedCarts',
                'description_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                سلتك المتروكة رقم ( {ORDERID} ) والاجمالي ({ORDERTOTAL}) 😎.

                اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

                ( {ORDERURL} )

                مع تحيات فريق عمل واتس لوب ❤️',
                            'description_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                سلتك المتروكة رقم ( {ORDERID} ) والاجمالي ({ORDERTOTAL}) 😎.

                اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

                ( {ORDERURL} )

                مع تحيات فريق عمل واتس لوب ❤️',
                'status' => 1,
            ]);
        } 
        

        $input = \Request::all();
        $modelName = 'abandonedCarts';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeToken = Variable::getVar('ZidStoreToken');
        $oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
        $authorize = $oauthDataObj != null && $oauthDataObj->token_type != null ? $oauthDataObj->token_type . ' ' . $oauthDataObj->authorization : $storeToken;
        
        $dataURL = $baseUrl.'/managers/store/abandoned-carts'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $storeToken,
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $authorize,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [
                'page' => 1,
                'page_size' => 100,
            ],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';

        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            try {
                dispatch(new SyncAddonsData($dataArr))->onConnection('cjobs');;
            } catch (Exception $e) {
                
            }
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }

        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }


    public function runModuleService($model,$tableName,$ajaxCheck=0){
        $input = \Request::all();
        $service = $this->service;
        $paginationNo = isset($input['recordNumber']) && !empty($input['recordNumber']) ? $input['recordNumber'] : 15;

        if (Schema::hasTable($tableName)) {
            $source = DB::table($tableName);
        }else{
            $source = [];
        }
        
        if($model == 'customers'){
            // Begin Search
            if(isset($input['name']) && !empty($input['name'])){
                $source->where('first_name','LIKE','%'.$input['name'].'%')->orWhere('last_name','LIKE','%'.$input['name'].'%');
            }

            if(isset($input['email']) && !empty($input['email'])){
                $source->where('email',$input['email']);
            }

            if(isset($input['phone']) && !empty($input['phone'])){
                $source->where('mobile',$input['phone']);
            }

            if(isset($input['address']) && !empty($input['address'])){
                $source->where('city','LIKE','%'.$input['address'].'%');
            }

            if($ajaxCheck){
                if(isset($input['recordNumber']) && !empty($input['recordNumber'])){
                    $paginationNo = $input['recordNumber'];
                }
            }

            if(isset($input['keyword']) && !empty($input['keyword'])){
                $source->where('name','LIKE','%'.$input['keyword'].'%')->orWhere('email','LIKE','%'.$input['keyword'].'%')->orWhere('mobile','LIKE','%'.$input['keyword'].'%')->orWhere('city','LIKE','%'.$input['keyword'].'%');
            }

            $modelData = $source == [] ?  [] : ($paginationNo != 'all' ? $source->paginate($paginationNo) : $source->paginate($source->count()));
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.customers'),
                'url' => 'customers',
                'service' => $service,
                'icon' => ' fas fa-user-tie',
            ];

            $data['searchData'] = [
                'name' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.name_ar'),
                ],
                'phone' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.phone') .trans('main.noCountryCode'),
                ],
                'email' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.email'),
                ],
                'address' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.address'),
                ], 
            ];
        }elseif ($model == 'orders') {
            // Begin Search
            if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                $source->where('created_at','>=', $input['from'].' 00:00:00')->where('date','<=',$input['to']. ' 23:59:59');
            }
            if(isset($input['id']) && !empty($input['id'])){
                $source->where('id',$input['code']);
            }

            if(isset($input['status']) && !empty($input['status'])){
                $source->where('order_status','LIKE','%'.$input['status'].'%');
            }

            $modelData = $source == [] ?  [] : $source->orderBy('created_at','DESC')->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.orders'),
                'url' => 'orders',
                'service' => $service,
                'icon' => 'mdi mdi-truck-delivery-outline',
            ];
            
            if (Schema::hasTable($service.'_order_status')) {
                $options = DB::table($service.'_order_status')->get();
            }else{
                $options = [
                    ['id'=>'جديد','name'=>'جديد'],
                    ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                    ['id'=>'جاهز','name'=>'جاهز'],
                    ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                    ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                    ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ];
            }

            $data['searchData'] = [
                'id' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.id'),
                ],
                'status' => [
                    'type' => 'select',
                    'class' => 'form-control',
                    'index' => '',
                    'options' => $options,
                    'label' => trans('main.status'),
                ],
                'from' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker1',
                    'label' => trans('main.dateFrom'),
                ],
                'to' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker2',
                    'label' => trans('main.dateTo'),
                ], 
            ];
        }elseif($model=='products'){

            if(isset($input['id']) && !empty($input['id'])){
                $source->where('id',$input['id']);
            }

            if(isset($input['name']) && !empty($input['name'])){
                $source->where('name','LIKE','%'.$input['name'].'%');
            }

            if(isset($input['price']) && !empty($input['price'])){
                if (strpos($input['price'], '||') !== false) {
                    $arr = explode('||', $input['price']);
                    $min = (int) $arr[0];
                    $max = (int) $arr[1];
                    $source->where('price','>=',$min)->where('price','<=',$max);
                }else{
                    $source->where('price',$input['price']);
                }
            }

            if(isset($input['status']) && !empty($input['status'])){
                $source->where('status',$input['status']);
            }

            $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.products'),
                'url' => 'products',
                'service' => $service,
                'icon' => ' fab fa-product-hunt',
            ];

            $options = [
                ['id'=>0,'name'=>trans('main.unAvail')],
                ['id'=>1,'name'=>trans('main.avail')]
            ];

            $data['searchData'] = [
                'id' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.id'),
                ],
                'name' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.name'),
                ],
                'price' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.price'),
                ],
                'status' => [
                    'type' => 'select',
                    'class' => 'form-control',
                    'index' => '',
                    'options' => $options,
                    'label' => trans('main.status'),
                ],
            ];
        }elseif($model == 'abandonedCarts'){
            // Begin Search

            if(isset($input['date']) && !empty($input['date'])){
                $source->whereBetween('created_at',[date('Y-m-d',strtotime($input['date'])).' 00:00:00' , date('Y-m-d',strtotime($input['date'])).' 23:59:59']);
            }

            if(isset($input['phone']) && !empty($input['phone'])){
                $source->where('customer_mobile','LIKE','%'.$input['phone'].'%');
            }

            if(isset($input['client']) && !empty($input['client'])){
                $source->where('customer_id',$input['client']);
            }

            if(isset($input['status']) && !empty($input['status'])){
                if($input['status'] == 1){
                    $source->where('reminders_count','>=',1);
                }elseif($input['status'] == 2){
                    $source->where('reminders_count',0);
                }
            }

            if(isset($input['duration']) && !empty($input['duration'])){
                if($input['duration'] == 1){
                    $source->whereBetween('created_at',[date('Y-m-d',strtotime('-1 day',strtotime('today'))).' 00:00:00' ,date('Y-m-d') .' 23:59:59']);
                }elseif($input['duration'] == 2){
                    $source->whereBetween('created_at',[date('Y-m-d',strtotime('-1 week',strtotime('today'))).' 00:00:00' ,date('Y-m-d') .' 23:59:59']);
                }elseif($input['duration'] == 3){
                    $source->whereBetween('created_at',[date('Y-m-d',strtotime('-1 month',strtotime('today'))).' 00:00:00' ,date('Y-m-d') .' 23:59:59']);
                }elseif($input['duration'] == 4){
                    $source->whereBetween('created_at',[date('Y-m-d',strtotime('-1 year',strtotime('today'))).' 00:00:00' ,date('Y-m-d') .' 23:59:59']);
                }
            }

            if(isset($input['price']) && !empty($input['price'])){
                $source->where('cart_total','LIKE','%'.$input['price'].'%');
            }

            
            $clients = [];
            $ids = [];
            $firstOrderObj = \DB::table('zid_orders')->first();
            if($firstOrderObj){
                $storeUrl = $firstOrderObj->store_url.'cart/view';
            }
            if(!empty($source)){
                foreach($source->orderBy('created_at','DESC')->get() as $oneItem){
                    $clients[] = [
                        'id' => $oneItem->customer_id,
                        'name' => $oneItem->customer_name,
                        'mobile' => $oneItem->customer_mobile,
                        'order_id' => $oneItem->cart_id,
                        'total' => $oneItem->cart_total_string,
                        'url' => $firstOrderObj ? $storeUrl : '',
                    ];
                    $ids[] = $oneItem->cart_id;
                }
            }

            $modelData = $source == [] ?  [] : $source->orderBy('created_at','DESC')->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            $data['mainData'] = [
                'title' => trans('main.abandonedCarts'),
                'url' => 'abandonedCarts',
                'service' => $service,
                'icon' => ' fas fa-user-tie',
            ];

            $data['searchData'] = [];
            $mainData['customers'] = $clients;
            $mainData['ids'] = $ids;
            $mainData['template'] = Template::where('name_en','abandonedCarts')->first();

            $mainData['schedulemsg'] = Variable::where('var_key','LIKE','SCHEDULEMSG_ZID_%')->first();
            $mainData['schedulemsg_data'] = ($mainData['schedulemsg'] != null ? explode('_', str_replace('SCHEDULEMSG_ZID','',$mainData['schedulemsg']->var_key)) : []);

            $checkAvailBot = UserAddon::checkUserAvailability(USER_ID,1);
            $checkAvailBotPlus = UserAddon::checkUserAvailability(USER_ID,10);
            $mainData['bots'] = $checkAvailBot ? Bot::dataList(1)['data'] : [];
            $mainData['botPlus'] = $checkAvailBotPlus ? BotPlus::dataList(1)['data'] : [];
            $mainData['checkAvailBotPlus'] = $checkAvailBotPlus != null ? 1 : 0;        
            $mainData['checkAvailBot'] = $checkAvailBot != null ? 1 : 0;

            $mainData['events'] = CartEvents::dataList(1)['data'];
        }

        $mainData['designElems'] = $data;
        $mainData['type'] = $model;
        $mainData['data'] = $formattedData;
        $mainData['dis'] = $this->checkPerm();

        if(!empty($formattedData)){
            $mainData['pagination'] = \Helper::GeneratePagination($modelData);
        }

        if($ajaxCheck && $data['mainData']['url'] == 'abandonedCarts'){
            $returnHTML = view('Tenancy.ExternalServices.Views.V5.cartsAjax')->with('data', (object) $mainData)->render();
            return response()->json( array('success' => true, 'html'=>$returnHTML) );
        }
        // dd($mainData);
        if($ajaxCheck){
            $returnHTML = view('Tenancy.ExternalServices.Views.V5.ajaxData')->with('data', (object) $mainData)->render();
            return response()->json( array('success' => true, 'html'=>$returnHTML) );
        }

        return view('Tenancy.ExternalServices.Views.V5.'.$model)->with('data', (object) $mainData);
    }

    public function formatData($data,$table,$extraData=null){
        $objs = [];
        foreach ($data as $key => $value) {
            $dataObj = new \stdClass();
            if($table == 'products'){
                $dataObj->id = $value->id;
                $name_ar = @unserialize($value->name)['ar'];
                $name_en = @unserialize($value->name)['en'];
                
                $price = $value->formatted_price;
                $images = @unserialize($value->images)[0]['image']['full_size'];
                $url = $value->html_url;
                $status = $value->is_published;
                $withTax = $value->is_taxable;
                $created_at = $value->created_at;
                $require_shipping = $value->requires_shipping;
                $categories = unserialize($value->categories);
                $categories_ar = [];
                $categories_en = [];
                foreach ($categories as $category) {
                    $categories_ar[]= isset($category['name']['ar']) && !empty($category['name']['ar']) ? $category['name']['ar'] : '' ;
                    $categories_en[]= isset($category['name']['en']) && !empty($category['name']['en']) ? $category['name']['en'] : '' ;
                }
                $dataObj->sku = $value->sku;
                $dataObj->quantity = $value->quantity;
                $dataObj->name_ar = $name_ar == null ? $name_en : $name_ar;
                $dataObj->name_en = $name_en == null ? $name_ar : $name_en;
                $dataObj->categories_ar = $categories_ar == [] ? $categories_en : $categories_ar;
                $dataObj->categories_en = $categories_en == [] ? $categories_ar : $categories_en;
                $dataObj->price = $price;
                $dataObj->images = $images;
                $dataObj->url = $url;
                $dataObj->status = $status;
                $dataObj->withTax = $withTax;
                $dataObj->require_shipping = $require_shipping;
                $dataObj->created_at = date('Y-m-d H:i:s',strtotime($created_at));
                $dataObj->updated_at = date('Y-m-d H:i:s',strtotime($value->updated_at));
            }elseif($table == 'customers'){
                $dataObj->id = $value->id;
                $dataObj->name = $value->name;
                $dataObj->phone = $value->mobile;
                $dataObj->email = $value->email;
                $dataObj->image = asset('images/not-available.jpg');
                $dataObj->city = isset($value->city) && !empty($value->city) ? unserialize($value->city)['name'] : '';
                $dataObj->country = isset($value->city) && !empty($value->city) ? unserialize($value->city)['country_name'] : '';
            }elseif($table == 'orders'){
                $status = unserialize($value->order_status);
                $dataObj->created_at = date('Y-m-d H:i:s', strtotime($value->created_at));
                $dataObj->id = $value->id;
                $dataObj->order_url = $value->order_url;
                $dataObj->total = $value->order_total_string;
                $dataObj->status = $status['name'];
                $dataObj->statusID = $status['code'];
                $dataObj->items = [['name'=>$value->store_name,'quantity'=>1]];
            }elseif($table == 'abandonedCarts'){
                $price = $value->cart_total;
                $customer = [
                    'name' => $value->customer_name,
                    'mobile' => $value->customer_mobile,
                    'email' => $value->customer_email,
                    'country' => '',
                ];

                $dataObj->id = $value->cart_id;
                $dataObj->reference_id = $value->id;
                $dataObj->store_id = $value->store_id;
                $dataObj->session_id = $value->session_id;
                $dataObj->order_id = $value->order_id;
                $dataObj->phase = $value->phase;
                $dataObj->customer = $customer;
                $dataObj->total = $value->cart_total_string;
                $dataObj->order_url = 'https://web.zid.sa/abandoned-cart/'.$value->store_id;
                $dataObj->reminders_count = $value->reminders_count;
                $dataObj->sent_count = $value->reminders_count;
                $dataObj->products_count = $value->products_count;
                $dataObj->items = trans('main.products_count') . $value->products_count;
                $dataObj->created_at = date('Y-m-d H:i:s',strtotime($value->created_at));
                $dataObj->updated_at = date('Y-m-d H:i:s',strtotime($value->updated_at));
            }
            $objs[] = $dataObj;
        }
        return $objs;
    }

    public function reports(Request $request){
        $service = $this->service;
       
        $data['designElems']['mainData'] = [
            'title' => trans('main.notReports'),
            'url' => 'services/'.$service.'/reports',
            'name' => 'reports',
            'service' => $service,
            'icon' => 'mdi mdi-file-account-outline',
        ];

        if (Schema::hasTable($service.'_order_status')) {
            $options = DB::table($service.'_order_status')->get();
        }else{
            $options = [
                ['id'=>'جديد','name'=>'جديد'],
                ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                ['id'=>'جاهز','name'=>'جاهز'],
                ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
            ];
        }

        $sendOptions =[
            ['id'=> 0 , 'name' => trans('main.notSent')],
            ['id'=> 1 , 'name' => trans('main.sentDone')],
        ];

        $extraTableData = [];
        $extraSearchData = [];
        
        $oldSearchData = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.id'),
                'index' => '0',
            ],
            'order_id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.order'),
            ],
            'statusText' => [
                'type' => 'select',
                'class' => 'form-control',
                'id' => '',
                'index' => '',
                'options' => $options,
                'label' => trans('main.status'),
            ],
            'from' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => trans('main.dateTo'),
            ],
        ];

        $oldTableData=  [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'order_id' => [
                'label' => trans('main.order'),
                'type' => '',
                'className' => '',
                'data-col' => 'order_id',
                'anchor-class' => '',
            ],  
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusText',
                'anchor-class' => '',
            ],   
            'created_at' => [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => 'created_at',
                'anchor-class' => '',
            ],   
        ];


        if($request->ajax()){
            $data = ModNotificationReport::dataList(2);
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['searchData'] = array_merge($oldSearchData,$extraSearchData); 
        $data['designElems']['tableData'] = array_merge($oldTableData,$extraTableData);
        return view('Tenancy.ExternalServices.Views.V5.reports')->with('data', (object) $data);
    }

    public function templates(Request $request){
        $service = $this->service;

        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->name = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['designElems']['mainData'] = [
            'title' => trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fas fa-envelope-open-text',
            'addOne' => trans('main.newTemplate'),
        ];

        $actives = [
            ['id'=>0,'name'=>trans('main.notActive')],
            ['id'=>1,'name'=>trans('main.active')],
        ];

        $options = [
            ['id'=>'جديد','name'=>'جديد'],
            ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
            ['id'=>'جاهز','name'=>'جاهز'],
            ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
            ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
            ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
            ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
        ];

        $searchData = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.id'),
                'index' => '0',
            ],
            'channel' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.channel'),
                'index' => '',
                'options' => $channels,
            ],
            'statusText' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.type'),
                'index' => '',
                'options' => $options,
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.status'),
                'index' => '',
                'options' => $actives,
            ],
            
        ];

        $tableData=  [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'channel' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => '',
                'data-col' => 'channel',
                'anchor-class' => 'badge badge-dark',
            ],  
            'content_'.LANGUAGE_PREF => [
                'label' => trans('main.content_'.LANGUAGE_PREF),
                'type' => '',
                'className' => 'text-center pre-space',
                'data-col' => 'content_'.LANGUAGE_PREF,
                'anchor-class' => 'pre-space',
            ],   
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusText',
                'anchor-class' => '',
            ],  
            'statusIDText' => [
                'label' => trans('main.type'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusIDText',
                'anchor-class' => '',
            ],  
            'actions' => [
                'label' => trans('main.actions'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],   
        ];

        if($request->ajax()){
            $data = ModTemplate::dataList(null,2);
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['searchData'] = $searchData; 
        $data['designElems']['tableData'] = $tableData;
        return view('Tenancy.ExternalServices.Views.V5.templates')->with('data', (object) $data);
    }

    public function templatesEdit($id) {
        $id = (int) $id;
        $service = $this->service;
        

        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('id',$id)->first();
        if($dataObj == null) {
            return Redirect('404');
        }

        $checkAvailBot = UserAddon::checkUserAvailability(USER_ID,1);
        $checkAvailBotPlus = UserAddon::checkUserAvailability(USER_ID,10);

        $userObj = User::getData(User::getOne(USER_ID));

        $data['designElems']['mainData'] = [
            'title' => trans('main.edit') . ' '.trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fa fa-pencil-alt',
        ];

        $data['data'] = ModTemplate::getData($dataObj);
        $data['mods'] = User::getModerators()['data'];
        $data['labels'] = Category::dataList()['data'];
        $data['statuses'] = [
            ['id'=>'جديد','name'=>'جديد'],
            ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
            ['id'=>'جاهز','name'=>'جاهز'],
            ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
            ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
            ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
            ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
        ];
        $data['bots'] = $checkAvailBot ? Bot::dataList(1)['data'] : [];
        $data['botPluss'] = $checkAvailBotPlus ? BotPlus::dataList(1)['data'] : [];
        $data['botPlus'] = $dataObj->type > 1 ? BotPlus::getData(BotPlus::find($dataObj->type)) : [];        
        $data['checkAvailBotPlus'] = $checkAvailBotPlus != null ? 1 : 0;
        $data['checkAvailBot'] = $checkAvailBot != null ? 1 : 0;
        return view('Tenancy.ExternalServices.Views.V5.edit')->with('data', (object) $data);      
    }

    protected function validateInsertObject($input){
        $rules = [
            'title' => 'required',
            'body' => 'required',
            'footer' => 'required',
            'buttons' => 'required',
        ];

        $message = [
            'title.required' => trans('main.titleValidate'),
            'body.required' => trans('main.bodyValidate'),
            'footer.required' => trans('main.footerValidate'),
            'buttons.required' => trans('main.buttonsValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function templatesUpdate($id) {
        $id = (int) $id;
        $service = $this->service;

        $input = \Request::all();
        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('id',$id)->first();
        if($dataObj == null) {
            return Redirect('404');
        }
        // dd($input);

        if(isset($input['type']) && !empty($input['type'])){
            if($input['type'] == 2){
                $validate = $this->validateInsertObject($input);
                if($validate->fails()){
                    Session::flash('error', $validate->messages()->first());
                    return redirect()->back();
                }

                $myData = [];
                for ($i = 0; $i < $input['buttons']; $i++) {
                    if(!isset($input['btn_text_'.($i+1)]) || empty($input['btn_text_'.($i+1)]) || $input['btn_text_'.($i+1)] == null ){
                        Session::flash('error', trans('main.invalidText',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    if(!isset($input['btn_reply_type_'.($i+1)]) || empty($input['btn_reply_type_'.($i+1)]) || $input['btn_reply_type_'.($i+1)] == null ){
                        Session::flash('error', trans('main.invalidType',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    $replyType = (int)$input['btn_reply_type_'.($i+1)];
                    if($replyType == 1 && ( !isset($input['btn_reply_'.($i+1)]) || empty($input['btn_reply_'.($i+1)]) )){
                        Session::flash('error', trans('main.invalidReply',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    if($replyType == 2 && ( !isset($input['btn_msg_'.($i+1)]) || empty($input['btn_msg_'.($i+1)]) )){
                        Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    if($replyType == 3 && ( !isset($input['btn_msgs_'.($i+1)]) || empty($input['btn_msgs_'.($i+1)]) )){
                        Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    $modelType = '';
                    if($replyType == 2 && ( !isset($input['btn_msg_type_'.($i+1)]) || empty($input['btn_msg_type_'.($i+1)]) )){
                        Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                        return redirect()->back()->withInput();
                    }

                    $modelType = (int)$input['btn_msg_type_'.($i+1)];

                    $modelName = $modelType != '' ?  ($modelType == 1 ? '\App\Models\Bot' : '\App\Models\BotPlus')  : '';
                    $msg = $replyType == 1 ? $input['btn_reply_'.($i+1)] : '';

                    if($modelName != '' && $msg == '' && $replyType != 3){
                        $itemObj = $modelName::find($input['btn_msg_'.($i+1)]);
                        if($itemObj){
                            $msg = $itemObj->id;
                        }
                    }

                    if($replyType == 3){
                        $msg = $input['btn_msgs_'.($i+1)];
                        $modelName = 'zid_order_status';
                    }

                    $myData[] = [
                        'id' => $i + 1,
                        'text' => $input['btn_text_'.($i+1)],
                        'reply_type' => $input['btn_reply_type_'.($i+1)],
                        'msg_type' => $modelType,
                        'model_name' => $modelName,
                        'msg' => $msg,
                    ];
                }

                // dd($myData);
                if($dataObj->type > 1){
                    $botObj = BotPlus::find($dataObj->type);
                }else{
                    $botObj = new BotPlus();
                }
                
                $botObj->message_type = 1;
                $botObj->channel = Session::get('channelCode');
                $botObj->message = 'Zid Template '.$id;
                $botObj->title = $input['title'];
                $botObj->body = $input['body'];
                $botObj->footer = $input['footer'];
                $botObj->buttons = $input['buttons'];
                $botObj->buttonsData = serialize($myData);
                $botObj->status = 1;
                $botObj->deleted_by = 1;
                $botObj->deleted_at = DATE_TIME;
                $botObj->save();

                $input['content_ar'] = $input['body'];
                $input['content_en'] = $input['body'];
                $input['type'] = $botObj->id;
            }

            $dataObj = ModTemplate::find($id);
            $dataObj->content_ar = $input['content_ar'];
            $dataObj->content_en = $input['content_en'];
            $dataObj->status = $input['status'];
            $dataObj->type = $input['type'];
            $dataObj->category_id = $input['category_id'];
            $dataObj->moderator_id = $input['moderator_id'];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
            Session::flash('success', trans('main.editSuccess'));
        }        
        return \Redirect::back()->withInput();
    }

    public function templatesAdd() {
        $service = $this->service;
        $data['designElems']['mainData'] = [
            'title' => trans('main.add') . ' '.trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fa fa-plus',
        ];
        $userObj = User::getData(User::getOne(USER_ID));
        $data['channel'] = $userObj->channels[0];
        $data['statuses'] = [
                ['id'=>'جديد','name'=>'جديد'],
                ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                ['id'=>'جاهز','name'=>'جاهز'],
                ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
        ];
        return view('Tenancy.ExternalServices.Views.V5.add')->with('data', (object) $data);      
    }

    public function templatesCreate() {
        $service = $this->service;
        $input = \Request::all();
        
        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('statusText',$input['statusText'])->where('status',1)->first();
        if($dataObj && $input['status'] == 1){
            Session::flash('error', trans('main.statusFound'));
            return \Redirect::back()->withInput();
        }

        $dataObj = new ModTemplate;
        $dataObj->channel = Session::get('channelCode');
        $dataObj->content_ar = $input['content_ar'];
        $dataObj->content_en = $input['content_en'];
        $dataObj->statusText = $input['statusText'];
        $dataObj->status = $input['status'];
        $dataObj->mod_id = 2;
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function templatesDelete($id) {
        $id = (int) $id;
        $dataObj = ModTemplate::getOne($id);
        return \Helper::globalDelete($dataObj);
    }

    public function uploadImage($type,Request $request){
        $rand = rand() . date("YmdhisA");

        if ($request->hasFile('file')) {
            $files = $request->file('file');

            $file_size = $files->getSize();
            $file_size = $file_size/(1024 * 1024);
            $file_size = number_format($file_size,2);
            $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
            $totalStorage = Session::get('storageSize');
            $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
            if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
            }

            
            $type = \ImagesHelper::checkFileExtension($files->getClientOriginalName());
            $fileSize = $files->getSize();
            if($fileSize >= 15000000){
                return \TraitsFunc::ErrorMessage(trans('main.file100kb'));
            }
            
            if(!in_array($type, ['file','photo']) ){
                return \TraitsFunc::ErrorMessage(trans('main.selectFile'));
            }

            Storage::put($rand,$files);
            Session::put('msgFile',$rand);
            Session::put('msgFileType',$type);
            return \TraitsFunc::SuccessResponse('');
        }
    }

    public function addImage($images,$nextID=false){
        $fileName = \ImagesHelper::UploadFile('ZidCarts', $images, $nextID);
        if($fileName == false){
            return false;
        }
        return $fileName;        
    }
}
