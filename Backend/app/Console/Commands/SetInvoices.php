<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\CentralUser;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\ExtraQuota;
use App\Models\CentralChannel;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;

class SetInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set User Invoices Every Day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   

        $channels = CentralChannel::dataList()['data'];
        $invoices = [];
        foreach ($channels as $value) {
            if($value->leftDays <= 7){
                $userObj = CentralUser::where('global_id',$value->global_user_id)->first();
                if($userObj->membership_id != null){
                    $membershipObj = $userObj->Membership; 
                    $invoiceItems['id'] = $membershipObj->id;
                    $invoiceItems['title_ar'] = $membershipObj->title_ar;
                    $invoiceItems['title_en'] = $membershipObj->title_en;
                    if(in_array($userObj->duration_type,[1,2])){
                        if($userObj->duration_type == 1){
                            $invoiceItems['price'] = $membershipObj->monthly_price;
                            $total = $membershipObj->monthly_after_vat;
                            $invoiceItems['price_after_vat'] = $total;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }else if($userObj->duration_type == 2){
                            $invoiceItems['price'] = $membershipObj->annual_price;
                            $total = $membershipObj->annual_after_vat;
                            $invoiceItems['price_after_vat'] = $total;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }
                        $invoiceItems['duration_type'] = $userObj->duration_type;
                        $invoiceItems['quantity'] = 1;
                        $invoices[$userObj->id][date('Y-m-d',$dueDate)] = [
                            'data' => [
                                'total' => $total,
                                'leftDays' => $value->leftDays,
                                'main' => 1,
                                'items' => [[
                                    'type' => 'membership',
                                    'data' => $invoiceItems,
                                ]],
                            ]
                        ];
                    }
                }
            }
        }

        if(!empty($invoices)){
            // Check New Invoices For Addons
            $userAddons = UserAddon::NotDeleted()->groupBy(['user_id','end_date'])->get();
            foreach ($userAddons as $addon) {
                $userAddon = UserAddon::dataList(null,$addon->user_id,$addon->end_date,[1,3])['data'];
                
                $userObj = CentralUser::find($addon->user_id);

                foreach ($userAddon as $value) {
                    if($value->leftDays <= 7){

                        $membershipObj = $value->Addon; 
                        $oneObj = [];
                        $oneObj['id'] = $membershipObj->id;
                        $oneObj['title_ar'] = $membershipObj->title_ar;
                        $oneObj['title_en'] = $membershipObj->title_en;
                        if($value->duration_type == 1){
                            $oneObj['price'] = $membershipObj->monthly_price;
                            $total = $membershipObj->monthly_after_vat;
                            $oneObj['price_after_vat'] = $membershipObj->monthly_after_vat;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }else if($value->duration_type == 2){
                            $oneObj['price'] = $membershipObj->annual_price;
                            $total = $membershipObj->annual_after_vat;
                            $oneObj['price_after_vat'] = $membershipObj->annual_after_vat;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }
                        $oneObj['duration_type'] = $value->duration_type;
                        $oneObj['quantity'] = 1;

                        if(isset($invoices[$userObj->id])){
                            if(isset($invoices[$userObj->id][date('Y-m-d',$dueDate)])){
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['total'] =  $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['total'] + $total;
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['items'][] = [
                                    'type' => 'addon',
                                    'data' => $oneObj,
                                ]; 
                            }else{
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data'] = [
                                    'total' => $total,
                                    'leftDays' => $value->leftDays,
                                    'main' => 0,
                                    'items' => [[
                                        'type' => 'addon',
                                        'data' => $oneObj,
                                    ]],
                                ];
                            }
                        }else{
                            $invoices[$userObj->id][date('Y-m-d',$dueDate)] = [
                                'data' => [
                                    'total' => $oneObj['price_after_vat'],
                                    'leftDays' => $value->leftDays,
                                    'main' => 0,
                                    'items' => [[
                                        'type' => 'addon',
                                        'data' => $oneObj,
                                    ]],
                                ]
                            ];
                        }
                    }    
                }
              
            }

            // Check New Invoices For Extra Quota
            $userExtraQuotas = UserExtraQuota::NotDeleted()->groupBy(['user_id','end_date'])->get();
            $found = [];
            foreach ($userExtraQuotas as $userExtraQuota) {
                $userExtra = UserExtraQuota::dataList($userExtraQuota->user_id,$userExtraQuota->end_date)['data'];
                $userObj = CentralUser::find($userExtraQuota->user_id);
                foreach ($userExtra as $value) {
                    if($value->leftDays <= 7){

                        $membershipObj = ExtraQuota::getData($value->ExtraQuota);
                        if(isset($found[$membershipObj->id])){
                            $found[$membershipObj->id] = $found[$membershipObj->id] + 1;
                        }else{
                            $found[$membershipObj->id] = 1;
                        }
                        $oneObj = [];
                        $oneObj['id'] = $membershipObj->id;
                        $oneObj['title_ar'] = $membershipObj->extra_count . ' '.$membershipObj->extraTypeText;
                        $oneObj['title_en'] = $membershipObj->extra_count . ' '.$membershipObj->extraTypeText;
                        if($value->duration_type == 1){
                            $oneObj['price'] = $membershipObj->monthly_price;
                            $total = $membershipObj->monthly_after_vat;
                            $oneObj['price_after_vat'] = $membershipObj->monthly_after_vat;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }else if($value->duration_type == 2){
                            $oneObj['price'] = $membershipObj->annual_price;
                            $total = $membershipObj->annual_after_vat;
                            $oneObj['price_after_vat'] = $membershipObj->annual_after_vat;
                            $dueDate = strtotime('+1 day',strtotime($value->end_date) );
                        }
                        $oneObj['duration_type'] = $value->duration_type;
                        $oneObj['quantity'] = $found[$membershipObj->id];

                        if(isset($invoices[$userObj->id])){
                            if(isset($invoices[$userObj->id][date('Y-m-d',$dueDate)])){
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['total'] =  $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['total'] + $total;
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data']['items'][] = [
                                    'type' => 'extra_quota',
                                    'data' => $oneObj,
                                ]; 
                            }else{
                                $invoices[$userObj->id][date('Y-m-d',$dueDate)]['data'] = [
                                    'total' => $total,
                                    'leftDays' => $value->leftDays,
                                    'main' => 0,
                                    'items' => [[
                                        'type' => 'extra_quota',
                                        'data' => $oneObj,
                                    ]],
                                ];
                            }
                        }else{
                            $invoices[$userObj->id][date('Y-m-d',$dueDate)] = [
                                'data' => [
                                    'total' => $oneObj['price_after_vat'],
                                    'leftDays' => $value->leftDays,
                                    'main' => 0,
                                    'items' => [[
                                        'type' => 'extra_quota',
                                        'data' => $oneObj,
                                    ]],
                                ]
                            ];
                        }
                    }    
                }
              
            }

            $channelObj = \DB::connection('main')->table('channels')->first();

            foreach($invoices as $invoiceKey  =>  $invoice){
                foreach ($invoice as $invoiceDate => $oneItem) {

                    $invoiceObj = Invoice::NotDeleted()->where('client_id',$invoiceKey)->where('items',serialize($oneItem['data']['items']))->where('due_date',$invoiceDate)->first();

                    $status = 2;
                    if(date('Y-m-d',strtotime($invoiceDate)) > date('Y-m-d')){
                        $status = 3;
                    }

                    if(!$invoiceObj){
                        $invoiceObj = new Invoice;
                        $invoiceObj->client_id = $invoiceKey;
                        $invoiceObj->due_date = $invoiceDate;
                        $invoiceObj->total = $oneItem['data']['total'];
                        $invoiceObj->items = serialize($oneItem['data']['items']);
                        $invoiceObj->main = $oneItem['data']['main'];
                        $invoiceObj->status = $status;
                        $invoiceObj->sort = Invoice::newSortIndex();
                        $invoiceObj->created_at = date('Y-m-d H:i:s');
                        $invoiceObj->created_by = 1;
                        $invoiceObj->save();
                    }else{
                        if($invoiceObj->status == 2){
                            $invoiceObj->status = 3;
                        }
                        $invoiceObj->main = $oneItem['data']['main'];
                        $invoiceObj->save();
                    }

                    $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
                    $data['phone'] = str_replace('+','',$invoiceObj->Client->phone);
                    
                    if($oneItem['data']['leftDays'] == 7 && (int) date('H') == 12){
                        // Invoice Created;
                        $data['body'] = 'Invoice #'.$invoiceObj->id. ' For '.date('M').' has been created';
                        $test = $whatsLoopObj->sendMessage($data);

                    }else if($oneItem['data']['leftDays'] == 3 && (int) date('H') == 12){
                        // First Reminder
                        $data['body'] = 'First Reminder for Invoice #'.$invoiceObj->id;
                        $test = $whatsLoopObj->sendMessage($data);

                    }else if($oneItem['data']['leftDays'] == 1 && (int) date('H') == 12){
                        // Second Reminder
                        $data['body'] = 'Second Reminder for Invoice #'.$invoiceObj->id;
                        $test = $whatsLoopObj->sendMessage($data);

                    }else if($oneItem['data']['leftDays'] < 0){
                        // Suspend 
                        if($invoiceObj->status == 2  && (int) date('H') == 9 ){
                            $subscriptions = '( ';
                            $userObj = $invoiceObj->Client;
                            $domainObj = Domain::where('domain',CentralUser::getDomain($userObj))->first();
                            $tenant = Tenant::find($domainObj->tenant_id);
                            
                            foreach($oneItem['data']['items'] as $itemKey => $anItem){
                                $subscriptions.= $anItem['type'] .': '.$anItem['data']['title_en'].',';

                                if($anItem['type'] == 'membership'){
                                    // dd($userObj->id);

                                    $userObj->membership_id = null;
                                    $userObj->save();
                                    
                                    tenancy()->initialize($tenant);

                                    $tenantUserObj = User::first();
                                    $tenantUserObj->membership_id = null;
                                    $tenantUserObj->save();
                                    
                                    tenancy()->end($tenant);
                                }

                                if($anItem['type'] == 'addon'){
                                    $userAddonsArr = unserialize($userObj->addons);
                                    $userAddonsArr = array_diff( $userAddonsArr, [$anItem['data']['id']] );

                                    $userObj->addons = !empty($userAddonsArr) ? serialize($userAddonsArr) : null;
                                    $userObj->save();

                                    tenancy()->initialize($tenant);

                                    $tenantUserObj = User::first();
                                    $tenantUserObj->addons = !empty($userAddonsArr) ? serialize($userAddonsArr) : null;
                                    $tenantUserObj->save();
                                    
                                    tenancy()->end($tenant);

                                    $addonObj = UserAddon::where('user_id',$userObj->id)->where('addon_id',$anItem['data']['id'])->orderBy('id','DESC')->first();
                                    $addonObj->status = 2;
                                    $addonObj->save();
                                }

                                if($anItem['type'] == 'extra_quota'){
                                    $addonObj = UserExtraQuota::where('user_id',$userObj->id)->where('extra_quota_id',$anItem['data']['id'])->orderBy('id','DESC')->first();
                                    $addonObj->status = 2;
                                    $addonObj->save();
                                }


                            }
                            $subscriptions = substr($subscriptions, 0, -1). ' )';
                            $data['body'] = 'Subscription of '.$subscriptions. ' For '.date('M').' has been ended due to unpaid invoice #'.$invoiceObj->id;
                            $test = $whatsLoopObj->sendMessage($data);
                        }   
                    }

                }
            }
        }
    }
}
