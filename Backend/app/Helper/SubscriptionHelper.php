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

class SubscriptionHelper {

    public function newSubscription($cartObj,$type,$transaction_id,$paymentGateaway,$start_date=null,$invoiceObj=null,$transferObj=null,$arrType=null,$myEndDate=null){
        $tenant = null;
        if($transferObj){
            $tenant = Tenant::find($transferObj->tenant_id);
        }

        if($tenant){
            tenancy()->initialize($tenant);
        }
        $userObj = User::first();

        if($tenant){
            tenancy()->end($tenant);
        }
        
        $centralUser = CentralUser::find($userObj->id);
        
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $tenant_id = $tenantObj->tenant_id;
        
        if($tenant){
            tenancy()->initialize($tenant);
        }

        $userCreditsObj = Variable::getVar('userCredits');
        
        if($tenant){
            tenancy()->end($tenant);
        }

        $userCredits = 0;
        if($userCreditsObj){
            if($tenant){
                tenancy()->initialize($tenant);
            }
            $start_date = Variable::getVar('start_date');
            if($tenant){
                tenancy()->end($tenant);
            }
            $userCredits = $userCreditsObj;
        } 
        // dd($cartObj);

        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = 0;

        $hasMembership = 0;
        if($arrType == 'old'){
            foreach($cartObj as $key => $one){
                $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
                if($one['type'] == 'membership'){
                    $disableUpdate = 0;
                    $hasMembership = 1;
                    $dataObj = Membership::getOne($one['data']['id']);
                    $userObj->update([
                        'membership_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                    ]);

                    $centralUser->update([
                        'membership_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                    ]);
                }else if($one['type'] == 'addon'){
                    $dataObj = Addons::getOne($one['data']['id']);
                    $addon[] = $one['data']['id'];
                    $addonData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $userObj->global_id,
                        'user_id' => $userObj->id,
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
                            'global_user_id' => $userObj->global_id,
                            'user_id' => $userObj->id,
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
                $total+= $price_after_vat * $one['data']['quantity'];
                $items[] = $item;
            }
        }else{
            foreach($cartObj as $key => $one){
                $end_date =  $myEndDate != null ? $myEndDate : ($one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date)));
                if($one[1] == 'membership'){
                    $disableUpdate = 0;
                    $hasMembership = 1;
                    $dataObj = Membership::getOne($one[0]);
                    $userObj->update([
                        'membership_id' => $one[0],
                        'duration_type' => $one[3],
                    ]);

                    $centralUser->update([
                        'membership_id' => $one[0],
                        'duration_type' => $one[3],
                    ]);
                }else if($one[1] == 'addon'){
                    $dataObj = Addons::getOne($one[0]);
                    $addon[] = $one[0];
                    if($myEndDate != null){
                        $item = [
                            'tenant_id' => $tenant_id,
                            'global_user_id' => $userObj->global_id,
                            'user_id' => $userObj->id,
                            'addon_id' => $one[0],
                            'status' => 1,
                            'duration_type' => $one[3],
                            'end_date' => $myEndDate, 
                        ];
                        if($type == 'new' || $start_date != null){
                            $item = array_merge($item,['start_date'=>$start_date]);
                        }
                        $addonData[] = $item;
                    }else{
                        $addonData[] = [
                            'tenant_id' => $tenant_id,
                            'global_user_id' => $userObj->global_id,
                            'user_id' => $userObj->id,
                            'addon_id' => $one[0],
                            'status' => 1,
                            'duration_type' => $one[3],
                            'start_date' => $start_date,
                            'end_date' => $end_date, 
                        ];
                    }
                    
                }else if($one[1] == 'extra_quota'){
                    $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                    for ($i = 0; $i < $one[7] ; $i++) {
                        if($myEndDate != null){
                            $item = [
                                'tenant_id' => $tenant_id,
                                'global_user_id' => $userObj->global_id,
                                'user_id' => $userObj->id,
                                'extra_quota_id' => $one[0],
                                'duration_type' => $one[3],
                                'status' => 1,
                                'end_date' => $myEndDate, 
                            ];
                            if($type == 'new' || $start_date != null){
                                $item = array_merge($item,['start_date'=>$start_date]);
                            }
                            $extraQuotaData[] = $item;
                        }else{
                            $extraQuotaData[] = [
                                'tenant_id' => $tenant_id,
                                'global_user_id' => $userObj->global_id,
                                'user_id' => $userObj->id,
                                'extra_quota_id' => $one[0],
                                'duration_type' => $one[3],
                                'status' => 1,
                                'start_date' => $start_date,
                                'end_date' => $end_date, 
                            ];
                        }
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

        if(!empty($addon)){
            $oldData = unserialize($userObj->addons) != null ? unserialize($userObj->addons) : [];
            $newData = array_merge($oldData,$addon);
            $newData = array_unique($newData);

            if($tenant){
                tenancy()->initialize($tenant);
            }
            $userObj->update([
                'addons' =>  serialize($newData),
            ]);
            if($tenant){
                tenancy()->end($tenant);
            }

            $centralUser->update([
                'addons' =>  serialize($newData),
            ]);
        }

        if($type == 'new'){
            $invoiceObj = new Invoice;
            $invoiceObj->client_id = $userObj->id;
            $invoiceObj->transaction_id = $transaction_id;
            $invoiceObj->payment_gateaway = $paymentGateaway;  
            $invoiceObj->total = $total - $userCredits ;
            $invoiceObj->due_date = $myEndDate != null ?  date('Y-m-d') : $start_date;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->items = serialize($items);
            $invoiceObj->status = 1;
            $invoiceObj->payment_method = 2;
            $invoiceObj->sort = Invoice::newSortIndex();
            $invoiceObj->created_at = DATE_TIME;
            $invoiceObj->created_by = $userObj->id;
            $invoiceObj->save();

            $emailData = [
                'name' => $userObj->name,
                'subject' => 'حساب جديد',
                'content' => 'تم التسجيل في واتس لووب بنجاح وهذا رابط النطاق الخاص بك:' . 'https://'.$userObj->domain.'.wloop.net',
                'email' => $userObj->email,
            ];
            \MailHelper::prepareEmail($emailData);
        }elseif($type == 'payInvoice'){
            $invoiceObj->status = 1;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->transaction_id = $transaction_id;
            $invoiceObj->payment_gateaway = $paymentGateaway;  
            $invoiceObj->save();

            $emailData = [
                'name' => $userObj->name,
                'subject' => 'دفع فاتورة رقم #'.$invoiceObj->id,
                'content' => 'تم دفع فاتورة رقم #:' . $invoiceObj->id . ' والاجمالي هو : '. $invoiceObj->total,
                'email' => $userObj->email,
            ];
            \MailHelper::prepareEmail($emailData);
        }elseif($type == 'transferRequest'){
            $invoiceObj = new Invoice;
            $invoiceObj->client_id = $userObj->id;
            $invoiceObj->transaction_id = $transaction_id;
            $invoiceObj->payment_gateaway = $paymentGateaway;  
            $invoiceObj->total = $total - $userCredits ;
            $invoiceObj->due_date = $myEndDate != null ?  date('Y-m-d') : $start_date;;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->items = serialize($items);
            $invoiceObj->status = 1;
            $invoiceObj->payment_method = 2;
            $invoiceObj->sort = Invoice::newSortIndex();
            $invoiceObj->created_at = DATE_TIME;
            $invoiceObj->created_by = $userObj->id;
            $invoiceObj->save();
        }

        $disableTransfer = 0;

        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where('user_id',$oneAddonData['user_id'])->where('addon_id',$oneAddonData['addon_id'])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
            }else{
                UserAddon::insert($oneAddonData);
            }
            if(!$hasMembership){
                $disableUpdate = 1;
            }

        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where('user_id',$oneItemData['user_id'])->where('extra_quota_id',$oneItemData['extra_quota_id'])->where('status','!=',1)->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
            }else{
                UserExtraQuota::insert($oneItemData);                
            }
            if(!$hasMembership){
                $disableUpdate = 1;
            }
        }


 
        if($tenant){
            tenancy()->initialize($tenant);
        }
        $mainUserChannel = UserChannels::first();
        if($tenant){
            tenancy()->end($tenant);
        }
        $channelObj = CentralChannel::first();
        $instanceId = '';
        if(!$mainUserChannel){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
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
            if($tenant){
                tenancy()->initialize($tenant);
            }
            $mainUserChannel = UserChannels::create($channel);
            if($tenant){
                tenancy()->end($tenant);
            }
        }else{

            if($mainUserChannel->end_date > date('Y-m-d')){
                $disableTransfer = 1;
            }

            $centralChannelObj = CentralChannel::where('id',$mainUserChannel->id)->first();
            $instanceId = $centralChannelObj->instanceId;
            if(!$disableUpdate){
                if($tenant){
                    tenancy()->initialize($tenant);
                }
                if($myEndDate != null){
                    $mainUserChannel->end_date = $myEndDate;
                }else{
                    $mainUserChannel->start_date = $start_date;
                    $mainUserChannel->end_date = $end_date;
                }
                $mainUserChannel->save();
                if($tenant){
                    tenancy()->end($tenant);
                }

                if($myEndDate != null){
                    $centralChannelObj->end_date = $myEndDate;
                    $disableTransfer = 1;
                }else{
                    $centralChannelObj->start_date = $start_date;
                    $centralChannelObj->end_date = $end_date;
                }
                $centralChannelObj->save();
            }

            $channel = [
                'id' => $mainUserChannel->id,
                'token' => $mainUserChannel->token,
                'name' => 'Channel #'.$mainUserChannel->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];
        }
        

        if(!$disableTransfer){
            $transferDaysData = [
                'receiver' => $channel['id'],
                'days' => 1, // 3
                'source' => $channelObj->id,
            ];

            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
            $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
            $result = $updateResult->json();
        }
        
        if($tenant){
            tenancy()->initialize($tenant);
        }
        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);
        Variable::whereIn('var_key',['userCredits','start_date','cartObj','endDate'])->delete();
        if($tenant){
            tenancy()->end($tenant);
        }

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if(!empty($addon) && in_array(9,$addon)){
            if($tenant){
                tenancy()->initialize($tenant);
            }
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

            if($tenant){
                tenancy()->end($tenant);
            }
        }

        if(!empty($addon) && in_array(5,$addon)){
            if($tenant){
                tenancy()->initialize($tenant);
            }
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

            if($tenant){
                tenancy()->end($tenant);
            }
        }

        if(!empty($addon) && in_array(4,$addon)){
            if($tenant){
                tenancy()->initialize($tenant);
            }
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
            if($tenant){
                tenancy()->end($tenant);
            }
            
        }

        if($tenant && $transferObj){
            $transferObj->invoice_id = $invoiceObj->id;
            $transferObj->save();
        }

        return [1,''];
    }
}

