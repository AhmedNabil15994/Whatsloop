<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use DataTables;
use Storage;
use App\Models\Contact;
use App\Models\ContactReport;
use App\Models\UserStatus;
use App\Models\User;
use App\Models\ChatMessage;

class ApiModsControllers extends Controller {

    use \TraitsFunc;

    public function index(Request $request) {
        if($request->ajax()){
            $data = UserStatus::dataList();
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.statuses'),
            'url' => 'statuses',
            'name' => 'statuses',
            'nameOne' => 'status',
            'modelName' => 'Status',
            'icon' => 'mdi mdi-format-list-bulleted-type',
        ];

        $statuses = [
            ['id' => 1,'title' => trans('main.authenticated')],
            ['id' => 2,'title' => trans('main.init')],
            ['id' => 3,'title' => trans('main.loading')],
            ['id' => 4,'title' => trans('main.gotQrCode')],
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
                'options' => $statuses,
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
        ];
      
        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function report(Request $request) {
        if($request->ajax()){
            $data = Contact::getContactsReports();
            return Datatables::of($data)->rawColumns(['contacts','hasWhatsapp','hasNoWhatsapp'])->make(true);
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.groupNumberRepors'),
            'url' => 'groupNumberReports',
            'name' => 'groupNumberReports',
            'nameOne' => 'groupNumberReports',
            'modelName' => 'groupNumberReports',
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
            'group_name' => [
                'label' => trans('main.group'),
                'type' => '',
                'className' => '',
                'data-col' => 'group_name',
                'anchor-class' => 'badge badge-primary',
            ],
            'status' => [
                'label' => trans('main.addType'),
                'type' => '',
                'className' => '',
                'data-col' => 'status',
                'anchor-class' => 'badge badge-success',
            ],
            'total' => [
                'label' => trans('main.addNos'),
                'type' => '',
                'className' => '',
                'data-col' => 'total',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.sentDate'),
                'type' => '',
                'className' => '',
                'data-col' => 'created_at',
                'anchor-class' => '',
            ],
        ];
        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function msgsArchive(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = @$columnIndex_arr[0]['column']; // Column index
        $columnName = @$columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = @$order_arr[0]['dir']; // asc or desc
        $searchValue = @$search_arr['value']; // Search value
        // dd($searchValue);
        // Fetch records
        // $draw = ($start / 10) + 1 ; 
        if($start >= 10){
            $records = ChatMessage::NotDeleted()->search($searchValue)->orderBy('time','DESC')
               ->skip($start)
               ->take($rowperpage);
            $data = ChatMessage::generateObj($records,null,true);
        }else{
            $records = ChatMessage::NotDeleted()->search($searchValue)->orderBy('time','DESC');
            $data = ChatMessage::generateObj($records,$rowperpage != null ? $rowperpage : 10,true);
        }

        $totalRecords = ChatMessage::search($searchValue)->count();
        $totalRecordswithFilter = $totalRecords;
        
        if($request->ajax()){       
            $response = [
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordswithFilter,
                "iTotalDisplayRecords" => $totalRecords,
                "data" => $data['data']
            ];
            return json_encode($response);
        }

        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['designElems']['mainData'] = [
            'title' => trans('main.msgsArchive'),
            'url' => 'msgsArchive',
            'name' => 'msgsArchive',
            'nameOne' => 'msgsArchive',
            'modelName' => 'msgsArchive',
            'icon' => 'mdi mdi-archive-outline',
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
            'chatId3' => [
                'label' => trans('main.dialog'),
                'type' => '',
                'className' => 'phone',
                'data-col' => 'chatId3',
                'anchor-class' => 'phone',
            ],
            'messageContent' => [
                'label' => trans('main.messageContent'),
                'type' => '',
                'className' => 'pre',
                'data-col' => 'messageContent',
                'anchor-class' => 'pre',
            ],
            'sending_status_text' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'sending_status_text',
                'anchor-class' => '',
            ],
            'icon' => [
                'label' => trans('main.extra_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'icon',
                'anchor-class' => '',
            ],
            'date_time' => [
                'label' => trans('main.sentDate'),
                'type' => '',
                'className' => 'date',
                'data-col' => 'date_time',
                'anchor-class' => '',
            ],
        ];

        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

}
