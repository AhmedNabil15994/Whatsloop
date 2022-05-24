<?php namespace App\Http\Controllers;

use Request;
use Response;
use URL;
use Session;
use Illuminate\Support\Facades\Http;
use \Whmcs;

class HomeController extends Controller {

	use \TraitsFunc;

	public function getProducts()
	{		
		$input = \Request::all();

		$postfields = array(
		    'action' => 'GetProducts',
		    'limitstart' => isset($input['start']) && !empty($input['start']) ? $input['start'] : null,
		    'limitnum' => isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : null,
		    'sorting' => isset($input['sorting']) && !empty($input['sorting']) ? $input['sorting'] : null,
		    'orderby' => isset($input['orderby']) && !empty($input['orderby']) ? $input['orderby'] : null,
		    'search' => isset($input['search']) && !empty($input['search']) ? $input['search'] : null,
		    'status' => isset($input['status']) && !empty($input['status']) ? $input['status'] : null,
		);

		$data = \WhmcsHelper::pullData($postfields);
		$dataList['data'] = isset($data['products']['product']) ? $data['products']['product'] : $data;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}


	/************************* Clients *******************************/
	public function getClients()
	{		
		$input = \Request::all();

		$postfields = array(
		    'action' => 'GetClients',
		    'limitstart' => isset($input['start']) && !empty($input['start']) ? $input['start'] : null,
		    'limitnum' => isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : null,
		    'sorting' => isset($input['sorting']) && !empty($input['sorting']) ? $input['sorting'] : null,
		    'orderby' => isset($input['orderby']) && !empty($input['orderby']) ? $input['orderby'] : null,
		    'search' => isset($input['search']) && !empty($input['search']) ? $input['search'] : null,
		    'status' => isset($input['status']) && !empty($input['status']) ? $input['status'] : null,
		);

		$data = \WhmcsHelper::pullData($postfields);
		$dataList['data'] = isset($data['clients']['client']) ? $data['clients']['client'] : $data;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function addClient(){
		$input = \Request::all();
		$input['action'] = 'AddClient';
		if(isset($input['password2']) && !empty($input['password2'])){
			$input['password2'] = md5($input['password2']);
		}
		if(isset($input['phonenumber']) && !empty($input['phonenumber'])){
			$input['phonenumber'] = str_replace('+','',$input['phonenumber']);
		}
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function updateClient(){
		$input = \Request::all();
		$input['action'] = 'UpdateClient';
		if(isset($input['password2']) && !empty($input['password2'])){
			$input['password2'] = md5($input['password2']);
		}
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}


	/************************* Orders *******************************/
	public function getOrders(){		
		$input = \Request::all();

		$postfields = array(
		    'action' => 'GetOrders',
		    'limitstart' => isset($input['start']) && !empty($input['start']) ? $input['start'] : null,
		    'limitnum' => isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : null,
		    'id' => isset($input['id']) && !empty($input['id']) ? $input['id'] : null,
		    'userid' => isset($input['userid']) && !empty($input['userid']) ? $input['userid'] : null,
		    'status' => isset($input['status']) && !empty($input['status']) ? $input['status'] : null,
		);
		$data = \WhmcsHelper::pullData($postfields);
		$dataList['data'] = isset($data['orders']['order']) ? $data['orders']['order'] : $data;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function addOrder(){
		$input = \Request::all();
		$input['action'] = 'AddOrder';
		$input['noinvoice'] = 0;
		$input['noinvoiceemail'] = 0;
		$input['noemail'] = 0;
		if(isset($input['pid']) && !empty($input['pid'])){
			$input['pid'] = explode(',', $input['pid']);
		}
		if(isset($input['domain']) && !empty($input['domain'])){
			$input['domain'] = explode(',', $input['domain']);
		}
		if(isset($input['priceoverride']) && !empty($input['priceoverride'])){
			$input['priceoverride'] = explode(',', $input['priceoverride']);
		}
		if(isset($input['billingcycle']) && !empty($input['billingcycle'])){
			$input['billingcycle'] = explode(',', $input['billingcycle']);
		}
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function getClientProduct(){
		$input = \Request::all();
		$input['action'] = 'GetClientsProducts';
		
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function acceptOrder(){
		$input = \Request::all();
		$input['action'] = 'AcceptOrder';
		
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function updateClientProduct(){
		$input = \Request::all();
		$input['action'] = 'UpdateClientProduct';
		
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	/************************* Invoices *******************************/
	public function getInvoices()
	{		
		$input = \Request::all();

		$postfields = array(
		    'action' => 'GetInvoices',
		    'limitstart' => isset($input['start']) && !empty($input['start']) ? $input['start'] : null,
		    'limitnum' => isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : null,
		    'order' => isset($input['order']) && !empty($input['order']) ? $input['order'] : null,
		    'id' => isset($input['id']) && !empty($input['id']) ? $input['id'] : null,
		    'userid' => isset($input['userid']) && !empty($input['userid']) ? $input['userid'] : null,
		    'status' => isset($input['status']) && !empty($input['status']) ? $input['status'] : null,
		    'orderby' => isset($input['orderby']) && !empty($input['orderby']) ? $input['orderby'] : null,
		);
		$data = \WhmcsHelper::pullData($postfields);
		$dataList['data'] = isset($data['invoices']['invoice']) ? $data['invoices']['invoice'] : $data;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function getInvoice()
	{		
		$input = \Request::all();

		$postfields = array(
		    'action' => 'GetInvoice',
		    'invoiceid' => isset($input['invoiceid']) && !empty($input['invoiceid']) ? $input['invoiceid'] : null,
		);
		$data = \WhmcsHelper::pullData($postfields);
		$dataList['data'] = isset($data['invoices']['invoice']) ? $data['invoices']['invoice'] : $data;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function addInvoice(){
		$input = \Request::all();
		$input['action'] = 'CreateInvoice';
		$input['sendinvoice'] = 0;

		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function addInvoicePayment(){
		$input = \Request::all();
		$input['action'] = 'AddInvoicePayment';
		$input['noemail'] = 1;
		
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}

	public function addTransaction(){
		$input = \Request::all();
		$input['action'] = 'AddTransaction';
		$input['noemail'] = 1;
		
		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}


	public function formData($input){
		$data = [];
		if(str_contains($input, '},{')){
			$input = explode('},{', $input);
			foreach ($input as $key => $value) {
				if($key == 0){
					$value = $value.'}';
				}elseif($key == count($input) - 1){
					$value =  '{'.$value;
				}else{
					$value = '{'.$value.'}';
				}
				$value = json_decode($value);
				$data[$value->item_id] = $value->value; 
			}
		}else{
			$value = json_decode($input);
			$data[$value->item_id] = $value->value; 
		}
		
		return $data;
	}

	public function updateInvoice(){
		$input = \Request::all();
		$input['action'] = 'UpdateInvoice';

		if(isset($input['itemdescription']) && !empty($input['itemdescription']) && isset($input['itemamount']) && !empty($input['itemamount']) && isset($input['itemtaxed']) && !empty($input['itemtaxed'])){
			
			$input['itemdescription']= $this->formData($input['itemdescription']);
			$input['itemamount']= $this->formData($input['itemamount']);
			$input['itemtaxed']= $this->formData($input['itemtaxed']);
						
		}

		if(isset($input['newitemdescription']) && !empty($input['newitemdescription']) && isset($input['newitemamount']) && !empty($input['newitemamount']) && isset($input['newitemtaxed']) && !empty($input['newitemtaxed'])){
			
			$input['newitemdescription']= explode(',', $input['newitemdescription']);
			$input['newitemamount']= explode(',', $input['newitemamount']);
			$input['newitemtaxed']= explode(',', $input['newitemtaxed']);
						
		}

		$pullData = \WhmcsHelper::pullData($input);
		if($pullData['result'] == 'error'){
			return \TraitsFunc::ErrorMessage($pullData['message']);
		}
		$dataList['data'] = $pullData;
		$dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);  
	}
}
