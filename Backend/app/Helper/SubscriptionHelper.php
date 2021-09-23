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

class SubscriptionHelper {

    public function newSubscription($cartObj,$type,$transaction_id,$paymentGateaway,$start_date,$invoiceObj=null,$transferObj=null,$arrType=null){
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
                $end_date =  $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
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
                    $addonData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $userObj->global_id,
                        'user_id' => $userObj->id,
                        'addon_id' => $one[0],
                        'status' => 1,
                        'duration_type' => $one[3],
                        'start_date' => $start_date,
                        'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date)), 
                    ];
                }else if($one[1] == 'extra_quota'){
                    $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                    for ($i = 0; $i < $one[7] ; $i++) {
                        $extraQuotaData[] = [
                            'tenant_id' => $tenant_id,
                            'global_user_id' => $userObj->global_id,
                            'user_id' => $userObj->id,
                            'extra_quota_id' => $one[0],
                            'duration_type' => $one[3],
                            'status' => 1,
                            'start_date' => $start_date,
                            'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date)), 
                        ];
                    }
                }
                $price = $dataObj->monthly_price;
                $price_after_vat = $dataObj->monthly_after_vat;
                if($one[3] == 2){
                    $price = $dataObj->annual_price;
                    $price_after_vat = $dataObj->annual_after_vat;
                }
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
            $invoiceObj->due_date = $start_date;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->items = serialize($items);
            $invoiceObj->status = 1;
            $invoiceObj->payment_method = 2;
            $invoiceObj->sort = Invoice::newSortIndex();
            $invoiceObj->created_at = DATE_TIME;
            $invoiceObj->created_by = $userObj->id;
            $invoiceObj->save();
        }elseif($type == 'payInvoice'){
            $invoiceObj->status = 1;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->transaction_id = $transaction_id;
            $invoiceObj->payment_gateaway = $paymentGateaway;  
            $invoiceObj->save();
        }elseif($type == 'transferRequest'){
            $invoiceObj = new Invoice;
            $invoiceObj->client_id = $userObj->id;
            $invoiceObj->transaction_id = $transaction_id;
            $invoiceObj->payment_gateaway = $paymentGateaway;  
            $invoiceObj->total = $total - $userCredits ;
            $invoiceObj->due_date = $start_date;
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
            $userAddonObj = UserAddon::where([
                ['user_id',$oneAddonData['user_id']],
                ['addon_id',$oneAddonData['addon_id']],
                ['status',2],
            ])->orWhere([
                ['user_id',$oneAddonData['user_id']],
                ['addon_id',$oneAddonData['addon_id']],
                ['end_date','<',date('Y-m-d')],
            ])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
                $disableUpdate = 1;
            }else{
                UserAddon::insert($oneAddonData);
                if(!$hasMembership){
                    $disableUpdate = 1;
                }
            }

        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where([
                ['user_id',$oneItemData['user_id']],
                ['extra_quota_id',$oneItemData['extra_quota_id']],
                ['status',2],
            ])->orWhere([
                ['user_id',$oneItemData['user_id']],
                ['extra_quota_id',$oneItemData['extra_quota_id']],
                ['end_date','<',date('Y-m-d')],
            ])->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
                $disableUpdate = 1;
            }else{
                UserExtraQuota::insert($oneItemData);
                if(!$hasMembership){
                    $disableUpdate = 1;
                }
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
            $extraChannelData['instanceToken'] = $generatedData[1];

            CentralChannel::create($extraChannelData);
            if($tenant){
                tenancy()->initialize($tenant);
            }
            UserChannels::create($channel);
            if($tenant){
                tenancy()->end($tenant);
            }
        }else{

            if($mainUserChannel->end_date > date('Y-m-d')){
                $disableTransfer = 1;
            }

            if(!$disableUpdate){
                if($tenant){
                    tenancy()->initialize($tenant);
                }
                $mainUserChannel->start_date = $start_date;
                $mainUserChannel->end_date = $end_date;
                $mainUserChannel->save();
                if($tenant){
                    tenancy()->end($tenant);
                }
                CentralChannel::where('id',$mainUserChannel->id)->update([
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]);
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

            $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
            $result = $updateResult->json();
        }
        
        if($tenant){
            tenancy()->initialize($tenant);
        }
        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);
        if($tenant){
            Variable::whereIn('var_key',['userCredits','start_date'])->delete();
            tenancy()->end($tenant);
        }

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if(!empty($addon) && in_array(4,$addon)){
            if($tenant){
                tenancy()->initialize($tenant);
            }
            $varObj = Variable::where('var_key','ZidURL')->first();
            if($tenant){
                tenancy()->end($tenant);
            }
            if(!$varObj){
                if($tenant){
                    tenancy()->initialize($tenant);
                }
                Variable::insert([
                    [
                        'var_key' => 'ZidURL',
                        'var_value' => 'https://api.zid.dev/app/v2',
                    ],
                ]);
                if($tenant){
                    tenancy()->end($tenant);
                }
            }
        }

        if(!empty($addon) && in_array(5,$addon)){
            if($tenant){
                tenancy()->initialize($tenant);
            }
            $varObj = Variable::where('var_key','SallaURL')->first();
            if($tenant){
                tenancy()->end($tenant);
            }
            if(!$varObj){
                if($tenant){
                    tenancy()->initialize($tenant);
                }
                Variable::insert([
                    [
                        'var_key' => 'SallaURL',
                        'var_value' => 'https://api.salla.dev/admin/v2',
                    ],
                ]);
                if($tenant){
                    tenancy()->end($tenant);
                }
            }
        }

        if($tenant && $transferObj){
            $transferObj->invoice_id = $invoiceObj->id;
            $transferObj->save();
        }

        return [1,''];
    }
}

