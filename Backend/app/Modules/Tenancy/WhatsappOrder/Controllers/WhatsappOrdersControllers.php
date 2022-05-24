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
use App\Models\OAuthData;
use App\Events\SentMessage;
use DataTables;
use Http;

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

    public function settings(){
        $data['mainData'] = [
            'title' => trans('main.settings'),
            'url' => 'settings',
            'icon' => ' mdi mdi-truck-delivery-outline',
        ];
        $mainData['designElems'] = $data;
        $mainData['dis'] = $this->checkPerm();
        $mainData['design'] = Variable::getVar('DESIGN');
        $mainData['invoice'] = Variable::getVar('INVOICE');
        return view('Tenancy.WhatsappOrder.Views.V5.settings')->with('data', (object) $mainData);
    }

    public function postSettings(){
        $input = \Request::all();
        if(isset($input['design']) && !empty($input['design'])){
            $varObj = Variable::where('var_key','DESIGN')->first();
            if(!$varObj){
                $varObj = new Variable;
                $varObj->var_key = 'DESIGN';
            }
            $varObj->var_value = (int) $input['design'];
            $varObj->save();
        }

        if(isset($input['invoice']) && !empty($input['invoice'])){
            $varObj = Variable::where('var_key','INVOICE')->first();
            if(!$varObj){
                $varObj = new Variable;
                $varObj->var_key = 'INVOICE';
            }
            $varObj->var_value = (int) $input['invoice'];
            $varObj->save();
        }
        
        Session::flash('success',trans('main.editSuccess'));
        return redirect()->back();
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
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $userRequestData = $mainWhatsLoopObj->me();
            $userRequestData = $userRequestData->json();

            if($userRequestData && isset($userRequestData['status']) && $userRequestData['status']['status'] == 1){
                $urlData['businessId'] = str_replace('@c.us','',$userRequestData['data']['id']);
                $result = $mainWhatsLoopObj->getProducts($urlData);
                $result = $result->json();
                if(isset($result['data']) && isset($result['data']['products'])){
                    Product::truncate();
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
        }

        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(5,$disabled)){
            $dis = 1;
        }
        $mainData['disAssign'] = $dis;
        $mainData = Product::dataList();
        $mainData['designElems'] = $data;
        $mainData['dis'] = $this->checkPerm();
        $mainData['salla_products'] = \DB::table('salla_products')->get();
        // dd($mainData['salla_products']);
        return view('Tenancy.WhatsappOrder.Views.V5.products')->with('data', (object) $mainData);
    }

    public function assignCategory(){
        $input = \Request::all();
        $productObj = Product::find($input['product_id']);
        $productObj->category_id = $input['category_id'];
        $productObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function assignSallaProduct(){
        $input = \Request::all();
        $productObj = Product::find($input['product_id']);
        $productObj->addon_product_id = $input['salla_product_id'];
        $productObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function sendLink($id){
        $id = (string) $id; 
        $order_id = base64_decode($id);

        $oldOrderObj = Order::find($id);
        if(!$oldOrderObj || $oldOrderObj->status != 1){
            return redirect(404);
        }
        $orderObj = Order::getData($oldOrderObj);
        $sendData['chatId'] = $orderObj->client_id;
        $url = \URL::to('/').'/orders/'.$orderObj->order_id.'/view';
        foreach($orderObj->products as $product){
            $productObj = Product::where('product_id',$product['id'])->first();
            if(!$productObj || $productObj->category_id == null){
                Session::flash('error','يرجي اختيار التصنيف لمنتج: '.$product['name'].' قبل ارسال رابط الشراء للعميل');
                return redirect()->back()->withInput();     
            }
        }

    
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
            $oldOrderObj->channel = $templateObj->channel;
            $oldOrderObj->save();
        }   
        return redirect()->back()->withInput();     
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

    public function initTransferData(){
        $mainData['mainData'] = [
            'title' => trans('main.transfers'),
            'url' => 'whatsappOrders/bankTransfers',
            'name' => 'whatsapp-bankTransfers',
            'nameOne' => 'whatsapp-bankTransfer',
            'modelName' => '',
            'icon' => ' dripicons-duplicate',
            'sortName' => '',
            'addOne' => '',
        ];

        $mainData['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
        ];

        $mainData['tableData'] = [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'order_no' => [
                'label' => trans('main.order_no'),
                'type' => '',
                'className' => '',
                'data-col' => 'order_no',
                'anchor-class' => '',
            ],
            'client' => [
                'label' => trans('main.client'),
                'type' => '',
                'className' => '',
                'data-col' => 'user_id',
                'anchor-class' => '',
            ],
            'total' => [
                'label' => trans('main.total'),
                'type' => '',
                'className' => '',
                'data-col' => 'total',
                'anchor-class' => '',
            ],
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'status',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => 'created_at',
                'anchor-class' => '',
            ],
            'actions' => [
                'label' => trans('main.actions'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
        ];
        return $mainData;
    }

    public function bankTransfers(Request $request){
        if($request->ajax()){
            $data = OrderDetails::transfersList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->initTransferData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function viewTransfer($id){
        $id = (int) $id;

        $userObj = OrderDetails::find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = OrderDetails::getTransfer($userObj);
        $data['designElems'] = $this->initTransferData();
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.transfers') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        return view('Tenancy.WhatsappOrder.Views.V5.viewTransfer')->with('data', (object) $data);      
    }
    
    public function updateTransfer($id) {
        $id = (int) $id;

        $input = \Request::all();
        $oldTransferObj = OrderDetails::find($id);
        $status = (int) $input['status'];
        if($oldTransferObj == null || !in_array($status , [2,3])) {
            return Redirect('404');
        }

        $transferObj = OrderDetails::getTransfer($oldTransferObj);
        $oldStatus = $transferObj->status;
        
        $beginProcess = 0;
        if($status == 2 && $oldStatus != $status){
            $beginProcess = 1;
        }

        if($beginProcess){
            $orderObj = $oldTransferObj->Order;
            $orderObj->status = 2;
            $orderObj->save();

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
        }

        $oldTransferObj->transfer_status = $status;
        $oldTransferObj->save();
        Session::flash('success', trans('main.inPrgo'));
        return \Redirect::back()->withInput();
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = OrderDetails::getOne($id);
        if($dataObj){
            $dataObj->bank_name = null;
            $dataObj->account_name = null;
            $dataObj->account_number = null;
            $dataObj->image = null;
            $dataObj->transfer_status = null;
            $dataObj->transfer_date = null;
            $dataObj->save();
        }
        $data['status'] = \TraitsFunc::SuccessResponse(trans('main.deleteSuccess'));
        return response()->json($data);
    }

    /********************* Client Views ****************************/

    public function getOneOrder($id){
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || !in_array($orderObj->status,[1,2])){
            return redirect(404);
        }

        $products = unserialize($orderObj->products);
        $productsArr = [];
        $baseUrl = 'https://api.salla.dev/admin/v2/products/';
        $token = Variable::getVar('SallaStoreToken'); 
        $userObj = User::first();
        $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
        if($oauthDataObj && $oauthDataObj->authorization != null){
            $token = $oauthDataObj->authorization;
        }

        foreach ($products as $key => $product) {
            $productObj = Product::where('product_id',$product['id'])->first();
            if(!$productObj || $productObj->addon_product_id == null){
                return redirect(404);
            }
            $item=[
                'id' => $product['id'],
                'name' => $product['name'],
                'product_id' => $productObj->addon_product_id,
            ];

            $responseData = Http::withToken($token)->get($baseUrl.$productObj->addon_product_id);
            $result = $responseData->json();
            $item['options'] = $result['data']['options'];
            $productsArr[$product['id']] = $item;
        }

        $data['data'] = Order::getData($orderObj);
        $data['productDetails'] = $productsArr;
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.orderProducts')->with('data', (object) $data);
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

    public function getCities(){
        $input = \Request::all();
        $statusObj['regions'] =  \DB::connection('main')->table('salla_cities')->where('country_id',$input['id'])->get();
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

    public function postNewOrder($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;

        if($designIndex == 1){
            if(!isset($input['payment_type']) || empty($input['payment_type'])){
                Session::flash('error','يرجي اختيار طريقة الدفع');
                return redirect()->back()->withInput();
            }
            $orderObj->payment_type = $input['payment_type'];
        }

        $orderObj->coupon = $input['coupon'];
        $orderObj->options = isset($input['options']) && !empty($input['options']) ? serialize($input['options']) : null ;
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
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.personalInfo')->with('data', (object) $data);
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

        $orderDetailsObj->order_id = $orderObj->id;
        $orderDetailsObj->name = $input['name'];
        $orderDetailsObj->email = $input['email'];
        // $orderDetailsObj->phone = $input['phone'];
        $orderDetailsObj->save();

        $this->updateCustomer($input,$orderObj->id);

        Session::flash('success','تم تحديث البيانات الشخصية بنجاح');
        return redirect()->to('/orders/'.$id.'/paymentInfo');
    }

    public function updateCustomer($input,$orderId){
        $name = explode(' ', $input['name']);
        $mobile = '+'.str_replace('@c.us','',$input['phone']);
        $phoneNumber = preg_replace("/^\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/", '',$mobile);

        $customerArr = [
            'first_name' => $name[0],
            'last_name' => isset($name[1]) && $name[1] != null && $name[1] != '' ? $name[1] : $name[0],
            'mobile' => $phoneNumber,
            'mobile_code_country' => str_replace($phoneNumber,'',$mobile),
            'email' => $input['email'],
        ];

        $orderObj = Order::where('id',$orderId)->first();
        if($orderObj){
            $orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
            if(!$orderDetailsObj){
                $orderDetailsObj = new OrderDetails;
            }
            $orderDetailsObj->order_id = $orderObj->id;
            $orderDetailsObj->name = $input['name'];
            $orderDetailsObj->phone = str_replace('+', '', $mobile);
            $orderDetailsObj->save();

            if($orderDetailsObj->addon_customer_id != null){
                $baseUrl = 'https://api.salla.dev/admin/v2/customers/'.$orderDetailsObj->addon_customer_id;
                $token = Variable::getVar('SallaStoreToken'); 
                $userObj = User::first();
                $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
                if($oauthDataObj && $oauthDataObj->authorization != null){
                    $token = $oauthDataObj->authorization;
                }

                $data = Http::withToken($token)->put($baseUrl,$customerArr);
                $result = $data->json();
            }           
        }
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
        $data['countries'] = \DB::connection('main')->table('salla_countries')->get();
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.paymentInfo')->with('data', (object) $data);
    }

    public function postPaymentInfo($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;
 
        if(!isset($input['country']) || empty($input['country'])){
            Session::flash('error','يرجي اختيار الدولة');
            return redirect()->back()->withInput();
        }

        if(!isset($input['city']) || empty($input['city'])){
            Session::flash('error','يرجي اختيار المدينة');
            return redirect()->back()->withInput();
        }

        if(!isset($input['address']) || empty($input['address'])){
            Session::flash('error','يرجي ادخال العنوان');
            return redirect()->back()->withInput();
        }

        if(!isset($input['street_number']) || empty($input['street_number'])){
            Session::flash('error','يرجي ادخال رقم الشارع');
            return redirect()->back()->withInput();
        }

        if(!isset($input['block']) || empty($input['block'])){
            Session::flash('error','يرجي ادخال رقم البلوك');
            return redirect()->back()->withInput();
        }

        if(!isset($input['postal_code']) || empty($input['postal_code'])){
            Session::flash('error','يرجي ادخال الرمز البريدي');
            return redirect()->back()->withInput();
        }

        if(!isset($input['lat']) || empty($input['lat'])){
            Session::flash('error','يرجي ادخال احداثيات الموقع');
            return redirect()->back()->withInput();
        }
        
        if(!isset($input['lng']) || empty($input['lng'])){
            Session::flash('error','يرجي ادخال احداثيات الموقع');
            return redirect()->back()->withInput();
        }

        $orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
        if(!$orderDetailsObj){
            $orderDetailsObj = new OrderDetails;
        }

        $orderDetailsObj->order_id = $orderObj->id;
        $orderDetailsObj->country = $input['country'];
        $orderDetailsObj->country_id = $input['country_id'];
        $orderDetailsObj->city = $input['city'];
        $orderDetailsObj->city_id = $input['city_id'];
        $orderDetailsObj->street_number = $input['street_number'];
        $orderDetailsObj->address = $input['address'];
        $orderDetailsObj->block = $input['block'];
        $orderDetailsObj->postal_code = $input['postal_code'];
        $orderDetailsObj->lat = $input['lat'];
        $orderDetailsObj->lng = $input['lng'];
        $orderDetailsObj->save();

        Session::flash('success','تم تحديث معلومات الشحن بنجاح');
        return $this->createOrder($orderDetailsObj,$orderObj);
        // if($designIndex == 1){
        //     return redirect()->to('/orders/'.$id.'/completeOrder');
        // }elseif($designIndex == 2 || $designIndex == 3){
        //     return redirect()->to('/orders/'.$id.'/setPaymentType');
        // }
    }

    public function createOrder($orderDetailsObj,$orderObj){
        // dd('be ready');
        $mobile = '+'.$orderDetailsObj->phone;
        $phoneNumber = preg_replace("/^\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/", '',$mobile);

        $mobileCode = str_replace($phoneNumber,'',$mobile);
        $country = \DB::connection('main')->table('salla_countries')->where('mobile_code',$mobileCode)->first();
        $productsArr = [];
        $products = unserialize($orderObj->products);
        $options = $orderObj->options != [] ? unserialize($orderObj->options) : [];

        foreach($products as $product){
            $product_id = Product::where('product_id',$product['id'])->first()->addon_product_id;
            $obj = new \stdClass();
            $obj->id = $product_id;
            $obj->quantity = $product['quantity'];
            // $obj->name = $product['name'];
            // $obj->price = $product['price'];
            if($options != [] && isset($options[$product['id']])){
                $productOptions = $options[$product['id']];
                $arr = [];
                foreach ($productOptions as $oneKey => $productOption) {
                    if(str_contains($productOption, '[') && str_contains($productOption, ']')){
                        // $arr[] = json_decode($productOption);
                    }else{
                        if(is_numeric($productOption)){
                            // $arr[$oneKey] = (int)$productOption;
                            $arr[] = [
                                'id' => (int) $oneKey,
                                "name"=> "اللون",
                                "type"=> "radio",
                                "required"=> true,
                                "values"=> [
                                    [
                                        "id" => (int) $productOption,
                                        "name"=> "ابيض",
                                        "option_id"=> (int) $oneKey,
                                    ],
                                ],
                                // 'name' => 'ابيض',
                                // 'value' => [
                                //     'type' => 'radio',
                                //     'vlaue' => (int) $productOption,
                                // ],
                                // 'required' => 1,
                                // 'type' => 'radio',
                                'product_id' => $product_id,
                            ];
                        }else{
                            // $arr[] = $productOption;
                        }
                    }
                }
                // dd($arr);
                // 1033164210 , 1499337660  ,  2124503054  ,  441746952  ,  1715353233  ,  166645831
                $obj->options =  $arr;
                // $obj->options = [
                //     [
                //         'id' => 1341238682,
                //         'value' => 1033164210
                //     ] , 1499337660  ,  2124503054  ,  441746952  ,  1715353233  ,  166645831
                //     // [
                //     //     "id" => 1110785137,
                //     //     "product_option_id" => 1341238682,
                //     //     "name" => "اللون",
                //     //     "type" => "radio",
                //     //     "value" => [
                //     //       "id" => 1033164210,
                //     //       "name" => "ابيض",
                //     //       "price" => [
                //     //         "amount" => 0,
                //     //         "currency" => "SAR",
                //     //       ],
                //     //     ],
                //     // ],
                // ];
            }
            $productsArr[] = $obj;

        }
        // dd($productsArr);
        $orderData = [
            'customer_id' => $orderDetailsObj->addon_customer_id,
            'receiver' => [
                'name' => $orderDetailsObj->name,
                'country_code' => $country != null ? $country->code : 'SA',
                'phone' => $phoneNumber,
                'country_prefix' => $mobileCode,
                'email' => $orderDetailsObj->email,
                'notify' => 1,
            ],
            'shipping_address' => [
                'country_id' => $orderDetailsObj->country_id,
                'city_id' => $orderDetailsObj->city_id,
                'block' => $orderDetailsObj->block,
                'street_number' => $orderDetailsObj->street_number,
                'address' => $orderDetailsObj->address,
                'postal_code' => $orderDetailsObj->postal_code,
                'geocode' => $orderDetailsObj->lat.','.$orderDetailsObj->lng,
            ],
            'payment' => [
                'status' => 'waiting',
                'method' => 'bank',
                'accepted_methods' => ['bank','credit_card','mada','apple_pay','paypal'],
            ],
            'products' => $productsArr,
        ];
        // dd($orderData);

        if($orderDetailsObj->addon_customer_id != null){
            $baseUrl = 'https://api.salla.dev/admin/v2/orders';
            $token = Variable::getVar('SallaStoreToken'); 
            $userObj = User::first();
            $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
            if($oauthDataObj && $oauthDataObj->authorization != null){
                $token = $oauthDataObj->authorization;
            }

            $data = Http::withToken($token)->post($baseUrl,$orderData);
            $result = $data->json();
            dd($result);
            if(isset($result['success']) && $result['success'] == true && isset($result['data']) && isset($result['data']['urls'])){
                return redirect($result['data']['urls']['customer']);     
            }
        }    
    }

    public function setPaymentType($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;

        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.setPaymentType')->with('data', (object) $data);
    }

    public function postPaymentType($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        if(!isset($input['payment_type']) || empty($input['payment_type'])){
            Session::flash('error','يرجي اختيار طريقة الدفع');
            return redirect()->back()->withInput();
        }
        $orderObj->payment_type = $input['payment_type'];
        $orderObj->save();
        Session::flash('success','تم اختيار طريقة الدفع بنجاح');
        return redirect()->to('/orders/'.$id.'/completeOrder');
    }

    public function completeOrder($id){
        $input = \Request::all();
        $id = (string) $id; 
        $orderObj = Order::getOne($id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;

        if($orderObj->payment_type == 4){ // E-Payment
            return redirect()->to('/orders/'.$id.'/finish');
        }elseif($orderObj->payment_type == 3){ // Bank Transfer
            $data['order'] = Order::getData($orderObj);
            $data['user'] = User::getData(User::first());
            return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.bankTransfer')->with('data', (object) $data);
        }else{
            return redirect()->to('/orders/'.$id.'/finish');
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
        $orderDetailsObj->transfer_date = date('Y-m-d H:i:s');
        $orderDetailsObj->transfer_status = 1;
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
        $designIndex = Variable::getVar('DESIGN') != null ? Variable::getVar('DESIGN') : 1;
        return view('Tenancy.WhatsappOrder.Views.V5.Designs.'.$designIndex.'.finish')->with('data', (object) $data);
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
            \Session::flash('success',trans('main.inPrgo'));
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
        if(!$orderObj || ($orderObj->status != 2  && ($orderObj->payment_type == 3 && $orderObj->Details->image != null) && ($orderObj->payment_type == 4 && $orderObj->Details->transaction_id != null) )){
            return redirect(404);
        }

        $data['order'] = Order::getData($orderObj);
        $data['user'] = User::getData(User::first());
        $data['details'] = $orderObj->Details;
        $invoiceIndex = Variable::getVar('INVOICE') != null ? Variable::getVar('INVOICE') : 1;
        return view('Tenancy.WhatsappOrder.Views.V5.Invoices.invoice'.$invoiceIndex)->with('data', (object) $data);
    }
}
