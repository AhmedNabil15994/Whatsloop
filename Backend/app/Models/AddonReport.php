<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonReport extends Model{

    use \TraitsFunc;

    protected $table = 'addon_reports';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function dataList($type=null) {
        $input = \Request::all();

        $source = self::where('type',$type);

        if (isset($input['id']) && !empty($input['id'])) {
            $source->where('id',  $input['id']);
        } 
        if (isset($input['type']) && !empty($input['type'])) {
            $source->where('type',  $input['type']);
        } 
    
        $source->orderBy('end_date','DESC');

        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        // $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $dataObj = new  \stdClass();
        $dataObj->id = $source->id;
        $dataObj->type = $source->type;
        $dataObj->tenant_id = $source->tenant_id;
        $dataObj->instanceId = $source->instanceId;
        $dataObj->user_id = $source->user_id;
        $dataObj->name = $source->name;
        $dataObj->count = $source->count;
        $dataObj->paid_date = $source->paid_date != '0000-00-00' ? $source->paid_date : '';
        $dataObj->total = $source->total != null ? $source->total : '';
        $dataObj->invoice_id = $source->invoice_id != 0 ? $source->invoice_id : '';
        $dataObj->start_date = $source->start_date;
        $dataObj->end_date = $source->end_date;
        $dataObj->created_at = $source->start_date. ' - ' . $source->end_date;
        return $dataObj;
    }
    
}
