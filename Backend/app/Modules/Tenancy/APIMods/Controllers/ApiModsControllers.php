<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use DataTables;
use Storage;
use App\Models\User;

class ApiModsControllers extends Controller {

    use \TraitsFunc;

    public function index(Request $request) {
        if($request->ajax()){
            return Datatables::of([])->make(true);
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.statuses'),
            'url' => 'statuses',
            'name' => 'statuses',
            'nameOne' => 'status',
            'modelName' => 'Status',
            'icon' => 'mdi mdi-format-list-bulleted-type',
        ];
        $data['designElems']['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => [],
                'label' => trans('main.status'),
            ],
            'from' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '',
                'id' => 'datepicker1',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '',
                'id' => 'datepicker2',
                'label' => trans('main.dateTo'),
            ],
        ];

        $data['designElems']['tableData'] = [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],

            'status' => [
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
        ];
      
        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function report(Request $request) {
        if($request->ajax()){
            // $data = User::dataList();
            return Datatables::of([])->make(true);
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.statuses'),
            'url' => 'statuses',
            'name' => 'statuses',
            'nameOne' => 'status',
            'modelName' => 'Status',
            'icon' => 'mdi mdi-file-account-outline',
        ];
        $data['designElems']['searchData'] = [];
        $data['designElems']['tableData'] = [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'group' => [
                'label' => trans('main.group'),
                'type' => '',
                'className' => '',
                'data-col' => 'group_id',
                'anchor-class' => '',
            ],
            'addType' => [
                'label' => trans('main.addType'),
                'type' => '',
                'className' => '',
                'data-col' => 'addType',
                'anchor-class' => '',
            ],
            'contactsNos' => [
                'label' => trans('main.contactsNos'),
                'type' => '',
                'className' => '',
                'data-col' => 'contactsNos',
                'anchor-class' => '',
            ],
            'hasWhats' => [
                'label' => trans('main.hasWhats'),
                'type' => '',
                'className' => '',
                'data-col' => 'hasWhats',
                'anchor-class' => '',
            ],
            'hasNotWhats' => [
                'label' => trans('main.hasNotWhats'),
                'type' => '',
                'className' => '',
                'data-col' => 'hasNotWhats',
                'anchor-class' => '',
            ],
            'addNos' => [
                'label' => trans('main.addNos'),
                'type' => '',
                'className' => '',
                'data-col' => 'addNos',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.sentDate'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
        ];

        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function msgsArchive(Request $request){
        if($request->ajax()){
            // $data = User::dataList();
            return Datatables::of([])->make(true);
        }

        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value->id;
            $channelObj->title = $value->name;
            $channels[] = $channelObj;
        }

        $message_types = [
            ['id'=>1,'title'=>trans('main.text')],
            ['id'=>2,'title'=>trans('main.photoOrFile')],
            ['id'=>4,'title'=>trans('main.sound')],
            ['id'=>5,'title'=>trans('main.link')],
            ['id'=>6,'title'=>trans('main.whatsappNos')],
            ['id'=>7,'title'=>trans('main.mapLocation')],
        ];

        $data['designElems']['mainData'] = [
            'title' => trans('main.msgsArchive'),
            'url' => 'msgsArchive',
            'name' => 'msgsArchive',
            'nameOne' => 'msgsArchive',
            'modelName' => 'msgsArchive',
            'icon' => 'mdi mdi-archive-outline',
        ];
        $data['designElems']['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
            ],
            'sender' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.sender'),
            ],
            'receiver' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.receiver'),
            ],
            'channel' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $channels,
                'label' => trans('main.channel'),
            ],
            'message_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $message_types,
                'label' => trans('main.message_type'),
            ],
            'message_content' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '5',
                'label' => trans('main.message_content'),
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => [['id'=>1,'title'=>trans('main.sent')],['id'=>2,'title'=> trans('main.received')],['id'=>3,'title'=> trans('main.seen')]],
                'label' => trans('main.status'),
            ],
            'from' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '',
                'id' => 'datepicker1',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '',
                'id' => 'datepicker2',
                'label' => trans('main.dateTo'),
            ],
        ];
        $data['designElems']['tableData'] = [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'sender' => [
                'label' => trans('main.sender'),
                'type' => '',
                'className' => '',
                'data-col' => 'sender',
                'anchor-class' => '',
            ],
            'receiver' => [
                'label' => trans('main.receiver'),
                'type' => '',
                'className' => '',
                'data-col' => 'receiver',
                'anchor-class' => '',
            ],
            'channel' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => '',
                'data-col' => 'channel',
                'anchor-class' => '',
            ],
            'message_type' => [
                'label' => trans('main.message_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'message_type',
                'anchor-class' => '',
            ],
            'message_content' => [
                'label' => trans('main.message_content'),
                'type' => '',
                'className' => '',
                'data-col' => 'message_content',
                'anchor-class' => '',
            ],
            'status' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'status',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.sentDate'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
        ];

        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

}
