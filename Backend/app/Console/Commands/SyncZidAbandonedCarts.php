<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncAddonsData;
use App\Models\Template;
use App\Models\CentralVariable;
use App\Models\Variable;
use App\Models\OAuthData;
use App\Models\UserChannels;
use App\Models\User;

class SyncZidAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:zid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Zid Abandoned Carts Every 30 minutes';

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

        $tempObj = Template::where('name_en','abandonedCarts')->first();
        if(!$tempObj){
            Template::create([
                'channel' => UserChannels::first()->instanceId,
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

        $modelName = 'abandonedCarts';
        $service = 'zid';

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = CentralVariable::getVar('ZidMerchantToken');
        $managerToken = Variable::getVar('ZidStoreToken');
        
        $dataURL = $baseUrl.'/managers/store/abandoned-carts'; 

        $tableName = $service.'_'.$modelName;

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
            'params' => [
                'page' => 1,
                'page_size' => 100,
            ],
        ];

        try {
            dispatch(new SyncAddonsData($dataArr))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }
    }
}
