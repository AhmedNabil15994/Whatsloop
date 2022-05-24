<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class ModNotificationReport extends Model{

    use \TraitsFunc;

    protected $table = 'mod_reports';
    protected $primaryKey = 'id';
    protected $fillable = ['client','statusText','order_id','mod_id','created_at'];    
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($mod_id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where('statusText','!=','new')->where(function ($query) use ($input) {
                    if (isset($input['order_id']) && $input['order_id'] != null) {
                        $query->where('order_id',$input['order_id']);
                    }
                    if (isset($input['statusText']) && !empty($input['statusText'])) {
                        $query->where('statusText',$input['statusText']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', date('Y-m-d 00:00:00',strtotime($input['from'])))->where('created_at','<=',date('Y-m-d 23:59:59',strtotime($input['to'])));
                    }
                });
        if($mod_id != null){
            $source->where('mod_id',$mod_id);
        }
      
        $source->orderBy('id','ASC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->client = $source->client;
        $data->order_id = $source->order_id == null ? str_replace('@c.us','',$source->client) : $source->order_id;
        $data->statusText = $source->statusText;
        $data->mod_id = $source->mod_id;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
