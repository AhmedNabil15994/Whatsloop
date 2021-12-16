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
use App\Models\Addons;
use App\Models\Variable;
use App\Models\UserAddon;

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
        Variable::insert([
            'var_key' => 'SYNCING',
            'var_value' => 1,
        ]);
        $whatsProductsArr = [];
        $whatsOrdersArr = [];
        if(!empty($this->messages)){
            foreach ($this->messages as $message) {
                ChatMessage::newMessage($message,1);
                if($message['type'] == 'product' && isset($message['metadata']) && isset($message['metadata']['productId'])){
                    $whatsProductsArr[] = $message['metadata']['productId'];
                }

                if($message['type'] == 'order' && isset($message['metadata']) && isset($message['metadata']['orderId'])){
                    $whatsOrdersArr[] = [
                        'orderId' => $message['metadata']['orderId'],
                        'orderToken' => $message['metadata']['orderToken'],
                        'messageId' => $message['id'],
                        'author' => $message['author'],
                    ];
                }
            }

            $userObj = User::first();
            $addonObj = Addons::NotDeleted()->where('id',9)->where('status',1)->first();
            $foundFlag = 0;
            if($addonObj){
                $userAddonObj = UserAddon::NotDeleted()->where('user_id',$userObj->id)->where('addon_id',9)->where('status',1)->first();
                $foundFlag = $userAddonObj ? 1 : 0;
            }

            if($foundFlag){
                $whatsProductsArr = array_unique($whatsProductsArr);

                // Init Whatsloop Helper
                $mainWhatsLoopObj = new \MainWhatsLoop();

                // Fetch Products Data
                $urlData['businessId'] = str_replace('+','',User::first()->phone);
                $productsCall = $mainWhatsLoopObj->getProducts($urlData);
                $productsCall = $productsCall->json();

                if(isset($productsCall['data']) && isset($productsCall['data']['products'])){
                    $products = $productsCall['data']['products'];

                    foreach($products as $oneProduct){
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
                    }
                }
                
                foreach($whatsOrdersArr as $oneOrder){

                    $messageId = $oneOrder['messageId'];
                    $author = $oneOrder['author'];
                    $count = 0;

                    unset($oneOrder['messageId']);
                    unset($oneOrder['author']);
                    $oneOrder['sellerJid'] = str_replace('@c.us','',$urlData['businessId']);
                    // Fetch Orders Data
                    $ordersCall = $mainWhatsLoopObj->getOrder($oneOrder);
                    $ordersCall = $ordersCall->json();

                    if($ordersCall && $ordersCall['status'] && $ordersCall['status']['status'] == 1 && isset($ordersCall['data']['orders']) && !empty($ordersCall['data']['orders'])  ){

                        $ordersData = $ordersCall['data']['orders'];
                        foreach($ordersData as $orderData){
                            $orderObj = Order::getOne($orderData['id']);
                            if(!$orderObj){
                                $orderObj = new Order;
                                $orderObj->status = 1;
                            }

                            $count+= count($orderData['products']);

                            $orderObj->order_id =  $orderData['id'];
                            $orderObj->subtotal = $orderData['subtotal'];
                            $orderObj->tax = $orderData['tax'];
                            $orderObj->total = $orderData['total'];
                            $orderObj->message_id = $messageId;
                            $orderObj->products = serialize($orderData['products']);
                            $orderObj->client_id = $author;
                            $orderObj->products_count = $count;
                            $orderObj->created_at = $orderData['createdAt'];
                            $orderObj->save();
                        }
                    }
                }
            }
        }
        Variable::where('var_key','QRSYNCING')->delete();
        Variable::where('var_key','SYNCING')->delete();
    }
}
