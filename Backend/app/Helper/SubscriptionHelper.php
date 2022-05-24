<?php
use App\Models\CentralVariable;
use App\Models\User;
use App\Models\CentralUser;
use App\Models\Variable;
use App\Models\Membership;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\Invoice;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\UserChannels;
use App\Models\CentralChannel;
use App\Models\Tenant;
use App\Models\ModTemplate;
use App\Models\Template;
use App\Models\OldMembership;
use App\Models\NotificationTemplate;

use App\Models\PaymentInfo;
use App\Models\BotPlus;


use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Barryvdh\Snappy\Facades\SnappyPdf;

class SubscriptionHelper {

    public function setInvoice($invoiceData,$userId,$tenant_id,$global_id,$type,$totalPrice=null){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = 0;
        $bundle = 0;
        $bundle = Variable::getVar('bundle');
        $main = 0;

        if($type == 'NewClient' || $type == 'SubscriptionChanged'){
            $start_date = date('Y-m-d');
        
            foreach($invoiceData as $key => $one){
                $end_date =  $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
                if($one[1] == 'membership'){
                    $dataObj = Membership::getOne($one[0]);
                    $main = 1;
                }else if($one[1] == 'addon'){
                    $dataObj = Addons::getOne($one[0]);
                    $addon[] = $one[0];
                    $addonData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $global_id,
                        'user_id' => $userId,
                        'addon_id' => $one[0],
                        'status' => 1,
                        'duration_type' => $one[3],
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];        
                }else if($one[1] == 'extra_quota'){
                    $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                    for ($i = 0; $i < $one[7] ; $i++) {
                        $extraQuotaData[] = [
                            'tenant_id' => $tenant_id,
                            'global_user_id' => $global_id,
                            'user_id' => $userId,
                            'extra_quota_id' => $one[0],
                            'duration_type' => $one[3],
                            'status' => 1,
                            'start_date' => $start_date,
                            'end_date' => $end_date, 
                        ];
                    }
                }
                $price = $one[6];
                $price_after_vat = $one[6];
               
                $item = [
                    'type' => $one[1],
                    'data' => [
                        'id' => $one[0],
                        'title_ar' => ($one[1] != 'extra_quota' ? $dataObj->title_ar : $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '') ),
                        'title_en' => ($one[1] != 'extra_quota' ? $dataObj->title_en : $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '') ),
                        'price' => $price,
                        'price_after_vat' => $price_after_vat,
                        'duration_type' => $one[3],
                        'quantity' => $one[7],
                    ],
                ];
                $total+= $price_after_vat * $one[7];
                $items[] = $item;
            }
        }

        $invoiceObj = Invoice::where('client_id',$userId)->where('status',0)->first();
        $centralUser = CentralUser::find($userId);
        $oldPrice = 0;
        if($centralUser->is_old){
            $oldPrice = $this->oldUser($centralUser,$type,0);
        }
        if(!$invoiceObj){
            $invoiceObj = new Invoice;
        }
        $invoiceObj->client_id = $userId;
        $invoiceObj->transaction_id = null;
        $invoiceObj->payment_gateaway = null;  
        $invoiceObj->total = $totalPrice > 0 ? number_format((float)$totalPrice, 2, '.', '') : ($oldPrice > 0 && $main ? number_format((float)$oldPrice, 2, '.', '') : number_format((float)$total, 2, '.', '')) ;
        $invoiceObj->due_date = $start_date;
        $invoiceObj->main = $main;
        $invoiceObj->paid_date = null;
        $invoiceObj->items = serialize($items);
        $invoiceObj->status = 0;
        $invoiceObj->payment_method = null;
        $invoiceObj->sort = Invoice::newSortIndex();
        $invoiceObj->created_at = DATE_TIME;
        $invoiceObj->created_by = $userId;
        $invoiceObj->save();
        return $invoiceObj;
    }

    public function initSubscription($data){
        $tenantData = $this->setTenant($data['transferObj'],$data['invoiceObj']->client_id);

        $userObj = $tenantData['userObj'];
        $centralUser = CentralUser::find($userObj->id);
        $oldMembershipID = $centralUser->membership_id;
        
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $tenant_id = $tenantObj->tenant_id;
        
        if($data['type'] == 'NewClient'){
            $this->newClient($data,$tenant_id,$centralUser->global_id,$centralUser->id,$data['invoiceObj']->id,$data['transaction_id'],$data['paymentGateaway']);
        }else if($data['type'] == 'SubscriptionChanged'){
            $this->changeSubscription($data,$tenant_id,$centralUser->global_id,$centralUser->id,$data['invoiceObj']->id,$data['transaction_id'],$data['paymentGateaway']);
        }else if($data['type'] == 'Suspended'){
            $this->reactivateAccount($data,$tenant_id,$centralUser->global_id,$centralUser->id,$data['invoiceObj']->id,$data['transaction_id'],$data['paymentGateaway']);
        }else if($data['type'] == 'PayInvoice'){
            $this->payInvoice($data,$tenant_id,$centralUser->global_id,$centralUser->id,$data['invoiceObj']->id,$data['transaction_id'],$data['paymentGateaway']);
        }else if($data['type'] == 'Upgraded'){
            $this->upgrade($data,$tenant_id,$centralUser->global_id,$centralUser->id,$data['invoiceObj']->id,$data['transaction_id'],$data['paymentGateaway']);
        }
        $this->sendInvoice($data['invoiceObj'],$tenant_id,$centralUser);
    }

    public function sendInvoice($invoiceObj,$tenant_id,$userObj){
        $data['invoice'] = Invoice::getData(Invoice::find($invoiceObj->id));
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];
        $tax = \Helper::calcTax($data['invoice']->roTtotal);
        tenancy()->initialize($tenant_id);
        $paymentObj = PaymentInfo::NotDeleted()->where('user_id',$invoiceObj->client_id)->first();
        if($paymentObj){
            $data['paymentObj'] = PaymentInfo::getData($paymentObj);
        }
        tenancy()->end();
        if(!defined('LANGUAGE_PREF')){
            define('LANGUAGE_PREF','ar');
        }
        if(!defined('DIRECTION')){
            define('DIRECTION','rtl');
        }
        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers),
            new TaxNumber($data['companyAddress']->tax_id),
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))),
            new InvoiceTotalAmount($data['invoice']->roTtotal),
            new InvoiceTaxAmount($tax)
        ])->render();

        $fileName = 'invoice '.($invoiceObj->id+10000).'.pdf';
        if(!file_exists(public_path().'/uploads/invoices/'.$invoiceObj->id.'/'.$fileName)){
            $pdf = SnappyPdf::loadView('Tenancy.Invoice.Views.V5.invoicePDF',['data'=> (object)$data])
                ->setPaper('a4', 'portrait')
                ->setOption('margin-bottom', '0mm')
                ->setOption('margin-top', '0mm')
                ->setOption('margin-right', '0mm')
                ->setOption('margin-left', '0mm');
            $pdf->save(public_path().'/uploads/invoices/'.$invoiceObj->id.'/'.$fileName);
        }
        
        $baseURL = config('app.BASE_URL').'/public';
        $baseURL.= '/uploads/invoices/'.$invoiceObj->id.'/'.$fileName;
        $sendData = [
            'phone' => str_replace('+', '', $userObj->phone),
            'body' => $baseURL,
            'filename' => $fileName,
        ];
        $centralChannelObj = CentralChannel::first();
        $mainWhatsLoopObj = new MainWhatsLoop($centralChannelObj->id,$centralChannelObj->token);
        $result = $mainWhatsLoopObj->sendFile($sendData);

        $sendButtonsData = [
            'chatId' => $sendData['phone'],
            'title' => 'Dear Customer',
            'body' => 'Body',
            'footer' => 'Your Opinion matters!!',
            'buttons' => 'Neutral,Satisfied,Unsatisfied',
        ];

        tenancy()->initialize($centralChannelObj->tenant_id);
        $botObj  = BotPlus::where('message','invoiceme')->first();
        tenancy()->end();

        $buttons = '';
        if($botObj){
            $botObj = BotPlus::getData($botObj);
            if(isset($botObj->buttonsData) && !empty($botObj->buttonsData)){
                foreach($botObj->buttonsData as $key => $oneItem){
                    $buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
                }
            }

            $sendButtonsData['title'] = $botObj->title;
            $sendButtonsData['body'] = $botObj->body;
            $sendButtonsData['footer'] = $botObj->footer;
            $sendButtonsData['buttons'] = $buttons;
        }

        $result2 = $mainWhatsLoopObj->sendButtons($sendButtonsData);
    }

    public function setTenant($transferObj=null,$userId){
        $tenant = null;
        $bundle = 0;
        $userCredits = 0;

        if($transferObj != null){
            $tenant = Tenant::find($transferObj->tenant_id);
        }else{
            $tenantUser = CentralUser::find($userId);
            $tenants = \DB::connection('main')->table('tenant_users')->where('global_user_id',$tenantUser->global_id)->first();
            $tenant = Tenant::find($tenants->tenant_id);
        }

        tenancy()->initialize($tenant);
        $userObj = User::first();
        $userCreditsObj = Variable::getVar('userCredits');
        $bundle = Variable::getVar('bundle');
        $start_date = Variable::getVar('start_date');
        if($userCreditsObj){
            $userCredits = $userCreditsObj;
        }
        tenancy()->end($tenant);

        return [
            'userObj' => $userObj,
            'bundle' => $bundle,
            'start_date' => $start_date,
            'userCredits' => $userCredits,
        ];
    }

    public function setTemplates($addon,$tenant,$instanceId){
        if(!empty($addon) && in_array(9,$addon)){
            tenancy()->initialize($tenant);
            Template::insert([
                [
                    'channel' => $instanceId,
                    'name_ar' => 'whatsAppOrders',
                    'name_en' => 'whatsAppOrders',
                    'description_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        طلبك رقم ( {ORDERID} ) جاهز الان للشراء 😎.

                                        اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

                                        ( {ORDERURL} )

                                        مع تحيات فريق عمل واتس لوب ❤️',
                    'description_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        طلبك رقم ( {ORDERID} ) جاهز الان للشراء 😎.

                                        اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

                                        ( {ORDERURL} )

                                        مع تحيات فريق عمل واتس لوب ❤️',
                    'status' => 1,
                ],
                [
                    'channel' => $instanceId,
                    'name_ar' => 'whatsAppInvoices',
                    'name_en' => 'whatsAppInvoices',
                    'description_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        تم تأكيد شراء طلبك رقم ( {ORDERID} )  😎.

                                        اذا ما عليك امر تتوجه الي طباعة فاتورة طلبك 😊 من خلال الرابط التالي :

                                        ( {INVOICEURL} )

                                        مع تحيات فريق عمل واتس لوب ❤️',
                    'description_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        تم تأكيد شراء طلبك رقم ( {ORDERID} )  😎.

                                        اذا ما عليك امر تتوجه الي طباعة فاتورة طلبك 😊 من خلال الرابط التالي :

                                        ( {INVOICEURL} )

                                        مع تحيات فريق عمل واتس لوب ❤️',
                    'status' => 1,
                ],

            ]);

            tenancy()->end($tenant);
        }

        if(!empty($addon) && in_array(5,$addon)){
            tenancy()->initialize($tenant);
            $modCount = ModTemplate::where('mod_id',1)->count();
            if($modCount == 0){
                ModTemplate::insert([
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'ترحيب بالعميل',
                        'content_ar' => 'يا اهلا بـ {CUSTOMERNAME} 😍
                                        
                                        اهلا وسهلا بك نورتنا وشرفتنا في متجرنا 🤩
                                        
                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يا اهلا بـ {CUSTOMERNAME} 😍
                                        
                                        اهلا وسهلا بك نورتنا وشرفتنا في متجرنا 🤩

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'بإنتظار الدفع',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'بإنتظار المراجعة',
                        'content_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        نشكرك على طلبك من متجر {STORENAME} 🤩 رقم طلبك هو ( {ORDERID} ) وحالته ( {ORDERSTATUS} ).

                                        ولاتشيل هم راح نراجع طلبك ونعتمده في أسرع وقت.

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        نشكرك على طلبك من متجر {STORENAME} 🤩 رقم طلبك هو ( {ORDERID} ) وحالته ( {ORDERSTATUS} ).

                                        ولاتشيل هم راح نراجع طلبك ونعتمده في أسرع وقت.

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'قيد التنفيذ',
                        'content_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        طلبك رقم  ( {ORDERID} ) نعمل على تجهيزه في اقرب وقت ممكن 😎 ( {ORDERSTATUS} ).

                                        اذا ما عليك امر تفيدنا بتقيمك للخدمه 😊 من خلال الرابط التالي :

                                        https://survey.whatsloop.net/q/1.html

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        طلبك رقم  ( {ORDERID} ) نعمل على تجهيزه في اقرب وقت ممكن 😎 ( {ORDERSTATUS} ).

                                        اذا ما عليك امر تفيدنا بتقيمك للخدمه 😊 من خلال الرابط التالي :

                                        https://survey.whatsloop.net/q/1.html

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'تم التنفيذ',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'جاري التوصيل',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'تم التوصيل',
                        'content_ar' => 'يااهلا بـ  {CUSTOMERNAME} 😍

                                        سعيدين بانه طلبك رقم  ( {ORDERID} ) صارت حالته ( {ORDERSTATUS} ) 🤩 

                                        نتمنى لك تجربة ممتعه ويسعدنا تقييمك لنا على الرابط التالي :
                                        https://survey.whatsloop.net/q/1.html

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يااهلا بـ  {CUSTOMERNAME} 😍

                                        سعيدين بانه طلبك رقم  ( {ORDERID} ) صارت حالته ( {ORDERSTATUS} ) 🤩 

                                        نتمنى لك تجربة ممتعه ويسعدنا تقييمك لنا على الرابط التالي :
                                        https://survey.whatsloop.net/q/1.html

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'تم الشحن',
                        'content_ar' => 'يا اهلا بـ  {CUSTOMERNAME} 😍

                                        طلبك رقم ( {ORDERID} ) طلع من عندنا الى شركة الشحن 🤩

                                         وصارت حالته ( {ORDERSTATUS} ). سيصلك قربيا باذن الله

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يا اهلا بـ  {CUSTOMERNAME} 😍

                                        طلبك رقم ( {ORDERID} ) طلع من عندنا الى شركة الشحن 🤩

                                         وصارت حالته ( {ORDERSTATUS} ). سيصلك قربيا باذن الله

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'ملغي',
                        'content_ar' => 'يااهلا بـ {CUSTOMERNAME} 😭 

                                        يؤسفنا ابلاغكم بانه تم الغاء طلبكم رقم ( {ORDERID} ) وصارت حالته ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يااهلا بـ {CUSTOMERNAME} 😭 

                                        يؤسفنا ابلاغكم بانه تم الغاء طلبكم رقم ( {ORDERID} ) وصارت حالته ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 1,
                        'status' => 1,
                        'statusText' => 'مسترجع',
                        'content_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        نفيدكم انه طلبكم رقم  ( {ORDERID} ) تم تغير حالته إلى ( {ORDERSTATUS} ).😥

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                        'content_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

                                        نفيدكم انه طلبكم رقم  ( {ORDERID} ) تم تغير حالته إلى ( {ORDERSTATUS} ).😥

                                        مع تحيات فريق عمل {STORENAME} ❤️',
                    ],

                ]);
            }
            tenancy()->end($tenant);
        }

        if(!empty($addon) && in_array(4,$addon)){
            tenancy()->initialize($tenant);
            $modCount = ModTemplate::where('mod_id',2)->count();
            if($modCount == 0){
                ModTemplate::insert([
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'جديد',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم انشاء طلبكم برقم ( {ORDERID} ) وحالته ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم انشاء طلبكم برقم ( {ORDERID} ) وحالته ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'جاري التجهيز',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'جاهز',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'جارى التوصيل',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'تم التوصيل',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        كما يسعدنا تقييمكم من خلال الرابط التالي :

                                        ضع رابط التقيم هنا

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ).

                                        كما يسعدنا تقييمكم من خلال الرابط التالي :

                                        ضع رابط التقيم هنا

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                    [
                        'channel' => $instanceId,
                        'mod_id' => 2,
                        'status' => 1,
                        'statusText' => 'تم الالغاء',
                        'content_ar' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ). 😞

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                        'content_en' => 'عميلنا العزيز، {CUSTOMERNAME}

                                        تم تغيير حالة طلبكم برقم ( {ORDERID} ) إلى ( {ORDERSTATUS} ). 😞

                                        مع تحيات فريق عمل {STORENAME}

                                        {ORDER_URL}',
                    ],
                ]);
            }
            tenancy()->end($tenant);
        }
    }

    public function sendNotifications($userObj,$invoiceObj,$type){
        $notificationTemplateObj = NotificationTemplate::getOne(2,'paymentSuccess');
        $allData = [
            'name' => $userObj->name,
            'subject' => $notificationTemplateObj->title_ar,
            'content' => $notificationTemplateObj->content_ar,
            'email' => $userObj->email,
            'template' => 'tenant.emailUsers.default',
            'url' => 'https://'.$userObj->domain.'.wloop.net/login',
            'extras' => [
                'invoiceObj' => Invoice::getData(Invoice::find($invoiceObj->id)),
                'company' => $userObj->company,
                'url' => 'https://'.$userObj->domain.'.wloop.net/login',
            ],
        ];
        \MailHelper::prepareEmail($allData);

        $salesData = $allData;
        $salesData['email'] = 'sales@whatsloop.net';
        \MailHelper::prepareEmail($salesData);

        $notificationTemplateObj = NotificationTemplate::getOne(1,'paymentSuccess');
        $phoneData = $allData;
        $phoneData['phone'] = $userObj->phone;
        \MailHelper::prepareEmail($phoneData,1);

        if($type == 'NewClient'){
            // Second Email
            $notificationTemplateObj = NotificationTemplate::getOne(2,'activateAccount');
            $allData = [
                'name' => $userObj->name,
                'subject' => $notificationTemplateObj->title_ar,
                'content' => $notificationTemplateObj->content_ar,
                'email' => $userObj->email,
                'template' => 'tenant.emailUsers.default',
                'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                'extras' => [
                    'invoiceObj' => Invoice::getData(Invoice::find($invoiceObj->id)),
                    'company' => $userObj->company,
                    'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                ],
            ];
            \MailHelper::prepareEmail($allData);

            $notificationTemplateObj = NotificationTemplate::getOne(1,'activateAccount');
            $phoneData = $allData;
            $phoneData['phone'] = $userObj->phone;
            \MailHelper::prepareEmail($phoneData,1);
        }

        if($type == 'Suspended'){
            $notificationTemplateObj = NotificationTemplate::getOne(2,'renewAccount');
            $allData = [
                'name' => $userObj->name,
                'subject' => $notificationTemplateObj->title_ar,
                'content' => $notificationTemplateObj->content_ar,
                'email' => $userObj->email,
                'template' => 'tenant.emailUsers.default',
                'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                'extras' => [
                    'invoiceObj' => Invoice::getData(Invoice::find($invoiceObj->id)),
                    'company' => $userObj->company,
                    'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                ],
            ];
            \MailHelper::prepareEmail($allData);

            $notificationTemplateObj = NotificationTemplate::getOne(1,'renewAccount');
            $phoneData = $allData;
            $phoneData['phone'] = $userObj->phone;
            \MailHelper::prepareEmail($phoneData,1);
        }

        if($type == 'Upgraded'){
            $notificationTemplateObj = NotificationTemplate::getOne(2,'upgradeSuccess');
            $allData = [
                'name' => $userObj->name,
                'subject' => $notificationTemplateObj->title_ar,
                'content' => $notificationTemplateObj->content_ar,
                'email' => $userObj->email,
                'template' => 'tenant.emailUsers.default',
                'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                'extras' => [
                    'invoiceObj' => Invoice::getData(Invoice::find($invoiceObj->id)),
                    'company' => $userObj->company,
                    'url' => 'https://'.$userObj->domain.'.wloop.net/login',
                ],
            ];
            \MailHelper::prepareEmail($allData);

            $notificationTemplateObj = NotificationTemplate::getOne(1,'upgradeSuccess');
            $phoneData = $allData;
            $phoneData['phone'] = $userObj->phone;
            \MailHelper::prepareEmail($phoneData,1);
        }
    }

    public function oldUser($userObj,$type,$invoiceStatus){
        $oldMembership = OldMembership::where('user_id',$userObj->id)->first();
        if(in_array($type,['SubscriptionChanged','Upgraded']) && $oldMembership){
            if($invoiceStatus != 0){
                OldMembership::where('user_id',$userObj->id)->delete();
            }
            return 0;
        }

        if(!in_array($type,['SubscriptionChanged','Upgraded'])  && $oldMembership){
            return OldMembership::getOldPrice($oldMembership->membership);
        }
    }

    public function newClient($data,$tenant_id,$global_id,$userId,$invoice_id,$transaction_id,$paymentGateaway){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = $data['invoiceObj']->total;
        $invoiceData = unserialize($data['invoiceObj']->items);
        $start_date = date('Y-m-d');
        $centralUser = CentralUser::find($userId);
        $membership_id = null;
        $duration_type = 1;
        
        foreach($invoiceData as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $membership_id = $dataObj->id;
                $duration_type = $one['data']['duration_type'];
            }else if($one['type'] == 'addon'){
                $dataObj = Addons::getOne($one['data']['id']);
                $addon[] = $one['data']['id'];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $global_id,
                    'user_id' => $userId,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => $start_date,
                    'end_date' => $end_date, 
                ];
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                for ($i = 0; $i < $one['data']['quantity'] ; $i++) {
                    $extraQuotaData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $global_id,
                        'user_id' => $userId,
                        'extra_quota_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                        'status' => 1,
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];
                }
            }

            $price = $dataObj->monthly_price ;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price ;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $items[] = $item;
        }

        $tenant = Tenant::find($tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        tenancy()->end($tenant);

        if(!empty($addon)){
            $oldData = unserialize($centralUser->addons) != null ? unserialize($centralUser->addons) : [];
            $newData = array_merge($oldData,$addon);
            $newData = array_unique($newData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::first();
            User::where('id',$centralUser->id)->update([
                'addons' =>  serialize($newData),
            ]);
            tenancy()->end($tenant);
            $centralUser->update([
                'addons' =>  serialize($newData),
            ]);
        }

        $invoiceObj = Invoice::find($invoice_id);
        $invoiceObj->main = 1;
        $invoiceObj->status = 1;
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->items = serialize($items);
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->payment_method = $paymentGateaway == 'Noon' ? 1 : 2;
        $invoiceObj->save();

        $this->sendNotifications($userObj,$invoiceObj,'NewClient');
        tenancy()->initialize($tenant);
        $mainUserChannel = UserChannels::first();
        tenancy()->end($tenant);
        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where('user_id',$oneAddonData['user_id'])->where('addon_id',$oneAddonData['addon_id'])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
            }else{
                UserAddon::insert($oneAddonData);
            }
        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where('user_id',$oneItemData['user_id'])->where('extra_quota_id',$oneItemData['extra_quota_id'])->where('status','!=',1)->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
            }else{
                UserExtraQuota::insert($oneItemData);                
            }
        }
 
        $channelObj = CentralChannel::first();
        $instanceId = '';
        if(!$mainUserChannel){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);

            $lastCentralChannelObj = CentralChannel::orderBy('id','DESC')->first();
            $lastTransferDaysData = [
                'receiver' => $lastCentralChannelObj->id,
                'days' => 1, // 3
                'source' => $channelObj->id,
            ];
            $transResult = $mainWhatsLoopObj->transferDays($lastTransferDaysData);

            $updateResult = $mainWhatsLoopObj->createChannel();
            $result = $updateResult->json();

        
            if($result['status']['status'] != 1){
                return [0,$result['status']['message']];
            }

            $channel = [
                'id' => $result['data']['channel']['id'],
                'token' => $result['data']['channel']['token'],
                'name' => 'Channel #'.$result['data']['channel']['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];

            $extraChannelData = $channel;
            $extraChannelData['tenant_id'] = $tenant_id;
            $extraChannelData['global_user_id'] = $userObj->global_id;
            $generatedData = CentralChannel::generateNewKey($result['data']['channel']['id']); // [ generated Key , generated Token]
            $extraChannelData['instanceId'] = $generatedData[0];
            $instanceId = $extraChannelData['instanceId'];
            $extraChannelData['instanceToken'] = $generatedData[1];

            CentralChannel::create($extraChannelData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::create($channel);
            tenancy()->end($tenant);
        }else{
            $centralChannelObj = CentralChannel::where('id',$mainUserChannel->id)->first();
            $instanceId = $centralChannelObj->instanceId;
             
            tenancy()->initialize($tenant);
            $mainUserChannel->start_date = $start_date;
            $mainUserChannel->end_date = $end_date;
            $mainUserChannel->save();
            tenancy()->end($tenant);
            
            $centralChannelObj->start_date = $start_date;
            $centralChannelObj->end_date = $end_date;
            $centralChannelObj->save();

            $channel = [
                'id' => $mainUserChannel->id,
                'token' => $mainUserChannel->token,
                'name' => 'Channel #'.$mainUserChannel->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];
        }
        

        $transferDaysData = [
            'receiver' => $channel['id'],
            'days' => 1, // 3
            'source' => $channelObj->id,
        ];

        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();
        
        tenancy()->initialize($tenant);
        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);
        if($membership_id != null){
            $userObj->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate','inv_status','bundle'])->delete();
        tenancy()->end($tenant);
        

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if($membership_id != null){
            $centralUser->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
       

        $this->setTemplates($addon,$tenant,$instanceId);
    }

    public function changeSubscription($data,$tenant_id,$global_id,$userId,$invoice_id,$transaction_id,$paymentGateaway){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = $data['invoiceObj']->total;
        $invoiceData = unserialize($data['invoiceObj']->items);
        $start_date = date('Y-m-d');
        $centralUser = CentralUser::find($userId);
        $channelEndDate = date('Y-m-d',strtotime('+1 month',strtotime($start_date)));
        $membership_id = null;
        $duration_type = 1;
        $main = 0;

        foreach($invoiceData as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $membership_id = $dataObj->id;
                $main = 1;
                $duration_type = $one['data']['duration_type'];
                $channelEndDate = $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            }else if($one['type'] == 'addon'){        
                $dataObj = Addons::getOne($one['data']['id']);
                $addon[] = $one['data']['id'];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $global_id,
                    'user_id' => $userId,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => $start_date,
                    'end_date' => $end_date, 
                ];
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                for ($i = 0; $i < $one['data']['quantity'] ; $i++) {
                    $extraQuotaData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $global_id,
                        'user_id' => $userId,
                        'extra_quota_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                        'status' => 1,
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];
                }
            }

            $price = $dataObj->monthly_price;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $items[] = $item;
        }

        $tenant = Tenant::find($tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        tenancy()->end($tenant);

        if(!empty($addon)){
            $oldData = unserialize($centralUser->addons) != null ? unserialize($centralUser->addons) : [];
            $newData = array_merge($oldData,$addon);
            $newData = array_unique($newData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::first();
            User::where('id',$centralUser->id)->update([
                'addons' =>  serialize($newData),
            ]);
            tenancy()->end($tenant);
            $centralUser->update([
                'addons' =>  serialize($newData),
            ]);
        }

        $invoiceObj = Invoice::find($invoice_id);
        $invoiceObj->main = $main;
        $invoiceObj->status = 1;
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->items = serialize($items);
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->payment_method = $paymentGateaway == 'Noon' ? 1 : 2;
        $invoiceObj->save();

        // Check If there is unpaid invoice then delete it 
        if($main){
            Invoice::where('client_id',$userId)->where('main',1)->where('status','!=',1)->delete();
        }

        $this->sendNotifications($userObj,$invoiceObj,'SubscriptionChanged');
        tenancy()->initialize($tenant);
        $mainUserChannel = UserChannels::first();
        tenancy()->end($tenant);

        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where('user_id',$oneAddonData['user_id'])->where('addon_id',$oneAddonData['addon_id'])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
            }else{
                UserAddon::insert($oneAddonData);
            }
        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where('user_id',$oneItemData['user_id'])->where('extra_quota_id',$oneItemData['extra_quota_id'])->where('status','!=',1)->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
            }else{
                UserExtraQuota::insert($oneItemData);                
            }
        }
    

        $channelObj = CentralChannel::first();
        $instanceId = '';
        if(!$mainUserChannel){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);

            $lastCentralChannelObj = CentralChannel::orderBy('id','DESC')->first();
            $lastTransferDaysData = [
                'receiver' => $lastCentralChannelObj->id,
                'days' => 1, // 3
                'source' => $channelObj->id,
            ];
            $transResult = $mainWhatsLoopObj->transferDays($lastTransferDaysData);

            $updateResult = $mainWhatsLoopObj->createChannel();
            $result = $updateResult->json();

        
            if($result['status']['status'] != 1){
                return [0,$result['status']['message']];
            }

            $channel = [
                'id' => $result['data']['channel']['id'],
                'token' => $result['data']['channel']['token'],
                'name' => 'Channel #'.$result['data']['channel']['id'],
                'start_date' => $start_date,
                'end_date' => $channelEndDate,
            ];

            $extraChannelData = $channel;
            $extraChannelData['tenant_id'] = $tenant_id;
            $extraChannelData['global_user_id'] = $userObj->global_id;
            $generatedData = CentralChannel::generateNewKey($result['data']['channel']['id']); // [ generated Key , generated Token]
            $extraChannelData['instanceId'] = $generatedData[0];
            $instanceId = $extraChannelData['instanceId'];
            $extraChannelData['instanceToken'] = $generatedData[1];

            CentralChannel::create($extraChannelData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::create($channel);
            tenancy()->end($tenant);
        }else{
            $centralChannelObj = CentralChannel::where('id',$mainUserChannel->id)->first();
            $instanceId = $centralChannelObj->instanceId;
             
            if($main){
                tenancy()->initialize($tenant);
                $mainUserChannel->start_date = $start_date;
                $mainUserChannel->end_date = $channelEndDate;
                $mainUserChannel->save();
                tenancy()->end($tenant);
                
                $centralChannelObj->start_date = $start_date;
                $centralChannelObj->end_date = $channelEndDate;
                $centralChannelObj->save();
            }

            $channel = [
                'id' => $mainUserChannel->id,
                'token' => $mainUserChannel->token,
                'name' => 'Channel #'.$mainUserChannel->id,
                'start_date' => $start_date,
                'end_date' => $channelEndDate,
            ];
        }
        

        $transferDaysData = [
            'receiver' => $channel['id'],
            'days' => 1, // 3
            'source' => $channelObj->id,
        ];
        OldMembership::where('user_id',$centralUser->id)->delete();
        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();
        
        tenancy()->initialize($tenant);
        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);
        if($membership_id != null){
            $userObj->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate','inv_status','bundle'])->delete();
        tenancy()->end($tenant);
        

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if($membership_id != null){
            $centralUser->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }

        $this->setTemplates($addon,$tenant,$instanceId);
    }

    public function reactivateAccount($data,$tenant_id,$global_id,$userId,$invoice_id,$transaction_id,$paymentGateaway){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = $data['invoiceObj']->total;
        $invoiceData = unserialize($data['invoiceObj']->items);
        $start_date = date('Y-m-d');
        $duration_type = 1;
        $channelEndDate = date('Y-m-d',strtotime('+1 month',strtotime($start_date)));
        $centralUser = CentralUser::find($userId);
        $membership_id = null;
        $main = 0;
        
        foreach($invoiceData as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $membership_id = $dataObj->id;
                $main = 1;
                $duration_type = $one['data']['duration_type'];
                $channelEndDate = $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            }else if($one['type'] == 'addon'){
                $dataObj = Addons::getOne($one['data']['id']);
                $addon[] = $one['data']['id'];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $global_id,
                    'user_id' => $userId,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => $start_date,
                    'end_date' => $end_date, 
                ];
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                for ($i = 0; $i < $one['data']['quantity'] ; $i++) {
                    $extraQuotaData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $global_id,
                        'user_id' => $userId,
                        'extra_quota_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                        'status' => 1,
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];
                }
            }

            $price = $dataObj->monthly_price;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $items[] = $item;
        }

        $tenant = Tenant::find($tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        tenancy()->end($tenant);

        if(!empty($addon)){
            $oldData = unserialize($centralUser->addons) != null ? unserialize($centralUser->addons) : [];
            $newData = array_merge($oldData,$addon);
            $newData = array_unique($newData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::first();
            User::where('id',$centralUser->id)->update([
                'addons' =>  serialize($newData),
            ]);
            tenancy()->end($tenant);
            $centralUser->update([
                'addons' =>  serialize($newData),
            ]);
        }

        $invoiceObj = Invoice::find($invoice_id);
        $invoiceObj->main = $main;
        $invoiceObj->status = 1;
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->items = serialize($items);
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->payment_method = $paymentGateaway == 'Noon' ? 1 : 2;
        $invoiceObj->save();

        $this->sendNotifications($userObj,$invoiceObj,'Suspended');
        tenancy()->initialize($tenant);
        $mainUserChannel = UserChannels::first();
        tenancy()->end($tenant);

        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where('user_id',$oneAddonData['user_id'])->where('addon_id',$oneAddonData['addon_id'])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
            }else{
                UserAddon::insert($oneAddonData);
            }
        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where('user_id',$oneItemData['user_id'])->where('extra_quota_id',$oneItemData['extra_quota_id'])->where('status','!=',1)->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
            }else{
                UserExtraQuota::insert($oneItemData);                
            }
        }
 
        $channelObj = CentralChannel::first();
        $instanceId = '';
        $centralChannelObj = CentralChannel::where('id',$mainUserChannel->id)->first();
        $instanceId = $centralChannelObj->instanceId;
         
        tenancy()->initialize($tenant);
        $mainUserChannel->start_date = $start_date;
        $mainUserChannel->end_date = $channelEndDate;
        $mainUserChannel->save();
        tenancy()->end($tenant);
        
        $centralChannelObj->start_date = $start_date;
        $centralChannelObj->end_date = $channelEndDate;
        $centralChannelObj->save();

        $channel = [
            'id' => $mainUserChannel->id,
            'token' => $mainUserChannel->token,
            'name' => 'Channel #'.$mainUserChannel->id,
            'start_date' => $start_date,
            'end_date' => $channelEndDate,
        ];        

        $transferDaysData = [
            'receiver' => $channel['id'],
            'days' => 1, // 3
            'source' => $channelObj->id,
        ];

        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();
        
        tenancy()->initialize($tenant);
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate','inv_status','bundle'])->delete();
        tenancy()->end($tenant);
        

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if($membership_id != null){
            $centralUser->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }

        $this->setTemplates($addon,$tenant,$instanceId);
    }

    public function payInvoice($data,$tenant_id,$global_id,$userId,$invoice_id,$transaction_id,$paymentGateaway){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = $data['invoiceObj']->total;
        $invoiceData = unserialize($data['invoiceObj']->items);
        $start_date = $data['start_date'];
        $centralUser = CentralUser::find($userId);
        $channelEndDate = date('Y-m-d',strtotime('+1 month',strtotime($start_date)));
        $membership_id = null;
        $duration_type = 1;
        $main = 0;
        $oldStartDate = $start_date > date('Y-m-d') ? 1 : 0;
        foreach($invoiceData as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $membership_id = $dataObj->id;
                $main = 1;
                $duration_type = $one['data']['duration_type'];
                $channelEndDate = $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            }else if($one['type'] == 'addon'){
                $dataObj = Addons::getOne($one['data']['id']);
                $addon[] = $one['data']['id'];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $global_id,
                    'user_id' => $userId,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => $start_date,
                    'end_date' => $end_date, 
                ];
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                for ($i = 0; $i < $one['data']['quantity'] ; $i++) {
                    $extraQuotaData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $global_id,
                        'user_id' => $userId,
                        'extra_quota_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                        'status' => 1,
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];
                }
            }

            $price = $dataObj->monthly_price;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $items[] = $item;
        }

        $tenant = Tenant::find($tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        tenancy()->end($tenant);

        if(!empty($addon)){
            $oldData = unserialize($centralUser->addons) != null ? unserialize($centralUser->addons) : [];
            $newData = array_merge($oldData,$addon);
            $newData = array_unique($newData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::first();
            User::where('id',$centralUser->id)->update([
                'addons' =>  serialize($newData),
            ]);
            tenancy()->end($tenant);
            $centralUser->update([
                'addons' =>  serialize($newData),
            ]);
        }

        $invoiceObj = Invoice::find($invoice_id);
        $invoiceObj->main = $main;
        $invoiceObj->status = 1;
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->items = serialize($items);
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->payment_method = $paymentGateaway == 'Noon' ? 1 : 2;
        $invoiceObj->save();

        // Check If there is unpaid invoice then delete it 
        if($main){
            Invoice::where('client_id',$userId)->where('main',1)->where('status','!=',1)->delete();
        }

        $this->sendNotifications($userObj,$invoiceObj,'SubscriptionChanged');
        tenancy()->initialize($tenant);
        $mainUserChannel = UserChannels::first();
        tenancy()->end($tenant);

        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where('user_id',$oneAddonData['user_id'])->where('addon_id',$oneAddonData['addon_id'])->first();
            if($userAddonObj){
                unset($oneAddonData['start_date']);
                $userAddonObj->update($oneAddonData);
            }else{
                UserAddon::insert($oneAddonData);
            }
        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where('user_id',$oneItemData['user_id'])->where('extra_quota_id',$oneItemData['extra_quota_id'])->where('status','!=',1)->first();
            if($userExtraQuotaObj){
                unset($oneItemData['start_date']);
                $userExtraQuotaObj->update($oneItemData);
            }else{
                UserExtraQuota::insert($oneItemData);                
            }
        }
    

        $channelObj = CentralChannel::first();
        $instanceId = '';
        if(!$mainUserChannel){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);

            $lastCentralChannelObj = CentralChannel::orderBy('id','DESC')->first();
            $lastTransferDaysData = [
                'receiver' => $lastCentralChannelObj->id,
                'days' => 1, // 3
                'source' => $channelObj->id,
            ];
            $transResult = $mainWhatsLoopObj->transferDays($lastTransferDaysData);
            
            $updateResult = $mainWhatsLoopObj->createChannel();
            $result = $updateResult->json();

        
            if($result['status']['status'] != 1){
                return [0,$result['status']['message']];
            }

            $channel = [
                'id' => $result['data']['channel']['id'],
                'token' => $result['data']['channel']['token'],
                'name' => 'Channel #'.$result['data']['channel']['id'],
                'start_date' => $oldStartDate == 1 ? date('Y-m-d') : $start_date,
                'end_date' => $channelEndDate,
            ];

            $extraChannelData = $channel;
            $extraChannelData['tenant_id'] = $tenant_id;
            $extraChannelData['global_user_id'] = $userObj->global_id;
            $generatedData = CentralChannel::generateNewKey($result['data']['channel']['id']); // [ generated Key , generated Token]
            $extraChannelData['instanceId'] = $generatedData[0];
            $instanceId = $extraChannelData['instanceId'];
            $extraChannelData['instanceToken'] = $generatedData[1];

            CentralChannel::create($extraChannelData);

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::create($channel);
            tenancy()->end($tenant);
        }else{
            $centralChannelObj = CentralChannel::where('id',$mainUserChannel->id)->first();
            $instanceId = $centralChannelObj->instanceId;
             
            if($main){
                tenancy()->initialize($tenant);
                // $mainUserChannel->start_date = $oldStartDate == 1 ? date('Y-m-d') : $start_date;
                $mainUserChannel->end_date = $channelEndDate;
                $mainUserChannel->save();
                tenancy()->end($tenant);
                
                // $centralChannelObj->start_date = $oldStartDate == 1 ? date('Y-m-d') : $start_date;
                $centralChannelObj->end_date = $channelEndDate;
                $centralChannelObj->save();
            }

            $channel = [
                'id' => $mainUserChannel->id,
                'token' => $mainUserChannel->token,
                'name' => 'Channel #'.$mainUserChannel->id,
                // 'start_date' => $$oldStartDate == 1 ? date('Y-m-d') : $start_date,
                'end_date' => $channelEndDate,
            ];
        }
        
        tenancy()->initialize($tenant);
        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);
        if($membership_id != null){
            $userObj->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate','inv_status','bundle'])->delete();
        tenancy()->end($tenant);
        

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if($membership_id != null){
            $centralUser->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
    }

    public function upgrade($data,$tenant_id,$global_id,$userId,$invoice_id,$transaction_id,$paymentGateaway){
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = $data['invoiceObj']->total;
        $invoiceData = unserialize($data['invoiceObj']->items);
        $start_date = $data['start_date'];
        $centralUser = CentralUser::find($userId);
        $membership_id = null;
        $duration_type = 1;
        $main = 0;
        foreach($invoiceData as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $membership_id = $dataObj->id;
                $main = 1;
                $duration_type = $one['data']['duration_type'];
                $channelEndDate = $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date)));
            }

            $price = $dataObj->monthly_price;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $items[] = $item;
        }

        $tenant = Tenant::find($tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        tenancy()->end($tenant);

        $invoiceObj = Invoice::find($invoice_id);
        $invoiceObj->main = $main;
        $invoiceObj->status = 1;
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->items = serialize($items);
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->payment_method = $paymentGateaway == 'Noon' ? 1 : 2;
        $invoiceObj->save();

        // Check If there is unpaid invoice then delete it 
        if($main){
            Invoice::where('client_id',$userId)->where('main',1)->where('status','!=',1)->delete();
        }
        
        $this->sendNotifications($userObj,$invoiceObj,'Upgraded');
        tenancy()->initialize($tenant);
        $mainUserChannel = UserChannels::first();
        tenancy()->end($tenant);
        
        $channelObj = CentralChannel::first();
 
        tenancy()->initialize($tenant);
        if($membership_id != null){
            $userObj->update([
                'membership_id' => $membership_id,
            ]);
        }
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate','inv_status','bundle'])->delete();
        tenancy()->end($tenant);
        OldMembership::where('user_id',$centralUser->id)->delete();
        
        if($membership_id != null){
            $centralUser->update([
                'membership_id' => $membership_id,
                'duration_type' => $duration_type,
            ]);
        }
    }

}

