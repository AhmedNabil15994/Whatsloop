<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GroupMsg;
use App\Models\Bot;
use App\Models\ChatMessage;
use App\Models\UserExtraQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use Storage;
use File;


class UserStorageControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.storage'),
            'url' => 'storage',
            'name' => 'storage',
            'nameOne' => 'storage',
            'icon' => 'mdi mdi-folder-star-outline',
        ];

        $file_size = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
        $data['totalSize'] = $file_size;

        $dailyCount = Session::get('storageSize');
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
        $totalStorage = $dailyCount + $extraQuotas;
        $totalStorage = $totalStorage * 1024;

        $data['totalStorage'] = $totalStorage;
        return $data;
    }

    public function index(Request $request) {
        $users = User::where('image','!=',null)->get(['id','created_at']);
        $dataArr = [];
        foreach ($users as $key => $value) {
            $dataArr[$key] = new \stdClass();
            $dataArr[$key]->id = $value->id;
            $dataArr[$key]->created_at = $value->created_at;
            $dataArr[$key]->folder_size = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/users/'.$value->id);
        }
        $data['designElems'] = $this->getData();
        $data['data'] = $dataArr;
        $data['type'] = 'users';
        $data['parent'] = 'main';
        $data['totalSize'] = $data['designElems']['totalSize'];
        $data['totalStorage'] = $data['designElems']['totalStorage'];
        return view('Tenancy.UserStorage.Views.index')->with('data', (object) $data);
    }

    public function bots(Request $request) {
        $users = Bot::where('file_name','!=',null)->get(['id','created_at']);
        $dataArr = [];
        foreach ($users as $key => $value) {
            $dataArr[$key] = new \stdClass();
            $dataArr[$key]->id = $value->id;
            $dataArr[$key]->created_at = $value->created_at;
            $dataArr[$key]->folder_size = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/bots/'.$value->id);
        }
        $data['designElems'] = $this->getData();
        $data['data'] = $dataArr;
        $data['type'] = 'bots';
        $data['parent'] = 'main';
        $data['totalSize'] = $data['designElems']['totalSize'];
        $data['totalStorage'] = $data['designElems']['totalStorage'];
        return view('Tenancy.UserStorage.Views.index')->with('data', (object) $data);
    }

    public function groupMsgs(Request $request) {
        $users = GroupMsg::where('file_name','!=',null)->get(['id','created_at']);
        $dataArr = [];
        foreach ($users as $key => $value) {
            $dataArr[$key] = new \stdClass();
            $dataArr[$key]->id = $value->id;
            $dataArr[$key]->created_at = $value->created_at;
            $dataArr[$key]->folder_size = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/groupMessages/'.$value->id);
        }
        $data['designElems'] = $this->getData();
        $data['data'] = $dataArr;
        $data['type'] = 'groupMsgs';
        $data['parent'] = 'main';
        $data['totalSize'] = $data['designElems']['totalSize'];
        $data['totalStorage'] = $data['designElems']['totalStorage'];
        return view('Tenancy.UserStorage.Views.index')->with('data', (object) $data);
    }

    public function chats(Request $request) {
        $dataObj = [];
        $path = public_path().'/uploads/'.TENANT_ID.'/chats/';
        foreach (File::allFiles($path) as $key => $file) {
            $dataObj[$key] = new \stdClass();
            $file_size = $file->getSize();
            $file_size = $file_size/(1024 * 1024);
            $file_size = number_format($file_size,2) . " MB ";
            $dataObj[$key]->file_size = $file_size;
            $dataObj[$key]->file_name = $file->getFileName();
            $dataObj[$key]->file = \URL::to('/').'/uploads/'.TENANT_ID.'/chats/'.$file->getFileName();
        }
        // dd($dataObj);
        $data['data'] = $dataObj;
        $data['designElems'] = $this->getData();
        $data['type'] = 'chats';
        $data['parent'] = 'child';
        $data['totalSize'] = $data['designElems']['totalSize'];
        $data['totalStorage'] = $data['designElems']['totalStorage'];
        return view('Tenancy.UserStorage.Views.index')->with('data', (object) $data);
    }

    public function getByTypeAndId($type,$id){
        if($type == 'users'){
            $dataObj = User::getData(User::getOne($id));
        }else if($type == 'bots'){
            $dataObj = Bot::getData(Bot::getOne($id));
        }else if($type == 'groupMsgs'){
            $dataObj = GroupMsg::getData(GroupMsg::getOne($id));
        }
        
        $data['data'] = $dataObj;
        $data['designElems'] = $this->getData();
        $data['type'] = $type;
        $data['parent'] = 'child';
        $data['totalSize'] = $data['designElems']['totalSize'];
        $data['totalStorage'] = $data['designElems']['totalStorage'];
        return view('Tenancy.UserStorage.Views.index')->with('data', (object) $data);
    }

    public function removeByTypeAndId($type,$id){
        if($type == 'users'){
            $dataObj = User::getOne($id);
        }else if($type == 'bots'){
            $dataObj = Bot::getOne($id);
        }else if($type == 'groupMsgs'){
            $dataObj = GroupMsg::getOne($id);
        }
        if(isset($dataObj->image)){
            $dataObj->image = null;
            $dataObj->save();
        }

        if(isset($dataObj->file_name)){
            $dataObj->file_name = null;
            $dataObj->save();
        } 
        \ImagesHelper::deleteDirectory(public_path('/').'uploads/'.TENANT_ID.'/'.$type.'/'.$id);
        \Session::flash('success',trans('main.deleteSuccess'));
        return redirect()->back();
    }

    public function removeChatFile($id){
        \ImagesHelper::deleteDirectory(public_path('/').'uploads/'.TENANT_ID.'/chats/'.$id);
        \Session::flash('success',trans('main.deleteSuccess'));
        return redirect()->back();
    }

}
