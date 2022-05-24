<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CentralUser;
use App\Models\OAuthData;
use DataTables;


class CentralSallaControllers extends Controller {

    use \TraitsFunc;

    protected function validateInsertObject($input){
        $rules = [
            'user_id' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
            'webhook_secret' => 'required',
        ];

        $message = [
            'user_id.required' => 'Sorry Client Record is Required',
            'client_id.required' => 'Sorry Client ID Record is Required',
            'client_secret.required' => 'Sorry Client Secret Record is Required',
            'webhook_secret.required' => 'Sorry Webhook Secret Record is Required',
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.salla'),
            'url' => 'salla',
            'name' => 'Salla',
            'nameOne' => 'Salla',
            'modelName' => '',
            'icon' => '',
            'sortName' => '',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'name' => [
                'type' => 'name',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.name'),
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
            'name' => [
                'label' => trans('main.name'),
                'type' => '',
                'className' => '',
                'data-col' => 'name',
                'anchor-class' => '',
            ],
            'phone' => [
                'label' => trans('main.phone'),
                'type' => '',
                'className' => '',
                'data-col' => 'phone',
                'anchor-class' => '',
            ],
            'domain' => [
                'label' => trans('main.domain'),
                'type' => '',
                'className' => '',
                'data-col' => 'domain',
                'anchor-class' => '',
            ],
            'channelCodes' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => '',
                'data-col' => 'channels',
                'anchor-class' => '',
            ],
            'client_id' => [
                'label' => 'Client ID',
                'type' => '',
                'className' => '',
                'data-col' => 'client_id',
                'anchor-class' => '',
            ],
            'client_secret' => [
                'label' => 'Client Secret',
                'type' => '',
                'className' => '',
                'data-col' => 'client_secret',
                'anchor-class' => '',
            ],
            'webhook_url' => [
                'label' => 'Webhook URL',
                'type' => '',
                'className' => '',
                'data-col' => 'webhook_url',
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
            $queryData = CentralUser::with(['tenants','OAuthData'])->whereHas('Addons',function($whereHasQuery){
                $whereHasQuery->where('addon_id',5);
            })->whereHas('OAuthData');
            $data = CentralUser::generateSallaObj($queryData);
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Central.User.Views.index')->with('data', (object) $data);
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.salla') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['users'] = CentralUser::sallaList()['data'];
        return view('Central.Salla.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $dataObj = OAuthData::where('type','salla')->where('user_id',$input['user_id'])->first();

        $userObj = CentralUser::NotDeleted()->with(['tenants','OAuthData'])->whereHas('Addons',function($whereHasQuery){
            $whereHasQuery->where('addon_id',5);
        })->find($input['user_id']);

        if($userObj){
        	$userObj = CentralUser::getSallaData($userObj);
        }

        if(!$dataObj){
        	$dataObj = new OAuthData;
	        $dataObj->created_at = date('Y-m-d H:i:s');
        }else{
	        $dataObj->updated_at = date('Y-m-d H:i:s');
        }

        $dataObj->user_id = $userObj->id;
        $dataObj->domain = $userObj->domain;
        $dataObj->phone = $userObj->phone;
        $dataObj->tenant_id = $userObj->tenant_id;
        $dataObj->type = 'salla';
        $dataObj->client_id = $input['client_id'];
        $dataObj->client_secret = $input['client_secret'];
        $dataObj->webhook_secret = $input['webhook_secret'];
	    $dataObj->save();

        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = OAuthData::find($id);
        
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = CentralUser::getSallaData(CentralUser::getOne($userObj->user_id));
        $data['users'] = CentralUser::sallaList()['data'];
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.salla') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        return view('Central.Salla.Views.edit')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $dataObj = OAuthData::find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $userObj = CentralUser::NotDeleted()->with(['tenants','OAuthData'])->whereHas('Addons',function($whereHasQuery){
            $whereHasQuery->where('addon_id',5);
        })->find($input['user_id']);

        if($userObj){
        	$userObj = CentralUser::getSallaData($userObj);
        }

        $dataObj->user_id = $userObj->id;
        $dataObj->domain = $userObj->domain;
        $dataObj->phone = $userObj->phone;
        $dataObj->tenant_id = $userObj->tenant_id;
        $dataObj->type = 'salla';
        $dataObj->client_id = $input['client_id'];
        $dataObj->client_secret = $input['client_secret'];
        $dataObj->webhook_secret = $input['webhook_secret'];
		$dataObj->updated_at = date('Y-m-d H:i:s');
        $dataObj->save();

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

}
