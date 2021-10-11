<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class NewClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $cartObj;
    public $type;
    public $transaction_id;
    public $paymentGateaway;
    public $startDate;
    public $invoiceObj;
    public $transferObj;
    public $arrType;
    public $myEndDate;
    public function __construct($cartObj,$type,$transaction_id,$paymentGateaway,$startDate,$invoiceObj=null,$transferObj=null,$arrType=null,$myEndDate=null)
    {
        $this->cartObj = $cartObj;
        $this->type = $type;
        $this->transaction_id = $transaction_id;
        $this->paymentGateaway = $paymentGateaway;
        $this->startDate = $startDate;
        $this->invoiceObj = $invoiceObj;
        $this->transferObj = $transferObj;
        $this->arrType = $arrType;
        $this->myEndDate = $myEndDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentObj = new \SubscriptionHelper(); 
        $paymentObj->newSubscription($this->cartObj,$this->type,$this->transaction_id,$this->paymentGateaway,$this->startDate,$this->invoiceObj,$this->transferObj,$this->arrType,$this->myEndDate);   
    }

}
