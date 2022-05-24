<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model{

    use \TraitsFunc;

    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','client_id');
    }

    public function OldMembership(){
        return $this->hasOne('App\Models\OldMembership','user_id','client_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$client_id=null) {
        $input = \Request::all();

        $source = self::with('OldMembership')->NotDeleted()->where('status','!=',0)->where(function ($query) use ($input,$status,$client_id) { 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if (isset($input['client_id']) && !empty($input['client_id'])) {
                        $query->where('client_id',  $input['client_id']);
                    } 
                    if (isset($input['status']) && $input['status'] != null) {
                        $query->where('status',  $input['status']);
                    } 
                    if (isset($input['due_date']) && !empty($input['due_date'])) {
                        $query->where('due_date',  $input['due_date']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
                    }
                    if($client_id != null){
                        $query->where('client_id',$client_id);
                    }
                })->orderBy('id','DESC');

        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        // $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->client_id = $source->client_id;
        $data->client = $source->Client != null ? $source->Client->name : '';
        $data->company = $source->Client != null ? $source->Client->company : '';
        $data->transaction_id = $source->transaction_id;
        $data->due_date = $source->due_date;
        $data->paid_date = $source->paid_date != null ? $source->paid_date : '';
        $data->payment_method = $source->payment_method;
        $data->notes = $source->notes;
        $data->payment_gateaway = $source->payment_gateaway == 'Noon' ? 'دفع إلكتروني' : $source->payment_gateaway;
        $data->items = $source->items != null ? unserialize($source->items) : [];
        $data->sort = $source->sort;
        $data->main = $source->main;
        $data->whmcs_order_id = $source->whmcs_order_id;
        $data->whmcs_invoice_id = $source->whmcs_invoice_id;
        $data->discount_type = $source->discount_type;
        $data->discount_value = $source->discount_value;
        $domainData = self::checkDomain($data->items,$source->total);
    
        if(in_array($data->id,[1386,12681,12813])){
            $data->oldPrice =  0 ;
            $data->zidOrSalla = 0;
            $data->discount = 0;
            $data->oldDiscount =  $data->discount;
            if($source->main && count($data->items) == 1){
                $data->discount = 0;
            }
            $data->total = $source->total;
            $data->tax = \Helper::calcTax($data->total);
            $data->grandTotal = $data->total - $data->tax;
            $data->roTtotal = $source->total - $data->discount;
        }else if(in_array($data->id, [7680,])){
            $data->oldPrice =  0 ;
            $data->oldDiscount =  0;
            $data->zidOrSalla = 0;
            $data->discount = 460;
            $data->total = $source->total;
            $data->tax = \Helper::calcTax($data->total);
            $data->grandTotal = $data->total - $data->tax;
            $data->roTtotal = $source->total - $data->discount;
        }else{
            $oldPrice = $source->OldMembership != null && $source->main ? OldMembership::calcOldPrice($source->OldMembership,$data->items[0]['data']['duration_type']) : 0 ;
            $prices = self::calcPrices($source,$domainData);
            $data->zidOrSalla = $prices['zidOrSalla'];
            if($prices['zidOrSalla'] && !$oldPrice){
                $prices['discount'] = $prices['discount'] - \Helper::calcTax($prices['discount']) ;
                $prices['oldDiscount'] = $prices['oldDiscount'] - \Helper::calcTax($prices['oldDiscount']) ;
            }
            $data->discount = $prices['discount'];
            $data->oldDiscount = $prices['oldDiscount'];
            $data->total = $prices['total'];
            $data->roTtotal = $prices['roTtotal'];
            $data->tax = $prices['grandTax'];
            $data->grandTotal = $prices['grandTotal'];
            if($source->discount_type != null && $source->discount_value != null){

                $grandForNewDisc = $prices['grandTotal'] - $prices['hostingGrandTotal'];
                $taxForNewDisc = $prices['grandTax'] - $prices['hostingTax'];

                $newDiscountGrand = $source->discount_type == 1 ?  $source->discount_value : ($source->discount_value/100) * $grandForNewDisc;  
                $data->discount+= round($newDiscountGrand,2);
                $data->grandTotal-= round($newDiscountGrand,2);
                // dd($data->grandTotal);
                $data->tax = round(15/100 * $data->grandTotal,2); 
                $data->roTtotal = round($data->tax + $data->grandTotal,2);
            }
        }
        
        $data->status = $source->status;
        $data->statusText = trans('main.invoice_status_'.$source->status);
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function calcPrices($source,$domainData){
        $zidOrSalla = 0;
        $items = $source->items != null ? unserialize($source->items) : [];

        $hostingTax = $domainData[1] == 1 ? \Helper::calcTax($domainData[0]) : 0;
        $hostingGrandTotal = $domainData[1] == 1 ? $domainData[0] - $hostingTax : 0;

        $extraQuotasTotal = self::calcExtraPrices($items);
        $extraQuotasTax = \Helper::calcTax($extraQuotasTotal);
        $extraQuotasGrandTotal = $extraQuotasTotal - $extraQuotasTax;

        $oldPrice = $source->OldMembership != null && $source->main ? OldMembership::calcOldPrice($source->OldMembership,$items[0]['data']['duration_type']) : 0 ;

        
        if($oldPrice == 0){
            $datas = self::checkZidOrSalla($items,$source->total);
            $oldPrice = $datas[0] == 0 ? $source->total : $datas[0] ;
            $zidOrSalla = $datas[1];
        }

        $oldPriceTax = \Helper::calcTax($oldPrice);
        $oldPriceGrandTotal = $oldPrice - $oldPriceTax;
        
        $grandTax = $hostingTax + $oldPriceTax + $extraQuotasTax;
        $grandTotal = $hostingGrandTotal + $oldPriceGrandTotal + $extraQuotasGrandTotal;

        $discount = $oldPrice == 0 ? 0 : abs($source->total - $oldPrice) - ($domainData[1] == 1 ? $domainData[0] : 0) - $extraQuotasTotal;
        $discountTax = \Helper::calcTax($discount);
        $discountGrand = $discount - $discountTax;
        if($source->main && count($items) == 1){
            $discount = 0;
        }
        $oldDiscount = $discount;

        $total = $source->total;
        $roTtotal = $source->total - $discount;

        return [
            'zidOrSalla' => $zidOrSalla,
            'hostingTax' => $hostingTax,
            'hostingGrandTotal' => $hostingGrandTotal,
            'oldPrice' => $oldPrice,
            'oldPriceTax' => $oldPriceTax,
            'oldPriceGrandTotal' => $oldPriceGrandTotal,
            'grandTax' => $grandTax,
            'grandTotal' => $grandTotal,
            'discount' => $discount,
            'discountTax' => $discountTax,
            'discountGrand' => $discountGrand,
            'oldDiscount' => $oldDiscount,
            'total' => $total,
            'roTtotal' => $roTtotal,
        ];

        // if($source->discount_type != null && $source->discount_value != null){
        //     if($data->zidOrSalla == 1 || $data->oldPrice > 0){
        //         if($domainData[1] == 1){
        //             $data->tax = 0;
        //             $data->discount = $data->oldPrice == 0 ? 0 : abs($source->total - $data->oldPrice - ($domainData[1] == 1 ? $domainData[0] : 0));
        //             $data->oldDiscount =  round($data->discount - \Helper::calcTax($data->discount),2);
        //             $data->tax = \Helper::calcTax($data->oldPrice + ($domainData[1] == 1 ? $domainData[0] : 0));
        //             $data->grandTotal =  round($data->oldPrice - $data->tax + ($domainData[1] == 1 ? $domainData[0] : 0),2);
                    
        //             $coupDis = $source->discount_type == 1 ? $source->discount_value : round($source->discount_value*$data->grandTotal/100,2);
    
        //             $data->discount = $data->oldDiscount + $coupDis ;
        //             $data->grandTotal = round($data->grandTotal - $coupDis,2);
    
        //             $data->tax = round(((15/100) * $data->grandTotal),2); 
        //             $data->roTtotal = round($data->grandTotal + $data->tax,2);
        //             $data->total = round($data->grandTotal + $data->tax,2);
        //         }else{
        //             $data->tax = 0;
        //             $data->oldDiscount =  round($data->discount - \Helper::calcTax($data->discount),2);
        //             $data->tax = \Helper::calcTax($data->oldPrice);
        //             $data->grandTotal =  round($data->oldPrice - $data->tax,2);
    
        //             $coupDis = $source->discount_type == 1 ? $source->discount_value : round($source->discount_value*$data->grandTotal/100,2);
    
        //             $data->discount = $data->oldDiscount + $coupDis - ($domainData[1] == 1 ? $domainData[0] : 0);
        //             $data->grandTotal = round($data->grandTotal - $coupDis,2);
    
        //             $data->tax = round(((15/100) * $data->grandTotal),2); 
        //             // $data->tax = round($data->tax - ((15/100) * $data->tax),2); 
        //             $data->roTtotal = round($data->grandTotal + $data->tax,2);
        //             $data->total = round($data->grandTotal + $data->tax,2);
        //         }
                
        //     }else{
        //         $data->oldDiscount =  $data->discount;
        //         $data->oldPrice =  0 ;
        //         $data->zidOrSalla = 0;
        //         $data->tax = round(\Helper::calcTax($data->total),2);
        //         $data->grandTotal =  round($data->total - $data->tax,2);
        //         $data->discount = (round($source->discount_type == 1 ? $data->oldDiscount + $source->discount_value : $data->oldDiscount + (($source->discount_value*$data->grandTotal)/100),2))- ($domainData[1] == 1 ? $domainData[0] : 0);
        //         $data->grandTotal = round($data->grandTotal - $data->discount,2);
        //         $data->tax = round(($data->grandTotal * 15) / 100,2);
        //         $data->total = round($data->grandTotal + $data->tax,2);
        //         $data->roTtotal = round($data->total,2);
        //     }
        // }
    }

    static function calcExtraPrices($items){
        $price = 0;
        foreach ($items as $key => $value) {
            if($value['type'] == 'extra_quota'){
                $price += $value['data']['price_after_vat'];
            }
        }
        return $price;
    }

    static function checkZidOrSalla($items,$total){
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

        if(($hasBot && $hasSalla) || ($hasBot  && $hasZid)){
            return [$total - ($duration_type == 1 ? 230 : 2300),1];
        }

        return [0,0];
    }

    static function checkDomain($items,$total){
        $hasData = 0 ;
        $duration_type = $items[0]['data']['duration_type'];
        foreach ($items as $key => $value) {
            if($value['type'] == 'addon' && $value['data']['title_en'] == 'Hosting'){
                $hasData = 1;
            }
        }

        if(($hasData)){
            return [ ($duration_type == 1 ? 2000 : 20000),1];
        }

        return [0,0];
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

    static function getDisabled($user_id){
        $to = date('Y-m-t');
        $from = date('Y-m-01');
        $lastInvoice = self::NotDeleted()->where('main',1)->whereBetween('due_date',[$from,$to])->where('client_id',$user_id)->where('status',2)->first();
        if(!$lastInvoice){
            $lastInvoice = self::NotDeleted()->where('main',1)->where('client_id',$user_id)->where('status',2)->orderBy('id','desc')->first();
        }
        return $lastInvoice;
    }
}
