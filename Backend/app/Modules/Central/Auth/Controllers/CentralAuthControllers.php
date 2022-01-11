<?php namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\CentralVariable;
use App\Models\CentralWebActions;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserData;
use App\Models\MobileData;
use App\Models\ClientsRequests;
use App\Models\NotificationTemplate;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Validator;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \Spatie\WebhookServer\WebhookCall;
use App\Jobs\SyncHugeOld;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Handler\MyTokenGenerator;

class CentralAuthControllers extends Controller {

    use \TraitsFunc;
    
     public function syncData(){
        $syncData = [
            '966501000975','966597889373','966555100651','966582972674','966553270140','966570350011','966563909811','966505241489','966555609559','966508045510','966555992837','966502097362','966546325966','966541973828','966555465200','966592905003','966509732281','966558510483','966567944274','966547228770','966555579717','966500866200','966501838226','966507414222','966581713123','966596555008','966554666887','966554928877','966500074730','966550781810','966546775950','966565559900','966550581041','966591101607','966556671348','966561172636','966556686060','966564666673','966509793074','0581222019','966503233776','966504460624','966533458061','96894951228','966122255301','966559722200','966549492525','966544422577','966544288796','966501141150','966532683521','966595998884','966560383854','966534115111','966543434787','966560013232','966543434787','966562963194','966580913392','966554607294','966501141152','966567925217','966580045706','966560888499','966555522831','966568499560','966504463699','966536753582','966566695111','966548226089','966556494105','966564239993','966566577000','966506277646','966558448545','966566114147','966500438877','966503814831','966549999293','966596147619','201553108896','966503120111','966548183979','970597886679','966535455298','962792808526','966565577774','966536922022','966543994510','966545888855','13305646228','966504423403','966508694587','966555553656','966549494948','966555871138','966502146010','966568863652','966507988847','966537600200','966555804031','966538102020','966548655704','966536050601','966570116626','966562122212','966558131437','96899825492','966500947441','966546452530','966504662917','966557841489','966590116867','966501650423','966542777712','966500944513','966507977900','966503061646','966501119735','966540020724','966540066878','966500625386','97333972222','966502222494','966509485081','97333233633','966552581638','966548898033','966530632838','966552122345','966599944586','966540545423','966540784847','966591122229','966505866686','966114816063','966556001269','966552997099','966500412244','966568510582','966502414006','966505241303','966544577857','966556909069','966543333995','966556937540','966537770857','966566699657','966534170748','966549343624','966558242442','966556189599','966501219477','966536273816','966536759524','971507049234','966555730969','966554765656','966508740770','966549822298','966504585587','966504446617','966568472554','966560000934','966555264943','966550045457','966550909656','966125333337','966530123494','966531055880','966509208611','966541818185','966551507205','966549558858','966549425442','966593300133','966599062499','966546850651','966535108080','966592486276','966502414401','966566715816','966509461563','966126929522','966549963355','966544596160','966562396115','966500870519','966560807992','966559385000','966580552231','966505190280','966503724211','966507044838','966558458045','966555252415','966502616329','966122156110','966504434465','966548277655','966559084991','966555266516','966556800680','966548709773','966554969877','966114500821','966553020199','966500279598','966126108400','966509411829','966566006771','966559996095','966599118483','966541405420','966534249538','966543787870','966558336023','966531133979','966590355888','966590185604','966569929153','966580987069','966564113618','966509191040','966581752030','966501385699','966570676431','966590858850','966504866118','966504509093','966564990916','966563119119','966564779014','966552347332','966590070708','966593661064','966551046926','966563488666','966503223482','966548961888','966556351235','966505594790','966594152288','966505336536','966500648889','966553696947','966546683996','966502762140','966535222712','966539155551','966560056224','966567838310','966553810951','966560781768','966548727910','966506509728','966555023350','966560716214','966506029947','966550288808','966552820275','966598463373','966559601000','966500223677','966565088966','966555606553','966533790855','966555663242','966594326488','966164234445','966507611950','966554414494','966569148737','966501649536','966566670147','966543713714','966556617448','966595555764','966553692103','966507490903','966567470990','966532111277','966539090659','966504367476','966544444437','966501207711','966563159486','966599778824','966546864151','966543252229','966560020589','966566899829','966566180281','966571207777','966594496153','966598444403','966557070575','966581119696','966503209934','966561609756','966536951026','966508788021','966548039979','966549735534','966530002925','966597673915','966553211974','966503433063','966568071487','966537623352','966504113880','966553339691','966563394680','966555031419','966504440344','966551682092','966505254315','966558877294','966550180096','966500395533','966581068773','966581282427','966559559859','96896007203','966563622999','966501914028','966555447840','966581665077','966508992896','966555602485','966556771520','966591012184','966500087545','966508900919','966558020013','966554323372','966555304346','966500442257','966500878056','966568939992','966122610216','966502489223','966504446692','966503046617','966535707495','966558171056','966560707733','966554142160','966555949405','966564941413','966506646420','966550311377','966595147787','966501239936','966598344464','966539084366','966551008883','966599997579','966566700035','966551109899','966555909944','966540222140','966122255303','966509040552','966570549490','966557744447','966557591611','966581841004','971564777332','966561013129','966500459364','966138088863','966568333310','971521787524','966544696753','966500381000','966569118339','966533475051','966567522522','966535553479','966552205500','966550427396','966506148020','966591161126','966530976456','966563222197','966506059934','96560087760','966560616263','966505666097','966563613730','966558380299','966504649162','966559923433','966580272847','966595959590','966555459000','966570116646','966547589688','966581986620','966570116626','966112364000','966532330313','966552334335','966552299577','966506630764','966509761477','966551360094','966562227440','966500909381','966507464887','966552030131','0538982875','966580541244','966552335821','966112368888','966501182708','96566668998','966552931114','966567700065','966544806964','966551427430','96899808290','966561453338','966594423386','971544300330','966559900198','966532297986','966564033089','966565732050','966508433566','966538724733','966550579337','966505321409','966506300222','966565655741','966558705245','966562112929','966552952003','966502066526','966565046261','966559768888','966580294489','966542364967','966559736463','966920016626','966531288857','966581545151','966582400666','966556550094','96550228899','97466870704','966500359049','966559663167','96566992982','966536351555','966560080085','966565184326','966558455610','966562650822','966125334500','966539277844','971526074348','966570288247','96597445655','201008277336','966566333427','966505192211','966565538076','96550531333','966502957331','966556274033'

        ];

        foreach (array_unique($syncData) as $key => $value) {
            // dispatch(new SyncHugeOld($value,$key+1));        
        }
        dd('syncing......');
    }

    public function translate(){
        $input = \Request::all();
        $tr = new GoogleTranslate('en','ar',[],new MyTokenGenerator);
        $result = $tr->translate($input['company']);
        $result = str_replace(' ','-',$result);
        $statusObj['data'] = $result;
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

    public function appLogin() {
        $input =\Request::all();
        if(isset($input['type']) && !empty($input['type']) && $input['type'] == 'mob'){
            return redirect()->away('https://whatsloop.net/ar/Login.html');
        }
        
        if(isset($input['onesignal_push_id']) && !empty($input['onesignal_push_id']) ){
            $data['onesignal_push_id'] = $input['onesignal_push_id'];
            $url = 'https://whatsloop.net/ar/Login.html';
            \Helper::RedirectWithPostForm($data,$url);
        }
        
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        return redirect()->away('https://whatsloop.net/ar/Login.html');
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.login')->with('data',(object) $data);
    }
    
    public function login() {
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        
        if(Session::has('t_user_id')){
            $userId = Session::get('t_user_id');
            $tenantPhone = '';
            if(Session::has('t_phone')){
                $tenantPhone = Session::get('t_phone');
            }
            
            if($tenantPhone != ''){
                $rootId = Session::has('t_root') && Session::get('t_root') != $userId  ? 0 : 1;
                if(!$rootId){
                    $userObj = UserData::where('phone',$tenantPhone)->first();
                    if($userObj){
                        $domainObj = Domain::where('domain',$userObj->domain)->first();
                        $tenant = Tenant::find($domainObj->tenant_id);
                        tenancy()->initialize($tenant->id);
                        $dataObj = User::where('phone',$tenantPhone)->first();
                        tenancy()->end();
                        if(isset($dataObj)){
                            $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                            return redirect( tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                                'token' => $token
                            ]));
                        }
                    }
                }
                
                $userObj = CentralUser::find($userId);
                $domainObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('id',$userId)->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $token = tenancy()->impersonate($tenant,$userId,'/dashboard');
                    return redirect(tenant_route($dataObj->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]));
                }   
            }
        }
        
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.login')->with('data',(object) $data);
    }

    public function register() {
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }elseif(!Session::has('checked_user_phone')){
            return redirect('/checkAvailability');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.register')->with('data',(object) $data);
    }

    public function doLogin() {
        $input = \Request::all();
        $rules = array(
            'password' => 'required',
            'phone' => 'required',
        );

        $message = array(
            'password.required' => trans('auth.passwordValidation'),
            'phone.required' => trans('auth.phoneValidation'),
        );

        $validate = \Validator::make($input, $rules,$message);

        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if ($userObj == null) {
            $userObj = UserData::where('phone',$input['phone'])->first();
            if($userObj){
                $checkPassword = Hash::check($input['password'], $userObj->password);
                if ($checkPassword == null) {
                    $statusObj['data'] = \URL::to('/getResetPassword?type=old');
                    $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.invalidPassword'));
                    return \Response::json((object) $statusObj);
                }

                $domainObj = Domain::where('domain',$userObj->domain)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('phone',$input['phone'])->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                    Session::put('check_user_id',$dataObj->id);
                    Session::put('t_user_id',$dataObj->id);
                    Session::put('t_phone',$dataObj->phone);
                    Session::put('t_root',$dataObj->group_id == 1 ? 1 : 0);
                    $statusObj['data'] = tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]);
                    $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($dataObj->name));
                    return \Response::json((object) $statusObj);
                }
                return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
            }
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        $checkPassword = Hash::check($input['password'], $userObj->password);
        if ($checkPassword == null) {
            $statusObj['data'] = \URL::to('/getResetPassword?type=old');
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.invalidPassword'));
            return \Response::json((object) $statusObj);
        }

        if($userObj->group_id == 0){
            $userObj = CentralUser::getData($userObj);
            $domainObj = Domain::where('domain',$userObj->domain)->first();
            $tenant = Tenant::find($domainObj->tenant_id);
            $token = tenancy()->impersonate($tenant,$userObj->id,'/dashboard');
            Session::put('check_user_id',$userObj->id);
            Session::put('t_user_id',$userObj->id);
            Session::put('t_phone',$userObj->phone);
            Session::put('t_root',$userObj->group_id == 1 ? 1 : 0);
            $statusObj['data'] = tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                'token' => $token
            ]);
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($userObj->name));
            return \Response::json((object) $statusObj);
        }

        // Send Code Here
        $code = rand(1000,10000);
        $userObj->code = $code;
        $userObj->save();


        // $whatsLoopObj =  new \WhatsLoop();
        // $test = $whatsLoopObj->sendMessage('كود التحقق الخاص بك هو : '.$code,$input['phone']);

        // if(json_decode($test)->Code == 'OK'){
        //     \Session::put('check_user_id',$userObj->id);
        //     return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        // }else{
        //     return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        // }

        // $isAdmin = in_array($userObj->group_id, [1,]);
        // session(['group_id' => $userObj->group_id]);
        // session(['user_id' => $userObj->id]);
        // session(['email' => $userObj->email]);
        // session(['name' => $userObj->name]);
        // session(['is_admin' => $isAdmin]);
        // session(['group_name' => $userObj->Group->name_ar]);
        // $channels = CentralUser::getData($userObj)->channels;
        // session(['channel' => $channels[0]->id]);

        // Session::flash('success', trans('auth.passwordChanged'));
        // return redirect('/dashboard');

        if($userObj->two_auth == 1){
            $channelObj = \DB::connection('main')->table('channels')->first();
            $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
            $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
            $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
            if($notificationTemplateObj){
                $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
            }
            $data['phone'] = str_replace('+','',$input['phone']);
            $test = $whatsLoopObj->sendMessage($data);
            $result = $test->json();
            if($result['status']['status'] != 1){
                return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
            }

            \Session::put('check_user_id',$userObj->id);
            return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        }else{
            $isAdmin = in_array($userObj->group_id, [1,]);
            session(['group_id' => $userObj->group_id]);
            session(['user_id' => $userObj->id]);
            session(['email' => $userObj->email]);
            session(['name' => $userObj->name]);
            session(['is_admin' => $isAdmin]);
            session(['group_name' => $userObj->Group->name_ar]);
            $channels = CentralUser::getData($userObj)->channels;
            if(!empty($channels)){
                session(['channel' => $channels[0]->id]);
            }
            if($isAdmin){
                session(['central' => 1]);
            }

            Session::flash('success', trans('auth.welcome') . ucwords($userObj->name));
            $statusObj['data'] = \URL::to('/dashboard');
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($userObj->name));
            return \Response::json((object) $statusObj);
        }
        
    }

    public function checkByCode(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = CentralUser::getOne($user_id);
        if($code != $userObj->code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }
        $isAdmin = in_array($userObj->group_id, [1,]);
        session(['group_id' => $userObj->group_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => $userObj->Group->name_ar]);
        $channels = CentralUser::getData($userObj)->channels;
        if(!empty($channels)){
            session(['channel' => $channels[0]->id]);
        }

        Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
        return \TraitsFunc::SuccessResponse(trans('auth.welcome') . $userObj->name_ar);
    }

    public function logout() {
        $lang = Session::get('locale');
        session()->flush();
        $lang = Session::put('locale',$lang);
        Session::flash('success', trans('auth.seeYou'));
        return redirect()->to(route('login'));
    }

    public function getResetPassword(){
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.resetPassword')->with('data',(object) $data);
    }

    public function resetPassword(){
        $input = \Request::all();
        $rules = [
            'phone' => 'required',
        ];

        $message = [
            'phone.required' => trans('auth.phoneValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);

        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $phone = $input['phone'];
        $userObj = CentralUser::checkUserBy('phone',$phone);

        if ($userObj == null) {
            $userObj = UserData::where('phone',$input['phone'])->first();
            if($userObj){
                $domainObj = Domain::where('domain',$userObj->domain)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('phone',$input['phone'])->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $code = rand(1000,10000);
                    tenancy()->initialize($tenant->id);
                    $dataObj->code = $code;
                    $dataObj->save();
                    tenancy()->end();
                    $userObj->code = $code;
                    $userObj->save();
                    $channelObj = \DB::connection('main')->table('channels')->first();
                    $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
                    $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
                    $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
                    if($notificationTemplateObj){
                        $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
                    }
                    $data['phone'] = str_replace('+','',$input['phone']);
                    $test = $whatsLoopObj->sendMessage($data);
                    $result = $test->json();
                    if($result['status']['status'] != 1){
                        return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
                    }

                    Session::put('check_user_id',$dataObj->phone);
                    Session::put('t_user_id',$dataObj->id);
                    Session::put('t_phone',$dataObj->phone);
                    Session::put('t_root',$dataObj->group_id == 1 ? 1 : 0);
                    return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
                }
                return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
            }
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        // Send Code Here
        $code = rand(1000,10000);
        $userObj->code = $code;
        $userObj->save();

        if($userObj->group_id == 0){
            Session::put('t_user_id',$userObj->id);
            Session::put('t_phone',$userObj->phone);
            Session::put('t_root',1);
        }

        // $whatsLoopObj =  new \WhatsLoop();
        // $test = $whatsLoopObj->sendMessage('كود التحقق الخاص بك هو : '.$code,$input['phone']);

        // if(json_decode($test)->Code == 'OK'){
        //     Session::put('check_user_id',$userObj->id);
        //     return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        // }else{
        //     return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        // }

        $channelObj = \DB::connection('main')->table('channels')->first();
        $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
        $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
        if($notificationTemplateObj){
            $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
        }
        $data['phone'] = str_replace('+','',$input['phone']);
        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();
        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        }

        Session::put('check_user_id',$userObj->id);
        return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
    }

    public function checkResetPassword(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = UserData::where('phone',$user_id)->first();
        if($userObj){
            if($code != $userObj->code){
                return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
            }
        }else{
            $userObj = CentralUser::getOne($user_id);
            if($code != $userObj->code){
                return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
            }

        }

        Session::flash('success', trans('auth.validated'));
        return \TraitsFunc::SuccessResponse(trans('auth.validated'));
    }

    public function changePassword() {
        if(!Session::has('check_user_id')){
            return redirect('/getResetPassword');
        }
        return view('Central.Auth.Views.V5.changePassword');
    }

    public function completeReset() {
        $input = \Request::all();
        $rules = [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        $message = [
            'password.required' => trans('auth.passwordValidation'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
        }

        $password = $input['password'];
        $user_id = Session::get('check_user_id');

        if(Session::has('t_user_id')){
            $userId = Session::get('t_user_id');
            $tenantPhone = '';
            if(Session::has('t_phone')){
                $tenantPhone = Session::get('t_phone');
            }
            
            if($tenantPhone != ''){
                $rootId = Session::has('t_root') && Session::get('t_root') != $userId  ? 0 : 1;
                if(!$rootId){
                    $userObj = UserData::where('phone',$tenantPhone)->first();
                    if($userObj){
                        $userObj->password = Hash::make($password);
                        $userObj->save();

                        $domainObj = Domain::where('domain',$userObj->domain)->first();
                        $tenant = Tenant::find($domainObj->tenant_id);
                        tenancy()->initialize($tenant->id);
                        $dataObj = User::where('phone',$tenantPhone)->first();
                        $userObj->update(['password'=>Hash::make($password)]);
                        tenancy()->end();
                        if(isset($dataObj)){
                            $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                            return redirect( tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                                'token' => $token
                            ]));
                        }
                    }
                }
                
                $userObj = CentralUser::find($userId);
                if($userObj->group_id == 0){
                    $userObj->password = Hash::make($password);
                    $userObj->save();

                    $tenant = Tenant::where('phone',$userObj->phone)->first();
                    tenancy()->initialize($tenant->id);
                    User::where('id',$user_id)->update(['password'=>Hash::make($password)]);
                    tenancy()->end();
                    $token = tenancy()->impersonate($tenant,$user_id,'/dashboard');

                    return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]));
                }else{

                    $userObj->password = Hash::make($password);
                    $userObj->save();
                    Session::forget('check_user_id');

                    $isAdmin = in_array($userObj->group_id, [1,]);
                    session(['group_id' => $userObj->group_id]);
                    session(['user_id' => $userObj->id]);
                    session(['email' => $userObj->email]);
                    session(['name' => $userObj->name]);
                    session(['is_admin' => $isAdmin]);
                    session(['group_name' => $userObj->Group->name_ar]);
                    $channels = CentralUser::getData($userObj)->channels;
                    if(!empty($channels)){
                        session(['channel' => $channels[0]->id]);
                    }
                    if($isAdmin){
                        session(['central' => 1]);
                    }

                    Session::flash('success', trans('auth.passwordChanged'));
                    return redirect('/dashboard');
                } 
            }
        }
    }

    public function checkAvailability(){
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.checkAvailability')->with('data',(object) $data);
    }

    public function postCheckAvailability(Request $request){
        $input = \Request::all();
        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if($userObj){
            Session::flash('error', trans('main.phoneError'));
            return redirect()->back()->withInput();
        }
        
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        $dataArr['phone'] = $input['phone'];
        
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        if($clientRequestObj){
            $clientRequestObj->delete();
        }
        $channelObj = \DB::connection('main')->table('channels')->first();
        $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
        
        $code = rand(1000,10000);
        $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
        $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
        if($notificationTemplateObj){
            $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
        }
        $data['phone'] = str_replace('+','',$input['phone']);

        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();

        if($result['status']['status'] != 1){
            Session::flash('error', trans('auth.codeProblem'));
            return redirect()->back()->withInput();
        }

        $clientRequestObj = new ClientsRequests();
        $clientRequestObj->phone = $input['phone'];
        $clientRequestObj->code = $code;
        $clientRequestObj->ip_address = $request->ip();
        $clientRequestObj->created_at = DATE_TIME;
        $clientRequestObj->save();
        return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
    }

    public function checkAvailabilityCode(){
        $input = \Request::all();
        
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        $dataArr['phone'] = $input['phone'];
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        if(!$clientRequestObj){
            Session::flash('error', trans('main.userNotFound'));
            return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
        }

        if($clientRequestObj->code != $input['code']){
            Session::flash('error', trans('auth.codeError'));
            return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
        }

        Session::put('checked_user_phone',$input['phone']);
        return redirect()->to('/register');
    }

    protected function validateInsertObject($input){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'company' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required|email',
            'domain' => 'required|regex:/^([a-zA-Z0-9][a-zA-Z0-9-_])*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]$/',
        ];

        $message = [
            'name.required' => trans('main.nameValidate'),
            'comapny.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'password.required' => trans('main.passwordValidate'),
            'password.min' => trans('main.passwordValidate2'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
            'email.required' => trans('main.emailValidate'),
            'email.email' => trans('main.emailValidate'),
            'domain.required' => trans('main.domainValidate'),
            'domain.regex' => trans('main.domain2Validate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function postRegister(){
        $input = \Request::all();
        $input['phone'] = Session::get('checked_user_phone');

        $names = explode(' ',$input['name']);
        if(count($names) < 2){
            Session::flash('error', trans('main.name2Validate'));
            return redirect()->back()->withInput();
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
            
        $domainObj = Domain::getOneByDomain($input['domain']);
        if($domainObj){
            Session::flash('error', trans('main.domainValidate2'));
            return redirect()->back()->withInput();
        }

        $userObj = CentralUser::checkUserBy('email',$input['email']);
        if($userObj){
            Session::flash('error', trans('main.emailError'));
            return redirect()->back()->withInput();
        }

        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if($userObj){
            Session::flash('error', trans('main.phoneError'));
            return redirect()->back()->withInput();
        }


        $tenant = Tenant::create([
            'phone' => $input['phone'],
            'title' => $input['name'],
            'description' => '',
        ]);
        
        $tenant->domains()->create([
            'domain' => $input['domain'],
        ]);

        $baseUrl = 'https://whatsloop.net/api/v1/';

        // Get User Details
        $mainURL = $baseUrl.'user-details';
        $isOld = 0;

        $data = ['phone' => str_replace('+','',$input['phone']) /*'966570116626'*/];
        $result =  \Http::post($mainURL,$data);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] === true){
                // Begin Sync
                $isOld = 1;
            }
        }
        
        
        $centralUser = CentralUser::create([
            'global_id' => (string) Str::orderedUuid(),
            'name' => $input['name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'company' => $input['company'],
            'password' => Hash::make($input['password']),
            'notifications' => 0,
            'setting_pushed' => 0,
            'offers' => 0,
            'group_id' => 0,
            'is_active' => 1,
            'is_approved' => 1,
            'status' => 1,
            'two_auth' => 0,
            'is_old' => $isOld,
            'is_synced' => 0,
        ]);

        
        \DB::connection('main')->table('tenant_users')->insert([
            'tenant_id' => $tenant->id,
            'global_user_id' => $centralUser->global_id,
        ]);
        
        $user = $tenant->run(function() use(&$centralUser,$input){

            $userObj = User::create([
                'id' => $centralUser->id,
                'global_id' => $centralUser->global_id,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'],
                'company' => $input['company'],
                'group_id' => 1,
                'status' => 1,
                'domain' => $input['domain'],
                'is_old' => $centralUser->is_old,
                'is_synced' => $centralUser->is_synced,
                'two_auth' => 0,
                'sort' => 1,
                'setting_pushed' => 0,
                'password' => Hash::make($input['password']),
                'is_active' => 1,
                'is_approved' => 1,
                'notifications' => 0,
                'offers' => 0,
            ]);

            return $userObj;
        });

        Session::flash('success', trans('main.addSuccess'));
        $token = tenancy()->impersonate($tenant,$user->id,'/menu');
        if($isOld){
            $token = tenancy()->impersonate($tenant,$user->id,'/sync');
        }

        $notificationTemplateObj = NotificationTemplate::getOne(2,'newClient');
        $allData = [
            'name' => $input['name'],
            'subject' => $notificationTemplateObj->title_ar,
            'content' => $notificationTemplateObj->content_ar,
            'email' => $input['email'],
            'template' => 'tenant.emailUsers.default',
            'url' => 'https://'.$input['domain'].'.wloop.net/login',
            'extras' => [
                'company' => $input['company'],
                'url' => 'https://'.$input['domain'].'.wloop.net/login',
            ],
        ];
        \MailHelper::prepareEmail($allData);
        $salesData = $allData;
        $salesData['email'] = 'sales@whatsloop.net';
        \MailHelper::prepareEmail($salesData);


        $notificationTemplateObj = NotificationTemplate::getOne(1,'newClient');
        $allData['phone'] = $input['phone'];
        \MailHelper::prepareEmail($allData,1);

        
        Session::put('check_user_id',$user->id);
        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
            'token' => $token
        ]));
    }

    public function changeLang(Request $request){
        if($request->ajax()){
            if(!Session::has('locale')){
                Session::put('locale', $request->locale);
            }else{
                Session::forget('locale');
                Session::put('locale', $request->locale);
            }
            return Redirect::back();
        }
    }
}
