<?php namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\CentralUser;
use App\Models\Variable;
use App\Models\CentralChannel;
use App\Models\UserChannels;
use App\Models\UserExtraQuota;
use App\Models\UserAddon;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\Tenant;
use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CentralWebActions;
use App\Jobs\NewClient;
use DataTables;


class TransferRequestControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.transfers'),
            'url' => 'transfers',
            'name' => 'transfers',
            'nameOne' => 'transfers',
            'modelName' => 'BankTransfer',
            'icon' => ' dripicons-duplicate',
            'sortName' => 'user_id',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
        ];

        $data['tableData'] = [
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
        return $data;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = BankTransfer::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Central.User.Views.index')->with('data', (object) $data);
    }

    public function view($id) {
        $id = (int) $id;

        $userObj = BankTransfer::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = BankTransfer::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.departments') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        return view('Central.TransferRequest.Views.view')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $transferObj = BankTransfer::NotDeleted()->find($id);
        $status = (int) $input['status'];
        if($transferObj == null || !in_array($status , [2,3])) {
            return Redirect('404');
        }

        $oldStatus = $transferObj->status;

        $beginProcess = 0;
        if($status == 2 && $oldStatus != $status){
            $beginProcess = 1;
        }

        if($beginProcess){
            $tenant = Tenant::find($transferObj->tenant_id);
            tenancy()->initialize($tenant);
            $cartObj = Variable::getVar('cartObj');
            $endDate = Variable::getVar('endDate');
            $type = Variable::getVar('inv_status');
            $cartObj = json_decode(json_decode($cartObj));
            tenancy()->end($tenant);

            $invoiceObj = Invoice::getOne($transferObj->invoice_id);
            if(!$invoiceObj){
                $invoiceObj = Invoice::NotDeleted()->where('client_id',$transferObj->user_id)->where('id','>',$transferObj->invoice_id)->where('status','!=',1)->where('total',$transferObj->total)->orderBy('id','DESC')->first();
                if($invoiceObj){
                    $transferObj->invoice_id = $invoiceObj->id;
                    $transferObj->save();
                }else{
                    return Redirect('404');
                }
            }
           
            $data = [
                'cartObj' => $cartObj, 
                'type' => $type,
                'transaction_id' => $transferObj->order_no,
                'paymentGateaway' => trans('main.bankTransfer'),
                'start_date' => $type == 'Suspended' ? date('Y-m-d') : $invoiceObj->due_date,
                'invoiceObj' => $invoiceObj,
                'transferObj' => $transferObj,
                'arrType' => null,
                'myEndDate' => null,
            ];
            try {
                dispatch(new NewClient($data))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            // if($resultData[0] == 0){
            //     Session::flash('error',$resultData[1]);
            //     return back()->withInput();
            // }  
        }

        // if($status == 3){
        //     if($oldStatus !== 2){
        //         $transferObj->status = $status;
        //     }
        // }elseif($status == 2){
        //     $transferObj->status = $status;
        // }
        $transferObj->status = $status;

        $transferObj->updated_at = DATE_TIME;
        $transferObj->updated_by = USER_ID;
        $transferObj->save();

        CentralWebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = BankTransfer::getOne($id);
        CentralWebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }
}
