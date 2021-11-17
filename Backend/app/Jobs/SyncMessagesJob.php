<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class SyncMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $messages;
    public function __construct($messages)
    {
        // ini_set('memory_limit', '-1');
        $this->messages = $messages;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $whatsProductsArr = [];
        $whatsOrdersArr = [];
        if(!empty($this->messages)){
            foreach ($this->messages as $message) {
                ChatMessage::newMessage($message,1);
                if($message['type'] == 'product'){
                    $whatsProductsArr[] = $message['metadata']['productId'];
                }

                if($message['type'] == 'order'){
                    $whatsOrdersArr[] = [
                        'orderId' => $message['metadata']['orderId'],
                        'orderToken' => $message['metadata']['orderToken'],
                        'messageId' => $message['id'],
                        'author' => $message['author'],
                    ];
                }
            }

            // $whatsProductsArr = array_unique($whatsProductsArr);

            // // Init Whatsloop Helper
            // $mainWhatsLoopObj = new \MainWhatsLoop();

            // // Fetch Products Data
            // $urlData['businessId'] = str_replace('+','',User::first()->phone);
            // $productsCall = $mainWhatsLoopObj->getProducts($urlData);
            // $productsCall = $productsCall->json();

            // if(isset($productsCall['data']) && isset($productsCall['data']['products'])){
            //     $products = $productsCall['data']['products'];

            //     foreach($products as $oneProduct){
            //         $productObj = Product::getOne($oneProduct['id']);
            //         if(!$productObj){
            //             $productObj = new Product;
            //         }
                    
            //         $productObj->product_id =  $oneProduct['id'];
            //         $productObj->name =  $oneProduct['name'];
            //         $productObj->currency =  $oneProduct['currency'];
            //         $productObj->price =  $oneProduct['price'];
            //         $productObj->images =  serialize($oneProduct['images']);
            //         $productObj->save();
            //     }
            // }

            
            // foreach($whatsOrdersArr as $oneOrder){

            //     $messageId = $oneOrder['messageId'];
            //     $author = $oneOrder['author'];
            //     $count = 0;

            //     unset($oneOrder['messageId']);
            //     unset($oneOrder['author']);

            //     // Fetch Orders Data
            //     $ordersCall = $mainWhatsLoopObj->getOrder($oneOrder);
            //     $ordersCall = $ordersCall->json();

            //     if($ordersCall && $ordersCall['status'] && $ordersCall['status']['status'] == 1 && isset($ordersCall['data']['orders']) && !empty($ordersCall['data']['orders'])  ){
            //         $orderObj = Order::getOne($oneOrder['orderId']);
            //         if(!$orderObj){
            //             $orderObj = new Order;
            //             $orderObj->status = 1;
            //         }

            //         foreach($ordersCall['data']['orders'][0]['products'] as $oneProduct){
            //             $count+= $oneProduct['quantity'];
            //         }

            //         $orderObj->order_id =  $oneOrder['orderId'];
            //         $orderObj->subtotal = $ordersCall['data']['orders'][0]['subtotal'];
            //         $orderObj->tax = $ordersCall['data']['orders'][0]['tax'];
            //         $orderObj->total = $ordersCall['data']['orders'][0]['total'];
            //         $orderObj->message_id = $messageId;
            //         $orderObj->products = serialize($ordersCall['data']['orders'][0]['products']);
            //         $orderObj->client_id = $author;
            //         $orderObj->products_count = $count;
            //         $orderObj->created_at = $ordersCall['data']['orders'][0]['createdAt'];
            //         $orderObj->save();
            //     }
            // }
        }
    }
}
