<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAddon;
use App\Models\CentralChannel;
use App\Models\Invoice;
use App\Models\AddonReport;

class SetAddonReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:addonReports {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Addon Webhook Reports Monthly';

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
        // 1 == Zid || 2 == Salla
        $type_id = $this->argument('type');
        $type = $type_id == 1 ? 'Zid' : 'Salla';
        $addon_id = $type == 'Zid' ? 4 : 5;
        $typeSearchText = $type == 'Zid' ? 's:8:"title_ar";s:4:"زد";s:8:"title_en";s:3:"Zid"' : 's:8:"title_ar";s:6:"سلة";s:8:"title_en";s:5:"Salla"';
        
        $userAddons = UserAddon::with('Client')->where('addon_id',$addon_id)->where('setting_pushed','>=',0)->orderBy('start_date','DESC')->get();

        $i = 0;
        foreach($userAddons as $mainKey => $userData){
            $centralChannelObj = CentralChannel::where('tenant_id',$userData->tenant_id)->first();
            try {
                tenancy()->initialize($userData->tenant_id);
                $webHooks = \DB::table('webhook_calls')->where('name',$type)->select(\DB::raw('* ,count(id) as forThisMonth'))->groupBy(\DB::raw('MONTH(created_at)'))->get();
                tenancy()->end();
                } catch (Exception $e) {
                    
                }
            foreach ($webHooks as $key => $value) {
                $i++;
                $startDate = date('Y-m-01',strtotime($value->created_at));
                $endDate = date('Y-m-t',strtotime($value->created_at));

                $invoiceObj = Invoice::NotDeleted()->where('client_id',$userData->user_id)->whereBetween('due_date',[$startDate,$endDate])->where('status',1)->where('items','LIKE','%'. $typeSearchText .'%')->first();
                if($invoiceObj){
                    $invoiceObj = Invoice::getData($invoiceObj);
                }


                if($centralChannelObj && $centralChannelObj->instanceId){
                    $dataObj = AddonReport::where('type',$type)->where('user_id',$userData->user_id)->where('start_date',$startDate)->where('end_date',$endDate)->first();
                    if(!$dataObj){
                        $dataObj = new AddonReport();
                        $dataObj->type = $type;
                        $dataObj->tenant_id = $userData->tenant_id;
                        $dataObj->instanceId = $centralChannelObj->instanceId;
                        $dataObj->user_id = $userData->user_id;
                        $dataObj->name = $userData->Client->name;
                        $dataObj->count = $value->forThisMonth;
                        $dataObj->paid_date = $invoiceObj != null ? $invoiceObj->paid_date : '';
                        $dataObj->total = $invoiceObj != null ? $invoiceObj->total : '';
                        $dataObj->invoice_id = $invoiceObj != null ? $invoiceObj->id + 10000 : '';
                        $dataObj->start_date = $startDate;
                        $dataObj->end_date = $endDate;
                        $dataObj->created_at = date('Y-m-d H:i:s');
                        $dataObj->save();
                    }else{
                        $dataObj->count = $value->forThisMonth;
                        $dataObj->paid_date = $invoiceObj != null ? $invoiceObj->paid_date : '';
                        $dataObj->total = $invoiceObj != null ? $invoiceObj->total : '';
                        $dataObj->invoice_id = $invoiceObj != null ? $invoiceObj->id + 10000 : '';
                        $dataObj->save();
                    }
                }
            }
        }
    }
}
