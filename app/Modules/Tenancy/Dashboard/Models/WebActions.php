<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebActions extends Model{

    use \TraitsFunc;

    protected $table = 'web_actions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function User(){
        return $this->belongsTo('App\Models\User','created_by');
    }

    static function getOne($id){
        return self::where('id', $id)
            ->first();
    }

    static function getByDate($date=null,$start=null,$end=null,$type=null,$module_name=null){
        $source = self::where(function($whereQuery) use ($date,$start,$end,$type,$module_name){
            if($date == null){
                $whereQuery->where('created_at','>=',now()->format('Y-m-d').' 00:00:00')->where('created_at','<=',now()->format('Y-m-d').' 23:59:59');
            }else{
                $whereQuery->where('created_at','>=',$start)->where('created_at','<=',$end);
            }
            if($type != null){
                $whereQuery->where('type',$type);
            }
            if($module_name != null){
                $whereQuery->where('module_name',$module_name);
            }
        })->orderBy('id','DESC');
        return self::generateObj($source);
    }

    static function getByModule($module_name,$count=null){
        $source = self::where('module_name',$module_name)->orderBy('id','DESC');
        if($count != null){
            $source->take($count);
        }
        return self::generateObj($source);
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::orderBy('id','ASC');

        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();
        $count = $source->count();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        // $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        $data['data'] = $list;
        $data['count'] = $count;

        return $data;
    }

    static function getData($source) {
        $types = self::getType($source->type);
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->type = $source->type;
        $data->typeText = $types[0];
        $data->label = $types[1];
        $data->username = $source->created_by != 0 || $source->created_by != null ? $source->User->name: '';
        $data->module_name = $source->module_name;
        $data->module_page = self::getPageTitle($source->module_name);
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        $data->created_at2 = \Carbon\Carbon::createFromTimeStamp(strtotime($source->created_at))->diffForHumans();
        return $data;
    }

    static function getPageTitle($name){
        $text = '';
        if($name == 'Variable'){
            $text = 'الاعدادات';
        }elseif($name == 'Group'){
            $text = 'مجموعات المشرفين';
        }elseif($name == 'User'){
            $text = 'المشرفين والاداريين';
        }

        return $text;
    }

    static function getType($type){
        $text = '';
        $label = '';
        if($type == 1){
            $text = trans('main.add');
            $label = 'brand';
        }elseif($type == 2){
            $text = trans('main.edit');
            $label = 'success';
        }elseif($type == 3){
            $text = trans('main.delete');
            $label = 'danger';
        }elseif($type == 4){
            $text = trans('main.fastEdit');
            $label = 'primary';
        }
        return [$text,$label];
    }

    static function getCountByType($type){
        return self::where('type',$type)->count();
    }

    static function newType($type,$name,$user=null){
        $myObj = new self;
        $myObj->type = $type;
        $myObj->module_name = $name;
        $myObj->created_at = DATE_TIME;
        if($user != null){
            $myObj->created_by = 0;
        }else{
            $myObj->created_by = USER_ID;
        }
        $myObj->save();
    }

}
