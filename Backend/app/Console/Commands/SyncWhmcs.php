<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CentralUser;
use App\Models\Invoice;
use App\Models\TenantPivot;
use App\Models\OldMembership;

class SyncWhmcs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:whmcs {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Whmcs Data Everyday';

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
        $this->apiURL = 'http://whmcs.whatsloop.loc';//'https://wloop.net/whmcsAPI/';
        $this->authorization = 'f450c1e62a74ad454a4a1eb86abe2d2d';
        $type_id = $this->argument('type');
        if($type_id == 1){
            $this->fetchAll();
        }else{
            $this->runDailyJob();
        }
        
    }


    public function fetchAll(){
        $this->fetchClients();
        $this->fetchInvoices();
    }

    public function fetchClients($type=null){
        // Begin Working With Clients Data to assign to wloop db
        $clientsData = [];
        $whmcsClientsData = [];

        $mainURL = $this->apiURL .'clients';
        $urlData = [
            'start' => 0,
            'limit' => 1000,
            'sorting' => 'DESC',
            'orderby' => 'id',
            'status' => '',
        ];

        $whmcsClients =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->get($mainURL,$urlData);

        $result = $whmcsClients->json();
        if($result && isset($result['data'])){
            $whmcsClientsData = $result['data'];
        }

        $foundData = $this->getMatchingClients($whmcsClientsData,$type);
        
        foreach ($foundData as $key => $value) {
            $term = strtolower($value['email']);
            CentralUser::whereRaw('lower(email) like (?)',["{$term}"])->update(['whmcs_id'=>$value['whmcs_id']]);
        }

        $users = CentralUser::NotDeleted()->where('group_id',0)->where('whmcs_id',null)->where('status',1)->get();

        foreach ($users as $key => $userObj) {
            $names = explode(' ', $userObj->name,2);
            if(TenantPivot::where('global_user_id',$userObj->global_id)->first() != null){
                $tenantId = TenantPivot::where('global_user_id',$userObj->global_id)->first()->tenant_id;
            }
            $item = [
                'firstname' => $names[0],
                'lastname' => isset($names[1]) && !empty($names[1]) ? $names[1] : $names[0],
                'phonenumber' => $userObj->phone,
                'email' => $userObj->email,
                'password2' => 'user'.$userObj->id,
                'address1' => 'address1',
                'city' => 'city',
                'state' => 'state',
                'postcode' => 'postcode',
                'country' => 'SA',
            ];    

            if($userObj->whmcs_id == null){
                if( $userObj->email == null){
                    $item['email'] = 'it@whatsloop.net';
                }

                $postClient =  \Http::withHeaders([
                'AUTHORIZATION' => $this->authorization,
                ])->post($mainURL.'/addClient',$item);

                $postResult = $postClient->json();
                if($postResult['status']['status'] == 0){
                    return 1;
                }
                
                if($postResult && isset($postResult['data']) && isset($postResult['data']['clientid'])){
                    $userObj->whmcs_id = $postResult['data']['clientid'];
                    $userObj->save();
                } 
            }
        }
    }

    public function getMatchingClients($clients,$type=null){
        $foundData = [];

        $usersEmail = CentralUser::NotDeleted()->with('tenants')->where('group_id',0)->where(function($whereQuery) use ($type){
            if($type != null){
                $whereQuery->where('whmcs_id',null);
            }
        })->where('status',1)->pluck('email');
        $usersEmail = $usersEmail->map(function ($name) {
           return strtolower($name);
        });
        $usersEmail = reset($usersEmail);

        foreach ($clients as $key => $client) {
            if(in_array(strtolower($client['email']), $usersEmail)){
                $foundData[] = [
                    'email' => strtolower($client['email']),
                    'whmcs_id' => $client['id'],
                ];
            }
        }

        return $foundData;
    }
    
    public function fetchInvoices($type=null){
        // Begin Working With Invoices Data to assign to wloop db        
        $users = CentralUser::NotDeleted()->where('group_id',0)->where('whmcs_id','!=',null)->where('status',1)->orderBy('id','DESC')->get();

        foreach ($users as $key => $userObj) {
            $this->setInvoice($userObj,$type);
        }
    }

    public function setInvoice($userObj,$type=null){
        // $products = $this->getProducts();
        $domain = CentralUser::getDomain($userObj);
        $clientid = $userObj->whmcs_id;
        $pid = '0';
        $billingcycle = '';
        $notes = '';

        $invoices = Invoice::NotDeleted()->with('OldMembership')->where('client_id',$userObj->id)->where(function($whereQuery) use ($type){
            if($type != null){
                $whereQuery->where('whmcs_invoice_id',null);
            }
        })->where('status',1)->orderBy('id','DESC')->get();
        foreach ($invoices as $key => $invoice) {

            $invoiceObj = Invoice::getData($invoice);
            $start_date = $invoice->due_date;
            if(date('Y-m-d',strtotime($invoice->paid_date)) > $invoice->due_date){
                $start_date = $invoice->paid_date;
            }
            $paymentmethod = $invoiceObj->payment_method == 1 ? 'dscpayment' : 'dscmada';
            $packageType = $this->checkZidOrSalla($invoiceObj->items);
            $end_date = $packageType[1] == 2 ? date('Y-m-d',strtotime('+1 year',strtotime($start_date))) : date('Y-m-d',strtotime('+1 month',strtotime($start_date)));
            
            $billingcycle =  $packageType[1] == 2 ? 'Annual' : 'Monthly';
            $notes = 'فاتورة #'.($invoiceObj->id) ."\r\n";
            
            $oldMemb = OldMembership::where('user_id',$invoice->client_id)->first();
            $invoiceObj->oldPrice = $oldMemb ? OldMembership::calcOldPrice($oldMemb,$billingcycle) : 0;
            
            $pid = $this->getInvoiceItemsIDS($packageType[0],$invoiceObj->items);

            if($invoiceObj->whmcs_order_id == null){
                $this->addOrder($clientid,$paymentmethod,$pid,$domain,$billingcycle,$invoiceObj,$invoice);
            }

            $this->updateClientProduct($clientid,$invoiceObj,$pid,$billingcycle,$start_date,$end_date,$domain);

            $this->payInvoice($invoiceObj,$invoice,$paymentmethod,$notes,$start_date,$end_date);

        }

        // dd($invoices);
    }

    public function updateClientProduct($clientid,$invoiceObj,$pid,$billingcycle,$start_date,$end_date,$domain){

        $mainURL = $this->apiURL .'orders/getClientProduct';
        $urlData = [
            'clientid' => $clientid,
        ];

        $pidArr = explode(',', $pid);
        $services_ids = [];
        foreach ($pidArr as $key => $value) {
            if($value != ''){
                $urlData['pid'] = $value;

                $getClientProduct =  \Http::withHeaders([
                    'AUTHORIZATION' => $this->authorization,
                ])->post($mainURL,$urlData);
                $result = $getClientProduct->json();
                
                if($result && isset($result['data']) && isset($result['data']['products']['product'][0])){
                $services_ids[] = $result['data']['products']['product'][0]['id'];
                }
            }
        }

        foreach ($services_ids as $key => $value) {
            $updateClientProductURL = $this->apiURL .'orders/updateClientProduct';
            $updateClientProductURLData = [
                'serviceid' => $value,
                'regdate' => $start_date,
                'nextduedate' =>  date('Y-m-d',strtotime('-1 day',strtotime($end_date))),
                'terminationdate' => $end_date,
                'domain' => $domain,
                'status' => 'Active',
                'billingcycle' => $billingcycle,
            ];

            $updateClientProduct =  \Http::withHeaders([
                'AUTHORIZATION' => $this->authorization,
            ])->post($updateClientProductURL,$updateClientProductURLData);
            $updateResult = $updateClientProduct->json();
        }
    }

    public function addOrder($clientid,$paymentmethod,$pid,$domain,$billingcycle,$invoiceObj,$invoice){

        $mainURL = $this->apiURL .'orders/addOrder';
        $urlData = [
            'clientid' => $clientid,
            'paymentmethod' => $paymentmethod,
            'pid' => $pid,
            'domain' => $domain,
        ];

        $addOrder =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->post($mainURL,$urlData);

        $result = $addOrder->json();

        if($result && isset($result['data'])){
            $orderId = isset($result['data']['orderid']) ? $result['data']['orderid'] : '';
            $invoiceId = isset($result['data']['invoiceid']) ? $result['data']['invoiceid'] : '';

            $invoice->whmcs_order_id = $orderId;
            $invoice->whmcs_invoice_id = $invoiceId;
            $invoice->save();
        }
        return 1;
    }

    public function payInvoice($invoiceObj,$invoice,$paymentmethod,$notes,$start_date,$end_date){
        $hasDiscount = 0;

        if($invoiceObj->roTtotal != $invoice->total){
            $hasDiscount = 1;
            $notes.= 'خصم ( '.(abs(round($invoice->total - $invoiceObj->roTtotal, 2))) .' ) ';
        }

        $items_description = $this->reformatItemsDescription($start_date,$end_date,$invoice->whmcs_invoice_id);
      
        // Update Invoice 
        $updateInvoiceURL = $this->apiURL.'invoices/updateInvoice';
        $updateInvoiceURLData = [
            'invoiceid' => $invoice->whmcs_invoice_id,
            'notes' => $notes,
            'date' => date('Y-m-d',strtotime($invoice->created_at)),
            'duedate' => date('Y-m-d',strtotime($invoice->due_date)),
            'itemdescription' => $items_description[0],
            'itemamount' => $items_description[1],
            'itemtaxed' => $items_description[2],
        ];

        if($hasDiscount){
            $updateInvoiceURLData['newitemdescription'] = 'خصم ( '.(abs(round($invoice->total - $invoiceObj->roTtotal, 2))) .' ) ';
            $updateInvoiceURLData['newitemtaxed'] = 1;
            $updateInvoiceURLData['newitemamount'] = - abs(round($invoice->total - $invoiceObj->roTtotal, 2));
        }

        $updateInv =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->post($updateInvoiceURL,$updateInvoiceURLData);
        $updateInvResult = $updateInv->json();

        // Pay Invoice 
        $payInvoiceURL = $this->apiURL.'invoices/addInvoicePayment';
        $payInvoiceURLData = [
            'invoiceid' => $invoice->whmcs_invoice_id,
            'transid' => $invoice->transaction_id,
            'amount' => $invoiceObj->roTtotal,
            'gateway' => $paymentmethod,
            'date' => date('Y-m-d H:i:s',strtotime($invoice->paid_date)),
        ];
        $payInv =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->post($payInvoiceURL,$payInvoiceURLData);
        $payInvResult = $payInv->json();

        // Add Transaction To Invoice 
        // $addTransactionURL = $this->apiURL.'invoices/addTransaction';
        // $addTransactionURLData = [
        //     'invoiceid' => $invoice->whmcs_invoice_id,
        //     'transid' => $invoice->transaction_id,
        //     'paymentmethod' => $paymentmethod,
        //     'date' => date('d/m/Y H:i',strtotime($invoice->paid_date)),
        // ];

        // $addTransaction =  \Http::withHeaders([
        //     'AUTHORIZATION' => $this->authorization,
        // ])->post($addTransactionURL,$addTransactionURLData);
        // $addTransactionResult = $addTransaction->json();
 
        // Accept Order 
        $acceptOrderURL = $this->apiURL.'orders/acceptOrder';
        $acceptOrderURLData = [
            'orderid' => $invoice->whmcs_order_id,
        ];

        $acceptOrder =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->post($acceptOrderURL,$acceptOrderURLData);
        $acceptOrderResult = $acceptOrder->json();
    }

    public function reformatItemsDescription($start_date,$end_date,$whmcs_invoice_id){
        $description_text = '';
        $amount_text = '';
        $taxed_text = '';

        $mainURL = $this->apiURL .'invoices/getInvoice';
        $urlData = [
            'invoiceid' => $whmcs_invoice_id,
        ];
        
        $end_date = date('Y-m-d',strtotime('-1 day',strtotime($end_date)));

        $whmcsClients =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->get($mainURL,$urlData);

        $result = $whmcsClients->json();
        if($result && isset($result['data'])){
            $items = isset($result['data']['items']['item']) && !empty($result['data']['items']['item']) ? $result['data']['items']['item'] : [];
            foreach ($items as $key => $value) {
                if((int)$value['amount'] > 0){
                    $newDescription = ' ( '. date('d/m/Y',strtotime($end_date)) .' - '. date('d/m/Y',strtotime($start_date)) .' ) '.preg_replace("/\([^)]+\)/","",$value['description']);
                    $description_text.='{"item_id":"'.$value['id'].'","value":"'.$newDescription.'"},';

                    $amount_text.='{"item_id":"'.$value['id'].'","value":"'.$value['amount'].'"},';

                    $taxed_text.='{"item_id":"'.$value['id'].'","value":"'.$value['taxed'].'"},';
                }
            }
        }
        return [
            substr($description_text, 0, -1),
            substr($amount_text, 0, -1),
            substr($taxed_text, 0, -1),
        ];
    }

    public function getProducts(){
        $data = [];

        $mainURL = $this->apiURL .'products';
        $urlData = [
            'start' => 0,
            'limit' => 1000,
            'sorting' => 'DESC',
            'orderby' => 'id',
            'status' => '',
        ];

        $whmcsClients =  \Http::withHeaders([
            'AUTHORIZATION' => $this->authorization,
        ])->get($mainURL,$urlData);

        $result = $whmcsClients->json();
        if($result && isset($result['data'])){
            $data = $result['data'];
        }
        return $data;
    }

    public function getInvoiceItemsIDS($zidOrSalla,$items){
        $pid = '' ;
     
        foreach ($items as $key => $value) {
            if($value['type'] == 'membership' && $value['data']['id'] == 1 ){
                $pid .= '9,';
            }

            if($value['type'] == 'membership' && $value['data']['id'] == 2 ){
                $pid .= '10,';
            }

            if($value['type'] == 'membership' && $value['data']['id'] == 3 ){
                $pid .= '11,';
            }

            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Salla' && $zidOrSalla != 'Salla'){
                $pid .= '16,';
            }elseif($value['type'] == 'addon' && $value['data']['title_en'] == 'Salla' && $zidOrSalla == 'Salla'){
                $pid .= '16,12,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Zid' && $zidOrSalla != 'Zid'){
                $pid .= '15,';
            }elseif($value['type'] == 'addon' && $value['data']['title_en'] == 'Zid' && $zidOrSalla == 'Zid'){
                $pid .= '15,12,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Bot' && !in_array($zidOrSalla, ['Salla','Zid']) ){
                $pid .= '12,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Live Chat'){
                $pid .= '13,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Group Messages'){
                $pid .= '14,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Zapier'){
                $pid .= '17,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Whatsapp Orders'){
                $pid .= '18,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Bot Plus'){
                $pid .= '20,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'API'){
                $pid .= '21,';
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Hosting'){
                $pid .= '8,';
            }
        }
        return $pid;
    }

    public function checkZidOrSalla($items){
        $hasSalla = 0 ;
        $hasZid = 0 ;
        $hasBot = 0 ;
        $duration_type = $items[0]['data']['duration_type'];
        foreach ($items as $key => $value) {
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Salla'){
                $hasSalla = 1;
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Zid'){
                $hasZid = 1;
            }
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Bot'){
                $hasBot = 1;
            }
        }

        if(($hasBot  && $hasZid)){
            return ['Zid',$duration_type];
        }

        if(($hasBot && $hasSalla) ){
            return ['Salla',$duration_type];
        }

        return ['',$duration_type];
    }
    
    public function runDailyJob(){
        $this->fetchClients(2);
        $this->fetchInvoices(2);

    }
}
