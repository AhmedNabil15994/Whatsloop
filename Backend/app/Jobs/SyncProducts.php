<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Product;

class SyncProducts  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $metaData;
    public $author;
    public $messageId;

    public function __construct($metaData,$author,$messageId)
    {
        $this->metaData = $metaData;
        $this->author = $author;
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $mainWhatsLoopObj = new \MainWhatsLoop();

        if(isset($this->metaData['orderId']) && isset($this->metaData['orderToken'])){
            $data['orderId'] = $this->metaData['orderId'];
            $data['orderToken'] = $this->metaData['orderToken'];
            $result = $mainWhatsLoopObj->getOrder($data);
            $testResult = $result->json();
            $count = 0;
            if($testResult && $testResult['status'] && $testResult['status']['status'] == 1 && isset($testResult['data']['orders']) && !empty($testResult['data']['orders'])  ){
                $orderObj = Order::getOne($data['orderId']);
                if(!$orderObj){
                    $orderObj = new Order;
                    $orderObj->status = 1;
                }

                $prodsArr = [];
                foreach($result['data']['orders'][0]['products'] as $oneProduct){
                    $prodsArr[] = $oneProduct['id'];
                    $productObj = Product::getOne($oneProduct['id']);
                    if(!$productObj){
                        $productObj = new Product;
                    }

                    $productObj->product_id =  $oneProduct['id'];
                    $productObj->name =  $oneProduct['name'];
                    $productObj->currency =  $oneProduct['currency'];
                    $productObj->price =  $oneProduct['price'];
                    $productObj->images =  serialize($oneProduct['images']);
                    $productObj->save();
                    $count+= $oneProduct['quantity'] ;
                }

                $orderObj->order_id =  $this->metaData['orderId'];
                $orderObj->subtotal = $result['data']['orders'][0]['subtotal'];
                $orderObj->tax = $result['data']['orders'][0]['tax'];
                $orderObj->total = $result['data']['orders'][0]['total'];
                $orderObj->message_id = $this->messageId;
                $orderObj->products = serialize($result['data']['orders'][0]['products']);
                $orderObj->client_id = $this->author;
                $orderObj->products_count = $count;
                $orderObj->created_at = $result['data']['orders'][0]['createdAt'];
                $orderObj->save();
            }
        }
       
    }
}
