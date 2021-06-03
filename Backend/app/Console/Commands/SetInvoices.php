<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\CentralUser;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\ExtraQuota;
use App\Models\CentralChannel;

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
            if($value->leftDays <= 3){
                $userObj = CentralUser::where('global_id',$value->global_user_id)->first();
                $membershipObj = $userObj->Membership; 
                $invoiceItems['id'] = $membershipObj->id;
                $invoiceItems['title_ar'] = $membershipObj->title_ar;
                $invoiceItems['title_en'] = $membershipObj->title_en;
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
                $invoices[$userObj->id][date('Y-m-d',$dueDate)] = [
                    'data' => [
                        'total' => $total,
                        'items' => [[
                            'type' => 'membership',
                            'data' => $invoiceItems,
                        ]],
                    ]
                ];
            }
        }

        // Check New Invoices For Addons
        $userAddons = UserAddon::NotDeleted()->groupBy(['user_id','end_date'])->get();
        foreach ($userAddons as $addon) {
            $userAddon = UserAddon::dataList(null,$addon->user_id,$addon->end_date)['data'];
            
            $userObj = CentralUser::find($addon->user_id);

            foreach ($userAddon as $value) {
                if($value->leftDays <= 3){

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
        foreach ($userExtraQuotas as $userExtraQuota) {
            $userExtra = UserExtraQuota::dataList($userExtraQuota->user_id,$userExtraQuota->end_date)['data'];
            $userObj = CentralUser::find($userExtraQuota->user_id);

            foreach ($userExtra as $value) {
                if($value->leftDays <= 3){

                    $membershipObj = ExtraQuota::getData($value->ExtraQuota);
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

        foreach($invoices as $invoiceKey  =>  $invoice){
            foreach ($invoice as $invoiceDate => $oneItem) {
                $invoiceObj = Invoice::NotDeleted()->where('client_id',$invoiceKey)->where('items',serialize($oneItem['data']['items']))->where('due_date',$invoiceDate)->first();

                if(!$invoiceObj){
                    $invoiceObj = new Invoice;
                    $invoiceObj->client_id = $invoiceKey;
                    $invoiceObj->due_date = $invoiceDate;
                    $invoiceObj->total = $oneItem['data']['total'];
                    $invoiceObj->items = serialize($oneItem['data']['items']);
                    $invoiceObj->status = 3;
                    $invoiceObj->sort = Invoice::newSortIndex();
                    $invoiceObj->created_at = date('Y-m-d H:i:s');
                    $invoiceObj->created_by = 1;
                    $invoiceObj->save();
                }else{
                    if($invoiceObj->status == 3 && $invoiceObj->due_date >= date('Y-m-d')){
                        $invoiceObj->status = 2;
                        $invoiceObj->save();
                    }
                }
            }
        }
        
    }
}
