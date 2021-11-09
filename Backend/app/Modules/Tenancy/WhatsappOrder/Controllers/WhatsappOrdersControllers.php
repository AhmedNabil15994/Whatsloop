<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\UserAddon;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Models\Product;
use App\Models\WhatsAppCoupon;
use App\Models\Variable;
use App\Models\Template;
use App\Events\SentMessage;
use DataTables;

class WhatsappOrdersControllers extends Controller {

    use \TraitsFunc;
    
    public function checkPerm(){
        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(9,$disabled)){
            $dis = 1;
        }
        return $dis;
    }

    public function products(){
        $data['mainData'] = [
            'title' => trans('main.products'),
            'url' => 'products',
            'icon' => ' fab fa-product-hunt',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.id'),
            ],
            'name' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.name'),
            ],
            'price' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.price'),
            ],
        ];
        $input = \Request::all();
        if(isset($input['refresh']) && !empty($input['refresh']) && $input['refresh'] == 'refresh'){
            $data['businessId'] = str_replace('+','',User::first()->phone);
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->getProducts($data);
            $result = $result->json();
            if(isset($result['data']) && isset($result['data']['products'])){
                $products = $result['data']['products'];
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
        }
        $mainData = Product::dataList();
        $mainData['designElems'] = $data;
        $mainData['dis'] = $this->checkPerm();
        return view('Tenancy.WhatsappOrder.Views.V5.products')->with('data', (object) $mainData);
    }

    public function orders(){
        $data['mainData'] = [
            'title' => trans('main.orders'),
            'url' => 'orders',
            'icon' => ' mdi mdi-truck-delivery-outline',
        ];

        $data['searchData'] = [
                'id' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.id'),
                ],
                'from' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker1',
                    'label' => trans('main.dateFrom'),
                ],
                'to' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker2',
                    'label' => trans('main.dateTo'),
                ],
        ];
        $mainData = Order::dataList();
        $mainData['designElems'] = $data;
        $mainData['dis'] = $this->checkPerm();
        return view('Tenancy.WhatsappOrder.Views.V5.orders')->with('data', (object) $mainData);
    }

    public function sendLink($id){
        $id = (string) $id; 
        $order_id = base64_decode($id);

        $orderObj = Order::find($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        $orderObj = Order::getData($orderObj);
        $sendData['chatId'] = $orderObj->client_id;
        $url = \URL::to('/').'/orders/'.$orderObj->order_id.'/view';

        $templateObj = Template::NotDeleted()->where('name_ar','whatsAppOrders')->first();
        if($templateObj){
            $content = $templateObj->description_ar;
            $content = str_replace('{CUSTOMERNAME}', str_replace('@c.us','',str_replace('+','',$orderObj->client->name)), $content);
            $content = str_replace('{ORDERID}', $orderObj->order_id, $content);
            $content = str_replace('{ORDERURL}', $url, $content);

            $message_type = 'text';
            $whats_message_type = 'chat';
            $sendData['body'] = $content;
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->sendMessage($sendData);
            $result = $result->json();
            if(isset($result['data']) && isset($result['data']['id'])){
                $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
                $messageId = $result['data']['id'];
                $lastMessage['status'] = 'APP';
                $lastMessage['id'] = $messageId;
                $lastMessage['fromMe'] = 1;
                $lastMessage['chatId'] = $sendData['chatId'];
                $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
                $lastMessage['body'] = $sendData['body'];
                $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
                $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
                $lastMessage['message_type'] = $message_type;
                $lastMessage['sending_status'] = 1;
                $lastMessage['type'] = $whats_message_type;
                $messageObj = ChatMessage::newMessage($lastMessage);
                $dialog = ChatDialog::getOne($sendData['chatId']);
                $dialog->last_time = $lastMessage['time'];
                $dialogObj = ChatDialog::getData($dialog);
                broadcast(new SentMessage(User::first()->domain , $dialogObj ));
                Session::flash('success',trans('main.inPrgo'));
            }else{
                Session::flash('error',$result['status']['message']);
            }
            $orderObj->channel = $templateObj->channel;
            $orderObj->save();
        }   
        return redirect()->back()->withInput();     
    }



    /********************* Client Views ****************************/

    public function getOneOrder($id){
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || !in_array($orderObj->status,[1,2])){
            return redirect(404);
        }

        $data = Order::getData($orderObj);
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.1.orderProducts')->with('data', (object) $data);
    }

    public function checkCoupon(){
        $input = \Request::all();

        $availableCoupons = WhatsAppCoupon::availableCoupons();
        $availableCoupons = reset($availableCoupons);
        
        $coupon = $input['coupon'];

        if(!empty($coupon)){
            if(count($availableCoupons) > 0 && !in_array($coupon, $availableCoupons)){
                return \TraitsFunc::ErrorMessage('هذا الكود ('.$coupon.') غير متاح حاليا');
            }
        }

        if(!empty($coupon)){
            if(count($availableCoupons) > 0 && in_array($coupon, $availableCoupons)){
                $couponObj = WhatsAppCoupon::getOneByCode($coupon);
                if($couponObj->discount_type == 1){
                    $couponVal = $couponObj->discount_value;
                }else{
                    $couponVal = round(($couponObj->discount_value * $input['total'] ) / 100, 2);
                }
            }
        }
        
        $total = doubleval($input['total']) - $couponVal;
        return $total; 
    }

    public function postNewOrder($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        $orderObj->coupon = $input['coupon'];
        $orderObj->payment_type = $input['payment_type'];
        $orderObj->total_after_discount = $input['total_after_discount'];
        $orderObj->save();

        return redirect()->to('/orders/'.$id.'/personalInfo');
    }

    public function personalInfo($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        
        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.1.personalInfo')->with('data', (object) $data);
    }

    public function postPersonalInfo($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        $names = explode(' ',$input['name']);
        if(count($names) < 2){
            Session::flash('error','يرجي ادخال الاسم الثنائي');
            return redirect()->back()->withInput();
        }

        if(!isset($input['email']) || empty($input['email'])){
            Session::flash('error','يرجي ادخال البريد الالكتروني');
            return redirect()->back()->withInput();
        }

        if(!isset($input['phone']) || empty($input['phone'])){
            Session::flash('error','يرجي ادخال رقم الجوال');
            return redirect()->back()->withInput();
        }

        $orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
        if(!$orderDetailsObj){
            $orderDetailsObj = new OrderDetails;
        }

        $orderDetailsObj->order_id = $id;
        $orderDetailsObj->name = $input['name'];
        $orderDetailsObj->email = $input['email'];
        $orderDetailsObj->phone = $input['phone'];
        $orderDetailsObj->save();

        Session::flash('success','تم تحديث البيانات الشخصية بنجاح');
        return redirect()->to('/orders/'.$id.'/paymentInfo');
    }

    public function paymentInfo($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        
        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        $data['countries'] = countries();
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.1.paymentInfo')->with('data', (object) $data);
    }

    public function getCities(){
        $input = \Request::all();
        $egypt = country($input['id']); 

        $statusObj['regions'] = $egypt->getDivisions();
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }


     public function postPaymentInfo($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
 
        if(!isset($input['country']) || empty($input['country'])){
            Session::flash('error','يرجي اختيار الدولة');
            return redirect()->back()->withInput();
        }

        if(!isset($input['city']) || empty($input['city'])){
            Session::flash('error','يرجي اختيار المدينة');
            return redirect()->back()->withInput();
        }

        if(!isset($input['region']) || empty($input['region'])){
            Session::flash('error','يرجي ادخال الحي');
            return redirect()->back()->withInput();
        }

        if(!isset($input['address']) || empty($input['address'])){
            Session::flash('error','يرجي ادخال اسم الشارع');
            return redirect()->back()->withInput();
        }

        if(!isset($input['shipping_method']) || empty($input['shipping_method'])){
            Session::flash('error','يرجي تحديد خيارات الشحن');
            return redirect()->back()->withInput();
        }

        $orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
        if(!$orderDetailsObj){
            $orderDetailsObj = new OrderDetails;
        }

        $orderDetailsObj->order_id = $orderObj->id;
        $orderDetailsObj->country = $input['country'];
        $orderDetailsObj->city = $input['city'];
        $orderDetailsObj->region = $input['region'];
        $orderDetailsObj->address = $input['address'];
        $orderDetailsObj->shipping_method = $input['shipping_method'];
        $orderDetailsObj->save();

        Session::flash('success','تم تحديث معلومات الشحن بنجاح');
        return redirect()->to('/orders/'.$id.'/completeOrder');
    }

    public function completeOrder($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        if($orderObj->payment_type == 4){ // E-Payment
            return redirect()->to('/orders/'.$id.'/finish');
        }elseif($orderObj->payment_type == 3){ // Bank Transfer
            $data['order'] = Order::getData($orderObj);
            $data['user'] = User::getData(User::first());
            return view('Tenancy.WhatsappOrder.Views.V5.Designs.1.bankTransfer')->with('data', (object) $data);
        }else{

        }
    }

    public function bankTransfer($id,Request $request){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        if(!isset($input['bank_name']) || empty($input['bank_name'])){
            Session::flash('error','يرجي ادخال اسم البنك');
            return redirect()->back()->withInput();
        }

        if(!isset($input['account_name']) || empty($input['account_name'])){
            Session::flash('error','يرجي ادخال اسم صاحب الحساب');
            return redirect()->back()->withInput();
        }

        if(!isset($input['account_number']) || empty($input['account_number'])){
            Session::flash('error','يرجي ادخال رقم الحساب البنك');
            return redirect()->back()->withInput();
        }

        if(!$request->hasFile('file')){
            Session::flash('error','يرجي ارفاق ايصال التحويل');
            return redirect()->back()->withInput();
        }

        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',User::first()->global_id)->first();
        define('TENANT_ID',$tenantObj->tenant_id);

        $fileName = \ImagesHelper::uploadFileFromRequest('bank_transfers', $request->file('file'),$orderObj->id);
        if($fileName == false){
            return false;
        }

        $orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
        if(!$orderDetailsObj){
            $orderDetailsObj = new OrderDetails;
        }

        $orderDetailsObj->order_id = $orderObj->id;
        $orderDetailsObj->bank_name = $input['bank_name'];
        $orderDetailsObj->account_name = $input['account_name'];
        $orderDetailsObj->account_number = $input['account_number'];
        $orderDetailsObj->image = $fileName;
        $orderDetailsObj->save();

        Session::flash('success','تم ارسال بيانات التحويل البنكي بنجاح');
        return redirect()->to('/orders/'.$id.'/finish');
    }

     public function finish($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1 || ($orderObj->payment_type == 3 && $orderObj->Details->image == null)){
            return redirect(404);
        }

        if($orderObj->payment_type == 4){
            $urlSecondSegment = '/noon';
            $noonData = [
                'returnURL' => \URL::to('/orders/pushInvoice/'.$id),
                // 'returnURL' => \URL::to('/pushInvoice'),  // For Local 
                'cart_id' => 'order-'.$id,
                'cart_amount' => $orderObj->total_after_discount > 0 ? $orderObj->total_after_discount : $orderObj->total,
                'cart_description' => 'New Order',
                'paypage_lang' => 'ar',
                'description' => 'New Order For Client '.str_replace('@c.us','',$orderObj->client_id) . ' Channel #'.$orderObj->channel,
            ];

            $paymentObj = new \PaymentHelper(); 
            $resultData = $paymentObj->initNoon($noonData);            
                   
            $result = $paymentObj->hostedPayment($resultData['dataArr'],$urlSecondSegment,$resultData['extraHeaders']);
            $result = json_decode($result);


            if(($result->data) && $result->data->result->redirect_url){
                $data['url'] = $result->data->result->redirect_url;
                $data['urlData'] = explode('?data=',$data['url'])[1];               
            }
        }

        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.1.finish')->with('data', (object) $data);
    }

    public function pushInvoice($id){
        $input = \Request::all();
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);
        if($data['status']->status == 1){
            $orderObj = Order::getOne($id);
            $orderDetailsObj = $orderObj->Details;
            $orderDetailsObj->transaction_id = $data['data']->transaction_id;
            $orderDetailsObj->paymentGateaway = $data['data']->paymentGateaway;
            $orderDetailsObj->save();

            $orderObj->status = 2;
            $orderObj->save();


            $couponObj = WhatsAppCoupon::getOneByCode($orderObj->coupon);
            if($couponObj->valid_type == 1){
                $oldVal = $couponObj->valid_value;
                $couponObj->valid_value = $oldVal - 1;
                $couponObj->save();
            }

            $orderObj = Order::getData($orderObj);
            $sendData['chatId'] = $orderObj->client_id;
            $url = \URL::to('/').'/orders/'.$orderObj->order_id.'/invoice';

            $templateObj = Template::NotDeleted()->where('name_ar','whatsAppInvoices')->first();
            if($templateObj){
                $content = $templateObj->description_ar;
                $content = str_replace('{CUSTOMERNAME}', str_replace('@c.us','',str_replace('+','',$orderObj->client->name)), $content);
                $content = str_replace('{ORDERID}', $orderObj->order_id, $content);
                $content = str_replace('{INVOICEURL}', $url, $content);

                $message_type = 'text';
                $whats_message_type = 'chat';
                $sendData['body'] = $content;
                $mainWhatsLoopObj = new \MainWhatsLoop();
                $result = $mainWhatsLoopObj->sendMessage($sendData);
                $result = $result->json();
                if(isset($result['data']) && isset($result['data']['id'])){
                    $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
                    $messageId = $result['data']['id'];
                    $lastMessage['status'] = 'APP';
                    $lastMessage['id'] = $messageId;
                    $lastMessage['fromMe'] = 1;
                    $lastMessage['chatId'] = $sendData['chatId'];
                    $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
                    $lastMessage['body'] = $sendData['body'];
                    $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
                    $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
                    $lastMessage['message_type'] = $message_type;
                    $lastMessage['sending_status'] = 1;
                    $lastMessage['type'] = $whats_message_type;
                    $messageObj = ChatMessage::newMessage($lastMessage);
                    $dialog = ChatDialog::getOne($sendData['chatId']);
                    $dialog->last_time = $lastMessage['time'];
                    $dialogObj = ChatDialog::getData($dialog);
                    broadcast(new SentMessage(User::first()->domain , $dialogObj ));
                    Session::flash('success',trans('main.inPrgo'));
                }else{
                    Session::flash('error',$result['status']['message']);
                }
            }   
            return redirect()->to(\URL::to('/').'/orders/'.$orderObj->order_id.'/view');            
        }else{
            \Session::flash('error',$data['status']->message);
            return redirect()->to(\URL::to('/').'/orders/'.$orderObj->order_id.'/view');            
        }
    }

    public function invoice($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 2  || ($orderObj->payment_type == 3 && $orderObj->Details->image == null)){
            return redirect(404);
        }

        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        $data['details'] = $orderObj->Details;
        // return view('Tenancy.WhatsappOrder.Views.V5.Invoices.invoice1')->with('data', (object) $data);
        // return view('Tenancy.WhatsappOrder.Views.V5.Invoices.invoice2')->with('data', (object) $data);
        // return view('Tenancy.WhatsappOrder.Views.V5.Invoices.invoice3')->with('data', (object) $data);
        return view('Tenancy.WhatsappOrder.Views.V5.Invoices.invoice4')->with('data', (object) $data);
    }
}
