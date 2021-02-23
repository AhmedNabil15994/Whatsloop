<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use DataTables;
use Storage;


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
            'sortName' => 'name_'.LANGUAGE_PREF,
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
                'class' => 'form-control m-input datetimepicker',
                'index' => '7',
                'id' => 'datetimepicker1',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datetimepicker',
                'index' => '8',
                'id' => 'datetimepicker2',
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
            'sortName' => 'name_'.LANGUAGE_PREF,
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
                'className' => 'edits selects',
                'data-col' => 'group_id',
                'anchor-class' => 'editable',
            ],
            'addType' => [
                'label' => trans('main.addType'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'addType',
                'anchor-class' => 'editable',
            ],
            'contactsNos' => [
                'label' => trans('main.contactsNos'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'contactsNos',
                'anchor-class' => 'editable',
            ],
            'hasWhats' => [
                'label' => trans('main.hasWhats'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'hasWhats',
                'anchor-class' => 'editable',
            ],
            'hasNotWhats' => [
                'label' => trans('main.hasNotWhats'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'hasNotWhats',
                'anchor-class' => 'editable',
            ],
            'addNos' => [
                'label' => trans('main.addNos'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'addNos',
                'anchor-class' => 'editable',
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
