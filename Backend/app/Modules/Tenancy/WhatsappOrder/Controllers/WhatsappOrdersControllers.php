<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Order;
use App\Models\UserAddon;
use App\Models\Product;
use DataTables;

class WhatsappOrdersControllers extends Controller {

    use \TraitsFunc;
    
    public function checkPerm(){
        $$disabled = UserAddon::getDeactivated(User::first()->id);
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

        $mainData = Product::dataList();
        $mainData['designElems'] = $data;
        $mainData['dis'] = $this->checkPerm();
        return view('Tenancy.WhatsappOrder.Views.products')->with('data', (object) $mainData);
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
        return view('Tenancy.WhatsappOrder.Views.orders')->with('data', (object) $mainData);
    }

    public function getOneOrder($id){
        $id = (string) $id; 
        $order_id = base64_decode($id);

        $orderObj = Order::getOne($order_id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        $data = Order::getData($orderObj);
        return view('Tenancy.WhatsappOrder.Views.order_products')->with('data', (object) $data);
    }

    public function setOrderIno($id){
        $id = (string) $id; 
        $order_id = base64_decode($id);
        $input = \Request::all();

        $orderObj = Order::getOne($order_id);
        if(!$orderObj || $orderObj->status != 1){
            return redirect(404);
        }

        // dd($input);
        
        $data = Order::getData($orderObj);
        return view('Tenancy.WhatsappOrder.Views.order_info')->with('data', (object) $data);
    }
}
